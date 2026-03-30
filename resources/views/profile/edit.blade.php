@extends('layouts.master')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('breadcrumb')
    <li><a href="{{ route('dashboard') }}" style="color:#1565C0;">Accueil</a></li>
    <li>Mon profil</li>
@endsection

@push('styles')
<style>
/* ── PROFIL : Variables ──────────────────────────────────── */
.prof-tab {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.45rem 1rem; border-radius: 8px; border: none;
    font-size: 0.8125rem; font-weight: 500; cursor: pointer;
    background: transparent; color: #6B7280;
    transition: background 150ms, color 150ms;
}
.prof-tab:hover { background: #F3F4F6; color: #374151; }
.prof-tab.tab-active {
    background: #EFF6FF; color: #1565C0; font-weight: 600;
}
[data-theme="dark"] .prof-tab { color: #8d96a0; }
[data-theme="dark"] .prof-tab:hover { background: #21262d; color: #e6edf3; }
[data-theme="dark"] .prof-tab.tab-active { background: rgba(88,166,255,0.15); color: #58a6ff; }

/* ── Champs & labels ──────────────────────────────────────── */
.pf-label {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.05em; color: #6B7280; margin-bottom: 4px;
}
.pf-value { font-size: 14px; font-weight: 500; }
.pf-readonly {
    background: #F9FAFB; border: 1px solid #E5E7EB;
    border-radius: 8px; padding: 0.5rem 0.75rem;
    font-size: 0.875rem; color: #374151; cursor: not-allowed;
}
[data-theme="dark"] .pf-label { color: #6e7681; }
[data-theme="dark"] .pf-value { color: #e6edf3; }
[data-theme="dark"] .pf-readonly {
    background: #161b22; border-color: #30363d; color: #8d96a0;
}

/* ── Badges ───────────────────────────────────────────────── */
.badge-role {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
}
.badge-admin   { background: #EDE9FE; color: #5B21B6; }
.badge-drh     { background: #FEF3C7; color: #92400E; }
.badge-rh      { background: #DBEAFE; color: #1E40AF; }
.badge-manager { background: #D1FAE5; color: #065F46; }
.badge-agent   { background: #F3F4F6; color: #374151; }
.badge-actif   { background: #D1FAE5; color: #065F46; }
.badge-inactif { background: #FEE2E2; color: #991B1B; }
.badge-suspendu{ background: #FEF3C7; color: #92400E; }
[data-theme="dark"] .badge-admin    { background: rgba(139,92,246,0.2); color: #a78bfa; }
[data-theme="dark"] .badge-drh     { background: rgba(245,158,11,0.2); color: #fbbf24; }
[data-theme="dark"] .badge-rh      { background: rgba(59,130,246,0.2); color: #93c5fd; }
[data-theme="dark"] .badge-manager { background: rgba(16,185,129,0.2); color: #34d399; }
[data-theme="dark"] .badge-agent   { background: rgba(107,114,128,0.2); color: #9ca3af; }
[data-theme="dark"] .badge-actif   { background: rgba(16,185,129,0.18); color: #34d399; }

/* ── Stat card (ancienneté, etc.) ─────────────────────────── */
.stat-pill {
    text-align: center; padding: 0.625rem 1.25rem;
    border-radius: 10px; background: rgba(10,77,140,0.06);
    border: 1px solid rgba(10,77,140,0.1);
}
.stat-pill .val { font-size: 18px; font-weight: 700; color: #0A4D8C; line-height: 1; }
.stat-pill .lbl { font-size: 10px; font-weight: 500; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }
[data-theme="dark"] .stat-pill { background: rgba(88,166,255,0.08); border-color: rgba(88,166,255,0.15); }
[data-theme="dark"] .stat-pill .val { color: #58a6ff; }

/* ── Encrypted badge ──────────────────────────────────────── */
.enc-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; padding: 2px 8px; border-radius: 12px;
    background: rgba(245,158,11,0.12); color: #D97706; font-weight: 600;
}
[data-theme="dark"] .enc-badge { background: rgba(245,158,11,0.2); color: #fbbf24; }

/* ── Mot de passe : indicateur force ─────────────────────── */
.pwd-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: width 300ms, background 300ms; }

/* ── Alert success ────────────────────────────────────────── */
.alert-sirh {
    padding: 0.75rem 1rem; border-radius: 8px;
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.875rem; font-weight: 500;
}
.alert-sirh.success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
.alert-sirh.error   { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
[data-theme="dark"] .alert-sirh.success { background: rgba(16,185,129,0.15); color: #34d399; border-color: rgba(16,185,129,0.3); }
[data-theme="dark"] .alert-sirh.error   { background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.3); }

/* ── Section divider ─────────────────────────────────────── */
.pf-section-title {
    font-size: 13px; font-weight: 600; color: #374151;
    display: flex; align-items: center; gap: 8px;
    padding-bottom: 10px; border-bottom: 1px solid #E5E7EB; margin-bottom: 16px;
}
.pf-section-title i { color: #0A4D8C; }
[data-theme="dark"] .pf-section-title { color: #e6edf3; border-bottom-color: #30363d; }

/* ── Security tip box ────────────────────────────────────── */
.security-tip {
    background: #F0F7FF; border-radius: 10px; padding: 1rem;
    border-left: 3px solid #1565C0; margin-bottom: 0.75rem;
}
[data-theme="dark"] .security-tip { background: rgba(88,166,255,0.08); border-left-color: #58a6ff; }
</style>
@endpush

@section('content')

@php
    $agent = $user->agent;
    $role  = $user->getRoleNames()->first() ?? 'Agent';

    /* Libellé + badge CSS du rôle */
    $roleLabels = [
        'AdminSystème' => ['Administrateur Système', 'badge-admin',   'fa-user-shield'],
        'DRH'          => ['Directeur RH',           'badge-drh',     'fa-crown'],
        'AgentRH'      => ['Agent RH',               'badge-rh',      'fa-user-tie'],
        'Manager'      => ['Manager',                'badge-manager', 'fa-users-cog'],
        'Agent'        => ['Agent',                  'badge-agent',   'fa-user'],
    ];
    [$roleLabel, $roleBadgeClass, $roleIcon] = $roleLabels[$role] ?? ['Utilisateur', 'badge-agent', 'fa-user'];

    /* Statut compte */
    $statutCompte  = strtolower($user->statut_compte ?? 'actif');
    $statutLabel   = match($statutCompte) { 'actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu', default => ucfirst($statutCompte) };
    $statutBadge   = match($statutCompte) { 'actif' => 'badge-actif', 'suspendu' => 'badge-suspendu', default => 'badge-inactif' };
    $statutIcon    = match($statutCompte) { 'actif' => 'fa-circle text-success', 'suspendu' => 'fa-ban text-warning', default => 'fa-times-circle text-danger' };

    /* Ancienneté */
    $anciennete = null;
    if ($agent && $agent->date_prise_service) {
        $anciennete = $agent->date_prise_service->diffInYears(now());
    }

    /* Onglet actif selon la session (ex: retour après changement mdp) */
    $initTab = session('tab', 'compte');
    if (session('status') === 'password-updated') $initTab = 'securite';
    if ($errors->updatePassword->any()) $initTab = 'securite';

    /* Initiales (si pas d'agent) */
    $initiales = $agent
        ? strtoupper(substr($agent->prenom ?? 'U', 0, 1) . substr($agent->nom ?? 'S', 0, 1))
        : strtoupper(substr($user->login ?? 'U', 0, 1));
@endphp

<div x-data="{ tab: '{{ $initTab }}' }">

{{-- ══════════════════════════════════════════════════════════════
     HERO HEADER
══════════════════════════════════════════════════════════════ --}}
<div class="panel mb-4">
    <div class="p-4">
        <div class="d-flex align-items-start gap-4 flex-wrap">

            {{-- Avatar ──────────────────────────────────────── --}}
            @if($agent && $agent->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($agent->photo))
                <img src="{{ \Illuminate\Support\Facades\Storage::url($agent->photo) }}"
                     alt="{{ $agent->nomComplet }}"
                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #E5E7EB;flex-shrink:0;">
            @else
                @php
                    $avatarColors = ['#0A4D8C','#1565C0','#10B981','#7C3AED','#DB2777','#0891B2'];
                    $avatarBg = $avatarColors[ord($initiales[0]) % count($avatarColors)];
                @endphp
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,{{ $avatarBg }},{{ $avatarBg }}cc);display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:700;flex-shrink:0;user-select:none;border:3px solid rgba(255,255,255,0.3);">
                    {{ $initiales }}
                </div>
            @endif

            {{-- Infos principales ───────────────────────────── --}}
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-700" style="font-size:20px;">
                    {{ $agent ? $agent->nomComplet : ($user->name ?? $user->login) }}
                </h4>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    {{-- Login --}}
                    <code style="font-size:12px;background:rgba(10,77,140,0.08);color:#0A4D8C;padding:2px 8px;border-radius:6px;">
                        <i class="fas fa-at" style="font-size:10px;"></i> {{ $user->login }}
                    </code>
                    {{-- Rôle --}}
                    <span class="badge-role {{ $roleBadgeClass }}">
                        <i class="fas {{ $roleIcon }}" style="font-size:10px;"></i>
                        {{ $roleLabel }}
                    </span>
                    {{-- Matricule --}}
                    @if($agent)
                        <code style="font-size:12px;background:rgba(16,185,129,0.08);color:#059669;padding:2px 8px;border-radius:6px;">
                            <i class="fas fa-id-badge" style="font-size:10px;"></i> {{ $agent->matricule }}
                        </code>
                    @endif
                    {{-- Statut compte --}}
                    <span class="badge-role {{ $statutBadge }}">
                        <i class="fas {{ $statutIcon }}" style="font-size:7px;"></i>
                        {{ $statutLabel }}
                    </span>
                </div>
                <div style="font-size:12px;color:#6B7280;">
                    @if($user->email)
                        <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                        <span class="mx-2">·</span>
                    @endif
                    @if($user->derniere_connexion)
                        <i class="fas fa-clock me-1"></i>Dernière connexion : {{ $user->derniere_connexion->diffForHumans() }}
                    @else
                        <i class="fas fa-clock me-1"></i>Première connexion
                    @endif
                </div>
            </div>

            {{-- Statistiques rapides (si agent) ────────────── --}}
            @if($agent)
            <div class="d-none d-lg-flex gap-3 ms-auto flex-shrink-0">
                @if($anciennete !== null)
                <div class="stat-pill">
                    <div class="val">{{ $anciennete }}<span style="font-size:12px;font-weight:400;">ans</span></div>
                    <div class="lbl">Ancienneté</div>
                </div>
                @endif
                @if($agent->enfants->count() || $agent->conjoints->count())
                <div class="stat-pill">
                    <div class="val">{{ $agent->enfants->count() + $agent->conjoints->count() }}</div>
                    <div class="lbl">Personnes à charge</div>
                </div>
                @endif
                @if($agent->contrats->count())
                <div class="stat-pill">
                    <div class="val">{{ $agent->contrats->count() }}</div>
                    <div class="lbl">Contrat(s)</div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Navigation par onglets ──────────────────────────── --}}
        <div class="mt-3 pt-3 d-flex gap-1 flex-wrap" style="border-top:1px solid #E5E7EB;">
            <button @click="tab = 'compte'"
                    :class="tab === 'compte' ? 'prof-tab tab-active' : 'prof-tab'">
                <i class="fas fa-user-circle"></i>Mon compte
            </button>
            @if($agent)
            <button @click="tab = 'rh'"
                    :class="tab === 'rh' ? 'prof-tab tab-active' : 'prof-tab'">
                <i class="fas fa-id-card"></i>Dossier RH
            </button>
            @endif
            <button @click="tab = 'securite'"
                    :class="tab === 'securite' ? 'prof-tab tab-active' : 'prof-tab'">
                <i class="fas fa-shield-alt"></i>Sécurité
            </button>
            <div class="ms-auto d-flex align-items-center">
                <a href="{{ route('preferences.index') }}" class="prof-tab" style="text-decoration:none;">
                    <i class="fas fa-sliders-h"></i>Préférences
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB : MON COMPTE
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'compte'" x-cloak>

    {{-- Alertes session --}}
    @if(session('status') === 'profile-updated')
    <div class="alert-sirh success mb-4" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
        <i class="fas fa-check-circle"></i>
        Profil mis à jour avec succès.
    </div>
    @endif
    @if($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
    <div class="alert-sirh error mb-4">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <div class="row g-4">
        {{-- Informations du compte ──────────────────────── --}}
        <div class="col-lg-6">
            <div class="panel p-4 h-100">
                <div class="pf-section-title">
                    <i class="fas fa-user-circle"></i> Informations du compte
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    {{-- Identifiant (read-only) --}}
                    <div class="mb-3">
                        <label class="form-label pf-label">Identifiant de connexion</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="pf-readonly flex-grow-1">
                                <i class="fas fa-at me-1" style="color:#0A4D8C;"></i>{{ $user->login }}
                            </div>
                            <span title="Géré par l'administrateur" style="cursor:help;">
                                <i class="fas fa-lock" style="color:#9CA3AF;font-size:13px;"></i>
                            </span>
                        </div>
                        <small class="text-muted" style="font-size:11px;">
                            L'identifiant ne peut être modifié que par l'administrateur système.
                        </small>
                    </div>

                    {{-- Email (éditable) --}}
                    <div class="mb-3">
                        <label for="email" class="form-label pf-label">Adresse email <span style="font-size:10px;font-weight:400;text-transform:none;color:#9CA3AF;">(optionnelle)</span></label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="votre@email.sn"
                               autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" style="font-size:11px;">
                            Utilisé pour les notifications du système.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Enregistrer
                    </button>
                </form>
            </div>
        </div>

        {{-- Statut & Informations du compte ────────────── --}}
        <div class="col-lg-6">
            <div class="panel p-4 h-100">
                <div class="pf-section-title">
                    <i class="fas fa-info-circle"></i> État du compte
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="pf-label">Statut</div>
                        <span class="badge-role {{ $statutBadge }}">
                            <i class="fas {{ $statutIcon }}" style="font-size:8px;"></i>
                            {{ $statutLabel }}
                        </span>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Rôle</div>
                        <span class="badge-role {{ $roleBadgeClass }}">
                            <i class="fas {{ $roleIcon }}" style="font-size:10px;"></i>
                            {{ $roleLabel }}
                        </span>
                    </div>
                    <div class="col-12">
                        <div class="pf-label">Membre depuis</div>
                        <div class="pf-value">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="pf-label">Dernière connexion</div>
                        <div class="pf-value">
                            {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y à H:i') : 'Première session' }}
                        </div>
                    </div>
                    @if($user->tentatives_connexion > 0)
                    <div class="col-12">
                        <div class="pf-label">Tentatives de connexion échouées</div>
                        <div class="pf-value" style="color:#D97706;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $user->tentatives_connexion }} tentative(s) échouée(s)
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Liens rapides ───────────────────────────────── --}}
        <div class="col-12">
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-rocket"></i> Accès rapides
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('preferences.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sliders-h me-2"></i>Préférences
                    </a>
                    <a href="{{ route('aide.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-life-ring me-2"></i>Aide & FAQ
                    </a>
                    @if($agent && auth()->user()->hasRole('Agent'))
                    <a href="{{ route('agent.profil') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-id-card me-2"></i>Mon dossier complet
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('AdminSystème'))
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-users-cog me-2"></i>Gestion des comptes
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     TAB : DOSSIER RH (si agent associé)
══════════════════════════════════════════════════════════════ --}}
@if($agent)
<div x-show="tab === 'rh'" x-cloak>
    <div class="row g-4">

        {{-- Informations personnelles ────────────────────── --}}
        <div class="col-lg-6">
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-user"></i> Informations personnelles
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="pf-label">Nom</div>
                        <div class="pf-value">{{ $agent->nom }}</div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Prénom</div>
                        <div class="pf-value">{{ $agent->prenom }}</div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Date de naissance</div>
                        <div class="pf-value">{{ $agent->date_naissance?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Lieu de naissance</div>
                        <div class="pf-value">{{ $agent->lieu_naissance ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Sexe</div>
                        <div class="pf-value">{{ $agent->sexe === 'M' ? 'Masculin' : 'Féminin' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Situation familiale</div>
                        <div class="pf-value">{{ $agent->situation_familiale ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="pf-label">Nationalité</div>
                        <div class="pf-value">{{ $agent->nationalite ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Coordonnées (données protégées) ──────────────── --}}
        <div class="col-lg-6">
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-address-card"></i> Coordonnées
                    <span class="enc-badge ms-2"><i class="fas fa-lock"></i>AES-256</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="pf-label">Téléphone</div>
                        <div class="pf-value">
                            @if($agent->telephone)
                                <i class="fas fa-lock text-warning me-1" style="font-size:11px;" title="Chiffré AES-256"></i>
                                <code style="font-size:13px;">{{ $agent->telephone_masque }}</code>
                            @else
                                <span style="color:#9CA3AF;">Non renseigné</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="pf-label">Email professionnel</div>
                        <div class="pf-value">{{ $agent->email ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="pf-label">Adresse</div>
                        <div class="pf-value">
                            @if($agent->adresse)
                                <i class="fas fa-lock text-warning me-1" style="font-size:11px;" title="Chiffré AES-256"></i>
                                {{ $agent->adresse_masquee }}
                            @else
                                <span style="color:#9CA3AF;">Non renseignée</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-2 rounded" style="background:rgba(245,158,11,0.06);border:1px dashed #F59E0B;font-size:11px;color:#D97706;">
                    <i class="fas fa-shield-alt me-1"></i>
                    Les données sensibles sont chiffrées en base via AES-256 (Pilier Confidentialité – Triade CID).
                </div>
            </div>
        </div>

        {{-- Informations professionnelles ──────────────── --}}
        <div class="col-12">
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-briefcase"></i> Informations professionnelles
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Matricule</div>
                        <div class="pf-value">
                            <code style="font-size:13px;color:#0A4D8C;">{{ $agent->matricule }}</code>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Date de recrutement</div>
                        <div class="pf-value">{{ $agent->date_prise_service?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Fonction</div>
                        <div class="pf-value">{{ $agent->fontion ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Grade</div>
                        <div class="pf-value">{{ $agent->grade ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Catégorie</div>
                        <div class="pf-value">{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Service</div>
                        <div class="pf-value">{{ $agent->service?->nom_service ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Division</div>
                        <div class="pf-value">{{ $agent->division?->nom_division ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Statut agent</div>
                        <div class="pf-value">
                            @php $st = strtolower($agent->statut ?? 'actif'); @endphp
                            @if($st === 'actif')
                                <span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                                    <i class="fas fa-circle me-1" style="font-size:7px;"></i>Actif
                                </span>
                            @elseif($st === 'en_conge')
                                <span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                                    <i class="fas fa-umbrella-beach me-1"></i>En congé
                                </span>
                            @elseif($st === 'suspendu')
                                <span style="background:#FEE2E2;color:#991B1B;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                                    <i class="fas fa-ban me-1"></i>Suspendu
                                </span>
                            @else
                                <span style="background:#F3F4F6;color:#374151;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                                    {{ $agent->statut }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($agent->contratActif)
                    <div class="col-6 col-md-3">
                        <div class="pf-label">Type contrat</div>
                        <div class="pf-value">{{ $agent->contratActif->type_contrat ?? '—' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Famille ───────────────────────────────────────── --}}
        @if($agent->conjoints->count() || $agent->enfants->count())
        <div class="col-12">
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-users"></i> Situation familiale
                    @if(auth()->user()->hasRole('Agent'))
                    <a href="{{ route('agent.famille') }}" class="ms-auto btn btn-outline-primary btn-sm" style="font-size:11px;">
                        Voir le détail
                    </a>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($agent->conjoints as $conjoint)
                    <div style="display:flex;align-items:center;gap:8px;background:rgba(10,77,140,0.06);padding:8px 14px;border-radius:20px;">
                        <i class="fas fa-ring" style="color:#0A4D8C;font-size:12px;"></i>
                        <span style="font-size:13px;font-weight:500;">{{ $conjoint->prenom }} {{ $conjoint->nom }}</span>
                        <span style="font-size:11px;color:#6B7280;">Conjoint(e)</span>
                    </div>
                    @endforeach
                    @foreach($agent->enfants as $enfant)
                    <div style="display:flex;align-items:center;gap:8px;background:rgba(16,185,129,0.06);padding:8px 14px;border-radius:20px;">
                        <i class="fas fa-child" style="color:#059669;font-size:12px;"></i>
                        <span style="font-size:13px;font-weight:500;">{{ $enfant->prenom }} {{ $enfant->nom }}</span>
                        <span style="font-size:11px;color:#6B7280;">Enfant</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════
     TAB : SÉCURITÉ
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'securite'" x-cloak>

    {{-- Alerte mot de passe mis à jour --}}
    @if(session('status') === 'password-updated')
    <div class="alert-sirh success mb-4" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
        <i class="fas fa-check-circle"></i>
        Mot de passe modifié avec succès.
    </div>
    @endif

    <div class="row g-4">

        {{-- Formulaire changement de mot de passe ────────── --}}
        <div class="col-lg-6">
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-key"></i> Changer le mot de passe
                </div>

                <form method="POST" action="{{ route('password.update') }}" id="passwordForm">
                    @csrf
                    @method('PUT')

                    {{-- Mot de passe actuel --}}
                    <div class="mb-3">
                        <label for="current_password" class="form-label pf-label">Mot de passe actuel</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePwd('current_password')" tabindex="-1"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;">
                                <i class="fas fa-eye" id="eye_current_password"></i>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nouveau mot de passe --}}
                    <div class="mb-1">
                        <label for="password" class="form-label pf-label">Nouveau mot de passe</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   oninput="checkStrength(this.value)">
                            <button type="button" onclick="togglePwd('password')" tabindex="-1"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;">
                                <i class="fas fa-eye" id="eye_password"></i>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                            <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Indicateur de force --}}
                    <div class="mb-3">
                        <div style="height:4px;background:#E5E7EB;border-radius:2px;overflow:hidden;">
                            <div id="strengthBar" class="pwd-strength" style="width:0%;background:#EF4444;height:100%;"></div>
                        </div>
                        <div id="strengthLabel" style="font-size:11px;color:#9CA3AF;margin-top:4px;"></div>
                    </div>

                    {{-- Confirmation --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label pf-label">Confirmer le mot de passe</label>
                        <div style="position:relative;">
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePwd('password_confirmation')" tabindex="-1"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;">
                                <i class="fas fa-eye" id="eye_password_confirmation"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>

        {{-- Conseils de sécurité + audit ───────────────── --}}
        <div class="col-lg-6">

            {{-- Politique de mot de passe --}}
            <div class="panel p-4 mb-4">
                <div class="pf-section-title">
                    <i class="fas fa-shield-check"></i> Politique de sécurité
                </div>
                <div class="security-tip">
                    <div style="font-size:12px;font-weight:600;color:#1565C0;margin-bottom:6px;">
                        <i class="fas fa-check-circle me-1"></i>Exigences du mot de passe
                    </div>
                    <ul style="margin:0;padding-left:16px;font-size:12px;color:#374151;">
                        <li>Minimum 8 caractères</li>
                        <li>Au moins une majuscule (A-Z)</li>
                        <li>Au moins une minuscule (a-z)</li>
                        <li>Au moins un chiffre (0-9)</li>
                        <li>Au moins un caractère spécial (!@#$%...)</li>
                    </ul>
                </div>
                <div class="security-tip" style="border-left-color:#10B981;background:#F0FDF4;">
                    <div style="font-size:12px;font-weight:600;color:#059669;margin-bottom:4px;">
                        <i class="fas fa-lightbulb me-1"></i>Bonnes pratiques
                    </div>
                    <ul style="margin:0;padding-left:16px;font-size:12px;color:#374151;">
                        <li>Changez votre mot de passe tous les 90 jours</li>
                        <li>N'utilisez pas le même mot de passe ailleurs</li>
                        <li>Ne partagez jamais vos identifiants</li>
                    </ul>
                </div>
            </div>

            {{-- Activité du compte --}}
            <div class="panel p-4">
                <div class="pf-section-title">
                    <i class="fas fa-history"></i> Activité du compte
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="pf-label">Dernière connexion</div>
                        <div class="pf-value" style="font-size:13px;">
                            {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Tentatives échouées</div>
                        <div class="pf-value">
                            @if($user->tentatives_connexion > 0)
                                <span style="color:#D97706;font-weight:600;">{{ $user->tentatives_connexion }}</span>
                            @else
                                <span style="color:#10B981;font-weight:600;">0</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Statut du compte</div>
                        <div class="pf-value">
                            <span class="badge-role {{ $statutBadge }}" style="font-size:11px;">{{ $statutLabel }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pf-label">Verrouillage</div>
                        <div class="pf-value">
                            @if($user->verouille)
                                <span style="color:#EF4444;font-size:12px;">
                                    <i class="fas fa-lock me-1"></i>Verrouillé
                                </span>
                            @else
                                <span style="color:#10B981;font-size:12px;">
                                    <i class="fas fa-lock-open me-1"></i>Normal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- /x-data --}}

@endsection

@push('scripts')
<script>
/* ── Afficher/masquer mot de passe ──────────────────────── */
function togglePwd(id) {
    const input = document.getElementById(id);
    const icon  = document.getElementById('eye_' + id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

/* ── Indicateur de force du mot de passe ──────────────── */
function checkStrength(pwd) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    if (!bar) return;

    let score = 0;
    if (pwd.length >= 8)                                    score++;
    if (/[A-Z]/.test(pwd))                                  score++;
    if (/[a-z]/.test(pwd))                                  score++;
    if (/\d/.test(pwd))                                     score++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(pwd))                score++;

    const levels = [
        { pct: '20%', color: '#EF4444', text: 'Très faible' },
        { pct: '40%', color: '#F59E0B', text: 'Faible' },
        { pct: '60%', color: '#F59E0B', text: 'Moyen' },
        { pct: '80%', color: '#10B981', text: 'Fort' },
        { pct: '100%',color: '#10B981', text: 'Très fort ✓' },
    ];
    const lvl = levels[Math.max(0, score - 1)];
    if (pwd.length === 0) {
        bar.style.width = '0%';
        label.textContent = '';
        return;
    }
    bar.style.width = lvl.pct;
    bar.style.background = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;
}
</script>
@endpush
