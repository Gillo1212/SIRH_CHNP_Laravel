<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Agent;
use App\Models\HeureSup;
use App\Models\LignePlanning;
use App\Models\Planning;
use App\Models\Service;
use App\Models\User;

/**
 * HeuresSupSeeder
 *
 * Crée un jeu de données réaliste pour le module Heures Supplémentaires :
 *   - 1 planning mensuel validé pour le service Urgences (SAU)
 *   - 8 lignes de planning avec des durées variées (< 8h, = 8h, > 8h)
 *   - 5 déclarations HeureSup (statuts mixtes : En_attente + Validé)
 *   - 3 avis Major sur des demandes de congé existantes
 *
 * Données cibles : service Urgences SAU (id=5), Major Rokhaya MBAYE
 */
class HeuresSupSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('  → HeuresSupSeeder : début');

        // ── 1. Récupérer le contexte ───────────────────────────────────────────
        $service = Service::find(5); // Urgences (SAU)
        if (!$service) {
            $this->command->error('  ✘ Service Urgences (SAU, id=5) introuvable. Abandon.');
            return;
        }

        $agents = Agent::where('id_service', 5)
            ->whereHas('user') // uniquement ceux avec un compte
            ->orderBy('id_agent')
            ->take(8)
            ->get();

        if ($agents->count() < 4) {
            $this->command->error('  ✘ Pas assez d\'agents dans le service. Abandon.');
            return;
        }

        $this->command->info("  ✔ Service : {$service->nom_service} ({$agents->count()} agents cibles)");

        // ── 2. Créer le planning du mois de mars 2026 ────────────────────────
        // Vérifier qu'il n'existe pas déjà un planning pour ce service ce mois
        $planning = Planning::firstOrCreate(
            [
                'id_service'    => 5,
                'periode_debut' => '2026-03-01',
                'periode_fin'   => '2026-03-31',
            ],
            [
                'statut_planning' => 'Validé',
                'date_creation'   => now(),
            ]
        );

        $this->command->info("  ✔ Planning mars 2026 : #{$planning->id_planning} ({$planning->statut_planning})");

        // ── 3. Définir les lignes à créer ─────────────────────────────────────
        // Chaque entrée : [agent_index, typeposte_id, date, heure_debut, heure_fin]
        // Durées variées pour illustrer les différents cas :
        //   Nuit  20h→08h = 12h  →  +4h sup
        //   Garde 08h→20h = 12h  →  +4h sup
        //   Perm  07h→20h = 13h  →  +5h sup
        //   Nuit  22h→07h =  9h  →  +1h sup
        //   Jour  07h→15h =  8h  →   0h sup
        //   Jour  08h→16h =  8h  →   0h sup
        $lignesData = [
            [0, 2, '2026-03-10', '20:00', '08:00'], // Nuit 12h → +4h
            [1, 3, '2026-03-11', '08:00', '20:00'], // Garde 12h → +4h
            [2, 4, '2026-03-12', '07:00', '20:00'], // Permanence 13h → +5h
            [3, 2, '2026-03-15', '22:00', '07:00'], // Nuit 9h → +1h
            [4, 1, '2026-03-17', '07:00', '15:00'], // Jour 8h → pas de sup
            [5, 1, '2026-03-18', '08:00', '16:00'], // Jour 8h → pas de sup
            [6, 3, '2026-03-20', '08:00', '20:00'], // Garde 12h → +4h
            [7, 5, '2026-03-22', '08:00', '20:00'], // Astreinte 12h → +4h
        ];

        $lignes = [];
        foreach ($lignesData as $i => [$agentIdx, $typeposteId, $date, $hDeb, $hFin]) {
            $agent = $agents->get($agentIdx);
            if (!$agent) continue;

            // Eviter les doublons : même agent + même planning + même date
            $ligne = LignePlanning::firstOrCreate(
                [
                    'id_planning'  => $planning->id_planning,
                    'id_agent'     => $agent->id_agent,
                    'date_poste'   => $date,
                ],
                [
                    'id_typeposte' => $typeposteId,
                    'heure_debut'  => $hDeb,
                    'heure_fin'    => $hFin,
                ]
            );
            $lignes[] = $ligne;

            $this->command->line(
                "     Ligne #{$ligne->id_ligne} — {$agent->prenom} {$agent->nom}"
                . " — poste {$typeposteId} — {$date} {$hDeb}→{$hFin}"
            );
        }

        $this->command->info('  ✔ ' . count($lignes) . ' lignes de planning créées/vérifiées');

        // ── 4. Déclarer les heures supplémentaires ────────────────────────────
        // Lignes avec dépassement réel : index 0,1,2,3,6,7 → sup > 0
        // On crée des HS avec statuts variés pour illustrer tout le workflow
        $hsData = [
            // [ligne_index, nb_heures, periode, statut]
            [0, 4.0, 'Trimestre', 'Validé'],     // Nuit +4h — déjà validé par RH
            [1, 4.0, 'Trimestre', 'En_attente'],  // Garde +4h — en attente
            [2, 5.0, 'Semestre',  'En_attente'],  // Permanence +5h — en attente
            [3, 1.0, 'Trimestre', 'En_attente'],  // Nuit +1h — en attente
            [6, 4.0, 'Trimestre', 'Validé'],     // Garde +4h — déjà validé
        ];

        $hsCreees = 0;
        foreach ($hsData as [$ligneIdx, $nbH, $periode, $statut]) {
            $ligne = $lignes[$ligneIdx] ?? null;
            if (!$ligne) continue;

            // Ne pas recréer si déjà existant
            if ($ligne->heureSup()->exists()) {
                $this->command->line("     ⚠ HeureSup déjà existante pour ligne #{$ligne->id_ligne} — ignorée");
                continue;
            }

            HeureSup::create([
                'id_ligne'  => $ligne->id_ligne,
                'nb_heures' => $nbH,
                'taux'      => 1.25,
                'montant'   => 0.00,
                'periode'   => $periode,
                'statut_hs' => $statut,
            ]);
            $hsCreees++;

            $agent = $agents->get($ligneIdx);
            $this->command->line(
                "     HeureSup : {$agent->prenom} {$agent->nom}"
                . " — {$nbH}h — {$periode} — {$statut}"
            );
        }

        $this->command->info("  ✔ {$hsCreees} déclarations d'heures sup créées");

        // ── 5. Avis Major sur des demandes de congé existantes ────────────────
        $demandes = \App\Models\Demande::where('type_demande', 'Conge')
            ->whereIn('id_agent', $agents->pluck('id_agent'))
            ->whereNull('avis_major')
            ->take(3)
            ->get();

        $avisExemples = [
            "Agent disponible, aucun impact sur la permanence du service. Avis favorable.",
            "Période chargée aux urgences — préférable de décaler d'une semaine si possible.",
            "Congé justifié, remplacé par l'agent de garde. Avis favorable.",
        ];

        $avisCount = 0;
        foreach ($demandes as $i => $demande) {
            $demande->update([
                'avis_major'    => $avisExemples[$i] ?? $avisExemples[0],
                'avis_major_at' => now()->subDays(rand(1, 5)),
            ]);
            $avisCount++;
        }

        $this->command->info("  ✔ {$avisCount} avis Major ajoutés sur des demandes de congé");

        // ── 6. Récapitulatif final ─────────────────────────────────────────────
        $this->command->newLine();
        $this->command->table(
            ['Donnée', 'Valeur'],
            [
                ['Planning créé',          "#{$planning->id_planning} — mars 2026 ({$service->nom_service})"],
                ['Lignes de planning',     count($lignes)],
                ['Déclarations HS',        $hsCreees . ' (dont ' . collect($hsData)->where(3, 'En_attente')->count() . ' en attente)'],
                ['Avis Major sur congés',  $avisCount],
                ['Connexion Major',        'rokhaya.mbaye / Password123!'],
                ['Connexion RH',           'rh@chnp.sn / password'],
            ]
        );
    }
}
