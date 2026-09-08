<?php

namespace App\Notifications;

use App\Models\FaultReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoChannel;

class NewFaultReported extends Notification
{
    use Queueable;

    public function __construct(
        public FaultReport $fault
    ) {}

    public function via($notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title('New Fault Reported')
            ->body("{$this->fault->fault_number} on {$this->fault->machine?->machine_number}: {$this->fault->description}")
            ->data([
                'fault_id' => $this->fault->id,
                'type' => 'fault_report',
            ]);
    }
}