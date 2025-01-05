<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans_for_me = Loan::with(['payments'])->where('type', 'Recibido')
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(50);

        $loans_given = Loan::with(['payments'])->where('type', 'Otorgado')
            ->where('user_id', auth()->id())
            ->latest('id')
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

        $loan = Loan::create($request->all() + ['user_id' => auth()->id(), 'is_for_me' => $request->type === 'Recibido']);

        // si el prestamo tiene registro automatico de ingresos o egresos
        if ($loan->automatic) {
            //sumar o restar la cantidad del abono segun sea el tipo del préstamo (otorgado o recibido) al total global en la tabla users
            $user = auth()->user();
            if ($loan->type === 'Recibido') {
                $user->total_money += $loan->amount;
            } else {
                //si el monto del préstamo es mayor al dinero global registrado, se manda a cero para no tener numeros negativos.
                if ($user->total_money < $loan->amount) {
                    $user->total_money = 0;
                } else {
                    $user->total_money -= $loan->amount;
                }
            }
            $user->save();
        }

        return to_route('loans.show', $loan->id);
    }

    public function show(Loan $loan)
    {
        $loan->load(['payments']);
        $loans = Loan::latest()->where('user_id', auth()->id())->get(['id', 'type', 'beneficiary_name', 'lender_name', 'amount']);

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

        //Actualizar el prestamos
        $loan->update($request->all() + ['is_for_me' => $request->type === 'Recibido']);

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
        $loans = Loan::where('user_id', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                ->orWhere('beneficiary_name', 'like', "%{$query}%")
                ->orWhere('lender_name', 'like', "%{$query}%")
                ->orWhere('amount', 'like', "%{$query}%")
                ->orWhere('loan_date', 'like', "%{$query}%")
                ->orWhere('profitability_type', 'like', "%{$query}%");
            })
            ->paginate(200);

        // Devuelve los items encontrados
        return response()->json(['items' => $loans], 200);
    }
}
