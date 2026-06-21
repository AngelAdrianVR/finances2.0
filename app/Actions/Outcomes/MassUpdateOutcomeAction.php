<?php

namespace App\Actions\Outcomes;

use App\Models\Outcome;

class MassUpdateOutcomeAction
{
    public function execute(array $ids, array $updates): int
    {
        return Outcome::where('user_id', auth()->id())
            ->whereIn('id', $ids)
            ->update(array_intersect_key($updates, array_flip(['category', 'payment_method'])));
    }
}
