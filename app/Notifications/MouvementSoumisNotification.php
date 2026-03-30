<?php

namespace App\Notifications;

use App\Models\Mouvement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie le DRH qu'un mouvement a été enregistré et attend validation.
 */
class MouvementSoumisNotification extends Notification
{
    use Queueable;

    public function __construct(private Mouvement $mouvement) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $agent = $this->mouvement->agent;
        $type  = $this->mouvement->type_mouvement ?? $this->mouvement->type ?? '—';

        return [
            'title'     => 'Mouvement en attente de validation',
            'message'   => 'Un mouvement « ' . $type . ' » pour ' . ($agent->nom_complet ?? 'un agent') . ' nécessite votre validation.',
            'icon'      => 'fa-exchange-alt',
            'color'     => '#EDE9FE',
            'iconColor' => '#7C3AED',
            'url'       => route('drh.validations.mouvements'),
        ];
    }
}
