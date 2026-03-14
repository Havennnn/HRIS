<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// First half: Mar 1–15
Schedule::command('payroll:generate-cutoff')->monthlyOn(10, '23:55');
Schedule::command('payroll:auto-disburse')->monthlyOn(15, '17:00');

// Second half: Mar 16–EOM
Schedule::command('payroll:generate-second-half')->monthlyOn(25, '23:55');
Schedule::command('payroll:auto-disburse-second-half')->lastDayOfMonth('17:00');
