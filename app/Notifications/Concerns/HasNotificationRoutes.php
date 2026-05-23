<?php

namespace App\Notifications\Concerns;

/**
 * Adds notification routing methods for SMS and other custom channels.
 *
 * Use alongside Illuminate\Notifications\Notifiable on your models.
 * Does NOT define 'notifications()' or 'unreadNotifications()' relations
 * — those come from Laravel's built-in Notifiable trait.
 */
trait HasNotificationRoutes
{
    /**
     * Route notification to the mail channel.
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->email ?? null;
    }

    /**
     * Route notification to the SMS channel.
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
        if (! method_exists($this, 'unreadNotifications')) {
            return 0;
        }

        return $this->unreadNotifications()->count();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): void
    {
        if (! method_exists($this, 'unreadNotifications')) {
            return;
        }

        $this->unreadNotifications()->update(['read_at' => now()]);
    }
}
