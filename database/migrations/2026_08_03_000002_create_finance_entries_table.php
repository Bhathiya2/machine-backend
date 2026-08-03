<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->string('work_order_number')->index();
            $table->foreignId('machine_id')->constrained('machines')->cascadeOnDelete();
            $table->string('category');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('details')->nullable();
            $table->date('entry_date');
            $table->string('recorded_by')->nullable();
            $table->timestamps();

            $table->index(['work_order_id', 'entry_date']);
            $table->index(['machine_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_entries');
    }
};