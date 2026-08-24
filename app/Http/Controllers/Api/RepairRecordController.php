<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\RepairRecord\StoreRepairRecordRequest;
use App\Http\Requests\RepairRecord\UpdateRepairRecordRequest;
use App\Models\Machine;
use App\Models\RepairRecord;
use App\Repositories\All\RepairRecord\RepairRecordRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class RepairRecordController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly RepairRecordRepositoryInterface $repairs,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('repairs.view');

        return response()->json(
            $this->repairs->allWithMachine()->map(fn (RepairRecord $record) => $this->format($record))
        );
    }

    public function store(StoreRepairRecordRequest $request): JsonResponse
    {
        $machine = Machine::query()
            ->where('machine_number', $request->validated('machine_number'))
            ->firstOrFail();

        $parts = $request->validated('parts_replaced') ?? [];
        $partsCost = collect($parts)->sum(fn ($part) => (float) ($part['cost'] ?? 0));
        $labor = (float) ($request->validated('labor_cost') ?? 0);
        $total = $request->validated('total_cost');

        $photos = collect($request->file('photos', []))->map(function ($photo) use ($request) {
            $path = $photo->store('repair-records', 'public');
            return [
                'id' => (string) str()->uuid(),
                'url' => Storage::disk('public')->url($path),
                'type' => $request->validated('photo_type', 'after'),
                'caption' => $photo->getClientOriginalName(),
            ];
        })->values()->all();

        $record = RepairRecord::query()->create([
            'repair_number' => $this->repairs->nextRepairNumber(),
            'work_order_number' => $request->validated('work_order_number'),
            'machine_id' => $machine->id,
            'date' => $request->validated('date'),
            'issue_category' => $request->validated('issue_category'),
            'issue_description' => $request->validated('issue_description'),
            'parts_replaced' => $parts,
            'labor_cost' => $labor,
            'total_cost' => $total !== null ? (float) $total : $partsCost + $labor,
            'technician_id' => $request->validated('technician_id'),
            'photos' => $photos,
        ])->load('machine');

        return response()->json($this->format($record), Response::HTTP_CREATED);
    }

    public function show(RepairRecord $repairRecord): JsonResponse
    {
        $this->authorizePermission('repairs.view');

        return response()->json($this->format($repairRecord->load('machine')));
    }

    public function update(UpdateRepairRecordRequest $request, RepairRecord $repairRecord): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['parts_replaced']) || isset($data['labor_cost'])) {
            $parts = $data['parts_replaced'] ?? $repairRecord->parts_replaced ?? [];
            $partsCost = collect($parts)->sum(fn ($part) => (float) ($part['cost'] ?? 0));
            $labor = (float) ($data['labor_cost'] ?? $repairRecord->labor_cost);
            if (! array_key_exists('total_cost', $data)) {
                $data['total_cost'] = $partsCost + $labor;
            }
        }

        $repairRecord->update($data);

        return response()->json($this->format($repairRecord->fresh('machine')));
    }

    public function destroy(RepairRecord $repairRecord): Response
    {
        $this->authorizePermission('repairs.delete');
        $repairRecord->delete();

        return response()->noContent();
    }

    private function format(RepairRecord $record): array
    {
        return [
            'id' => $record->id,
            'repair_number' => $record->repair_number,
            'work_order_number' => $record->work_order_number,
            'machine_number' => $record->machine?->machine_number,
            'date' => optional($record->date)?->format('Y-m-d'),
            'issue_category' => $record->issue_category,
            'issue_description' => $record->issue_description,
            'parts_replaced' => $record->parts_replaced ?? [],
            'labor_cost' => (float) $record->labor_cost,
            'total_cost' => (float) $record->total_cost,
            'technician_id' => $record->technician_id,
            'photos' => $record->photos ?? [],
            'created_at' => $record->created_at,
            'updated_at' => $record->updated_at,
        ];
    }
}
