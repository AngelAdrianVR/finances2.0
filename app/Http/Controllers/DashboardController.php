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

        // Use the provided period date, or default to today
        $date = $request->period ? Carbon::parse($request->period) : now();

        [$currentStart, $currentEnd, $prevStart, $prevEnd] = match ($periodicity) {
            'Por día' => [
                $date->copy()->startOfDay(), $date->copy()->endOfDay(),
                $date->copy()->subDay()->startOfDay(), $date->copy()->subDay()->endOfDay(),
            ],
            'Semanal' => [
                $date->copy()->startOfWeek(Carbon::SUNDAY)->addDay(),
                $date->copy()->endOfWeek(Carbon::SUNDAY)->addDay(),
                $date->copy()->subWeek()->startOfWeek(Carbon::SUNDAY)->addDay(),
                $date->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->addDay(),
            ],
            'Mensual' => [
                $date->copy()->startOfMonth(), $date->copy()->endOfMonth(),
                $date->copy()->subMonth()->startOfMonth(), $date->copy()->subMonth()->endOfMonth(),
            ],
            'Anual' => [
                $date->copy()->startOfYear(), $date->copy()->endOfYear(),
                $date->copy()->subYear()->startOfYear(), $date->copy()->subYear()->endOfYear(),
            ],
            default => [
                $date->copy()->startOfMonth(), $date->copy()->endOfMonth(),
                $date->copy()->subMonth()->startOfMonth(), $date->copy()->subMonth()->endOfMonth(),
            ],
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
    public function fetchYearlyAverages(Request $request)
    {
        $userId = auth()->id();
        $year = (int) ($request->input('year') ?: now()->year);

        $yearStart = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::createFromDate($year, 12, 31)->endOfDay();

        $isCurrentYear = $year === now()->year;

        // Ingresos y egresos del año
        $yearTotalIncome = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->sum('amount');

        $yearTotalOutcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->sum('amount');

        // Meses transcurridos: 12 para años pasados/futuros, mes actual para el año en curso
        $monthsElapsed = $isCurrentYear ? max(now()->month, 1) : 12;

        // Promedios mensuales
        $avgMonthlyIncome = round($yearTotalIncome / $monthsElapsed, 2);
        $avgMonthlyOutcome = round($yearTotalOutcome / $monthsElapsed, 2);

        // Balance del año (neto)
        $yearNet = $yearTotalIncome - $yearTotalOutcome;

        // "Mes actual": si es el año en curso, el mes actual; si no, el último mes con datos (diciembre)
        if ($isCurrentYear) {
            $currentMonthStart = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();
        } else {
            $currentMonthStart = Carbon::createFromDate($year, 12, 1)->startOfDay();
            $currentMonthEnd = Carbon::createFromDate($year, 12, 31)->endOfDay();
        }

        $currentMonthIncome = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $currentMonthOutcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
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
