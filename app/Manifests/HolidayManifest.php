<?php
declare(strict_types=1);
namespace App\Manifests;
use App\Enums\Type\HolidayType;
use PiaCore\Contracts\Import\ManifestStructure;

class HolidayManifest implements ManifestStructure
{
    public function name(): string { return 'Holiday Import Template'; }
    public function headers(): array { return ['Name', 'Date', 'Type', 'Is Paid', 'Description']; }
    public function exampleRow(): array {
        return [
            'Name' => 'Independence Day',
            'Date' => '2026-06-12',
            'Type' => 'Regular Holiday',
            'Is Paid' => 'Yes',
            'Description' => 'Araw ng Kalayaan',
        ];
    }
    public function columnOptions(): array {
        return [
            'Type' => collect(HolidayType::options())->mapWithKeys(fn(array $o) => [$o['label'] => (string) $o['value']])->all(),
            'Is Paid' => ['Yes' => '1', 'No' => '0'],
        ];
    }
}
