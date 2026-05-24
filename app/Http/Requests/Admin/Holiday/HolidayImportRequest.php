<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Holiday;

use PiaCore\Http\Requests\ImportRequest;

class HolidayImportRequest extends ImportRequest
{
    public function rowRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['nullable', 'integer', 'in:1,2'],
            'is_paid' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function rowMessages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
        ];
    }
}
