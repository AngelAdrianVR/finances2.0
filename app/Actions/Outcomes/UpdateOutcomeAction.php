<?php

namespace App\Actions\Outcomes;

use App\Models\Outcome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use App\Services\TotalMoneyService;

class UpdateOutcomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(Outcome $outcome, array $data): Outcome
    {
        $oldAmount = $outcome->amount;
        $wasRecurring = RecurringOutcome::where('concept', $outcome->concept)
            ->where('user_id', auth()->id())
            ->exists();

        $isRecurring = ! empty($data['is_recurring_outcome']);
        $isSplit = ! empty($data['split_enabled']) && ! empty($data['split_with']);

        // Clean up old split outcomes if they existed
        if ($outcome->split_enabled) {
            $this->cleanupOldSplitOutcomes($outcome);
        }

        if ($isSplit) {
            $data['split_with'] = array_map('intval', $data['split_with']);
            $data = $this->handleSplit($data, $outcome);
        } else {
            $data['split_enabled'] = false;
            $data['split_with'] = null;
        }

        if ($wasRecurring && (! $isRecurring || $data['concept'] !== $outcome->concept)) {
            RecurringOutcome::where('concept', $outcome->concept)
                ->where('user_id', auth()->id())
                ->delete();
            $this->calendarService->removeByTitle($outcome->concept);
        }

        $outcome->update($data);

        $this->totalMoneyService->adjust(auth()->user(), $oldAmount, $outcome->amount);

        if ($isRecurring) {
            $this->calendarService->removeByTitle($outcome->concept);

            RecurringOutcome::updateOrCreate(
                ['concept' => $data['concept'], 'user_id' => auth()->id()],
                $data + ['user_id' => auth()->id()]
            );

            $this->calendarService->generateRecurringEvents([
                'type'           => 'Gasto fijo',
                'title'          => $data['concept'],
                'amount'         => $data['amount'],
                'category'       => $data['category'] ?? null,
                'description'    => $data['description'] ?? null,
                'periodicity'    => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id'        => auth()->id(),
                'created_at'     => $data['created_at'] ?? $outcome->created_at,
            ]);
        }

        return $outcome->fresh();
    }

    private function handleSplit(array $data, Outcome $outcome): array
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
            'created_at'            => $data['created_at'] ?? $outcome->created_at,
            'automatically_created' => true,
            'split_enabled'         => false,
            'split_with'            => null,
        ];

        foreach ($splitUserIds as $userId) {
            Outcome::create($baseData + ['user_id' => $userId]);
        }

        return $data;
    }

    private function cleanupOldSplitOutcomes(Outcome $outcome): void
    {
        $oldLinkedIds = $outcome->split_with ?? [];
        if (empty($oldLinkedIds)) {
            return;
        }

        Outcome::where('user_id', '!=', auth()->id())
            ->where('automatically_created', true)
            ->where('concept', $outcome->concept)
            ->whereDate('created_at', $outcome->created_at->toDateString())
            ->whereIn('user_id', $oldLinkedIds)
            ->delete();
    }
}
