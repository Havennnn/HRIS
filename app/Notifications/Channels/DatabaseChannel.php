<?php

namespace App\Notifications\Channels;

use App\Models\Notification;
use App\Notifications\BaseNotification;

class DatabaseChannel
{
    /**
     * Send a notification to the database.
     */
    public function send(BaseNotification $notification, mixed $notifiable): ?Notification
    {
        $record = Notification::query()->create([
            'type' => $notification->type(),
            'notifiable_id' => $notifiable->getKey(),
            'notifiable_type' => $notifiable->getMorphClass(),
            'data' => $notification->toDatabase(),
            'status' => 'sent',
            'channels' => $notification->getSentVia() ?: ['database'],
        ]);

        return $record;
    }

    public function name(): string
    {
        return 'database';
    }
}
