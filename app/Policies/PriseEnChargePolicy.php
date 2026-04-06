<?php

namespace App\Policies;

use App\Models\PriseEnCharge;
use App\Models\User;

/**
 * PriseEnChargePolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les prises en charge médicales.
 *
 * Workflow :
 *  - Agent : demande pour soi, conjoint ou enfant
 *  - AgentRH : validation standard
 */
class PriseEnChargePolicy
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
     * Voir la liste des prises en charge.
     * - Agent : ses propres PEC (filtré en controller)
     * - AgentRH/DRH : toutes
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'AgentRH', 'DRH']);
    }

    /**
     * Voir le détail d'une prise en charge.
     * - Agent : uniquement la sienne
     * - AgentRH/DRH : toutes
     */
    public function view(User $user, PriseEnCharge $priseEnCharge): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        // Agent : sa propre PEC
        $agentId = $priseEnCharge->demande?->id_agent;
        return $user->hasRole('Agent')
            && $user->agent?->id_agent === $agentId;
    }

    /**
     * Créer une demande de prise en charge.
     * - Agent : pour soi, conjoint ou enfant
     * - AgentRH : peut créer au nom d'un agent
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'AgentRH', 'DRH']);
    }

    /**
     * Valider une PEC standard — AgentRH.
     * La demande doit être en attente.
     */
    public function valider(User $user, PriseEnCharge $priseEnCharge): bool
    {
        if (!$user->hasAnyRole(['AgentRH', 'DRH'])) {
            return false;
        }

        $statut = $priseEnCharge->demande?->statut_demande;
        return $statut === 'En_attente';
    }

    /**
     * Rejeter une PEC — AgentRH ou DRH.
     */
    public function rejeter(User $user, PriseEnCharge $priseEnCharge): bool
    {
        if (!$user->hasAnyRole(['AgentRH', 'DRH'])) {
            return false;
        }

        $statut = $priseEnCharge->demande?->statut_demande;
        return in_array($statut, ['En_attente', 'Validé']);
    }

    /**
     * Télécharger l'attestation de prise en charge.
     * - Agent : sa propre attestation (statut Approuvé)
     * - AgentRH/DRH : toutes les attestations générées
     */
    public function telechargerAttestation(User $user, PriseEnCharge $priseEnCharge): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        $agentId = $priseEnCharge->demande?->id_agent;
        $statut  = $priseEnCharge->demande?->statut_demande;

        return $user->agent?->id_agent === $agentId
            && $statut === 'Approuvé';
    }
}
