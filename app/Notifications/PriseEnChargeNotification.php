<?php

namespace App\Notifications;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie les AgentRH qu'une demande de prise en charge médicale a été soumise.
 */
class PriseEnChargeNotification extends Notification
{
    use Queueable;

    public function __construct(private Agent $agent) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Nouvelle demande de prise en charge',
            'message'   => ($this->agent->nom_complet ?? 'Un agent') . ' a soumis une demande de prise en charge médicale.',
            'icon'      => 'fa-heartbeat',
            'color'     => '#FEF3C7',
            'iconColor' => '#D97706',
            'url'       => route('pec.index'),
        ];
    }
}
