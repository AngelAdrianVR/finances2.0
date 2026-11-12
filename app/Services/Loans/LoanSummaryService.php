<?php

namespace App\Services\Loans;

use App\Models\Loan;
use Illuminate\Support\Collection;

class LoanSummaryService
{
    /**
     * Aggregate totals for the loan dashboard, split by loan type.
     *
     * @return array{given: array<string, int|float>, received: array<string, int|float>}
     */
    public function forUser(?int $userId = null): array
    {
        $loans = Loan::forUser($userId)
            ->withSum('payments as capital_paid', 'capital')
            ->get(['id', 'type', 'amount', 'status']);

        return [
            'given'    => $this->aggregate($loans->where('type', 'Otorgado')),
            'received' => $this->aggregate($loans->where('type', 'Recibido')),
        ];
    }

    /**
     * @param  Collection<int, Loan>  $loans
     * @return array<string, int|float>
     */
    private function aggregate(Collection $loans): array
    {
        $total = round((float) $loans->sum('amount'), 2);
        $paid = round((float) $loans->sum(fn (Loan $loan) => (float) $loan->capital_paid), 2);
        $paid = min($paid, $total);
        $remaining = round(max(0, $total - $paid), 2);

        return [
            'count'        => $loans->count(),
            'active_count' => $loans->where('status', 'En curso')->count(),
            'paid_count'   => $loans->where('status', 'Pagado')->count(),
            'total'        => $total,
            'paid'         => $paid,
            'remaining'    => $remaining,
            'progress'     => $total > 0 ? round(($paid / $total) * 100, 1) : 0.0,
        ];
    }
}
