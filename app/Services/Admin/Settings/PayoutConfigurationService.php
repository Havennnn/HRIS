<?php

namespace App\Services\Admin\Settings;

use App\Constant\PayoutConfigurationDefaults;
use App\Models\PayoutConfiguration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PayoutConfigurationService
{
    public function getAll(): array
    {
        return PayoutConfiguration::query()
            ->orderBy('period_start_day')
            ->get()
            ->all();
    }

    public function getBySlug(string $slug): ?PayoutConfiguration
    {
        return PayoutConfiguration::query()->where('slug', $slug)->first();
    }

    public function update(PayoutConfiguration $config, array $data): RedirectResponse
    {
        DB::transaction(function () use ($config, $data) {
            $config->update([
                'name' => $data['name'],
                'period_start_day' => $data['period_start_day'],
                'period_end_day' => $data['period_end_day'] ?? null,
                'period_end_is_last_day' => $data['period_end_is_last_day'] ?? false,
                'cutoff_generation_day' => $data['cutoff_generation_day'],
                'cutoff_disburse_day' => $data['cutoff_disburse_day'] ?? null,
                'disburse_is_last_day' => $data['disburse_is_last_day'] ?? false,
                'assumed_from_day' => $data['assumed_from_day'],
                'is_active' => $data['is_active'] ?? true,
            ]);
        });

        return redirect()->route('settings.payout-configurations.index')
            ->with('success', 'Payout configuration updated successfully.');
    }

    public function resetToDefaults(): RedirectResponse
    {
        foreach (PayoutConfigurationDefaults::all() as $data) {
            PayoutConfiguration::updateOrCreate(['slug' => $data['slug']], $data);
        }

        return redirect()->route('settings.payout-configurations.index')
            ->with('success', 'Payout configurations reset to defaults.');
    }
}
