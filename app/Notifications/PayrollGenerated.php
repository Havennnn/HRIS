<?php

namespace App\Notifications;

use App\Models\Payroll;
use PiaCore\Notifications\BaseNotification;

class PayrollGenerated extends BaseNotification
{
    public function __construct(
        protected Payroll $payroll
    ) {}

    public function type(): string
    {
        return 'payroll.generated';
    }

    public function toDatabase(): array
    {
        return [
            'title' => 'Payroll Generated',
            'message' => "Your payroll for period {$this->payroll->pay_period_start->toDateString()} to {$this->payroll->pay_period_end->toDateString()} has been generated.",
            'payroll_id' => $this->payroll->id,
            'gross_pay' => $this->payroll->gross_pay,
            'net_pay' => $this->payroll->net_pay,
            'status' => $this->payroll->status->label(),
            'action_url' => '/payrolls/' . $this->payroll->id,
        ];
    }

    public function toMail(): array
    {
        return [
            'subject' => 'Payroll Generated - ' . config('app.name'),
            'body' => "Your payroll for period {$this->payroll->pay_period_start->toDateString()} to {$this->payroll->pay_period_end->toDateString()} has been generated.\n\n"
                . "Gross Pay: ₱" . number_format($this->payroll->gross_pay, 2) . "\n"
                . "Net Pay: ₱" . number_format($this->payroll->net_pay, 2) . "\n"
                . "Status: {$this->payroll->status->label()}\n\n"
                . "Login to view the details.",
            'html' => view('notifications.payroll-generated', [
                'payroll' => $this->payroll,
            ])->render(),
        ];
    }

    public function toSms(): ?string
    {
        return "Your payroll for {$this->payroll->pay_period_start->toDateString()} to {$this->payroll->pay_period_end->toDateString()} is ready. Net pay: ₱" . number_format($this->payroll->net_pay, 2);
    }
}
