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

    public function extension(): string
    {
        return 'csv';
    }

    public function headers(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'position',
            'type',
            'status',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'position' => 'Software Engineer',
            'type' => 'Regular',
            'status' => 'Active',
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
            'position' => $positions,
            'type' => collect(EmployeeType::options())->mapWithKeys(
                fn (array $o) => [$o['label'] => (string) $o['value']],
            )->all(),
            'status' => collect(EmployeeStatus::options())->mapWithKeys(
                fn (array $o) => [$o['label'] => (string) $o['value']],
            )->all(),
        ];
    }

    public function filename(): string
    {
        return $this->name() . '.' . $this->extension();
    }

    public function mimeType(): string
    {
        return 'text/csv; charset=utf-8';
    }
}
