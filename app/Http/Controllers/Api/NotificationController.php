<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\AuthorizesApiPermissions;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Repositories\All\Notification\NotificationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    use AuthorizesApiPermissions;

    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('notifications.view');

        $user = auth()->user();
        $userCode = $user?->user_code ?? (string) $user?->id;

        return response()->json(
            $this->notifications->forUser($userCode)->map(fn (AppNotification $n) => $this->format($n))
        );
    }

    public function markRead(int $notification): JsonResponse
    {
        $this->authorizePermission('notifications.view');

        $model = AppNotification::query()->findOrFail($notification);
        $this->assertOwner($model);

        $model->update(['read' => true]);

        return response()->json($this->format($model->fresh()));
    }

    public function markAllRead(): JsonResponse
    {
        $this->authorizePermission('notifications.view');

        $user = auth()->user();
        $userCode = $user?->user_code ?? (string) $user?->id;

        AppNotification::query()
            ->where('user_code', $userCode)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    private function assertOwner(AppNotification $notification): void
    {
        $user = auth()->user();
        $userCode = $user?->user_code ?? (string) $user?->id;
        abort_unless($notification->user_code === $userCode, Response::HTTP_FORBIDDEN);
    }

    private function format(AppNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'notification_code' => $notification->notification_code,
            'user_code' => $notification->user_code,
            'message' => $notification->message,
            'read' => $notification->read,
            'work_order_number' => $notification->work_order_number,
            'created_at' => $notification->created_at,
            'updated_at' => $notification->updated_at,
        ];
    }
}
