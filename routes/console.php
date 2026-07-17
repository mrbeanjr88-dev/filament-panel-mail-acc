<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincronizare automată a mailurilor în carantină (pe queue, fără suprapunere).
Schedule::command('emails:sync --queue')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Arhivare emailuri vechi (zilnic, la 2 AM)
if (config('email-customs.archive.enabled')) {
    Schedule::command('email:archive')
        ->dailyAt('02:00')
        ->withoutOverlapping();
}
