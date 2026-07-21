<?php

namespace App\Repositories\All\Permission;

use App\Models\Permission;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function allGrouped(): Collection
    {
        return $this->model->newQuery()->orderBy('group')->orderBy('label')->get();
    }
}
