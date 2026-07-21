<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $hasProfileFields = Schema::hasColumn('users', 'role');
        $hasRbac = Schema::hasTable('roles') && Schema::hasTable('permissions');

        if ($hasRbac) {
            $this->call(PermissionSeeder::class);
            $this->call(RoleSeeder::class);
        }

        $this->seedUsers();

        if ($hasRbac) {
            $this->assignRoleIds();
        }

        $this->call(MachineSeeder::class);

        if ($hasProfileFields) {
            $this->call(TechnicianSeeder::class);
            $this->call(RoleUserSeeder::class);

            if ($hasRbac) {
                $this->assignRoleIds();
            }

            $this->call(WorkOrderSeeder::class);
            $this->call(OpsDataSeeder::class);
        }
    }

    private function seedUsers(): void
    {
        $hasProfileFields = Schema::hasColumn('users', 'role');

        if (! $hasProfileFields) {
            User::query()->updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Super Admin',
                    'password' => 'password',
                    'email_verified_at' => now(),
                ]
            );

            return;
        }

        User::query()->updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'user_code' => 'u0',
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => 'Super Admin',
                'site' => 'All Sites',
                'phone' => '+1-555-0001',
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'user_code' => 'u1',
                'name' => 'Marcus Webb',
                'password' => 'password',
                'role' => 'Super Admin',
                'site' => 'All Sites',
                'phone' => '+1-555-0101',
                'email_verified_at' => now(),
            ]
        );
    }

    private function assignRoleIds(): void
    {
        if (! Schema::hasColumn('users', 'role_id')) {
            return;
        }

        $roles = Role::query()->pluck('id', 'name');

        User::query()->each(function (User $user) use ($roles) {
            $roleName = $user->role ?: 'Technician';
            $roleId = $roles->get($roleName);

            if ($roleId && $user->role_id !== $roleId) {
                $user->update(['role_id' => $roleId]);
            }
        });
    }
}
