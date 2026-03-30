<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * AuditObserver — Intégrité CID
 * Enregistre automatiquement toutes les créations, modifications
 * et suppressions sur les modèles sensibles via Spatie Activity Log.
 *
 * Ce observer s'enregistre dans AppServiceProvider pour les modèles :
 * Agent, Contrat, Mouvement, Conge, Absence, PriseEnCharge,
 * DemandeDocument, User, Planning, Document
 */
class AuditObserver
{
    /**
     * Modèles dont on log les données OLD/NEW
     * (éviter de loguer des modèles peu sensibles)
     */
    private const MODELES_SENSIBLES = [
        \App\Models\Agent::class,
        \App\Models\Contrat::class,
        \App\Models\Mouvement::class,
        \App\Models\User::class,
    ];

    // ─────────────────────────────────────────────────────
    // ÉVÉNEMENTS ELOQUENT
    // ─────────────────────────────────────────────────────

    public function created(Model $model): void
    {
        $this->log('created', $model, 'Création : ' . class_basename($model));
    }

    public function updated(Model $model): void
    {
        // Ne pas loguer les mises à jour de timestamps uniquement
        if (empty($model->getDirty()) || array_keys($model->getDirty()) === ['updated_at']) {
            return;
        }
        $this->log('updated', $model, 'Modification : ' . class_basename($model));
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, 'Suppression : ' . class_basename($model));
    }

    public function restored(Model $model): void
    {
        $this->log('restored', $model, 'Restauration : ' . class_basename($model));
    }

    // ─────────────────────────────────────────────────────
    // MÉTHODE COMMUNE
    // ─────────────────────────────────────────────────────

    private function log(string $event, Model $model, string $description): void
    {
        try {
            $isSensible = in_array(get_class($model), self::MODELES_SENSIBLES);

            $properties = [
                'model'    => class_basename($model),
                'model_id' => $model->getKey(),
                'ip'       => Request::ip(),
                'event'    => $event,
            ];

            // Pour les modèles sensibles, inclure old/new values (sans données chiffrées)
            if ($isSensible && $event === 'updated') {
                $dirty    = $model->getDirty();
                $excluded = ['adresse', 'telephone', 'cni', 'password', 'remember_token'];

                $properties['changed_fields'] = array_keys(
                    array_diff_key($dirty, array_flip($excluded))
                );

                // Old values (avant modification)
                $oldValues = [];
                foreach ($properties['changed_fields'] as $field) {
                    $oldValues[$field] = $model->getOriginal($field);
                }
                if (!empty($oldValues)) {
                    $properties['old'] = $oldValues;
                }
            }

            activity('audit')
                ->causedBy(Auth::user())
                ->performedOn($model)
                ->withProperties($properties)
                ->event($event)
                ->log($description);

        } catch (\Throwable $e) {
            // Ne jamais bloquer une opération à cause de l'audit
            \Log::error('AuditObserver error: ' . $e->getMessage());
        }
    }

    /**
     * Enregistre un événement d'authentification (login/logout/fail)
     * Appelé directement depuis AuthController ou Listener
     */
    public static function logAuth(string $event, ?object $user, string $description, array $extra = []): void
    {
        try {
            activity('auth')
                ->causedBy($user)
                ->withProperties(array_merge([
                    'ip'         => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'event'      => $event,
                ], $extra))
                ->event($event)
                ->log($description);
        } catch (\Throwable $e) {
            \Log::error('AuditObserver::logAuth error: ' . $e->getMessage());
        }
    }
}
