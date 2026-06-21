<?php

namespace App\Actions\Incomes;

use App\Models\Income;
use App\Models\RecurringIncome;
use App\Services\CalendarService;
use App\Services\TotalMoneyService;

class UpdateIncomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(Income $income, array $data): Income
    {
        $oldAmount = $income->amount;
        $wasRecurring = RecurringIncome::where('concept', $income->concept)
            ->where('user_id', auth()->id())
            ->exists();

        $isRecurring = ! empty($data['is_recurring_income']);

        // If recurring status changed or concept changed, clean up old recurring records
        if ($wasRecurring && (! $isRecurring || $data['concept'] !== $income->concept)) {
            RecurringIncome::where('concept', $income->concept)
                ->where('user_id', auth()->id())
                ->delete();
            $this->calendarService->removeByTitle($income->concept);
        }

        $income->update($data);

        // Adjust global balance
        $this->totalMoneyService->adjust(auth()->user(), $oldAmount, $income->amount);

        // Re-create recurring if needed
        if ($isRecurring) {
            // Clean old calendar events for this concept
            $this->calendarService->removeByTitle($income->concept);

            RecurringIncome::updateOrCreate(
                [
                    'concept' => $data['concept'],
                    'user_id' => auth()->id(),
                ],
                $data + ['user_id' => auth()->id()]
            );

            $this->calendarService->generateRecurringEvents([
                'type'           => 'Ingreso recurrente',
                'title'          => $data['concept'],
                'amount'         => $data['amount'],
                'category'       => $data['category'] ?? null,
                'description'    => $data['description'] ?? null,
                'periodicity'    => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id'        => auth()->id(),
                'created_at'     => $data['created_at'] ?? $income->created_at,
            ]);
        }

        return $income->fresh();
    }
}
