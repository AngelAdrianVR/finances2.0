<?php

namespace App\Services;

use App\Models\Calendar;
use Carbon\Carbon;

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
     * @return int Number of calendar events created.
     */
    public function generateRecurringEvents(array $data): int
    {
        $dates = $this->calculateDates(
            Carbon::parse($data['created_at']),
            $data['periodicity']
        );

        $count = 0;
        $chunk = [];
        foreach ($dates as $date) {
            $chunk[] = [
                'type'           => $data['type'],
                'title'          => $data['title'],
                'date'           => $date->toDateString(),
                'amount'         => $data['amount'],
                'category'       => $data['category'] ?? null,
                'description'    => $data['description'] ?? null,
                'periodicity'    => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id'        => $data['user_id'],
                'created_at'     => now(),
                'updated_at'     => now(),
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
     * Remove all calendar events matching a title, optionally from a specific date forward.
     */
    public function removeByTitle(string $title, ?string $fromDate = null): int
    {
        $query = Calendar::where('title', $title);

        if ($fromDate !== null) {
            $query->where('date', '>=', $fromDate);
        }

        return $query->delete();
    }
}
