<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
        // Obtener el préstamo asociado
        $loan = Loan::findOrFail($request->loan_id);
        $profitability = $loan->profitability;
        $profitabilityMode = $loan->profitability_mode;
        $profitabilityPeriod = $loan->profitability_period;

        // Obtener el último pago del préstamo si existe
        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();

        // Calcular remaining
        $remaining = $lastPayment
            ? $lastPayment->remaining
            : $loan->amount;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $remaining,
            'payment_method' => 'required|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'loan_id' => 'required|numeric|exists:loans,id',
        ]);

        // convertir a dias el periodo de interés


        // Calcular interés según el tipo de interés
        $interest = 0;
        $interest = $profitabilityMode === 'Porcentaje'
            ? ($remaining * $profitability) / 100
            : $profitability; // Cantidad fija
        $interest = round($interest, 2);

        // Calcular pago a capital
        $capital = round($validated['amount'] - $interest, 2);

        // Calcular nuevo restante
        $newRemaining = round($remaining - $capital, 2);

        // Crear el nuevo pago
        $payment = Payment::create([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'date' => $validated['date'],
            'notes' => $validated['notes'],
            'loan_id' => $validated['loan_id'],
            'interest' => $interest,
            'capital' => $capital,
            'remaining' => $newRemaining,
        ]);
    }


    public function show(Payment $payment)
    {
        //
    }

    public function edit(Payment $payment)
    {
        //
    }

    public function update(Request $request, Payment $payment)
    {
        //
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
    }
}
