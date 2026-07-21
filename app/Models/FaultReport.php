<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaultReport extends Model
{
    protected $fillable = [
        'fault_number',
        'machine_id',
        'reported_by',
        'description',
        'severity',
        'category',
        'status',
        'converted_to_wo',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
