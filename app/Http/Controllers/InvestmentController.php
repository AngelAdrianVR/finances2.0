<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Investment;
use App\Models\Outcome;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index(): JsonResponse
    {
        $investments = Investment::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'amount', 'annual_rate']);

        return response()->json(['investments' => $investments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'annual_rate' => 'required|numeric|min:0',
        ]);

        $investment = Investment::create([
            'user_id'     => auth()->id(),
            'name'        => $validated['name'] ?? null,
            'amount'      => $validated['amount'],
            'annual_rate' => $validated['annual_rate'],
        ]);

        return response()->json([
            'message'     => 'Inversión registrada correctamente.',
            'investment' => $investment->only(['id', 'name', 'amount', 'annual_rate']),
        ], 201);
    }

    public function update(Request $request, Investment $investment): JsonResponse
    {
        $this->authorizeOwnership($investment);

        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'annual_rate' => 'required|numeric|min:0',
        ]);

        $investment->update([
            'name'        => $validated['name'] ?? null,
            'amount'      => $validated['amount'],
            'annual_rate' => $validated['annual_rate'],
        ]);

        return response()->json([
            'message'     => 'Inversión actualizada correctamente.',
            'investment' => $investment->fresh()->only(['id', 'name', 'amount', 'annual_rate']),
        ]);
    }

    public function destroy(Investment $investment): JsonResponse
    {
        $this->authorizeOwnership($investment);

        $investment->delete();

        return response()->json(['message' => 'Inversión eliminada correctamente.']);
    }

    public function projection(): JsonResponse
    {
        $userId = auth()->id();

        $investments = Investment::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'amount', 'annual_rate']);

        $totalInvested = (float) $investments->sum('amount');

        // Tasa anual ponderada por monto
        $weightedRate = $totalInvested > 0
            ? (float) $investments->sum(fn ($inv) => $inv->amount * $inv->annual_rate) / $totalInvested
            : 0.0;

        // Promedios mensuales del año en curso (misma lógica que DashboardController)
        $year = now()->year;
        $yearStart = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::createFromDate($year, 12, 31)->endOfDay();
        $monthsElapsed = max(now()->month, 1);

        $yearTotalIncome = Income::where('user_id', $userId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->sum('amount');

        $yearTotalOutcome = Outcome::where('user_id', $userId)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->sum('amount');

        $avgMonthlyIncome = $yearTotalIncome / $monthsElapsed;
        $avgMonthlyOutcome = $yearTotalOutcome / $monthsElapsed;
        $monthlyContribution = max($avgMonthlyIncome - $avgMonthlyOutcome, 0);
        $monthlyExpenses = $avgMonthlyOutcome;

        // Simulación mes a mes (interés compuesto mensual)
        $monthlyRate = $weightedRate / 100 / 12;
        $projection = [];
        $breakEven = null;

        $capital = $totalInvested;
        $month = now()->startOfMonth();

        for ($yearIndex = 1; $yearIndex <= 40; $yearIndex++) {
            $yearStartCapital = $capital;
            $yearReturns = 0.0;

            for ($m = 0; $m < 12; $m++) {
                $monthlyReturn = $capital * $monthlyRate;
                $yearReturns += $monthlyReturn;
                $capital += $monthlyReturn + $monthlyContribution;

                // Punto de equilibrio: rendimiento mensual >= gastos mensuales
                if (
                    $breakEven === null
                    && $monthlyExpenses > 0
                    && $monthlyRate > 0
                    && $capital * $monthlyRate >= $monthlyExpenses
                ) {
                    $breakEven = [
                        'year'             => $month->year,
                        'month'            => $month->month,
                        'capital'          => round($capital, 2),
                        'monthly_income'   => round($capital * $monthlyRate, 2),
                        'monthly_expenses' => round($monthlyExpenses, 2),
                    ];
                }

                $month = $month->addMonth();
            }

            $projection[] = [
                'year'               => now()->year + $yearIndex,
                'capital'            => round($capital, 2),
                'yearly_return'      => round($yearReturns, 2),
                'monthly_return'     => round($yearReturns / 12, 2),
                'year_start_capital' => round($yearStartCapital, 2),
            ];

            if ($breakEven !== null) {
                break;
            }
        }

        return response()->json([
            'investments'          => $investments,
            'total_invested'       => round($totalInvested, 2),
            'weighted_rate'        => round($weightedRate, 2),
            'monthly_contribution' => round($monthlyContribution, 2),
            'monthly_expenses'     => round($monthlyExpenses, 2),
            'yearly_projection'    => $projection,
            'break_even'           => $breakEven,
        ]);
    }

    private function authorizeOwnership(Investment $investment): void
    {
        abort_unless($investment->user_id === auth()->id(), 403);
    }
}