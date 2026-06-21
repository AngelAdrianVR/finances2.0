<?php

namespace App\Actions\Loans;

use App\Models\Loan;
use App\Models\Payment;

class DeleteLoanAction
{
    public function execute(Loan $loan): void
    {
        // Delete all payments first to maintain referential integrity
        Payment::where('loan_id', $loan->id)->delete();
        $loan->delete();
    }

    /**
     * Bulk delete loans by IDs, scoped to the authenticated user.
     */
    public function executeBulk(array $ids): int
    {
        $query = Loan::where('user_id', auth()->id())->whereIn('id', $ids);

        // Delete payments for all affected loans
        Payment::whereIn('loan_id', $query->pluck('id'))->delete();

        return $query->delete();
    }
}
