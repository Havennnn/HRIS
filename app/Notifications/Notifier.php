<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Log;

class Notifier
{
    /**
     * The notifiable entity (employee, admin, etc.)
     */
    protected mixed $notifiable = null;

    /**
     * Set the recipient.
     */
    public function to(mixed $notifiable): static
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    /**
     * Send a notification.
     */
    public function send(BaseNotification $notification): void
    {
        $recipient = $this->notifiable;

        if ($recipient === null) {
            throw new \InvalidArgumentException('No recipient set. Call ->to($entity) first.');
        }

        $notification->to($recipient);
        $channels = $notification->channels();

        if (empty($channels)) {
            Log::info('Notification skipped: no channels configured', [
                'type' => $notification->type(),
            ]);

            return;
        }

        $channelInstances = BaseNotification::resolveChannels($channels);

        foreach ($channelInstances as $name => $channel) {
            try {
                $channel->send($notification, $recipient);
                $notification->markChannelSent($name);

                Log::debug('Notification sent via ' . $name, [
                    'type' => $notification->type(),
                    'recipient' => $recipient->getMorphClass() . '#' . $recipient->getKey(),
                ]);
            } catch (\Throwable $e) {
                Log::error("Notification failed via {$name}", [
                    'type' => $notification->type(),
                    'error' => $e->getMessage(),
                    'recipient' => $recipient->getMorphClass() . '#' . $recipient->getKey(),
                ]);
            }
        }
    }

    /**
     * Quick-send a notification to a notifiable entity.
     */
    public static function notify(mixed $notifiable, BaseNotification $notification): void
    {
        (new static)->to($notifiable)->send($notification);
    }
}
