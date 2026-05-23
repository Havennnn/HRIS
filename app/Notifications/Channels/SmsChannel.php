<?php

namespace App\Notifications\Channels;

use App\Notifications\BaseNotification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send a notification via SMS.
     */
    public function send(BaseNotification $notification, mixed $notifiable): void
    {
        $message = $notification->toSms();
        $number = $this->resolvePhoneNumber($notifiable);

        if (empty($message)) {
            return; // No SMS content defined
        }

        if (empty($number)) {
            Log::warning('Notification SMS skipped: no phone number', [
                'notification' => $notification->type(),
                'notifiable' => $notifiable->getMorphClass() . '#' . $notifiable->getKey(),
            ]);

            return;
        }

        $driver = config('notifications.sms.driver', 'log');

        match ($driver) {
            'log' => $this->sendViaLog($number, $message),
            'twilio' => $this->sendViaTwilio($number, $message),
            'vonage' => $this->sendViaVonage($number, $message),
            default => $this->sendViaLog($number, $message),
        };
    }

    public function name(): string
    {
        return 'sms';
    }

    /**
     * Resolve the phone number from the notifiable entity.
     */
    protected function resolvePhoneNumber(mixed $notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationForSms')) {
            return $notifiable->routeNotificationForSms();
        }

        return $notifiable->mobile_number ?? $notifiable->getAttribute('mobile_number')
            ?? $notifiable->phone ?? $notifiable->getAttribute('phone');
    }

    /**
     * Log SMS (default/dev driver).
     */
    protected function sendViaLog(string $number, string $message): void
    {
        Log::info('[SMS Notification]', [
            'to' => $number,
            'message' => $message,
            'from' => config('notifications.sms.from', 'HRIS'),
        ]);
    }

    /**
     * Send via Twilio.
     * Requires: twilio/sdk package in production.
     */
    protected function sendViaTwilio(string $number, string $message): void
    {
        $sid = config('notifications.sms.twilio.sid');
        $token = config('notifications.sms.twilio.token');
        $from = config('notifications.sms.twilio.from');

        if (! $sid || ! $token || ! $from) {
            Log::error('Twilio not configured for SMS notification');
            return;
        }

        try {
            $twilio = new \Twilio\Rest\Client($sid, $token);
            $twilio->messages->create($number, [
                'from' => $from,
                'body' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Twilio SMS failed', ['error' => $e->getMessage(), 'to' => $number]);
        }
    }

    /**
     * Send via Vonage (formerly Nexmo).
     * Requires: vonage/client package in production.
     */
    protected function sendViaVonage(string $number, string $message): void
    {
        $key = config('notifications.sms.vonage.key');
        $secret = config('notifications.sms.vonage.secret');
        $from = config('notifications.sms.vonage.from');

        if (! $key || ! $secret || ! $from) {
            Log::error('Vonage not configured for SMS notification');
            return;
        }

        try {
            $basic = new \Vonage\Client\Credentials\Basic($key, $secret);
            $client = new \Vonage\Client($basic);
            $client->sms()->send(
                new \Vonage\SMS\Message\SMS($number, $from, $message)
            );
        } catch (\Throwable $e) {
            Log::error('Vonage SMS failed', ['error' => $e->getMessage(), 'to' => $number]);
        }
    }
}
