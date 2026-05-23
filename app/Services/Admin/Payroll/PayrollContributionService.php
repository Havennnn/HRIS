<?php

namespace App\Services\Admin\Payroll;

class PayrollContributionService
{
    /**
     * Calculate withholding tax using PH monthly brackets,
     * normalized back to the current payroll period (e.g. semi-monthly).
     */
    public function calculateTax(float $taxableCompensation): float
    {
        $payPeriodsPerMonth = max(1, (int) config('payroll.pay_periods_per_month', 2));
        $monthlyTaxable = $taxableCompensation * $payPeriodsPerMonth;

        $brackets = config('payroll.bir_withholding_monthly', []);

        foreach ($brackets as $bracket) {
            $withinUpper = $bracket['to'] === null || $monthlyTaxable < $bracket['to'];

            if ($monthlyTaxable >= $bracket['from'] && $withinUpper) {
                $monthlyTax = $bracket['base'] + (($monthlyTaxable - $bracket['from']) * $bracket['rate']);

                return $this->roundMoney(max(0, $monthlyTax / $payPeriodsPerMonth));
            }
        }

        return 0;
    }

    /**
     * Calculate SSS contribution.
     * Uses config-driven SSS contribution table.
     */
    public function calculateSSS(float $basicSalary): float
    {
        $table = config('payroll.sss_table', []);
        $divisor = $this->getContributionDivisor();

        foreach ($table as $row) {
            $withinUpper = $row['to'] === null || $basicSalary < $row['to'];

            if ($basicSalary >= $row['from'] && $withinUpper) {
                $monthlyContribution = (float) $row['contribution'];

                return $this->roundMoney($monthlyContribution / $divisor);
            }
        }

        return 0;
    }

    /**
     * Calculate Pag-IBIG employee contribution.
     */
    public function calculatePagibig(float $basicSalary): float
    {
        $thresholdSalary = (float) config('payroll.pagibig.threshold_salary', 1500);
        $rateBelow = (float) config('payroll.pagibig.rate_below_threshold', 0.01);
        $rateAtOrAbove = (float) config('payroll.pagibig.rate_at_or_above_threshold', 0.02);
        $maxSalaryBase = (float) config('payroll.pagibig.max_salary_base', 5000);
        $divisor = $this->getContributionDivisor();

        $salaryBase = min($basicSalary, $maxSalaryBase);
        $rate = $basicSalary < $thresholdSalary ? $rateBelow : $rateAtOrAbove;

        $monthlyContribution = $salaryBase * $rate;

        return $this->roundMoney($monthlyContribution / $divisor);
    }

    /**
     * Calculate PhilHealth employee contribution.
     */
    public function calculatePhilHealth(float $basicSalary): float
    {
        $premiumRate = (float) config('payroll.philhealth.premium_rate', 0.05);
        $employeeShare = (float) config('payroll.philhealth.employee_share', 0.50);
        $minSalaryBase = (float) config('payroll.philhealth.min_salary_base', 10000);
        $maxSalaryBase = (float) config('payroll.philhealth.max_salary_base', 100000);
        $divisor = $this->getContributionDivisor();

        $salaryBase = min(max($basicSalary, $minSalaryBase), $maxSalaryBase);

        $monthlyContribution = $salaryBase * $premiumRate * $employeeShare;

        return $this->roundMoney($monthlyContribution / $divisor);
    }

    /**
     * Determine how monthly contribution amounts are divided per payroll run.
     */
    public function getContributionDivisor(): int
    {
        $proratePerPayroll = (bool) config('payroll.contributions.prorate_per_payroll', true);

        if (! $proratePerPayroll) {
            return 1;
        }

        return max(1, (int) config('payroll.pay_periods_per_month', 2));
    }

    /**
     * Calculate all mandatory government contributions for a payroll period.
     *
     * @return array{tax: float, sss: float, pagibig: float, philhealth: float}
     */
    public function calculateAll(float $basicSalary, float $grossPay, float $lateDeduction, float $customDeduction): array
    {
        $taxableGrossPay = $this->roundMoney(max(0, $grossPay - $lateDeduction - $customDeduction));

        $sss = $this->calculateSSS($basicSalary);
        $pagibig = $this->calculatePagibig($basicSalary);
        $philhealth = $this->calculatePhilHealth($basicSalary);
        $taxableCompensation = max(0, $taxableGrossPay - $sss - $pagibig - $philhealth);
        $tax = $this->calculateTax($taxableCompensation);

        return [
            'tax' => $tax,
            'sss' => $sss,
            'pagibig' => $pagibig,
            'philhealth' => $philhealth,
        ];
    }

    /**
     * Round monetary values consistently based on payroll config precision.
     */
    protected function roundMoney(float $amount): float
    {
        $precision = max(0, (int) config('payroll.money_precision', 2));

        return round($amount, $precision);
    }
}
