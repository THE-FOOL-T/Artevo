<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| No scheduled jobs exist yet in Phase 1. Future phases register their
| own Schedule::job(...)/Schedule::command(...) calls here, for example:
|   - Phase 7 (Auctions): closing expired auctions and selecting winners
|   - Phase 9 (Integrations): warming the homepage/analytics cache
| Run locally with: php artisan schedule:work
*/
