<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    public function run(): void
    {
        $machines = [
            [
                'machine_number' => 'MCH-0042',
                'name' => 'Welding Arm Alpha',
                'model' => 'FANUC ARC Mate 120iD',
                'site' => 'Plant A',
                'factory_group' => 'North America Manufacturing',
                'factory' => 'Detroit Assembly',
                'install_date' => '2023-04-15',
                'setup_by' => 'Marcus Webb',
                'status' => 'Operational',
            ],
            [
                'machine_number' => 'MCH-0071',
                'name' => 'Assembly Bot Bravo',
                'model' => 'KUKA KR 6 R700',
                'site' => 'Plant A',
                'factory_group' => 'North America Manufacturing',
                'factory' => 'Detroit Assembly',
                'install_date' => '2022-11-03',
                'setup_by' => 'Marcus Webb',
                'status' => 'Under Maintenance',
            ],
            [
                'machine_number' => 'MCH-0103',
                'name' => 'Palletizer Charlie',
                'model' => 'ABB IRB 660',
                'site' => 'Plant B',
                'factory_group' => 'Europe Logistics',
                'factory' => 'Berlin Hub',
                'install_date' => '2024-01-20',
                'setup_by' => 'Derek Santos',
                'status' => 'Broken',
            ],
            [
                'machine_number' => 'MCH-0118',
                'name' => 'Paint Sprayer Delta',
                'model' => 'Yaskawa Motoman PX2750',
                'site' => 'Plant C',
                'factory_group' => 'Asia Pacific Production',
                'factory' => 'Tokyo Plant',
                'install_date' => '2023-08-09',
                'setup_by' => 'Aisha Okafor',
                'status' => 'Operational',
            ],
            [
                'machine_number' => 'MCH-0134',
                'name' => 'Vision Inspect Echo',
                'model' => 'Cognex IS-7802M',
                'site' => 'Plant B',
                'factory_group' => 'Europe Logistics',
                'factory' => 'Berlin Hub',
                'install_date' => '2023-12-01',
                'setup_by' => 'Marcus Webb',
                'status' => 'Offline',
            ],
        ];

        foreach ($machines as $machine) {
            Machine::query()->updateOrCreate(
                ['machine_number' => $machine['machine_number']],
                $machine
            );
        }
    }
}
