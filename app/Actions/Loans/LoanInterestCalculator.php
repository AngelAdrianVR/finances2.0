<?php

namespace App\Actions\Loans;

use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Pure interest calculation engine for loans.
 * Extracted from PaymentController private methods for testability and reuse.
 */
class LoanInterestCalculator
{
    /**
     * Calculate interest and capital breakdown for a payment.
     *
     * @return array{interest: float, capital: float, newRemaining: float}
     */
    public function calculate(Loan $loan, float $remaining, float $amount, string|Carbon $lastDate, string|Carbon $currentDate): array
    {
        $profitability = (float) $loan->profitability;
        $mode = $loan->profitability_mode;
        $period = $loan->profitability_period;

        $daysElapsed = Carbon::parse($lastDate)->diffInDays(Carbon::parse($currentDate));

        $daysInPeriod = match ($period) {
            'Todos los días' => 1,
            'Semanal'        => 7,
            'Quincenal'      => 15,
            'Mensual'        => 30,
            'Anual'          => 365,
            default          => 1,
        };

        // Daily rate
        $dailyProfitability = $mode === 'Porcentaje'
            ? ($remaining * $profitability) / 100 / $daysInPeriod
            : $profitability / $daysInPeriod;

        $interest = round($dailyProfitability * $daysElapsed, 2);
        $capital = round($amount - $interest, 2);
        $newRemaining = round($remaining - $capital, 2);

        return [
            'interest'     => $interest,
            'capital'      => $capital,
            'newRemaining' => $newRemaining,
        ];
    }
}
