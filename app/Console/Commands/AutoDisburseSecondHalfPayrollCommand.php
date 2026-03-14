<?php

namespace App\Console\Commands;

use App\Services\Admin\Payroll\PayrollGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class AutoDisburseSecondHalfPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:auto-disburse-second-half
        {--date= : Run command for a specific date (Y-m-d)}
        {--force : Run even when date is not the last day of the month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'True-up actual attendance for days 26–EOM, then disburse approved payrolls on the last day of the month';

    /**
     * Execute the console command.
     */
    public function handle(PayrollGenerationService $service): int
    {
        try {
            $asOf = $this->resolveDate();
        } catch (\InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $force = (bool) $this->option('force');

        if (! $force && ! $asOf->isLastOfMonth()) {
            $this->info("Skipped: {$asOf->toDateString()} is not the last day of the month.");

            return 0;
        }

        $disbursedCount = $service->trueUpApprovedPayrollsForSecondHalf($asOf);

        $this->info("True-up complete. Disbursed {$disbursedCount} payroll(s) for {$asOf->toDateString()}.");

        return 0;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function resolveDate(): CarbonImmutable
    {
        $dateInput = $this->option('date');

        if (! is_string($dateInput) || trim($dateInput) === '') {
            return CarbonImmutable::today();
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m-d', trim($dateInput))->startOfDay();
        } catch (\Throwable) {
            throw new \InvalidArgumentException('Invalid --date format. Use Y-m-d.');
        }
    }
}
