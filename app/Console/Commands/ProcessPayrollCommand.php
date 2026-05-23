<?php

namespace App\Console\Commands;

use App\Models\PayoutConfiguration;
use App\Notifications\NewRequestSubmitted;
use App\Notifications\Notifier;
use App\Notifications\PayrollFailed;
use App\Notifications\PayrollGenerated;
use App\Services\Admin\Payroll\PayrollGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PiaCore\Models\Admin;
use Throwable;

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

                try {
                    if (now()->startOfDay()->greaterThanOrEqualTo($periodEnd)) {
                        $payrolls = $service->generateForAllEmployees($periodStart, $periodEnd);
                    } else {
                        $payrolls = $service->generateForAllEmployeesWithAssumedDays($periodStart, $periodEnd, $assumedFrom);
                    }

                    // Notify each employee about their generated payroll
                    foreach ($payrolls as $payroll) {
                        try {
                            Notifier::notify($payroll->employee, new PayrollGenerated($payroll));
                        } catch (Throwable $e) {
                            Log::warning("Failed to notify employee #{$payroll->employee_id} about payroll", [
                                'payroll_id' => $payroll->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    $this->info("Generated and notified for payrolls: {$config->name}");
                    $processed++;
                } catch (Throwable $e) {
                    $this->error("Payroll generation failed for {$config->name}: {$e->getMessage()}");

                    // Notify admins about the failure
                    try {
                        $admins = Admin::all();
                        foreach ($admins as $admin) {
                            Notifier::notify($admin, new PayrollFailed(
                                $periodStart->format('Y-m-d'),
                                $periodEnd->format('Y-m-d'),
                                $e->getMessage()
                            ));
                        }
                    } catch (Throwable $notifyError) {
                        Log::error('Failed to notify admins about payroll failure', [
                            'error' => $notifyError->getMessage(),
                        ]);
                    }
                }
            }

            if ($action === 'disburse' && $config->isDisburseDay(now())) {
                $periodStart = $config->resolvePeriodStart(now());
                $periodEnd = $config->resolvePeriodEnd(now());

                try {
                    $count = $service->trueUpApprovedPayrollsForReleaseDate($periodStart, $periodEnd);

                    $this->info("Disbursed {$count} payrolls for: {$config->name}");
                    $processed++;
                } catch (Throwable $e) {
                    $this->error("Payroll disbursement failed for {$config->name}: {$e->getMessage()}");

                    // Notify admins about the failure
                    try {
                        $admins = Admin::all();
                        foreach ($admins as $admin) {
                            Notifier::notify($admin, new PayrollFailed(
                                $periodStart->format('Y-m-d'),
                                $periodEnd->format('Y-m-d'),
                                $e->getMessage()
                            ));
                        }
                    } catch (Throwable $notifyError) {
                        Log::error('Failed to notify admins about disbursement failure', [
                            'error' => $notifyError->getMessage(),
                        ]);
                    }
                }
            }
        }

        if ($processed === 0) {
            $this->info('No payout configurations matched today\'s date.');
        }

        return Command::SUCCESS;
    }
}
