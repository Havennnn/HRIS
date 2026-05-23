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
            // Uploaded file validation (applied by Laravel automatically)
            'file' => ['required', 'file', 'mimes:csv,xlsx', 'max:5120'],
        ];
    }

    /**
     * Validation rules applied to each CSV/XLSX row during background import.
     *
     * These are NOT checked by Laravel's automatic validation — they're used
     * by ImportJob to validate each row during queued processing.
     */
    public function rowRules(): array
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
            'file.required' => 'Please select a CSV file to upload.',
            'file.mimes' => 'The file must be a CSV or Excel (.xlsx) file.',
            'file.max' => 'The file size must not exceed 5MB.',
        ];
    }

    /**
     * Custom error messages for row-level validation.
     * Used by ImportJob during background processing.
     */
    public function rowMessages(): array
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
