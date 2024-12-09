<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans_for_me = Loan::with(['payments'])->where('type', 'Recibido')
            ->paginate(50);

        $loans_given = Loan::with(['payments'])->where('type', 'Otorgado')
            ->paginate(50);

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
            'profitability_type' => $request->no_interest ? 'nullable|string' : 'required|string',
            'expired_date' => 'nullable|date|after:today',
            'amount' => 'required|numeric|min:0|max:999999',
            'status' => 'required|string',
            'loan_date' => 'nullable|date',
        ]);

        $loan = Loan::create($request->all() + ['user_id' => auth()->id()]);

        return to_route('loans.show', $loan->id);
    }

    public function show(Loan $loan)
    {   
        $loan->load(['payments']);
        $loans = Loan::latest()->get(['id', 'type', 'beneficiary_name', 'lender_name', 'amount']);

        // return $loans;
        return inertia('Loan/Show', compact('loan', 'loans'));
    }

    public function edit(Loan $loan)
    {
        return inertia('Loan/Edit', compact('loan'));
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'type' => 'required|string',
            'beneficiary_name' => $request->type == 'Otorgado' ? 'required|string' : 'nullable|string',
            'lender_name' => $request->type == 'Recibido' ? 'required|string' : 'nullable|string',
            'payment_periodicity' =>  'nullable|string',
            'profitability' => $request->no_interest ? 'nullable|numeric|min:0|max:999999' : 'required|numeric|min:0|max:999999',
            'profitability_type' => $request->no_interest ? 'nullable|string' : 'required|string',
            'expired_date' => 'nullable|date|after:today',
            'amount' => 'required|numeric|min:0|max:999999',
            'status' => 'required|string',
            'loan_date' => 'nullable|date',
        ]);

        $loan->update($request->all());

        return to_route('loans.index');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();

        return to_route('loans.index');
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->loans as $loan) {
            $loan = Loan::find($loan['id']);
            $loan?->delete();            
        }

        // return response()->json(['message' => 'Producto(s) eliminado(s)']);
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $loans = Loan::where('id', 'like', "%{$query}%")
            ->orWhere('beneficiary_name', 'like', "%{$query}%")
            ->orWhere('lender_name', 'like', "%{$query}%")
            ->orWhere('amount', 'like', "%{$query}%")
            ->orWhere('loan_date', 'like', "%{$query}%")
            ->orWhere('profitability_type', 'like', "%{$query}%")
            ->paginate(200);

        // Devuelve los items encontrados
        return response()->json(['items' => $loans], 200);
    }
}
