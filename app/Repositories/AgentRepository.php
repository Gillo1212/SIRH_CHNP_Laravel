<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\User;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AgentRepository implements AgentRepositoryInterface
{
    /**
     * Liste paginée avec filtres multicritères
     * Disponibilité CID : eager loading pour éviter N+1
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Agent::with(['service:id_service,nom_service', 'user:id,statut_compte'])
            ->select('id_agent', 'matricule', 'nom', 'prenom', 'fonction', 'grade',
                     'categorie_cp', 'statut', 'photo', 'id_service', 'user_id',
                     'date_recrutement', 'created_at');

        // Filtre texte : nom, prénom, matricule, fonction
        if (!empty($filters['recherche'])) {
            $terme = $filters['recherche'];
            $query->where(function ($q) use ($terme) {
                $q->where('nom', 'like', "%{$terme}%")
                  ->orWhere('prenom', 'like', "%{$terme}%")
                  ->orWhere('matricule', 'like', "%{$terme}%")
                  ->orWhere('fonction', 'like', "%{$terme}%");
            });
        }

        // Filtre service
        if (!empty($filters['service'])) {
            $query->where('id_service', $filters['service']);
        }

        // Filtre statut
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        // Filtre catégorie
        if (!empty($filters['categorie'])) {
            $query->where('categorie_cp', $filters['categorie']);
        }

        // Filtre sexe
        if (!empty($filters['sexe'])) {
            $query->where('sexe', $filters['sexe']);
        }

        return $query->orderBy('nom')->orderBy('prenom')->paginate($perPage)->withQueryString();
    }

    /**
     * Tous les agents actifs pour les listes déroulantes
     */
    public function allActifs(): Collection
    {
        return Agent::actif()
            ->select('id_agent', 'matricule', 'nom', 'prenom', 'fonction')
            ->orderBy('nom')->orderBy('prenom')
            ->get();
    }

    /**
     * Trouver par ID avec toutes les relations nécessaires
     */
    public function findById(int $id): Agent
    {
        return Agent::with([
            'user:id,login,statut_compte,derniere_connexion',
            'service:id_service,nom_service',
            'division:id_division,nom_division',
            'enfants',
            'conjoints',
            'contrats' => fn($q) => $q->orderBy('date_debut', 'desc'),
            'mouvements' => fn($q) => $q->with('service:id_service,nom_service')->orderBy('date_mouvement', 'desc')->limit(5),
        ])->findOrFail($id);
    }

    /**
     * Créer un agent (sans la logique métier — dans le Service)
     */
    public function create(array $data): Agent
    {
        return Agent::create($data);
    }

    /**
     * Mettre à jour un agent
     */
    public function update(int $id, array $data): Agent
    {
        $agent = Agent::findOrFail($id);
        $agent->update($data);
        return $agent->fresh();
    }

    /**
     * Soft-delete
     */
    public function delete(int $id): void
    {
        Agent::findOrFail($id)->delete();
    }

    /**
     * Restaurer un agent supprimé
     */
    public function restore(int $id): void
    {
        Agent::withTrashed()->findOrFail($id)->restore();
    }

    /**
     * Génère le prochain matricule au format CHNP-XXXXX
     * Intégrité CID : séquence atomique basée sur MAX
     */
    public function nextMatricule(): string
    {
        // Prend en compte les agents supprimés (withTrashed) pour éviter les doublons
        $last = Agent::withTrashed()
            ->where('matricule', 'like', 'CHNP-%')
            ->orderByRaw("CAST(SUBSTRING(matricule, 6) AS UNSIGNED) DESC")
            ->value('matricule');

        if (!$last) {
            return 'CHNP-00001';
        }

        $num = (int) substr($last, 5);
        return 'CHNP-' . str_pad($num + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Vérifie si un login est disponible
     */
    public function loginDisponible(string $login, ?int $exceptUserId = null): bool
    {
        $query = User::where('login', $login);
        if ($exceptUserId) {
            $query->where('id', '!=', $exceptUserId);
        }
        return !$query->exists();
    }
}
