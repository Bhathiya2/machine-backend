<?php

namespace App\Repositories\All\FaultReport;

use App\Models\FaultReport;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class FaultReportRepository extends BaseRepository implements FaultReportRepositoryInterface
{
    public function __construct(FaultReport $model)
    {
        parent::__construct($model);
    }

    public function allWithMachine(): Collection
    {
        return $this->model->newQuery()
            ->with('machine')
            ->orderByDesc('created_at')
            ->get();
    }

    public function nextFaultNumber(): string
    {
        $latest = $this->model->newQuery()->orderByDesc('id')->value('fault_number');

        if (! $latest || ! preg_match('/FR-(\d+)/', $latest, $matches)) {
            return 'FR-0001';
        }

        return 'FR-'.str_pad((string) ((int) $matches[1] + 1), 4, '0', STR_PAD_LEFT);
    }
}
