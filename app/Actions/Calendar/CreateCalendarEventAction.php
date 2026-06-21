<?php

namespace App\Actions\Calendar;

use App\Models\Calendar;
use App\Models\RecurringIncome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;

class CreateCalendarEventAction
{
    public function __construct(
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(array $data): Calendar
    {
        $data['user_id'] = auth()->id();

        // Create the first event as the representative record
        $calendar = Calendar::create([
            'type'           => $data['type'],
            'title'          => $data['title'],
            'date'           => $data['date'],
            'amount'         => $data['amount'],
            'category'       => $data['category'] ?? null,
            'description'    => $data['description'] ?? null,
            'periodicity'    => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id'        => $data['user_id'],
        ]);

        // Create corresponding recurring record
        $recurringData = [
            'concept'        => $data['title'],
            'periodicity'    => $data['periodicity'],
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'description'    => $data['description'] ?? null,
            'category'       => $data['category'] ?? null,
            'created_at'     => $data['date'],
            'user_id'        => $data['user_id'],
        ];

        if ($data['type'] === 'Ingreso recurrente') {
            RecurringIncome::updateOrCreate(
                ['concept' => $data['title'], 'user_id' => $data['user_id']],
                $recurringData
            );
        } else {
            RecurringOutcome::updateOrCreate(
                ['concept' => $data['title'], 'user_id' => $data['user_id']],
                $recurringData
            );
        }

        // Generate all future occurrences
        $this->calendarService->generateRecurringEvents([
            'type'           => $data['type'],
            'title'          => $data['title'],
            'amount'         => $data['amount'],
            'category'       => $data['category'] ?? null,
            'description'    => $data['description'] ?? null,
            'periodicity'    => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id'        => $data['user_id'],
            'created_at'     => $data['date'],
        ]);

        return $calendar;
    }
}
