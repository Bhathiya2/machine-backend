<?php

namespace App\Repositories\All\Machine;

use App\Models\Machine;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class MachineRepository extends BaseRepository implements MachineRepositoryInterface
{
    public function __construct(Machine $model)
    {
        parent::__construct($model);
    }

    public function allOrdered(): Collection
    {
        return $this->model->newQuery()->orderBy('machine_number')->get();
    }
}
