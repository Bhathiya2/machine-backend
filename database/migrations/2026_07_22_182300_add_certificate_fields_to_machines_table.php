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
        Schema::table('machines', function (Blueprint $table) {
            $table->string('cert_reference')->nullable();
            $table->string('cert_calibration')->nullable();
            $table->string('cert_warranty')->nullable();
            $table->string('cert_contract')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_contact_person')->nullable();
            $table->string('client_phone_number')->nullable();
            $table->string('client_system')->nullable();
            $table->string('client_customer_code')->nullable();
            $table->string('client_job_title')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_expired_date')->nullable();
            $table->string('client_date_of_manufacture')->nullable();
            $table->string('tech_freq')->nullable();
            $table->string('tech_voltage')->nullable();
            $table->string('tech_amp')->nullable();
            $table->string('tech_total_mc_power')->nullable();
            $table->string('tech_ups')->nullable();
            $table->string('tech_chiller_cooling_system')->nullable();
            $table->string('tech_chiller_absorbed_power')->nullable();
            $table->string('tech_smoke_extractor')->nullable();
            $table->string('tech_room_temp')->nullable();
            $table->boolean('sign_completed')->default(false);
            $table->boolean('sign_incompleted')->default(false);
            $table->string('sign_signed_by')->nullable();
            $table->string('sign_technician_signature')->nullable();
            $table->string('sign_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn([
                'cert_reference', 'cert_calibration', 'cert_warranty', 'cert_contract',
                'client_name', 'client_contact_person', 'client_phone_number', 'client_system',
                'client_customer_code', 'client_job_title', 'client_email', 'client_expired_date',
                'client_date_of_manufacture', 'tech_freq', 'tech_voltage', 'tech_amp',
                'tech_total_mc_power', 'tech_ups', 'tech_chiller_cooling_system',
                'tech_chiller_absorbed_power', 'tech_smoke_extractor', 'tech_room_temp',
                'sign_completed', 'sign_incompleted', 'sign_signed_by', 'sign_technician_signature', 'sign_date',
            ]);
        });
    }
};
