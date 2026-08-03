<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceEntry extends Model
{
    protected $fillable = [
        'work_order_id',
        'work_order_number',
        'machine_id',
        'category',
        'quantity',
        'unit_price',
        'amount',
        'details',
        'entry_date',
        'recorded_by',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'amount' => 'float',
        'entry_date' => 'date',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}