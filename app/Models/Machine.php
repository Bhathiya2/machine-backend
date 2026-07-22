<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $casts = [
        'install_date' => 'date',
    ];

    protected $fillable = [
        'machine_number',
        'name',
        'model',
        'site',
        'install_date',
        'setup_by',
        'factory_group',
        'factory',
        'status',
        'cert_reference', 'cert_calibration', 'cert_warranty', 'cert_contract',
        'client_name', 'client_contact_person', 'client_phone_number', 'client_system',
        'client_customer_code', 'client_job_title', 'client_email', 'client_expired_date',
        'client_date_of_manufacture', 'tech_freq', 'tech_voltage', 'tech_amp',
        'tech_total_mc_power', 'tech_ups', 'tech_chiller_cooling_system',
        'tech_chiller_absorbed_power', 'tech_smoke_extractor', 'tech_room_temp',
        'sign_completed', 'sign_incompleted', 'sign_signed_by', 'sign_technician_signature', 'sign_date'
    ];
}