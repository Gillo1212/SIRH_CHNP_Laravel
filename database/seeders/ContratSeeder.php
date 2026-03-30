<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Contrat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ContratSeeder
 * ─────────────────────────────────────────────────────
 * Crée des contrats réalistes pour tous les agents du CHNP.
 *
 * Scénarios couverts (démo SIRH) :
 *   • Fonctionnaires permanents (PE / PU) — cadres & DRH
 *   • Contractuels hospitaliers (PCH) — personnel RH & admin
 *   • Praticiens universitaires (PU) — médecins spécialistes
 *   • Vacataires — temporaires, certains en fin de contrat
 *   • Internes & Stagiaires — personnel en formation
 *   • Historique : anciens contrats clôturés / expirés
 *   • Alertes : contrats expirant dans < 30 jours
 *
 * Triade CID :
 *   Intégrité — transaction atomique, pas de doublons actifs
 *   Disponibilité — données réalistes pour KPIs & graphiques
 */
class ContratSeeder extends Seeder
{
    public function run(): void
    {
        // Supprimer les contrats existants (idempotent)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Contrat::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $agents = Agent::orderBy('id_agent')->get()->keyBy('matricule');

        // Tout dans une transaction — atomique (Intégrité CID)
        DB::transaction(function () use ($agents) {

            // ─────────────────────────────────────────────────────
            // 1. AMADOU DIOP — Admin Système — Cadre Supérieur
            //    Fonctionnaire permanent → PE sans date fin
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00001')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2010-01-15',
                    'date_fin'       => '2012-12-31',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Contrat initial avant titularisation comme fonctionnaire.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PE',
                    'date_debut'     => '2013-01-01',
                    'date_fin'       => null,
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Titularisation — Poste permanent Administrateur Système.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 2. FATOU SARR — Agent RH — PCH renouvelé
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00002')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2012-06-01',
                    'date_fin'       => '2015-05-31',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Premier contrat PCH — Responsable RH.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2015-06-01',
                    'date_fin'       => '2019-05-31',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Renouvellement PCH — promotion Responsable Principale RH.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2019-06-01',
                    'date_fin'       => Carbon::now()->addMonths(18)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Renouvellement triennal en cours.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 3. MOUSSA NDIAYE — Manager Chef de Pédiatrie
            //    PU (Praticien Universitaire)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00003')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PU',
                    'date_debut'     => '2008-09-15',
                    'date_fin'       => '2013-09-14',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Contrat PU initial — Pédiatre.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PU',
                    'date_debut'     => '2013-09-15',
                    'date_fin'       => null,
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Renouvellement PU — Chef de Service Pédiatrie. Titularisation UCAD.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 4. AÏSSATOU FALL — Infirmière
            //    PCH, contrat expirant dans ~20 jours (⚠️ alerte orange)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00004')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2018-03-01',
                    'date_fin'       => '2021-02-28',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => "Contrat PCH initial — Infirmière Diplômée d'État.",
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2021-03-01',
                    'date_fin'       => Carbon::now()->addDays(20)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Renouvellement PCH triennal — à renouveler avant échéance.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 5. IBRAHIMA DIALLO — DRH
            //    PE permanent (fonctionnaire de l'État)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00005')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PE',
                    'date_debut'     => '2005-04-01',
                    'date_fin'       => '2010-03-31',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Premier mandat PE — Direction Médicale.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PE',
                    'date_debut'     => '2010-04-01',
                    'date_fin'       => null,
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Poste permanent DRH — Décision ministérielle n°2010/DRH/042.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 6. OUSMANE SY — Agent Pédiatrie
            //    Vacataire, expirant dans ~8 jours (🔴 critique)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00006')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'Vacataire',
                    'date_debut'     => '2023-01-02',
                    'date_fin'       => '2024-12-31',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Mission vacataire — Aide-soignant Pédiatrie.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'Vacataire',
                    'date_debut'     => '2025-01-02',
                    'date_fin'       => Carbon::now()->addDays(8)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Renouvellement vacataire annuel — renouvellement urgent requis.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 7. MARIAMA CISSÉ — Maternité
            //    PCH, contrat sain (12 mois restants)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00007')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2020-03-01',
                    'date_fin'       => '2022-02-28',
                    'statut_contrat' => 'Expiré',
                    'observation'    => 'Contrat PCH initial — Sage-femme.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2022-03-01',
                    'date_fin'       => Carbon::now()->addMonths(12)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Renouvellement PCH — Sage-femme diplômée.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 8. IBRAHIMA BA — Urgences
            //    Interne, expirant dans ~45 jours (🟡 alerte)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00008')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'Interne',
                    'date_debut'     => '2024-11-01',
                    'date_fin'       => Carbon::now()->addDays(45)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => "Internat en Médecine d'Urgence — rotation semestrielle.",
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 9. KHADY DIALLO — Chirurgie
            //    CMSAS → PCH (changement de statut contractuel)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00009')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'CMSAS',
                    'date_debut'     => '2021-07-01',
                    'date_fin'       => '2023-06-30',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Convention CMSAS — programme coopération médicale.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2023-07-01',
                    'date_fin'       => Carbon::now()->addMonths(9)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Passage au statut PCH après fin convention CMSAS.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 10. CHEIKH GUEYE — Laboratoire
            //     Stagiaire en cours d'évaluation
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00010')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'Stagiaire',
                    'date_debut'     => Carbon::now()->subMonths(3)->format('Y-m-d'),
                    'date_fin'       => Carbon::now()->addMonths(3)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => "Stage de fin d'études — Technicien de Laboratoire. Convention UCAD.",
                ]);
            }

            // ─────────────────────────────────────────────────────
            // 11. AMINATA SOW — Service RH
            //     PCH récent, contrat sain (2 ans restants)
            // ─────────────────────────────────────────────────────
            if ($a = $agents->get('CHNP-00011')) {
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2020-01-02',
                    'date_fin'       => '2023-01-01',
                    'statut_contrat' => 'Clôturé',
                    'observation'    => 'Contrat PCH initial — Assistante RH.',
                ]);
                Contrat::create([
                    'id_agent'       => $a->id_agent,
                    'type_contrat'   => 'PCH',
                    'date_debut'     => '2023-01-02',
                    'date_fin'       => Carbon::now()->addMonths(22)->format('Y-m-d'),
                    'statut_contrat' => 'Actif',
                    'observation'    => 'Renouvellement PCH triennal — Assistante RH Senior.',
                ]);
            }

            // ─────────────────────────────────────────────────────
            // Agents sans contrat explicite → PCH générique
            // On cible uniquement les agents non traités ci-dessus
            // ─────────────────────────────────────────────────────
            $matriculesTraites = [
                'CHNP-00001','CHNP-00002','CHNP-00003','CHNP-00004','CHNP-00005',
                'CHNP-00006','CHNP-00007','CHNP-00008','CHNP-00009','CHNP-00010','CHNP-00011',
            ];

            Agent::whereNotIn('matricule', $matriculesTraites)
                ->whereDoesntHave('contrats')
                ->each(function (Agent $ag) {
                    $debut = $ag->date_recrutement ?? Carbon::now()->subYears(2);
                    Contrat::create([
                        'id_agent'       => $ag->id_agent,
                        'type_contrat'   => 'PCH',
                        'date_debut'     => $debut instanceof \Carbon\Carbon ? $debut->format('Y-m-d') : $debut,
                        'date_fin'       => Carbon::now()->addYear()->format('Y-m-d'),
                        'statut_contrat' => 'Actif',
                        'observation'    => 'Contrat PCH — généré automatiquement.',
                    ]);
                });

        }); // fin DB::transaction

        $total     = Contrat::count();
        $actifs    = Contrat::where('statut_contrat', 'Actif')->count();
        $alert60   = Contrat::actif()->expirant(60)->count();
        $alert30   = Contrat::actif()->expirant(30)->count();

        $this->command->info("  {$total} contrats insérés — {$actifs} actifs | {$alert30} alertes < 30j | {$alert60} alertes < 60j");
    }
}
