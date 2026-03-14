<?php

namespace App\Http\Resources\Admin\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'full_name' => $this->employee?->full_name,
            'position' => $this->employee?->position?->name,
            'department' => $this->employee?->position?->department?->name,
            'status_value' => $this->status?->value,
            'status' => $this->status?->badge(),
            'basic_salary' => (float) $this->basic_salary,
            'allowance' => (float) $this->allowance,
            'tax' => (float) $this->tax,
            'sss' => (float) $this->sss,
            'pagibig' => (float) $this->pagibig,
            'philhealth' => (float) $this->philhealth,
            'gross_pay' => (float) $this->gross_pay,
            'net_pay' => (float) $this->net_pay,
            'pay_period_start' => $this->pay_period_start?->format('M d, Y'),
            'pay_period_end' => $this->pay_period_end?->format('M d, Y'),
            'created_at' => $this->created_at?->format('M d, Y H:i:s'),
        ];
    }
}
