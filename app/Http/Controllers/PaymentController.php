<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Loan;
use App\Models\Outcome;
use App\Models\Payment;
use App\Notifications\MovementNotification;
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

        // Obtener el último pago del préstamo si existe
        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();

        // Calcular remaining
        $remaining = $lastPayment
            ? $lastPayment->remaining
            : $loan->amount;

        // Calcular intereses y nuevo restante
        $calculation = $this->calculateInterestAndCapital(
            $loan,
            $remaining,
            $request->amount,
            $lastPayment ? $lastPayment->date : $loan->loan_date,
            $request->date
        );

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . ($remaining + $calculation['interest']),
            'payment_method' => 'required|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'loan_id' => 'required|numeric|exists:loans,id',
        ]);

        // Crear el nuevo pago
        $payment = Payment::create([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'date' => $validated['date'],
            'notes' => $validated['notes'],
            'loan_id' => $validated['loan_id'],
            'interest' => $calculation['interest'],
            'capital' => $calculation['capital'],
            'remaining' => $calculation['newRemaining'],
        ]);


        // si el prestamo tiene registro automatico de ingresos o egresos
        if ($loan->automatic) {
            //sumar o restar la cantidad del abono segun sea el tipo del préstamo (otorgado o recibido) al total global en la tabla users
            $user = auth()->user();
            if ($loan->type === 'Otorgado') {
                $user->total_money += $payment->amount;

                if ($calculation['interest'] > 0) {
                    // registrar nuevo ingreso por intereses de prestamo
                    Income::create([
                        'amount' => $calculation['interest'],
                        'concept' => "Intereses de préstamo $loan->id",
                        'category' => 'Intereses',
                        'payment_method' => 'Transferencia',
                        'user_id' => auth()->id(),
                    ]);

                    // notificar al usuario
                    $user->notify(new MovementNotification(
                        "Se ha registrado un ingreso por intereses de préstamo $loan->id",
                        "income",
                        route('loans.show', $loan->id)
                    ));
                }
            } else {
                //si el monto del abono es mayor al dinero global registrado, se manda a cero para no tener numeros negativos.
                if ($user->total_money < $payment->amount) {
                    $user->total_money = 0;
                } else {
                    $user->total_money -= $payment->amount;
                }

                if ($calculation['interest'] > 0) {
                    // registrar nuevo gasto por abono de prestamo recibido
                    Outcome::create([
                        'amount' => $calculation['interest'],
                        'concept' => "Intereses de préstamo $loan->id",
                        'category' => 'Otros',
                        'payment_method' => 'Transferencia',
                        'user_id' => auth()->id(),
                    ]);

                    // notificar al usuario
                    $user->notify(new MovementNotification(
                        "Se ha registrado un gasto por intereses de préstamo $loan->id",
                        "outcome",
                        route('loans.show', $loan->id)
                    ));
                }
            }
            $user->save();
        }

        // si no queda restante por pagar, marcar prestamo como pagado
        if ($calculation['newRemaining'] == 0) {
            $loan->update(['status' => 'Pagado']);
        }
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
        // Validar los datos
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Obtener el préstamo asociado
        $loan = $payment->loan;

        // Obtener el último pago antes del actual (si existe)
        $previousPayment = Payment::where('loan_id', $loan->id)
            ->where('id', '<', $payment->id)
            ->latest('id')
            ->first();

        // Calcular el restante inicial
        $remaining = $previousPayment ? $previousPayment->remaining : $loan->amount;

        // Calcular intereses y nuevo restante
        $calculation = $this->calculateInterestAndCapital(
            $loan,
            $remaining,
            $validated['amount'],
            $previousPayment ? $previousPayment->date : $loan->loan_date,
            $validated['date']
        );

        // Actualizar el pago
        $payment->update([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'date' => $validated['date'],
            'notes' => $validated['notes'],
            'interest' => $calculation['interest'],
            'capital' => $calculation['capital'],
            'remaining' => $calculation['newRemaining'],
        ]);

        // Actualizar los pagos posteriores
        $this->updateSubsequentPayments($loan, $payment);
    }

    public function destroy(Payment $payment)
    {
        // Obtener el préstamo asociado
        $loan = $payment->loan;

        // Obtener el último pago antes del que se eliminará
        $previousPayment = Payment::where('loan_id', $loan->id)
            ->where('id', '<', $payment->id)
            ->latest('id')
            ->first();

        // Calcular el restante inicial después de eliminar
        $remaining = $previousPayment ? $previousPayment->remaining : $loan->amount;
        $lastDate = $previousPayment ? $previousPayment->date : $loan->loan_date;

        // Eliminar el pago
        $payment->delete();

        // Actualizar los pagos subsecuentes
        $this->updateSubsequentPaymentsAfterDeletion($loan, $remaining, $lastDate);
    }

    private function updateSubsequentPaymentsAfterDeletion(Loan $loan, $remaining, $lastDate)
    {
        $subsequentPayments = Payment::where('loan_id', $loan->id)
            ->orderBy('id')
            ->get();

        foreach ($subsequentPayments as $payment) {
            $calculation = $this->calculateInterestAndCapital(
                $loan,
                $remaining,
                $payment->amount,
                $lastDate,
                $payment->date
            );

            $payment->update([
                'interest' => $calculation['interest'],
                'capital' => $calculation['capital'],
                'remaining' => $calculation['newRemaining'],
            ]);

            $remaining = $calculation['newRemaining'];
            $lastDate = $payment->date;
        }

        // Revisar el restante del último pago
        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();
        if ($lastPayment && $lastPayment->remaining > 0) {
            $loan->update(['status' => 'En curso']);
        }
    }

    private function calculateInterestAndCapital(Loan $loan, $remaining, $amount, $lastDate, $currentDate)
    {
        $profitability = $loan->profitability;
        $profitabilityMode = $loan->profitability_mode;
        $profitabilityPeriod = $loan->profitability_period;

        // Calcular días transcurridos
        $daysElapsed = \Carbon\Carbon::parse($lastDate)->diffInDays($currentDate);

        // Determinar días del período de rentabilidad
        $daysInPeriod = match ($profitabilityPeriod) {
            'Todos los días' => 1,
            'Semanal' => 7,
            'Quincenal' => 15,
            'Mensual' => 30,
            'Anual' => 365,
            default => 1,
        };

        // Calcular interés diario
        $dailyProfitability = $profitabilityMode === 'Porcentaje'
            ? ($remaining * $profitability) / 100 / $daysInPeriod
            : $profitability / $daysInPeriod;

        // Calcular interés total
        $interest = round($dailyProfitability * $daysElapsed, 2);

        // Calcular pago a capital y nuevo restante
        $capital = round($amount - $interest, 2);
        $newRemaining = round($remaining - $capital, 2);

        return [
            'interest' => $interest,
            'capital' => $capital,
            'newRemaining' => $newRemaining,
        ];
    }

    private function updateSubsequentPayments(Loan $loan, Payment $updatedPayment)
    {
        $remaining = $updatedPayment->remaining;
        $lastDate = $updatedPayment->date;

        $subsequentPayments = Payment::where('loan_id', $loan->id)
            ->where('id', '>', $updatedPayment->id)
            ->orderBy('id')
            ->get();

        foreach ($subsequentPayments as $payment) {
            $calculation = $this->calculateInterestAndCapital(
                $loan,
                $remaining,
                $payment->amount,
                $lastDate,
                $payment->date
            );

            $payment->update([
                'interest' => $calculation['interest'],
                'capital' => $calculation['capital'],
                'remaining' => $calculation['newRemaining'],
            ]);

            $remaining = $calculation['newRemaining'];
            $lastDate = $payment->date;
        }

        // Revisar el restante del último pago
        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();
        if ($lastPayment && $lastPayment->remaining > 0) {
            $loan->update(['status' => 'En curso']);
        }
    }
}
