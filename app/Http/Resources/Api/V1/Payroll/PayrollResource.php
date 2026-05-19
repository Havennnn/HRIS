<?php

namespace App\Http\Resources\Api\V1\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'status' => $this->status?->badge(),
            'basic_salary' => $this->basic_salary,
            'allowance' => $this->allowance,
            'gross_pay' => $this->gross_pay,
            'tax' => $this->tax,
            'sss' => $this->sss,
            'pagibig' => $this->pagibig,
            'philhealth' => $this->philhealth,
            'net_pay' => $this->net_pay,
            'pay_period_start' => $this->pay_period_start?->toDateString(),
            'pay_period_end' => $this->pay_period_end?->toDateString(),
            'adjustments' => $this->whenLoaded('adjustments', fn () => $this->adjustments->map(fn ($adj) => [
                'id' => $adj->id,
                'type' => $adj->type?->label(),
                'label' => $adj->label,
                'amount' => $adj->amount,
            ])),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
