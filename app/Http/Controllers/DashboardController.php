<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Income;
use App\Models\Loan;
use App\Models\Outcome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('Dashboard/Index');
    }

    public function fetchDataForPeriod(Request $request)
    {
        $userId = auth()->id();

        $outcomesQuery = Outcome::where('user_id', $userId);
        $incomesQuery = Income::where('user_id', $userId);

        if (! $request->period) {
            $outcomes = $outcomesQuery->whereDate('created_at', now())
                ->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

            $incomes = $incomesQuery->whereDate('created_at', now())
                ->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

            $loans = Loan::with('payments:amount,loan_id,interest,capital,remaining')
                ->where('status', 'En curso')
                ->where('user_id', $userId)
                ->get(['id', 'amount', 'is_for_me', 'user_id', 'status']);

            return response()->json(compact('outcomes', 'incomes', 'loans'));
        }

        $date = Carbon::parse($request->period);

        [$start, $end] = match ($request->periodicity) {
            'Por día'  => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
            'Semanal'  => [
                $date->copy()->startOfWeek(Carbon::SUNDAY)->addDay(),
                $date->copy()->endOfWeek(Carbon::SUNDAY)->addDay(),
            ],
            'Mensual'  => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            'Anual'    => [$date->copy()->startOfYear(), $date->copy()->endOfYear()],
            default    => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
        };

        $outcomes = $outcomesQuery->whereBetween('created_at', [$start, $end])
            ->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

        $incomes = $incomesQuery->whereBetween('created_at', [$start, $end])
            ->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

        $loans = Loan::with('payments:amount,loan_id,interest,capital,remaining')
            ->where('status', 'En curso')
            ->where('user_id', $userId)
            ->get(['id', 'amount', 'is_for_me', 'user_id', 'status']);

        return response()->json(compact('outcomes', 'incomes', 'loans'));
    }

    public function fetchDataComparison(Request $request)
    {
        $userId = auth()->id();
        $periodicity = $request->input('periodicity', 'Mensual');

        [$currentStart, $currentEnd, $prevStart, $prevEnd] = match ($periodicity) {
            'Por día' => [
                now()->startOfDay(), now()->endOfDay(),
                now()->subDay()->startOfDay(), now()->subDay()->endOfDay(),
            ],
            'Semanal' => [
                now()->startOfWeek(), now()->endOfWeek(),
                now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek(),
            ],
            'Mensual' => [
                now()->startOfMonth(), now()->endOfMonth(),
                now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(),
            ],
            'Anual' => [
                now()->startOfYear(), now()->endOfYear(),
                now()->subYear()->startOfYear(), now()->subYear()->endOfYear(),
            ],
            default => [now()->startOfMonth(), now()->endOfMonth(),
                        now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
        };

        $current_income = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('amount');

        $prev_income = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('amount');
        
        $current_outcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('amount');
        
        $prev_outcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('amount');

        return response()->json(compact('current_income', 'prev_income', 'current_outcome', 'prev_outcome'));
    }

    /**
     * Promedios mensuales del año en curso y otros indicadores clave.
     */
    public function fetchYearlyAverages()
    {
        $userId = auth()->id();
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        // Ingresos y egresos del año en curso
        $yearTotalIncome = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->sum('amount');

        $yearTotalOutcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->sum('amount');

        // Meses transcurridos en el año (mínimo 1 para evitar división por cero)
        $monthsElapsed = max(now()->month, 1);

        // Promedios mensuales
        $avgMonthlyIncome = round($yearTotalIncome / $monthsElapsed, 2);
        $avgMonthlyOutcome = round($yearTotalOutcome / $monthsElapsed, 2);

        // Balance del año (neto)
        $yearNet = $yearTotalIncome - $yearTotalOutcome;

        // Mes actual
        $currentMonthIncome = Income::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $currentMonthOutcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        // Proyección de fin de año (basado en el promedio mensual)
        $projectedYearEndIncome = $avgMonthlyIncome * 12;
        $projectedYearEndOutcome = $avgMonthlyOutcome * 12;

        return response()->json([
            'year_total_income'          => $yearTotalIncome,
            'year_total_outcome'         => $yearTotalOutcome,
            'year_net'                   => $yearNet,
            'avg_monthly_income'         => $avgMonthlyIncome,
            'avg_monthly_outcome'        => $avgMonthlyOutcome,
            'months_elapsed'             => $monthsElapsed,
            'current_month_income'       => $currentMonthIncome,
            'current_month_outcome'      => $currentMonthOutcome,
            'projected_year_end_income'  => $projectedYearEndIncome,
            'projected_year_end_outcome' => $projectedYearEndOutcome,
        ]);
    }

}
