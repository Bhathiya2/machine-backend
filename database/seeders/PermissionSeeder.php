<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('permissions.definitions', []) as $definition) {
            Permission::query()->updateOrCreate(
                ['name' => $definition['name']],
                [
                    'group' => $definition['group'],
                    'label' => $definition['label'],
                ]
            );
        }
    }
}
