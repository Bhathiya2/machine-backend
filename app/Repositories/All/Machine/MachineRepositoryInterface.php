<?php

namespace App\Repositories\All\Machine;

use App\Repositories\Base\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface MachineRepositoryInterface extends EloquentRepositoryInterface
{
    public function allOrdered(): Collection;
}
