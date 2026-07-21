<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('assigned_to');
            $table->string('created_by');
            $table->string('status')->default('Assigned');
            $table->string('priority')->default('Medium');
            $table->text('notes')->nullable();
            $table->string('fault_report_id')->nullable();
            $table->json('cost_entries')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
