<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_only_sees_work_orders_assigned_to_them(): void
    {
        $technician = User::factory()->create([
            'name' => 'Technician One',
            'email' => 'tech1@example.com',
            'role' => 'Technician',
            'user_code' => 'tech-1',
        ]);

        $otherTechnician = User::factory()->create([
            'name' => 'Technician Two',
            'email' => 'tech2@example.com',
            'role' => 'Technician',
            'user_code' => 'tech-2',
        ]);

        $machine = Machine::create([
            'machine_number' => 'M-100',
            'name' => 'Press 1',
            'model' => 'Model X',
            'site' => 'Factory A',
            'install_date' => now()->toDateString(),
            'setup_by' => 'Owner',
            'status' => 'Operational',
        ]);

        $assignedWorkOrder = WorkOrder::create([
            'work_order_number' => 'WO-0001',
            'machine_id' => $machine->id,
            'title' => 'Assigned job',
            'description' => 'Should be visible',
            'assigned_to' => $technician->user_code,
            'created_by' => 'superadmin',
            'status' => 'New',
            'priority' => 'High',
        ]);

        WorkOrder::create([
            'work_order_number' => 'WO-0002',
            'machine_id' => $machine->id,
            'title' => 'Other job',
            'description' => 'Should not be visible',
            'assigned_to' => $otherTechnician->user_code,
            'created_by' => 'superadmin',
            'status' => 'New',
            'priority' => 'Medium',
        ]);

        $response = $this->actingAs($technician, 'sanctum')->getJson('/api/work-orders');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.work_order_number', $assignedWorkOrder->work_order_number);
        $response->assertJsonMissing(['work_order_number' => 'WO-0002']);
    }
}
