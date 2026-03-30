<?php

namespace App\Exports;

use App\Models\Agent;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export CSV des agents
 * Note : utilisation de CSV natif PHP (maatwebsite/excel v1.1 non compatible Laravel 12)
 * Confidentialité CID : les champs chiffrés (téléphone, cni) ne sont PAS exportés.
 */
class AgentsExport
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Génère le StreamedResponse CSV
     */
    public function download(): StreamedResponse
    {
        $filename = 'agents_chnp_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // BOM pour Excel (UTF-8)
            fputs($handle, "\xEF\xBB\xBF");

            // En-têtes
            fputcsv($handle, [
                'Matricule', 'Nom', 'Prénom', 'Sexe',
                'Date Naissance', 'Catégorie CSP',
                "Famille d'emploi", 'Statut contrat',
                'Service', 'Division',
            ], ';');

            // Données (sans les champs chiffrés — Confidentialité CID)
            $query = Agent::with(['service:id_service,nom_service', 'division:id_division,nom_division'])
                ->select('id_agent', 'matricule', 'nom', 'prenom', 'sexe',
                         'date_naissance', 'famille_d_emploi',
                         'categorie_cp', 'statut_agent', 'id_service', 'id_division');

            if (!empty($this->filters['statut_agent'])) {
                $query->where('statut_agent', $this->filters['statut_agent']);
            }
            if (!empty($this->filters['service'])) {
                $query->where('id_service', $this->filters['service']);
            }

            $query->orderBy('nom')->orderBy('prenom')
                ->chunk(200, function ($agents) use ($handle) {
                    foreach ($agents as $agent) {
                        fputcsv($handle, [
                            $agent->matricule,
                            $agent->nom,
                            $agent->prenom,
                            $agent->sexe === 'M' ? 'Masculin' : 'Féminin',
                            $agent->date_naissance?->format('d/m/Y'),
                            str_replace('_', ' ', $agent->categorie_cp ?? '—'),
                            $agent->famille_d_emploi ? str_replace('_', ' ', $agent->famille_d_emploi) : '—',
                            $agent->statut_agent ?? '—',
                            $agent->service?->nom_service ?? '—',
                            $agent->division?->nom_division ?? '—',
                        ], ';');
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
