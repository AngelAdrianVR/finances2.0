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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Dashboard $dashboard)
    {
        //
    }

    public function edit(Dashboard $dashboard)
    {
        //
    }

    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    public function destroy(Dashboard $dashboard)
    {
        //
    }

    public function fetchDataForPeriod(Request $request)
    {
        // Inicializar las consultas base
        $outcomesQuery = Outcome::where('user_id', auth()->id());
        $incomesQuery = Income::where('user_id', auth()->id());

        if (!$request->period) {
            $outcomesQuery->whereDate('created_at', now());
            $outcomes = $outcomesQuery->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

            $incomesQuery->whereDate('created_at', now());
            $incomes = $incomesQuery->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

            // Recuperar informacion de préstamos
            $loans = Loan::with('payments:amount,loan_id,interest,capital,remaining')
                ->where([
                    'status' => 'En curso',
                    'user_id' => auth()->id()
                ])
                ->get(['id', 'amount', 'is_for_me', 'user_id', 'status']);

            return response()->json([
                'outcomes' => $outcomes,
                'incomes' => $incomes,
                'loans' => $loans,
            ]);
        }

        // Determinar el rango de fechas según la periodicidad seleccionada
        if ($request->periodicity === 'Por día') {
            // Rango del día actual
            $outcomesQuery->where('created_at', $request->period);
            $incomesQuery->where('created_at', $request->period);
        } elseif ($request->periodicity === 'Semanal') {
            // Obtener el inicio y fin de la semana seleccionada
            $startOfWeek = Carbon::parse($request->period)->startOfWeek();
            $endOfWeek = Carbon::parse($request->period)->endOfWeek();

            // arreglar el offset que tiene el metodo
            $startOfWeek->addDays(6);
            $endOfWeek->addDays(6);

            $outcomesQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            $incomesQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        } elseif ($request->periodicity === 'Mensual') {
            // Obtener el inicio y fin del mes seleccionado
            $startOfMonth = Carbon::parse($request->period)->startOfMonth();
            $endOfMonth = Carbon::parse($request->period)->endOfMonth();

            $outcomesQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            $incomesQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        } elseif ($request->periodicity === 'Anual') {
            // Obtener el inicio y fin del año seleccionado
            $startOfYear = Carbon::parse($request->period)->startOfYear();
            $endOfYear = Carbon::parse($request->period)->endOfYear();

            $outcomesQuery->whereBetween('created_at', [$startOfYear, $endOfYear]);
            $incomesQuery->whereBetween('created_at', [$startOfYear, $endOfYear]);
        }

        // Recuperar los resultados
        $outcomes = $outcomesQuery->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);
        $incomes = $incomesQuery->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

        // Recuperar informacion de préstamos
        $loans = Loan::with('payments:amount,loan_id,interest,capital,remaining')
            ->where([
                'status' => 'En curso',
                'user_id' => auth()->id()
            ])
            ->get(['id', 'amount', 'is_for_me', 'user_id', 'status']);

        return response()->json([
            'outcomes' => $outcomes,
            'incomes' => $incomes,
            'loans' => $loans,
        ]);
    }

    // Obtener los datos de comparación de ingresos y egresos mensuales actuales y anteriores del usuario autenticado
    // public function fetchDataComparison()
    // {
    //     $current_month_income = Income::where('user_id', auth()->id())
    //         ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
    //         ->sum('amount');

    //     $prev_month_income = Income::where('user_id', auth()->id())
    //         ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
    //         ->sum('amount');
        
    //     $current_month_outcome = Outcome::where('user_id', auth()->id())
    //         ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
    //         ->sum('amount');
        
    //     $prev_month_outcome = Outcome::where('user_id', auth()->id())
    //         ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
    //         ->sum('amount');

    //     return response()->json(compact('current_month_income', 'prev_month_income', 'current_month_outcome', 'prev_month_outcome'));
        
    // }

    public function fetchDataComparison(Request $request) 
    {
        $userId = auth()->id();
        $periodicity = $request->input('periodicity', 'Mensual');
        // return $periodicity;
        switch ($periodicity) {
            case 'Por día':
                $currentStart = now()->startOfDay();
                $currentEnd = now()->endOfDay();
                $prevStart = now()->subDay()->startOfDay();
                $prevEnd = now()->subDay()->endOfDay();
                break;
            
            case 'Semanal':
                $currentStart = now()->startOfWeek();
                $currentEnd = now()->endOfWeek();
                $prevStart = now()->subWeek()->startOfWeek();
                $prevEnd = now()->subWeek()->endOfWeek();
                break;
            
            case 'Mensual':
                $currentStart = now()->startOfMonth();
                $currentEnd = now()->endOfMonth();
                $prevStart = now()->subMonth()->startOfMonth();
                $prevEnd = now()->subMonth()->endOfMonth();
                break;
            
            case 'Anual':
                $currentStart = now()->startOfYear();
                $currentEnd = now()->endOfYear();
                $prevStart = now()->subYear()->startOfYear();
                $prevEnd = now()->subYear()->endOfYear();
                break;
            
            default:
                return response()->json(['error' => 'Periodicidad no válida'], 400);
        }

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

}
