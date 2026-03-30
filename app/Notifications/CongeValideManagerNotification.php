<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie les AgentRH quand un Manager valide une demande de congé (transmise au RH).
 */
class CongeValideManagerNotification extends Notification
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
            'title'     => 'Congé validé par le Manager',
            'message'   => ($agent->nom_complet ?? 'Un agent') . ' — ' . ($conge->nbres_jours ?? '?') . ' jour(s) en attente d\'approbation RH.',
            'icon'      => 'fa-calendar-check',
            'color'     => '#D1FAE5',
            'iconColor' => '#059669',
            'url'       => route('rh.conges.pending'),
        ];
    }
}
