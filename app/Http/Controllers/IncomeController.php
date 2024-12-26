<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Income;
use App\Models\RecurringIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::where('user_id', auth()->id())->latest('id')->paginate(50);
        $recurring_incomes = RecurringIncome::where('user_id', auth()->id())->latest('id')->paginate(50);

        return inertia('Income/Index', compact('incomes', 'recurring_incomes'));
    }

    public function create()
    {
        return inertia('Income/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'created_at' => 'required',
            'periodicity' => $request->is_recurring_income ? 'required' : 'nullable',
            'description' => 'nullable',
        ]);

        $income = Income::create($request->all() + ['user_id' => auth()->id()]);

        //sumar la cantidad del ingreso al total global
        $user = auth()->user();
        $user->total_money += $income->amount;
        $user->save();

        // Registrar en ingresos recurrentes si se seleccionó el check.
        if ($request->is_recurring_income) {
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
        }

        return to_route('incomes.index');
    }

    public function show(Income $income)
    {
        //
    }

    public function edit(Income $income)
    {
        return inertia('Income/Edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'created_at' => 'required',
            'periodicity' => $request->is_recurring_income ? 'required' : 'nullable',
            'description' => 'nullable',
        ]);

        if ($request->is_recurring_income) {
            //eliminar todos los registros del calendario con el nombre del ingreso recurrente editado.
            Calendar::where('title', $income->concept)->delete();

            //elimina el ingreso recurrente de la tabla de recurring incomes
            RecurringIncome::where('concept', $income->concept)->delete();
        }

        //resta el ingreso al total global para sumar el actualizado
        $user = auth()->user();
        $user->total_money -= $income->amount;
        $user->save();

        //actualizar el ingreso
        $income->update($request->all());

        //sumar la cantidad del ingreso editado a el total global
        $user->total_money += $income->amount;
        $user->save();

        // Registrar en ingresos recurrentes si se seleccionó el check.
        if ($request->is_recurring_income) {
            //vuelve a crear el ingreso recurrente nuevo
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
        }

        return to_route('incomes.index');
    }

    public function destroy(Income $income)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->incomes as $income) {
            $income = Income::find($income['id']);
            $income?->delete();
        }
    }
    
    public function massiveUpdate(Request $request)
    {
        foreach ($request->selections as $income) {
            $income = Income::find($income['id']);
            $income?->update([
                'category' => $request->category,
                'payment_method' => $request->payment_method,
            ]);
        }
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $incomes = Income::where('id', 'like', "%{$query}%")
            ->orWhere('concept', 'like', "%{$query}%")
            ->orWhere('amount', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('created_at', 'like', "%{$query}%")
            ->orWhere('payment_method', 'like', "%{$query}%")
            ->paginate(200);

        // Devuelve los items encontrados
        return response()->json(['items' => $incomes], 200);
    }
}
