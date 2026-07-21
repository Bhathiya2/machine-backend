<?php

namespace App\Repositories\All\Notification;

use App\Models\AppNotification;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function forUser(string $userCode): Collection;

    public function nextCode(): string;

    public function createForUser(string $userCode, string $message, ?string $workOrderNumber = null): AppNotification;
}
