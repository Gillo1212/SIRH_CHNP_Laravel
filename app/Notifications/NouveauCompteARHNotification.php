<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifie la RH qu'un nouveau compte vient d'être créé et que le dossier agent doit être complété.
 */
class NouveauCompteARHNotification extends Notification
{
    public function __construct(
        protected User $newUser
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('SIRH CHNP — Nouveau compte à compléter')
            ->greeting('Bonjour,')
            ->line('Un nouveau compte utilisateur a été créé par l\'administrateur système.')
            ->line('**Login :** ' . $this->newUser->login)
            ->line('**Email :** ' . ($this->newUser->email ?? '—'))
            ->line('**Rôle(s) :** ' . $this->newUser->roles->pluck('name')->join(', '))
            ->action('Compléter le dossier agent', route('rh.agents.index') . '?user_id=' . $this->newUser->id)
            ->line('Merci de compléter le dossier RH de cet agent dès que possible.');
    }

    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'login'   => $this->newUser->login,
            'message' => 'Nouveau compte à compléter : ' . $this->newUser->login,
            'type'    => 'nouveau_compte',
        ];
    }
}
