<?php

namespace App\Notifications\Concerns;

use App\Notifications\BaseNotification;
use App\Notifications\Notifier;
use App\Models\Notification;

trait HandlesNotifications
{
    /**
     * Get the entity's notifications.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Get the entity's unread notifications.
     */
    public function unreadNotifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->notifications()->unread();
    }

    /**
     * Send a notification to this entity.
     */
    public function notify(BaseNotification $notification): void
    {
        Notifier::notify($this, $notification);
    }

    /**
     * Route notification to email channel.
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->email ?? null;
    }

    /**
     * Route notification to SMS channel.
     */
    public function routeNotificationForSms(): ?string
    {
        return $this->mobile_number ?? null;
    }

    /**
     * Get the total unread notification count.
     */
    public function unreadNotificationCount(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
    }
}
