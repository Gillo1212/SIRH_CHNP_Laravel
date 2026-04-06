<?php

namespace App\Policies;

use App\Models\Conge;
use App\Models\Service;
use App\Models\User;

/**
 * CongePolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les congés.
 *
 * Workflow : Agent → (avis Major) → Manager (validation) → AgentRH (approbation finale)
 */
class CongePolicy
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
     * Voir la liste des congés.
     * - AgentRH/DRH : tous
     * - Manager/Major : leur service uniquement
     * - Agent : ses propres congés (filtré en controller)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'Major', 'Manager', 'AgentRH', 'DRH']);
    }

    /**
     * Voir le détail d'un congé.
     * - Agent : son propre congé
     * - Manager : congé d'un agent de son service
     * - Major : congé d'un agent de son service
     * - AgentRH/DRH : tous
     */
    public function view(User $user, Conge $conge): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        $agentId = $conge->demande?->id_agent;

        // Agent : son propre congé
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
     * Demander un congé (Agent) ou saisie physique (AgentRH).
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'AgentRH', 'DRH']);
    }

    /**
     * Donner un avis Major (ne valide pas, donne seulement un avis).
     */
    public function donnerAvis(User $user, Conge $conge): bool
    {
        if (!$user->hasRole('Major')) {
            return false;
        }

        $agentId = $conge->demande?->id_agent;
        return $this->agentDansServiceMajor($user, $agentId)
            && $conge->demande?->statut_demande === 'En_attente';
    }

    /**
     * Valider (1ère validation) — Manager uniquement.
     */
    public function validerManager(User $user, Conge $conge): bool
    {
        if (!$user->hasRole('Manager')) {
            return false;
        }

        $agentId = $conge->demande?->id_agent;
        $statut  = $conge->demande?->statut_demande;

        return $this->agentDansServiceManager($user, $agentId)
            && in_array($statut, ['En_attente', 'Avis_Major']);
    }

    /**
     * Rejeter (Manager).
     */
    public function rejeterManager(User $user, Conge $conge): bool
    {
        return $this->validerManager($user, $conge);
    }

    /**
     * Approuver (validation finale) — AgentRH.
     */
    public function approuverRH(User $user, Conge $conge): bool
    {
        if (!$user->hasAnyRole(['AgentRH', 'DRH'])) {
            return false;
        }

        $statut = $conge->demande?->statut_demande;
        return in_array($statut, ['En_attente', 'Validé']);
    }

    /**
     * Rejeter (AgentRH).
     */
    public function rejeterRH(User $user, Conge $conge): bool
    {
        return $this->approuverRH($user, $conge);
    }

    /**
     * Annuler un congé — Agent (son propre) ou AgentRH.
     */
    public function annuler(User $user, Conge $conge): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        // L'agent peut annuler son propre congé s'il est encore en attente
        $agentId = $conge->demande?->id_agent;
        $statut  = $conge->demande?->statut_demande;

        return $user->agent?->id_agent === $agentId
            && $statut === 'En_attente';
    }

    // ──────────────────────────────────────────────────────
    // HELPERS PRIVÉS
    // ──────────────────────────────────────────────────────

    private function agentDansServiceManager(User $user, ?int $agentId): bool
    {
        if (!$agentId) return false;

        $serviceId = Service::where('id_agent_manager', $user->id)->value('id_service');
        if (!$serviceId) return false;

        return \App\Models\Agent::where('id_agent', $agentId)
            ->where('id_service', $serviceId)
            ->exists();
    }

    private function agentDansServiceMajor(User $user, ?int $agentId): bool
    {
        if (!$agentId) return false;

        $serviceId = Service::where('id_agent_major', $user->id)->value('id_service');
        if (!$serviceId) return false;

        return \App\Models\Agent::where('id_agent', $agentId)
            ->where('id_service', $serviceId)
            ->exists();
    }
}
