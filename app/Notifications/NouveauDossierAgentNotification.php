<?php

namespace App\Notifications;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NouveauDossierAgentNotification extends Notification
{
    use Queueable;

    public function __construct(public Agent $agent) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'nouveau_dossier_agent',
            'agent_id'   => $this->agent->id_agent,
            'nom_complet'=> $this->agent->prenom . ' ' . $this->agent->nom,
            'matricule'  => $this->agent->matricule,
            'message'    => "Nouveau dossier agent : {$this->agent->prenom} {$this->agent->nom} ({$this->agent->matricule}) — compte utilisateur à créer.",
            'url'        => route('admin.users.create-for-agent', $this->agent->id_agent),
        ];
    }
}
