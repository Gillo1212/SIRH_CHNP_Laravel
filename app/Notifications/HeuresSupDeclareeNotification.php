<?php

namespace App\Notifications;

use App\Models\HeureSup;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie les AgentRH quand un Major déclare des heures supplémentaires.
 */
class HeuresSupDeclareeNotification extends Notification
{
    use Queueable;

    public function __construct(private HeureSup $heureSup) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $ligne   = $this->heureSup->lignePlanning;
        $agent   = $ligne?->agent;
        $service = $ligne?->planning?->service;

        return [
            'title'     => 'Heures supplémentaires à valider',
            'message'   => ($agent?->nom_complet ?? 'Un agent')
                . ' — ' . number_format($this->heureSup->nb_heures, 1) . 'h sup'
                . ' (' . ($service?->nom_service ?? 'service inconnu') . ')',
            'icon'      => 'fa-business-time',
            'color'     => '#EDE9FE',
            'iconColor' => '#7C3AED',
            'url'       => route('rh.heures-sup.index'),
        ];
    }
}
