<?php

namespace App\Providers;

use App\Models\Agent;
use App\Models\Absence;
use App\Models\Contrat;
use App\Models\Document;
use App\Models\Mouvement;
use App\Models\Planning;
use App\Models\Service;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Policies\AgentPolicy;
use App\Policies\ContratPolicy;
use App\Policies\MouvementPolicy;
use App\Policies\ServicePolicy;
use App\Repositories\AgentRepository;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Liaison Repository Interface → Implémentation
     */
    public function register(): void
    {
        $this->app->bind(
            AgentRepositoryInterface::class,
            AgentRepository::class
        );
    }

    public function boot(): void
    {
        // ── Policies (Confidentialité CID) ───────────────────
        Gate::policy(Agent::class,    AgentPolicy::class);
        Gate::policy(Contrat::class,  ContratPolicy::class);
        Gate::policy(Mouvement::class,MouvementPolicy::class);
        Gate::policy(Service::class,  ServicePolicy::class);

        // ── Gates granulaires (Confidentialité CID) ──────────
        $this->registerGates();

        // ── Observers Eloquent (Intégrité CID) ───────────────
        $this->registerObservers();
    }

    // ──────────────────────────────────────────────────────
    // GATES
    // ──────────────────────────────────────────────────────

    private function registerGates(): void
    {
        // AdminSystème bypass toutes les vérifications (sauf permissions critique)
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('AdminSystème')) {
                return true;
            }
            return null;
        });

        // ── Agents ──────────────────────────────────────────
        Gate::define('agents.view-service', function (User $user) {
            return $user->hasPermissionTo('agents.view-service');
        });

        Gate::define('agents.view-own', function (User $user, Agent $agent) {
            return $user->agent?->id_agent === $agent->id_agent
                || $user->hasPermissionTo('agents.view-all');
        });

        // ── Congés ──────────────────────────────────────────
        Gate::define('conges.validate', function (User $user) {
            return $user->hasPermissionTo('conges.validate');
        });

        Gate::define('conges.approve', function (User $user) {
            return $user->hasPermissionTo('conges.approve');
        });

        // ── Mouvements ──────────────────────────────────────
        Gate::define('mouvements.validate', function (User $user) {
            return $user->hasAnyRole(['DRH', 'AdminSystème']);
        });

        // ── Dashboard ────────────────────────────────────────
        Gate::define('dashboard.view-global', function (User $user) {
            return $user->hasPermissionTo('dashboard.view-global');
        });

        Gate::define('dashboard.view-service', function (User $user) {
            return $user->hasPermissionTo('dashboard.view-service');
        });

        // ── Administration ───────────────────────────────────
        Gate::define('admin.access', function (User $user) {
            return $user->hasRole('AdminSystème');
        });

        Gate::define('audit.view', function (User $user) {
            return $user->hasPermissionTo('audit.view');
        });

        Gate::define('settings.update', function (User $user) {
            return $user->hasPermissionTo('settings.update');
        });

        Gate::define('backups.create', function (User $user) {
            return $user->hasPermissionTo('backups.create');
        });

        // ── Données sensibles (déchiffrement) ────────────────
        Gate::define('voir-donnees-sensibles', function (User $user) {
            return $user->hasAnyRole(['AgentRH', 'DRH', 'AdminSystème']);
        });

        // ── Documents / GED ─────────────────────────────────
        Gate::define('documents.manage', function (User $user) {
            return $user->hasPermissionTo('documents.create');
        });

        // ── Rapports ─────────────────────────────────────────
        Gate::define('reports.export', function (User $user) {
            return $user->hasAnyPermission(['reports.export-pdf', 'reports.export-excel']);
        });
    }

    // ──────────────────────────────────────────────────────
    // OBSERVERS (Intégrité CID — audit automatique)
    // ──────────────────────────────────────────────────────

    private function registerObservers(): void
    {
        $observer = AuditObserver::class;

        // Modèles avec suivi complet old/new values
        Agent::observe($observer);
        Contrat::observe($observer);
        Mouvement::observe($observer);
        User::observe($observer);

        // Modèles avec suivi d'existence (créé/supprimé uniquement)
        // (on vérifie l'existence des classes avant observe)
        if (class_exists(Absence::class)) {
            Absence::observe($observer);
        }
        if (class_exists(Document::class)) {
            Document::observe($observer);
        }
        if (class_exists(Planning::class)) {
            Planning::observe($observer);
        }
    }
}
