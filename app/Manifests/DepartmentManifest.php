<?php

declare(strict_types=1);

namespace App\Manifests;

use PiaCore\Contracts\Import\ManifestStructure;

class DepartmentManifest implements ManifestStructure
{
    public function name(): string
    {
        return 'Department Import Template';
    }
    public function headers(): array
    {
        return ['Name'];
    }

    public function exampleRow(): array
    {
        return ['Name' => 'Human Resources'];
    }

    public function columnOptions(): array
    {
        return [];
    }
}
