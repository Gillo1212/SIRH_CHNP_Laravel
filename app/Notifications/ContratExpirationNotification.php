<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie les agents RH des contrats arrivant à expiration.
 * Envoyée automatiquement par la commande artisan sirh:verifier-contrats.
 */
class ContratExpirationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Contrat $contrat,
        private string  $urgence  // 'critical' | 'high' | 'medium' | 'low' | 'expired'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $agent  = $this->contrat->agent;
        $jours  = $this->contrat->jours_restants;
        $nom    = $agent->nom_complet ?? 'Agent inconnu';

        [$icon, $color, $iconColor, $message] = match ($this->urgence) {
            'expired'  => ['fa-exclamation-circle', '#FEE2E2', '#DC2626',
                            "Le contrat de {$nom} ({$this->contrat->type_contrat}) est expiré."],
            'critical' => ['fa-exclamation-triangle', '#FEE2E2', '#DC2626',
                            "Contrat de {$nom} expire dans {$jours} jour(s) — ACTION URGENTE."],
            'high'     => ['fa-clock', '#FEF3C7', '#D97706',
                            "Contrat de {$nom} expire dans {$jours} jour(s)."],
            'medium'   => ['fa-calendar-times', '#FEF3C7', '#F59E0B',
                            "Contrat de {$nom} expire dans {$jours} jour(s)."],
            default    => ['fa-calendar-alt', '#DBEAFE', '#1D4ED8',
                            "Contrat de {$nom} arrive à échéance dans {$jours} jour(s)."],
        };

        return [
            'title'      => 'Alerte expiration contrat',
            'message'    => $message,
            'icon'       => $icon,
            'color'      => $color,
            'iconColor'  => $iconColor,
            'url'        => route('rh.contrats.expiring'),
            'contrat_id' => $this->contrat->id_contrat,
            'agent_id'   => $agent->id_agent ?? null,
            'urgence'    => $this->urgence,
        ];
    }
}
