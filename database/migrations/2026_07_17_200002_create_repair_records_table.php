<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_records', function (Blueprint $table) {
            $table->id();
            $table->string('repair_number', 30)->unique();
            $table->string('work_order_number', 30)->index();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('issue_category', 50);
            $table->text('issue_description');
            $table->json('parts_replaced')->nullable();
            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->string('technician_id', 50);
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_records');
    }
};
