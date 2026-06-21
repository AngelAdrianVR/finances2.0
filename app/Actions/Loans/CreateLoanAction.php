<?php

namespace App\Actions\Loans;

use App\Models\Loan;
use App\Services\TotalMoneyService;

class CreateLoanAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
    ) {}

    public function execute(array $data): Loan
    {
        $data['user_id'] = auth()->id();
        $data['is_for_me'] = ($data['type'] ?? '') === 'Recibido';

        $loan = Loan::create($data);

        // If automatic mode, adjust total_money immediately
        if (! empty($loan->automatic)) {
            if ($loan->type === 'Recibido') {
                $this->totalMoneyService->increment(auth()->user(), $loan->amount);
            } else {
                $this->totalMoneyService->decrement(auth()->user(), $loan->amount);
            }
        }

        return $loan;
    }
}
