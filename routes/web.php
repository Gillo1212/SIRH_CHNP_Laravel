<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserAccountController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BackupController;
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
use App\Http\Controllers\Agent\GEDAgentController;
use App\Http\Controllers\RH\ServiceController;
use App\Http\Controllers\RH\DivisionController;
use App\Http\Controllers\RH\DemandeDocController;
use App\Http\Controllers\RH\RapportRHController;
use App\Http\Controllers\DRH\DRHDashboardController;
use App\Http\Controllers\DRH\RapportDRHController;
use App\Http\Controllers\DRH\IndicateurController;
use App\Http\Controllers\DRH\ValidationDRHController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\EquipeController;
use App\Http\Controllers\Manager\CongeManagerController;
use App\Http\Controllers\Manager\AbsenceManagerController;
use App\Http\Controllers\Manager\PlanningManagerController;
use App\Http\Controllers\Manager\MonServiceController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\ProfilController;
use App\Http\Controllers\Agent\CongeAgentController;
use App\Http\Controllers\Agent\PECAgentController;
use App\Http\Controllers\Agent\DocAdminAgentController;
use App\Http\Controllers\Agent\AbsenceAgentController;
use App\Http\Controllers\Agent\PlanningAgentController;
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
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/connexions', [AuditController::class, 'connexions'])->name('audit.connexions');
        Route::get('/audit/echecs', [AuditController::class, 'echecs'])->name('audit.echecs');
        Route::get('/audit/export', [AuditController::class, 'export'])->name('audit.export');

        // Paramètres
        Route::get('/settings',               [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings',              [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/notifications', [SettingsController::class, 'notifications'])->name('settings.notifications');

        // Sauvegardes
        Route::get('/backups',                          [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create',                  [BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{filename}/download',      [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{filename}',            [BackupController::class, 'delete'])->name('backups.delete');
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
        Route::get('/organigramme', [DRHDashboardController::class, 'organigramme'])->name('organigramme');

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
            Route::post('/mouvements/{id}/rejeter', [ValidationDRHController::class, 'rejeterMouvement'])->name('rejeter-mouvement');
            Route::get('/pec-exceptionnelles', [ValidationDRHController::class, 'pecExceptionnelles'])->name('pec');
            Route::post('/pec/{id}/valider', [ValidationDRHController::class, 'validerPEC'])->name('valider-pec');
        });

        // Rapports direction
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/bilan-social', [RapportDRHController::class, 'bilanSocial'])->name('bilan');
            Route::get('/effectifs', [RapportDRHController::class, 'effectifs'])->name('effectifs');
            Route::get('/previsions-departs', [RapportDRHController::class, 'previsions'])->name('previsions');
            Route::get('/export-consolide', [RapportDRHController::class, 'exportConsolide'])->name('export');
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
            Route::get('/export/csv',   [AgentRHController::class, 'export'])->name('export.csv');
            Route::get('/export/excel', [AgentRHController::class, 'exportExcel'])->name('export.excel');
        });

        // Contrats
        Route::prefix('contrats')->name('contrats.')->group(function () {
            Route::get('/',                                    [ContratController::class, 'index'])->name('index');
            Route::get('/create',                              [ContratController::class, 'create'])->name('create');
            Route::post('/',                                   [ContratController::class, 'store'])->name('store');
            Route::get('/export',                              [ContratController::class, 'export'])->name('export');
            Route::get('/expiring',                            [ContratController::class, 'expiring'])->name('expiring');
            Route::get('/{id}',                                [ContratController::class, 'show'])->name('show');
            Route::get('/{id}/edit',                           [ContratController::class, 'edit'])->name('edit');
            Route::put('/{id}',                                [ContratController::class, 'update'])->name('update');
            Route::post('/{id}/renouveler',                    [ContratController::class, 'renouveler'])->name('renouveler');
            Route::patch('/{id}/cloturer',                     [ContratController::class, 'cloturer'])->name('cloturer');
        });

        // Mouvements
        Route::prefix('mouvements')->name('mouvements.')->group(function () {
            Route::get('/',                              [MouvementController::class, 'index'])->name('index');
            Route::get('/affectations',                  [MouvementController::class, 'affectations'])->name('affectations');
            Route::get('/mutations',                     [MouvementController::class, 'mutations'])->name('mutations');
            Route::get('/retours',                       [MouvementController::class, 'retours'])->name('retours');
            Route::get('/departs',                       [MouvementController::class, 'departs'])->name('departs');
            Route::get('/export',                        [MouvementController::class, 'export'])->name('export');
            Route::get('/create',                        [MouvementController::class, 'create'])->name('create');
            Route::post('/',                             [MouvementController::class, 'store'])->name('store');
            Route::get('/{id}',                          [MouvementController::class, 'show'])->name('show');
            Route::put('/{id}',                          [MouvementController::class, 'update'])->name('update');
            Route::post('/{id}/effectuer',               [MouvementController::class, 'effectuer'])->name('effectuer');
            Route::post('/{id}/annuler',                 [MouvementController::class, 'annuler'])->name('annuler');
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
            Route::get('/',        [AbsenceRHController::class, 'index'])->name('index');
            Route::get('/export',  [AbsenceRHController::class, 'export'])->name('export');
            Route::get('/create',  [AbsenceRHController::class, 'create'])->name('create');
            Route::post('/',       [AbsenceRHController::class, 'store'])->name('store');
            Route::get('/{id}',    [AbsenceRHController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AbsenceRHController::class, 'edit'])->name('edit');
            Route::put('/{id}',    [AbsenceRHController::class, 'update'])->name('update');
            Route::delete('/{id}', [AbsenceRHController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/valider-justificatif',            [AbsenceRHController::class, 'validerJustificatif'])->name('valider-justificatif');
            Route::patch('/{id}/rejeter-justificatif',            [AbsenceRHController::class, 'rejeterJustificatif'])->name('rejeter-justificatif');
            Route::patch('/{id}/pieces/{pieceId}/valider',        [AbsenceRHController::class, 'validerPiece'])->name('pieces.valider');
            Route::patch('/{id}/pieces/{pieceId}/rejeter',        [AbsenceRHController::class, 'rejeterPiece'])->name('pieces.rejeter');
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
            Route::get('/historique', [PriseEnChargeController::class, 'historique'])->name('historique');
            Route::get('/{id}', [PriseEnChargeController::class, 'show'])->name('show');
            Route::patch('/{id}', [PriseEnChargeController::class, 'update'])->name('update');
        });

        // Plannings
        Route::prefix('plannings')->name('plannings.')->group(function () {
            Route::get('/',               [PlanningRHController::class, 'index'])->name('index');
            Route::get('/pending',        [PlanningRHController::class, 'pending'])->name('pending');
            Route::get('/{id}',           [PlanningRHController::class, 'show'])->name('show');
            Route::post('/{id}/valider',  [PlanningRHController::class, 'valider'])->name('valider');
            Route::post('/{id}/rejeter',  [PlanningRHController::class, 'rejeter'])->name('rejeter');
        });

        // GED — Gestion Électronique de Documents
        Route::prefix('ged')->name('ged.')->group(function () {
            // Dashboard GED
            Route::get('/', [GEDController::class, 'index'])->name('index');
            // Recherche globale
            Route::get('/recherche', [GEDController::class, 'search'])->name('search');
            // Étagères
            Route::get('/etageres', [GEDController::class, 'etageres'])->name('etageres');
            Route::post('/etageres', [GEDController::class, 'etagereStore'])->name('etageres.store');
            // Dossiers (enveloppes)
            Route::get('/dossiers', [GEDController::class, 'dossiers'])->name('dossiers');
            Route::get('/dossiers/{id}', [GEDController::class, 'dossierShow'])->name('dossier.show');
            // Documents
            Route::get('/documents/create', [GEDController::class, 'create'])->name('documents.create');
            Route::post('/documents', [GEDController::class, 'store'])->name('documents.store');
            Route::get('/documents/{id}', [GEDController::class, 'show'])->name('documents.show');
            Route::get('/documents/{id}/preview', [GEDController::class, 'preview'])->name('documents.preview');
            Route::get('/documents/{id}/download', [GEDController::class, 'download'])->name('documents.download');
            Route::patch('/documents/{id}/archiver', [GEDController::class, 'archiver'])->name('documents.archiver');
            Route::patch('/documents/{id}/restaurer', [GEDController::class, 'restaurer'])->name('documents.restaurer');
            Route::patch('/documents/{id}/detruire', [GEDController::class, 'detruire'])->name('documents.detruire');
        });

        // Alias de compatibilité pour l'ancienne route documents.*
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [GEDController::class, 'index'])->name('index');
            Route::get('/search', [GEDController::class, 'search'])->name('search');
            Route::get('/create', [GEDController::class, 'create'])->name('create');
            Route::post('/', [GEDController::class, 'store'])->name('store');
            Route::get('/{id}', [GEDController::class, 'show'])->name('show');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{id}', [ServiceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/assigner-manager', [ServiceController::class, 'assignerManager'])->name('assigner-manager');
            Route::post('/{id}/attach-agent', [ServiceController::class, 'attachAgent'])->name('attach-agent');
            Route::delete('/{id}/agents/{agentId}', [ServiceController::class, 'detachAgent'])->name('detach-agent');
        });

        // Divisions
        Route::prefix('divisions')->name('divisions.')->group(function () {
            Route::get('/', [DivisionController::class, 'index'])->name('index');
            Route::get('/create', [DivisionController::class, 'create'])->name('create');
            Route::post('/', [DivisionController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DivisionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DivisionController::class, 'update'])->name('update');
            Route::delete('/{id}', [DivisionController::class, 'destroy'])->name('destroy');
        });

        // Rapports RH
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [RapportRHController::class, 'index'])->name('index');
            Route::get('/mensuel', [RapportRHController::class, 'mensuel'])->name('mensuel');
            Route::get('/effectifs', [RapportRHController::class, 'effectifs'])->name('effectifs');
            Route::get('/stats', [RapportRHController::class, 'stats'])->name('stats');
            Route::get('/export', [RapportRHController::class, 'export'])->name('export');
            Route::get('/chart-data', [RapportRHController::class, 'chartData'])->name('chart-data');
        });
    });

    // Alias pour documents-admin (compatibilité sidebar DRH)
    Route::middleware(['role:AgentRH|DRH|AdminSystème'])->prefix('documents-admin')->name('documents-admin.')->group(function () {
        Route::get('/', [DocumentAdminController::class, 'index'])->name('index');
        Route::get('/attestation/{agent}', [DocumentAdminController::class, 'attestation'])->name('attestation');
        Route::get('/certificat/{agent}', [DocumentAdminController::class, 'certificat'])->name('certificat');
        Route::get('/decision-affectation/{mouvement}', [DocumentAdminController::class, 'decisionAffectation'])->name('decision');
        Route::get('/ordre-mission/{agent}', [DocumentAdminController::class, 'ordreMission'])->name('ordre-mission');
    });

    // Alias pec.* (compatibilité sidebar)
    Route::middleware(['role:AgentRH|DRH|AdminSystème'])->prefix('prises-en-charge')->name('pec.')->group(function () {
        Route::get('/', [PriseEnChargeController::class, 'index'])->name('index');
        Route::get('/create', [PriseEnChargeController::class, 'create'])->name('create');
        Route::post('/store', [PriseEnChargeController::class, 'store'])->name('store');
        Route::get('/historique', [PriseEnChargeController::class, 'historique'])->name('historique');
        Route::get('/{id}', [PriseEnChargeController::class, 'show'])->name('show');
        Route::patch('/{id}', [PriseEnChargeController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes MANAGER
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Manager'])->prefix('manager')->name('manager.')->group(function () {

        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

        // Mon Service (isolation stricte par service)
        Route::prefix('mon-service')->name('service.')->middleware('manager.service')->group(function () {
            Route::get('/', [MonServiceController::class, 'index'])->name('index');
            Route::get('/agents', [MonServiceController::class, 'agents'])->name('agents');
            Route::get('/statistiques', [MonServiceController::class, 'statistics'])->name('statistics');
        });

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
            Route::get('/',        [AbsenceManagerController::class, 'index'])->name('index');
            Route::get('/create',  [AbsenceManagerController::class, 'create'])->name('create');
            Route::post('/',       [AbsenceManagerController::class, 'store'])->name('store');
            Route::get('/{id}',    [AbsenceManagerController::class, 'show'])->name('show');
        });

        // Plannings
        Route::prefix('planning')->name('planning.')->group(function () {
            Route::get('/',                                [PlanningManagerController::class, 'index'])->name('index');
            Route::get('/create',                          [PlanningManagerController::class, 'create'])->name('create');
            Route::post('/',                               [PlanningManagerController::class, 'store'])->name('store');
            Route::get('/{id}',                            [PlanningManagerController::class, 'show'])->name('show');
            Route::put('/{id}',                            [PlanningManagerController::class, 'update'])->name('update');
            Route::delete('/{id}',                         [PlanningManagerController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/transmettre',               [PlanningManagerController::class, 'transmettre'])->name('transmettre');
            Route::post('/{id}/lignes',                    [PlanningManagerController::class, 'addLigne'])->name('lignes.store');
            Route::delete('/{id}/lignes/{ligneId}',        [PlanningManagerController::class, 'removeLigne'])->name('lignes.destroy');
        });

        // Mouvements du service (lecture seule)
        Route::get('/mouvements', [MonServiceController::class, 'mouvements'])->name('mouvements');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes AGENT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['agent.profile'])->prefix('agent')->name('agent.')->group(function () {

        Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');

        // Mon dossier
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::get('/famille', [ProfilController::class, 'famille'])->name('famille');
        Route::post('/profil/photo', [ProfilController::class, 'updatePhoto'])->name('profil.photo');
        Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

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

        // Mes absences (demande + justification)
        Route::prefix('absences')->name('absences.')->group(function () {
            Route::get('/',                      [AbsenceAgentController::class, 'index'])->name('index');
            Route::post('/',                     [AbsenceAgentController::class, 'store'])->name('store');
            Route::post('/{id}/justifier',       [AbsenceAgentController::class, 'uploadJustificatif'])->name('justifier');
        });

        // Mon contrat
        Route::get('/mon-contrat', [ProfilController::class, 'monContrat'])->name('mon-contrat');

        // Mon parcours professionnel
        Route::get('/mon-parcours', [ProfilController::class, 'monParcours'])->name('mon-parcours');

        // Mon planning
        Route::get('/planning', [PlanningAgentController::class, 'index'])->name('planning');

        // Mes documents (GED Agent)
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [GEDAgentController::class, 'index'])->name('index');
            Route::get('/{id}', [GEDAgentController::class, 'show'])->name('show');
            Route::get('/{id}/preview', [GEDAgentController::class, 'preview'])->name('preview');
            Route::get('/{id}/download', [GEDAgentController::class, 'download'])->name('download');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications (tous les rôles authentifiés)
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                         [NotificationController::class, 'index'])->name('index');
        Route::get('/{id}/read',                [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read',           [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}',                  [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/',                      [NotificationController::class, 'destroyAll'])->name('destroy-all');
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
