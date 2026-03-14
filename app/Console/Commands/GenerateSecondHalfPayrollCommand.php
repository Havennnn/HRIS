<?php

namespace App\Console\Commands;

use App\Services\Admin\Payroll\PayrollGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GenerateSecondHalfPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:generate-second-half
        {--date= : Run command for a specific date (Y-m-d)}
        {--force : Run even when date is not the 25th}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pending payrolls for 16th–EOM (days 26–EOM assumed present, true-up on last day)';

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

        if (! $force && $asOf->day !== 25) {
            $this->info("Skipped: {$asOf->toDateString()} is not the 25th day.");

            return 0;
        }

        $periodStart = $asOf->setDay(16);                    // e.g. Mar 16
        $periodEnd   = $asOf->endOfMonth()->startOfDay();    // e.g. Mar 31
        $assumedFrom = $asOf->setDay(26);                    // Mar 26–31 are assumed present

        $payrolls = $service->generateForAllEmployeesWithAssumedDays($periodStart, $periodEnd, $assumedFrom);

        $this->info("Generated/updated {$payrolls->count()} payroll(s) for {$periodStart->toDateString()} to {$periodEnd->toDateString()} (days 26–EOM assumed).");

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
