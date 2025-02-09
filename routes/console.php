<?php

use App\Models\Calendar;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// comando para registrar ingresos o egresos programados en calendario
Schedule::command('calendar:process-scheduled')->daily();