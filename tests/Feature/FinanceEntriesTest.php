<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceEntriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_work_order_cost_entries_are_persisted_to_finance_table(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Super Admin',
            'user_code' => 'u1',
            'site' => 'HQ',
        ]);

        $machine = Machine::create([
            'machine_number' => 'M-200',
            'name' => 'Lathe 1',
            'model' => 'LX-200',
            'site' => 'Factory B',
            'install_date' => now()->toDateString(),
            'setup_by' => 'Owner',
            'status' => 'Operational',
        ]);

        $workOrder = WorkOrder::create([
            'work_order_number' => 'WO-0099',
            'machine_id' => $machine->id,
            'title' => 'Replace bearing',
            'description' => 'Finance sync test',
            'assigned_to' => 'tech-9',
            'created_by' => 'u1',
            'status' => 'New',
            'priority' => 'Medium',
            'cost_entries' => [],
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/work-orders/{$workOrder->id}", [
            'cost_entries' => [
                [
                    'id' => 'ce-1',
                    'category' => 'Spare Part',
                    'quantity' => 2,
                    'unitPrice' => 1250,
                    'amount' => 2500,
                    'details' => 'Main bearing set',
                    'date' => now()->toDateString(),
                ],
                [
                    'id' => 'ce-2',
                    'category' => 'Labor',
                    'quantity' => 3,
                    'unitPrice' => 500,
                    'amount' => 1500,
                    'details' => 'Technician support',
                    'date' => now()->toDateString(),
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonCount(2, 'cost_entries');

        $this->assertDatabaseCount('finance_entries', 2);
        $this->assertDatabaseHas('finance_entries', [
            'work_order_id' => $workOrder->id,
            'work_order_number' => 'WO-0099',
            'category' => 'Spare Part',
            'amount' => 2500,
            'recorded_by' => 'u1',
        ]);
        $this->assertDatabaseHas('finance_entries', [
            'work_order_id' => $workOrder->id,
            'category' => 'Labor',
            'amount' => 1500,
        ]);
    }
}