@extends('layouts.master')

@section('title', 'Dossier — '.$agent->nom_complet)
@section('page-title', 'Dossier Agent')

@section('breadcrumb')
    <li><a href="{{ route('rh.agents.index') }}" style="color:#1565C0;">Personnel</a></li>
    <li>{{ $agent->matricule }}</li>
@endsection

@push('styles')
<style>
/* ════════════════════════════════════════
   HERO PROFIL
   ════════════════════════════════════════ */
.profile-hero {
    background: linear-gradient(135deg, #0A4D8C 0%, #1565C0 50%, #1976D2 100%);
    border-radius: 20px;
    padding: 32px 36px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
    box-shadow: 0 15px 50px rgba(10, 77, 140, 0.35);
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.profile-hero::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: 35%;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.profile-avatar-hero {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,.45);
    box-shadow: 0 8px 25px rgba(0,0,0,.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    overflow: hidden;
    background: rgba(255,255,255,.22);
}
.profile-avatar-hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-badge-statut {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    padding: 6px 14px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    backdrop-filter: blur(4px);
}
.profile-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: rgba(255,255,255,.9);
}
.hero-stats {
    display: flex;
    gap: 40px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.18);
}
.hero-stat {
    text-align: center;
}
.hero-stat-val {
    font-size: 24px;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}
.hero-stat-label {
    font-size: 12px;
    color: rgba(255,255,255,.75);
    margin-top: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* ════════════════════════════════════════
   ACTION BUTTONS
   ════════════════════════════════════════ */
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 180ms;
    white-space: nowrap;
}
.action-btn-primary {
    background: rgba(255,255,255,.2);
    color: #fff;
    border: 1px solid rgba(255,255,255,.35);
    backdrop-filter: blur(4px);
}
.action-btn-primary:hover {
    background: rgba(255,255,255,.3);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,.2);
}
.action-btn-outline {
    background: rgba(255,255,255,.12);
    color: #fff;
    border: 1px solid rgba(255,255,255,.25);
}
.action-btn-outline:hover {
    background: rgba(255,255,255,.2);
    color: #fff;
}

/* ════════════════════════════════════════
   TABS NAVIGATION
   ════════════════════════════════════════ */
.tabs-container {
    background: var(--theme-panel-bg);
    border-radius: 16px;
    border: 1px solid var(--theme-border);
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.sirh-tabs {
    display: flex;
    padding: 0 24px;
    background: var(--theme-bg-secondary);
    border-bottom: 2px solid var(--theme-border);
    overflow-x: auto;
}
.sirh-tab-btn {
    padding: 16px 20px;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: var(--theme-text-muted);
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 150ms;
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}
.sirh-tab-btn:hover:not(.active) {
    color: var(--theme-text);
    background: rgba(10,77,140,.04);
}
.sirh-tab-btn.active {
    color: #0A4D8C;
    border-bottom-color: #0A4D8C;
    font-weight: 700;
}
.sirh-tab-badge {
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    background: var(--theme-bg-secondary);
    color: var(--theme-text-muted);
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 7px;
}
.sirh-tab-btn.active .sirh-tab-badge {
    background: #EFF6FF;
    color: #0A4D8C;
}

/* ════════════════════════════════════════
   PANELS & CARDS
   ════════════════════════════════════════ */
.info-panel {
    background: var(--theme-panel-bg);
    border-radius: 16px;
    border: 1px solid var(--theme-border);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    margin-bottom: 20px;
}
.info-panel-header {
    padding: 18px 24px;
    background: var(--theme-bg-secondary);
    border-bottom: 1px solid var(--theme-border);
    display: flex;
    align-items: center;
    gap: 12px;
}
.info-panel-header .header-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.info-panel-header h6 {
    font-size: 14px;
    font-weight: 700;
    color: var(--theme-text);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.info-panel-body {
    padding: 24px;
}

/* ════════════════════════════════════════
   INFO ROWS
   ════════════════════════════════════════ */
.info-row {
    display: flex;
    align-items: flex-start;
    padding: 14px 0;
    border-bottom: 1px solid var(--theme-border);
}
.info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.info-row:first-child {
    padding-top: 0;
}
.info-label {
    width: 40%;
    font-size: 13px;
    font-weight: 600;
    color: var(--theme-text-muted);
    flex-shrink: 0;
}
.info-value {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: var(--theme-text);
}

/* ════════════════════════════════════════
   SENSITIVE DATA + BOUTONS DÉCHIFFRER
   ════════════════════════════════════════ */
.sensitive-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    background: #FEE2E2;
    color: #991B1B;
    padding: 3px 10px;
    border-radius: 12px;
    font-weight: 700;
}
.masked-value {
    letter-spacing: 3px;
    color: var(--theme-text-muted);
    font-family: monospace;
}
.btn-decrypt-show {
    background: linear-gradient(135deg, #D97706, #F59E0B);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 150ms;
    margin-left: 12px;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
}
.btn-decrypt-show:hover {
    background: linear-gradient(135deg, #B45309, #D97706);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.4);
    transform: translateY(-1px);
}
.btn-decrypt-show.revealed {
    background: linear-gradient(135deg, #059669, #10B981);
    box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
}
.sensitive-revealed {
    color: var(--theme-text);
    font-weight: 600;
}

/* ════════════════════════════════════════
   FAMILLE CARDS
   ════════════════════════════════════════ */
.famille-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 18px;
    border-radius: 14px;
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    margin-bottom: 12px;
    transition: all 150ms;
}
.famille-card:hover {
    border-color: rgba(10,77,140,.2);
    box-shadow: 0 4px 12px rgba(0,0,0,.06);
}
.famille-card:last-child {
    margin-bottom: 0;
}
.famille-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.famille-info h6 {
    font-size: 14px;
    font-weight: 700;
    color: var(--theme-text);
    margin: 0 0 3px 0;
}
.famille-info span {
    font-size: 12px;
    color: var(--theme-text-muted);
}

/* ════════════════════════════════════════
   CONTRATS & TIMELINE
   ════════════════════════════════════════ */
.contrat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 0;
    border-bottom: 1px solid var(--theme-border);
    gap: 16px;
}
.contrat-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.contrat-row:first-child {
    padding-top: 0;
}
.contrat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #EFF6FF;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.contrat-icon i {
    font-size: 18px;
    color: #0A4D8C;
}
.contrat-info h6 {
    font-size: 14px;
    font-weight: 700;
    color: var(--theme-text);
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.contrat-info span {
    font-size: 13px;
    color: var(--theme-text-muted);
}
.timeline-item {
    display: flex;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid var(--theme-border);
}
.timeline-item:last-child {
    border-bottom: none;
}
.timeline-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #0A4D8C;
    flex-shrink: 0;
    margin-top: 4px;
    box-shadow: 0 0 0 4px rgba(10,77,140,.15);
}

/* ════════════════════════════════════════
   BADGES
   ════════════════════════════════════════ */
.badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.bp-actif { background: #D1FAE5; color: #065F46; }
.bp-conge { background: #FEF3C7; color: #92400E; }
.bp-suspendu { background: #FEE2E2; color: #991B1B; }
.bp-retraite { background: #F3F4F6; color: #374151; }

/* ════════════════════════════════════════
   EMPTY STATES
   ════════════════════════════════════════ */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}
.empty-state i {
    font-size: 48px;
    color: #E5E7EB;
    margin-bottom: 16px;
}
.empty-state p {
    font-size: 14px;
    color: var(--theme-text-muted);
    margin: 0;
}

/* ════════════════════════════════════════
   DARK MODE
   ════════════════════════════════════════ */
[data-theme="dark"] .profile-hero {
    background: linear-gradient(135deg, #0d1117 0%, #0a4d8c 100%);
}
[data-theme="dark"] .tabs-container,
[data-theme="dark"] .info-panel {
    background: #161b22;
    border-color: #30363d;
}
[data-theme="dark"] .sirh-tabs {
    background: #0d1117;
    border-bottom-color: #30363d;
}
[data-theme="dark"] .info-panel-header {
    background: #0d1117;
    border-bottom-color: #30363d;
}
[data-theme="dark"] .famille-card {
    background: #0d1117;
    border-color: #30363d;
}
[data-theme="dark"] .bp-actif { background: rgba(16,185,129,.18); color: #34d399; }
[data-theme="dark"] .bp-conge { background: rgba(245,158,11,.18); color: #fbbf24; }
[data-theme="dark"] .bp-suspendu { background: rgba(239,68,68,.18); color: #f87171; }
[data-theme="dark"] .bp-retraite { background: rgba(107,114,128,.18); color: #9ca3af; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;font-size:14px;padding:16px 20px;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     HERO PROFIL
     ═══════════════════════════════════════════════════════════ --}}
<div class="profile-hero">
    <div class="d-flex align-items-start gap-4 flex-wrap" style="position:relative;z-index:1;">

        <div class="profile-avatar-hero">
            @if($agent->photo)
                <img src="{{ asset('storage/'.$agent->photo) }}" alt="{{ $agent->nom_complet }}">
            @else
                {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
            @endif
        </div>

        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                <h4 class="mb-0" style="font-size:26px;font-weight:800;color:#fff;">
                    {{ $agent->prenom }} {{ $agent->nom }}
                </h4>
                @php
                    $bpClass = match($agent->statut) {
                        'Actif'    => 'fa-circle-check',
                        'En_congé' => 'fa-umbrella-beach',
                        'Suspendu' => 'fa-ban',
                        'Retraité' => 'fa-door-open',
                        default    => 'fa-circle',
                    };
                    $statutLabel = $agent->statut === 'En_congé' ? 'En congé' : $agent->statut;
                @endphp
                <span class="profile-badge-statut">
                    <i class="fas {{ $bpClass }}" style="font-size:11px;"></i>
                    {{ $statutLabel }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-4 flex-wrap mb-4">
                <div class="profile-meta">
                    <i class="fas fa-id-badge"></i>
                    <strong style="letter-spacing:1px;">{{ $agent->matricule }}</strong>
                </div>
                @if($agent->fonction)
                <div class="profile-meta">
                    <i class="fas fa-stethoscope"></i>
                    {{ $agent->fonction }}
                </div>
                @endif
                @if($agent->service)
                <div class="profile-meta">
                    <i class="fas fa-building-columns"></i>
                    {{ $agent->service->nom_service }}
                </div>
                @endif
            </div>

            <div class="d-flex gap-3 flex-wrap">
                @can('update', $agent)
                <a href="{{ route('rh.agents.edit', $agent->id_agent) }}" class="action-btn action-btn-primary">
                    <i class="fas fa-pen"></i> Modifier le dossier
                </a>
                @endcan
                <a href="{{ route('rh.agents.index') }}" class="action-btn action-btn-outline">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>

        <div class="d-none d-xl-flex flex-column gap-3" style="position:relative;z-index:1;">
            @if($agent->grade)
            <div style="background:rgba(255,255,255,.15);border-radius:12px;padding:14px 20px;text-align:center;backdrop-filter:blur(4px);">
                <div style="font-size:20px;font-weight:800;color:#fff;">{{ $agent->grade }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.75);margin-top:2px;">Grade</div>
            </div>
            @endif
            @if($agent->date_recrutement)
            <div style="background:rgba(255,255,255,.15);border-radius:12px;padding:14px 20px;text-align:center;backdrop-filter:blur(4px);">
                <div style="font-size:20px;font-weight:800;color:#fff;">{{ $agent->date_recrutement->diffInYears(now()) }} ans</div>
                <div style="font-size:11px;color:rgba(255,255,255,.75);margin-top:2px;">Ancienneté</div>
            </div>
            @endif
        </div>
    </div>

    <div class="hero-stats" style="position:relative;z-index:1;">
        <div class="hero-stat">
            <div class="hero-stat-val">{{ $agent->contrats->count() }}</div>
            <div class="hero-stat-label"><i class="fas fa-file-contract"></i> Contrat(s)</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val">{{ $agent->enfants->count() }}</div>
            <div class="hero-stat-label"><i class="fas fa-child"></i> Enfant(s)</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-val">{{ $agent->mouvements->count() }}</div>
            <div class="hero-stat-label"><i class="fas fa-arrows-alt-h"></i> Mouvement(s)</div>
        </div>
        <div class="hero-stat d-none d-md-block">
            <div class="hero-stat-val">
                @if($agent->user_id)
                    <i class="fas fa-check-circle" style="color:#34d399;"></i>
                @else
                    <i class="fas fa-times-circle" style="color:#f87171;"></i>
                @endif
            </div>
            <div class="hero-stat-label"><i class="fas fa-user-circle"></i> Compte</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLETS
     ═══════════════════════════════════════════════════════════ --}}
<div x-data="showAgentData()">

    <div class="tabs-container">
        <div class="sirh-tabs">
            <button class="sirh-tab-btn" :class="{ active: tab==='profil' }" @click="tab='profil'">
                <i class="fas fa-user"></i> Profil
            </button>
            <button class="sirh-tab-btn" :class="{ active: tab==='famille' }" @click="tab='famille'">
                <i class="fas fa-users"></i> Famille
                @if($agent->enfants->count() + $agent->conjoints->count() > 0)
                <span class="sirh-tab-badge">{{ $agent->enfants->count() + $agent->conjoints->count() }}</span>
                @endif
            </button>
            <button class="sirh-tab-btn" :class="{ active: tab==='contrats' }" @click="tab='contrats'">
                <i class="fas fa-file-contract"></i> Contrats
                @if($agent->contrats->count() > 0)
                <span class="sirh-tab-badge">{{ $agent->contrats->count() }}</span>
                @endif
            </button>
            <button class="sirh-tab-btn" :class="{ active: tab==='mouvements' }" @click="tab='mouvements'">
                <i class="fas fa-arrows-alt-h"></i> Mouvements
                @if($agent->mouvements->count() > 0)
                <span class="sirh-tab-badge">{{ $agent->mouvements->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- ═══════════ ONGLET PROFIL ═══════════ --}}
    <div x-show="tab==='profil'" x-transition:enter="animate__animated animate__fadeIn animate__faster">
        <div class="row g-4">

            {{-- Identité civile --}}
            <div class="col-12 col-lg-6">
                <div class="info-panel">
                    <div class="info-panel-header">
                        <div class="header-icon" style="background:#EFF6FF;">
                            <i class="fas fa-id-card" style="color:#0A4D8C;"></i>
                        </div>
                        <h6>Identité civile</h6>
                    </div>
                    <div class="info-panel-body">
                        <div class="info-row">
                            <span class="info-label">Nom</span>
                            <span class="info-value" style="font-weight:700;">{{ $agent->nom }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Prénom</span>
                            <span class="info-value">{{ $agent->prenom }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date de naissance</span>
                            <span class="info-value">{{ $agent->date_naissance?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Lieu de naissance</span>
                            <span class="info-value">{{ $agent->lieu_naissance ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sexe</span>
                            <span class="info-value">
                                <i class="fas fa-{{ $agent->sexe === 'M' ? 'mars' : 'venus' }} me-2"
                                   style="color:{{ $agent->sexe === 'M' ? '#3B82F6' : '#EC4899' }};"></i>
                                {{ $agent->sexe === 'M' ? 'Masculin' : 'Féminin' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Situation familiale</span>
                            <span class="info-value">{{ $agent->situation_familiale ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nationalité</span>
                            <span class="info-value">{{ $agent->nationalite ?? 'Sénégalaise' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coordonnées sensibles --}}
            <div class="col-12 col-lg-6">
                <div class="info-panel">
                    <div class="info-panel-header">
                        <div class="header-icon" style="background:#FEE2E2;">
                            <i class="fas fa-lock" style="color:#DC2626;"></i>
                        </div>
                        <h6>Coordonnées</h6>
                        <span class="sensitive-badge ms-2">
                            <i class="fas fa-shield-halved" style="font-size:9px;"></i> AES-256
                        </span>
                    </div>
                    <div class="info-panel-body">
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">
                                @if($agent->email)
                                    <a href="mailto:{{ $agent->email }}" style="color:#0A4D8C;text-decoration:none;font-weight:500;">
                                        {{ $agent->email }}
                                    </a>
                                @else —
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                Téléphone
                                <i class="fas fa-lock ms-1" style="font-size:9px;color:#D97706;"></i>
                            </span>
                            <span class="info-value">
                                <span x-show="!revealed.telephone" class="masked-value">
                                    {{ $agent->telephone ? '●●● ●● ●●● ●●' : '—' }}
                                </span>
                                <span x-show="revealed.telephone" class="sensitive-revealed">
                                    {{ $agent->telephone ?? '—' }}
                                </span>
                                @if($agent->telephone)
                                <button type="button" class="btn-decrypt-show"
                                        :class="{ 'revealed': revealed.telephone }"
                                        @click="toggleReveal('telephone')">
                                    <i class="fas" :class="revealed.telephone ? 'fa-eye-slash' : 'fa-key'"></i>
                                    <span x-text="revealed.telephone ? 'Masquer' : 'Déchiffrer'"></span>
                                </button>
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                Adresse
                                <i class="fas fa-lock ms-1" style="font-size:9px;color:#D97706;"></i>
                            </span>
                            <span class="info-value">
                                <span x-show="!revealed.adresse" class="masked-value">
                                    {{ $agent->adresse ? '●●●●●●●●●●' : '—' }}
                                </span>
                                <span x-show="revealed.adresse" class="sensitive-revealed">
                                    {{ $agent->adresse ?? '—' }}
                                </span>
                                @if($agent->adresse)
                                <button type="button" class="btn-decrypt-show"
                                        :class="{ 'revealed': revealed.adresse }"
                                        @click="toggleReveal('adresse')">
                                    <i class="fas" :class="revealed.adresse ? 'fa-eye-slash' : 'fa-key'"></i>
                                    <span x-text="revealed.adresse ? 'Masquer' : 'Déchiffrer'"></span>
                                </button>
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                N° Assurance
                                <i class="fas fa-lock ms-1" style="font-size:9px;color:#D97706;"></i>
                            </span>
                            <span class="info-value">
                                <span x-show="!revealed.assurance" class="masked-value">
                                    {{ $agent->numero_assurance ? '●●●●●●●●' : '—' }}
                                </span>
                                <span x-show="revealed.assurance" class="sensitive-revealed">
                                    {{ $agent->numero_assurance ?? '—' }}
                                </span>
                                @if($agent->numero_assurance)
                                <button type="button" class="btn-decrypt-show"
                                        :class="{ 'revealed': revealed.assurance }"
                                        @click="toggleReveal('assurance')">
                                    <i class="fas" :class="revealed.assurance ? 'fa-eye-slash' : 'fa-key'"></i>
                                    <span x-text="revealed.assurance ? 'Masquer' : 'Déchiffrer'"></span>
                                </button>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Compte système --}}
                <div class="info-panel">
                    <div class="info-panel-header">
                        <div class="header-icon" style="background:#F3E8FF;">
                            <i class="fas fa-user-circle" style="color:#7C3AED;"></i>
                        </div>
                        <h6>Compte système</h6>
                    </div>
                    <div class="info-panel-body">
                        @if($agent->user)
                        <div class="info-row">
                            <span class="info-label">Login</span>
                            <span class="info-value">
                                <code style="font-size:14px;background:var(--theme-bg-secondary);padding:4px 10px;border-radius:6px;">{{ $agent->user->login }}</code>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Statut</span>
                            <span class="info-value">
                                @php $sc = $agent->user->statut_compte; @endphp
                                <span class="badge-pill {{ $sc === 'actif' ? 'bp-actif' : 'bp-suspendu' }}">
                                    <i class="fas fa-{{ $sc === 'actif' ? 'check-circle' : 'ban' }}" style="font-size:10px;"></i>
                                    {{ ucfirst($sc) }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dernière connexion</span>
                            <span class="info-value" style="font-size:13px;">
                                {{ $agent->user->derniere_connexion ? \Carbon\Carbon::parse($agent->user->derniere_connexion)->diffForHumans() : '—' }}
                            </span>
                        </div>
                        @else
                        <div class="empty-state" style="padding:30px;">
                            <i class="fas fa-user-clock" style="font-size:36px;"></i>
                            <p>Compte en attente de création</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Profil professionnel --}}
            <div class="col-12">
                <div class="info-panel">
                    <div class="info-panel-header">
                        <div class="header-icon" style="background:#EFF6FF;">
                            <i class="fas fa-briefcase" style="color:#0A4D8C;"></i>
                        </div>
                        <h6>Profil professionnel</h6>
                    </div>
                    <div class="info-panel-body">
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="info-row">
                                    <span class="info-label">Fonction</span>
                                    <span class="info-value">{{ $agent->fonction ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-row">
                                    <span class="info-label">Grade</span>
                                    <span class="info-value" style="font-weight:700;color:#0A4D8C;">{{ $agent->grade ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-row">
                                    <span class="info-label">Catégorie CSP</span>
                                    <span class="info-value" style="font-size:13px;">{{ str_replace('_',' ',$agent->categorie_cp ?? '—') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-row">
                                    <span class="info-label">Recrutement</span>
                                    <span class="info-value">{{ $agent->date_recrutement?->format('d/m/Y') ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-row">
                                    <span class="info-label">Service</span>
                                    <span class="info-value">{{ $agent->service?->nom_service ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-row">
                                    <span class="info-label">Division</span>
                                    <span class="info-value">{{ $agent->division?->nom_division ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Ancienneté</span>
                                    <span class="info-value">
                                        @if($agent->date_recrutement)
                                            <strong style="color:#0A4D8C;">{{ $agent->date_recrutement->diffInYears(now()) }} an(s)</strong>
                                            <span style="color:var(--theme-text-muted);font-size:12px;margin-left:6px;">
                                                depuis {{ $agent->date_recrutement->format('d/m/Y') }}
                                            </span>
                                        @else —
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════ ONGLET FAMILLE ═══════════ --}}
    <div x-show="tab==='famille'" x-transition:enter="animate__animated animate__fadeIn animate__faster">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="info-panel">
                    <div class="info-panel-header">
                        <div class="header-icon" style="background:#FEF3C7;">
                            <i class="fas fa-heart" style="color:#D97706;"></i>
                        </div>
                        <h6>Conjoint(e)</h6>
                    </div>
                    <div class="info-panel-body">
                        @forelse($agent->conjoints as $c)
                        <div class="famille-card">
                            <div class="famille-avatar" style="background:#FEF3C7;">
                                <i class="fas fa-heart" style="color:#D97706;"></i>
                            </div>
                            <div class="famille-info">
                                <h6>{{ $c->nom_complet }}</h6>
                                <span>
                                    {{ $c->type_lien }}
                                    @if($c->date_naissance_conj)
                                        · {{ $c->date_naissance_conj->format('d/m/Y') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-heart-crack"></i>
                            <p>Aucun conjoint enregistré</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="info-panel">
                    <div class="info-panel-header">
                        <div class="header-icon" style="background:#ECFDF5;">
                            <i class="fas fa-child" style="color:#059669;"></i>
                        </div>
                        <h6>Enfants</h6>
                        <span class="sirh-tab-badge ms-2">{{ $agent->enfants->count() }}</span>
                    </div>
                    <div class="info-panel-body">
                        @forelse($agent->enfants->sortBy('date_naissance_enfant') as $e)
                        <div class="famille-card">
                            <div class="famille-avatar" style="background:#ECFDF5;">
                                <i class="fas fa-child" style="color:#059669;"></i>
                            </div>
                            <div class="famille-info flex-grow-1">
                                <h6>{{ $e->prenom_complet }}</h6>
                                <span>
                                    {{ $e->lien_filiation }}
                                    @if($e->date_naissance_enfant)
                                        · {{ $e->date_naissance_enfant->format('d/m/Y') }}
                                        ({{ $e->age ?? '?' }} ans)
                                    @endif
                                </span>
                            </div>
                            @if(isset($e->est_mineur) && $e->est_mineur)
                            <span class="badge-pill bp-conge">Mineur</span>
                            @endif
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-baby"></i>
                            <p>Aucun enfant enregistré</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ ONGLET CONTRATS ═══════════ --}}
    <div x-show="tab==='contrats'" x-transition:enter="animate__animated animate__fadeIn animate__faster">
        <div class="info-panel">
            <div class="info-panel-header">
                <div class="header-icon" style="background:#EFF6FF;">
                    <i class="fas fa-file-contract" style="color:#0A4D8C;"></i>
                </div>
                <h6>Historique des contrats</h6>
                @can('create', \App\Models\Agent::class)
                <a href="{{ route('rh.contrats.create', ['agent' => $agent->id_agent]) }}"
                   class="ms-auto" style="background:#0A4D8C;color:#fff;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-plus"></i> Nouveau
                </a>
                @endcan
            </div>
            <div class="info-panel-body">
                @forelse($agent->contrats->sortByDesc('date_debut') as $contrat)
                <div class="contrat-row">
                    <div class="d-flex align-items-center gap-4">
                        <div class="contrat-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="contrat-info">
                            <h6>
                                {{ $contrat->type_contrat }}
                                @if($contrat->statut_contrat === 'Actif')
                                <span class="badge-pill bp-actif">
                                    <i class="fas fa-check-circle" style="font-size:9px;"></i> En cours
                                </span>
                                @endif
                            </h6>
                            <span>
                                Du {{ $contrat->date_debut->format('d/m/Y') }}
                                @if($contrat->date_fin) au {{ $contrat->date_fin->format('d/m/Y') }}
                                @else (CDI — sans terme)
                                @endif
                            </span>
                        </div>
                    </div>
                    <span class="badge-pill {{ $contrat->statut_contrat === 'Actif' ? 'bp-actif' : 'bp-retraite' }}">
                        {{ $contrat->statut_contrat }}
                    </span>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-file-circle-xmark"></i>
                    <p>Aucun contrat enregistré</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════ ONGLET MOUVEMENTS ═══════════ --}}
    <div x-show="tab==='mouvements'" x-transition:enter="animate__animated animate__fadeIn animate__faster">
        <div class="info-panel">
            <div class="info-panel-header">
                <div class="header-icon" style="background:#EFF6FF;">
                    <i class="fas fa-arrows-alt-h" style="color:#0A4D8C;"></i>
                </div>
                <h6>Historique des mouvements</h6>
            </div>
            <div class="info-panel-body">
                @forelse($agent->mouvements as $m)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                            <strong style="font-size:14px;">{{ ucfirst($m->type ?? $m->type_mouvement ?? '—') }}</strong>
                            <span style="font-size:13px;color:var(--theme-text-muted);">
                                {{ isset($m->date_mouvement) ? $m->date_mouvement?->format('d/m/Y') : ($m->date_effet?->format('d/m/Y') ?? '—') }}
                            </span>
                        </div>
                        @if(isset($m->service) && $m->service)
                        <div style="font-size:13px;color:var(--theme-text-muted);">
                            <i class="fas fa-building-columns me-1"></i>Vers {{ $m->service->nom_service }}
                        </div>
                        @endif
                        @if($m->motif)
                        <div style="font-size:13px;margin-top:8px;padding:10px 14px;background:var(--theme-bg-secondary);border-radius:8px;">
                            {{ $m->motif }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-route"></i>
                    <p>Aucun mouvement enregistré</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function showAgentData() {
    return {
        tab: 'profil',
        revealed: { telephone: false, adresse: false, assurance: false },
        toggleReveal(field) { this.revealed[field] = !this.revealed[field]; }
    };
}
</script>
@endpush