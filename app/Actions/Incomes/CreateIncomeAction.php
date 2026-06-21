<?php

namespace App\Actions\Incomes;

use App\Models\Income;
use App\Models\RecurringIncome;
use App\Services\CalendarService;
use App\Services\TotalMoneyService;

class CreateIncomeAction
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
        private readonly CalendarService $calendarService,
    ) {}

    public function execute(array $data): Income
    {
        $data['user_id'] = auth()->id();
        $isRecurring = ! empty($data['is_recurring_income']);

        $income = Income::create($data);

        // Update global balance
        $this->totalMoneyService->increment(auth()->user(), $income->amount);

        // Handle recurring income
        if ($isRecurring) {
            RecurringIncome::create($data);

            $this->calendarService->generateRecurringEvents([
                'type'           => 'Ingreso recurrente',
                'title'          => $data['concept'],
                'amount'         => $data['amount'],
                'category'       => $data['category'] ?? null,
                'description'    => $data['description'] ?? null,
                'periodicity'    => $data['periodicity'],
                'payment_method' => $data['payment_method'] ?? null,
                'user_id'        => $data['user_id'],
                'created_at'     => $data['created_at'],
            ]);
        }

        return $income;
    }
}
