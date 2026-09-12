<?php

namespace App\Console\Commands;

use App\Models\RecurringIncome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use Illuminate\Console\Command;

class SyncRecurringCalendarEvents extends Command
{
    protected $signature = 'calendar:sync-recurring {--user= : Sincroniza unicamente los recurrentes de este usuario}';

    protected $description = 'Asegura que los ingresos y gastos recurrentes activos tengan sus eventos futuros en el calendario';

    public function handle(CalendarService $calendarService): int
    {
        $userId = $this->option('user');

        $incomes = RecurringIncome::query()
            ->active()
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->get();

        $outcomes = RecurringOutcome::query()
            ->active()
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->get();

        $created = 0;

        foreach ($incomes as $income) {
            $created += $calendarService->ensureRecurringEventsFromModel($income, 'Ingreso recurrente');
        }

        foreach ($outcomes as $outcome) {
            $created += $calendarService->ensureRecurringEventsFromModel($outcome, 'Gasto fijo');
        }

        $this->info("Recurrentes sincronizados. Eventos de calendario creados: {$created}");

        return self::SUCCESS;
    }
}
