<?php

namespace Database\Seeders;

use App\Constant\PayoutConfigurationDefaults;
use App\Models\PayoutConfiguration;
use Illuminate\Database\Seeder;

class PayoutConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PayoutConfigurationDefaults::all() as $data) {
            PayoutConfiguration::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
