<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $fillable = [
        'work_order_number',
        'machine_id',
        'title',
        'description',
        'assigned_to',
        'created_by',
        'status',
        'priority',
        'notes',
        'fault_report_id',
        'cost_entries',
        'active_technician_id',
        'checked_in_at',
    ];

    protected $casts = [
        'cost_entries' => 'array',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function checkInSessions(): HasMany
    {
        return $this->hasMany(WorkOrderCheckInSession::class);
    }

    public function technicianNotes(): HasMany
    {
        return $this->hasMany(TechnicianNote::class)->latest();
    }
}
