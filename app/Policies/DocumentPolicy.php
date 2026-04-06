<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * DocumentPolicy — Confidentialité CID (GED)
 * Contrôle d'accès basé sur le niveau de confidentialité du document.
 *
 * Niveaux de confidentialité :
 *  - Public      : tous les utilisateurs authentifiés
 *  - Interne     : tous les utilisateurs authentifiés (idem Public dans ce contexte)
 *  - Confidentiel: AgentRH, DRH, AdminSystème
 *  - Secret      : DRH, AdminSystème uniquement
 */
class DocumentPolicy
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
     * Voir la liste des documents.
     * Tout utilisateur authentifié peut accéder à la GED
     * (les documents secrets sont filtrés en controller).
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtrage par niveau de confidentialité dans le controller
    }

    /**
     * Voir le contenu d'un document selon son niveau de confidentialité.
     */
    public function view(User $user, Document $document): bool
    {
        return match ($document->niveau_confidentialite) {
            'Public', 'Interne' => true,
            'Confidentiel'      => $user->hasAnyRole(['AgentRH', 'DRH']),
            'Secret'            => $user->hasRole('DRH'),
            default             => false,
        };
    }

    /**
     * Uploader / créer un document — AgentRH/DRH.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Modifier les métadonnées d'un document — AgentRH/DRH.
     */
    public function update(User $user, Document $document): bool
    {
        if ($user->hasRole('DRH')) {
            return true;
        }

        // AgentRH : seulement pour les documents non secrets
        return $user->hasRole('AgentRH')
            && $document->niveau_confidentialite !== 'Secret';
    }

    /**
     * Supprimer un document — DRH uniquement (action irréversible).
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->hasRole('DRH');
    }

    /**
     * Télécharger un document (même règle que view).
     */
    public function telecharger(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    /**
     * Gérer les étagères (créer, modifier) — AgentRH/DRH.
     */
    public function gererEtageres(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }

    /**
     * Gérer les dossiers (créer, modifier) — AgentRH/DRH.
     */
    public function gererDossiers(User $user): bool
    {
        return $user->hasAnyRole(['AgentRH', 'DRH']);
    }
}
