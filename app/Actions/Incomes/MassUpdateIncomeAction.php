<?php

namespace App\Actions\Incomes;

use App\Models\Income;

class MassUpdateIncomeAction
{
    /**
     * Bulk update category and/or payment_method for multiple incomes.
     */
    public function execute(array $ids, array $updates): int
    {
        return Income::where('user_id', auth()->id())
            ->whereIn('id', $ids)
            ->update(array_intersect_key($updates, array_flip(['category', 'payment_method'])));
    }
}
