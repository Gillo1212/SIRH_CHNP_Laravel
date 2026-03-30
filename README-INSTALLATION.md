# 🏥 SIRH CHNP - Design System Complet
## Guide d'Installation et d'Intégration

---

## 📦 Contenu du Package

```
sirh-chnp-design-system-complet/
├── css/
│   └── sirh-design-system.css      # Variables CSS + composants complets
├── layouts/
│   ├── master.blade.php            # Layout principal avec styles intégrés
│   └── partials/
│       ├── sidebar-pro.blade.php   # Sidebar navigation RBAC
│       ├── header-pro.blade.php    # Header avec recherche/notifs/menu user
│       └── footer-pro.blade.php    # Footer avec badges sécurité
├── auth/
│   └── login.blade.php             # Page de connexion split-screen
├── dashboards/
│   ├── admin-dashboard.blade.php   # Dashboard AdminSystème
│   ├── rh-dashboard.blade.php      # Dashboard AgentRH
│   ├── manager-dashboard.blade.php # Dashboard Manager
│   └── agent-dashboard.blade.php   # Dashboard Agent
└── tailwind.config.js              # Config Tailwind avec palette CHNP
```

---

## 🎨 Charte Graphique Officielle v2.0

### Couleurs Principales
| Variable CSS | HEX | Usage |
|--------------|-----|-------|
| `--sirh-primary` | `#0A4D8C` | Couleur dominante |
| `--sirh-secondary` | `#1565C0` | Boutons, liens actifs |
| `--sirh-success` | `#10B981` | États réussite |
| `--sirh-warning` | `#F59E0B` | Alertes, en attente |
| `--sirh-danger` | `#EF4444` | Erreurs, suppressions |
| `--sirh-info` | `#3B82F6` | Informations |

### Typographie
- **Police** : Inter (Google Fonts)
- Importée automatiquement dans `master.blade.php`

---

## 🚀 Installation Rapide

### Étape 1 : Extraire les fichiers
```bash
# Depuis C:\laragon\www\sirh-chnp
unzip sirh-chnp-design-system-complet.zip -d resources/views/
```

### Étape 2 : Copier le logo CHNP
```bash
# Copier le logo vers public/
cp Logo_1.png public/assets/images/logo.png
```

### Étape 3 : Vérifier les dépendances JS (CDN inclus)
Le layout `master.blade.php` charge automatiquement :
-  Alpine.js 3.x
-  ApexCharts
-  SweetAlert2
-  Font Awesome 6
-  Google Fonts (Inter)

---

## 📂 Structure des Dossiers Finale

```
resources/views/
├── layouts/
│   ├── master.blade.php
│   └── partials/
│       ├── sidebar-pro.blade.php
│       ├── header-pro.blade.php
│       └── footer-pro.blade.php
├── auth/
│   └── login.blade.php
├── dashboards/
│   ├── admin.blade.php    (renommer admin-dashboard)
│   ├── rh.blade.php       (renommer rh-dashboard)
│   ├── manager.blade.php  (renommer manager-dashboard)
│   └── agent.blade.php    (renommer agent-dashboard)
└── [autres modules]/
    ├── agents/
    ├── conges/
    ├── contrats/
    └── ...
```

---

## 🔧 Configuration des Routes

### Routes Dashboards (routes/web.php)
```php
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    
    // Dashboard routing basé sur le rôle
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Dashboards spécifiques (optionnel)
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:AdminSystème')
        ->name('dashboard.admin');
        
    Route::get('/dashboard/rh', [DashboardController::class, 'rh'])
        ->middleware('role:AgentRH')
        ->name('dashboard.rh');
        
    Route::get('/dashboard/manager', [DashboardController::class, 'manager'])
        ->middleware('role:Manager')
        ->name('dashboard.manager');
        
    Route::get('/dashboard/agent', [DashboardController::class, 'agent'])
        ->middleware('role:Agent')
        ->name('dashboard.agent');
});
```

### DashboardController
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\User;
use App\Models\Conge;
use App\Models\LogAudit;

class DashboardController extends Controller
{
    /**
     * Redirection automatique vers le dashboard approprié
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('AdminSystème')) {
            return $this->admin();
        } elseif ($user->hasRole('AgentRH')) {
            return $this->rh();
        } elseif ($user->hasRole('Manager')) {
            return $this->manager();
        } else {
            return $this->agent();
        }
    }
    
    /**
     * Dashboard Admin Système
     */
    public function admin()
    {
        $stats = [
            'total_users' => User::actif()->count(),
            'connexions_jour' => LogAudit::whereDate('date_evenement', today())
                ->where('type_evenement', 'connexion')
                ->count(),
            'tentatives_echouees' => LogAudit::whereDate('date_evenement', today())
                ->where('type_evenement', 'echec_connexion')
                ->count(),
            'total_evenements' => LogAudit::whereDate('date_evenement', today())->count(),
        ];
        
        // Distribution des rôles
        $rolesDistribution = [
            'labels' => ['Admin', 'RH', 'Manager', 'Agent'],
            'data' => [
                User::role('AdminSystème')->count(),
                User::role('AgentRH')->count(),
                User::role('Manager')->count(),
                User::role('Agent')->count(),
            ]
        ];
        
        // Derniers événements audit
        $derniersLogs = LogAudit::with('user')
            ->orderBy('date_evenement', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboards.admin', compact('stats', 'rolesDistribution', 'derniersLogs'));
    }
    
    /**
     * Dashboard RH
     */
    public function rh()
    {
        $stats = [
            'total_agents' => Agent::where('statut', 'actif')->count(),
            'contrats_expiration' => \App\Models\Contrat::where('date_fin', '<=', now()->addDays(60))
                ->where('statut', 'actif')
                ->count(),
            'conges_pending' => Conge::where('statut', 'en_attente_rh')->count(),
            'absents_jour' => \App\Models\Absence::whereDate('date_absence', today())->count(),
            'en_conge' => Conge::where('statut', 'approuve')
                ->whereDate('date_debut', '<=', today())
                ->whereDate('date_fin', '>=', today())
                ->count(),
        ];
        
        // Effectifs par service
        $effectifsService = \App\Models\Service::withCount(['agents' => function($q) {
            $q->where('statut', 'actif');
        }])->get();
        
        // Congés en attente
        $congesPending = Conge::with('agent')
            ->where('statut', 'en_attente_rh')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboards.rh', compact('stats', 'effectifsService', 'congesPending'));
    }
    
    /**
     * Dashboard Manager
     */
    public function manager()
    {
        $user = auth()->user();
        $serviceId = $user->agent->service_id ?? null;
        
        $stats = [
            'total_equipe' => Agent::where('service_id', $serviceId)
                ->where('statut', 'actif')
                ->count(),
            'presents' => Agent::where('service_id', $serviceId)
                ->where('statut', 'actif')
                ->whereDoesntHave('absences', function($q) {
                    $q->whereDate('date_absence', today());
                })
                ->count(),
            'absents' => \App\Models\Absence::whereHas('agent', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            })->whereDate('date_absence', today())->count(),
            'en_conge' => Conge::whereHas('agent', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            })
            ->where('statut', 'approuve')
            ->whereDate('date_debut', '<=', today())
            ->whereDate('date_fin', '>=', today())
            ->count(),
            'demandes_pending' => Conge::whereHas('agent', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            })->where('statut', 'en_attente_manager')->count(),
        ];
        
        // Demandes de congés à valider
        $demandesConges = Conge::with('agent')
            ->whereHas('agent', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            })
            ->where('statut', 'en_attente_manager')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Équipe
        $equipe = Agent::where('service_id', $serviceId)
            ->where('statut', 'actif')
            ->with(['user', 'soldeConges'])
            ->get();
        
        return view('dashboards.manager', compact('stats', 'demandesConges', 'equipe'));
    }
    
    /**
     * Dashboard Agent
     */
    public function agent()
    {
        $user = auth()->user();
        $agent = $user->agent;
        
        // Soldes de congés
        $soldes = [
            'annuel' => $agent->soldeConges->solde_annuel ?? 18,
            'annuel_total' => 30,
            'maladie' => $agent->soldeConges->solde_maladie ?? 15,
            'maladie_total' => 15,
            'exceptionnel' => $agent->soldeConges->solde_exceptionnel ?? 5,
            'exceptionnel_total' => 10,
            'recuperation' => $agent->soldeConges->heures_recuperation ?? 0,
        ];
        
        // Demandes en cours
        $demandes = Conge::where('agent_id', $agent->id)
            ->whereIn('statut', ['en_attente_manager', 'en_attente_rh'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Documents récents
        $documents = \App\Models\Document::where('agent_id', $agent->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboards.agent', compact('soldes', 'demandes', 'documents'));
    }
}
```

---

##  Checklist d'Intégration

- [ ] Extraire le ZIP dans `resources/views/`
- [ ] Copier `Logo_1.png` vers `public/assets/images/logo.png`
- [ ] Renommer les dashboards (retirer `-dashboard`)
- [ ] Créer `DashboardController.php`
- [ ] Configurer les routes dans `routes/web.php`
- [ ] Vérifier les permissions Spatie (`@hasrole`, `@can`)
- [ ] Tester avec les 4 comptes (admin, rh, manager, agent)
- [ ] Adapter les autres vues avec `@extends('layouts.master')`

---

## 🔐 Comptes de Test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin Système | admin@chnp.sn | password |
| Agent RH | rh@chnp.sn | password |
| Manager | manager@chnp.sn | password |
| Agent | agent@chnp.sn | password |

---

## 📊 Fonctionnalités par Dashboard

### Dashboard Admin
- KPIs : Utilisateurs actifs, connexions, échecs, audit
- Graphique Donut : Distribution des rôles
- Graphique Area : Activité système 7 jours
- Tableau : Derniers événements d'audit
- Tableau : Utilisateurs actifs récemment

### Dashboard RH
- KPIs : Agents, contrats expiration, congés pending, absents
- Graphique Bar : Effectifs par service
- Graphique Area : Évolution congés 12 mois
- Tableau : Demandes congés en attente
- Tableau : Contrats à renouveler

### Dashboard Manager
- KPIs : Équipe, présents, absents, en congé
- Tableau : Demandes congés à valider (actions)
- Planning équipe semaine (miniature)
- Graphique Donut : Absences équipe
- Graphique Line : Taux de présence
- Liste : Mon équipe

### Dashboard Agent
- Profil : Photo, nom, matricule, service
- Soldes : Annuels, maladie, exceptionnels, récup
- Planning : Semaine courante (visuel)
- Liste : Demandes en cours
- Liste : Mes documents
- Actions : Demander congé, déclarer absence, attestation

---

## 🛡️ Sécurité Intégrée

Le design system inclut des éléments visuels de la **TRIADE CID** :
- Badge SSL/TLS dans le footer
- Badge RGPD
- Badge AES-256 (chiffrement)
- Indicateurs dans la sidebar
- Badges sur la page login

---

## 📝 Support

Pour toute question relative au mémoire ou à l'intégration technique :
- Projet : SIRH CHNP - Master 2 Informatique
- Sujet : Conception d'un SI sécurisé centré sur la TRIADE CID

---

**Bonne intégration ! 🎓**
