<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Setting — Paramètres système persistants
 * Disponibilité CID : configuration accessible sans modifier .env
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    protected function casts(): array
    {
        return [];
    }

    // ──────────────────────────────────────────────────────
    // VALEURS PAR DÉFAUT
    // ──────────────────────────────────────────────────────

    public const DEFAULTS = [
        // Groupe : app
        'app.nom'           => ['value' => 'SIRH CHNP',                         'type' => 'string',  'group' => 'app',           'label' => 'Nom du système'],
        'app.nom_hopital'   => ['value' => 'Centre Hospitalier National de Pikine','type' => 'string','group' => 'app',            'label' => 'Nom de l\'établissement'],
        'app.email_contact' => ['value' => 'sirh@chnp.sn',                       'type' => 'string',  'group' => 'app',           'label' => 'Email de contact'],
        'app.timezone'      => ['value' => 'Africa/Dakar',                        'type' => 'string',  'group' => 'app',           'label' => 'Fuseau horaire'],
        'app.locale'        => ['value' => 'fr',                                  'type' => 'string',  'group' => 'app',           'label' => 'Langue par défaut'],

        // Groupe : security
        'security.session_lifetime'   => ['value' => '120',   'type' => 'integer', 'group' => 'security', 'label' => 'Durée session (minutes)'],
        'security.max_login_attempts' => ['value' => '5',     'type' => 'integer', 'group' => 'security', 'label' => 'Tentatives connexion max'],
        'security.lockout_duration'   => ['value' => '30',    'type' => 'integer', 'group' => 'security', 'label' => 'Durée blocage (minutes)'],
        'security.password_min_length'=> ['value' => '8',     'type' => 'integer', 'group' => 'security', 'label' => 'Longueur minimale MDP'],
        'security.password_requires_uppercase' => ['value' => '1', 'type' => 'boolean', 'group' => 'security', 'label' => 'MDP : majuscule requise'],
        'security.password_requires_number'    => ['value' => '1', 'type' => 'boolean', 'group' => 'security', 'label' => 'MDP : chiffre requis'],
        'security.two_factor_enabled' => ['value' => '0',     'type' => 'boolean', 'group' => 'security', 'label' => 'Double authentification'],

        // Groupe : notifications
        'notifications.conge_demande'      => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Notifier demande congé'],
        'notifications.conge_valide'       => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Notifier validation congé'],
        'notifications.conge_rejete'       => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Notifier rejet congé'],
        'notifications.contrat_expiration' => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Alertes expiration contrat'],
        'notifications.document_pret'      => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Notifier document prêt'],
        'notifications.pec_traitement'     => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Notifier traitement PEC'],
        'notifications.mouvement_valide'   => ['value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'label' => 'Notifier validation mouvement'],

        // Groupe : backup
        'backup.auto_enabled'    => ['value' => '1',   'type' => 'boolean', 'group' => 'backup', 'label' => 'Sauvegarde automatique'],
        'backup.frequency'       => ['value' => 'daily','type' => 'string',  'group' => 'backup', 'label' => 'Fréquence (daily/weekly)'],
        'backup.retention_days'  => ['value' => '30',   'type' => 'integer', 'group' => 'backup', 'label' => 'Rétention (jours)'],
        'backup.time'            => ['value' => '02:00','type' => 'string',  'group' => 'backup', 'label' => 'Heure de sauvegarde'],
    ];

    // ──────────────────────────────────────────────────────
    // MÉTHODES STATIQUES
    // ──────────────────────────────────────────────────────

    /**
     * Lire un paramètre (avec valeur par défaut si absent)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default ?? (static::DEFAULTS[$key]['value'] ?? null);
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int)  $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    /**
     * Écrire/créer un paramètre
     */
    public static function set(string $key, mixed $value): static
    {
        $defaults = static::DEFAULTS[$key] ?? [];

        $setting = static::updateOrCreate(
            ['key' => $key],
            array_merge($defaults, [
                'key'   => $key,
                'value' => is_array($value) ? json_encode($value) : (string) $value,
            ])
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Tous les paramètres d'un groupe
     */
    public static function group(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get()->keyBy('key');
    }

    /**
     * Initialiser les valeurs par défaut (seeder)
     */
    public static function seedDefaults(): void
    {
        foreach (static::DEFAULTS as $key => $attrs) {
            static::firstOrCreate(
                ['key' => $key],
                array_merge($attrs, ['key' => $key])
            );
        }
    }

    // ──────────────────────────────────────────────────────
    // ACCESSORS
    // ──────────────────────────────────────────────────────

    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int)  $this->value,
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }
}
