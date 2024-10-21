<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\RecurringIncome;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {   
        $incomes = Income::paginate(20);
        $recurring_incomes = RecurringIncome::paginate(20);

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
        //
    }

    public function update(Request $request, Income $income)
    {
        //
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
}
