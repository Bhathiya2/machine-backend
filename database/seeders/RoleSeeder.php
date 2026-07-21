<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = Permission::query()->pluck('id', 'name');

        foreach (config('permissions.roles', []) as $roleName => $permissionNames) {
            $isSuperAdmin = $permissionNames === ['*'];

            $role = Role::query()->updateOrCreate(
                ['name' => $roleName],
                [
                    'slug' => Str::slug($roleName),
                    'description' => "{$roleName} system role",
                    'is_system' => true,
                    'is_super_admin' => $isSuperAdmin,
                ]
            );

            if ($isSuperAdmin) {
                $role->permissions()->sync($allPermissions->values()->all());
                continue;
            }

            $ids = collect($permissionNames)
                ->map(fn (string $name) => $allPermissions->get($name))
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($ids);
        }
    }
}
