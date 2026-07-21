<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\FaultReport\StoreFaultReportRequest;
use App\Http\Requests\FaultReport\UpdateFaultReportRequest;
use App\Models\FaultReport;
use App\Models\Machine;
use App\Repositories\All\FaultReport\FaultReportRepositoryInterface;
use App\Repositories\All\Notification\NotificationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FaultReportController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly FaultReportRepositoryInterface $faults,
        private readonly NotificationRepositoryInterface $notifications,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('faults.view');

        return response()->json(
            $this->faults->allWithMachine()->map(fn (FaultReport $fault) => $this->format($fault))
        );
    }

    public function store(StoreFaultReportRequest $request): JsonResponse
    {
        $machine = Machine::query()
            ->where('machine_number', $request->validated('machine_number'))
            ->firstOrFail();

        $fault = FaultReport::query()->create([
            'fault_number' => $this->faults->nextFaultNumber(),
            'machine_id' => $machine->id,
            'reported_by' => $request->validated('reported_by'),
            'description' => $request->validated('description'),
            'severity' => $request->validated('severity'),
            'category' => $request->validated('category'),
            'status' => 'Open',
        ])->load('machine');

        $this->notifications->createForUser(
            'u1',
            "New fault report {$fault->fault_number} on {$machine->machine_number}: {$fault->description}",
            null
        );

        return response()->json($this->format($fault), Response::HTTP_CREATED);
    }

    public function show(FaultReport $faultReport): JsonResponse
    {
        $this->authorizePermission('faults.view');

        return response()->json($this->format($faultReport->load('machine')));
    }

    public function update(UpdateFaultReportRequest $request, FaultReport $faultReport): JsonResponse
    {
        $faultReport->update($request->validated());

        return response()->json($this->format($faultReport->fresh('machine')));
    }

    public function destroy(FaultReport $faultReport): Response
    {
        $this->authorizePermission('faults.dismiss');
        $faultReport->delete();

        return response()->noContent();
    }

    private function format(FaultReport $fault): array
    {
        return [
            'id' => $fault->id,
            'fault_number' => $fault->fault_number,
            'machine_number' => $fault->machine?->machine_number,
            'reported_by' => $fault->reported_by,
            'description' => $fault->description,
            'severity' => $fault->severity,
            'category' => $fault->category,
            'status' => $fault->status,
            'converted_to_wo' => $fault->converted_to_wo,
            'created_at' => $fault->created_at,
            'updated_at' => $fault->updated_at,
        ];
    }
}
