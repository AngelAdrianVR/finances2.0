<?php

namespace App\Actions\Outcomes;

use App\Models\Outcome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use App\Services\TotalMoneyService;

class CreateOutcomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(array $data): Outcome
    {
        $data['user_id'] = auth()->id();
        $isRecurring = ! empty($data['is_recurring_outcome']);
        $isSplit = ! empty($data['split_enabled']) && ! empty($data['split_with']);

        if ($isSplit) {
            $data['split_with'] = array_map('intval', $data['split_with']);
            $data = $this->handleSplit($data);
        } else {
            $data['split_enabled'] = false;
            $data['split_with'] = null;
        }

        $outcome = Outcome::create($data);

        $this->totalMoneyService->decrement(auth()->user(), $outcome->amount);

        if ($isRecurring) {
            RecurringOutcome::create($data);

            $this->calendarService->generateRecurringEvents([
                'type'           => 'Gasto fijo',
                'title'          => $data['concept'],
                'amount'         => $data['amount'],
                'category'       => $data['category'] ?? null,
                'description'    => $data['description'] ?? null,
                'periodicity'    => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id'        => $data['user_id'],
                'created_at'     => $data['created_at'],
            ]);
        }

        return $outcome;
    }

    /**
     * Adjust the creator's amount to their proportional share,
     * and create outcome records for each linked user.
     */
    private function handleSplit(array $data): array
    {
        $splitUserIds = $data['split_with'];
        $totalPeople = count($splitUserIds) + 1;
        $proportionalAmount = round($data['amount'] / $totalPeople, 2);

        $data['amount'] = $proportionalAmount;

        $baseData = [
            'amount'                => $proportionalAmount,
            'concept'               => $data['concept'],
            'category'              => $data['category'] ?? null,
            'payment_method'        => $data['payment_method'] ?? null,
            'description'           => $data['description'] ?? null,
            'created_at'            => $data['created_at'] ?? now(),
            'automatically_created' => true,
            'split_enabled'         => false,
            'split_with'            => null,
        ];

        foreach ($splitUserIds as $userId) {
            Outcome::create($baseData + ['user_id' => $userId]);
        }

        return $data;
    }
}
