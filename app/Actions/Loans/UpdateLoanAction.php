<?php

namespace App\Actions\Loans;

use App\Models\Loan;

class UpdateLoanAction
{
    public function execute(Loan $loan, array $data): Loan
    {
        $data['is_for_me'] = ($data['type'] ?? $loan->type) === 'Recibido';
        $loan->update($data);

        return $loan->fresh();
    }
}
