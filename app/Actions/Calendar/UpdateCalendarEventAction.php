<?php

namespace App\Actions\Calendar;

use App\Models\Calendar;
use App\Models\RecurringIncome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;

class UpdateCalendarEventAction
{
    public function __construct(
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(Calendar $calendar, array $data): Calendar
    {
        $oldTitle = $calendar->title;

        // Remove old recurring records
        if ($calendar->type === 'Ingreso recurrente') {
            RecurringIncome::where('concept', $oldTitle)
                ->where('user_id', auth()->id())
                ->delete();
        } else {
            RecurringOutcome::where('concept', $oldTitle)
                ->where('user_id', auth()->id())
                ->delete();
        }

        // Delete old calendar events by title
        $this->calendarService->removeByTitle($oldTitle);

        // Update the representative event
        $calendar->update($data);

        // Re-create recurring record
        $recurringData = [
            'concept'        => $data['title'],
            'periodicity'    => $data['periodicity'],
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'description'    => $data['description'] ?? null,
            'category'       => $data['category'] ?? null,
            'created_at'     => $data['date'],
            'user_id'        => auth()->id(),
        ];

        if ($data['type'] === 'Ingreso recurrente') {
            RecurringIncome::updateOrCreate(
                ['concept' => $data['title'], 'user_id' => auth()->id()],
                $recurringData
            );
        } else {
            RecurringOutcome::updateOrCreate(
                ['concept' => $data['title'], 'user_id' => auth()->id()],
                $recurringData
            );
        }

        // Regenerate future occurrences
        $this->calendarService->generateRecurringEvents([
            'type'           => $data['type'],
            'title'          => $data['title'],
            'amount'         => $data['amount'],
            'category'       => $data['category'] ?? null,
            'description'    => $data['description'] ?? null,
            'periodicity'    => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id'        => auth()->id(),
            'created_at'     => $data['date'],
        ]);

        return $calendar->fresh();
    }
}
