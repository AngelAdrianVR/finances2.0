<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {   
        $incomes = Income::paginate(50);
        $recurring_incomes = RecurringIncome::paginate(50);

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

        Income::create($request->all() + ['user_id' => auth()->id()]);

        if ( $request->is_recurring_income ) {
            RecurringIncome::create($request->all() + ['user_id' => auth()->id()]);
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

        $income->update($request->all());

        if ( $request->is_recurring_income ) {
            RecurringIncome::create($request->all() + ['user_id' => auth()->id()]);
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

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
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
