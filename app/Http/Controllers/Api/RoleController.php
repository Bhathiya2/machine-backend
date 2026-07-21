<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\SyncRolePermissionsRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Repositories\All\Permission\PermissionRepositoryInterface;
use App\Repositories\All\Role\RoleRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('roles.view');

        return response()->json(
            $this->roles->allWithPermissions()->map(fn (Role $role) => $this->formatRole($role))
        );
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roles->create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'is_system' => false,
            'is_super_admin' => false,
        ]);

        if ($request->filled('permission_ids')) {
            $this->roles->syncPermissions($role, $request->validated('permission_ids'));
        }

        return response()->json($this->formatRole($role->fresh(['permissions'])), Response::HTTP_CREATED);
    }

    public function show(Role $role): JsonResponse
    {
        $this->authorizePermission('roles.view');

        return response()->json($this->formatRole($role->load('permissions')));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        abort_if($role->is_super_admin, Response::HTTP_FORBIDDEN, 'Super Admin role cannot be modified.');

        $role->update($request->safe()->only(['name', 'description']));

        if ($request->has('permission_ids')) {
            $this->roles->syncPermissions($role, $request->validated('permission_ids') ?? []);
        }

        return response()->json($this->formatRole($role->fresh(['permissions'])));
    }

    public function syncPermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        abort_if($role->is_super_admin, Response::HTTP_FORBIDDEN, 'Super Admin permissions cannot be modified.');

        $role = $this->roles->syncPermissions($role, $request->validated('permission_ids'));

        return response()->json($this->formatRole($role));
    }

    public function destroy(Role $role): Response
    {
        $this->authorizePermission('roles.delete');
        abort_if($role->is_system, Response::HTTP_FORBIDDEN, 'System roles cannot be deleted.');
        abort_if($role->users()->exists(), Response::HTTP_CONFLICT, 'Role is assigned to users.');

        $role->delete();

        return response()->noContent();
    }

    private function formatRole(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'is_system' => $role->is_system,
            'is_super_admin' => $role->is_super_admin,
            'users_count' => $role->users_count ?? $role->users()->count(),
            'permissions' => $role->permissions->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'group' => $permission->group,
                'label' => $permission->label,
            ])->values(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }
}
