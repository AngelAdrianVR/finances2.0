<?php

namespace App\Services;

use App\Models\Calendar;
use App\Models\Income;
use App\Models\Outcome;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Handles generation of recurring calendar events.
 * Eliminates the ~10 duplicated switch blocks across controllers.
 */
class CalendarService
{
    /**
     * Generate calendar events for a recurring item.
     *
     * @param array{
     *     type: string,
     *     title: string,
     *     amount: float,
     *     category: ?string,
     *     description: ?string,
     *     periodicity: string,
     *     payment_method: ?string,
     *     user_id: int,
     *     created_at: string|Carbon,
     * } $data
     * @param  bool  $futureOnly  When true, skip occurrences that already passed.
     *                            Today is always kept so the scheduler can register the same-day movement.
     * @param  bool  $excludeStart  When true, drop the first occurrence (the start date). Used when that
     *                              date is already covered by an immediate movement or by the
     *                              representative calendar event, to avoid duplicates.
     * @return int Number of calendar events created.
     */
    public function generateRecurringEvents(array $data, bool $futureOnly = false, bool $excludeStart = false): int
    {
        $dates = $this->calculateDates(
            Carbon::parse($data['created_at']),
            $data['periodicity']
        );

        // The start occurrence is already registered elsewhere (immediate movement / representative event).
        if ($excludeStart && ! empty($dates)) {
            array_shift($dates);
        }

        $today = Carbon::today();
        $count = 0;
        $chunk = [];
        foreach ($dates as $date) {
            // Keep today and future occurrences; only skip days that already passed.
            if ($futureOnly && $date->lt($today)) {
                continue;
            }

            $chunk[] = [
                'type' => $data['type'],
                'title' => $data['title'],
                'date' => $date->toDateString(),
                'amount' => $data['amount'],
                'category' => $data['category'] ?? null,
                'description' => $data['description'] ?? null,
                'periodicity' => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id' => $data['user_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Bulk insert in chunks to avoid memory issues with daily events
            if (count($chunk) >= 200) {
                Calendar::insert($chunk);
                $count += count($chunk);
                $chunk = [];
            }
        }

        if (! empty($chunk)) {
            Calendar::insert($chunk);
            $count += count($chunk);
        }

        return $count;
    }

    /**
     * Generate calendar events directly from a recurring model.
     *
     * @param  \App\Models\RecurringIncome|\App\Models\RecurringOutcome  $recurring
     */
    public function generateRecurringEventsFromModel(Model $recurring, string $type, bool $futureOnly = false, bool $excludeStart = false): int
    {
        return $this->generateRecurringEvents([
            'type' => $type,
            'title' => $recurring->concept,
            'amount' => $recurring->amount,
            'category' => $recurring->category,
            'description' => $recurring->description,
            'periodicity' => $recurring->periodicity,
            'payment_method' => $recurring->payment_method,
            'user_id' => $recurring->user_id,
            'created_at' => $recurring->created_at,
        ], $futureOnly, $excludeStart);
    }

    /**
     * Ensure every upcoming occurrence of a recurring item exists in the calendar
     * without creating duplicates. Idempotent: safe to run as many times as needed.
     *
     * It skips dates that already have a scheduled event and, for the start date,
     * it skips when a movement was already registered (e.g. incomes/outcomes created
     * with the "recurring" flag register their first occurrence immediately).
     *
     * @param  \App\Models\RecurringIncome|\App\Models\RecurringOutcome  $recurring
     * @return int Number of calendar events created.
     */
    public function ensureRecurringEventsFromModel(Model $recurring, string $type): int
    {
        if (empty($recurring->periodicity)) {
            return 0;
        }

        $start = Carbon::parse($recurring->created_at)->startOfDay();
        $today = Carbon::today();

        $dates = array_values(array_filter(
            $this->calculateDates($start, $recurring->periodicity),
            fn (Carbon $date) => $date->gte($today)
        ));

        // Skip the first occurrence when a movement already exists for that day.
        if (! empty($dates) && $dates[0]->isSameDay($start)) {
            $movementModel = $type === 'Ingreso recurrente' ? Income::class : Outcome::class;

            $alreadyRegistered = $movementModel::query()
                ->where('user_id', $recurring->user_id)
                ->where('concept', $recurring->concept)
                ->whereDate('created_at', $start->toDateString())
                ->exists();

            if ($alreadyRegistered) {
                array_shift($dates);
            }
        }

        if (empty($dates)) {
            return 0;
        }

        // Dates that already have a scheduled event for this recurring item.
        $existing = Calendar::query()
            ->where('user_id', $recurring->user_id)
            ->where('type', $type)
            ->where('title', $recurring->concept)
            ->where('date', '>=', $today->toDateString())
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $count = 0;
        $chunk = [];
        foreach ($dates as $date) {
            if ($existing->has($date->toDateString())) {
                continue;
            }

            $chunk[] = [
                'type' => $type,
                'title' => $recurring->concept,
                'date' => $date->toDateString(),
                'amount' => $recurring->amount,
                'category' => $recurring->category,
                'description' => $recurring->description,
                'periodicity' => $recurring->periodicity,
                'payment_method' => $recurring->payment_method,
                'user_id' => $recurring->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($chunk) >= 200) {
                Calendar::insert($chunk);
                $count += count($chunk);
                $chunk = [];
            }
        }

        if (! empty($chunk)) {
            Calendar::insert($chunk);
            $count += count($chunk);
        }

        return $count;
    }

    /**
     * Calculate all occurrence dates for a given periodicity.
     *
     * @return Carbon[]
     */
    public function calculateDates(Carbon $startDate, string $periodicity, ?Carbon $endDate = null): array
    {
        $dates = [];
        $cursor = $startDate->copy();

        switch ($periodicity) {
            case 'Todos los días':
            case 'Todos los dias':
                $endDate ??= Carbon::now()->endOfYear();
                while ($cursor->lte($endDate)) {
                    $dates[] = $cursor->copy();
                    $cursor->addDay();
                }
                break;

            case 'Semanal':
                $endDate ??= Carbon::now()->endOfYear();
                while ($cursor->lte($endDate)) {
                    $dates[] = $cursor->copy();
                    $cursor->addWeek();
                }
                break;

            case 'Mensual':
                $endDate ??= Carbon::now()->endOfYear();
                while ($cursor->lte($endDate)) {
                    $dates[] = $cursor->copy();
                    $cursor->addMonth();
                }
                break;

            case 'Anual':
                $endDate ??= $startDate->copy()->addYears(3);
                while ($cursor->lte($endDate)) {
                    $dates[] = $cursor->copy();
                    $cursor->addYear();
                }
                break;
        }

        return $dates;
    }

    /**
     * Remove calendar events matching a title.
     *
     * @param  string  $title  Recurring title/concept.
     * @param  string|null  $type  Restrict to a calendar type ('Gasto fijo', 'Ingreso recurrente', ...).
     * @param  int|null  $userId  Restrict to a user (avoids deleting events from other users).
     * @param  string|null  $fromDate  Only delete events on or after this date (Y-m-d).
     */
    public function removeByTitle(
        string $title,
        ?string $type = null,
        ?int $userId = null,
        ?string $fromDate = null
    ): int {
        return Calendar::query()
            ->where('title', $title)
            ->when($type !== null, fn ($query) => $query->where('type', $type))
            ->when($userId !== null, fn ($query) => $query->where('user_id', $userId))
            ->when($fromDate !== null, fn ($query) => $query->where('date', '>=', $fromDate))
            ->delete();
    }
}
