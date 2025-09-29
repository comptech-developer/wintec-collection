<?php

use App\Jobs\CheckPaymentStatusJob;
use App\Jobs\SendMonthlySMSNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CheckPaymentStatusJob)->dailyAt('23:00');

Schedule::job(new SendMonthlySMSNotification)->monthlyOn(Carbon::now()->endOfMonth()->day, '12:00');

$lastMonthDay = Carbon::now()->endOfMonth();
$firstDayOfLastWeek = $lastMonthDay->copy()->startOfWeek(Carbon::MONDAY);

Schedule::job(new SendMonthlySMSNotification)->monthlyOn($firstDayOfLastWeek->day, '12:00');
