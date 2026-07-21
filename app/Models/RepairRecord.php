<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairRecord extends Model
{
    protected $fillable = [
        'repair_number',
        'work_order_number',
        'machine_id',
        'date',
        'issue_category',
        'issue_description',
        'parts_replaced',
        'labor_cost',
        'total_cost',
        'technician_id',
        'photos',
    ];

    protected $casts = [
        'parts_replaced' => 'array',
        'photos' => 'array',
        'date' => 'date',
        'labor_cost' => 'float',
        'total_cost' => 'float',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
