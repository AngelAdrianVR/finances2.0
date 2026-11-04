<?php

namespace App\Services\Dashboard;

use App\Models\Income;
use App\Models\Loan;
use App\Models\Outcome;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Builds every dataset the dashboard needs in a single payload
 * (totals, comparison, trend series, categories and KPIs).
 */
class DashboardOverviewService
{
    private const MONTH_LABELS = [
        'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic',
    ];

    private const WEEKDAY_LABELS = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    public function build(User $user, ?string $periodicity, ?string $period): array
    {
        $periodicity = $periodicity ?: 'Por día';
        $date = $period ? Carbon::parse($period) : now();

        [$start, $end, $prevStart, $prevEnd, $bucket] = $this->resolveRange($periodicity, $date);

        $incomes = Income::where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

        $outcomes = Outcome::where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

        $incomeTotal = round((float) $incomes->sum('amount'), 2);
        $outcomeTotal = round((float) $outcomes->sum('amount'), 2);

        $prevIncomeTotal = round((float) Income::where('user_id', $user->id)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('amount'), 2);

        $prevOutcomeTotal = round((float) Outcome::where('user_id', $user->id)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('amount'), 2);

        $net = round($incomeTotal - $outcomeTotal, 2);
        $prevNet = round($prevIncomeTotal - $prevOutcomeTotal, 2);

        return [
            'range' => [
                'periodicity' => $periodicity,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'label' => $this->rangeLabel($periodicity, $start, $end),
                'bucket' => $bucket,
            ],
            'totals' => [
                'income' => $incomeTotal,
                'outcome' => $outcomeTotal,
                'net' => $net,
                'prev_income' => $prevIncomeTotal,
                'prev_outcome' => $prevOutcomeTotal,
                'prev_net' => $prevNet,
                'income_change' => $this->changePercentage($incomeTotal, $prevIncomeTotal),
                'outcome_change' => $this->changePercentage($outcomeTotal, $prevOutcomeTotal),
                'net_change' => $this->changePercentage($net, $prevNet),
                'income_count' => $incomes->count(),
                'outcome_count' => $outcomes->count(),
            ],
            'balance' => round((float) $user->total_money, 2),
            'kpis' => $this->buildKpis($incomeTotal, $outcomeTotal, $start, $end),
            'trend' => $this->buildTrend($incomes, $outcomes, $start, $end, $bucket),
            'categories' => [
                'outcome' => $this->buildCategories($outcomes),
                'income' => $this->buildCategories($incomes),
            ],
            'top_concepts' => [
                'outcome' => $this->buildTopConcepts($outcomes),
                'income' => $this->buildTopConcepts($incomes),
            ],
            'incomes' => $incomes,
            'outcomes' => $outcomes,
            'loans' => $this->activeLoans($user),
        ];
    }

    private function resolveRange(string $periodicity, Carbon $date): array
    {
        return match ($periodicity) {
            'Semanal' => [
                $date->copy()->startOfWeek(Carbon::SUNDAY)->addDay()->startOfDay(),
                $date->copy()->endOfWeek(Carbon::SUNDAY)->addDay()->endOfDay(),
                $date->copy()->subWeek()->startOfWeek(Carbon::SUNDAY)->addDay()->startOfDay(),
                $date->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->addDay()->endOfDay(),
                'day',
            ],
            'Mensual' => [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
                $date->copy()->subMonth()->startOfMonth(),
                $date->copy()->subMonth()->endOfMonth(),
                'day',
            ],
            'Anual' => [
                $date->copy()->startOfYear(),
                $date->copy()->endOfYear(),
                $date->copy()->subYear()->startOfYear(),
                $date->copy()->subYear()->endOfYear(),
                'month',
            ],
            default => [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
                $date->copy()->subDay()->startOfDay(),
                $date->copy()->subDay()->endOfDay(),
                'hour',
            ],
        };
    }

    private function rangeLabel(string $periodicity, Carbon $start, Carbon $end): string
    {
        return match ($periodicity) {
            'Semanal' => sprintf('%s – %s', $start->translatedFormat('d M'), $end->translatedFormat('d M Y')),
            'Mensual' => ucfirst($start->translatedFormat('F Y')),
            'Anual' => (string) $start->year,
            default => ucfirst($start->translatedFormat('l, d \d\e F')),
        };
    }

    private function changePercentage(float $current, float $previous): float
    {
        if ($previous === 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    private function buildKpis(float $incomeTotal, float $outcomeTotal, Carbon $start, Carbon $end): array
    {
        $net = $incomeTotal - $outcomeTotal;
        $totalDays = max(1, (int) round($start->diffInDays($end)) + 1);
        $elapsedDays = max(1, min($totalDays, (int) round($start->diffInDays(now()->startOfDay())) + 1));
        $dailyAverage = round($outcomeTotal / $elapsedDays, 2);
        $remainingDays = max(0, $totalDays - $elapsedDays);

        return [
            'net' => round($net, 2),
            'savings_rate' => $incomeTotal > 0 ? round(($net / $incomeTotal) * 100, 1) : 0.0,
            'daily_average_outcome' => $dailyAverage,
            'elapsed_days' => $elapsedDays,
            'total_days' => $totalDays,
            'remaining_days' => $remainingDays,
            'projected_outcome' => round($dailyAverage * $totalDays, 2),
            'projected_net' => round($incomeTotal - ($dailyAverage * $totalDays), 2),
            'outcome_per_income_ratio' => $incomeTotal > 0 ? round(($outcomeTotal / $incomeTotal) * 100, 1) : 0.0,
        ];
    }

    private function buildTrend($incomes, $outcomes, Carbon $start, Carbon $end, string $bucket): array
    {
        [$labels, $keys] = $this->buildBuckets($start, $end, $bucket);

        $incomeSeries = array_fill_keys($keys, 0.0);
        $outcomeSeries = array_fill_keys($keys, 0.0);

        foreach ($incomes as $income) {
            $key = $this->bucketKey(Carbon::parse($income->created_at), $bucket);
            if (array_key_exists($key, $incomeSeries)) {
                $incomeSeries[$key] += (float) $income->amount;
            }
        }

        foreach ($outcomes as $outcome) {
            $key = $this->bucketKey(Carbon::parse($outcome->created_at), $bucket);
            if (array_key_exists($key, $outcomeSeries)) {
                $outcomeSeries[$key] += (float) $outcome->amount;
            }
        }

        $incomeValues = array_map(fn ($value) => round($value, 2), array_values($incomeSeries));
        $outcomeValues = array_map(fn ($value) => round($value, 2), array_values($outcomeSeries));
        $netValues = array_map(fn ($i, $o) => round($i - $o, 2), $incomeValues, $outcomeValues);

        return [
            'labels' => $labels,
            'income' => $incomeValues,
            'outcome' => $outcomeValues,
            'net' => $netValues,
        ];
    }

    private function buildBuckets(Carbon $start, Carbon $end, string $bucket): array
    {
        if ($bucket === 'hour') {
            return [
                array_map(fn ($hour) => str_pad((string) $hour, 2, '0', STR_PAD_LEFT).'h', range(0, 23)),
                array_map(fn ($hour) => (string) $hour, range(0, 23)),
            ];
        }

        if ($bucket === 'month') {
            return [
                self::MONTH_LABELS,
                array_map(fn ($month) => (string) $month, range(1, 12)),
            ];
        }

        $labels = [];
        $keys = [];

        foreach (CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()) as $day) {
            $labels[] = $day->format('j').' '.self::WEEKDAY_LABELS[(int) $day->dayOfWeek];
            $keys[] = $day->toDateString();
        }

        return [$labels, $keys];
    }

    private function bucketKey(Carbon $date, string $bucket): string
    {
        return match ($bucket) {
            'hour' => (string) $date->hour,
            'month' => (string) $date->month,
            default => $date->toDateString(),
        };
    }

    private function buildCategories($movements): array
    {
        $totals = [];

        foreach ($movements as $movement) {
            $key = $movement->category ?: 'Otro';
            $totals[$key] = ($totals[$key] ?? 0) + (float) $movement->amount;
        }

        arsort($totals);

        $grandTotal = array_sum($totals);

        return collect($totals)
            ->map(fn ($total, $key) => [
                'key' => $key,
                'total' => round($total, 2),
                'percentage' => $grandTotal > 0 ? round(($total / $grandTotal) * 100, 1) : 0.0,
            ])
            ->values()
            ->all();
    }

    private function buildTopConcepts($movements): array
    {
        return $movements
            ->groupBy(fn ($movement) => $movement->concept ?: 'Sin concepto')
            ->map(fn ($group, $concept) => [
                'concept' => $concept,
                'total' => round((float) $group->sum('amount'), 2),
                'count' => $group->count(),
                'category' => $group->first()->category,
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values()
            ->all();
    }

    private function activeLoans(User $user)
    {
        return Loan::with('payments:amount,loan_id,interest,capital,remaining')
            ->where('status', 'En curso')
            ->where('user_id', $user->id)
            ->get(['id', 'amount', 'is_for_me', 'user_id', 'status']);
    }
}
