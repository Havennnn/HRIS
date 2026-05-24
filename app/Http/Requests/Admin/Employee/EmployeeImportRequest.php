<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Employee;

use PiaCore\Http\Requests\ImportRequest;

class EmployeeImportRequest extends ImportRequest
{
    public function rowRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'birthdate' => ['nullable', 'date'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'type' => ['nullable', 'integer', 'in:1,2,3,4'],
            'status' => ['nullable', 'integer', 'in:1,2,3,4'],
        ];
    }

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
