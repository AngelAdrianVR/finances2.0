<?php

namespace App\Actions\Calendar;

use App\Models\Calendar;
use App\Models\RecurringIncome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;

class DeleteCalendarEventAction
{
    public function __construct(
        private readonly CalendarService $calendarService,
    ) {}

    /**
     * Delete calendar events based on the deletion mode.
     *
     * @param  string  $mode  'Este' | 'Este y los siguientes' | 'Todos'
     */
    public function execute(Calendar $calendar, string $mode): void
    {
        $title = $calendar->title;

        switch ($mode) {
            case 'Este':
                $calendar->delete();
                break;

            case 'Este y los siguientes':
                $this->calendarService->removeByTitle(
                    $title,
                    $calendar->type,
                    $calendar->user_id,
                    $calendar->date->toDateString()
                );
                // Also delete the representative event if it falls in range
                $calendar->delete();
                // Remove recurring records
                RecurringIncome::where('concept', $title)->where('user_id', $calendar->user_id)->delete();
                RecurringOutcome::where('concept', $title)->where('user_id', $calendar->user_id)->delete();
                break;

            case 'Todos':
                $this->calendarService->removeByTitle($title, $calendar->type, $calendar->user_id);
                // Remove recurring records
                RecurringIncome::where('concept', $title)->where('user_id', $calendar->user_id)->delete();
                RecurringOutcome::where('concept', $title)->where('user_id', $calendar->user_id)->delete();
                break;
        }
    }
}
