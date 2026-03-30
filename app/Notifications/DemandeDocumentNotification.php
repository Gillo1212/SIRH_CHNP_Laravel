<?php

namespace App\Notifications;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifie les AgentRH qu'une demande de document administratif a été soumise.
 */
class DemandeDocumentNotification extends Notification
{
    use Queueable;

    public function __construct(private Agent $agent, private string $typeDocument) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $labels = [
            'attestation_travail' => 'attestation de travail',
            'certificat_travail'  => 'certificat de travail',
            'ordre_mission'       => 'ordre de mission',
        ];

        $label = $labels[$this->typeDocument] ?? $this->typeDocument;

        return [
            'title'     => 'Demande de document administratif',
            'message'   => ($this->agent->nom_complet ?? 'Un agent') . ' demande une ' . $label . '.',
            'icon'      => 'fa-file-alt',
            'color'     => '#F3F4F6',
            'iconColor' => '#374151',
            'url'       => route('rh.demandes-docs.index'),
        ];
    }
}
