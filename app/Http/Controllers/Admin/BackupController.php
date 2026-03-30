<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BackupController — Sauvegardes BDD (Disponibilité CID)
 * Création, liste, téléchargement et suppression des backups.
 */
class BackupController extends Controller
{
    private string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
    }

    // ─────────────────────────────────────────────────────
    // LISTE
    // ─────────────────────────────────────────────────────

    public function index()
    {
        $backups = [];

        if (is_dir($this->backupPath)) {
            foreach (glob($this->backupPath . '/*.sql') as $file) {
                $backups[] = [
                    'name'    => basename($file),
                    'size'    => $this->formatSize(filesize($file)),
                    'size_raw'=> filesize($file),
                    'date'    => date('d/m/Y H:i', filemtime($file)),
                    'ts'      => filemtime($file),
                    'path'    => $file,
                ];
            }
            usort($backups, fn($a, $b) => $b['ts'] - $a['ts']);
        }

        $stats = [
            'count'       => count($backups),
            'total_size'  => $this->formatSize(array_sum(array_column($backups, 'size_raw'))),
            'derniere'    => !empty($backups) ? $backups[0]['date'] : '—',
            'disk_free'   => $this->diskFree(),
        ];

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    // ─────────────────────────────────────────────────────
    // CRÉER UNE SAUVEGARDE
    // ─────────────────────────────────────────────────────

    public function create()
    {
        $this->authorize('backups.create');

        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }

        $filename = 'backup-sirh-' . now()->format('Y-m-d-His') . '.sql';
        $filepath = $this->backupPath . '/' . $filename;

        $dbHost     = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort     = config('database.connections.mysql.port', '3306');
        $dbDatabase = config('database.connections.mysql.database');
        $dbUsername = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');

        // Construire la commande mysqldump de façon sécurisée
        $args = [
            '--host='     . $dbHost,
            '--port='     . $dbPort,
            '--user='     . $dbUsername,
            '--databases',
            $dbDatabase,
            '--single-transaction',
            '--routines',
            '--triggers',
        ];

        $cmd = 'mysqldump';
        if ($dbPassword) {
            $args[] = '--password=' . $dbPassword;
        }

        $fullCommand = $cmd . ' ' . implode(' ', array_map('escapeshellarg', $args))
            . ' > ' . escapeshellarg($filepath) . ' 2>&1';

        exec($fullCommand, $output, $returnCode);

        // Audit (Intégrité CID)
        activity('backup')
            ->causedBy(Auth::user())
            ->withProperties(['filename' => $filename, 'ip' => request()->ip()])
            ->log('Sauvegarde manuelle créée : ' . $filename);

        if ($returnCode === 0 && file_exists($filepath) && filesize($filepath) > 100) {
            return back()->with('success', "Sauvegarde créée : {$filename} (" . $this->formatSize(filesize($filepath)) . ')');
        }

        // Fallback démonstration (mysqldump non disponible ou pas de BDD)
        $demoContent = "-- SIRH CHNP Backup (mode démonstration)\n"
            . "-- Généré le : " . now()->toDateTimeString() . "\n"
            . "-- Base : {$dbDatabase}\n"
            . "-- Serveur : {$dbHost}:{$dbPort}\n\n"
            . "-- Note: mysqldump n'est pas disponible dans cet environnement.\n"
            . "-- En production, utilisez mysqldump ou spatie/laravel-backup.\n";

        file_put_contents($filepath, $demoContent);

        return back()->with('info', "Sauvegarde simulée créée : {$filename} (mode démonstration)");
    }

    // ─────────────────────────────────────────────────────
    // TÉLÉCHARGER
    // ─────────────────────────────────────────────────────

    public function download(string $filename)
    {
        $this->authorize('backups.download');

        // Sécurisation : interdire les traversées de répertoire
        $filename = basename($filename);
        $filepath = $this->backupPath . '/' . $filename;

        if (!file_exists($filepath) || !str_ends_with($filename, '.sql')) {
            abort(404, 'Sauvegarde introuvable.');
        }

        // Audit
        activity('backup')
            ->causedBy(Auth::user())
            ->withProperties(['filename' => $filename, 'ip' => request()->ip()])
            ->log('Téléchargement sauvegarde : ' . $filename);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    // ─────────────────────────────────────────────────────
    // SUPPRIMER
    // ─────────────────────────────────────────────────────

    public function delete(string $filename)
    {
        $this->authorize('backups.delete');

        $filename = basename($filename);
        $filepath = $this->backupPath . '/' . $filename;

        if (!file_exists($filepath) || !str_ends_with($filename, '.sql')) {
            return back()->with('error', 'Sauvegarde introuvable.');
        }

        unlink($filepath);

        // Audit
        activity('backup')
            ->causedBy(Auth::user())
            ->withProperties(['filename' => $filename, 'ip' => request()->ip()])
            ->log('Suppression sauvegarde : ' . $filename);

        return back()->with('success', "Sauvegarde {$filename} supprimée.");
    }

    // ─────────────────────────────────────────────────────
    // PRIVÉ
    // ─────────────────────────────────────────────────────

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024)    return $bytes . ' o';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' Ko';
        return round($bytes / 1048576, 1) . ' Mo';
    }

    private function diskFree(): string
    {
        try {
            $free = disk_free_space($this->backupPath ?: base_path());
            return $free !== false ? $this->formatSize((int)$free) : '—';
        } catch (\Throwable) {
            return '—';
        }
    }
}
