@extends('layouts.master')

@section('title', 'Services Hospitaliers')
@section('page-title', 'Gestion des Services')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Services</li>
@endsection

@push('styles')
<style>
/* ════════════════════════════════════════════════════════════
   KPI CARDS
   ════════════════════════════════════════════════════════════ */
.kpi-card {
    border-radius:12px;padding:20px 24px;
    transition:box-shadow 200ms,transform 200ms;
    position:relative;overflow:hidden;
}
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label { font-size:13px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.red::before    { background:#DC2626; }

/* ════════════════════════════════════════════════════════════
   ACTION BUTTONS
   ════════════════════════════════════════════════════════════ */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30);transform:translateY(-1px); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }

/* filter-bar styles handled by master layout */

/* ════════════════════════════════════════════════════════════
   TABLEAU SERVICES
   ════════════════════════════════════════════════════════════ */
.services-table { width:100%;border-collapse:separate;border-spacing:0; }
.services-table thead th { padding:11px 14px;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;background:var(--theme-bg-secondary);color:var(--theme-text-muted);border-bottom:2px solid var(--theme-border);white-space:nowrap; }
.services-table thead th:first-child { border-radius:10px 0 0 0;padding-left:20px; }
.services-table thead th:last-child  { border-radius:0 10px 0 0; }
.services-table tbody td { padding:13px 14px;border-bottom:1px solid var(--theme-border);font-size:13px;vertical-align:middle;color:var(--theme-text);transition:background 100ms; }
.services-table tbody td:first-child { padding-left:20px; }
.services-table tbody tr:hover td { background:var(--sirh-primary-hover); }
.services-table tbody tr:last-child td { border-bottom:none; }

/* ════════════════════════════════════════════════════════════
   BADGES & PILLS
   ════════════════════════════════════════════════════════════ */
.type-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em; }
.tb-clinique      { background:#EFF6FF;color:#1E40AF; }
.tb-administratif { background:#ECFDF5;color:#065F46; }
.tb-aide          { background:#F5F3FF;color:#5B21B6; }
.tb-support       { background:#FFFBEB;color:#92400E; }

/* ════════════════════════════════════════════════════════════
   BTN ICON
   ════════════════════════════════════════════════════════════ */
.btn-icon { width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text-muted);cursor:pointer;transition:all 150ms;font-size:13px;text-decoration:none;padding:0; }
.btn-icon:hover { transform:translateY(-1px); }
.btn-icon.v:hover { background:#EFF6FF;border-color:#BFDBFE;color:#1E40AF; }
.btn-icon.e:hover { background:#F0FDF4;border-color:#BBF7D0;color:#15803D; }
.btn-icon.m:hover { background:#F5F3FF;border-color:#DDD6FE;color:#7C3AED; }
.btn-icon.d:hover { background:#FEF2F2;border-color:#FECACA;color:#DC2626; }

/* ════════════════════════════════════════════════════════════
   CARTES SERVICES
   ════════════════════════════════════════════════════════════ */
.services-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:16px; }
.service-card { background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:14px;overflow:hidden;transition:transform 150ms,box-shadow 200ms;display:flex;flex-direction:column;border-top:4px solid transparent; }
.service-card:hover { transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,0,0,.11); }
.service-card.t-clinique      { border-top-color:#3B82F6; }
.service-card.t-administratif { border-top-color:#10B981; }
.service-card.t-aide          { border-top-color:#8B5CF6; }
.service-card.t-support       { border-top-color:#F59E0B; }
.service-card-body { padding:18px 18px 14px; }
.service-card-footer { padding:10px 18px;border-top:1px solid var(--theme-border);display:flex;gap:6px;background:var(--theme-bg-secondary); }
.service-card-footer .btn-icon { flex:1;width:auto;border-radius:8px;height:32px;font-size:12px;gap:5px; }
.service-card-footer .btn-icon span { font-size:11px;font-weight:500; }

/* ════════════════════════════════════════════════════════════
   TOGGLE VUE
   ════════════════════════════════════════════════════════════ */
.view-toggle { display:flex;border:1px solid var(--theme-border);border-radius:8px;overflow:hidden; }
.view-toggle button { padding:7px 13px;border:none;background:none;cursor:pointer;font-size:13px;color:var(--theme-text-muted);transition:all 120ms; }
.view-toggle button.active { background:#0A4D8C;color:#fff; }

/* ════════════════════════════════════════════════════════════
   MODALS — Charte SIRH
   ════════════════════════════════════════════════════════════ */
.modal-content { border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.18); }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
.modal-input { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);padding:9px 12px;border:1.5px solid var(--theme-border);width:100%;transition:border-color 150ms; }
.modal-input:focus { outline:none;border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12); }
.modal-input.is-invalid { border-color:#EF4444; }
.modal-nav-tabs { display:flex;gap:0;border-bottom:2px solid var(--theme-border);margin:0 -24px 20px;padding:0 24px;overflow-x:auto; }
.modal-nav-tabs button { padding:10px 16px;border:none;background:none;cursor:pointer;font-size:12.5px;font-weight:500;color:var(--theme-text-muted);border-bottom:2px solid transparent;margin-bottom:-2px;transition:all 150ms;white-space:nowrap; }
.modal-nav-tabs button.active { color:#0A4D8C;border-bottom-color:#0A4D8C;font-weight:600; }
.modal-nav-tabs button:hover:not(.active) { color:var(--theme-text);background:var(--theme-bg-secondary);border-radius:6px 6px 0 0; }

/* Agent row in modal */
.agent-modal-row { display:flex;align-items:center;padding:8px 0;border-bottom:1px solid var(--theme-border); }
.agent-modal-row:last-child { border-bottom:none; }
.agent-avatar-xs { width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0; }

/* Toast */
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }

/* Dark mode */
[data-theme="dark"] .kpi-card.blue  { background:rgba(10,77,140,.15);border:1px solid rgba(10,77,140,.30); }
[data-theme="dark"] .kpi-card.green { background:rgba(5,150,105,.15);border:1px solid rgba(5,150,105,.30); }
[data-theme="dark"] .kpi-card.amber { background:rgba(217,119,6,.15);border:1px solid rgba(217,119,6,.30); }
[data-theme="dark"] .kpi-card.red   { background:rgba(220,38,38,.15);border:1px solid rgba(220,38,38,.30); }
[data-theme="dark"] .services-table tbody tr:hover td { background:rgba(255,255,255,.04); }
[data-theme="dark"] .modal-content  { background:#161b22;border:1px solid #30363d; }
[data-theme="dark"] .modal-nav-tabs { border-bottom-color:#30363d; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     KPI CARDS
     ═══════════════════════════════════════════════════════════ --}}
<div style="font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--theme-text-muted);margin-bottom:12px;">Vue d'ensemble</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card blue panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-hospital-alt" style="color:#0A4D8C;"></i></div>
                <span style="background:#EFF6FF;color:#1E40AF;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">Total</span>
            </div>
            <div class="kpi-value">{{ $totaux['services'] }}</div>
            <div class="kpi-label">Services actifs</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card green panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-users" style="color:#059669;"></i></div>
                <span style="background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">Effectifs</span>
            </div>
            <div class="kpi-value">{{ $totaux['agents'] }}</div>
            <div class="kpi-label">Total agents</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card amber panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-user-check" style="color:#D97706;"></i></div>
                <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">Managés</span>
            </div>
            <div class="kpi-value">{{ $totaux['avec_manager'] }}</div>
            <div class="kpi-label">Avec manager</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card red panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FEF2F2;"><i class="fas fa-user-slash" style="color:#DC2626;"></i></div>
                <span style="background:#FEE2E2;color:#991B1B;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">Alerte</span>
            </div>
            <div class="kpi-value">{{ $totaux['sans_manager'] }}</div>
            <div class="kpi-label">Sans manager</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     BARRE ACTIONS
     ═══════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <div class="fw-700" style="font-size:15px;">
            Liste des services
            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;background:#EFF6FF;color:#0A4D8C;border-radius:50%;font-size:11px;font-weight:700;margin-left:6px;padding:0 5px;">{{ $totaux['services'] }}</span>
        </div>
        <div style="font-size:12px;color:var(--theme-text-muted);">{{ $totaux['services'] }} service(s) · {{ $totaux['agents'] }} agent(s)</div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="view-toggle">
            <button type="button" id="btnTableView" class="active" onclick="switchView('table')" title="Vue tableau">
                <i class="fas fa-table-list"></i>
            </button>
            <button type="button" id="btnCardView" onclick="switchView('cards')" title="Vue cartes">
                <i class="fas fa-grip"></i>
            </button>
        </div>
        <a href="{{ route('rh.divisions.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-sitemap"></i> Divisions
        </a>
        @can('create', App\Models\Service::class)
        <button type="button" class="action-btn action-btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalCreateService">
            <i class="fas fa-plus"></i> Nouveau service
        </button>
        @endcan
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     FILTRES
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('rh.services.index') }}" id="filterForm">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                    </span>
                    <input type="text" name="recherche" id="inputRecherche"
                           class="form-control border-start-0" placeholder="Nom du service…"
                           value="{{ request('recherche') }}">
                </div>
            </div>
            <select name="type" id="filterType" class="form-select" style="width:auto;min-width:160px;">
                <option value="">Tous les types</option>
                <option value="Clinique"        @selected(request('type') === 'Clinique')>Clinique</option>
                <option value="Administratif"   @selected(request('type') === 'Administratif')>Administratif</option>
                <option value="Aide_diagnostic" @selected(request('type') === 'Aide_diagnostic')>Aide diagnostic</option>
                <option value="Support"         @selected(request('type') === 'Support')>Support</option>
            </select>
            <select name="division" id="filterDivision" class="form-select" style="width:auto;min-width:180px;">
                <option value="">Toutes les divisions</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id_division }}" @selected(request('division') == $div->id_division)>
                        {{ $div->nom_division }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                <i class="fas fa-filter"></i> Filtrer
            </button>
            @if(request()->anyFilled(['recherche', 'type', 'division']))
                <a href="{{ route('rh.services.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════════════════════════
     VUE TABLEAU
     ═══════════════════════════════════════════════════════════ --}}
<div id="viewTable">
@if($services->count() > 0)
<div class="panel p-0" style="overflow:hidden;border-radius:12px;">
    <div class="table-responsive">
        <table class="services-table" id="servicesTableEl">
            <thead>
                <tr>
                    <th>Service</th>
                    <th class="d-none d-md-table-cell">Type</th>
                    <th class="d-none d-lg-table-cell">Division</th>
                    <th class="d-none d-md-table-cell">Manager</th>
                    <th>Agents</th>
                    <th class="d-none d-md-table-cell">Tél.</th>
                    <th class="text-end pe-3" style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                @php
                    $typeClass = match($service->type_service) {
                        'Clinique'        => 'tb-clinique',
                        'Administratif'   => 'tb-administratif',
                        'Aide_diagnostic' => 'tb-aide',
                        'Support'         => 'tb-support',
                        default           => 'tb-clinique',
                    };
                    $typeLabel = match($service->type_service) {
                        'Aide_diagnostic' => 'Aide diag.',
                        default           => $service->type_service,
                    };
                @endphp
                <tr data-nom="{{ strtolower($service->nom_service) }}"
                    data-type="{{ $service->type_service }}"
                    data-division="{{ $service->id_division }}">
                    <td>
                        <div class="fw-600" style="font-size:13.5px;">{{ $service->nom_service }}</div>
                        @if($service->tel_service)
                        <div class="text-muted d-md-none" style="font-size:11px;"><i class="fas fa-phone me-1"></i>{{ $service->tel_service }}</div>
                        @endif
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                    </td>
                    <td class="d-none d-lg-table-cell" style="color:var(--theme-text-muted);font-size:12.5px;">
                        {{ $service->division?->nom_division ?? '—' }}
                    </td>
                    <td class="d-none d-md-table-cell">
                        @if($service->manager && $service->manager->agent)
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:9px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($service->manager->agent->prenom ?? 'M', 0, 1)) }}
                                </div>
                                <span style="font-size:12px;font-weight:500;">{{ $service->manager->agent->nom ?? '—' }}</span>
                            </div>
                        @else
                            <span style="font-size:12px;color:var(--theme-text-muted);"><i class="fas fa-user-slash me-1"></i>Non assigné</span>
                        @endif
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:4px;background:#EFF6FF;color:#1E40AF;font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;">
                            <i class="fas fa-users" style="font-size:9px;"></i>{{ $service->agents_count }}
                        </span>
                    </td>
                    <td class="d-none d-md-table-cell" style="color:var(--theme-text-muted);font-size:12.5px;">
                        {{ $service->tel_service ?? '—' }}
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('rh.services.show', $service->id_service) }}"
                               class="btn-icon v" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('update', $service)
                            <button type="button" class="btn-icon e" title="Modifier"
                                    onclick="openEditModal({{ $service->id_service }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            @endcan
                            @can('assignerManager', $service)
                            <button type="button" class="btn-icon m" title="Assigner manager"
                                    onclick="openManagerModal({{ $service->id_service }})">
                                <i class="fas fa-user-tie"></i>
                            </button>
                            @endcan
                            @can('delete', $service)
                            <button type="button" class="btn-icon d" title="Supprimer"
                                    onclick="openDeleteModal({{ $service->id_service }}, '{{ addslashes($service->nom_service) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between px-4 py-3"
         style="border-top:1px solid var(--theme-border);background:var(--theme-bg-secondary);">
        <span style="font-size:12px;color:var(--theme-text-muted);">
            <strong>{{ $totaux['services'] }}</strong> service(s) au total
        </span>
    </div>
</div>
@else
<div class="panel text-center py-5">
    <div style="width:80px;height:80px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="fas fa-hospital" style="font-size:32px;color:#BFDBFE;"></i>
    </div>
    <h6 class="fw-600 mb-1">Aucun service trouvé</h6>
    <p class="text-muted mb-3" style="font-size:13.5px;">Créez le premier service hospitalier.</p>
    @can('create', App\Models\Service::class)
    <button type="button" class="action-btn action-btn-primary"
            data-bs-toggle="modal" data-bs-target="#modalCreateService">
        <i class="fas fa-plus"></i> Créer un service
    </button>
    @endcan
</div>
@endif
</div>{{-- /viewTable --}}

{{-- ═══════════════════════════════════════════════════════════
     VUE CARTES
     ═══════════════════════════════════════════════════════════ --}}
<div id="viewCards" style="display:none;">
@if($services->count() > 0)
<div class="services-grid" id="servicesGrid">
    @foreach($services as $service)
    @php
        $cardClass = match($service->type_service) {
            'Clinique'        => 't-clinique',
            'Administratif'   => 't-administratif',
            'Aide_diagnostic' => 't-aide',
            'Support'         => 't-support',
            default           => '',
        };
        $typeClass = match($service->type_service) {
            'Clinique'        => 'tb-clinique',
            'Administratif'   => 'tb-administratif',
            'Aide_diagnostic' => 'tb-aide',
            'Support'         => 'tb-support',
            default           => 'tb-clinique',
        };
        $typeLabel = match($service->type_service) {
            'Aide_diagnostic' => 'Aide diagnostic',
            default           => $service->type_service,
        };
        $colors = ['#0A4D8C','#059669','#7C3AED','#D97706','#0891B2','#DC2626'];
        $color  = $colors[$service->id_service % 6];
    @endphp
    <div class="service-card {{ $cardClass }}"
         data-nom="{{ strtolower($service->nom_service) }}"
         data-type="{{ $service->type_service }}"
         data-division="{{ $service->id_division }}">

        <div class="service-card-body">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <div class="fw-700" style="font-size:14px;color:var(--theme-text);">{{ $service->nom_service }}</div>
                    @if($service->division)
                        <div style="font-size:11.5px;color:var(--theme-text-muted);margin-top:2px;">
                            <i class="fas fa-sitemap me-1"></i>{{ $service->division->nom_division }}
                        </div>
                    @endif
                </div>
                <span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
            </div>

            <div class="d-flex gap-2 mt-3 flex-wrap">
                <span style="background:#EFF6FF;color:#1E40AF;font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;">
                    <i class="fas fa-users" style="font-size:9px;"></i> {{ $service->agents_count }} agent(s)
                </span>
                @if($service->tel_service)
                <span style="background:var(--theme-bg-secondary);color:var(--theme-text-muted);font-size:11px;padding:3px 9px;border-radius:20px;">
                    <i class="fas fa-phone" style="font-size:9px;"></i> {{ $service->tel_service }}
                </span>
                @endif
            </div>

            <div class="mt-3 pt-2" style="border-top:1px solid var(--theme-border);">
                @if($service->manager && $service->manager->agent)
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:9px;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr($service->manager->agent->prenom ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;">{{ $service->manager->agent->nom_complet }}</div>
                            <div style="font-size:10px;color:var(--theme-text-muted);">Manager</div>
                        </div>
                    </div>
                @else
                    <span style="font-size:12px;color:var(--theme-text-muted);"><i class="fas fa-user-slash me-1"></i>Sans manager</span>
                @endif
            </div>
        </div>

        <div class="service-card-footer">
            <a href="{{ route('rh.services.show', $service->id_service) }}"
               class="btn-icon v" title="Voir">
                <i class="fas fa-eye"></i><span>Voir</span>
            </a>
            @can('update', $service)
            <button type="button" class="btn-icon e" title="Modifier"
                    onclick="openEditModal({{ $service->id_service }})">
                <i class="fas fa-pen"></i><span>Modifier</span>
            </button>
            @endcan
            @can('assignerManager', $service)
            <button type="button" class="btn-icon m" title="Manager"
                    onclick="openManagerModal({{ $service->id_service }})">
                <i class="fas fa-user-tie"></i><span>Manager</span>
            </button>
            @endcan
            @can('delete', $service)
            <button type="button" class="btn-icon d" title="Supprimer"
                    onclick="openDeleteModal({{ $service->id_service }}, '{{ addslashes($service->nom_service) }}')">
                <i class="fas fa-trash"></i>
            </button>
            @endcan
        </div>
    </div>
    @endforeach
</div>
@endif
</div>{{-- /viewCards --}}


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — CRÉER UN SERVICE
     ═══════════════════════════════════════════════════════════════════════ --}}
@can('create', App\Models\Service::class)
<div class="modal fade" id="modalCreateService" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:var(--theme-panel-bg);">
            <form action="{{ route('rh.services.store') }}" method="POST">
                @csrf
                <div style="padding:24px 24px 0;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div>
                            <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                                <i class="fas fa-hospital-alt" style="font-size:18px;color:#0A4D8C;"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color:var(--theme-text);">Nouveau service hospitalier</h5>
                            <p style="font-size:13px;color:var(--theme-text-muted);margin:0;">Renseignez les informations du service</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div style="padding:20px 24px;">
                    <div style="font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--theme-text-muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--theme-border);">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Informations générales
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="modal-label">Nom du service <span class="text-danger">*</span></label>
                            <input type="text" name="nom_service" class="modal-input @error('nom_service') is-invalid @enderror"
                                   value="{{ old('nom_service') }}" required placeholder="ex: Cardiologie, Bloc Opératoire…">
                            @error('nom_service')<div class="invalid-feedback" style="font-size:12px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select name="type_service" class="modal-input @error('type_service') is-invalid @enderror" required>
                                <option value="">— Choisir —</option>
                                <option value="Clinique"        {{ old('type_service') == 'Clinique' ? 'selected' : '' }}>Clinique</option>
                                <option value="Administratif"   {{ old('type_service') == 'Administratif' ? 'selected' : '' }}>Administratif</option>
                                <option value="Aide_diagnostic" {{ old('type_service') == 'Aide_diagnostic' ? 'selected' : '' }}>Aide au diagnostic</option>
                                <option value="Support"         {{ old('type_service') == 'Support' ? 'selected' : '' }}>Support</option>
                            </select>
                            @error('type_service')<div class="invalid-feedback" style="font-size:12px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Division</label>
                            <select name="id_division" class="modal-input">
                                <option value="">— Aucune division —</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id_division }}" {{ old('id_division') == $div->id_division ? 'selected' : '' }}>
                                        {{ $div->nom_division }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Téléphone</label>
                            <input type="text" name="tel_service" class="modal-input" value="{{ old('tel_service') }}" placeholder="ex: 33 869 00 00">
                        </div>
                        <div class="col-12">
                            <label class="modal-label">Manager responsable</label>
                            <select name="id_agent_manager" class="modal-input">
                                <option value="">— Aucun pour l'instant —</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}" {{ old('id_agent_manager') == $mgr->id ? 'selected' : '' }}>
                                        {{ $mgr->agent?->nom_complet ?? $mgr->login }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div style="padding:16px 24px 20px;border-top:1px solid var(--theme-border);display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i> Créer le service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — MODIFIER UN SERVICE + GÉRER LES AGENTS
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditService" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="background:var(--theme-panel-bg);">

            {{-- Header --}}
            <div style="padding:24px 24px 0;">
                <div class="d-flex align-items-start justify-content-between mb-1">
                    <div>
                        <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#FFFBEB,#FEF3C7);display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                            <i class="fas fa-edit" style="font-size:18px;color:#D97706;"></i>
                        </div>
                        <h5 class="fw-bold mb-1" style="color:var(--theme-text);" id="editModalTitle">Modifier le service</h5>
                        <p style="font-size:13px;color:var(--theme-text-muted);margin:0;" id="editModalSubtitle">—</p>
                    </div>
                    <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
                </div>
            </div>

            {{-- Tabs nav --}}
            <div style="padding:0 24px;">
                <div class="modal-nav-tabs">
                    <button type="button" class="active" onclick="switchEditTab('info', this)">
                        <i class="fas fa-info-circle me-1"></i>Informations
                    </button>
                    <button type="button" onclick="switchEditTab('agents', this)">
                        <i class="fas fa-users me-1"></i>Agents <span id="editAgentsCount" style="background:#EFF6FF;color:#1E40AF;border-radius:20px;padding:1px 7px;font-size:10px;margin-left:4px;font-weight:700;">0</span>
                    </button>
                </div>
            </div>

            {{-- TAB 1 : INFORMATIONS --}}
            <div id="editTabInfo" style="padding:0 24px 4px;">
                <form id="formEditService" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="modal-label">Nom du service <span class="text-danger">*</span></label>
                            <input type="text" id="editNom" name="nom_service" class="modal-input" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select id="editType" name="type_service" class="modal-input" required>
                                <option value="Clinique">Clinique</option>
                                <option value="Administratif">Administratif</option>
                                <option value="Aide_diagnostic">Aide au diagnostic</option>
                                <option value="Support">Support</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Division</label>
                            <select id="editDivision" name="id_division" class="modal-input">
                                <option value="">— Aucune —</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id_division }}">{{ $div->nom_division }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Téléphone</label>
                            <input type="text" id="editTel" name="tel_service" class="modal-input" placeholder="+221 33 xxx xx xx">
                        </div>
                        <div class="col-12">
                            <label class="modal-label">Manager responsable</label>
                            <select id="editManager" name="id_agent_manager" class="modal-input">
                                <option value="">— Aucun —</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}">{{ $mgr->agent?->nom_complet ?? $mgr->login }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            {{-- TAB 2 : AGENTS --}}
            <div id="editTabAgents" style="display:none;padding:0 24px 4px;">

                {{-- Ajouter un agent --}}
                <div style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:10px;padding:14px;margin-bottom:16px;">
                    <div class="modal-label mb-2"><i class="fas fa-user-plus me-1 text-success"></i>Affecter un agent à ce service</div>
                    <form id="formAttachAgent" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <select id="addAgentSelect" name="agent_id" class="modal-input" style="flex:1;">
                                <option value="">— Sélectionner un agent —</option>
                            </select>
                            <button type="submit" class="action-btn action-btn-primary" style="white-space:nowrap;">
                                <i class="fas fa-plus"></i> Affecter
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Liste des agents actuels --}}
                <div class="modal-label mb-2"><i class="fas fa-users me-1 text-primary"></i>Agents dans ce service</div>
                <div id="editAgentsList" style="max-height:280px;overflow-y:auto;"></div>
            </div>

            {{-- Footer --}}
            <div style="padding:16px 24px 20px;border-top:1px solid var(--theme-border);display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" form="formEditService" id="btnSaveEdit"
                        class="action-btn action-btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — ASSIGNER / CHANGER DE MANAGER
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAssignManager" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background:var(--theme-panel-bg);">
            <form id="formAssignManager" method="POST">
                @csrf
                <div style="padding:24px 24px 0;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0" style="color:var(--theme-text);">
                            <i class="fas fa-user-tie me-2" style="color:#7C3AED;"></i>Assigner manager
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <p id="managerModalSubtitle" class="text-muted small mb-3"></p>
                    <label class="modal-label">Manager</label>
                    <select id="managerSelect" name="id_agent_manager" class="modal-input">
                        <option value="">— Retirer le manager —</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}">{{ $mgr->agent?->nom_complet ?? $mgr->login }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="padding:16px 24px 20px;border-top:1px solid var(--theme-border);margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-check"></i> Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — SUPPRIMER UN SERVICE
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDeleteService" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background:var(--theme-panel-bg);">
            <form id="formDeleteService" method="POST">
                @csrf @method('DELETE')
                <div style="padding:24px 24px 0;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-danger mb-0"><i class="fas fa-trash me-2"></i>Supprimer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <p style="font-size:14px;color:var(--theme-text);">
                        Supprimer définitivement<br>
                        <strong id="deleteServiceName"></strong> ?
                    </p>
                    <p class="text-muted small mb-0">Cette action est irréversible.</p>
                </div>
                <div style="padding:16px 24px 20px;border-top:1px solid var(--theme-border);margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger d-flex align-items-center gap-2" style="border-radius:8px;font-size:13px;font-weight:500;padding:9px 16px;">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@php
    $jsServices = $services->map(function ($s) {
        return [
            'id'          => $s->id_service,
            'nom'         => $s->nom_service,
            'type'        => $s->type_service,
            'id_division' => $s->id_division,
            'tel'         => $s->tel_service,
            'id_manager'  => $s->id_agent_manager,
            'agents'      => $s->agents->map(function ($a) {
                return [
                    'id'        => $a->id_agent,
                    'nom'       => $a->nom,
                    'prenom'    => $a->prenom,
                    'matricule' => $a->matricule,
                    'fontion'   => $a->fontion,
                    'statut'    => $a->statut,
                ];
            })->values()->all(),
        ];
    })->values()->all();

    $jsAllAgents = $allAgents->map(function ($a) {
        return [
            'id'         => $a->id_agent,
            'id_service' => $a->id_service,
            'nom'        => $a->nom,
            'prenom'     => $a->prenom,
            'matricule'  => $a->matricule,
            'fontion'    => $a->fontion,
        ];
    })->values()->all();
@endphp

@push('scripts')
<script>
/* ══════════════════════════════════════════════════════════
   DONNÉES PHP → JS
   ══════════════════════════════════════════════════════════ */
const CSRF = '{{ csrf_token() }}';
const servicesData = @json($jsServices);
const allAgents    = @json($jsAllAgents);

const ROUTES = {
    update:      (id) => `/rh/services/${id}`,
    manager:     (id) => `/rh/services/${id}/assigner-manager`,
    destroy:     (id) => `/rh/services/${id}`,
    attach:      (id) => `/rh/services/${id}/attach-agent`,
    detach:      (id, aid) => `/rh/services/${id}/agents/${aid}`,
};

/* ══════════════════════════════════════════════════════════
   TOGGLE TABLE / CARTES
   ══════════════════════════════════════════════════════════ */
function switchView(v) {
    const isTable = v === 'table';
    document.getElementById('viewTable').style.display = isTable ? '' : 'none';
    document.getElementById('viewCards').style.display = isTable ? 'none' : '';
    document.getElementById('btnTableView').classList.toggle('active', isTable);
    document.getElementById('btnCardView').classList.toggle('active', !isTable);
    localStorage.setItem('servicesView', v);
}

/* Restaurer la dernière vue */
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('servicesView');
    if (saved === 'cards') switchView('cards');
});

/* ══════════════════════════════════════════════════════════
   FILTRES CÔTÉ CLIENT
   ══════════════════════════════════════════════════════════ */
function applyFilters() {
    const q    = document.getElementById('inputRecherche').value.toLowerCase().trim();
    const type = document.getElementById('filterType').value;
    const div  = document.getElementById('filterDivision').value;

    // Tableau
    document.querySelectorAll('#servicesTableEl tbody tr').forEach(row => {
        const matchNom  = !q    || row.dataset.nom?.includes(q);
        const matchType = !type || row.dataset.type === type;
        const matchDiv  = !div  || row.dataset.division == div;
        row.style.display = (matchNom && matchType && matchDiv) ? '' : 'none';
    });
    // Cartes
    document.querySelectorAll('#servicesGrid .service-card').forEach(card => {
        const matchNom  = !q    || card.dataset.nom?.includes(q);
        const matchType = !type || card.dataset.type === type;
        const matchDiv  = !div  || card.dataset.division == div;
        card.style.display = (matchNom && matchType && matchDiv) ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('inputRecherche').value = '';
    document.getElementById('filterType').value     = '';
    document.getElementById('filterDivision').value = '';
    applyFilters();
}

/* ══════════════════════════════════════════════════════════
   MODAL EDIT — Ouvrir + peupler
   ══════════════════════════════════════════════════════════ */
let currentEditId = null;
let currentEditTab = 'info';

function openEditModal(serviceId) {
    const s = servicesData.find(x => x.id == serviceId);
    if (!s) return;
    currentEditId = serviceId;

    // Titre
    document.getElementById('editModalTitle').textContent   = 'Modifier le service';
    document.getElementById('editModalSubtitle').textContent = s.nom;

    // Formulaire info
    document.getElementById('formEditService').action = ROUTES.update(serviceId);
    document.getElementById('editNom').value      = s.nom  || '';
    document.getElementById('editType').value     = s.type || '';
    document.getElementById('editDivision').value = s.id_division || '';
    document.getElementById('editTel').value      = s.tel  || '';
    document.getElementById('editManager').value  = s.id_manager || '';

    // Onglet agents
    document.getElementById('editAgentsCount').textContent = s.agents.length;
    renderAgentsTab(s);

    // Reset sur tab info
    switchEditTab('info', document.querySelector('.modal-nav-tabs button'));

    new bootstrap.Modal(document.getElementById('modalEditService')).show();
}

function switchEditTab(tab, btn) {
    currentEditTab = tab;
    document.getElementById('editTabInfo').style.display   = tab === 'info'   ? '' : 'none';
    document.getElementById('editTabAgents').style.display = tab === 'agents' ? '' : 'none';
    document.querySelectorAll('.modal-nav-tabs button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    // Le bouton "Enregistrer" n'est visible que sur l'onglet info
    document.getElementById('btnSaveEdit').style.display = tab === 'info' ? '' : 'none';
}

/* ══════════════════════════════════════════════════════════
   ONGLET AGENTS — Rendu dynamique
   ══════════════════════════════════════════════════════════ */
const AVATAR_COLORS = ['#0A4D8C','#059669','#7C3AED','#D97706','#0891B2','#DC2626'];

function renderAgentsTab(service) {
    const container = document.getElementById('editAgentsList');

    // 1. Liste des agents actuels
    if (!service.agents || service.agents.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-users fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                <small>Aucun agent dans ce service</small>
            </div>`;
    } else {
        container.innerHTML = service.agents.map((a, i) => {
            const color    = AVATAR_COLORS[a.id % 6];
            const initials = (a.prenom[0] + a.nom[0]).toUpperCase();
            const badge    = a.statut === 'actif'
                ? '<span style="background:#D1FAE5;color:#065F46;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">Actif</span>'
                : `<span style="background:#F3F4F6;color:#374151;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">${a.statut ?? '—'}</span>`;
            return `
                <div class="agent-modal-row">
                    <div class="agent-avatar-xs me-2" style="background:${color};">${initials}</div>
                    <div class="flex-grow-1">
                        <div style="font-size:13px;font-weight:600;">${a.prenom} ${a.nom}</div>
                        <div style="font-size:11px;color:var(--theme-text-muted);">${a.matricule} — ${a.fontion || '—'}</div>
                    </div>
                    ${badge}
                    <form action="${ROUTES.detach(service.id, a.id)}" method="POST" style="display:inline;margin-left:8px;">
                        <input type="hidden" name="_token" value="${CSRF}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:6px;font-size:11px;padding:3px 8px;"
                                onclick="return confirm('Retirer ${a.prenom} ${a.nom} de ce service ?')">
                            <i class="fas fa-times"></i> Retirer
                        </button>
                    </form>
                </div>`;
        }).join('');
    }

    // 2. Select des agents disponibles (pas dans ce service)
    const available = allAgents.filter(a => a.id_service != service.id);
    const sel = document.getElementById('addAgentSelect');
    sel.innerHTML = '<option value="">— Sélectionner un agent —</option>'
        + available.map(a => `<option value="${a.id}">${a.prenom} ${a.nom} (${a.matricule})</option>`).join('');

    // 3. Action du formulaire d'ajout
    document.getElementById('formAttachAgent').action = ROUTES.attach(service.id);
}

/* ══════════════════════════════════════════════════════════
   MODAL MANAGER
   ══════════════════════════════════════════════════════════ */
function openManagerModal(serviceId) {
    const s = servicesData.find(x => x.id == serviceId);
    if (!s) return;
    document.getElementById('formAssignManager').action  = ROUTES.manager(serviceId);
    document.getElementById('managerModalSubtitle').textContent = 'Service : ' + s.nom;
    document.getElementById('managerSelect').value       = s.id_manager || '';
    new bootstrap.Modal(document.getElementById('modalAssignManager')).show();
}

/* ══════════════════════════════════════════════════════════
   MODAL SUPPRESSION
   ══════════════════════════════════════════════════════════ */
function openDeleteModal(serviceId, nom) {
    document.getElementById('formDeleteService').action = ROUTES.destroy(serviceId);
    document.getElementById('deleteServiceName').textContent = '« ' + nom + ' »';
    new bootstrap.Modal(document.getElementById('modalDeleteService')).show();
}

/* ══════════════════════════════════════════════════════════
   TOAST
   ══════════════════════════════════════════════════════════ */
function showToast(message, type) {
    const cfg = {
        success: { bg:'#10B981', icon:'fa-check-circle' },
        error:   { bg:'#EF4444', icon:'fa-exclamation-circle' },
    };
    const c  = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:420px;animation:toastIn .3s ease;">
            <i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i>
            <span>${message}</span>
            <button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button>
        </div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}

document.addEventListener('DOMContentLoaded', () => {
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif

    {{-- Ré-ouvrir le modal edit si erreurs de validation --}}
    @if($errors->any())
        const errServiceId = {{ session('edit_service_id', 0) }};
        if (errServiceId) openEditModal(errServiceId);
    @endif
});
</script>
@endpush
