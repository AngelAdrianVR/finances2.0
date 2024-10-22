<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use App\Models\RecurringOutcome;
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

        Outcome::create($request->all() + ['user_id' => auth()->id()]);

        //se crea un registro de gasto recurrente si se señala que lo es
        if ( $request->is_recurring_outcome ) {
            RecurringOutcome::create($request->all() + ['user_id' => auth()->id()]);
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

        $outcome->update($request->all());

        //se crea un registro de gasto recurrente si se señala que lo es
        if ( $request->is_recurring_outcome ) {
            RecurringOutcome::create($request->all() + ['user_id' => auth()->id()]);
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
