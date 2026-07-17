<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

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
    ];
}