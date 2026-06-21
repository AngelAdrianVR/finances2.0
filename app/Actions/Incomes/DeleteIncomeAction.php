<?php

namespace App\Actions\Incomes;

use App\Models\Income;
use App\Models\RecurringIncome;
use App\Services\CalendarService;
use App\Services\TotalMoneyService;

class DeleteIncomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    /**
     * Delete a single income, adjusting balance and cleaning up recurring records.
     */
    public function execute(Income $income): void
    {
        $amount = $income->amount;
        $concept = $income->concept;

        $income->delete();

        // Subtract amount from global balance
        $this->totalMoneyService->decrement(auth()->user(), $amount);

        // Clean up recurring records and calendar events
        RecurringIncome::where('concept', $concept)
            ->where('user_id', auth()->id())
            ->delete();
        $this->calendarService->removeByTitle($concept);
    }

    /**
     * Bulk delete incomes by IDs, scoped to the authenticated user.
     * Returns the total amount subtracted.
     */
    public function executeBulk(array $ids): float
    {
        $user = auth()->user();
        $incomes = Income::where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->get();

        $totalDeducted = 0;
        $concepts = [];

        foreach ($incomes as $income) {
            $totalDeducted += $income->amount;
            $concepts[] = $income->concept;
        }

        // Delete all in one query
        Income::where('user_id', $user->id)->whereIn('id', $ids)->delete();

        // Adjust balance
        $this->totalMoneyService->decrement($user, $totalDeducted);

        // Clean up recurring records
        foreach (array_unique($concepts) as $concept) {
            RecurringIncome::where('concept', $concept)
                ->where('user_id', $user->id)
                ->delete();
            $this->calendarService->removeByTitle($concept);
        }

        return $totalDeducted;
    }
}
