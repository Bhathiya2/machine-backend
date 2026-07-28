<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkOrder\StoreWorkOrderRequest;
use App\Http\Requests\WorkOrder\UpdateWorkOrderRequest;
use App\Models\Machine;
use App\Models\RepairRecord;
use App\Models\WorkOrder;
use App\Repositories\All\Notification\NotificationRepositoryInterface;
use App\Repositories\All\RepairRecord\RepairRecordRepositoryInterface;
use App\Repositories\All\WorkOrder\WorkOrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkOrderController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly WorkOrderRepositoryInterface $workOrders,
        private readonly NotificationRepositoryInterface $notifications,
        private readonly RepairRecordRepositoryInterface $repairs,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizePermission('workorders.view');

        $filters = $request->only([
            'status',
            'assigned_to',
            'machine_number',
            'from',
            'to',
            'search',
        ]);
        $filters['current_user'] = $request->user();

        return response()->json($this->workOrders->getFiltered($filters));
    }

    public function store(StoreWorkOrderRequest $request): JsonResponse
    {
        $machine = Machine::query()
            ->where('machine_number', $request->validated('machine_number'))
            ->firstOrFail();

        $workOrder = $this->workOrders->createForMachine($machine, [
            'work_order_number' => $this->workOrders->nextWorkOrderNumber(),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'assigned_to' => $request->validated('assigned_to'),
            'created_by' => $request->validated('created_by'),
            'status' => $request->validated('status') ?? 'New',
            'priority' => $request->validated('priority'),
            'notes' => $request->validated('notes'),
            'fault_report_id' => $request->validated('fault_report_id'),
            'cost_entries' => $request->validated('cost_entries') ?? [],
        ]);

        $this->notifications->createForUser(
            $workOrder->assigned_to,
            "You have been assigned Work Order {$workOrder->work_order_number}: {$workOrder->title} on {$machine->machine_number}.",
            $workOrder->work_order_number
        );

        return response()->json($workOrder, Response::HTTP_CREATED);
    }

    public function show(WorkOrder $workOrder): JsonResponse
    {
        $this->authorizePermission('workorders.view');

        return response()->json($workOrder->load('machine'));
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder): JsonResponse
    {
        $data = $request->validated();
        $previousStatus = $workOrder->status;
        $previousAssignee = $workOrder->assigned_to;

        // Handle re-opening a closed work order
        if ($previousStatus === 'Close' && ($data['status'] ?? '') === 'Inprogress') {
            // Authorization is handled in UpdateWorkOrderRequest
        }

        if (isset($data['machine_number'])) {
            $machine = Machine::query()
                ->where('machine_number', $data['machine_number'])
                ->firstOrFail();
            $data['machine_id'] = $machine->id;
            unset($data['machine_number']);
        }

        $updated = $this->workOrders->updateWorkOrder($workOrder, $data);

        if (isset($data['assigned_to']) && $data['assigned_to'] !== $previousAssignee) {
            $machineNumber = $updated->machine?->machine_number ?? '';
            $this->notifications->createForUser(
                $updated->assigned_to,
                "You have been assigned Work Order {$updated->work_order_number}: {$updated->title} on {$machineNumber}.",
                $updated->work_order_number
            );
        }

        if (($data['status'] ?? null) === 'Finished' && $previousStatus !== 'Finished') {
            $this->notifications->createForUser(
                'u1',
                "Work Order {$updated->work_order_number} has been marked complete. Please verify and approve.",
                $updated->work_order_number
            );
        }

        if (($data['status'] ?? null) === 'Verified' && $previousStatus !== 'Verified') {
            $this->ensureRepairRecord($updated);
        }

        return response()->json($updated);
    }

    public function checkIn(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $user = $request->user();

        if (in_array($workOrder->status, ['Close', 'Verified', 'Finished'])) {
            return response()->json(['message' => 'Cannot check in to a work order that is closed, verified, or finished.'], Response::HTTP_FORBIDDEN);
        }

        // Check if another technician is already checked into this machine
        $existingSession = WorkOrder::query()
            ->where('machine_id', $workOrder->machine_id)
            ->whereNotNull('active_technician_id')
            ->where('id', '!=', $workOrder->id)
            ->first();

        if ($existingSession) {
            return response()->json(['message' => "Machine is already being worked on by another technician under WO #{$existingSession->work_order_number}."], Response::HTTP_CONFLICT);
        }
        
        if ($workOrder->active_technician_id !== null && $workOrder->active_technician_id !== $user->user_code) {
            return response()->json(['message' => "Another technician is already checked in to this work order."], Response::HTTP_CONFLICT);
        }


        $workOrder->update([
            'active_technician_id' => $user->user_code,
            'checked_in_at' => now(),
        ]);

        return response()->json($workOrder);
    }

    public function checkOut(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $user = $request->user();

        if ($workOrder->active_technician_id !== $user->user_code) {
            return response()->json(['message' => 'You are not checked in to this work order.'], Response::HTTP_FORBIDDEN);
        }

        $workOrder->update([
            'active_technician_id' => null,
            'checked_in_at' => null,
        ]);

        return response()->json($workOrder);
    }

    public function destroy(WorkOrder $workOrder): Response
    {
        $this->authorizePermission('workorders.delete');
        $this->workOrders->deleteWorkOrder($workOrder);

        return response()->noContent();
    }

    private function ensureRepairRecord(WorkOrder $workOrder): void
    {
        if ($this->repairs->findByWorkOrder($workOrder->work_order_number)) {
            return;
        }

        $costEntries = $workOrder->cost_entries ?? [];
        $parts = [];
        $labor = 0.0;
        $other = 0.0;

        foreach ($costEntries as $entry) {
            $amount = (float) ($entry['amount'] ?? 0);
            $category = $entry['category'] ?? 'Others';
            if ($category === 'Spare Part') {
                $parts[] = [
                    'name' => $entry['details'] ?? 'Spare part',
                    'partNumber' => $entry['id'] ?? '',
                    'cost' => $amount,
                ];
            } elseif ($category === 'Labor') {
                $labor += $amount;
            } else {
                $other += $amount;
            }
        }

        RepairRecord::query()->create([
            'repair_number' => $this->repairs->nextRepairNumber(),
            'work_order_number' => $workOrder->work_order_number,
            'machine_id' => $workOrder->machine_id,
            'date' => now()->toDateString(),
            'issue_category' => 'Mechanical',
            'issue_description' => $workOrder->description ?: $workOrder->title,
            'parts_replaced' => $parts,
            'labor_cost' => $labor,
            'total_cost' => $labor + $other + collect($parts)->sum('cost'),
            'technician_id' => $workOrder->assigned_to,
            'photos' => [],
        ]);
    }
}
