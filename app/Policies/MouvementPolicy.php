<?php

namespace App\Policies;

use App\Models\Mouvement;
use App\Models\User;

/**
 * MouvementPolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les mouvements de personnel.
 */
class MouvementPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('AdminSystème')) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH', 'Manager']);
    }

    public function view(User $user, Mouvement $mouvement): bool
    {
        // AgentRH et DRH voient tout
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) return true;

        // Manager voit les mouvements de son service
        if ($user->hasRole('Manager')) {
            $agentManagerId = $user->agent?->id_agent;
            $serviceManager = \App\Models\Service::where('id_agent_manager', $user->id)->value('id_service');
            return $serviceManager &&
                ($mouvement->id_service === $serviceManager || $mouvement->id_service_origine === $serviceManager);
        }

        // Agent voit ses propres mouvements
        return $user->agent?->id_agent === $mouvement->id_agent;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    public function update(User $user, Mouvement $mouvement): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']) && $mouvement->est_modifiable;
    }

    public function valider(User $user, Mouvement $mouvement): bool
    {
        return $user->hasRole('DRH') && $mouvement->est_validable;
    }

    public function effectuer(User $user, Mouvement $mouvement): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']) && $mouvement->est_effectuable;
    }

    public function annuler(User $user, Mouvement $mouvement): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']) && $mouvement->est_annulable;
    }
}
