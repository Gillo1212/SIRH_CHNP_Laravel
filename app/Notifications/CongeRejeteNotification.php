<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie l'Agent que son congé a été rejeté (par Manager ou RH).
 */
class CongeRejeteNotification extends Notification
{
    use Queueable;

    public function __construct(private Demande $demande, private string $par = 'le service RH') {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $motif = $this->demande->motif_refus ?? 'Aucun motif précisé.';

        return [
            'title'     => 'Congé rejeté',
            'message'   => 'Votre demande de congé a été rejetée par ' . $this->par . '. Motif : ' . $motif,
            'icon'      => 'fa-times-circle',
            'color'     => '#FEE2E2',
            'iconColor' => '#DC2626',
            'url'       => route('agent.conges.index'),
        ];
    }
}
