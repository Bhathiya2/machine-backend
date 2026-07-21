<?php

namespace App\Repositories\All\WorkOrder;

use App\Models\Machine;
use App\Models\WorkOrder;
use App\Repositories\Base\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface WorkOrderRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered(array $filters = []): Collection;

    public function findWithMachine(int $id): ?WorkOrder;

    public function createForMachine(Machine $machine, array $data): WorkOrder;

    public function updateWorkOrder(WorkOrder $workOrder, array $data): WorkOrder;

    public function deleteWorkOrder(WorkOrder $workOrder): bool;

    public function nextWorkOrderNumber(): string;
}
