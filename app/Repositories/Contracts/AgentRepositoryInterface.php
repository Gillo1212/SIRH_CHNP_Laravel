<?php

namespace App\Repositories\Contracts;

use App\Models\Agent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AgentRepositoryInterface
{
    /**
     * Liste paginée avec filtres multicritères
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Tous les agents actifs (pour listes déroulantes)
     */
    public function allActifs(): Collection;

    /**
     * Trouver par ID (avec relations)
     */
    public function findById(int $id): Agent;

    /**
     * Créer un agent
     */
    public function create(array $data): Agent;

    /**
     * Mettre à jour un agent
     */
    public function update(int $id, array $data): Agent;

    /**
     * Soft-delete un agent
     */
    public function delete(int $id): void;

    /**
     * Restaurer un agent supprimé
     */
    public function restore(int $id): void;

    /**
     * Générer le prochain matricule (CHNP-00001)
     */
    public function nextMatricule(): string;

    /**
     * Vérifier l'unicité du login proposé
     */
    public function loginDisponible(string $login, ?int $exceptUserId = null): bool;
}
