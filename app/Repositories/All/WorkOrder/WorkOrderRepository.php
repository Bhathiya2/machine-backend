<?php

namespace App\Repositories\All\WorkOrder;

use App\Models\Machine;
use App\Models\WorkOrder;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class WorkOrderRepository extends BaseRepository implements WorkOrderRepositoryInterface
{
    public function __construct(WorkOrder $model)
    {
        parent::__construct($model);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->model->newQuery()->with('machine')
            // Active WOs first; Verified & Closed / Cancelled sink to the bottom
            ->orderByRaw("CASE WHEN status IN ('Verified & Closed', 'Cancelled') THEN 1 ELSE 0 END")
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at');

        if (! empty($filters['status']) && $filters['status'] !== 'All') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['assigned_to']) && $filters['assigned_to'] !== 'All') {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['machine_number']) && $filters['machine_number'] !== 'All') {
            $machineNumber = $filters['machine_number'];
            $query->whereHas('machine', fn ($q) => $q->where('machine_number', $machineNumber));
        }

        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('work_order_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('machine', fn ($mq) => $mq->where('machine_number', 'like', "%{$search}%"));
            });
        }

        return $query->get();
    }

    public function findWithMachine(int $id): ?WorkOrder
    {
        return $this->model->newQuery()->with('machine')->find($id);
    }

    public function createForMachine(Machine $machine, array $data): WorkOrder
    {
        $workOrder = $this->model->newQuery()->create([
            ...$data,
            'machine_id' => $machine->id,
        ]);

        return $workOrder->load('machine');
    }

    public function updateWorkOrder(WorkOrder $workOrder, array $data): WorkOrder
    {
        $workOrder->update($data);

        return $workOrder->fresh()->load('machine');
    }

    public function deleteWorkOrder(WorkOrder $workOrder): bool
    {
        return (bool) $workOrder->delete();
    }

    public function nextWorkOrderNumber(): string
    {
        $latest = $this->model->newQuery()->orderByDesc('id')->value('work_order_number');

        if (! $latest || ! preg_match('/WO-(\d+)/', $latest, $matches)) {
            return 'WO-0001';
        }

        $next = (int) $matches[1] + 1;

        return 'WO-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
