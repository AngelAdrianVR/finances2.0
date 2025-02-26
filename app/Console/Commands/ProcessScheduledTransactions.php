<?php

namespace App\Console\Commands;

use App\Models\Calendar;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\User;
use App\Notifications\MovementNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessScheduledTransactions extends Command
{
    protected $signature = 'calendar:process-scheduled';
    protected $description = 'Procesa ingresos o gastos programados para el día actual';

    public function handle()
    {
        $today = Carbon::today();
        $transactions = Calendar::where('date', $today)
            ->where('status', 'Pendiente') // Ajusta según tu lógica
            ->get();


        foreach ($transactions as $transaction) {
            if ( $transaction->type === 'Gasto fijo' ) {
                //Se crea el ingreso
                $outcome = Outcome::create([
                    'concept' => $transaction->title,
                    'amount' => $transaction->amount,
                    'payment_method' => $transaction->payment_method,
                    'category' => $transaction->category,
                    'automatically_created' => true, //se creo automaticamente por el recordatorio
                    'description' => $transaction->description,
                    'user_id' => $transaction->user_id,
                ]);

                //se resta la cantidad al dinero total global guardado en la tabla de usuarios
                $user = User::find($transaction->user_id);
                
                if ( $user->total_money >= $transaction->amount ) {
                    $user->total_money -= $transaction->amount;
                    $user->save();
                }

                // notificar al usuario
                $user->notify(new MovementNotification(
                    "Se ha registrado un egreso programado en calendario $outcome->id",
                    "outcome",
                    route('outcomes.index')
                ));

            } elseif ( $transaction->type === 'Ingreso recurrente' ) {
                $income = Income::create([
                    'concept' => $transaction->title,
                    'amount' => $transaction->amount,
                    'payment_method' => $transaction->payment_method,
                    'category' => $transaction->category,
                    'automatically_created' => true, //se creo automaticamente por el recordatorio
                    'description' => $transaction->description,
                    'user_id' => $transaction->user_id,
                ]);

                //se aumenta la cantidad al dinero total global guardado en la tabla de usuarios
                $user = User::find($transaction->user_id);                
                $user->total_money += $transaction->amount;
                $user->save();


                // notificar al usuario
                $user->notify(new MovementNotification(
                    "Se ha registrado un ingreso programado en calendario $income->id",
                    "income",
                    route('incomes.index')
                ));
            }
            $transaction->status = 'Registrado'; // Cambia según tu lógica
            $transaction->save();
            $this->info("Procesado: {$transaction->id}");
        }

        $this->info('Transacciones programadas procesadas.');
    }
}
