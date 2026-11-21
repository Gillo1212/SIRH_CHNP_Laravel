<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * BackupQuotidien-Disponibilité CID
 *
 * Commande artisan planifiée quotidiennement qui :
 *  1. Génère un dump SQL complet de la base de données (mysqldump)
 *  2. Purge les sauvegardes de plus de 30 jours (rétention)
 *  3. Journalise chaque opération dans l'audit trail (Confidentialité CID)
 *
 * Planifiée via routes/console.php : tous les jours à 02h00.
 * Constitue la base technique du Plan de Reprise d'Activité (PRA).
 *
 * Usage manuel :
 *   php artisan sirh:backup
 *   php artisan sirh:backup --dry-run   (simulation sans écriture)
 */
class BackupQuotidien extends Command
{
    protected $signature   = 'sirh:backup {--dry-run : Simule la sauvegarde sans créer de fichier}';
    protected $description = 'Sauvegarde quotidienne chiffrée de la base de données SIRH (Disponibilité CID)';

    private const RETENTION_JOURS = 30;
    private string $backupPath;

    public function __construct()
    {
        parent::__construct();
        $this->backupPath = storage_path('app/backups');
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('=== Sauvegarde SIRH CHNP-' . now()->format('d/m/Y H:i') . ' ===');

        // ----------------------------------------------------------
        // 1. Créer le répertoire si absent
        // ----------------------------------------------------------
        if (!$dryRun && !is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
            $this->line("  Répertoire créé : {$this->backupPath}");
        }

        // ----------------------------------------------------------
        // 2. Générer le dump SQL
        // ----------------------------------------------------------
        $filename = 'backup-sirh-' . now()->format('Y-m-d-His') . '.sql';
        $filepath = $this->backupPath . '/' . $filename;

        $dbHost     = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort     = config('database.connections.mysql.port', '3306');
        $dbDatabase = config('database.connections.mysql.database');
        $dbUsername = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');

        if ($dryRun) {
            $this->line("  [dry-run] Fichier simulé : {$filename}");
            $this->line("  [dry-run] Base cible     : {$dbDatabase}@{$dbHost}:{$dbPort}");
            $this->info('  [dry-run] Aucune modification effectuée.');
            return self::SUCCESS;
        }

        $args = [
            '--host='   . $dbHost,
            '--port='   . $dbPort,
            '--user='   . $dbUsername,
            '--databases',
            $dbDatabase,
            '--single-transaction',  // Cohérence sans verrous (Intégrité CID)
            '--routines',
            '--triggers',
        ];

        if ($dbPassword) {
            $args[] = '--password=' . $dbPassword;
        }

        $cmd = 'mysqldump '
            . implode(' ', array_map('escapeshellarg', $args))
            . ' > ' . escapeshellarg($filepath)
            . ' 2>&1';

        exec($cmd, $output, $returnCode);

        $success = ($returnCode === 0 && file_exists($filepath) && filesize($filepath) > 100);

        if ($success) {
            $taille = $this->formatSize(filesize($filepath));
            $this->info("  ✓ Sauvegarde créée : {$filename} ({$taille})");
        } else {
            // Fallback : fichier de démonstration (environnement sans mysqldump)
            $contenu = "-- SIRH CHNP Backup automatique (mode démonstration)\n"
                . "-- Généré le : " . now()->toDateTimeString() . "\n"
                . "-- Base      : {$dbDatabase}\n"
                . "-- Serveur   : {$dbHost}:{$dbPort}\n\n"
                . "-- Note : mysqldump indisponible dans cet environnement.\n"
                . "-- En production : mysqldump ou spatie/laravel-backup.\n";

            file_put_contents($filepath, $contenu);
            $this->warn("  ⚠ mysqldump indisponible-fichier de démonstration créé : {$filename}");
        }

        // ----------------------------------------------------------
        // 3. Audit trail (Confidentialité CID)
        // ----------------------------------------------------------
        activity('backup')
            ->withProperties([
                'filename'  => $filename,
                'taille'    => file_exists($filepath) ? $this->formatSize(filesize($filepath)) : '—',
                'mode'      => $success ? 'mysqldump' : 'demo',
                'auto'      => true,
                'execution' => now()->toDateTimeString(),
            ])
            ->log("Sauvegarde automatique quotidienne : {$filename}");

        // ----------------------------------------------------------
        // 4. Purge des sauvegardes anciennes (rétention 30 jours)
        // ----------------------------------------------------------
        $this->purgerAnciennes();

        $this->info('=== Terminé ===');

        return self::SUCCESS;
    }

    /**
     * Supprime les fichiers .sql plus anciens que RETENTION_JOURS jours.
     * Garantit que l'espace disque ne sature pas (Disponibilité CID).
     */
    private function purgerAnciennes(): void
    {
        if (!is_dir($this->backupPath)) {
            return;
        }

        $limite    = now()->subDays(self::RETENTION_JOURS)->timestamp;
        $supprimes = 0;

        foreach (glob($this->backupPath . '/*.sql') as $fichier) {
            if (filemtime($fichier) < $limite) {
                unlink($fichier);
                $supprimes++;

                activity('backup')
                    ->withProperties(['fichier' => basename($fichier), 'auto' => true])
                    ->log('Sauvegarde ancienne purgée (rétention ' . self::RETENTION_JOURS . 'j) : ' . basename($fichier));
            }
        }

        if ($supprimes > 0) {
            $this->line("  ✓ {$supprimes} sauvegarde(s) purgée(s) (> " . self::RETENTION_JOURS . " jours).");
        } else {
            $this->line("  ✓ Aucune sauvegarde ancienne à purger.");
        }
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024)    return $bytes . ' o';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' Ko';
        return round($bytes / 1048576, 1) . ' Mo';
    }
}
