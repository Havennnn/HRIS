<?php

namespace App\Http\Requests\Api\V1\Request;

use App\Enums\Type\RequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'integer', Rule::enum(RequestType::class)],
            'message' => ['required', 'string', 'max:1000'],
            'requested_date' => ['required', 'date'],
            'days' => ['nullable', 'integer', 'min:1'],
            'end_date' => ['nullable', 'date', 'after_or_equal:requested_date'],
            'overtime_hours' => ['nullable', 'numeric', 'min:0.5'],
        ];
    }
}
