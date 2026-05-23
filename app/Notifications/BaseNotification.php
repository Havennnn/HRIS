<?php

namespace App\Notifications;

use App\Notifications\Channels\DatabaseChannel;
use App\Notifications\Channels\MailChannel;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Contracts\ShouldQueueNotification;
use Illuminate\Support\Str;

abstract class BaseNotification
{
    /**
     * The notifiable entity (employee, admin, etc.)
     */
    protected mixed $notifiable;

    /**
     * Channels this notification was sent through
     */
    protected array $sentVia = [];

    /**
     * Set the notifiable entity.
     */
    public function to(mixed $notifiable): static
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    /**
     * Get the notifiable entity.
     */
    public function getNotifiable(): mixed
    {
        return $this->notifiable;
    }

    /**
     * Get the notification type identifier.
     */
    public function type(): string
    {
        return static::class;
    }

    /**
     * Get a short, human-readable type key for storage/display.
     */
    public function typeKey(): string
    {
        return Str::of(static::class)
            ->classBasename()
            ->snake();
    }

    /**
     * Get the channels this notification should be sent through.
     */
    public function channels(): array
    {
        $config = config('notifications.channels.default', ['database', 'mail']);
        $specific = config('notifications.channels.' . static::class, null);

        return $specific ?? $config;
    }

    /**
     * Get the data for database storage.
     */
    abstract public function toDatabase(): array;

    /**
     * Get the mail representation.
     */
    abstract public function toMail(): array;

    /**
     * Get the SMS message.
     */
    public function toSms(): ?string
    {
        return null; // Override in subclass if SMS is supported
    }

    /**
     * Track that a channel was used.
     */
    public function markChannelSent(string $channel): void
    {
        $this->sentVia[] = $channel;
    }

    /**
     * Get the channels this was sent through.
     */
    public function getSentVia(): array
    {
        return $this->sentVia;
    }

    /**
     * Check if this notification should be queued.
     */
    public function shouldQueue(): bool
    {
        return $this instanceof ShouldQueueNotification;
    }

    /**
     * Resolve channel instances from channel names.
     */
    public static function resolveChannels(array $channelNames): array
    {
        $map = [
            'mail' => MailChannel::class,
            'sms' => SmsChannel::class,
            'database' => DatabaseChannel::class,
        ];

        $channels = [];
        foreach ($channelNames as $name) {
            $name = strtolower(trim($name));
            if (isset($map[$name])) {
                $channels[$name] = app($map[$name]);
            }
        }

        return $channels;
    }
}
