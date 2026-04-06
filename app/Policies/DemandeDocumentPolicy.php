<?php

namespace App\Policies;

use App\Models\DemandeDocument;
use App\Models\User;

/**
 * DemandeDocumentPolicy — Confidentialité CID
 * Contrôle d'accès granulaire sur les demandes de documents administratifs.
 *
 * Workflow :
 *  - Agent : demande en self-service
 *  - AgentRH : traitement, génération, rejet
 *  - DRH : signature des documents officiels
 */
class DemandeDocumentPolicy
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
     * Voir la liste des demandes.
     * - Agent : ses propres demandes (filtré en controller)
     * - AgentRH/DRH : toutes les demandes
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'AgentRH', 'DRH']);
    }

    /**
     * Voir le détail d'une demande.
     * - Agent : uniquement la sienne
     * - AgentRH/DRH : toutes
     */
    public function view(User $user, DemandeDocument $demande): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return true;
        }

        return $user->hasRole('Agent')
            && $user->agent?->id_agent === $demande->agent_id;
    }

    /**
     * Créer une demande de document — Agent (self-service).
     * AgentRH peut créer au nom d'un agent.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Agent', 'AgentRH', 'DRH']);
    }

    /**
     * Traiter une demande (passer en "en_cours") — AgentRH.
     */
    public function traiter(User $user, DemandeDocument $demande): bool
    {
        if (!$user->hasAnyRole(['AgentRH', 'DRH'])) {
            return false;
        }

        return in_array($demande->statut, ['en_attente', 'en_cours']);
    }

    /**
     * Générer le document — AgentRH.
     */
    public function generer(User $user, DemandeDocument $demande): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH'])
            && in_array($demande->statut, ['en_attente', 'en_cours']);
    }

    /**
     * Rejeter une demande — AgentRH.
     */
    public function rejeter(User $user, DemandeDocument $demande): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH'])
            && in_array($demande->statut, ['en_attente', 'en_cours']);
    }

    /**
     * Signer un document officiel — DRH uniquement.
     * Le document doit être prêt.
     */
    public function signer(User $user, DemandeDocument $demande): bool
    {
        return $user->hasRole('DRH')
            && $demande->statut === 'pret';
    }

    /**
     * Télécharger le document généré.
     * - Agent : son propre document, uniquement s'il est prêt
     * - AgentRH/DRH : tous les documents générés
     */
    public function telecharger(User $user, DemandeDocument $demande): bool
    {
        if ($user->hasAnyRole(['AgentRH', 'DRH'])) {
            return (bool) $demande->fichier_genere;
        }

        return $user->agent?->id_agent === $demande->agent_id
            && $demande->statut === 'pret'
            && (bool) $demande->fichier_genere;
    }
}
