# SIRH CHNP - Système d'Information de Ressources Humaines

##  À Propos du Projet

Système d'Information de Ressources Humaines (SIRH) développé pour le Centre Hospitalier National de Pikine (CHNP) au Sénégal dans le cadre d'un mémoire de Master 2 en Informatique.

### Objectifs
- Digitaliser la gestion des ressources humaines du CHNP
- Automatiser les workflows de validation (congés, plannings)
- Centraliser les informations du personnel
- Faciliter le reporting et le pilotage RH
- Garantir la traçabilité et la sécurité des données

##  Fonctionnalités Principales

### 1. Gestion Administrative du Personnel
- Dossier personnel complet
- Structure organisationnelle (divisions, services)
- Mouvements (affectations, mutations)
- Recherche multicritère

### 2. Gestion des Contrats
- CRUD contrats
- Alertes d'expiration
- Renouvellement
- Historique

### 3. Gestion des Congés
- Workflow à 3 niveaux (Agent → Manager → RH)
- Gestion des soldes
- Calcul automatique des jours ouvrables
- Notifications automatiques

### 4. Gestion des Absences
- Types d'absence (maladie, personnelle, professionnelle, injustifiée)
- Upload de justificatifs
- Validation multi-niveaux
- Statistiques d'absentéisme

### 5. Gestion des Plannings
- Création par les managers
- Validation par la RH
- Types: jour, nuit, garde, repos
- Consultation par les agents

### 6. GED (Gestion Électronique de Documents)
- Upload et catégorisation
- Indexation et recherche
- Contrôle d'accès
- Archivage automatique

### 7. Reporting & Tableaux de Bord
- Dashboard RH global
- Dashboard Manager par service
- Rapports PDF/Excel
- Indicateurs clés (KPIs)

### 8. Administration Système
- Gestion des utilisateurs
- RBAC (Rôles et Permissions)
- Audit trail complet
- Configuration système

##  Rôles Utilisateurs

### Agent (Personnel)
- Consulter son dossier
- Demander des congés
- Justifier absences
- Consulter son planning

### Manager de Service
- Gérer son équipe
- Valider les congés
- Enregistrer les absences
- Créer les plannings

### Agent RH
- Gestion complète du personnel
- Validation finale des congés
- Gestion des contrats
- Génération de rapports

### Administrateur Système
- Gestion des comptes
- Configuration RBAC
- Audit trail
- Paramètres système

##  Stack Technique

### Backend
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Base de données**: MySQL 8.0+ / PostgreSQL 14+

### Frontend
- **Template Engine**: Blade
- **CSS Framework**: Bootstrap 5.3
- **JavaScript**: Alpine.js / Vanilla JS
- **Icons**: Font Awesome 6
- **Charts**: Chart.js

### Packages Principaux
```json
{
  "spatie/laravel-permission": "^6.0",
  "spatie/laravel-activitylog": "^4.8",
  "barryvdh/laravel-dompdf": "^3.0",
  "maatwebsite/excel": "^3.1",
  "intervention/image": "^3.0"
}
```

##  Structure du Projet

```
sirh-chnp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Admin/
│   │   │   ├── RH/
│   │   │   ├── Manager/
│   │   │   └── Agent/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   ├── Policies/
│   └── Traits/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   └── views/
│       ├── admin/
│       ├── rh/
│       ├── manager/
│       ├── agent/
│       └── layouts/
└── tests/
    ├── Feature/
    └── Unit/
```

##  Installation

### Prérequis
- PHP 8.2+
- Composer 2.x
- Node.js 18+ & NPM
- MySQL 8.0+ ou PostgreSQL 14+

### Étapes

```bash
# Cloner le repository
git clone https://github.com/votre-username/sirh-chnp.git
cd sirh-chnp

# Installer les dépendances
composer install
npm install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Créer la base de données puis configurer .env

# Exécuter les migrations et seeders
php artisan migrate --seed

# Compiler les assets
npm run build

# Créer le lien symbolique storage
php artisan storage:link

# Démarrer le serveur
php artisan serve
```

### Comptes par Défaut

Après seeding:

- **Admin**: admin@chnp.sn / password
- **Agent RH**: rh@chnp.sn / password
- **Manager**: manager@chnp.sn / password
- **Agent**: agent@chnp.sn / password

##  Tests

```bash
# Exécuter tous les tests
php artisan test

# Avec couverture
php artisan test --coverage

# Tests spécifiques
php artisan test --filter UserTest
```

##  Documentation

- [Guide d'Installation](docs/installation.md)
- [Documentation Technique](docs/technique.md)
- [Guide Utilisateur](docs/user-guide.md)
- [API Documentation](docs/api.md)

##  Sécurité

### Fonctionnalités de Sécurité
-  Authentification sécurisée (bcrypt)
-  RBAC granulaire
-  CSRF Protection
-  XSS Protection
-  SQL Injection Prevention (Eloquent)
-  Validation des entrées
-  Audit trail complet
-  Rate limiting
-  Verrouillage de compte (5 tentatives)
-  Politique de mot de passe complexe

### Rapporter une Vulnérabilité
Si vous découvrez une vulnérabilité, envoyez un email à security@chnp.sn

## Contribution

Ce projet est développé dans le cadre d'un mémoire de Master 2. Les contributions externes ne sont pas acceptées pour le moment.

##  Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE] pour plus de détails.

##  Auteur

**Votre Nom**
- Email: nadielinegilbert@gmail.com
- GitHub: https://github.com/Gillo1212

##  Remerciements

- Centre Hospitalier National de Pikine (CHNP)
- Encadrant académique: Pr. Deme
- Encadrant professionnel: M. Leplan

##  Roadmap

### Phase 1 - Fondations 
- [x] Installation Laravel
- [x] Configuration RBAC
- [x] Authentification

### Phase 2 - Core Modules 
- [x] Gestion Personnel
- [x] Gestion Contrats
- [ ] Gestion Congés
- [ ] Gestion Absences

### Phase 3 - Features 
- [ ] Gestion Plannings
- [ ] GED
- [ ] Reporting

### Phase 4 - Finalisation 
- [ ] Tests complets
- [ ] Documentation
- [ ] Déploiement

##  Support

Pour toute question ou problème:
- Email: team_supportchnp@gmail.com
- Issues: https://github.com/Gillo1212/SIRH_CHNP_Laravel/issues

---

**Développé avec  pour le CHNP** | Mémoire de Master 2 