<?php

namespace App\Repositories\All\Notification;

use App\Models\AppNotification;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(AppNotification $model)
    {
        parent::__construct($model);
    }

    public function forUser(string $userCode): Collection
    {
        return $this->model->newQuery()
            ->where('user_code', $userCode)
            ->orderByDesc('created_at')
            ->get();
    }

    public function nextCode(): string
    {
        return 'n'.now()->format('YmdHis').random_int(100, 999);
    }

    public function createForUser(string $userCode, string $message, ?string $workOrderNumber = null): AppNotification
    {
        return $this->model->newQuery()->create([
            'notification_code' => $this->nextCode(),
            'user_code' => $userCode,
            'message' => $message,
            'read' => false,
            'work_order_number' => $workOrderNumber,
        ]);
    }
}
