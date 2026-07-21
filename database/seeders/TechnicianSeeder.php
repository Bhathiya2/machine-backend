<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = [
            [
                'user_code' => 'u2',
                'name' => 'Priya Nair',
                'email' => 'priya.nair@example.com',
                'password' => 'password',
                'role' => 'Technician',
                'site' => 'Plant A',
                'phone' => '+1-555-0142',
            ],
            [
                'user_code' => 'u3',
                'name' => 'Derek Santos',
                'email' => 'derek.santos@example.com',
                'password' => 'password',
                'role' => 'Technician',
                'site' => 'Plant B',
                'phone' => '+1-555-0163',
            ],
            [
                'user_code' => 'u4',
                'name' => 'Aisha Okafor',
                'email' => 'aisha.okafor@example.com',
                'password' => 'password',
                'role' => 'Technician',
                'site' => 'Plant C',
                'phone' => '+1-555-0178',
            ],
        ];

        foreach ($technicians as $technician) {
            User::query()->updateOrCreate(
                ['email' => $technician['email']],
                $technician
            );
        }
    }
}
