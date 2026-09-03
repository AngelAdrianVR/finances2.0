<?php

namespace App\Notifications;

use App\Models\Outcome;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutcomePaymentReminder extends Notification
{
    use Queueable;

    public function __construct(public Outcome $outcome) {}

    /**
     * Delivery channels: correo + notificación dentro de la app.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $outcome = $this->outcome;
        $dueDate = Carbon::parse($outcome->payment_due_date);
        $daysLeft = Carbon::today()->startOfDay()->diffInDays($dueDate->startOfDay(), false);

        if ($daysLeft <= 0) {
            $whenLine = 'Este pago vence HOY.';
        } elseif ($daysLeft === 1) {
            $whenLine = 'Este pago vence MAÑANA.';
        } else {
            $whenLine = "Este pago vence en {$daysLeft} días.";
        }

        $amount = '$'.number_format((float) $outcome->amount, 2);

        return (new MailMessage)
            ->subject("Recordatorio de pago: {$outcome->concept}")
            ->greeting("¡Hola {$notifiable->name}!")
            ->line('Tienes un gasto a crédito pendiente de pago:')
            ->lines([
                "• Concepto: {$outcome->concept}",
                "• Monto: {$amount}",
                "• Fecha límite de pago: {$dueDate->format('d/m/Y')}",
            ])
            ->line($whenLine)
            ->action('Ver mis gastos', route('outcomes.index'))
            ->line('Si ya realizaste el pago o no registraste este gasto, puedes ignorar este correo. Puedes desactivar o ajustar estos recordatorios desde la sección de Configuraciones.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $dueDate = Carbon::parse($this->outcome->payment_due_date);

        return [
            'description' => "Recordatorio: tu gasto a crédito \"{$this->outcome->concept}\" vence el {$dueDate->format('d/m/Y')}.",
            'type' => 'reminder',
            'url' => route('outcomes.index'),
        ];
    }
}
