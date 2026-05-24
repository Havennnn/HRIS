<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Department;

use PiaCore\Http\Requests\ImportRequest;

class DepartmentImportRequest extends ImportRequest
{
    public function rowRules(): array { return ['name' => ['required', 'string', 'max:255']]; }
    public function rowMessages(): array { return ['name.required' => 'Name is required.']; }
}
