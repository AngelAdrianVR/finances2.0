<?php

namespace App\Services;

use App\Models\Outcome;
use App\Models\User;

/**
 * Handles shared (split) outcomes.
 *
 * A shared outcome is registered by its creator, and every linked user gets an
 * auto-generated outcome with their proportional share. Because those records
 * count as real expenses, each linked user's available balance (users.total_money)
 * must be decremented by that share, and refunded when the outcome is updated or
 * removed. Otherwise the theoretical expense totals diverge from the real balance.
 */
class SharedOutcomeService
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
    ) {}

    /**
     * Create the auto-generated outcome for every linked user and subtract
     * their proportional share from their available balance.
     *
     * @param  array<int, int>  $userIds
     */
    public function distribute(Outcome $outcome, array $userIds): void
    {
        foreach (array_unique($userIds) as $userId) {
            $linkedOutcome = Outcome::create([
                'amount' => $outcome->amount,
                'concept' => $outcome->concept,
                'category' => $outcome->category,
                'payment_method' => $outcome->payment_method,
                'description' => $outcome->description,
                'created_at' => $outcome->created_at,
                'automatically_created' => true,
                'split_enabled' => false,
                'split_with' => null,
                'is_credit' => $outcome->is_credit,
                'payment_due_date' => $outcome->payment_due_date,
                'parent_outcome_id' => $outcome->id,
                'user_id' => $userId,
            ]);

            $linkedUser = User::find($userId);

            if ($linkedUser) {
                $this->totalMoneyService->decrement($linkedUser, $linkedOutcome->amount);
            }
        }
    }

    /**
     * Refund the proportional share to every linked user and delete their
     * auto-generated outcomes. Runs before updating or deleting the creator's
     * outcome so no balance is lost or counted twice.
     */
    public function refund(Outcome $outcome): void
    {
        $linkedOutcomes = Outcome::where('parent_outcome_id', $outcome->id)->get();

        foreach ($linkedOutcomes as $linkedOutcome) {
            $linkedUser = User::find($linkedOutcome->user_id);

            if ($linkedUser) {
                $this->totalMoneyService->increment($linkedUser, $linkedOutcome->amount);
            }

            $linkedOutcome->delete();
        }
    }

    /**
     * Amount each participant pays for a given total.
     *
     * @param  array<int, int>  $userIds
     */
    public function proportionalAmount(float $amount, array $userIds): float
    {
        $totalPeople = count(array_unique($userIds)) + 1;

        return round($amount / $totalPeople, 2);
    }
}
