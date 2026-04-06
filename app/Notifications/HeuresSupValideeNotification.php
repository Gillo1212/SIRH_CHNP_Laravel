<?php

namespace App\Notifications;

use App\Models\HeureSup;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie le Major quand la RH valide ses heures supplémentaires.
 */
class HeuresSupValideeNotification extends Notification
{
    use Queueable;

    public function __construct(private HeureSup $heureSup) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $ligne = $this->heureSup->lignePlanning;
        $agent = $ligne?->agent;

        return [
            'title'     => 'Heures supplémentaires validées',
            'message'   => number_format($this->heureSup->nb_heures, 1) . 'h pour '
                . ($agent?->nom_complet ?? 'un agent')
                . ' ont été validées par la RH.',
            'icon'      => 'fa-check-double',
            'color'     => '#D1FAE5',
            'iconColor' => '#059669',
            'url'       => route('major.heures-sup.index'),
        ];
    }
}
