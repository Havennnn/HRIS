<?php
declare(strict_types=1);
namespace App\Manifests;
use App\Enums\Status\CareerStatus;
use App\Models\Position;
use PiaCore\Contracts\Import\ManifestStructure;

class CareerManifest implements ManifestStructure
{
    public function name(): string { return 'Career Import Template'; }
    public function headers(): array { return ['Position', 'Description', 'Salary', 'Status']; }
    public function exampleRow(): array {
        return [
            'Position' => 'Software Engineer (Information Technology)',
            'Description' => 'Develop and maintain software applications.',
            'Salary' => '30000-50000',
            'Status' => 'Published',
        ];
    }
    public function columnOptions(): array {
        $positions = Position::query()->with('department')->get()
            ->mapWithKeys(fn($p) => [$p->name . ($p->department ? " ({$p->department->name})" : '') => (string) $p->id])
            ->all();
        return [
            'Position' => $positions,
            'Status' => collect(CareerStatus::options())->mapWithKeys(fn(array $o) => [$o['label'] => (string) $o['value']])->all(),
        ];
    }
}
