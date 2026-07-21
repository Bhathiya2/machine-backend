<?php

namespace App\Repositories\All\Role;

use App\Models\Role;
use App\Repositories\Base\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface extends EloquentRepositoryInterface
{
    public function allWithPermissions(): Collection;

    public function syncPermissions(Role $role, array $permissionIds): Role;
}
