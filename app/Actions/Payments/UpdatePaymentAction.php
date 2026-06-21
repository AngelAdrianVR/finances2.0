<?php

namespace App\Actions\Payments;

use App\Actions\Loans\LoanInterestCalculator;
use App\Models\Loan;
use App\Models\Payment;

class UpdatePaymentAction
{
    public function __construct(
        private readonly LoanInterestCalculator $interestCalculator,
    ) {}

    public function execute(Payment $payment, array $data): Payment
    {
        $loan = $payment->loan;

        // Find the payment before this one
        $previousPayment = Payment::where('loan_id', $loan->id)
            ->where('id', '<', $payment->id)
            ->latest('id')
            ->first();

        $remaining = $previousPayment ? $previousPayment->remaining : $loan->amount;
        $lastDate = $previousPayment ? $previousPayment->date : $loan->loan_date;

        $calculation = $this->interestCalculator->calculate(
            $loan, $remaining, $data['amount'], $lastDate, $data['date']
        );

        $payment->update([
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'],
            'date'           => $data['date'],
            'notes'          => $data['notes'] ?? null,
            'interest'       => $calculation['interest'],
            'capital'        => $calculation['capital'],
            'remaining'      => $calculation['newRemaining'],
        ]);

        // Recalculate subsequent payments
        $this->recalculateChain($loan, $payment);

        return $payment->fresh();
    }

    private function recalculateChain(Loan $loan, Payment $updatedPayment): void
    {
        $remaining = $updatedPayment->remaining;
        $lastDate = $updatedPayment->date;

        $subsequent = Payment::where('loan_id', $loan->id)
            ->where('id', '>', $updatedPayment->id)
            ->orderBy('id')
            ->get();

        foreach ($subsequent as $payment) {
            $calc = $this->interestCalculator->calculate(
                $loan, $remaining, $payment->amount, $lastDate, $payment->date
            );

            $payment->update([
                'interest'  => $calc['interest'],
                'capital'   => $calc['capital'],
                'remaining' => $calc['newRemaining'],
            ]);

            $remaining = $calc['newRemaining'];
            $lastDate = $payment->date;
        }

        // Check final state
        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();
        $loan->update(['status' => ($lastPayment && $lastPayment->remaining > 0) ? 'En curso' : 'Pagado']);
    }
}
