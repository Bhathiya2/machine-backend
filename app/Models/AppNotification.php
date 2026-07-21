<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'notification_code',
        'user_code',
        'message',
        'read',
        'work_order_number',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];
}
