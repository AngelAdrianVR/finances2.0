<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Outcome;
use App\Models\RecurringOutcome;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OutcomeController extends Controller
{
    public function index()
    {
        $outcomes = Outcome::paginate(50);
        $recurring_outcomes = RecurringOutcome::paginate(50);

        return inertia('Outcome/Index', compact('outcomes', 'recurring_outcomes'));
    }

    public function create()
    {
        return inertia('Outcome/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'created_at' => 'required',
            'periodicity' => $request->is_recurring_outcome ? 'required' : 'nullable',
            'description' => 'nullable',
        ]);

        $outcome = Outcome::create($request->all() + ['user_id' => auth()->id()]);

        //resta la cantidad del gasto a el total global
        $user = User::find(auth()->id());

        //si el dinero total es menor al gasto se queda en 0 para no tener números negativos
        if ( $user->total_money < $outcome->amount ) {
            $user->total_money = 0;
        } else {
            $user->total_money -= $outcome->amount;
        }
        $user->save();

        // Registrar en gasto fijo si se seleccionó el check.
        if ( $request->is_recurring_outcome ) {
            RecurringOutcome::create($request->all() + ['user_id' => auth()->id()]);

            //agregar a calendario el gasto fijo con la frecuencia indicada ---------------------
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
                    'type' => 'Gasto fijo',
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

        return to_route('outcomes.index');
    }

    public function show(Outcome $outcome)
    {
        //
    }

    public function edit(Outcome $outcome)
    {
        return inertia('Outcome/Edit', compact('outcome'));
    }

    public function update(Request $request, Outcome $outcome)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'created_at' => 'required',
            'periodicity' => $request->is_recurring_outcome ? 'required' : 'nullable',
            'description' => 'nullable',
        ]);

        if ( $request->is_recurring_outcome ) {
            //eliminar todos los registros del calendario con el nombre del gasto fijo editado.
            Calendar::where('title', $outcome->concept)->delete();

            //elimina el gasto fijo de la tabla de recurring outcomes
            RecurringOutcome::where('concept', $outcome->concept)->delete();
        }

        //suma la cantidad del gasto a el total global para restar la cantidad actualizada
        $user = User::find(auth()->id());
        $user->total_money += $outcome->amount;

        $outcome->update($request->all());

        //si el dinero total es menor al gasto se queda en 0 para no tener números negativos
        if ( $user->total_money < $outcome->amount ) {
            $user->total_money = 0;
        } else {
            $user->total_money -= $outcome->amount;
        }
        $user->save();

        // Registrar en gasto fijo si se seleccionó el check.
        if ( $request->is_recurring_outcome ) {

            //vuelve a crear el gasto fijo nuevo
            RecurringOutcome::create($request->all() + ['user_id' => auth()->id()]);

            //agregar a calendario el gasto fijo con la frecuencia indicada ---------------------
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
                    'type' => 'Gasto fijo',
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

        return to_route('outcomes.index');

    }

    public function destroy(Outcome $outcome)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->outcomes as $outcome) {
            $outcome = Outcome::find($outcome['id']);
            $outcome?->delete();            
        }

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $outcomes = Outcome::where('id', 'like', "%{$query}%")
            ->orWhere('concept', 'like', "%{$query}%")
            ->orWhere('amount', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('created_at', 'like', "%{$query}%")
            ->orWhere('payment_method', 'like', "%{$query}%")
            ->paginate(200);

        // Devuelve los items encontrados
        return response()->json(['items' => $outcomes], 200);
    }
}
