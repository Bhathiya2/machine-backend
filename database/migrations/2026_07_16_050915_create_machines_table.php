<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create ('machines', function (Blueprint $table) {
    $table->id();
    $table->string('machine_number')->unique();
    $table->date('install_date');
    $table->string('setup_by');
    $table->string('factory_group')->nullable();
    $table->string('factory')->nullable();
    $table->string('status')->default('active');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
