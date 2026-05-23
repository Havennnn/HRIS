<?php

namespace App\Http\Requests\Admin\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules applied to each CSV row during import.
     *
     * These rules are enforced by ImportAction after prepareRow() transforms
     * the raw CSV data. Each row that fails validation is flagged as an error
     * with the corresponding message from messages().
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'type' => ['nullable', 'integer', 'in:1,2,3,4'],
            'status' => ['nullable', 'integer', 'in:1,2,3,4'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Email address is invalid.',
            'position_id.exists' => 'Position not found.',
            'type.in' => 'Type must be Regular (1), Probationary (2), Contractual (3), or Part-time (4).',
            'status.in' => 'Status must be Active (1), Inactive (2), Resigned (3), or Terminated (4).',
        ];
    }
}
