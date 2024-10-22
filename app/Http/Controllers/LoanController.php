<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans_for_me = Loan::paginate(50);
        $loans_given = Loan::paginate(50);

        return inertia('Loan/Index', compact('loans_for_me', 'loans_given'));
    }

    public function create()
    {
        return inertia('Loan/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'beneficiary_name' => $request->type == 'Otorgado' ? 'required|string' : 'nullable|string',
            'lender_name' => $request->type == 'Recibido' ? 'required|string' : 'nullable|string',
            'payment_periodicity' =>  'nullable|string',
            'profitability' => $request->no_interest ? 'nullable|numeric|min:0|max:999999' : 'required|numeric|min:0|max:999999',
            'profitability_type' => 'required|string',
            'expired_date' => 'nullable|date|after:today',
            'amount' => 'required|numeric|min:0|max:999999',
            'status' => 'required|string',
            'loan_date' => 'nullable|date',
        ]);

        Loan::create($request->all() + ['user_id' => auth()->id()]);

        return to_route('loans.index');
    }

    public function show(Loan $loan)
    {
        //
    }

    public function edit(Loan $loan)
    {
        //
    }

    public function update(Request $request, Loan $loan)
    {
        //
    }

    public function destroy(Loan $loan)
    {
        //
    }
}
