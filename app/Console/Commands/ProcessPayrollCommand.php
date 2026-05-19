<?php

namespace App\Console\Commands;

use App\Models\PayoutConfiguration;
use App\Services\Admin\Payroll\PayrollGenerationService;
use Illuminate\Console\Command;

class ProcessPayrollCommand extends Command
{
    protected $signature = 'payroll:process {--action=generate : Action to perform: generate or disburse}';

    protected $description = 'Generate or disburse payrolls based on active payout configurations';

    public function handle(PayrollGenerationService $service): int
    {
        $action = $this->option('action');

        if (! in_array($action, ['generate', 'disburse'], true)) {
            $this->error('Invalid action. Use --action=generate or --action=disburse');

            return Command::FAILURE;
        }

        $configs = PayoutConfiguration::query()->where('is_active', true)->get();

        if ($configs->isEmpty()) {
            $this->warn('No active payout configurations found.');

            return Command::SUCCESS;
        }

        $processed = 0;

        foreach ($configs as $config) {
            if ($action === 'generate' && $config->isGenerationDay(now())) {
                $periodStart = $config->resolvePeriodStart(now());
                $periodEnd = $config->resolvePeriodEnd(now());
                $assumedFrom = now()->startOfDay()->setDay($config->assumed_from_day);

                if (now()->startOfDay()->greaterThanOrEqualTo($periodEnd)) {
                    $service->generateForAllEmployees($periodStart, $periodEnd);
                } else {
                    $service->generateForAllEmployeesWithAssumedDays($periodStart, $periodEnd, $assumedFrom);
                }

                $this->info("Generated payrolls for: {$config->name}");
                $processed++;
            }

            if ($action === 'disburse' && $config->isDisburseDay(now())) {
                $periodStart = $config->resolvePeriodStart(now());
                $periodEnd = $config->resolvePeriodEnd(now());

                $count = $service->trueUpApprovedPayrollsForReleaseDate($periodStart, $periodEnd);

                $this->info("Disbursed {$count} payrolls for: {$config->name}");
                $processed++;
            }
        }

        if ($processed === 0) {
            $this->info('No payout configurations matched today\'s date.');
        }

        return Command::SUCCESS;
    }
}
