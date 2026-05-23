<?php

namespace App\Notifications\Channels;

use App\Notifications\BaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailChannel
{
    /**
     * Send a notification via email.
     */
    public function send(BaseNotification $notification, mixed $notifiable): void
    {
        $mailData = $notification->toMail();
        $email = $this->resolveEmail($notifiable);

        if (empty($email)) {
            Log::warning('Notification mail skipped: no email address', [
                'notification' => $notification->type(),
                'notifiable' => $notifiable->getMorphClass() . '#' . $notifiable->getKey(),
            ]);

            return;
        }

        $from = config('notifications.mail.from', []);
        $subject = $mailData['subject'] ?? 'Notification';

        try {
            Mail::raw($mailData['body'] ?? '', function ($message) use ($email, $from, $subject, $mailData) {
                $message->to($email)
                    ->subject($subject)
                    ->from(
                        $from['address'] ?? config('mail.from.address'),
                        $from['name'] ?? config('mail.from.name')
                    );

                // Attach HTML content if provided
                if (! empty($mailData['html'])) {
                    $message->setBody($mailData['html'], 'text/html');
                }
            });

            Log::info('Notification mail sent', [
                'to' => $email,
                'subject' => $subject,
                'notification' => $notification->type(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Notification mail failed', [
                'to' => $email,
                'notification' => $notification->type(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function name(): string
    {
        return 'mail';
    }

    /**
     * Resolve the email address from the notifiable entity.
     */
    protected function resolveEmail(mixed $notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationForMail')) {
            return $notifiable->routeNotificationForMail();
        }

        return $notifiable->email ?? $notifiable->getAttribute('email');
    }
}
