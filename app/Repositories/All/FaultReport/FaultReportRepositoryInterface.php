<?php

namespace App\Repositories\All\FaultReport;

use Illuminate\Database\Eloquent\Collection;

interface FaultReportRepositoryInterface
{
    public function allWithMachine(): Collection;

    public function nextFaultNumber(): string;
}
