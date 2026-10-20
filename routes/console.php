<?php

use App\Console\Commands\SendLayawayRemindersCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::registerCommand(new SendLayawayRemindersCommand);

Schedule::command('layaway:send-reminders')->dailyAt('09:00');
