<?php

namespace App\Providers;

use App\Models\Agent;
use App\Policies\AgentPolicy;
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
        // Enregistrement de la Policy (Confidentialité CID)
        Gate::policy(Agent::class, AgentPolicy::class);
    }
}
