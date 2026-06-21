<?php

namespace App\Services;

use App\Models\User;

/**
 * Centralized service for all user.total_money mutations.
 * Every income, outcome, loan, and payment operation that affects
 * the global balance MUST go through this service.
 */
class TotalMoneyService
{
    /**
     * Add an amount to the user's total money.
     */
    public function increment(User $user, float $amount): void
    {
        $user->total_money = round($user->total_money + $amount, 2);
        $user->save();
    }

    /**
     * Subtract an amount from the user's total money.
     * Prevents the balance from going negative (floor at 0).
     */
    public function decrement(User $user, float $amount): void
    {
        $user->total_money = round(max(0, $user->total_money - $amount), 2);
        $user->save();
    }

    /**
     * Adjust the balance: reverses the old amount then applies the new one.
     * Useful when updating an income or outcome record.
     *
     * Example: old amount was 500, new amount is 300 → subtracts 200 from balance.
     */
    public function adjust(User $user, float $oldAmount, float $newAmount): void
    {
        $diff = round($newAmount - $oldAmount, 2);

        if ($diff > 0) {
            $this->increment($user, $diff);
        } elseif ($diff < 0) {
            $this->decrement($user, abs($diff));
        }
        // If diff is 0, no change needed.
    }

    /**
     * Recalculate the entire user balance from scratch based on
     * incomes, outcomes, and loan payments. Used as a safety net
     * after complex operations or for data integrity checks.
     */
    public function recalculate(User $user): float
    {
        $totalIncome = $user->incomes()->sum('amount');
        $totalOutcome = $user->outcomes()->sum('amount');
        // Loan adjustments would depend on business rules.

        $user->total_money = round($totalIncome - $totalOutcome, 2);
        $user->save();

        return $user->total_money;
    }
}
