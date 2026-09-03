<?php

namespace App\Console\Commands;

use App\Models\Outcome;
use App\Models\User;
use App\Notifications\OutcomePaymentReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendOutcomePaymentReminders extends Command
{
    protected $signature = 'outcomes:send-payment-reminders';

    protected $description = 'Envía por correo recordatorios de gastos a crédito próximos a vencer';

    public function handle(): int
    {
        $today = Carbon::today();
        $sent = 0;

        // Solo se notifica a usuarios con recordatorios activados.
        $users = User::where('payment_reminder_enabled', true)->cursor();

        foreach ($users as $user) {
            $daysBefore = (int) ($user->payment_reminder_days_before ?? 2);

            // Ventana: [hoy, hoy + N días]. Así cada gasto se recuerda desde
            // N días antes de la fecha límite hasta el día del vencimiento.
            $windowEnd = $today->copy()->addDays($daysBefore);

            $outcomes = Outcome::where('user_id', $user->id)
                ->where('is_credit', true)
                ->whereNotNull('payment_due_date')
                ->whereBetween('payment_due_date', [$today->toDateString(), $windowEnd->toDateString()])
                ->where(function ($query) use ($today) {
                    // Evita enviar el mismo recordatorio dos veces en un mismo día
                    // si el cron se ejecuta más de una vez.
                    $query->whereNull('reminder_last_sent_date')
                        ->orWhereDate('reminder_last_sent_date', '<', $today->toDateString());
                })
                ->get();

            foreach ($outcomes as $outcome) {
                $user->notify(new OutcomePaymentReminder($outcome));

                $outcome->forceFill(['reminder_last_sent_date' => $today->toDateString()])->save();

                $this->line("Recordatorio enviado al usuario #{$user->id} por el gasto #{$outcome->id} ({$outcome->concept})");
                $sent++;
            }
        }

        $this->info("Recordatorios de pago enviados: {$sent}.");

        return self::SUCCESS;
    }
}
