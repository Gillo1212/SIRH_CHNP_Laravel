<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;

/**
 * AgentPolicy — Confidentialité CID
 * Contrôle d'accès granulaire au niveau modèle
 */
class AgentPolicy
{
    /**
     * Avant toute vérification : AdminSystème a accès à tout
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('AdminSystème')) {
            return true;
        }
        return null;
    }

    /**
     * Voir la liste des agents
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH', 'Manager']);
    }

    /**
     * Voir le dossier complet d'un agent
     * - Agent : son propre dossier seulement
     * - Manager : uniquement les agents de son service
     * - AgentRH/DRH : tous les agents
     */
    public function view(User $user, Agent $agent): bool
    {
        // L'agent peut voir son propre dossier
        if ($user->agent && $user->agent->id_agent === $agent->id_agent) {
            return true;
        }

        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        // Manager : seulement les agents de son service
        if ($user->hasRole('Manager')) {
            $service = \App\Models\Service::where('id_agent_manager', $user->id)->first();
            return $service && $agent->id_service === $service->id_service;
        }

        return false;
    }

    /**
     * Créer un nouvel agent
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Modifier un agent
     */
    public function update(User $user, Agent $agent): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Supprimer un agent (soft delete)
     */
    public function delete(User $user, Agent $agent): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Restaurer un agent supprimé
     */
    public function restore(User $user, Agent $agent): bool
    {
        return $user->hasRole('DRH');
    }

    /**
     * Voir les données sensibles déchiffrées
     * (téléphone, adresse, numéro assurance)
     */
    public function voirDonneesSensibles(User $user, Agent $agent): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Exporter les données
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }
}
