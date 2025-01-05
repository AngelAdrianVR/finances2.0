<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\RecurringOutcome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurringOutcomeController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return inertia('RecurringOutcome/Create');
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

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function show(RecurringOutcome $recurring_outcome)
    {
        //
    }

    public function edit(RecurringOutcome $recurring_outcome)
    {
        return inertia('RecurringOutcome/Edit', compact('recurring_outcome'));
    }

    public function update(Request $request, RecurringOutcome $recurring_outcome)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'concept' => 'required|string|max:50',
            'periodicity' => 'required|string',
            'description' => 'nullable',
        ]);

        //eliminar todos los registros del calendario con el nombre del gasto fijo editado.
        Calendar::where('title', $recurring_outcome->concept)->delete();
                
        $recurring_outcome->update($request->all());

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

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function destroy(RecurringOutcome $recurring_outcome)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->recurring_outcomes as $outcome) {
            $outcome = RecurringOutcome::find($outcome['id']);
            $outcome?->delete();  
            Calendar::where('title', $outcome->concept)->where('date', '>=', $outcome->created_at)->delete();           
        }

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $recurring_outcomes = RecurringOutcome::where('user_id', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                ->orWhere('concept', 'like', "%{$query}%")
                ->orWhere('amount', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->orWhere('created_at', 'like', "%{$query}%")
                ->orWhere('payment_method', 'like', "%{$query}%");
            })
            ->paginate(200);

        // Devuelve los items encontrados
        return response()->json(['items' => $recurring_outcomes], 200);
    }

    public function toggleStatus(RecurringOutcome $recurring_outcome)
    {
        if ( $recurring_outcome->is_active ) {
            $recurring_outcome->update([
                'is_active' => false,
            ]);
        } else {
            $recurring_outcome->update([
                'is_active' => true,
            ]);
        }

        return response()->json(['is_active' => $recurring_outcome->is_active]);
    }
}
