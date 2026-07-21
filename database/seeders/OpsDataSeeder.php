<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\FaultReport;
use App\Models\Machine;
use Illuminate\Database\Seeder;

class OpsDataSeeder extends Seeder
{
    public function run(): void
    {
        $machines = Machine::query()->get()->keyBy('machine_number');

        $faults = [
            [
                'fault_number' => 'FR-0001',
                'machine_number' => 'MCH-0103',
                'reported_by' => 'u4',
                'description' => 'Arm joint 3 encoder fault alarm F-4320 triggered during production run.',
                'severity' => 'Critical',
                'category' => 'Electrical',
                'status' => 'Converted',
                'converted_to_wo' => 'WO-0025',
            ],
            [
                'fault_number' => 'FR-0002',
                'machine_number' => 'MCH-0071',
                'reported_by' => 'u4',
                'description' => 'Coolant pressure drops intermittently. Possible hose leak near pump inlet.',
                'severity' => 'High',
                'category' => 'Hydraulic',
                'status' => 'Converted',
                'converted_to_wo' => 'WO-0021',
            ],
            [
                'fault_number' => 'FR-0003',
                'machine_number' => 'MCH-0042',
                'reported_by' => 'u6',
                'description' => 'Unusual vibration on conveyor section B during high-speed mode.',
                'severity' => 'Medium',
                'category' => 'Mechanical',
                'status' => 'Open',
                'converted_to_wo' => null,
            ],
        ];

        foreach ($faults as $fault) {
            $machine = $machines->get($fault['machine_number']);
            if (! $machine) {
                continue;
            }

            FaultReport::query()->updateOrCreate(
                ['fault_number' => $fault['fault_number']],
                [
                    'machine_id' => $machine->id,
                    'reported_by' => $fault['reported_by'],
                    'description' => $fault['description'],
                    'severity' => $fault['severity'],
                    'category' => $fault['category'],
                    'status' => $fault['status'],
                    'converted_to_wo' => $fault['converted_to_wo'],
                ]
            );
        }

        $notifications = [
            [
                'notification_code' => 'n-seed-001',
                'user_code' => 'u2',
                'message' => 'You have been assigned Work Order WO-0021: Coolant leak investigation on MCH-0071.',
                'read' => false,
                'work_order_number' => 'WO-0021',
            ],
            [
                'notification_code' => 'n-seed-002',
                'user_code' => 'u1',
                'message' => 'Work Order WO-0027 has been marked complete. Please verify and approve.',
                'read' => false,
                'work_order_number' => 'WO-0027',
            ],
            [
                'notification_code' => 'n-seed-003',
                'user_code' => 'u3',
                'message' => 'You have been assigned Work Order WO-0025: Arm fault — joint 3 encoder failure on MCH-0103.',
                'read' => true,
                'work_order_number' => 'WO-0025',
            ],
        ];

        foreach ($notifications as $notification) {
            AppNotification::query()->updateOrCreate(
                ['notification_code' => $notification['notification_code']],
                $notification
            );
        }
    }
}
