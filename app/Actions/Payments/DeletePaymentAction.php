<?php

namespace App\Actions\Payments;

use App\Actions\Loans\LoanInterestCalculator;
use App\Models\Loan;
use App\Models\Payment;

class DeletePaymentAction
{
    public function __construct(
        private readonly LoanInterestCalculator $interestCalculator,
    ) {}

    public function execute(Payment $payment): void
    {
        $loan = $payment->loan;
        $deletedId = $payment->id;

        // Find previous payment
        $previousPayment = Payment::where('loan_id', $loan->id)
            ->where('id', '<', $deletedId)
            ->latest('id')
            ->first();

        $remaining = $previousPayment ? $previousPayment->remaining : $loan->amount;
        $lastDate = $previousPayment ? $previousPayment->date : $loan->loan_date;

        $payment->delete();

        // Recalculate chain
        $this->recalculateChainAfterDeletion($loan, $remaining, $lastDate, $deletedId);
    }

    private function recalculateChainAfterDeletion(Loan $loan, float $remaining, string $lastDate, int $deletedPaymentId): void
    {
        $subsequent = Payment::where('loan_id', $loan->id)
            ->where('id', '>', $deletedPaymentId)
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

        $lastPayment = Payment::where('loan_id', $loan->id)->latest('id')->first();
        $loan->update(['status' => ($lastPayment && $lastPayment->remaining > 0) ? 'En curso' : 'Pagado']);
    }
}
