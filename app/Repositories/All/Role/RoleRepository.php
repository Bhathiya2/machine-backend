<?php

namespace App\Repositories\All\Role;

use App\Models\Role;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function allWithPermissions(): Collection
    {
        return $this->model->newQuery()
            ->with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();
    }

    public function syncPermissions(Role $role, array $permissionIds): Role
    {
        $role->permissions()->sync($permissionIds);

        return $role->fresh(['permissions']);
    }
}
