<?php

namespace App\Http\Controllers;

use App\Models\RecurringOutcome;
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

        $recurring_outcome->update($request->all());

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
        }

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $recurring_outcomes = RecurringOutcome::where('id', 'like', "%{$query}%")
            ->orWhere('concept', 'like', "%{$query}%")
            ->orWhere('amount', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('created_at', 'like', "%{$query}%")
            ->orWhere('payment_method', 'like', "%{$query}%")
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
