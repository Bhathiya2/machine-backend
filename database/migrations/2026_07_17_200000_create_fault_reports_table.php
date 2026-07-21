<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fault_reports', function (Blueprint $table) {
            $table->id();
            $table->string('fault_number', 30)->unique();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->string('reported_by', 50);
            $table->text('description');
            $table->string('severity', 20)->default('Medium');
            $table->string('category', 50)->default('Mechanical');
            $table->string('status', 20)->default('Open');
            $table->string('converted_to_wo', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fault_reports');
    }
};
