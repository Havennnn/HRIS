<?php

namespace App\Console\Commands;

use App\Services\Admin\Payroll\PayrollGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GenerateCutoffPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:generate-cutoff
        {--date= : Run command for a specific date (Y-m-d)}
        {--force : Run even when date is not the 10th}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pending payrolls for 1st–15th (days 11–15 assumed present, true-up on 15th)';

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

        if (! $force && $asOf->day !== 10) {
            $this->info("Skipped: {$asOf->toDateString()} is not the 10th day.");

            return 0;
        }

        $periodStart = $asOf->startOfMonth();  // Mar 1
        $periodEnd   = $asOf->setDay(15);       // Mar 15 (full period)
        $assumedFrom = $asOf->setDay(11);       // Mar 11–15 are assumed present

        $payrolls = $service->generateForAllEmployeesWithAssumedDays($periodStart, $periodEnd, $assumedFrom);

        $this->info("Generated/updated {$payrolls->count()} payroll(s) for {$periodStart->toDateString()} to {$periodEnd->toDateString()} (days 11–15 assumed).");

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
