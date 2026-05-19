<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class PayoutConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'period_start_day' => ['required', 'integer', 'min:1', 'max:31'],
            'period_end_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'period_end_is_last_day' => ['boolean'],
            'cutoff_generation_day' => ['required', 'integer', 'min:1', 'max:31'],
            'cutoff_disburse_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'disburse_is_last_day' => ['boolean'],
            'assumed_from_day' => ['required', 'integer', 'min:1', 'max:31'],
            'is_active' => ['boolean'],
        ];
    }
}
