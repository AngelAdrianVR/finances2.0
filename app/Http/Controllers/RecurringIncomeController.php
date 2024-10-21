<?php

namespace App\Http\Controllers;

use App\Models\RecurringIncome;
use Illuminate\Http\Request;

class RecurringIncomeController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(RecurringIncome $recurring_income)
    {
        return inertia('RecurringIncome/Show', compact('recurring_income'));
    }

    public function edit(RecurringIncome $recurring_income)
    {
        //
    }

    public function update(Request $request, RecurringIncome $recurring_income)
    {
        //
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
        }

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }
}
