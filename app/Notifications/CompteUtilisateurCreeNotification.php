<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CompteUtilisateurCreeNotification extends Notification
{
    public function __construct(
        public string $login,
        public string $motDePasseTemporaire
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('SIRH CHNP — Vos identifiants de connexion')
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line('Votre compte sur le Système d\'Information RH du CHNP a été créé.')
            ->line('**Identifiant :** ' . $this->login)
            ->line('**Mot de passe temporaire :** ' . $this->motDePasseTemporaire)
            ->line('Vous devrez changer votre mot de passe lors de votre première connexion.')
            ->action('Se connecter au SIRH', route('login'))
            ->line('Pour toute question, contactez le service RH.');
    }
}
