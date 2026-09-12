<?php

use Illuminate\Support\Facades\Schedule;

// Sincroniza los recurrentes activos antes de procesar los del dia (genera los eventos faltantes, incluido hoy).
Schedule::command('calendar:sync-recurring')->dailyAt('06:50')->withoutOverlapping();

// comando para registrar ingresos o egresos programados en calendario
Schedule::command('calendar:process-scheduled')->dailyAt('07:00')->withoutOverlapping();

// Recordatorios diarios de gastos a crédito (todos los días a las 9:00 am)
Schedule::command('outcomes:send-payment-reminders')->dailyAt('09:00')->withoutOverlapping();
