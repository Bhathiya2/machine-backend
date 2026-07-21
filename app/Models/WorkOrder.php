<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'cost_entries' => 'array',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
