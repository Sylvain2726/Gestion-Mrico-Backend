<?php

namespace App\Notifications;

use App\Models\Pret;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EcheancePretNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Pret $pret
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dateEcheance = $this->pret->date_echeant->format('d/m/Y');
        $montantRestant = number_format($this->pret->montant_rest, 0, ',', ' ');

        return (new MailMessage)
            ->subject('Rappel d\'échéance de prêt - Action requise')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous informons que votre prêt arrive à échéance dans 3 jours.')
            ->line('**Détails du prêt :**')
            ->line('- Date d\'échéance : ' . $dateEcheance)
            ->line('- Montant restant à payer : ' . $montantRestant . ' FCFA')
            ->line('- Numéro de prêt : #' . $this->pret->id)
            ->line('**Il est important de vous présenter dans nos bureaux pour effectuer le paiement avant la date d\'échéance.**')
            ->line('Pour toute question ou information complémentaire, n\'hésitez pas à nous contacter.')
            ->salutation('Cordialement,')
            ->line('L\'équipe G Micro Service');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pret_id' => $this->pret->id,
            'date_echeance' => $this->pret->date_echeant,
            'montant_restant' => $this->pret->montant_rest,
        ];
    }
}
