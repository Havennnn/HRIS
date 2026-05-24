<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Career;

use PiaCore\Http\Requests\ImportRequest;

class CareerImportRequest extends ImportRequest
{
    public function rowRules(): array
    {
        return [
            'position_id' => ['required', 'exists:positions,id'],
            'description' => ['nullable', 'string'],
            'salary' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    public function rowMessages(): array
    {
        return [
            'position_id.required' => 'Position is required.',
            'position_id.exists' => 'Position not found.',
        ];
    }
}
