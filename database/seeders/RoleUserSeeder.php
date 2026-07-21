<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'user_code' => 'u5',
                'name' => 'Lena Fischer',
                'email' => 'owner@example.com',
                'password' => 'password',
                'role' => 'Owner',
                'site' => 'All Sites',
                'phone' => '+1-555-0195',
            ],
            [
                'user_code' => 'u6',
                'name' => 'James Osei',
                'email' => 'worker@example.com',
                'password' => 'password',
                'role' => 'Worker',
                'site' => 'Plant B',
                'phone' => '+1-555-0187',
            ],
            [
                'user_code' => 'u7',
                'name' => 'Diana Park',
                'email' => 'finance@example.com',
                'password' => 'password',
                'role' => 'Finance',
                'site' => 'All Sites',
                'phone' => '+1-555-0209',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, ['email_verified_at' => now()])
            );
        }
    }
}
