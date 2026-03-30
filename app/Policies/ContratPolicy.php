<?php

namespace App\Policies;

use App\Models\Contrat;
use App\Models\User;

/**
 * ContratPolicy — Confidentialité CID
 * Couche d'autorisation granulaire sur les contrats.
 * Complète la protection middleware role:AgentRH|DRH (défense en profondeur).
 */
class ContratPolicy
{
    /**
     * AdminSystème bypass toutes les vérifications.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('AdminSystème')) {
            return true;
        }
        return null;
    }

    /**
     * Voir la liste des contrats.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Voir le détail d'un contrat.
     * AgentRH/DRH : tous les contrats.
     * Agent : uniquement son propre contrat (via ProfilController, pas ce Policy).
     */
    public function view(User $user, Contrat $contrat): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Créer un nouveau contrat.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Modifier un contrat.
     */
    public function update(User $user, Contrat $contrat): bool
    {
        // Un contrat clôturé ne peut pas être modifié
        if ($contrat->statut_contrat === 'Clôturé') {
            return false;
        }
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Renouveler un contrat.
     */
    public function renouveler(User $user, Contrat $contrat): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH'])
            && $contrat->statut_contrat === 'Actif';
    }

    /**
     * Clôturer un contrat.
     */
    public function cloturer(User $user, Contrat $contrat): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH'])
            && $contrat->statut_contrat !== 'Clôturé';
    }
}
