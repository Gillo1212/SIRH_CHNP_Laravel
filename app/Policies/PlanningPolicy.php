<?php

namespace App\Policies;

use App\Models\Planning;
use App\Models\Service;
use App\Models\User;

/**
 * PlanningPolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les plannings de service.
 *
 * Cycle de vie : Brouillon → Transmis → Validé|Rejeté
 * Créateurs : Manager ou Major (pour leur service)
 * Validateur : AgentRH
 */
class PlanningPolicy
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
     * Voir la liste des plannings.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'Major', 'Manager', 'AgentRH', 'DRH']);
    }

    /**
     * Voir le détail d'un planning.
     * - Agent : le planning de son service
     * - Manager/Major : le planning de leur service
     * - AgentRH/DRH : tous
     */
    public function view(User $user, Planning $planning): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        // Vérifier que le planning appartient au service de l'utilisateur
        $agentServiceId = $user->agent?->id_service;
        if ($agentServiceId && $agentServiceId === $planning->id_service) {
            return true;
        }

        // Manager : son service
        if ($user->hasRole('Manager')) {
            $serviceId = Service::where('id_agent_manager', $user->id)->value('id_service');
            return $serviceId && $serviceId === $planning->id_service;
        }

        // Major : son service
        if ($user->hasRole('Major')) {
            $serviceId = Service::where('id_agent_major', $user->id)->value('id_service');
            return $serviceId && $serviceId === $planning->id_service;
        }

        return false;
    }

    /**
     * Créer un planning.
     * - Manager : pour son service
     * - Major : pour son service
     * - AgentRH : pour tout service
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Manager', 'Major', 'AgentRH', 'DRH']);
    }

    /**
     * Modifier un planning.
     * Seul un planning Brouillon ou Rejeté peut être modifié.
     * Le Manager/Major ne peut modifier que celui de leur service.
     */
    public function update(User $user, Planning $planning): bool
    {
        if (!$planning->est_modifiable) {
            return false;
        }

        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            $serviceId = Service::where('id_agent_manager', $user->id)->value('id_service');
            return $serviceId && $serviceId === $planning->id_service;
        }

        if ($user->hasRole('Major')) {
            $serviceId = Service::where('id_agent_major', $user->id)->value('id_service');
            return $serviceId && $serviceId === $planning->id_service;
        }

        return false;
    }

    /**
     * Transmettre un planning à la RH.
     * Seul un planning en Brouillon peut être transmis.
     * Manager/Major : uniquement leur service.
     */
    public function transmettre(User $user, Planning $planning): bool
    {
        if ($planning->statut_planning !== 'Brouillon') {
            return false;
        }

        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            $serviceId = Service::where('id_agent_manager', $user->id)->value('id_service');
            return $serviceId && $serviceId === $planning->id_service;
        }

        if ($user->hasRole('Major')) {
            $serviceId = Service::where('id_agent_major', $user->id)->value('id_service');
            return $serviceId && $serviceId === $planning->id_service;
        }

        return false;
    }

    /**
     * Valider un planning — AgentRH/DRH.
     * Le planning doit être en statut Transmis.
     */
    public function valider(User $user, Planning $planning): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH'])
            && $planning->statut_planning === 'Transmis';
    }

    /**
     * Rejeter un planning — AgentRH/DRH.
     * Le planning doit être en statut Transmis.
     */
    public function rejeter(User $user, Planning $planning): bool
    {
        return $this->valider($user, $planning);
    }

    /**
     * Supprimer un planning — AgentRH/DRH.
     * Uniquement s'il est en Brouillon.
     */
    public function delete(User $user, Planning $planning): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH'])
            && $planning->statut_planning === 'Brouillon';
    }
}
