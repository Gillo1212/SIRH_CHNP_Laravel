<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie le Manager quand un agent de son service soumet une demande de congé.
 */
class CongeDemandeNotification extends Notification
{
    use Queueable;

    public function __construct(private Demande $demande) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $agent = $this->demande->agent;
        $conge = $this->demande->conge;

        return [
            'title'     => 'Nouvelle demande de congé',
            'message'   => ($agent->nom_complet ?? 'Un agent') . ' demande ' . ($conge->nbres_jours ?? '?') . ' jour(s) à valider.',
            'icon'      => 'fa-calendar-plus',
            'color'     => '#DBEAFE',
            'iconColor' => '#1D4ED8',
            'url'       => route('manager.conges.pending'),
        ];
    }
}
