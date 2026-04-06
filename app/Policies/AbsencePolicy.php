<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\Agent;
use App\Models\Service;
use App\Models\User;

/**
 * AbsencePolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les absences.
 *
 * Enregistrement : Manager ou Major → Validation justificatif : AgentRH
 */
class AbsencePolicy
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
     * Voir la liste des absences.
     * - AgentRH/DRH : toutes
     * - Manager/Major : leur service uniquement (filtré en controller)
     * - Agent : ses propres absences (filtré en controller)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'Major', 'Manager', 'AgentRH', 'DRH']);
    }

    /**
     * Voir le détail d'une absence.
     * - Agent : sa propre absence
     * - Manager : absences de son service
     * - Major : absences de son service
     * - AgentRH/DRH : toutes
     */
    public function view(User $user, Absence $absence): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        $agentId = $absence->demande?->id_agent;

        // Agent : sa propre absence
        if ($user->hasRole('Agent') && $user->agent?->id_agent === $agentId) {
            return true;
        }

        // Manager : agents de son service
        if ($user->hasRole('Manager')) {
            return $this->agentDansServiceManager($user, $agentId);
        }

        // Major : agents de son service
        if ($user->hasRole('Major')) {
            return $this->agentDansServiceMajor($user, $agentId);
        }

        return false;
    }

    /**
     * Enregistrer une absence.
     * - Manager : pour les agents de son service
     * - Major : pour les agents de son service
     * - AgentRH/DRH : pour tous
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Major', 'Manager', 'AgentRH', 'DRH']);
    }

    /**
     * Modifier une absence.
     * - Manager/Major : pour les absences de leur service (si non validée)
     * - AgentRH/DRH : toutes
     */
    public function update(User $user, Absence $absence): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        $agentId = $absence->demande?->id_agent;
        $statut  = $absence->demande?->statut_demande;

        // Ne peut pas modifier une absence déjà traitée
        if (in_array($statut, ['Approuvé', 'Rejeté'])) {
            return false;
        }

        if ($user->hasRole('Manager')) {
            return $this->agentDansServiceManager($user, $agentId);
        }

        if ($user->hasRole('Major')) {
            return $this->agentDansServiceMajor($user, $agentId);
        }

        return false;
    }

    /**
     * Supprimer une absence — AgentRH/DRH uniquement.
     */
    public function delete(User $user, Absence $absence): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Valider le justificatif d'une absence — AgentRH uniquement.
     */
    public function validerJustificatif(User $user, Absence $absence): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Justifier une absence — Agent (sa propre absence).
     */
    public function justifier(User $user, Absence $absence): bool
    {
        $agentId = $absence->demande?->id_agent;
        return $user->agent?->id_agent === $agentId;
    }

    // ──────────────────────────────────────────────────────
    // HELPERS PRIVÉS
    // ──────────────────────────────────────────────────────

    private function agentDansServiceManager(User $user, ?int $agentId): bool
    {
        if (!$agentId) return false;

        $serviceId = Service::where('id_agent_manager', $user->id)->value('id_service');
        if (!$serviceId) return false;

        return Agent::where('id_agent', $agentId)
            ->where('id_service', $serviceId)
            ->exists();
    }

    private function agentDansServiceMajor(User $user, ?int $agentId): bool
    {
        if (!$agentId) return false;

        $serviceId = Service::where('id_agent_major', $user->id)->value('id_service');
        if (!$serviceId) return false;

        return Agent::where('id_agent', $agentId)
            ->where('id_service', $serviceId)
            ->exists();
    }
}
