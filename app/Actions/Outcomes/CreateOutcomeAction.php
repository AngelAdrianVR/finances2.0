<?php

namespace App\Actions\Outcomes;

use App\Models\Outcome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use App\Services\SharedOutcomeService;
use App\Services\TotalMoneyService;

class CreateOutcomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
        private readonly SharedOutcomeService $sharedOutcomeService,
    ) {}

    public function execute(array $data): Outcome
    {
        $data['user_id'] = auth()->id();
        $isRecurring = ! empty($data['is_recurring_outcome']);
        $splitUserIds = $this->resolveSplitUserIds($data);
        $isSplit = $splitUserIds !== [];

        // Crédito: si no es a crédito, nunca debe guardarse una fecha de pago.
        $isCredit = ! empty($data['is_credit']);
        $data['is_credit'] = $isCredit;
        if (! $isCredit) {
            $data['payment_due_date'] = null;
        }

        if ($isSplit) {
            // El creador solo guarda su parte proporcional del gasto compartido.
            $data['split_with'] = $splitUserIds;
            $data['amount'] = $this->sharedOutcomeService->proportionalAmount((float) $data['amount'], $splitUserIds);
        } else {
            $data['split_enabled'] = false;
            $data['split_with'] = null;
        }

        $outcome = Outcome::create($data);

        $this->totalMoneyService->decrement(auth()->user(), $outcome->amount);

        // Cada usuario vinculado registra su parte y se le descuenta de su saldo disponible.
        if ($isSplit) {
            $this->sharedOutcomeService->distribute($outcome, $splitUserIds);
        }

        if ($isRecurring) {
            RecurringOutcome::create($data);

            $this->calendarService->generateRecurringEvents([
                'type' => 'Gasto fijo',
                'title' => $data['concept'],
                'amount' => $data['amount'],
                'category' => $data['category'] ?? null,
                'description' => $data['description'] ?? null,
                'periodicity' => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id' => $data['user_id'],
                'created_at' => $data['created_at'],
            ], true, true); // el primer movimiento ya se registró arriba; solo se agendan las siguientes ocurrencias
        }

        return $outcome;
    }

    /**
     * Linked user ids of a shared expense, normalized and without duplicates.
     *
     * @return array<int, int>
     */
    private function resolveSplitUserIds(array $data): array
    {
        if (empty($data['split_enabled']) || empty($data['split_with'])) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $data['split_with'])));
    }
}
