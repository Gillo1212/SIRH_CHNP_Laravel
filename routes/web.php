<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserAccountController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\RH\RHDashboardController;
use App\Http\Controllers\RH\MouvementController;
use App\Http\Controllers\RH\DocumentAdminController;
use App\Http\Controllers\RH\PriseEnChargeController;
use App\Http\Controllers\RH\AgentRHController;
use App\Http\Controllers\RH\ContratController;
use App\Http\Controllers\RH\CongeRHController;
use App\Http\Controllers\RH\AbsenceRHController;
use App\Http\Controllers\RH\PlanningRHController;
use App\Http\Controllers\RH\GEDController;
use App\Http\Controllers\RH\ServiceController;
use App\Http\Controllers\RH\DivisionController;
use App\Http\Controllers\RH\DemandeDocController;
use App\Http\Controllers\RH\RapportRHController;
use App\Http\Controllers\DRH\DRHDashboardController;
use App\Http\Controllers\DRH\DecisionController;
use App\Http\Controllers\DRH\RapportDRHController;
use App\Http\Controllers\DRH\IndicateurController;
use App\Http\Controllers\DRH\ValidationDRHController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\EquipeController;
use App\Http\Controllers\Manager\CongeManagerController;
use App\Http\Controllers\Manager\AbsenceManagerController;
use App\Http\Controllers\Manager\PlanningManagerController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\ProfilController;
use App\Http\Controllers\Agent\CongeAgentController;
use App\Http\Controllers\Agent\PECAgentController;
use App\Http\Controllers\Agent\DocAdminAgentController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\AideController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Helpers stub view
|--------------------------------------------------------------------------
*/
if (!function_exists('wip')) {
    function wip(string $title) {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }
}

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::view('/privacy', 'pages.privacy')->name('privacy');


/*
|--------------------------------------------------------------------------
| Routes Authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.account.locked'])->group(function () {

    // Dashboard générique (redirige selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');

    /*
    |--------------------------------------------------------------------------
    | Routes ADMIN SYSTÈME
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:AdminSystème'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Utilisateurs — workflow Agent existant → compte
        Route::get('/users/agents-sans-compte', [AdminUserController::class, 'agentsSansCompte'])->name('users.agents-sans-compte');
        Route::get('/users/create-for-agent/{agent}', [AdminUserController::class, 'createForAgent'])->name('users.create-for-agent');
        Route::post('/users/store-for-agent/{agent}', [AdminUserController::class, 'storeForAgent'])->name('users.store-for-agent');

        // Comptes utilisateurs — workflow Admin-first
        Route::get('/accounts', [UserAccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/agents-sans-compte', [UserAccountController::class, 'agentsSansCompte'])->name('accounts.agents-sans-compte');
        Route::post('/accounts', [UserAccountController::class, 'store'])->name('accounts.store');
        Route::get('/accounts/{account}', [UserAccountController::class, 'show'])->name('accounts.show');
        Route::get('/accounts/{account}/data', [UserAccountController::class, 'getData'])->name('accounts.data');
        Route::put('/accounts/{account}', [UserAccountController::class, 'update'])->name('accounts.update');
        Route::post('/accounts/{account}/reset-password', [UserAccountController::class, 'resetPassword'])->name('accounts.reset-password');
        Route::post('/accounts/{account}/toggle-verrouillage', [UserAccountController::class, 'toggleVerrouillage'])->name('accounts.toggle-verrouillage');
        Route::post('/accounts/{account}/resend-rh', [UserAccountController::class, 'resendRHNotification'])->name('accounts.resend-rh');

        // Rôles & Permissions
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/matrix', [PermissionController::class, 'matrix'])->name('permissions.matrix');
        Route::post('/permissions/matrix', [PermissionController::class, 'updateMatrix'])->name('permissions.matrix.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Audit
        Route::get('/audit', fn() => wip('Journal d\'audit complet'))->name('audit.index');
        Route::get('/audit/connexions', fn() => wip('Historique des connexions'))->name('audit.connexions');
        Route::get('/audit/echecs', fn() => wip('Tentatives de connexion échouées'))->name('audit.echecs');
        Route::get('/audit/export', fn() => wip('Export du journal d\'audit'))->name('audit.export');

        // Paramètres
        Route::get('/settings', fn() => wip('Paramètres système'))->name('settings.index');
        Route::get('/settings/notifications', fn() => wip('Configuration des notifications'))->name('settings.notifications');

        // Sauvegardes
        Route::get('/backups', fn() => wip('Gestion des sauvegardes'))->name('backups.index');
        Route::post('/backups/create', fn() => wip('Backup manuel'))->name('backups.create');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes DRH (EXCLUSIVES)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:DRH'])->prefix('drh')->name('drh.')->group(function () {

        Route::get('/dashboard', [DRHDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kpis', [DRHDashboardController::class, 'kpis'])->name('kpis');
        Route::get('/budget', [DRHDashboardController::class, 'budget'])->name('budget');
        Route::get('/organigramme', fn() => wip('Organigramme CHNP'))->name('organigramme');

        // Indicateurs RH
        Route::prefix('indicateurs')->name('indicateurs.')->group(function () {
            Route::get('/effectifs', [IndicateurController::class, 'effectifs'])->name('effectifs');
            Route::get('/turnover', [IndicateurController::class, 'turnover'])->name('turnover');
            Route::get('/absenteisme', [IndicateurController::class, 'absenteisme'])->name('absenteisme');
            Route::get('/pyramide-ages', [IndicateurController::class, 'pyramideAges'])->name('pyramide-ages');
        });

        // Validations DRH
        Route::prefix('validations')->name('validations.')->group(function () {
            Route::get('/decisions', [ValidationDRHController::class, 'decisions'])->name('decisions');
            Route::post('/decisions/{id}/signer', [ValidationDRHController::class, 'signer'])->name('signer');
            Route::get('/mouvements', [ValidationDRHController::class, 'mouvements'])->name('mouvements');
            Route::post('/mouvements/{id}/valider', [ValidationDRHController::class, 'validerMouvement'])->name('valider-mouvement');
            Route::get('/pec-exceptionnelles', [ValidationDRHController::class, 'pecExceptionnelles'])->name('pec');
            Route::post('/pec/{id}/valider', [ValidationDRHController::class, 'validerPEC'])->name('valider-pec');
        });

        // Décisions (alias)
        Route::get('/decisions', [DecisionController::class, 'index'])->name('decisions.index');
        Route::post('/decisions/{id}/signer', [DecisionController::class, 'signer'])->name('decisions.signer');

        // Rapports direction
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/bilan-social', [RapportDRHController::class, 'bilanSocial'])->name('bilan');
            Route::get('/effectifs', fn() => wip('Rapport Effectifs'))->name('effectifs');
            Route::get('/previsions-departs', fn() => wip('Prévisions des départs'))->name('previsions');
            Route::get('/export-consolide', fn() => wip('Export consolidé'))->name('export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Routes PARTAGÉES AgentRH + DRH
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:AgentRH|DRH'])->prefix('rh')->name('rh.')->group(function () {

        Route::get('/dashboard', [RHDashboardController::class, 'index'])->name('dashboard');

        // Personnel
        Route::prefix('agents')->name('agents.')->group(function () {
            Route::get('/', [AgentRHController::class, 'index'])->name('index');
            Route::get('/comptes-a-completer', [AgentRHController::class, 'comptesACompleter'])->name('comptes-a-completer');
            Route::get('/create', [AgentRHController::class, 'create'])->name('create');
            Route::post('/', [AgentRHController::class, 'store'])->name('store');
            Route::get('/completer/{userId}', [AgentRHController::class, 'completerDossierForm'])->name('completer');
            Route::get('/{id}', [AgentRHController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AgentRHController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AgentRHController::class, 'update'])->name('update');
            Route::delete('/{id}', [AgentRHController::class, 'destroy'])->name('destroy');
            Route::get('/export/csv', [AgentRHController::class, 'export'])->name('export.csv');
        });

        // Contrats
        Route::prefix('contrats')->name('contrats.')->group(function () {
            Route::get('/', [ContratController::class, 'index'])->name('index');
            Route::get('/create', [ContratController::class, 'create'])->name('create');
            Route::post('/', [ContratController::class, 'store'])->name('store');
            Route::get('/expiring', [ContratController::class, 'expiring'])->name('expiring');
            Route::get('/{id}', [ContratController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ContratController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ContratController::class, 'update'])->name('update');
        });

        // Mouvements
        Route::prefix('mouvements')->name('mouvements.')->group(function () {
            Route::get('/', [MouvementController::class, 'index'])->name('index');
            Route::get('/affectations', [MouvementController::class, 'affectations'])->name('affectations');
            Route::get('/mutations', [MouvementController::class, 'mutations'])->name('mutations');
            Route::get('/retours', fn() => wip('Retours / Réintégrations'))->name('retours');
            Route::get('/departs', [MouvementController::class, 'departs'])->name('departs');
            Route::get('/create', fn() => wip('Nouveau mouvement'))->name('create');
            Route::post('/store', [MouvementController::class, 'store'])->name('store');
            Route::get('/{id}', fn($id) => wip('Détail mouvement'))->name('show');
        });

        // Congés
        Route::prefix('conges')->name('conges.')->group(function () {
            Route::get('/', [CongeRHController::class, 'index'])->name('index');
            Route::get('/pending', [CongeRHController::class, 'pending'])->name('pending');
            Route::get('/soldes', [CongeRHController::class, 'soldes'])->name('soldes');
            Route::post('/soldes/init', [CongeRHController::class, 'initSoldes'])->name('soldes.init');
            Route::get('/{id}', [CongeRHController::class, 'show'])->name('show');
            Route::post('/{id}/approuver', [CongeRHController::class, 'approuver'])->name('approuver');
            Route::post('/{id}/rejeter', [CongeRHController::class, 'rejeter'])->name('rejeter');
        });
        Route::get('/conge-physique', [CongeRHController::class, 'saisiePhysique'])->name('conge-physique');
        Route::post('/conge-physique', [CongeRHController::class, 'storeSaisiePhysique'])->name('conge-physique.store');

        // Absences
        Route::prefix('absences')->name('absences.')->group(function () {
            Route::get('/', [AbsenceRHController::class, 'index'])->name('index');
            Route::get('/create', [AbsenceRHController::class, 'create'])->name('create');
            Route::post('/', [AbsenceRHController::class, 'store'])->name('store');
            Route::get('/{id}', [AbsenceRHController::class, 'show'])->name('show');
        });

        // Demandes documents (traitement RH)
        Route::prefix('demandes-docs')->name('demandes-docs.')->group(function () {
            Route::get('/', [DemandeDocController::class, 'index'])->name('index');
            Route::get('/pending', [DemandeDocController::class, 'pending'])->name('pending');
            Route::get('/{id}', [DemandeDocController::class, 'show'])->name('show');
            Route::post('/{id}/traiter', [DemandeDocController::class, 'traiter'])->name('traiter');
            Route::post('/{id}/rejeter', [DemandeDocController::class, 'rejeter'])->name('rejeter');
        });

        // Documents administratifs (génération)
        Route::prefix('documents-admin')->name('docs-admin.')->group(function () {
            Route::get('/', [DocumentAdminController::class, 'index'])->name('index');
            Route::get('/attestation/{agent}', [DocumentAdminController::class, 'attestation'])->name('attestation');
            Route::get('/certificat/{agent}', [DocumentAdminController::class, 'certificat'])->name('certificat');
            Route::get('/decision-affectation/{mouvement}', [DocumentAdminController::class, 'decisionAffectation'])->name('decision');
            Route::get('/ordre-mission/{agent}', [DocumentAdminController::class, 'ordreMission'])->name('ordre-mission');
        });

        // Prises en charge
        Route::prefix('pec')->name('pec.')->group(function () {
            Route::get('/', [PriseEnChargeController::class, 'index'])->name('index');
            Route::get('/create', [PriseEnChargeController::class, 'create'])->name('create');
            Route::post('/store', [PriseEnChargeController::class, 'store'])->name('store');
            Route::get('/historique', fn() => wip('Historique des prises en charge'))->name('historique');
            Route::get('/{id}', [PriseEnChargeController::class, 'show'])->name('show');
        });

        // Plannings
        Route::prefix('plannings')->name('plannings.')->group(function () {
            Route::get('/', [PlanningRHController::class, 'index'])->name('index');
            Route::get('/pending', [PlanningRHController::class, 'pending'])->name('pending');
            Route::get('/{id}', [PlanningRHController::class, 'show'])->name('show');
            Route::post('/{id}/valider', [PlanningRHController::class, 'valider'])->name('valider');
        });

        // GED
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [GEDController::class, 'index'])->name('index');
            Route::get('/create', [GEDController::class, 'create'])->name('create');
            Route::post('/', [GEDController::class, 'store'])->name('store');
            Route::get('/search', [GEDController::class, 'search'])->name('search');
            Route::get('/{id}', [GEDController::class, 'show'])->name('show');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ServiceController::class, 'update'])->name('update');
            Route::post('/{id}/assigner-manager', [ServiceController::class, 'assignerManager'])->name('assigner-manager');
        });

        // Divisions
        Route::prefix('divisions')->name('divisions.')->group(function () {
            Route::get('/', [DivisionController::class, 'index'])->name('index');
            Route::get('/create', [DivisionController::class, 'create'])->name('create');
            Route::post('/', [DivisionController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DivisionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DivisionController::class, 'update'])->name('update');
        });

        // Rapports RH
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [RapportRHController::class, 'index'])->name('index');
            Route::get('/mensuel', [RapportRHController::class, 'mensuel'])->name('mensuel');
            Route::get('/effectifs', [RapportRHController::class, 'effectifs'])->name('effectifs');
            Route::get('/stats', [RapportRHController::class, 'stats'])->name('stats');
            Route::get('/export', [RapportRHController::class, 'export'])->name('export');
        });
    });

    // Alias pour documents-admin (compatibilité sidebar DRH)
    Route::middleware(['role:AgentRH|DRH'])->prefix('documents-admin')->name('documents-admin.')->group(function () {
        Route::get('/', [DocumentAdminController::class, 'index'])->name('index');
        Route::get('/attestation/{agent}', [DocumentAdminController::class, 'attestation'])->name('attestation');
        Route::get('/certificat/{agent}', [DocumentAdminController::class, 'certificat'])->name('certificat');
        Route::get('/decision-affectation/{mouvement}', [DocumentAdminController::class, 'decisionAffectation'])->name('decision');
        Route::get('/ordre-mission/{agent}', [DocumentAdminController::class, 'ordreMission'])->name('ordre-mission');
    });

    // Alias pec.* (compatibilité sidebar)
    Route::middleware(['role:AgentRH|DRH'])->prefix('prises-en-charge')->name('pec.')->group(function () {
        Route::get('/', [PriseEnChargeController::class, 'index'])->name('index');
        Route::get('/create', [PriseEnChargeController::class, 'create'])->name('create');
        Route::post('/store', [PriseEnChargeController::class, 'store'])->name('store');
        Route::get('/historique', fn() => wip('Historique des prises en charge'))->name('historique');
        Route::get('/{id}', [PriseEnChargeController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes MANAGER
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Manager'])->prefix('manager')->name('manager.')->group(function () {

        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

        // Mon Équipe
        Route::get('/equipe', [EquipeController::class, 'index'])->name('equipe');
        Route::get('/equipe/dossiers', [EquipeController::class, 'dossiers'])->name('equipe.dossiers');
        Route::get('/equipe/{id}', [EquipeController::class, 'show'])->name('equipe.show');

        // Congés (validation)
        Route::get('/conges/pending', [CongeManagerController::class, 'pending'])->name('conges.pending');
        Route::post('/conges/{id}/valider', [CongeManagerController::class, 'valider'])->name('conges.valider');
        Route::post('/conges/{id}/rejeter', [CongeManagerController::class, 'rejeter'])->name('conges.rejeter');

        // Absences
        Route::prefix('absences')->name('absences.')->group(function () {
            Route::get('/', [AbsenceManagerController::class, 'index'])->name('index');
            Route::get('/create', [AbsenceManagerController::class, 'create'])->name('create');
            Route::post('/', [AbsenceManagerController::class, 'store'])->name('store');
        });

        // Plannings
        Route::prefix('planning')->name('planning.')->group(function () {
            Route::get('/', [PlanningManagerController::class, 'index'])->name('index');
            Route::get('/create', [PlanningManagerController::class, 'create'])->name('create');
            Route::post('/', [PlanningManagerController::class, 'store'])->name('store');
            Route::get('/{id}', [PlanningManagerController::class, 'show'])->name('show');
            Route::post('/{id}/transmettre', [PlanningManagerController::class, 'transmettre'])->name('transmettre');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Routes AGENT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Agent'])->prefix('agent')->name('agent.')->group(function () {

        Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');

        // Mon dossier
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::get('/famille', [ProfilController::class, 'famille'])->name('famille');

        // Documents administratifs (self-service)
        Route::prefix('docs')->name('docs.')->group(function () {
            Route::get('/', [DocAdminAgentController::class, 'index'])->name('index');
            Route::get('/create', [DocAdminAgentController::class, 'create'])->name('create');
            Route::post('/', [DocAdminAgentController::class, 'store'])->name('store');
            Route::get('/{id}', [DocAdminAgentController::class, 'show'])->name('show');
            Route::get('/{id}/telecharger', [DocAdminAgentController::class, 'download'])->name('download');
        });

        // Prises en charge (self-service)
        Route::prefix('pec')->name('pec.')->group(function () {
            Route::get('/', [PECAgentController::class, 'index'])->name('index');
            Route::get('/create', [PECAgentController::class, 'create'])->name('create');
            Route::post('/', [PECAgentController::class, 'store'])->name('store');
            Route::get('/{id}', [PECAgentController::class, 'show'])->name('show');
            Route::get('/{id}/telecharger', [PECAgentController::class, 'download'])->name('download');
        });

        // Mes congés
        Route::prefix('conges')->name('conges.')->group(function () {
            Route::get('/', [CongeAgentController::class, 'index'])->name('index');
            Route::get('/create', [CongeAgentController::class, 'create'])->name('create');
            Route::post('/', [CongeAgentController::class, 'store'])->name('store');
            Route::get('/{id}', [CongeAgentController::class, 'show'])->name('show');
        });

        // Mon planning
        Route::get('/planning', fn() => wip('Mon planning'))->name('planning');

        // Mes documents
        Route::get('/documents', fn() => wip('Mes documents'))->name('documents');
    });

    /*
    |--------------------------------------------------------------------------
    | Préférences utilisateur (tous les rôles authentifiés)
    |--------------------------------------------------------------------------
    */
    Route::get('/preferences', [PreferenceController::class, 'index'])->name('preferences.index');
    Route::put('/preferences', [PreferenceController::class, 'update'])->name('preferences.update');
    Route::post('/preferences/theme', [PreferenceController::class, 'updateTheme'])->name('preferences.theme');

    /*
    |--------------------------------------------------------------------------
    | Aide & FAQ
    |--------------------------------------------------------------------------
    */
    Route::prefix('aide')->name('aide.')->group(function () {
        Route::get('/', [AideController::class, 'index'])->name('index');
        Route::get('/faq', [AideController::class, 'faq'])->name('faq');
        Route::get('/guide', [AideController::class, 'guide'])->name('guide');
        Route::get('/raccourcis', [AideController::class, 'raccourcis'])->name('raccourcis');
    });

    /*
    |--------------------------------------------------------------------------
    | Support technique
    |--------------------------------------------------------------------------
    */
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::get('/create', [SupportController::class, 'create'])->name('create');
        Route::post('/', [SupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Politique de confidentialité
    |--------------------------------------------------------------------------
    */
    Route::get('/politique-confidentialite', fn() => view('pages.politique-confidentialite'))
        ->name('politique-confidentialite');
});


require __DIR__.'/auth.php';
