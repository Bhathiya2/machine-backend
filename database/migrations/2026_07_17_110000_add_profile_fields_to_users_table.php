<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_code')->nullable()->unique()->after('id');
            $table->string('role')->default('Technician')->after('password');
            $table->string('site')->nullable()->after('role');
            $table->string('phone')->nullable()->after('site');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_code', 'role', 'site', 'phone']);
        });
    }
};
