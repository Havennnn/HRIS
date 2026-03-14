<?php

namespace App\Console\Commands;

use App\Services\Admin\Payroll\PayrollGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class AutoDisbursePayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:auto-disburse
        {--date= : Run command for a specific date (Y-m-d)}
        {--force : Run even when date is not the 15th}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'True-up actual attendance for days 11–15, then disburse approved payrolls on the 15th';

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

        if (! $force && $asOf->day !== 15) {
            $this->info("Skipped: {$asOf->toDateString()} is not the 15th day.");

            return 0;
        }

        $disbursedCount = $service->trueUpApprovedPayrollsForReleaseDate($asOf);

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
