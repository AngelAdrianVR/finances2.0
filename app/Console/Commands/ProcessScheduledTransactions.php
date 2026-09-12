<?php

namespace App\Console\Commands;

use App\Models\Calendar;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\User;
use App\Notifications\MovementNotification;
use App\Services\TotalMoneyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessScheduledTransactions extends Command
{
    protected $signature = 'calendar:process-scheduled';
    protected $description = 'Procesa ingresos o gastos programados para el día actual';

    public function handle(TotalMoneyService $totalMoneyService): int
    {
        $today = Carbon::today();

        // Recupera cualquier ocurrencia pendiente hasta hoy. Esto cubre los dias en que el scheduler
        // no pudo ejecutarse y los eventos creados despues de la hora programada.
        $transactions = Calendar::whereDate('date', '<=', $today)
            ->where('status', 'Pendiente')
            ->orderBy('date')
            ->get();

        foreach ($transactions as $transaction) {
            $user = User::find($transaction->user_id);

            if (! $user) {
                continue;
            }

            if ($transaction->type === 'Gasto fijo') {
                // El gasto se registra con la fecha en que estaba programado.
                $outcome = Outcome::create([
                    'concept' => $transaction->title,
                    'amount' => $transaction->amount,
                    'payment_method' => $transaction->payment_method,
                    'category' => $transaction->category,
                    'automatically_created' => true, //se creo automaticamente por el recordatorio
                    'description' => $transaction->description,
                    'created_at' => $transaction->date,
                    'user_id' => $transaction->user_id,
                ]);

                // Actualiza el dinero total global guardado en la tabla de usuarios
                $totalMoneyService->decrement($user, $transaction->amount);

                // notificar al usuario
                $user->notify(new MovementNotification(
                    "Se ha registrado un egreso programado en calendario $outcome->id",
                    "outcome",
                    route('outcomes.index')
                ));

            } elseif ($transaction->type === 'Ingreso recurrente') {
                // El ingreso se registra con la fecha en que estaba programado.
                $income = Income::create([
                    'concept' => $transaction->title,
                    'amount' => $transaction->amount,
                    'payment_method' => $transaction->payment_method,
                    'category' => $transaction->category,
                    'automatically_created' => true, //se creo automaticamente por el recordatorio
                    'description' => $transaction->description,
                    'created_at' => $transaction->date,
                    'user_id' => $transaction->user_id,
                ]);

                // Actualiza el dinero total global guardado en la tabla de usuarios
                $totalMoneyService->increment($user, $transaction->amount);

                // notificar al usuario
                $user->notify(new MovementNotification(
                    "Se ha registrado un ingreso programado en calendario $income->id",
                    "income",
                    route('incomes.index')
                ));
            }

            $transaction->status = 'Registrado';
            $transaction->save();

            $this->info("Procesado: {$transaction->id}");
        }

        $this->info('Transacciones programadas procesadas.');

        return self::SUCCESS;
    }
}
