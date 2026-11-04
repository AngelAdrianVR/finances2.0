<?php

namespace App\Services\Incomes;

use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\User;
use Carbon\Carbon;

/**
 * Aggregated indicators shown on top of the incomes index.
 */
class IncomeSummaryService
{
    public function build(User $user): array
    {
        $baseQuery = Income::where('user_id', $user->id);

        $total = round((float) (clone $baseQuery)->sum('amount'), 2);
        $count = (clone $baseQuery)->count();

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $prevMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $prevMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $currentMonth = round((float) (clone $baseQuery)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount'), 2);

        $previousMonth = round((float) (clone $baseQuery)
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->sum('amount'), 2);

        $topCategory = (clone $baseQuery)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('category, SUM(amount) as aggregated')
            ->groupBy('category')
            ->orderByDesc('aggregated')
            ->first();

        $recurringQuery = RecurringIncome::where('user_id', $user->id)->where('is_active', true);

        return [
            'total' => $total,
            'count' => $count,
            'average' => $count > 0 ? round($total / $count, 2) : 0.0,
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'month_change' => $this->changePercentage($currentMonth, $previousMonth),
            'month_label' => ucfirst(Carbon::now()->translatedFormat('F Y')),
            'top_category' => $topCategory ? [
                'key' => $topCategory->category ?: 'Otro',
                'total' => round((float) $topCategory->aggregated, 2),
            ] : null,
            'recurring_total' => round((float) (clone $recurringQuery)->sum('amount'), 2),
            'recurring_count' => (clone $recurringQuery)->count(),
        ];
    }

    private function changePercentage(float $current, float $previous): float
    {
        if ($previous === 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }
}
