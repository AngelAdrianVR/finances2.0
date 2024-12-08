<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\Income;
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

        if ( !$request->period ) {
            $outcomesQuery->whereDate('created_at', now());
            $outcomes = $outcomesQuery->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);

            $incomesQuery->whereDate('created_at', now());
            $incomes = $incomesQuery->get(['id', 'amount', 'category', 'concept', 'created_at', 'user_id']);
            return response()->json([
                'outcomes' => $outcomes,
                'incomes' => $incomes,
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

        return response()->json([
            'outcomes' => $outcomes,
            'incomes' => $incomes,
        ]);
    }

}
