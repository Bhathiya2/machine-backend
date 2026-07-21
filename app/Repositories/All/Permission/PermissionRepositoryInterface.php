<?php

namespace App\Repositories\All\Permission;

use App\Models\Permission;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    public function allGrouped(): Collection;
}
