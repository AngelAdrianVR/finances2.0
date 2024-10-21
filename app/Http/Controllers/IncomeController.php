<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {   
        $incomes = Income::all();

        return inertia('Income/Index', compact('incomes'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
}
