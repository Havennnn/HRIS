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
     * Validation rules applied to the uploaded CSV file AND each row.
     *
     * File rules: validated automatically by Laravel before ImportAction runs.
     * Row rules: enforced by ImportAction after prepareRow() transforms
     * each CSV row. Failed rows are flagged as errors.
     */
    public function rules(): array
    {
        return [
            // Uploaded file validation
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],

            // Per-row validation (applied by ImportAction to each CSV row)
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
            'file.required' => 'Please select a CSV file to upload.',
            'file.mimes' => 'The file must be a CSV file.',
            'file.max' => 'The file size must not exceed 5MB.',
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
