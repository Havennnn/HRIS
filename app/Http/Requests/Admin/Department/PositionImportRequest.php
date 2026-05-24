<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Department;

use PiaCore\Http\Requests\ImportRequest;

class PositionImportRequest extends ImportRequest
{
    public function rowRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric'],
            'allowance' => ['nullable', 'numeric'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ];
    }

    public function rowMessages(): array
    {
        return ['name.required' => 'Name is required.'];
    }
}
