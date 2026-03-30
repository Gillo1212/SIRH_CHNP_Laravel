<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SettingsController — Paramètres système (Disponibilité CID)
 * Gère la configuration persistante via la table settings.
 */
class SettingsController extends Controller
{
    // ─────────────────────────────────────────────────────
    // AFFICHAGE
    // ─────────────────────────────────────────────────────

    public function index()
    {
        // Initialiser les valeurs par défaut si la table est vide
        Setting::seedDefaults();

        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_driver'       => strtoupper(config('database.default')),
            'env'             => config('app.env'),
            'timezone'        => config('app.timezone'),
            'locale'          => config('app.locale'),
            'agents_total'    => Agent::count(),
            'users_total'     => User::count(),
            'storage_logs'    => $this->dirSize(storage_path('logs')),
            'storage_uploads' => $this->dirSize(storage_path('app/public')),
            'storage_backups' => $this->dirSize(storage_path('app/backups')),
        ];

        // Récupérer les paramètres par groupe
        $settingsApp      = Setting::group('app');
        $settingsSecurity = Setting::group('security');
        $settingsNotif    = Setting::group('notifications');
        $settingsBackup   = Setting::group('backup');

        return view('admin.settings.index', compact(
            'info', 'settingsApp', 'settingsSecurity', 'settingsNotif', 'settingsBackup'
        ));
    }

    // ─────────────────────────────────────────────────────
    // MISE À JOUR
    // ─────────────────────────────────────────────────────

    public function update(Request $request)
    {
        $this->authorize('settings.update');

        $group = $request->input('group', 'app');

        // Valider selon le groupe
        $rules = $this->rulesForGroup($group);
        $validated = $request->validate($rules);

        // Sauvegarder chaque setting du groupe
        foreach ($validated as $key => $value) {
            // Reconstruire la clé complète (group.key)
            $fullKey = $group . '.' . $key;
            Setting::set($fullKey, $value ?? '0');
        }

        // Audit trail (Intégrité CID)
        activity('settings')
            ->causedBy(Auth::user())
            ->withProperties([
                'group'   => $group,
                'changes' => array_keys($validated),
                'ip'      => $request->ip(),
            ])
            ->log("Paramètres modifiés (groupe : {$group})");

        return back()->with('success', "Paramètres '{$group}' mis à jour avec succès.");
    }

    // ─────────────────────────────────────────────────────
    // NOTIFICATIONS
    // ─────────────────────────────────────────────────────

    public function notifications()
    {
        $this->authorize('settings.notifications');
        Setting::seedDefaults();
        $settingsNotif = Setting::group('notifications');
        return view('admin.settings.notifications', compact('settingsNotif'));
    }

    // ─────────────────────────────────────────────────────
    // PRIVÉ
    // ─────────────────────────────────────────────────────

    private function rulesForGroup(string $group): array
    {
        return match ($group) {
            'app' => [
                'nom'           => 'nullable|string|max:200',
                'nom_hopital'   => 'nullable|string|max:300',
                'email_contact' => 'nullable|email|max:200',
                'timezone'      => 'nullable|string|max:50',
                'locale'        => 'nullable|in:fr,en',
            ],
            'security' => [
                'session_lifetime'            => 'nullable|integer|min:5|max:1440',
                'max_login_attempts'          => 'nullable|integer|min:1|max:20',
                'lockout_duration'            => 'nullable|integer|min:5|max:1440',
                'password_min_length'         => 'nullable|integer|min:6|max:32',
                'password_requires_uppercase' => 'nullable|in:0,1',
                'password_requires_number'    => 'nullable|in:0,1',
                'two_factor_enabled'          => 'nullable|in:0,1',
            ],
            'notifications' => [
                'conge_demande'      => 'nullable|in:0,1',
                'conge_valide'       => 'nullable|in:0,1',
                'conge_rejete'       => 'nullable|in:0,1',
                'contrat_expiration' => 'nullable|in:0,1',
                'document_pret'      => 'nullable|in:0,1',
                'pec_traitement'     => 'nullable|in:0,1',
                'mouvement_valide'   => 'nullable|in:0,1',
            ],
            'backup' => [
                'auto_enabled'   => 'nullable|in:0,1',
                'frequency'      => 'nullable|in:daily,weekly,monthly',
                'retention_days' => 'nullable|integer|min:1|max:365',
                'time'           => 'nullable|date_format:H:i',
            ],
            default => [],
        };
    }

    private function dirSize(string $path): string
    {
        if (!is_dir($path)) return '0 Ko';
        $size = 0;
        try {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
                if ($file->isFile()) $size += $file->getSize();
            }
        } catch (\Throwable) {
            return '— Ko';
        }
        if ($size < 1024)    return $size . ' o';
        if ($size < 1048576) return round($size / 1024, 1) . ' Ko';
        return round($size / 1048576, 1) . ' Mo';
    }
}
