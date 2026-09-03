<?php

use Illuminate\Support\Facades\Schedule;

// comando para registrar ingresos o egresos programados en calendario
Schedule::command('calendar:process-scheduled')->daily();

// Recordatorios diarios de gastos a crédito (todos los días a las 9:00 am)
Schedule::command('outcomes:send-payment-reminders')->dailyAt('09:00')->withoutOverlapping();
