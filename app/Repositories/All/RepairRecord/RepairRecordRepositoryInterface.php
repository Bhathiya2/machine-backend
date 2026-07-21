<?php

namespace App\Repositories\All\RepairRecord;

use App\Models\RepairRecord;
use Illuminate\Database\Eloquent\Collection;

interface RepairRecordRepositoryInterface
{
    public function allWithMachine(): Collection;

    public function nextRepairNumber(): string;

    public function findByWorkOrder(string $workOrderNumber): ?RepairRecord;
}
