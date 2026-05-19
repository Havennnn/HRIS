<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Payout schedule — reads from active PayoutConfiguration records
Schedule::command('payroll:process --action=generate')->dailyAt('23:55');
Schedule::command('payroll:process --action=disburse')->dailyAt('17:00');
