<?php

namespace Tests\Unit\Services\Admin\Payroll;

use App\Services\Admin\Payroll\PayrollContributionService;
use Tests\TestCase;

class PayrollContributionServiceTest extends TestCase
{
    private PayrollContributionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payroll.pay_periods_per_month' => 2,
            'payroll.money_precision' => 2,
            'payroll.contributions.prorate_per_payroll' => true,
            'payroll.bir_withholding_monthly' => [
                ['from' => 0, 'to' => 20833, 'base' => 0, 'rate' => 0.00],
                ['from' => 20833, 'to' => 33333, 'base' => 0, 'rate' => 0.15],
                ['from' => 33333, 'to' => 66667, 'base' => 1875, 'rate' => 0.20],
                ['from' => 66667, 'to' => 166667, 'base' => 8541.80, 'rate' => 0.25],
                ['from' => 166667, 'to' => 666667, 'base' => 33541.80, 'rate' => 0.30],
                ['from' => 666667, 'to' => null, 'base' => 183541.80, 'rate' => 0.35],
            ],
            'payroll.sss_table' => [
                ['from' => 0, 'to' => 5000, 'contribution' => 200],
                ['from' => 5000, 'to' => 7500, 'contribution' => 350],
                ['from' => 7500, 'to' => 10000, 'contribution' => 500],
                ['from' => 10000, 'to' => 12500, 'contribution' => 650],
                ['from' => 12500, 'to' => 15000, 'contribution' => 800],
                ['from' => 15000, 'to' => 17500, 'contribution' => 950],
                ['from' => 17500, 'to' => 20000, 'contribution' => 1100],
                ['from' => 20000, 'to' => 25000, 'contribution' => 1250],
                ['from' => 25000, 'to' => null, 'contribution' => 1350],
            ],
            'payroll.pagibig' => [
                'threshold_salary' => 1500,
                'rate_below_threshold' => 0.01,
                'rate_at_or_above_threshold' => 0.02,
                'max_salary_base' => 5000,
            ],
            'payroll.philhealth' => [
                'premium_rate' => 0.05,
                'employee_share' => 0.50,
                'min_salary_base' => 10000,
                'max_salary_base' => 100000,
            ],
        ]);

        $this->service = new PayrollContributionService;
    }

    public function test_it_calculates_sss_contribution(): void
    {
        $this->assertEquals(100.00, $this->service->calculateSSS(4500));  // bracket 0-5000: 200 / 2
        $this->assertEquals(675.00, $this->service->calculateSSS(30000)); // bracket 25000+: 1350 / 2
        $this->assertEquals(675.00, $this->service->calculateSSS(100000)); // bracket 25000+: 1350 / 2
    }

    public function test_it_calculates_pagibig_contribution(): void
    {
        // Below threshold: min(1000, 5000) * 0.01 / 2
        $this->assertEqualsWithDelta(5.0, $this->service->calculatePagibig(1000), 0.01);

        // Above threshold: min(50000, 5000) * 0.02 / 2
        $this->assertEquals(50.00, $this->service->calculatePagibig(50000));
    }

    public function test_it_calculates_philhealth_contribution(): void
    {
        // min(max(10000, 10000), 100000) * 0.05 * 0.50 / 2
        $this->assertEquals(125.00, $this->service->calculatePhilHealth(10000));

        // min(max(50000, 10000), 100000) * 0.05 * 0.50 / 2
        $this->assertEquals(625.00, $this->service->calculatePhilHealth(50000));
    }

    public function test_it_calculates_tax_for_non_taxable_income(): void
    {
        // Below 20833 monthly => no tax
        $tax = $this->service->calculateTax(10000);
        $this->assertEquals(0, $tax);
    }

    public function test_it_calculates_tax_for_taxable_income(): void
    {
        // Monthly taxable = 30000 * 2 = 60000 (semi-monthly)
        // Bracket: 33333 - 66667, base=1875, rate=0.20
        // Monthly: 1875 + (60000 - 33333) * 0.20 = 1875 + 5333.40 = 7208.40
        // Per period: 7208.40 / 2 = 3604.20
        $tax = $this->service->calculateTax(30000);
        $this->assertEquals(3604.20, $tax);
    }

    public function test_it_calculates_all_contributions_together(): void
    {
        $result = $this->service->calculateAll(
            basicSalary: 50000,
            grossPay: 20000,
            lateDeduction: 0,
            customDeduction: 0,
        );

        $this->assertArrayHasKey('tax', $result);
        $this->assertArrayHasKey('sss', $result);
        $this->assertArrayHasKey('pagibig', $result);
        $this->assertArrayHasKey('philhealth', $result);

        $this->assertEquals(675.00, $result['sss']);       // 1350 / 2 (bracket 25000+)
        $this->assertEquals(50.00, $result['pagibig']);    // min(50000, 5000) * 0.02 / 2
        $this->assertEquals(625.00, $result['philhealth']); // 50000 * 0.05 * 0.50 / 2
        $this->assertTrue($result['tax'] >= 0);
    }

    public function test_it_returns_zero_divisor_when_proration_disabled(): void
    {
        config(['payroll.contributions.prorate_per_payroll' => false]);

        $service = new PayrollContributionService;
        $this->assertEquals(1, $service->getContributionDivisor());
    }
}
