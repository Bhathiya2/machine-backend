<?php

namespace App\Repositories\All\Permission;

use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    public function allGrouped(): Collection;
}
