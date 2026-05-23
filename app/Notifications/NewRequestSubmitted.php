<?php

namespace App\Notifications;

use App\Models\Request;
use PiaCore\Notifications\BaseNotification;

class NewRequestSubmitted extends BaseNotification
{
    public function __construct(
        protected Request $request
    ) {}

    public function type(): string
    {
        return 'request.submitted';
    }

    public function toDatabase(): array
    {
        $employeeName = $this->request->employee?->name ?? 'An employee';
        $type = $this->request->type?->label() ?? 'request';
        $date = $this->request->requested_date?->format('M d, Y');

        return [
            'title' => 'New Request Submitted',
            'message' => "{$employeeName} submitted a {$type} request for {$date}.",
            'request_id' => $this->request->id,
            'type' => $this->request->type?->value,
            'employee_id' => $this->request->employee_id,
            'action_url' => '/requests/' . $this->request->id,
        ];
    }

    public function toMail(): array
    {
        $employeeName = $this->request->employee?->name ?? 'An employee';
        $type = $this->request->type?->label() ?? 'Request';

        return [
            'subject' => "New {$type} Request - " . config('app.name'),
            'body' => "{$employeeName} has submitted a new {$type} request.\n\n"
                . "Date: {$this->request->requested_date?->format('M d, Y')}\n"
                . "Message: {$this->request->message}\n\n"
                . "Login to the admin panel to review.",
            'html' => view('notifications.request-submitted', [
                'request' => $this->request,
            ])->render(),
        ];
    }

    public function toSms(): ?string
    {
        $employeeName = $this->request->employee?->name ?? 'An employee';
        $type = $this->request->type?->label() ?? 'request';

        return "HRIS: {$employeeName} submitted a {$type} request. Login to review.";
    }
}
