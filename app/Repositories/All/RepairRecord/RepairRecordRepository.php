<?php

namespace App\Repositories\All\RepairRecord;

use App\Models\RepairRecord;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class RepairRecordRepository extends BaseRepository implements RepairRecordRepositoryInterface
{
    public function __construct(RepairRecord $model)
    {
        parent::__construct($model);
    }

    public function allWithMachine(): Collection
    {
        return $this->model->newQuery()
            ->with('machine')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
    }

    public function nextRepairNumber(): string
    {
        $latest = $this->model->newQuery()->orderByDesc('id')->value('repair_number');

        if (! $latest || ! preg_match('/RR-(\d+)/', $latest, $matches)) {
            return 'RR-0001';
        }

        return 'RR-'.str_pad((string) ((int) $matches[1] + 1), 4, '0', STR_PAD_LEFT);
    }

    public function findByWorkOrder(string $workOrderNumber): ?RepairRecord
    {
        return $this->model->newQuery()
            ->where('work_order_number', $workOrderNumber)
            ->first();
    }
}
