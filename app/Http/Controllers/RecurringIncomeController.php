<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\RecurringIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurringIncomeController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return inertia('RecurringIncome/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'periodicity' => 'required|string',
            'description' => 'nullable',
        ]);

        RecurringIncome::create($request->all() + ['user_id' => auth()->id()]);

        //agregar a calendario el ingreso recurrente con la frecuencia indicada ---------------------
        $startDate = Carbon::parse($request->created_at);
        $endDate = Carbon::now()->endOfYear(); // Limitar a este año
        $dates = [];

        switch ($request->periodicity) {
            case 'Todos los días':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addDay();
                }
                break;

            case 'Semanal':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addWeek();
                }
                break;

            case 'Mensual':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addMonth();
                }
                break;

                case 'Anual':
                    $endDate = Carbon::parse($request->created_at)->addYears(3); // 3 años posteriores
                    while ($startDate->lte($endDate)) {
                        $dates[] = $startDate->copy();
                        $startDate->addYear();
                    }
                    break;
        }

        foreach ($dates as $date) {
            Calendar::create([
                'type' => 'Ingreso recurrente',
                'title' => $request->concept,
                'date' => $date->toDateString(),
                'amount' => $request->amount,
                'category' => $request->category,
                'description' => $request->description,
                'periodicity' => $request->periodicity,
                'payment_method' => $request->payment_method,
                'user_id' => auth()->id(),
            ]);
        }

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function show(RecurringIncome $recurring_income)
    {
        //
    }

    public function edit(RecurringIncome $recurring_income)
    {
        return inertia('RecurringIncome/Edit', compact('recurring_income'));
    }

    public function update(Request $request, RecurringIncome $recurring_income)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'periodicity' => 'required|string',
            'description' => 'nullable',
        ]);

        //eliminar todos los registros del calendario con el nombre del ingreso recurrente editado.
        Calendar::where('title', $recurring_income->concept)->delete();

        $recurring_income->update($request->all());

        //agregar a calendario el ingreso recurrente con la frecuencia indicada ---------------------
        $startDate = Carbon::parse($request->created_at);
        $endDate = Carbon::now()->endOfYear(); // Limitar a este año
        $dates = [];

        switch ($request->periodicity) {
            case 'Todos los días':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addDay();
                }
                break;

            case 'Semanal':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addWeek();
                }
                break;

            case 'Mensual':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addMonth();
                }
                break;

                case 'Anual':
                    $endDate = Carbon::parse($request->created_at)->addYears(3); // 3 años posteriores
                    while ($startDate->lte($endDate)) {
                        $dates[] = $startDate->copy();
                        $startDate->addYear();
                    }
                    break;
        }

        foreach ($dates as $date) {
            Calendar::create([
                'type' => 'Ingreso recurrente',
                'title' => $request->concept,
                'date' => $date->toDateString(),
                'amount' => $request->amount,
                'category' => $request->category,
                'description' => $request->description,
                'periodicity' => $request->periodicity,
                'payment_method' => $request->payment_method,
                'user_id' => auth()->id(),
            ]);
        }

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function destroy(RecurringIncome $recurring_income)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->recurring_incomes as $income) {
            $income = RecurringIncome::find($income['id']);
            $income?->delete();
            Calendar::where('title', $income->concept)->where('date', '>=', $income->created_at)->delete();        
        }

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $recurring_incomes = RecurringIncome::where('id', 'like', "%{$query}%")
            ->orWhere('concept', 'like', "%{$query}%")
            ->orWhere('amount', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('created_at', 'like', "%{$query}%")
            ->orWhere('payment_method', 'like', "%{$query}%")
            ->paginate(200);

        // Devuelve los items encontrados
        return response()->json(['items' => $recurring_incomes], 200);
    }

    public function toggleStatus(RecurringIncome $recurring_income)
    {
        if ( $recurring_income->is_active ) {
            $recurring_income->update([
                'is_active' => false,
            ]);
        } else {
            $recurring_income->update([
                'is_active' => true,
            ]);
        }

        return response()->json(['is_active' => $recurring_income->is_active]);
    }
}
