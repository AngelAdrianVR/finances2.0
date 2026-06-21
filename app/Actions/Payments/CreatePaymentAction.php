<?php

namespace App\Actions\Payments;

use App\Actions\Loans\LoanInterestCalculator;
use App\Models\Income;
use App\Models\Loan;
use App\Models\Outcome;
use App\Models\Payment;
use App\Notifications\MovementNotification;
use App\Services\TotalMoneyService;

class CreatePaymentAction
{
    public function __construct(
        private readonly LoanInterestCalculator $interestCalculator,
        private readonly TotalMoneyService $totalMoneyService,
    ) {}

    public function execute(array $data): Payment
    {
        $loan = Loan::findOrFail($data['loan_id']);

        // Find the latest payment to determine remaining
        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();
        $remaining = $lastPayment ? $lastPayment->remaining : $loan->amount;

        $lastDate = $lastPayment ? $lastPayment->date : $loan->loan_date;

        // Calculate interest/capital breakdown
        $calculation = $this->interestCalculator->calculate(
            $loan, $remaining, $data['amount'], $lastDate, $data['date']
        );

        // Create payment
        $payment = Payment::create([
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'],
            'date'           => $data['date'],
            'notes'          => $data['notes'] ?? null,
            'loan_id'        => $data['loan_id'],
            'interest'       => $calculation['interest'],
            'capital'        => $calculation['capital'],
            'remaining'      => $calculation['newRemaining'],
        ]);

        // Handle automatic income/outcome registration
        if ($loan->automatic) {
            $this->handleAutomaticMode($loan, $payment, $calculation);
        }

        // Mark loan as paid if fully settled
        if ($calculation['newRemaining'] <= 0) {
            $loan->update(['status' => 'Pagado']);
        }

        return $payment;
    }

    private function handleAutomaticMode(Loan $loan, Payment $payment, array $calculation): void
    {
        $user = auth()->user();

        if ($loan->type === 'Otorgado') {
            // Loan given → receiving payment = income
            $this->totalMoneyService->increment($user, $payment->amount);

            if ($calculation['interest'] > 0) {
                Income::create([
                    'amount'         => $calculation['interest'],
                    'concept'        => "Intereses de préstamo {$loan->id}",
                    'category'       => 'Intereses',
                    'payment_method' => 'Transferencia',
                    'user_id'        => $user->id,
                ]);

                $user->notify(new MovementNotification(
                    "Se ha registrado un ingreso por intereses de préstamo {$loan->id}",
                    'income',
                    route('loans.show', $loan->id)
                ));
            }
        } else {
            // Loan received → making payment = expense
            $this->totalMoneyService->decrement($user, $payment->amount);

            if ($calculation['interest'] > 0) {
                Outcome::create([
                    'amount'         => $calculation['interest'],
                    'concept'        => "Intereses de préstamo {$loan->id}",
                    'category'       => 'Otros',
                    'payment_method' => 'Transferencia',
                    'user_id'        => $user->id,
                ]);

                $user->notify(new MovementNotification(
                    "Se ha registrado un gasto por intereses de préstamo {$loan->id}",
                    'outcome',
                    route('loans.show', $loan->id)
                ));
            }
        }
    }
}
