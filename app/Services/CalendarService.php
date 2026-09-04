<?php

namespace App\Services;

use App\Models\Calendar;
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
     * @param  bool  $futureOnly  Whether to skip occurrences that already happened or occur today.
     *                            Used by interactive flows: a same-day event would never be processed
     *                            because the scheduler runs at 00:00.
     * @return int Number of calendar events created.
     */
    public function generateRecurringEvents(array $data, bool $futureOnly = false): int
    {
        $dates = $this->calculateDates(
            Carbon::parse($data['created_at']),
            $data['periodicity']
        );

        $today = Carbon::today();
        $count = 0;
        $chunk = [];
        foreach ($dates as $date) {
            // Keep strictly future occurrences (never re-create past or same-day events).
            if ($futureOnly && $date->lte($today)) {
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
    public function generateRecurringEventsFromModel(Model $recurring, string $type, bool $futureOnly = false): int
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
        ], $futureOnly);
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
