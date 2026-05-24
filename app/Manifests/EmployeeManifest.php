<?php

declare(strict_types=1);

namespace App\Manifests;

use App\Enums\Status\EmployeeStatus;
use App\Enums\Type\EmployeeType;
use App\Models\Position;
use PiaCore\Contracts\Import\ManifestStructure;

class EmployeeManifest implements ManifestStructure
{
    public function name(): string
    {
        return 'Employee Import Template';
    }
    public function headers(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Mobile Number',
            'Birthdate',
            'Position',
            'Type',
            'Status',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'First Name' => 'Juan',
            'Last Name' => 'Dela Cruz',
            'Email' => 'juan@example.com',
            'Mobile Number' => '09171234567',
            'Birthdate' => '2024-03-15',
            'Position' => 'Software Engineer',
            'Type' => 'Regular',
            'Status' => 'Active',
        ];
    }

    public function columnOptions(): array
    {
        $positions = Position::query()
            ->with('department')
            ->get()
            ->mapWithKeys(fn ($p) => [
                $p->name . ($p->department ? " ({$p->department->name})" : '') => (string) $p->id,
            ])
            ->all();

        return [
            'Position' => $positions,
            'Type' => collect(EmployeeType::options())->mapWithKeys(
                fn (array $o) => [$o['label'] => (string) $o['value']],
            )->all(),
            'Status' => collect(EmployeeStatus::options())->mapWithKeys(
                fn (array $o) => [$o['label'] => (string) $o['value']],
            )->all(),
        ];
    }
}
