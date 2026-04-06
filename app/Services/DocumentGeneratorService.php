<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\DemandeDocument;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;

/**
 * Service de génération de documents administratifs.
 * 
 * @author Gilbert - Mémoire M2 SIRH CHNP
 */
class DocumentGeneratorService
{
    /**
     * Textes de loi pour les visas.
     */
    private const VISAS_LEGAUX = [
        'Vu la Constitution ;',
        'Vu la loi 61-33 du 15 juin 1961 relative au statut général des fonctionnaires modifiée ;',
        'Vu la loi 97-17 du 1er décembre 1997 portant Code du Travail ;',
        'Vu la loi 98-08 du 02 mars 1998, portant réforme hospitalière modifiée ;',
        'Vu le décret 95-264 du 10 mars 1995 portant délégation de pouvoir du Président de la République en matière d\'Administration et de gestion du personnel ;',
        'Vu le décret n° 98-701 du 26 Août 1998 relatif à l\'organisation des EPSH ;',
        'Vu le décret n° 98-702 du 26 Août 1998 portant organisation administrative et financière des EPS notamment en ses articles 14 et 16 ;',
        'Vu le décret n° 2007-317 du 1er mars 2007, portant création d\'un établissement public de santé hospitalier de niveau III dénommé « Centre Hospitalier National de Pikine » ;',
    ];

    /**
     * Récupère les informations de l'établissement.
     */
    public function getInfosEtablissement(): array
    {
        return [
            'nom'   => Setting::get('etablissement_nom', 'Centre Hospitalier National de Pikine'),
            'sigle' => Setting::get('etablissement_sigle', 'CHNP'),
        ];
    }

    /**
     * Récupère les informations du directeur.
     */
    public function getInfosDirecteur(): array
    {
        return [
            'civilite' => Setting::get('directeur_civilite', 'Docteur'),
            'nom'      => Setting::get('directeur_nom', 'Souleymane LOUCAR'),
            'titre'    => Setting::get('directeur_titre', 'Directeur du Centre hospitalier national de Pikine'),
            'decret'   => Setting::get('directeur_decret', 'décret n° 2025-480 du 18 mars 2025'),
        ];
    }

    /**
     * Génère les visas légaux.
     */
    public function genererVisas(): array
    {
        return self::VISAS_LEGAUX;
    }

    /**
     * Génère les ampliations standard.
     */
    public function genererAmpliations(array $additionnelles = []): array
    {
        $standard = ['SRH', 'Intéressé(e)', 'Chrono/Archives'];
        return array_unique(array_merge($additionnelles, $standard));
    }

    /**
     * Prépare les données pour un document.
     */
    public function preparerDonnees(DemandeDocument $demande): array
    {
        $agent = $demande->agent;
        $donnees = $demande->donnees_specifiques ?? [];
        
        $base = [
            'agent'         => $agent,
            'demande'       => $demande,
            'etablissement' => $this->getInfosEtablissement(),
            'directeur'     => $this->getInfosDirecteur(),
            'reference'     => $demande->numero_reference ?? $demande->genererNumeroReference(),
            'date_document' => now(),
        ];

        return match($demande->type_document) {
            'decision_conge_administratif' => array_merge($base, [
                'visas'       => $this->genererVisas(),
                'ampliations' => $this->genererAmpliations(['SAF', $agent->service?->nom_service ?? '']),
                'duree_jours' => $donnees['duree_jours'] ?? 30,
                'periode_reference' => [
                    'debut' => isset($donnees['periode_ref_debut']) ? Carbon::parse($donnees['periode_ref_debut']) : now()->subYear(),
                    'fin'   => isset($donnees['periode_ref_fin']) ? Carbon::parse($donnees['periode_ref_fin']) : now(),
                ],
            ]),

            'attestation_jouissance_conge' => array_merge($base, [
                'ampliations'        => $this->genererAmpliations(['SAF', 'SSI', $agent->service?->nom_service ?? '']),
                'decision_reference' => $donnees['decision_reference'] ?? '',
                'decision_date'      => isset($donnees['decision_date']) ? Carbon::parse($donnees['decision_date']) : now(),
                'duree_totale'       => $donnees['duree_totale'] ?? 30,
                'duree_jouissance'   => $donnees['duree_jouissance'] ?? 15,
                'date_debut'         => isset($donnees['date_debut']) ? Carbon::parse($donnees['date_debut']) : now(),
                'date_reprise'       => isset($donnees['date_reprise']) ? Carbon::parse($donnees['date_reprise']) : now()->addDays(15),
            ]),

            'note_affectation' => array_merge($base, [
                'ampliations'         => $this->genererAmpliations(['SAF', 'SSI', $demande->serviceDestination?->nom_service ?? '']),
                'service_destination' => $demande->serviceDestination,
                'motif_affectation'   => $donnees['motif_affectation'] ?? 'complément d\'effectifs',
            ]),

            'attestation_prime_motivation' => array_merge($base, [
                'ampliations'     => $this->genererAmpliations(),
                'montant'         => $donnees['montant'] ?? 0,
                'montant_lettres' => $this->nombreEnLettres($donnees['montant'] ?? 0),
                'periodicite'     => $donnees['periodicite'] ?? 'mensuelle',
                'type_prime'      => $donnees['type_prime'] ?? 'variable',
            ]),

            'attestation_prise_service' => array_merge($base, [
                'ampliations'        => $this->genererAmpliations(['MSHP/CAB', 'MSHP/SG', 'MSHP/DGES', 'DRH/DGPEC']),
                'note_reference'     => $donnees['note_reference'] ?? '',
                'note_date'          => isset($donnees['note_date']) ? Carbon::parse($donnees['note_date']) : now(),
                'date_prise_service' => isset($donnees['date_prise_service']) ? Carbon::parse($donnees['date_prise_service']) : now(),
                'specialite'         => $donnees['specialite'] ?? '',
            ]),

            'attestation_stage' => array_merge($base, [
                'ampliations'   => $this->genererAmpliations(),
                'date_debut'    => isset($donnees['date_debut']) ? Carbon::parse($donnees['date_debut']) : now()->subMonth(),
                'date_fin'      => isset($donnees['date_fin']) ? Carbon::parse($donnees['date_fin']) : now(),
                'service_stage' => $donnees['service_stage'] ?? $agent->service?->nom_service ?? 'CHNP',
                'appreciation'  => $donnees['appreciation'] ?? 'avec assiduité et dévouement',
            ]),

            'autorisation_sortie_territoire' => array_merge($base, [
                'ampliations' => $this->genererAmpliations(['SAF']),
                'date_debut'  => isset($donnees['date_debut']) ? Carbon::parse($donnees['date_debut']) : now(),
                'date_fin'    => isset($donnees['date_fin']) ? Carbon::parse($donnees['date_fin']) : now()->addMonth(),
                'destination' => $donnees['destination'] ?? '',
                'motif'       => $donnees['motif'] ?? '',
                'organisme'   => $donnees['organisme'] ?? '',
            ]),

            'certificat_travail' => array_merge($base, [
                'ampliations' => $this->genererAmpliations(),
                'date_entree' => $agent->date_prise_service ?? now(),
            ]),

            'attestation_cessation_maternite' => array_merge($base, [
                'ampliations'        => $this->genererAmpliations(['SAF', 'SSI', $agent->service?->nom_service ?? '']),
                'decision_reference' => $donnees['decision_reference'] ?? '',
                'duree_semaines'     => $donnees['duree_semaines'] ?? 14,
                'date_cessation'     => isset($donnees['date_cessation']) ? Carbon::parse($donnees['date_cessation']) : now(),
            ]),

            'note_interim' => array_merge($base, [
                'ampliations'      => $this->genererAmpliations(['Tous services', 'Intéressés']),
                'agent_remplacant' => $demande->agentRemplacant,
                'date_debut'       => isset($donnees['date_debut']) ? Carbon::parse($donnees['date_debut']) : now(),
                'date_fin'         => isset($donnees['date_fin']) ? Carbon::parse($donnees['date_fin']) : now()->addMonth(),
                'motif_absence'    => $donnees['motif_absence'] ?? 'congés administratifs',
            ]),

            'attestation_travail' => array_merge($base, [
                'ampliations' => $this->genererAmpliations(),
            ]),

            'ordre_mission' => array_merge($base, [
                'ampliations'      => $this->genererAmpliations(['SAF', 'Agent Comptable']),
                'date_debut'       => isset($donnees['date_debut']) ? Carbon::parse($donnees['date_debut']) : now(),
                'date_fin'         => isset($donnees['date_fin']) ? Carbon::parse($donnees['date_fin']) : now()->addDays(3),
                'destination'      => $donnees['destination'] ?? '',
                'objet'            => $donnees['objet'] ?? '',
                'moyens_transport' => $donnees['moyens_transport'] ?? 'véhicule de service',
            ]),

            default => $base,
        };
    }

    /**
     * Retourne les champs spécifiques requis pour un type de document.
     */
    public function getChampsSpecifiques(string $typeDocument): array
    {
        return match($typeDocument) {
            'decision_conge_administratif' => [
                'duree_jours'       => ['label' => 'Durée (jours)', 'type' => 'number', 'default' => 30],
                'periode_ref_debut' => ['label' => 'Période de référence - Début', 'type' => 'date'],
                'periode_ref_fin'   => ['label' => 'Période de référence - Fin', 'type' => 'date'],
            ],
            'attestation_jouissance_conge' => [
                'decision_reference' => ['label' => 'Réf. de la décision de congé', 'type' => 'text'],
                'decision_date'      => ['label' => 'Date de la décision', 'type' => 'date'],
                'duree_totale'       => ['label' => 'Durée totale du congé (jours)', 'type' => 'number', 'default' => 30],
                'duree_jouissance'   => ['label' => 'Jours à jouir', 'type' => 'number', 'default' => 15],
                'date_debut'         => ['label' => 'Date de début de jouissance', 'type' => 'date'],
                'date_reprise'       => ['label' => 'Date de reprise', 'type' => 'date'],
            ],
            'note_affectation' => [
                'service_destination_id' => ['label' => 'Service de destination', 'type' => 'select_service'],
                'motif_affectation'      => ['label' => 'Motif', 'type' => 'text', 'default' => 'complément d\'effectifs'],
            ],
            'attestation_prime_motivation' => [
                'montant'     => ['label' => 'Montant (FCFA)', 'type' => 'number'],
                'periodicite' => ['label' => 'Périodicité', 'type' => 'select', 'options' => ['mensuelle', 'trimestrielle', 'annuelle']],
                'type_prime'  => ['label' => 'Type', 'type' => 'select', 'options' => ['fixe', 'variable']],
            ],
            'attestation_prise_service' => [
                'note_reference'     => ['label' => 'Réf. note de service', 'type' => 'text'],
                'note_date'          => ['label' => 'Date note de service', 'type' => 'date'],
                'date_prise_service' => ['label' => 'Date de prise de service', 'type' => 'date'],
                'specialite'         => ['label' => 'Spécialité/DES (si applicable)', 'type' => 'text'],
            ],
            'attestation_stage' => [
                'date_debut'    => ['label' => 'Date de début du stage', 'type' => 'date'],
                'date_fin'      => ['label' => 'Date de fin du stage', 'type' => 'date'],
                'service_stage' => ['label' => 'Service de stage', 'type' => 'text'],
                'appreciation'  => ['label' => 'Appréciation', 'type' => 'text', 'default' => 'avec assiduité et dévouement'],
            ],
            'autorisation_sortie_territoire' => [
                'date_debut'  => ['label' => 'Date de début', 'type' => 'date'],
                'date_fin'    => ['label' => 'Date de fin', 'type' => 'date'],
                'destination' => ['label' => 'Destination (pays/ville)', 'type' => 'text'],
                'motif'       => ['label' => 'Motif du déplacement', 'type' => 'textarea'],
                'organisme'   => ['label' => 'Organisme d\'accueil', 'type' => 'text'],
            ],
            'attestation_cessation_maternite' => [
                'decision_reference' => ['label' => 'Réf. de la décision de congé maternité', 'type' => 'text'],
                'duree_semaines'     => ['label' => 'Durée (semaines)', 'type' => 'number', 'default' => 14],
                'date_cessation'     => ['label' => 'Date de cessation', 'type' => 'date'],
            ],
            'note_interim' => [
                'agent_remplacant_id' => ['label' => 'Agent remplaçant (intérimaire)', 'type' => 'select_agent'],
                'date_debut'          => ['label' => 'Date de début de l\'intérim', 'type' => 'date'],
                'date_fin'            => ['label' => 'Date de fin de l\'intérim', 'type' => 'date'],
                'motif_absence'       => ['label' => 'Motif de l\'absence du titulaire', 'type' => 'text', 'default' => 'congés administratifs'],
            ],
            'ordre_mission' => [
                'date_debut'       => ['label' => 'Date de début', 'type' => 'date'],
                'date_fin'         => ['label' => 'Date de fin', 'type' => 'date'],
                'destination'      => ['label' => 'Destination', 'type' => 'text'],
                'objet'            => ['label' => 'Objet de la mission', 'type' => 'textarea'],
                'moyens_transport' => ['label' => 'Moyens de transport', 'type' => 'text', 'default' => 'véhicule de service'],
            ],
            default => [],
        };
    }

    /**
     * Convertit un nombre en lettres (français).
     */
    public function nombreEnLettres(int $nombre): string
    {
        $unite = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $dizaine = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];
        $dix = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];

        if ($nombre == 0) return 'zéro';
        if ($nombre < 0) return 'moins ' . $this->nombreEnLettres(-$nombre);
        
        $result = '';
        
        if ($nombre >= 1000000) {
            $millions = floor($nombre / 1000000);
            $result .= ($millions == 1 ? 'un million' : $this->nombreEnLettres($millions) . ' millions') . ' ';
            $nombre %= 1000000;
        }
        
        if ($nombre >= 1000) {
            $milliers = floor($nombre / 1000);
            $result .= ($milliers == 1 ? 'mille' : $this->nombreEnLettres($milliers) . ' mille') . ' ';
            $nombre %= 1000;
        }
        
        if ($nombre >= 100) {
            $centaines = floor($nombre / 100);
            $result .= ($centaines == 1 ? 'cent' : $unite[$centaines] . ' cent') . ' ';
            $nombre %= 100;
        }
        
        if ($nombre >= 10) {
            $d = floor($nombre / 10);
            $u = $nombre % 10;
            
            if ($d == 1) {
                $result .= $dix[$u];
            } elseif ($d == 7 || $d == 9) {
                $result .= $dizaine[$d] . '-' . $dix[$u];
            } else {
                $result .= $dizaine[$d];
                if ($u == 1 && $d != 8) {
                    $result .= '-et-un';
                } elseif ($u > 0) {
                    $result .= '-' . $unite[$u];
                } elseif ($d == 8) {
                    $result .= 's';
                }
            }
        } elseif ($nombre > 0) {
            $result .= $unite[$nombre];
        }
        
        return trim($result);
    }
}
