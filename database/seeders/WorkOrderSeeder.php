<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'work_order_number' => 'WO-0021',
                'machine_number' => 'MCH-0071',
                'title' => 'Coolant leak investigation',
                'description' => 'Machine reporting intermittent coolant pressure drops. Inspect fittings, hoses, and pump assembly. Replace seals as needed.',
                'assigned_to' => 'u2',
                'created_by' => 'u1',
                'status' => 'Work In Progress',
                'priority' => 'High',
                'notes' => 'Checked hose fittings — main seal on pump inlet cracked. Ordered replacement part.',
                'fault_report_id' => 'FR-002',
                'cost_entries' => [
                    ['id' => 'ce-001', 'category' => 'Transportation', 'amount' => 85, 'quantity' => 1, 'unitPrice' => 85, 'details' => 'Plant A roundtrip', 'date' => '2024-06-01'],
                    ['id' => 'ce-002', 'category' => 'Others', 'amount' => 200, 'quantity' => 1, 'unitPrice' => 200, 'details' => 'Technician advance', 'date' => '2024-06-01'],
                ],
                'created_at' => '2024-06-01',
                'updated_at' => '2024-06-03',
            ],
            [
                'work_order_number' => 'WO-0025',
                'machine_number' => 'MCH-0103',
                'title' => 'Arm fault — joint 3 encoder failure',
                'description' => 'Arm fault alarm F-4320 triggered. Diagnostics indicate encoder failure on joint 3. Full encoder swap required.',
                'assigned_to' => 'u3',
                'created_by' => 'u1',
                'status' => 'Technician En Route',
                'priority' => 'High',
                'notes' => '',
                'fault_report_id' => 'FR-001',
                'cost_entries' => [],
                'created_at' => '2024-07-04',
                'updated_at' => '2024-07-05',
            ],
            [
                'work_order_number' => 'WO-0027',
                'machine_number' => 'MCH-0042',
                'title' => 'Scheduled preventive maintenance',
                'description' => 'Quarterly PM per OEM schedule. Grease all joints, check cable harness, update firmware to v4.2.1.',
                'assigned_to' => 'u2',
                'created_by' => 'u1',
                'status' => 'Work Completed',
                'priority' => 'Low',
                'notes' => 'All joints greased. Firmware updated. Cable harness in good condition.',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-06-15',
                'updated_at' => '2024-06-18',
            ],
            [
                'work_order_number' => 'WO-0029',
                'machine_number' => 'MCH-0134',
                'title' => 'Firmware upgrade to v3.8.0',
                'description' => 'Apply latest firmware update from Cognex. Requires offline mode. Estimated downtime: 4 hours.',
                'assigned_to' => 'u3',
                'created_by' => 'u1',
                'status' => 'Verified & Closed',
                'priority' => 'Medium',
                'notes' => 'Firmware upgraded successfully. Machine back online.',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-05-18',
                'updated_at' => '2024-05-20',
            ],
            [
                'work_order_number' => 'WO-0030',
                'machine_number' => 'MCH-0071',
                'title' => 'Belt alignment check',
                'description' => 'Conveyor belt drifting left. Realign rollers and tension belt to OEM spec.',
                'assigned_to' => 'u2',
                'created_by' => 'u1',
                'status' => 'Assigned',
                'priority' => 'Medium',
                'notes' => '',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-07-10',
                'updated_at' => '2024-07-10',
            ],
            [
                'work_order_number' => 'WO-0031',
                'machine_number' => 'MCH-0103',
                'title' => 'Emergency stop button replacement',
                'description' => 'E-stop button sticky. Replace switch assembly and verify safety circuit.',
                'assigned_to' => 'u3',
                'created_by' => 'u1',
                'status' => 'Technician Arrived',
                'priority' => 'High',
                'notes' => '',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-07-08',
                'updated_at' => '2024-07-09',
            ],
            [
                'work_order_number' => 'WO-0032',
                'machine_number' => 'MCH-0042',
                'title' => 'Sensor calibration',
                'description' => 'Proximity sensors reading out of range. Recalibrate and document new baselines.',
                'assigned_to' => 'u2',
                'created_by' => 'u1',
                'status' => 'Assigned',
                'priority' => 'Low',
                'notes' => '',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-07-06',
                'updated_at' => '2024-07-06',
            ],
            [
                'work_order_number' => 'WO-0033',
                'machine_number' => 'MCH-0134',
                'title' => 'Oil filter change',
                'description' => 'Scheduled hydraulic oil filter replacement. Dispose of used filter per site policy.',
                'assigned_to' => 'u3',
                'created_by' => 'u1',
                'status' => 'Cancelled',
                'priority' => 'Low',
                'notes' => 'Cancelled — parts unavailable this week.',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-06-20',
                'updated_at' => '2024-06-22',
            ],
            [
                'work_order_number' => 'WO-0034',
                'machine_number' => 'MCH-0071',
                'title' => 'Motor bearing noise',
                'description' => 'Unusual noise from drive motor. Inspect bearings and lubricate or replace.',
                'assigned_to' => 'u2',
                'created_by' => 'u1',
                'status' => 'Verified & Closed',
                'priority' => 'Medium',
                'notes' => 'Bearing replaced. Noise resolved.',
                'fault_report_id' => null,
                'cost_entries' => [],
                'created_at' => '2024-05-01',
                'updated_at' => '2024-05-05',
            ],
        ];

        foreach ($orders as $order) {
            $machine = Machine::query()->where('machine_number', $order['machine_number'])->first();
            if (! $machine) {
                continue;
            }

            WorkOrder::query()->updateOrCreate(
                ['work_order_number' => $order['work_order_number']],
                [
                    'machine_id' => $machine->id,
                    'title' => $order['title'],
                    'description' => $order['description'],
                    'assigned_to' => $order['assigned_to'],
                    'created_by' => $order['created_by'],
                    'status' => $order['status'],
                    'priority' => $order['priority'],
                    'notes' => $order['notes'],
                    'fault_report_id' => $order['fault_report_id'],
                    'cost_entries' => $order['cost_entries'],
                    'created_at' => $order['created_at'],
                    'updated_at' => $order['updated_at'],
                ]
            );
        }
    }
}
