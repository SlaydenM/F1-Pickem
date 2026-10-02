<?php

use Illuminate\Support\Facades\Schedule;
use App\Services\SmsService;
use App\Services\PickemService;
use Carbon\Carbon;

// Run every morning at midnight to schedule all day's race/pick notifications
Schedule::call(function () {
    app(SmsService::class)->notifySchedule();
})->dailyAt('00:00');

// Run hourly to check for results of the next session and send notifications to users who opted in
Schedule::call(function () {
    app(PickemService::class)->pingResultsApi();
})->hourlyAt(0);

Schedule::call(function () {
    app(PickemService::class)->updateSchedule(Carbon::now()->year);
})->weeklyAt(3);
