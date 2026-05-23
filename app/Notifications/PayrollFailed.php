<?php

namespace App\Notifications;

use PiaCore\Notifications\BaseNotification;

class PayrollFailed extends BaseNotification
{
    public function __construct(
        protected string $periodStart,
        protected string $periodEnd,
        protected string $errorMessage
    ) {}

    public function type(): string
    {
        return 'payroll.failed';
    }

    public function toDatabase(): array
    {
        return [
            'title' => 'Payroll Generation Failed',
            'message' => "Payroll generation failed for period {$this->periodStart} to {$this->periodEnd}: {$this->errorMessage}",
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'error' => $this->errorMessage,
            'action_url' => '/payrolls',
        ];
    }

    public function toMail(): array
    {
        return [
            'subject' => 'Payroll Generation Failed - ' . config('app.name'),
            'body' => "Payroll generation for period {$this->periodStart} to {$this->periodEnd} has failed.\n\n"
                . "Error: {$this->errorMessage}\n\n"
                . "Please check the system logs and try again.",
            'html' => view('notifications.payroll-failed', [
                'periodStart' => $this->periodStart,
                'periodEnd' => $this->periodEnd,
                'errorMessage' => $this->errorMessage,
            ])->render(),
        ];
    }

    public function toSms(): ?string
    {
        return "HRIS: Payroll generation failed for {$this->periodStart} to {$this->periodEnd}. Check system logs.";
    }
}
