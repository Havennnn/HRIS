<?php
declare(strict_types=1);
namespace App\Manifests;
use App\Models\Department;
use PiaCore\Contracts\Import\ManifestStructure;

class PositionManifest implements ManifestStructure
{
    public function name(): string { return 'Position Import Template'; }
    public function headers(): array { return ['Name', 'Department', 'Level', 'Salary', 'Allowance']; }
    public function exampleRow(): array {
        return [
            'Name' => 'Software Engineer',
            'Department' => 'Information Technology',
            'Level' => 'Junior',
            'Salary' => '30000',
            'Allowance' => '1500',
        ];
    }
    public function columnOptions(): array {
        $departments = Department::query()->pluck('name', 'id')->map(fn($n) => (string) $n)->all();
        return ['Department' => array_flip($departments)];
    }
}
