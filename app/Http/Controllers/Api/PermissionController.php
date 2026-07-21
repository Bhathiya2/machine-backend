<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Repositories\All\Permission\PermissionRepositoryInterface;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly PermissionRepositoryInterface $permissions,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('roles.view');

        $permissions = $this->permissions->allGrouped();

        return response()->json(
            $permissions->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => $permission->group,
                'label' => $permission->label,
            ])->values()
        );
    }
}
