<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

/**
 * ServicePolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les services hospitaliers.
 */
class ServicePolicy
{
    /**
     * AdminSystème a accès à tout
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('AdminSystème')) {
            return true;
        }
        return null;
    }

    /**
     * Voir la liste des services
     * - AgentRH/DRH : tous les services
     * - Manager : seulement son service (filtrage applicatif)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH', 'Manager']);
    }

    /**
     * Voir un service précis
     * - AgentRH/DRH : tous
     * - Manager : uniquement le sien
     */
    public function view(User $user, Service $service): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            return $service->id_agent_manager === $user->id;
        }

        return false;
    }

    /**
     * Créer un service — AgentRH et DRH uniquement
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Modifier un service — AgentRH et DRH uniquement
     */
    public function update(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Supprimer un service — DRH uniquement (si aucun agent)
     */
    public function delete(User $user, Service $service): bool
    {
        if (!$user->hasRole('DRH')) {
            return false;
        }
        // Bloquer si des agents sont encore affectés
        return $service->agents()->count() === 0;
    }

    /**
     * Assigner un manager à un service
     */
    public function assignerManager(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Gérer les agents du service
     * - AgentRH/DRH : tous les services
     * - Manager : seulement son service
     */
    public function manageAgents(User $user, Service $service): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            return $service->id_agent_manager === $user->id;
        }

        return false;
    }
}
