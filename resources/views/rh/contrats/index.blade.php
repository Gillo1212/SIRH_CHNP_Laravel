@extends('layouts.master')

@section('title', 'Gestion des Contrats')
@section('page-title', 'Gestion des Contrats')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Contrats</li>
@endsection

@push('styles')
<style>
/* ── KPI ── */
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:26px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.red::before    { background:#DC2626; }
.kpi-card.gray::before   { background:#6B7280; }

/* ── BADGES ── */
.badge-actif        { background:#D1FAE5;color:#065F46; }
.badge-expire       { background:#FEE2E2;color:#991B1B; }
.badge-clotured     { background:#F3F4F6;color:#374151; }
.badge-renouv       { background:#FEF3C7;color:#92400E; }
.badge-urgence-critical { background:#FEE2E2;color:#991B1B;animation:pulse 1.5s infinite; }
.badge-urgence-high     { background:#FEE2E2;color:#991B1B; }
.badge-urgence-medium   { background:#FEF3C7;color:#92400E; }
.badge-urgence-low      { background:#ECFDF5;color:#065F46; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.6;} }

/* ── TABLE ── */
.badge-stat { display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:10.5px;font-weight:600;white-space:nowrap; }
.table-custom { width:100%;border-collapse:separate;border-spacing:0; }
.table-custom thead th { padding:10px 14px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
.table-custom tbody td { padding:12px 14px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.table-custom tbody tr:hover { background:var(--sirh-primary-hover); }
.table-custom tbody tr:last-child td { border-bottom:none; }

/* ── SECTION TITLE ── */
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;padding-bottom:6px; }

/* ── KPI TREND ── */
.kpi-card .kpi-trend { font-size:11px;font-weight:600;margin-top:5px; }
.kpi-card .kpi-trend.up      { color:#10B981; }
.kpi-card .kpi-trend.down    { color:#EF4444; }
.kpi-card .kpi-trend.neutral { color:#6B7280; }

/* filter-bar styles handled by master layout */

/* ── ACTION BTNS ── */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30);transform:translateY(-1px); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }
.action-btn-sm { padding:5px 10px;font-size:11px;gap:4px; }
.action-btn-danger { background:#FEE2E2;color:#991B1B;border:1px solid #FECACA; }
.action-btn-danger:hover { background:#DC2626;color:#fff; }

/* ── MODAL ── */
.modal-header-sirh { background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 100%);border:none; }
.modal-header-sirh .modal-title { color:#fff;font-size:15px;font-weight:600; }
.modal-header-sirh .btn-close { filter:invert(1); }
.modal-header-warning { background:linear-gradient(135deg,#D97706 0%,#F59E0B 100%);border:none; }
.modal-header-warning .modal-title { color:#fff;font-size:15px;font-weight:600; }
.modal-header-warning .btn-close { filter:invert(1); }
.modal-header-danger { background:linear-gradient(135deg,#DC2626 0%,#EF4444 100%);border:none; }
.modal-header-danger .modal-title { color:#fff;font-size:15px;font-weight:600; }
.modal-header-danger .btn-close { filter:invert(1); }
.form-label-up { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);margin-bottom:4px; }
.form-control-sirh,.form-select-sirh { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);padding:8px 12px; }
.form-control-sirh:focus,.form-select-sirh:focus { border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12); }

/* ── DETAIL CARD (modal voir) ── */
.detail-item { display:flex;flex-direction:column;gap:2px;padding:10px 0;border-bottom:1px solid var(--theme-border); }
.detail-item:last-child { border-bottom:none; }
.detail-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted); }
.detail-value { font-size:13.5px;color:var(--theme-text);font-weight:500; }
.urgence-bar { height:6px;border-radius:3px;margin-top:6px; }

/* ── TOAST ── */
@keyframes toastIn { from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);} }

/* ── DARK MODE ── */
[data-theme="dark"] .badge-actif  { background:rgba(16,185,129,.2);color:#34d399; }
[data-theme="dark"] .badge-expire { background:rgba(239,68,68,.2);color:#f87171; }
[data-theme="dark"] .badge-clotured { background:rgba(107,114,128,.2);color:#9ca3af; }
[data-theme="dark"] .badge-renouv  { background:rgba(245,158,11,.2);color:#fbbf24; }
[data-theme="dark"] .form-control-sirh,[data-theme="dark"] .form-select-sirh { background:#161b22;border-color:#30363d;color:#e6edf3; }
[data-theme="dark"] .form-control-sirh:focus,[data-theme="dark"] .form-select-sirh:focus { border-color:#58a6ff;box-shadow:0 0 0 3px rgba(88,166,255,.15); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Gestion des contrats</h4>
        <p class="text-muted small mb-0">Suivi et gestion des contrats du personnel hospitalier</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($stats['expiring_60'] > 0)
        <a href="{{ route('rh.contrats.expiring') }}" class="action-btn"
           style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $stats['expiring_60'] }} à renouveler
        </a>
        @endif
        <a href="{{ route('rh.contrats.expiring') }}" class="action-btn action-btn-outline">
            <i class="fas fa-clock"></i> Alertes expiration
        </a>
        <button type="button" class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreer">
            <i class="fas fa-plus"></i> Nouveau contrat
        </button>
    </div>
</div>

{{-- ─── KPIs ──────────────────────────────────────────────────────── --}}
<div class="section-title">Tableau de bord des contrats</div>
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="kpi-card green" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border,#E5E7EB);">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
                <span class="badge-stat" style="background:#D1FAE5;color:#065F46;">En cours</span>
            </div>
            <div class="kpi-value" style="color:#059669;">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Contrats actifs</div>
            <div class="kpi-trend up"><i class="fas fa-arrow-up me-1"></i>Personnel sous contrat</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="kpi-card red" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border,#E5E7EB);">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FEF2F2;"><i class="fas fa-exclamation-triangle" style="color:#DC2626;"></i></div>
                <span class="badge-stat" style="background:#FEE2E2;color:#991B1B;">Urgent</span>
            </div>
            <div class="kpi-value" style="color:#DC2626;">{{ $stats['expiring_30'] }}</div>
            <div class="kpi-label">Expirent dans 30 j.</div>
            <div class="kpi-trend {{ $stats['expiring_30'] > 0 ? 'down' : 'up' }}">
                <i class="fas fa-{{ $stats['expiring_30'] > 0 ? 'exclamation-triangle' : 'check' }} me-1"></i>
                {{ $stats['expiring_30'] > 0 ? 'Action requise' : 'Aucun urgent' }}
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="kpi-card amber" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border,#E5E7EB);">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-hourglass-half" style="color:#D97706;"></i></div>
                <span class="badge-stat" style="background:#FEF3C7;color:#92400E;">Attention</span>
            </div>
            <div class="kpi-value" style="color:#D97706;">{{ $stats['expiring_60'] }}</div>
            <div class="kpi-label">Expirent dans 60 j.</div>
            <div class="kpi-trend neutral"><i class="fas fa-calendar-alt me-1"></i>À anticiper</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="kpi-card amber" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border,#E5E7EB);">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-sync-alt" style="color:#D97706;"></i></div>
                <span class="badge-stat" style="background:#FEF3C7;color:#92400E;">En cours</span>
            </div>
            <div class="kpi-value" style="color:#D97706;">{{ $stats['en_renouv'] }}</div>
            <div class="kpi-label">En renouvellement</div>
            <div class="kpi-trend neutral"><i class="fas fa-sync me-1"></i>Workflow ouvert</div>
        </div>
    </div>

</div>

{{-- ─── FILTRES ──────────────────────────────────────────────────── --}}
@php $hasFilters = request()->anyFilled(['search','statut','type_contrat','date_debut_from','date_debut_to','date_fin_from','date_fin_to']); @endphp
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('rh.contrats.index') }}" id="formFiltres">

            {{-- Ligne 1 : Recherche + Statut + Type + Boutons --}}
            <div class="row g-2 align-items-center">
                <div class="col-12 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="background:var(--theme-panel-bg);">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Nom, prénom ou matricule…" value="{{ request('search') }}"
                               style="font-size:13px;">
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-2">
                    <select name="statut" class="form-select" style="font-size:13px;">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\Contrat::STATUTS as $val => $cfg)
                            <option value="{{ $val }}" {{ request('statut') === $val ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-lg-2">
                    <select name="type_contrat" class="form-select" style="font-size:13px;">
                        <option value="">Tous les types</option>
                        @foreach(\App\Models\Contrat::TYPES as $val => $label)
                            <option value="{{ $val }}" {{ request('type_contrat') === $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-4 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="font-size:13px;white-space:nowrap;">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    @if($hasFilters)
                    <a href="{{ route('rh.contrats.index') }}" class="btn btn-outline-secondary" title="Réinitialiser les filtres">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                    <a href="{{ route('rh.contrats.export', request()->query()) }}"
                       class="btn btn-outline-success d-flex align-items-center gap-1" style="font-size:13px;white-space:nowrap;">
                        <i class="fas fa-file-excel"></i> Excel
                        @if($hasFilters)
                        <span style="font-size:10px;background:#D1FAE5;color:#065F46;padding:1px 5px;border-radius:10px;font-weight:700;">filtré</span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Ligne 2 : Filtres par date --}}
            <div class="d-flex align-items-center gap-2 flex-wrap mt-2 pt-2" style="border-top:1px solid var(--theme-border,#E5E7EB);">
                <span style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;">
                    <i class="fas fa-calendar-alt me-1"></i>Début
                </span>
                <input type="date" name="date_debut_from" title="Du"
                       value="{{ request('date_debut_from') }}"
                       style="font-size:12px;padding:3px 7px;border:1px solid var(--theme-border,#D1D5DB);border-radius:6px;background:var(--theme-panel-bg);color:var(--theme-text);width:130px;">
                <span style="color:#9CA3AF;font-size:11px;">→</span>
                <input type="date" name="date_debut_to" title="Au"
                       value="{{ request('date_debut_to') }}"
                       style="font-size:12px;padding:3px 7px;border:1px solid var(--theme-border,#D1D5DB);border-radius:6px;background:var(--theme-panel-bg);color:var(--theme-text);width:130px;">

                <span style="color:#D1D5DB;font-size:14px;margin:0 2px;">|</span>

                <span style="font-size:10px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;">
                    <i class="fas fa-calendar-check me-1"></i>Fin
                </span>
                <input type="date" name="date_fin_from" title="Du"
                       value="{{ request('date_fin_from') }}"
                       style="font-size:12px;padding:3px 7px;border:1px solid var(--theme-border,#D1D5DB);border-radius:6px;background:var(--theme-panel-bg);color:var(--theme-text);width:130px;">
                <span style="color:#9CA3AF;font-size:11px;">→</span>
                <input type="date" name="date_fin_to" title="Au"
                       value="{{ request('date_fin_to') }}"
                       style="font-size:12px;padding:3px 7px;border:1px solid var(--theme-border,#D1D5DB);border-radius:6px;background:var(--theme-panel-bg);color:var(--theme-text);width:130px;">
            </div>

        </form>
    </div>
</div>

{{-- ─── TABLEAU ─────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
    <div class="card-body p-0">
        @if($contrats->count() > 0)
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Durée / Échéance</th>
                        <th>Statut</th>
                        <th style="width:130px;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contrats as $contrat)
                    @php
                        $agent = $contrat->agent;
                        $statut = $contrat->statut_contrat;
                        $badgeClass = match($statut) {
                            'Actif'             => 'badge-actif',
                            'Expiré'            => 'badge-expire',
                            'Clôturé'           => 'badge-clotured',
                            'En_renouvellement' => 'badge-renouv',
                            default             => '',
                        };
                        $jr = $contrat->jours_restants;
                        $urgence = $contrat->urgence;
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($agent?->photo)
                                    <img src="{{ Storage::url($agent->photo) }}" alt=""
                                         style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px;flex-shrink:0;">
                                        {{ strtoupper(substr($agent?->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($agent?->nom ?? '', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-600" style="color:var(--theme-text);font-size:13px;">
                                        {{ $agent?->nom_complet ?? '—' }}
                                    </div>
                                    <div class="text-muted" style="font-size:11px;">{{ $agent?->matricule }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--theme-text);">
                                {{ $agent?->service?->nom_service ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-stat" style="background:var(--theme-bg-secondary);color:#0A4D8C;font-size:11px;">
                                {{ $contrat->type_contrat }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--theme-text);">
                                {{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($contrat->date_fin)
                                <span style="font-size:12px;color:var(--theme-text);">
                                    {{ $contrat->date_fin->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:11px;font-style:italic;">Indéterminée</span>
                            @endif
                        </td>
                        <td>
                            @if($contrat->date_fin && $statut === 'Actif')
                                @if($jr !== null && $jr >= 0)
                                    <span class="badge-stat badge-urgence-{{ $urgence }}">
                                        @if($urgence === 'critical') <i class="fas fa-exclamation-circle me-1"></i>
                                        @elseif(in_array($urgence, ['high','medium'])) <i class="fas fa-clock me-1"></i>
                                        @endif
                                        {{ $jr }}j restants
                                    </span>
                                @elseif($jr !== null && $jr < 0)
                                    <span class="badge-stat badge-expire">
                                        <i class="fas fa-times-circle me-1"></i>Expiré ({{ abs($jr) }}j)
                                    </span>
                                @endif
                            @elseif(!$contrat->date_fin)
                                <span style="font-size:11px;color:var(--theme-text-muted);">{{ $contrat->duree }}</span>
                            @else
                                <span style="font-size:11px;color:var(--theme-text-muted);">{{ $contrat->duree }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-stat {{ $badgeClass }}">
                                @if($statut === 'Actif') <i class="fas fa-circle me-1" style="font-size:7px;"></i>
                                @elseif($statut === 'Expiré') <i class="fas fa-circle me-1" style="font-size:7px;"></i>
                                @endif
                                {{ \App\Models\Contrat::STATUTS[$statut]['label'] ?? $statut }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-1">
                                {{-- Voir --}}
                                <button type="button"
                                        class="action-btn action-btn-outline action-btn-sm btn-voir"
                                        data-id="{{ $contrat->id_contrat }}"
                                        title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                {{-- Modifier --}}
                                @if($statut !== 'Clôturé')
                                <button type="button"
                                        class="action-btn action-btn-outline action-btn-sm btn-editer"
                                        data-id="{{ $contrat->id_contrat }}"
                                        data-agent="{{ $agent?->nom_complet }}"
                                        data-type="{{ $contrat->type_contrat }}"
                                        data-debut="{{ $contrat->date_debut?->format('Y-m-d') }}"
                                        data-fin="{{ $contrat->date_fin?->format('Y-m-d') }}"
                                        data-statut="{{ $contrat->statut_contrat }}"
                                        data-obs="{{ $contrat->observation }}"
                                        title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endif
                                {{-- Renouveler --}}
                                @if($statut === 'Actif' && $contrat->date_fin)
                                <button type="button"
                                        class="action-btn action-btn-sm btn-renouveler"
                                        style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;"
                                        data-id="{{ $contrat->id_contrat }}"
                                        data-agent="{{ $agent?->nom_complet }}"
                                        data-type="{{ $contrat->type_contrat }}"
                                        data-fin="{{ $contrat->date_fin?->format('d/m/Y') }}"
                                        title="Renouveler">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                @endif
                                {{-- Clôturer --}}
                                @if($statut === 'Actif')
                                <button type="button"
                                        class="action-btn action-btn-danger action-btn-sm btn-cloturer"
                                        data-id="{{ $contrat->id_contrat }}"
                                        data-agent="{{ $agent?->nom_complet }}"
                                        title="Clôturer">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($contrats->hasPages())
        <div class="px-4 py-3 border-top" style="background:var(--theme-panel-bg);">
            {{ $contrats->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-5">
            <i class="fas fa-file-contract fa-3x mb-3 d-block" style="color:#0A4D8C;opacity:.2;"></i>
            <p class="text-muted mb-2">Aucun contrat trouvé.</p>
            <button type="button" class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreer">
                <i class="fas fa-plus"></i> Créer le premier contrat
            </button>
        </div>
        @endif
    </div>
</div>

</div>{{-- /.container-fluid --}}


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — CRÉER UN CONTRAT
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCreer" tabindex="-1" aria-labelledby="modalCreerLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-sirh">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-file-contract text-white" style="font-size:14px;"></i>
                    </div>
                    <h5 class="modal-title mb-0" id="modalCreerLabel">Nouveau contrat</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('rh.contrats.store') }}" id="formCreer">
                @csrf
                <input type="hidden" name="_from" value="create">
                <div class="modal-body p-4" style="background:var(--theme-panel-bg);">

                    {{-- Erreurs --}}
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius:8px;font-size:13px;">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Veuillez corriger les erreurs :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    {{-- Agent --}}
                    <div class="mb-3">
                        <label class="form-label-up">Agent <span class="text-danger">*</span></label>
                        <select name="id_agent" class="form-select-sirh form-select" required>
                            <option value="">— Sélectionner un agent —</option>
                            @foreach($agents as $ag)
                                <option value="{{ $ag->id_agent }}" {{ old('id_agent') == $ag->id_agent ? 'selected' : '' }}>
                                    {{ $ag->matricule }} — {{ $ag->nom_complet }}
                                    @if($ag->contratActif) (contrat actif : {{ $ag->contratActif->type_contrat }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size:11px;">
                            <i class="fas fa-info-circle me-1 text-primary"></i>
                            Si l'agent a déjà un contrat actif, il sera automatiquement clôturé.
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Type --}}
                        <div class="col-md-6">
                            <label class="form-label-up">Type de contrat <span class="text-danger">*</span></label>
                            <select name="type_contrat" class="form-select-sirh form-select" required>
                                <option value="">— Choisir —</option>
                                @foreach(\App\Models\Contrat::TYPES as $val => $label)
                                    <option value="{{ $val }}" {{ old('type_contrat') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Statut --}}
                        <div class="col-md-6">
                            <label class="form-label-up">Statut initial <span class="text-danger">*</span></label>
                            <select name="statut_contrat" class="form-select-sirh form-select" required>
                                @foreach(\App\Models\Contrat::STATUTS as $val => $cfg)
                                    <option value="{{ $val }}" {{ old('statut_contrat', 'Actif') === $val ? 'selected' : '' }}>
                                        {{ $cfg['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Date début --}}
                        <div class="col-md-6">
                            <label class="form-label-up">Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" class="form-control-sirh form-control"
                                   value="{{ old('date_debut') }}" required>
                        </div>
                        {{-- Date fin --}}
                        <div class="col-md-6">
                            <label class="form-label-up">Date de fin</label>
                            <input type="date" name="date_fin" class="form-control-sirh form-control"
                                   value="{{ old('date_fin') }}">
                            <div class="form-text" style="font-size:11px;">Laisser vide pour un contrat indéterminé (PE, CDI)</div>
                        </div>
                    </div>

                    {{-- Observation --}}
                    <div class="mt-3">
                        <label class="form-label-up">Observation / Notes</label>
                        <textarea name="observation" class="form-control-sirh form-control"
                                  rows="3" placeholder="Conditions particulières, clauses spécifiques…"
                                  style="resize:none;">{{ old('observation') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background:var(--theme-panel-bg);border-top:1px solid var(--theme-border);">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer le contrat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — VOIR LES DÉTAILS
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalVoir" tabindex="-1" aria-labelledby="modalVoirLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-sirh">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-search text-white" style="font-size:14px;"></i>
                    </div>
                    <h5 class="modal-title mb-0" id="modalVoirLabel">Détails du contrat</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background:var(--theme-panel-bg);">
                {{-- Loader --}}
                <div id="voirLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;"></div>
                    <p class="text-muted mt-2 small">Chargement…</p>
                </div>
                {{-- Contenu --}}
                <div id="voirContent" class="d-none">
                    {{-- Agent Banner --}}
                    <div style="background:linear-gradient(135deg,#EFF6FF,#E0F2FE);border-bottom:1px solid var(--theme-border);padding:20px 24px;">
                        <div class="d-flex align-items-center gap-3">
                            <div id="voirAvatar"
                                 style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                            </div>
                            <div>
                                <div id="voirNomAgent" class="fw-bold" style="font-size:16px;color:#0A4D8C;"></div>
                                <div class="d-flex gap-3 mt-1">
                                    <span id="voirMatricule" class="text-muted" style="font-size:12px;"></span>
                                    <span id="voirFonction" style="font-size:12px;color:#374151;"></span>
                                    <span id="voirService" class="text-muted" style="font-size:12px;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row g-4">
                            {{-- Colonne infos contrat --}}
                            <div class="col-md-6">
                                <div class="fw-600 mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);">
                                    Informations contrat
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Type</span>
                                    <span id="voirType" class="detail-value"></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Statut</span>
                                    <span id="voirStatutBadge"></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Date de début</span>
                                    <span id="voirDateDebut" class="detail-value"></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Date de fin</span>
                                    <span id="voirDateFin" class="detail-value"></span>
                                </div>
                            </div>

                            {{-- Colonne état --}}
                            <div class="col-md-6">
                                <div class="fw-600 mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);">
                                    État et échéance
                                </div>
                                <div id="voirEcheance">
                                    {{-- Contenu injecté par JS --}}
                                </div>
                                <div class="detail-item mt-2">
                                    <span class="detail-label">Observation</span>
                                    <span id="voirObs" class="detail-value" style="white-space:pre-wrap;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:var(--theme-panel-bg);border-top:1px solid var(--theme-border);">
                <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Fermer</button>
                <button type="button" id="voirBtnEdit" class="action-btn action-btn-primary d-none">
                    <i class="fas fa-edit me-1"></i>Modifier
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — MODIFIER UN CONTRAT
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditer" tabindex="-1" aria-labelledby="modalEditerLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-sirh">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-edit text-white" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalEditerLabel">Modifier le contrat</h5>
                        <div id="editAgentName" style="font-size:12px;opacity:.85;color:#fff;margin-top:2px;"></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditer" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4" style="background:var(--theme-panel-bg);">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-up">Type de contrat <span class="text-danger">*</span></label>
                            <select name="type_contrat" id="editType" class="form-select-sirh form-select" required>
                                @foreach(\App\Models\Contrat::TYPES as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-up">Statut <span class="text-danger">*</span></label>
                            <select name="statut_contrat" id="editStatut" class="form-select-sirh form-select" required>
                                @foreach(\App\Models\Contrat::STATUTS as $val => $cfg)
                                    <option value="{{ $val }}">{{ $cfg['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-up">Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" id="editDateDebut"
                                   class="form-control-sirh form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-up">Date de fin</label>
                            <input type="date" name="date_fin" id="editDateFin"
                                   class="form-control-sirh form-control">
                            <div class="form-text" style="font-size:11px;">Laisser vide pour un contrat indéterminé</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label-up">Observation / Notes</label>
                        <textarea name="observation" id="editObs" class="form-control-sirh form-control"
                                  rows="3" style="resize:none;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background:var(--theme-panel-bg);border-top:1px solid var(--theme-border);">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — RENOUVELER UN CONTRAT
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRenouveler" tabindex="-1" aria-labelledby="modalRenouvelerLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-warning">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-sync-alt text-white" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalRenouvelerLabel">Renouveler le contrat</h5>
                        <div id="renouvelAgentName" style="font-size:12px;opacity:.85;color:#fff;margin-top:2px;"></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRenouveler" method="POST" action="">
                @csrf
                <div class="modal-body p-4" style="background:var(--theme-panel-bg);">
                    {{-- Avertissement --}}
                    <div class="alert mb-4" role="alert"
                         style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;color:#92400E;font-size:13px;">
                        <div class="fw-600 mb-1"><i class="fas fa-info-circle me-2"></i>Renouvellement de contrat</div>
                        <div>Le contrat actuel sera <strong>automatiquement clôturé</strong> et un nouveau contrat actif sera créé.</div>
                        <div class="mt-1" id="renouvelInfo" style="font-size:12px;"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label-up">Type du nouveau contrat <span class="text-danger">*</span></label>
                            <select name="type_contrat" id="renouvelType" class="form-select-sirh form-select" required>
                                @foreach(\App\Models\Contrat::TYPES as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-up">Nouvelle date de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" id="renouvelDateDebut"
                                   class="form-control-sirh form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-up">Nouvelle date de fin</label>
                            <input type="date" name="date_fin" id="renouvelDateFin"
                                   class="form-control-sirh form-control">
                            <div class="form-text" style="font-size:11px;">Laisser vide pour CDI/indéterminé</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label-up">Motif / Observation</label>
                        <textarea name="observation" class="form-control-sirh form-control"
                                  rows="2" style="resize:none;"
                                  placeholder="Ex : Renouvellement annuel ordinaire…"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background:var(--theme-panel-bg);border-top:1px solid var(--theme-border);">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn"
                            style="background:#D97706;color:#fff;">
                        <i class="fas fa-sync-alt me-1"></i>Renouveler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — CLÔTURER UN CONTRAT
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCloturer" tabindex="-1" aria-labelledby="modalCloturerLabel">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-danger">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-times text-white" style="font-size:14px;"></i>
                    </div>
                    <h5 class="modal-title mb-0" id="modalCloturerLabel">Clôturer le contrat</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center" style="background:var(--theme-panel-bg);">
                <div style="width:56px;height:56px;background:#FEE2E2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-exclamation-triangle" style="font-size:22px;color:#DC2626;"></i>
                </div>
                <p style="color:var(--theme-text);font-size:14px;margin-bottom:4px;">
                    Confirmer la clôture du contrat de
                </p>
                <strong id="cloturerAgentName" style="color:var(--theme-text);font-size:15px;"></strong>
                <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                    Cette action est irréversible. Le contrat passera en statut <strong>Clôturé</strong>.
                </p>
            </div>
            <div class="modal-footer justify-content-center gap-2" style="background:var(--theme-panel-bg);border-top:1px solid var(--theme-border);">
                <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                <form id="formCloturer" method="POST" action="" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="action-btn action-btn-danger">
                        <i class="fas fa-times me-1"></i>Clôturer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════
// TOAST NOTIFICATIONS
// ═══════════════════════════════════════════════════════
function showToast(message, type = 'success') {
    const cfg = {
        success: { bg: '#10B981', icon: 'fa-check-circle' },
        error:   { bg: '#EF4444', icon: 'fa-exclamation-circle' },
        warning: { bg: '#F59E0B', icon: 'fa-exclamation-triangle' },
    };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:420px;animation:toastIn .3s ease;">
            <i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i>
            <span>${message}</span>
            <button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button>
        </div>
    `);
    setTimeout(() => document.getElementById(id)?.remove(), 5000);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif

// ═══════════════════════════════════════════════════════
// MODAL VOIR (AJAX)
// ═══════════════════════════════════════════════════════
document.querySelectorAll('.btn-voir').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const loader  = document.getElementById('voirLoader');
        const content = document.getElementById('voirContent');

        loader.classList.remove('d-none');
        content.classList.add('d-none');

        new bootstrap.Modal(document.getElementById('modalVoir')).show();

        fetch(`/rh/contrats/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            // Agent
            const initials = (data.agent.nom_complet || '').split(' ').slice(0,2).map(p => p[0]?.toUpperCase() || '').join('');
            document.getElementById('voirAvatar').textContent = initials;
            document.getElementById('voirNomAgent').textContent = data.agent.nom_complet;
            document.getElementById('voirMatricule').textContent = data.agent.matricule ? `# ${data.agent.matricule}` : '';
            document.getElementById('voirFonction').textContent  = data.agent.fonction || '';
            document.getElementById('voirService').textContent   = data.agent.service ? `📍 ${data.agent.service}` : '';

            // Contrat
            const types = @json(\App\Models\Contrat::TYPES);
            const statuts = @json(\App\Models\Contrat::STATUTS);
            document.getElementById('voirType').textContent = types[data.type_contrat] || data.type_contrat;
            document.getElementById('voirDateDebut').textContent = data.date_debut
                ? new Date(data.date_debut).toLocaleDateString('fr-FR') : '—';
            document.getElementById('voirDateFin').textContent = data.date_fin
                ? new Date(data.date_fin).toLocaleDateString('fr-FR') : 'Indéterminée (CDI/PE)';
            document.getElementById('voirObs').textContent = data.observation || '—';

            // Badge statut
            const st = statuts[data.statut_contrat] || { label: data.statut_contrat, color: '#374151', bg: '#F3F4F6' };
            document.getElementById('voirStatutBadge').innerHTML = `
                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:${st.bg};color:${st.color};">
                    ${st.label}
                </span>`;

            // Échéance
            let echeanceHTML = '';
            if (data.jours_restants !== null && data.jours_restants !== undefined) {
                const jr = data.jours_restants;
                if (jr < 0) {
                    echeanceHTML = `<div class="alert mb-0" style="background:#FEE2E2;border:none;border-radius:10px;color:#991B1B;font-size:13px;">
                        <i class="fas fa-times-circle me-2"></i>Contrat expiré depuis <strong>${Math.abs(jr)} jours</strong>
                    </div>`;
                } else if (jr <= 30) {
                    echeanceHTML = `<div class="alert mb-0" style="background:#FEE2E2;border:none;border-radius:10px;color:#991B1B;font-size:13px;">
                        <i class="fas fa-exclamation-circle me-2"></i>Expire dans <strong>${jr} jours</strong> — Action urgente requise
                    </div>`;
                } else if (jr <= 60) {
                    echeanceHTML = `<div class="alert mb-0" style="background:#FEF3C7;border:none;border-radius:10px;color:#92400E;font-size:13px;">
                        <i class="fas fa-clock me-2"></i>Expire dans <strong>${jr} jours</strong> — À renouveler bientôt
                    </div>`;
                } else {
                    echeanceHTML = `<div class="alert mb-0" style="background:#D1FAE5;border:none;border-radius:10px;color:#065F46;font-size:13px;">
                        <i class="fas fa-check-circle me-2"></i>Encore <strong>${jr} jours</strong> de validité
                    </div>`;
                }
            } else {
                echeanceHTML = `<div class="alert mb-0" style="background:#EFF6FF;border:none;border-radius:10px;color:#1E40AF;font-size:13px;">
                    <i class="fas fa-infinity me-2"></i>Contrat à durée indéterminée
                </div>`;
            }
            document.getElementById('voirEcheance').innerHTML = echeanceHTML;

            // Bouton modifier
            if (data.statut_contrat !== 'Clôturé') {
                const btnEdit = document.getElementById('voirBtnEdit');
                btnEdit.classList.remove('d-none');
                btnEdit.onclick = () => {
                    bootstrap.Modal.getInstance(document.getElementById('modalVoir')).hide();
                    // Déclencher modal éditer
                    const fakeBtn = document.createElement('button');
                    fakeBtn.dataset.id = data.id_contrat;
                    fakeBtn.dataset.agent = data.agent.nom_complet;
                    fakeBtn.dataset.type = data.type_contrat;
                    fakeBtn.dataset.debut = data.date_debut;
                    fakeBtn.dataset.fin = data.date_fin || '';
                    fakeBtn.dataset.statut = data.statut_contrat;
                    fakeBtn.dataset.obs = data.observation || '';
                    fakeBtn.classList.add('btn-editer');
                    document.body.appendChild(fakeBtn);
                    fakeBtn.click();
                    document.body.removeChild(fakeBtn);
                };
            }

            loader.classList.add('d-none');
            content.classList.remove('d-none');
        })
        .catch(() => {
            loader.innerHTML = `<p class="text-danger py-4">Erreur lors du chargement des données.</p>`;
        });
    });
});

// ═══════════════════════════════════════════════════════
// MODAL ÉDITER
// ═══════════════════════════════════════════════════════
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-editer');
    if (!btn) return;

    const id     = btn.dataset.id;
    const agent  = btn.dataset.agent || '';
    const type   = btn.dataset.type   || '';
    const debut  = btn.dataset.debut  || '';
    const fin    = btn.dataset.fin    || '';
    const statut = btn.dataset.statut || '';
    const obs    = btn.dataset.obs    || '';

    document.getElementById('editAgentName').textContent = agent;
    document.getElementById('editType').value      = type;
    document.getElementById('editDateDebut').value = debut;
    document.getElementById('editDateFin').value   = fin;
    document.getElementById('editStatut').value    = statut;
    document.getElementById('editObs').value       = obs;

    document.getElementById('formEditer').action = `/rh/contrats/${id}`;
    new bootstrap.Modal(document.getElementById('modalEditer')).show();
});

// ═══════════════════════════════════════════════════════
// MODAL RENOUVELER
// ═══════════════════════════════════════════════════════
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-renouveler');
    if (!btn) return;

    const id    = btn.dataset.id;
    const agent = btn.dataset.agent || '';
    const type  = btn.dataset.type  || '';
    const fin   = btn.dataset.fin   || '';

    document.getElementById('renouvelAgentName').textContent = agent;
    document.getElementById('renouvelInfo').textContent = fin ? `Contrat actuel expire le : ${fin}` : '';
    document.getElementById('renouvelType').value = type;

    // Date début proposée = lendemain
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('renouvelDateDebut').value = tomorrow.toISOString().split('T')[0];

    document.getElementById('formRenouveler').action = `/rh/contrats/${id}/renouveler`;
    new bootstrap.Modal(document.getElementById('modalRenouveler')).show();
});

// ═══════════════════════════════════════════════════════
// MODAL CLÔTURER
// ═══════════════════════════════════════════════════════
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-cloturer');
    if (!btn) return;

    const id    = btn.dataset.id;
    const agent = btn.dataset.agent || '';

    document.getElementById('cloturerAgentName').textContent = agent;
    document.getElementById('formCloturer').action = `/rh/contrats/${id}/cloturer`;
    new bootstrap.Modal(document.getElementById('modalCloturer')).show();
});

// ═══════════════════════════════════════════════════════
// AUTO-OUVRIR MODAL CRÉER si erreurs de validation
// ═══════════════════════════════════════════════════════
@if($errors->any() && old('_from') === 'create')
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalCreer')).show();
    });
@endif
</script>
@endpush
