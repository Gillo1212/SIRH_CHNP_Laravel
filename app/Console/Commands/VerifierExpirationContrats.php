<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\User;
use App\Notifications\ContratExpirationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * VerifierExpirationContrats — Disponibilité CID
 *
 * Commande artisan quotidienne qui :
 *  1. Détecte les contrats expirés → met à jour leur statut en base (Intégrité CID)
 *  2. Notifie les agents RH des contrats < 60 jours (Disponibilité CID)
 *  3. Journalise les actions dans l'audit trail (Confidentialité CID)
 *
 * Planifiée via routes/console.php : tous les jours à 06h00.
 */
class VerifierExpirationContrats extends Command
{
    protected $signature   = 'sirh:verifier-contrats {--dry-run : Affiche les résultats sans modifier la base}';
    protected $description = 'Vérifie les expirations de contrats, met à jour les statuts et notifie les RH';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('=== Vérification expirations contrats — ' . now()->format('d/m/Y H:i') . ' ===');

        // ----------------------------------------------------------
        // 1. Marquer les contrats expirés (Intégrité : cohérence DB)
        // ----------------------------------------------------------
        $expires = Contrat::with('agent')
            ->where('statut_contrat', 'Actif')
            ->whereNotNull('date_fin')
            ->where('date_fin', '<', now()->startOfDay())
            ->get();

        $nbExpires = $expires->count();

        if ($nbExpires > 0) {
            $this->warn("  → {$nbExpires} contrat(s) expirés détectés.");

            if (!$dryRun) {
                DB::transaction(function () use ($expires) {
                    foreach ($expires as $contrat) {
                        $contrat->update(['statut_contrat' => 'Expiré']);

                        activity('contrat')
                            ->on($contrat)
                            ->withProperties([
                                'agent'    => $contrat->agent?->nom_complet,
                                'date_fin' => $contrat->date_fin?->format('d/m/Y'),
                                'auto'     => true,
                            ])
                            ->log("Contrat #{$contrat->id_contrat} marqué Expiré automatiquement");
                    }
                });
                $this->info("  ✓ {$nbExpires} contrat(s) marqués comme Expiré en base.");
            } else {
                $this->line("  [dry-run] Aucune modification appliquée.");
            }
        } else {
            $this->info("  ✓ Aucun contrat à marquer comme expiré.");
        }

        // ----------------------------------------------------------
        // 2. Notifier les RH des contrats expirants (seuils groupés)
        // ----------------------------------------------------------
        $ids7  = Contrat::actif()->expirant(7)->pluck('id_contrat');
        $ids15 = Contrat::actif()->expirant(15)->pluck('id_contrat');
        $ids30 = Contrat::actif()->expirant(30)->pluck('id_contrat');
        $ids60 = Contrat::actif()->expirant(60)->pluck('id_contrat');

        $seuils = [
            'expired'  => $expires,
            'critical' => Contrat::with('agent')->actif()->expirant(7)->get(),
            'high'     => Contrat::with('agent')->actif()->expirant(15)->whereNotIn('id_contrat', $ids7)->get(),
            'medium'   => Contrat::with('agent')->actif()->expirant(30)->whereNotIn('id_contrat', $ids15)->get(),
            'low'      => Contrat::with('agent')->actif()->expirant(60)->whereNotIn('id_contrat', $ids30)->get(),
        ];

        $rhUsers = User::role(['AgentRH', 'DRH'])->get();

        if ($rhUsers->isEmpty()) {
            $this->warn("  Aucun utilisateur RH/DRH trouvé pour les notifications.");
            return self::SUCCESS;
        }

        $totalNotifications = 0;

        foreach ($seuils as $urgence => $contrats) {
            if ($contrats->isEmpty()) {
                continue;
            }

            $this->line("  Urgence [{$urgence}] : {$contrats->count()} contrat(s)");

            if (!$dryRun) {
                foreach ($contrats as $contrat) {
                    foreach ($rhUsers as $rhUser) {
                        $rhUser->notify(new ContratExpirationNotification($contrat, $urgence));
                        $totalNotifications++;
                    }
                }
            }
        }

        if (!$dryRun && $totalNotifications > 0) {
            $this->info("  ✓ {$totalNotifications} notification(s) envoyées aux agents RH/DRH.");

            activity('contrat')
                ->withProperties([
                    'expires_traites'     => $nbExpires,
                    'notifications_total' => $totalNotifications,
                    'rh_notifies'         => $rhUsers->count(),
                    'execution'           => now()->toDateTimeString(),
                ])
                ->log('Vérification quotidienne contrats exécutée');

        } elseif ($totalNotifications === 0 && !$dryRun) {
            $this->info("  ✓ Aucune notification à envoyer (tous les contrats sont à jour).");
        }

        $this->info('=== Terminé ===');

        return self::SUCCESS;
    }
}
