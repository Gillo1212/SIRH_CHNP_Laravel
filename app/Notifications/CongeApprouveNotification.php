<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie l'Agent que son congé a été approuvé par le service RH.
 */
class CongeApprouveNotification extends Notification
{
    use Queueable;

    public function __construct(private Demande $demande) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $conge = $this->demande->conge;

        return [
            'title'     => 'Congé approuvé',
            'message'   => 'Votre congé de ' . ($conge->nbres_jours ?? '?') . ' jour(s) a été approuvé par le service RH.',
            'icon'      => 'fa-check-circle',
            'color'     => '#D1FAE5',
            'iconColor' => '#059669',
            'url'       => route('agent.conges.index'),
        ];
    }
}
