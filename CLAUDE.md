# SIRH Centre Hospitalier National de Pikine

## 🎯 CONTEXTE MÉMOIRE

**Sujet** : "Conception et développement d'un système d'information sécurisé pour la gestion du personnel hospitalier : approche centrée sur la **confidentialité, l'intégrité et la disponibilité** des données RH – Cas du Centre Hospitalier National de Pikine (CHNP)"

**Type** : Mémoire Master 2 Informatique  
**Échéance** : Soutenance dans 2 semaines  
**Objectif** : Démontrer l'application de la **TRIADE CID** (Confidentialité, Intégrité, Disponibilité)

## 🔐 TRIADE CID (AU CŒUR DU MÉMOIRE)

### **C - CONFIDENTIALITÉ**
**Objectif** : Garantir que seules les personnes autorisées accèdent aux données

**Implémentation** :
1. **Chiffrement des données CRITIQUES en base** :
   - adresse (AES-256)
   - telephone (AES-256)
   - numero_assurance (AES-256)

2. **RBAC strict** (6 rôles + permissions granulaires)
3. **Policies Laravel** : Vérification autorisation AVANT chaque action
4. **Masquage à l'affichage** : Données sensibles partiellement masquées
5. **Audit trail complet** : Traçabilité de TOUS les accès

### **I - INTÉGRITÉ**
**Objectif** : Garantir que les données ne sont pas altérées

**Implémentation** :
1. **Validation stricte** : Form Requests pour TOUTES les entrées
2. **Transactions DB** : DB::transaction() pour opérations multi-tables
3. **Audit Log immuable** : Spatie Activity Log (impossible à modifier)
4. **Contraintes DB** : Clés étrangères, UNIQUE, NOT NULL
5. **Vérification cohérence** : Soldes congés, dates, etc.

### **D - DISPONIBILITÉ**
**Objectif** : Garantir l'accès au système quand nécessaire

**Implémentation** :
1. **Performance optimisée** : Eager loading, cache, index DB
2. **Sauvegarde automatique** : Backup quotidien chiffré
3. **Gestion erreurs** : Try-catch, messages clairs
4. **Tests robustes** : Feature tests + Unit tests
5. **Documentation complète** : Guide utilisateur + technique

## 🏗️ ARCHITECTURE : USER ET AGENT SÉPARÉS

### **Table `users`** (Authentification UNIQUEMENT)

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('login')->unique();
    $table->string('password'); // Bcrypt
    $table->enum('statut_compte', ['actif', 'inactif', 'suspendu'])->default('actif');
    $table->boolean('verouille')->default(false);
    $table->integer('tentatives_connexion')->default(0);
    $table->timestamp('date_creation');
    $table->timestamp('derniere_connexion')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### **Table `agents`** (Données RH COMPLÈTES)

```php
Schema::create('agents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
    $table->string('matricule')->unique(); // CHNP-00001
    
    // Données personnelles
    $table->string('nom');
    $table->string('prenom');
    $table->date('date_naissance');
    $table->string('lieu_naissance');
    $table->enum('sexe', ['M', 'F']);
    $table->enum('situation_familiale', ['Célibataire', 'Marié', 'Divorcé', 'Veuf']);
    $table->string('nationalite');
    
    // Données CRITIQUES (CHIFFREMENT AES-256)
    $table->text('adresse')->nullable();
    $table->string('telephone')->nullable();
    $table->string('email')->nullable();
    $table->string('numero_assurance')->nullable();
    
    // Données professionnelles
    $table->date('date_recrutement');
    $table->foreignId('service_id')->constrained();
    $table->string('fonction');
    $table->string('grade')->nullable();
    $table->enum('categorie_cp', [
        'Cadre_Superieur', 'Cadre_Moyen', 'Technicien_Superieur',
        'Technicien', 'Agent_Administratif', 'Agent_de_Service',
        'Commis_Administration', 'Ouvrier', 'Sans_Diplome'
    ]);
    $table->enum('statut', ['actif', 'en_conge', 'suspendu', 'retraite'])->default('actif');
    $table->string('photo')->nullable();
    
    $table->timestamps();
    $table->softDeletes();
});
```

## 🔧 STACK TECHNIQUE

- **Backend** : Laravel 12.x, PHP 8.2+
- **Base de données** : MySQL 8.0
- **Frontend** : Bootstrap 5.3, Blade, Alpine.js
- **RBAC** : Spatie Permission 7.2
- **Audit** : Spatie Activity Log 4.12
- **Chiffrement** : AES-256 (Laravel Crypt)
- **PDF** : DomPDF 3.1
- **Excel** : Maatwebsite Excel 1.1
- **Images** : Intervention Image 1.5

## 👥 RÔLES RBAC (6 RÔLES)

### Hiérarchie
```
Agent < Major ─┐
               ├─ < AgentRH < DRH < AdminSystème
    Manager ───┘
```
> **Major** et **Manager** sont des rôles de supervision de terrain, au même niveau hiérarchique, mais avec des périmètres différents :
> - **Manager** : supervision administrative d'un service (validation congés, statistiques)
> - **Major** : supervision opérationnelle de terrain (planning, heures sup, absences)

---

### **1. Agent**
**Permissions** :
```
voir_propre_dossier
demander_conge
voir_mes_conges
voir_mes_absences
justifier_absence
voir_mon_planning
telecharger_document
voir_dashboard_agent
demander_document_administratif
demander_prise_en_charge
voir_mes_demandes
```
**Accès** : Self-service uniquement (dossier personnel, demandes, téléchargements)

---

### **2. Major** ⭐ RÔLE TERRAIN
**Contexte** : Rôle spécifique milieu hospitalier — chef de salle/major infirmier. Gère son équipe opérationnellement (plannings, absences, heures sup) sans pouvoir valider les congés.

**Permissions** :
```
// Héritage Agent
voir_propre_dossier
demander_conge
voir_mes_conges
voir_mes_absences
justifier_absence
voir_mon_planning
telecharger_document
voir_dashboard_agent

// Gestion équipe (lecture)
voir_equipe
voir_conges_equipe
donner_avis_sur_conge        // Donne un avis mais NE VALIDE PAS

// Absences équipe
enregistrer_absence          // Enregistre les absences de son équipe

// Planning
voir_planning_service
creer_planning
modifier_planning
transmettre_planning         // Transmet au RH pour validation

// Heures supplémentaires
gerer_heures_sup             // Saisie et suivi des heures sup de l'équipe

// Dashboard exclusif
voir_dashboard_major
```
**N'a PAS accès à** : validation finale congés, GED globale, mouvements, documents administratifs tiers

---

### **3. Manager**
**Permissions** : Permissions Agent +
```
// Équipe
voir_equipe
voir_conges_equipe
voir_absences_equipe

// Congés
valider_conge_manager        // 1ère validation (avant RH)
rejeter_conge_manager

// Absences
enregistrer_absence
modifier_absence_equipe

// Planning
voir_planning_service
creer_planning
modifier_planning
transmettre_planning

// Dashboard
voir_dashboard_manager
voir_statistiques_service
```

---

### **4. AgentRH**
**Permissions** :
```
// Personnel
creer_agent, modifier_agent, voir_agent, supprimer_agent
gerer_enfants, gerer_conjoints
gerer_contrats
exporter_agents

// Congés
approuver_conge_rh           // Validation finale
rejeter_conge_rh
saisir_conge_physique        // Pour agent au bureau
gerer_soldes_conges

// Absences
valider_justificatif_absence

// Plannings
valider_planning
rejeter_planning

// GED
gerer_documents
gerer_etageres
gerer_dossiers

// Mouvements
creer_mouvement
modifier_mouvement
traiter_mouvement

// Documents administratifs
traiter_demande_document
generer_document
rejeter_demande_document

// Prises en charge
valider_pec
rejeter_pec

// Organisation
gerer_services
gerer_divisions
assigner_manager

// Rapports
generer_rapport
exporter_excel
exporter_pdf

// Dashboard
voir_dashboard_rh
```

---

### **5. DRH (Directeur RH)**
**Hérite de TOUTES les permissions AgentRH** +
```
// Pilotage stratégique
voir_dashboard_drh
voir_kpis_globaux
voir_organigramme
voir_indicateurs_effectifs
voir_pyramide_ages
voir_taux_turnover
voir_taux_absenteisme
voir_evolution_effectifs

// Validations finales
signer_document_officiel
valider_mouvement_strategique
valider_pec_exceptionnelle

// Rapports direction
generer_bilan_social
generer_rapport_direction
voir_previsions_departs

// Organisation
valider_creation_service
valider_modification_service
```

---

### **6. AdminSystème**
**Toutes les permissions** + exclusives :
```
gerer_utilisateurs
gerer_roles_permissions
voir_audit_trail_complet
configurer_systeme
gerer_sauvegardes
impersonner_utilisateur      // Pour support/debug
```

## ⚠️ PÉRIMÈTRE MVP (IMPORTANT)

###  INCLUS dans le MVP
- Gestion personnel (agents, enfants, conjoints)
- Gestion contrats (CRUD, alertes expiration)
- Gestion congés (workflow 3 niveaux + saisie physique)
- Gestion absences
- Gestion plannings
- GED (archivage documents)
- Mouvements (affectations, mutations, retours, départs)
- Documents administratifs (attestations, certificats, ordres de mission)
- Prises en charge médicales
- Services et divisions
- Reporting et dashboards
- RBAC complet (6 rôles : Agent, Major, Manager, AgentRH, DRH, AdminSystème)
- Audit trail

### ❌ EXCLUS du MVP (PAS DE PAIE)
- Calcul des salaires
- Gestion de la paie
- Bulletins de salaire
- Masse salariale
- Cotisations sociales
- Indicateurs financiers (coût moyen/agent, budget RH)

**Note** : Les indicateurs DRH sont basés sur les EFFECTIFS et MOUVEMENTS, pas sur les données financières.

## 🎯 MVP COMPLET : 7 MODULES + 3 NOUVEAUX (10 MODULES TOTAL)

### **MODULE 1 : GESTION PERSONNEL**
- CRUD agents (avec user associé)
- Génération matricule auto (CHNP-00001)
- Gestion enfants/conjoints inline
- Upload avatar
- Recherche multicritère
- Export Excel

### **MODULE 2 : GESTION CONTRATS**
- CRUD contrats
- Alertes expiration (< 60 jours)
- Command Artisan quotidien
- Workflow renouvellement

### **MODULE 3 : GESTION CONGÉS**
- Workflow 3 niveaux (Agent → Manager → RH)
- Vérification solde AVANT demande
- Calcul jours ouvrables
- MAJ solde automatique (transaction)
- Notifications à chaque étape
- **Saisie physique** (RH pour agent au bureau)

### **MODULE 4 : GESTION ABSENCES**
- Enregistrement absences (Manager/RH)
- Types : Maladie, Personnelle, Professionnelle, Injustifiée
- Upload justificatif
- Validation justificatif (RH)

### **MODULE 5 : GESTION PLANNINGS**
- Création planning mensuel (Manager **ou Major**)
- Types postes : Jour, Nuit, Garde, Repos, Astreinte
- Transmission à RH
- Validation/Rejet RH
- Export PDF
- **Heures supplémentaires** : Saisie et suivi par le Major

### **MODULE 6 : GED**
- Upload documents multi-formats
- Catégorisation
- Indexation, recherche
- Contrôle d'accès par rôle
- Versioning

### **MODULE 7 : REPORTING & DASHBOARDS**
- Dashboard par rôle (Agent, Manager, RH, DRH, Admin)
- Rapports PDF/Excel
- Charts interactifs (ApexCharts)

### **MODULE 8 : MOUVEMENTS** ⭐ NOUVEAU
- Affectations initiales
- Mutations (changement de service)
- Retours (réintégration)
- Départs (démission, retraite, fin de contrat)
- Génération décisions d'affectation
- Validation DRH pour mouvements stratégiques

### **MODULE 9 : DOCUMENTS ADMINISTRATIFS** ⭐ NOUVEAU
- **Self-service Agent** : Demande en ligne
- **Traitement RH** : Génération documents
- Types : Attestation de travail, Certificat de travail, Ordre de mission
- Workflow : Demande → Traitement → Notification → Téléchargement
- Signature DRH pour documents officiels

### **MODULE 10 : PRISES EN CHARGE MÉDICALES** ⭐ NOUVEAU
- **Self-service Agent** : Demande pour soi, conjoint ou enfant
- **Traitement RH** : Validation standard
- **Validation DRH** : Pour PEC exceptionnelles
- Génération attestation de prise en charge
- Workflow complet avec notifications

## 🗄️ NOUVELLES TABLES

### demandes_documents
```php
Schema::create('demandes_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('agent_id')->constrained()->onDelete('cascade');
    $table->enum('type_document', ['attestation_travail', 'certificat_travail', 'ordre_mission']);
    $table->text('motif')->nullable();
    $table->enum('statut', ['en_attente', 'en_cours', 'pret', 'rejete'])->default('en_attente');
    $table->foreignId('traite_par')->nullable()->constrained('users');
    $table->timestamp('date_traitement')->nullable();
    $table->string('fichier_genere')->nullable();
    $table->text('motif_rejet')->nullable();
    $table->timestamps();
});
```

### prises_en_charge
```php
Schema::create('prises_en_charge', function (Blueprint $table) {
    $table->id();
    $table->foreignId('agent_id')->constrained()->onDelete('cascade');
    $table->enum('beneficiaire', ['agent', 'conjoint', 'enfant']);
    $table->foreignId('conjoint_id')->nullable()->constrained();
    $table->foreignId('enfant_id')->nullable()->constrained();
    $table->string('etablissement_medical');
    $table->text('motif_medical');
    $table->decimal('montant_estime', 10, 2)->nullable();
    $table->enum('statut', ['en_attente', 'validee', 'validee_drh', 'rejetee'])->default('en_attente');
    $table->boolean('exceptionnelle')->default(false);
    $table->foreignId('validee_par')->nullable()->constrained('users');
    $table->foreignId('validee_drh_par')->nullable()->constrained('users');
    $table->timestamp('date_validation')->nullable();
    $table->string('attestation_generee')->nullable();
    $table->text('motif_rejet')->nullable();
    $table->timestamps();
});
```

### mouvements
```php
Schema::create('mouvements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('agent_id')->constrained()->onDelete('cascade');
    $table->enum('type', ['affectation', 'mutation', 'retour', 'depart']);
    $table->foreignId('service_origine_id')->nullable()->constrained('services');
    $table->foreignId('service_destination_id')->nullable()->constrained('services');
    $table->date('date_effet');
    $table->text('motif');
    $table->enum('statut', ['en_attente', 'valide_drh', 'effectue', 'annule'])->default('en_attente');
    $table->foreignId('cree_par')->constrained('users');
    $table->foreignId('valide_par')->nullable()->constrained('users');
    $table->string('decision_generee')->nullable();
    $table->timestamps();
});
```

## 🔑 COMPTES DE TEST

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin Système | admin@chnp.sn | password |
| DRH | drh@chnp.sn | password |
| Agent RH | rh@chnp.sn | password |
| Manager | manager@chnp.sn | password |
| Major | major@chnp.sn | password |
| Agent | agent@chnp.sn | password |

## 🎨 CHARTE GRAPHIQUE

### Couleurs
- Primary : `#0A4D8C` (Bleu médical)
- Secondary : `#1565C0` (Bleu sécurité)
- Success : `#10B981`
- Warning : `#F59E0B`
- Danger : `#EF4444`
- Info : `#3B82F6`

### Typographie
- Police : Inter (Google Fonts)
- Weights : 300, 400, 500, 600, 700

## ⚠️ RÈGLES CRITIQUES

### Confidentialité
1.  Chiffrement AES-256 pour données CRITIQUES
2.  Policies AVANT chaque action
3.  Masquage affichage données sensibles
4.  Audit trail TOUS les accès

### Intégrité
1.  Form Requests TOUTES entrées
2.  DB::transaction() opérations multi-tables
3.  Contraintes DB (FK, UNIQUE, NOT NULL)
4.  Audit Log immuable (Spatie)

### Disponibilité
1.  Eager loading (éviter N+1)
2.  Index DB optimisés
3.  Tests robustes (Feature + Unit)
4.  Backup quotidien chiffré

## 💡 NOTES CLAUDE CODE

### Context Window
Ton contexte se compacte automatiquement.
Ne t'arrête JAMAIS au milieu d'une tâche.

### Confirmations
Demande confirmation AVANT :
- Supprimer fichiers
- Modifier migrations existantes
- Commandes destructives

### Approche
1. Créer structure complète module
2. Tester au fur et à mesure
3. Documenter code
4. Générer tests

## 🎯 OBJECTIF SOUTENANCE

### Livrables
1.  10 modules COMPLETS et FONCTIONNELS
2.  6 rôles RBAC (Agent, Major, Manager, AgentRH, DRH, AdminSystème)
3.  Démonstration TRIADE CID
4.  Self-service Agent
5.  Tests (70% couverture)
6.  Documentation technique complète
7.  Code DÉFENDABLE
8.  0 bugs critiques

### Démonstration TRIADE CID

**Confidentialité** :
- Montrer chiffrement en base (phpMyAdmin)
- Démontrer RBAC 6 rôles (accès refusé si mauvais rôle)
- Audit trail complet

**Intégrité** :
- Montrer validation stricte
- Transactions (rollback si erreur)
- Audit log immuable

**Disponibilité** :
- Performance < 3s
- Tests passent tous
- Backup automatique

---

**MVP COMPLET : 10 MODULES + 6 RÔLES + TRIADE CID**