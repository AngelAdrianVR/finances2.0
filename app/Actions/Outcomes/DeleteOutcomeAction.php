<?php

namespace App\Actions\Outcomes;

use App\Models\Outcome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use App\Services\TotalMoneyService;

class DeleteOutcomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    /**
     * Delete a single outcome, adjusting balance and cleaning up recurring records.
     */
    public function execute(Outcome $outcome): void
    {
        $amount = $outcome->amount;
        $concept = $outcome->concept;

        $outcome->delete();

        // Add amount back to global balance
        $this->totalMoneyService->increment(auth()->user(), $amount);

        // Clean up recurring records
        RecurringOutcome::where('concept', $concept)
            ->where('user_id', auth()->id())
            ->delete();
        $this->calendarService->removeByTitle($concept);
    }

    /**
     * Bulk delete outcomes by IDs, scoped to the authenticated user.
     */
    public function executeBulk(array $ids): float
    {
        $user = auth()->user();
        $outcomes = Outcome::where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->get();

        $totalRestored = 0;
        $concepts = [];

        foreach ($outcomes as $outcome) {
            $totalRestored += $outcome->amount;
            $concepts[] = $outcome->concept;
        }

        Outcome::where('user_id', $user->id)->whereIn('id', $ids)->delete();

        $this->totalMoneyService->increment($user, $totalRestored);

        foreach (array_unique($concepts) as $concept) {
            RecurringOutcome::where('concept', $concept)
                ->where('user_id', $user->id)
                ->delete();
            $this->calendarService->removeByTitle($concept);
        }

        return $totalRestored;
    }
}
