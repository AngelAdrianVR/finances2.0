<?php

namespace App\Http\Controllers;

use App\Actions\Payments\CreatePaymentAction;
use App\Actions\Payments\DeletePaymentAction;
use App\Actions\Payments\UpdatePaymentAction;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly CreatePaymentAction $createPaymentAction,
        private readonly UpdatePaymentAction $updatePaymentAction,
        private readonly DeletePaymentAction $deletePaymentAction,
    ) {}

    // ========================
    // CRUD
    // ========================

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $this->createPaymentAction->execute($request->validated());

        return redirect()->back()->with('success', 'Abono registrado correctamente.');
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:255'],
            'date'           => ['required', 'date'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $this->updatePaymentAction->execute($payment, $validated);

        return redirect()->back()->with('success', 'Abono actualizado correctamente.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $this->deletePaymentAction->execute($payment);

        return redirect()->back()->with('success', 'Abono eliminado correctamente.');
    }
}
