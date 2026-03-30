@extends('layouts.master')

@section('title', 'Gestion du Personnel')
@section('page-title', 'Gestion du Personnel')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Personnel</li>
@endsection

@push('styles')
<style>
/* ════════════════════════════════════════════════════════════
   KPI CARDS — même style que le dashboard
   ════════════════════════════════════════════════════════════ */
.kpi-card {
    border-radius: 12px; padding: 20px 24px;
    transition: box-shadow 200ms, transform 200ms;
    position: relative; overflow: hidden;
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
   ACTION BUTTONS — charte graphique
   ════════════════════════════════════════════════════════════ */
.action-btn {
    display:inline-flex;align-items:center;gap:8px;
    padding:9px 16px;border-radius:8px;font-size:13px;
    font-weight:500;text-decoration:none;border:none;cursor:pointer;
    transition:all 180ms;white-space:nowrap;
}
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30);transform:translateY(-1px); }
.action-btn-outline {
    background:var(--theme-panel-bg);color:var(--theme-text);
    border:1px solid var(--theme-border);
}
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }

/* ════════════════════════════════════════════════════════════
   FILTER — styles handled by master layout .filter-bar
   ════════════════════════════════════════════════════════════ */
.input-group-text {
    background:var(--theme-bg-secondary);
    border-color:var(--theme-border);
    border-radius:8px 0 0 8px;
    color:var(--theme-text-muted);
}

/* ════════════════════════════════════════════════════════════
   TABLEAU
   ════════════════════════════════════════════════════════════ */
.agents-table { width:100%;border-collapse:separate;border-spacing:0; }
.agents-table thead th {
    padding:11px 14px;font-size:10.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.06em;
    background:var(--theme-bg-secondary);color:var(--theme-text-muted);
    border-bottom:2px solid var(--theme-border);
    white-space:nowrap;
}
.agents-table thead th:first-child { border-radius:10px 0 0 0; padding-left:20px; }
.agents-table thead th:last-child  { border-radius:0 10px 0 0; }
.agents-table tbody td {
    padding:13px 14px;border-bottom:1px solid var(--theme-border);
    font-size:13px;vertical-align:middle;color:var(--theme-text);
    transition:background 100ms;
}
.agents-table tbody td:first-child { padding-left:20px; }
.agents-table tbody tr:hover td { background:var(--sirh-primary-hover); }
.agents-table tbody tr:last-child td { border-bottom:none; }

/* ════════════════════════════════════════════════════════════
   AVATAR INLINE
   ════════════════════════════════════════════════════════════ */
.agent-avatar-sm {
    width:40px;height:40px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:14px;font-weight:700;color:#fff;flex-shrink:0;
    border:2px solid var(--theme-panel-bg);
    box-shadow:0 2px 8px rgba(0,0,0,.12);
    overflow:hidden;
}
.agent-avatar-sm img { width:100%;height:100%;object-fit:cover; }

/* ════════════════════════════════════════════════════════════
   BADGES STATUT
   ════════════════════════════════════════════════════════════ */
.badge-pill {
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 11px;border-radius:20px;
    font-size:11px;font-weight:600;white-space:nowrap;
}
.bp-actif    { background:#D1FAE5;color:#065F46; }
.bp-conge    { background:#FEF3C7;color:#92400E; }
.bp-suspendu { background:#FEE2E2;color:#991B1B; }
.bp-retraite { background:#F3F4F6;color:#374151; }
.bp-compte   { background:#EFF6FF;color:#1E40AF; }
.bp-attente  { background:#FEF9C3;color:#78350F; }

/* ════════════════════════════════════════════════════════════
   BOUTONS ACTIONS TABLEAU
   ════════════════════════════════════════════════════════════ */
.btn-icon {
    width:32px;height:32px;border-radius:8px;
    display:inline-flex;align-items:center;justify-content:center;
    border:1px solid var(--theme-border);
    background:var(--theme-panel-bg);color:var(--theme-text-muted);
    cursor:pointer;transition:all 150ms;font-size:13px;
    text-decoration:none;padding:0;
}
.btn-icon:hover { transform:translateY(-1px); }
.btn-icon.v:hover { background:#EFF6FF;border-color:#BFDBFE;color:#1E40AF; }
.btn-icon.e:hover { background:#F0FDF4;border-color:#BBF7D0;color:#15803D; }
.btn-icon.d:hover { background:#FEF2F2;border-color:#FECACA;color:#DC2626; }

/* ════════════════════════════════════════════════════════════
   VUE CARTES AGENTS
   ════════════════════════════════════════════════════════════ */
.agents-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(210px, 1fr));
    gap:16px;
}
.agent-card {
    background:var(--theme-panel-bg);border:1px solid var(--theme-border);
    border-radius:14px;overflow:hidden;
    transition:transform 150ms,box-shadow 200ms;
    display:flex;flex-direction:column;
}
.agent-card:hover { transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,0,0,.11); }
.agent-card-top {
    padding:24px 16px 16px;text-align:center;
    background:linear-gradient(160deg,#EFF6FF 0%,#DBEAFE 100%);
    position:relative;
}
.agent-card-avatar-lg {
    width:68px;height:68px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:24px;font-weight:700;color:#fff;
    border:4px solid #fff;margin:0 auto 10px;
    box-shadow:0 4px 14px rgba(10,77,140,.25);overflow:hidden;
}
.agent-card-avatar-lg img { width:100%;height:100%;object-fit:cover; }
.agent-card-badge { position:absolute;top:10px;right:10px; }
.agent-card-body { padding:12px 14px;flex:1; }
.agent-card-meta { display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--theme-text-muted);margin-bottom:4px; }
.agent-card-footer { padding:10px 14px;border-top:1px solid var(--theme-border);display:flex;gap:6px; }
.agent-card-footer .btn-icon { flex:1;width:auto;border-radius:8px;height:32px; }

/* ════════════════════════════════════════════════════════════
   TOGGLE VUE
   ════════════════════════════════════════════════════════════ */
.view-toggle { display:flex;border:1px solid var(--theme-border);border-radius:8px;overflow:hidden; }
.view-toggle button {
    padding:7px 13px;border:none;background:none;cursor:pointer;font-size:13px;
    color:var(--theme-text-muted);transition:all 120ms;
}
.view-toggle button.active { background:#0A4D8C;color:#fff; }

/* ════════════════════════════════════════════════════════════
   MODALS — Charte SIRH
   ════════════════════════════════════════════════════════════ */
.modal-content { border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.18); }
.modal-header-sirh {
    padding:20px 24px 0;border:none;
    display:flex;align-items:flex-start;justify-content:space-between;
}
.modal-header-sirh .modal-icon {
    width:52px;height:52px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;font-size:22px;
    margin-bottom:12px;
}
.modal-header-sirh h5 { font-size:17px;font-weight:700;margin-bottom:4px; }
.modal-header-sirh p  { font-size:13px;color:var(--theme-text-muted);margin-bottom:0; }
.modal-body-sirh {
    padding: 20px 24px;
    max-height: 420px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #0A4D8C #E5E7EB;
}
.modal-body-sirh::-webkit-scrollbar { width: 8px; }
.modal-body-sirh::-webkit-scrollbar-track { background: #E5E7EB; border-radius: 4px; }
.modal-body-sirh::-webkit-scrollbar-thumb { background: #0A4D8C; border-radius: 4px; }
.modal-body-sirh::-webkit-scrollbar-thumb:hover { background: #1565C0; }
.modal-footer-sirh { padding:16px 24px 20px;border:none;gap:10px;justify-content:flex-end; }

/* Tabs dans modal */
.modal-nav-tabs {
    display:flex;gap:0;border-bottom:2px solid var(--theme-border);
    margin:0 -24px 20px;padding:0 24px;overflow-x:auto;
}
.modal-nav-tabs button {
    padding:10px 16px;border:none;background:none;cursor:pointer;
    font-size:12.5px;font-weight:500;color:var(--theme-text-muted);
    border-bottom:2px solid transparent;margin-bottom:-2px;
    transition:all 150ms;white-space:nowrap;
}
.modal-nav-tabs button.active { color:#0A4D8C;border-bottom-color:#0A4D8C;font-weight:600; }
.modal-nav-tabs button:hover:not(.active) { color:var(--theme-text);background:var(--theme-bg-secondary);border-radius:6px 6px 0 0; }

/* Sections de formulaire */
.form-section-label {
    font-size:10.5px;font-weight:800;text-transform:uppercase;
    letter-spacing:.07em;color:var(--theme-text-muted);
    margin-bottom:14px;padding-bottom:8px;
    border-bottom:1px solid var(--theme-border);
    display:flex;align-items:center;gap:8px;
}
.form-label-sm { font-size:12px;font-weight:600;margin-bottom:5px; }
.form-control-sirh,.form-select-sirh {
    border-radius:8px;font-size:13px;padding:9px 12px;
    border:1.5px solid var(--theme-border);
    background:var(--theme-panel-bg);color:var(--theme-text);
    transition:border-color 150ms,box-shadow 150ms;width:100%;
}
.form-control-sirh:focus,.form-select-sirh:focus {
    outline:none;border-color:#0A4D8C;
    box-shadow:0 0 0 3px rgba(10,77,140,.12);
}
.form-control-sirh.is-invalid,.form-select-sirh.is-invalid { border-color:#EF4444; }

/* Photo upload */
.photo-upload-zone {
    width:90px;height:90px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#EFF6FF,#DBEAFE);
    border:3px dashed #BFDBFE;cursor:pointer;
    position:relative;overflow:hidden;margin:0 auto;
    transition:border-color 150ms;
}
.photo-upload-zone:hover { border-color:#0A4D8C; }
.photo-upload-zone img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.photo-upload-overlay {
    position:absolute;inset:0;background:rgba(10,77,140,.6);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    opacity:0;transition:opacity 150ms;color:#fff;font-size:18px;
}
.photo-upload-zone:hover .photo-upload-overlay { opacity:1; }

/* Champ sensible */
.field-sensitive .form-label-sm::after {
    content:"🔒";font-size:10px;margin-left:4px;
}
.hint-encrypted {
    font-size:10.5px;color:#9CA3AF;margin-top:3px;
    display:flex;align-items:center;gap:4px;
}

/* Famille rows */
.famille-item {
    background:var(--theme-bg-secondary);border:1px solid var(--theme-border);
    border-radius:10px;padding:12px;margin-bottom:8px;position:relative;
}
.famille-item .btn-remove {
    position:absolute;top:8px;right:8px;
    width:24px;height:24px;border-radius:6px;
    background:#FEF2F2;border:none;color:#DC2626;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;font-size:11px;padding:0;
}
.famille-item .btn-remove:hover { background:#DC2626;color:#fff; }

/* DARK MODE */
[data-theme="dark"] .kpi-card.blue { background:rgba(10,77,140,.15);border:1px solid rgba(10,77,140,.30); }
[data-theme="dark"] .kpi-card.green { background:rgba(5,150,105,.15);border:1px solid rgba(5,150,105,.30); }
[data-theme="dark"] .kpi-card.amber { background:rgba(217,119,6,.15);border:1px solid rgba(217,119,6,.30); }
[data-theme="dark"] .kpi-card.red { background:rgba(220,38,38,.15);border:1px solid rgba(220,38,38,.30); }
[data-theme="dark"] .agents-table tbody tr:hover td { background:rgba(255,255,255,.04); }
[data-theme="dark"] .bp-actif    { background:rgba(16,185,129,.18);color:#34d399; }
[data-theme="dark"] .bp-conge    { background:rgba(245,158,11,.18);color:#fbbf24; }
[data-theme="dark"] .bp-suspendu { background:rgba(239,68,68,.18);color:#f87171; }
[data-theme="dark"] .bp-retraite { background:rgba(107,114,128,.18);color:#9ca3af; }
[data-theme="dark"] .bp-compte   { background:rgba(59,130,246,.18);color:#93c5fd; }
[data-theme="dark"] .agent-card-top { background:linear-gradient(160deg,rgba(15,23,42,1),rgba(23,37,84,1)); }
[data-theme="dark"] .form-control-sirh,.form-select-sirh { background:#0d1117;border-color:#30363d;color:#e6edf3; }
[data-theme="dark"] .modal-content { background:#161b22;border:1px solid #30363d; }
[data-theme="dark"] .modal-nav-tabs { border-bottom-color:#30363d; }
[data-theme="dark"] code { color:#58a6ff !important; }

.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-pending { background:#FEF3C7;color:#92400E; }
.badge-urgent  { background:#FEE2E2;color:#991B1B; }
.badge-ok      { background:#D1FAE5;color:#065F46; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     KPI CARDS — identique au dashboard RH
     ═══════════════════════════════════════════════════════════ --}}
<div class="section-title">Tableau de bord du personnel</div> <br>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card blue panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#EFF6FF;">
                    <i class="fas fa-users" style="color:#0A4D8C;"></i>
                </div>
                <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">Total</span>
            </div>
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Agents enregistrés</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card green panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#ECFDF5;">
                    <i class="fas fa-user-check" style="color:#059669;"></i>
                </div>
                <span class="badge-status badge-ok" >En poste</span>
            </div>
            <div class="kpi-value">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Agents actifs</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card amber panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;">
                    <i class="fas fa-umbrella-beach" style="color:#D97706;"></i>
                </div>
                <span class="badge-status badge-pending">Congés</span>
            </div>
            <div class="kpi-value">{{ $stats['en_conge'] }}</div>
            <div class="kpi-label">Agents en congé</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card red panel">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FEF2F2;">
                    <i class="fas fa-user-slash" style="color:#DC2626;"></i>
                </div>
                <span class="badge-status badge-urgent">Alerte</span>
            </div>
            <div class="kpi-value">{{ $stats['suspendus'] }}</div>
            <div class="kpi-label">Agents suspendus</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     BARRE ACTIONS
     ═══════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <div>
            <div class="fw-700" style="font-size:15px;">Liste des agents
                <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#EFF6FF;color:#0A4D8C;border-radius:50%;font-size:11px;font-weight:700;margin-left:6px;">{{ $agents->total() }}</span>
            </div>
            <div style="font-size:12px;color:var(--theme-text-muted);">{{ $agents->total() }} agent(s) dans le système</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- Toggle vue --}}
        <div class="view-toggle">
            <button type="button" id="btnTableView" class="active" onclick="switchView('table')" title="Vue tableau">
                <i class="fas fa-table-list"></i>
            </button>
            <button type="button" id="btnCardView" onclick="switchView('cards')" title="Vue cartes">
                <i class="fas fa-grip"></i>
            </button>
        </div>
        @can('export', \App\Models\Agent::class)
        <a href="{{ route('rh.agents.export.excel', request()->query()) }}"
           class="action-btn action-btn-outline">
            <i class="fas fa-file-excel"></i> Export Excel
            @if(request()->anyFilled(['recherche','service','statut','sexe']))
            <span style="font-size:10px;background:#D1FAE5;color:#065F46;padding:1px 5px;border-radius:10px;font-weight:700;">filtré</span>
            @endif
        </a>
        @endcan
        @can('create', \App\Models\Agent::class)
        <button type="button" class="action-btn action-btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalAddAgent">
            <i class="fas fa-user-plus"></i> Nouvel agent
        </button>
        @endcan
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     FILTRES
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('rh.agents.index') }}">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                    </span>
                    <input type="text" name="recherche" class="form-control border-start-0"
                           placeholder="Nom, prénom, matricule, fonction…"
                           value="{{ $filters['recherche'] ?? '' }}">
                </div>
            </div>
            <select name="service" class="form-select" style="width:auto;min-width:160px;">
                <option value="">Tous les services</option>
                @foreach($services as $s)
                    <option value="{{ $s->id_service }}" @selected(($filters['service'] ?? '') == $s->id_service)>
                        {{ $s->nom_service }}
                    </option>
                @endforeach
            </select>
            <select name="statut" class="form-select" style="width:auto;min-width:150px;">
                <option value="">Tous les statuts</option>
                <option value="Actif"    @selected(($filters['statut'] ?? '') === 'Actif')>Actif</option>
                <option value="En_congé" @selected(($filters['statut'] ?? '') === 'En_congé')>En congé</option>
                <option value="Suspendu" @selected(($filters['statut'] ?? '') === 'Suspendu')>Suspendu</option>
                <option value="Retraité" @selected(($filters['statut'] ?? '') === 'Retraité')>Retraité</option>
            </select>
            <select name="sexe" class="form-select" style="width:auto;min-width:130px;">
                <option value="">Tous les sexes</option>
                <option value="M" @selected(($filters['sexe'] ?? '') === 'M')>Masculin</option>
                <option value="F" @selected(($filters['sexe'] ?? '') === 'F')>Féminin</option>
            </select>
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                <i class="fas fa-filter"></i> Filtrer
            </button>
            @if(!empty($filters['recherche']) || !empty($filters['service']) || !empty($filters['statut']) || !empty($filters['sexe']))
                <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
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
@if($agents->count() > 0)
<div class="panel p-0" style="overflow:hidden;border-radius:12px;">
    <div class="table-responsive">
        <table class="agents-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Agent</th>
                    <th class="d-none d-md-table-cell">Fonction</th>
                    <th class="d-none d-lg-table-cell">Service</th>
                    <th class="d-none d-xl-table-cell">Date recrut.</th>
                    <th>Statut</th>
                    <th class="d-none d-xl-table-cell">Compte</th>
                    <th class="text-end pe-3" style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $agent)
                @php
                    $colors = ['#0A4D8C','#059669','#7C3AED','#D97706','#0891B2','#DC2626'];
                    $color  = $colors[$agent->id_agent % 6];
                    $bpClass = match($agent->statut_agent) {
                        'Actif'    => 'bp-actif',
                        'En_congé' => 'bp-conge',
                        'Suspendu' => 'bp-suspendu',
                        'Retraité' => 'bp-retraite',
                        default    => 'bp-retraite',
                    };
                    $bpIcon = match($agent->statut_agent) {
                        'Actif'    => 'fa-circle-check',
                        'En_congé' => 'fa-umbrella-beach',
                        'Suspendu' => 'fa-ban',
                        'Retraité' => 'fa-door-open',
                        default    => 'fa-circle',
                    };
                    $statutLabel = $agent->statut_agent === 'En_congé' ? 'En congé' : $agent->statut_agent;
                @endphp
                <tr>
                    <td>
                        <code style="font-size:11.5px;background:var(--theme-bg-secondary);padding:3px 8px;border-radius:6px;font-weight:700;letter-spacing:.5px;">
                            {{ $agent->matricule }}
                        </code>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="agent-avatar-sm" style="background:{{ $color }};">
                                @if($agent->photo)
                                    <img src="{{ asset('storage/'.$agent->photo) }}" alt="">
                                @else
                                    {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $agent->prenom }} {{ $agent->nom }}</div>
                                @if($agent->grade)
                                <div style="font-size:11px;color:var(--theme-text-muted);">{{ $agent->grade }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell" style="color:var(--theme-text-muted);">{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') ?? '—' }}</td>
                    <td class="d-none d-lg-table-cell">
                        @if($agent->service)
                        <span style="font-size:12px;background:var(--theme-bg-secondary);padding:3px 9px;border-radius:6px;font-weight:500;">
                            {{ $agent->service->nom_service }}
                        </span>
                        @else <span style="color:var(--theme-text-muted);">—</span>
                        @endif
                    </td>
                    <td class="d-none d-xl-table-cell" style="color:var(--theme-text-muted);font-size:12.5px;">
                        {{ $agent->date_prise_service?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td>
                        <span class="badge-pill {{ $bpClass }}">
                            <i class="fas {{ $bpIcon }}" style="font-size:9px;"></i>
                            {{ $statutLabel }}
                        </span>
                    </td>
                    <td class="d-none d-xl-table-cell">
                        @if($agent->user_id)
                            <span class="badge-pill bp-compte"><i class="fas fa-check" style="font-size:9px;"></i>Actif</span>
                        @else
                            <span class="badge-pill bp-attente"><i class="fas fa-clock" style="font-size:9px;"></i>En attente</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('rh.agents.show', $agent->id_agent) }}"
                               class="btn-icon v" title="Voir le dossier complet">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('update', $agent)
                            <a href="{{ route('rh.agents.edit', $agent->id_agent) }}"
                               class="btn-icon e" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endcan
                            @can('delete', $agent)
                            <button type="button" class="btn-icon d" title="Archiver"
                                    onclick="confirmArchive({{ $agent->id_agent }}, '{{ addslashes($agent->prenom.' '.$agent->nom) }}')">
                                <i class="fas fa-archive"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    <div class="d-flex align-items-center justify-content-between px-4 py-3"
         style="border-top:1px solid var(--theme-border);background:var(--theme-bg-secondary);">
        <span style="font-size:12px;color:var(--theme-text-muted);">
            Affichage de <strong>{{ $agents->firstItem() }}</strong> à <strong>{{ $agents->lastItem() }}</strong>
            sur <strong>{{ $agents->total() }}</strong> agent(s)
        </span>
        {{ $agents->links('pagination::bootstrap-5') }}
    </div>
</div>
@else
{{-- Empty state --}}
<div class="panel text-center py-5">
    <div style="width:80px;height:80px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="fas fa-users" style="font-size:32px;color:#BFDBFE;"></i>
    </div>
    <h6 class="fw-600 mb-1">Aucun agent trouvé</h6>
    <p class="text-muted mb-3" style="font-size:13.5px;">
        @if(array_filter($filters))
            Aucun résultat pour ces critères.
        @else
            Aucun agent enregistré dans le système.
        @endif
    </p>
    @if(array_filter($filters))
        <a href="{{ route('rh.agents.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-xmark"></i> Réinitialiser les filtres
        </a>
    @else
        @can('create', \App\Models\Agent::class)
        <button type="button" class="action-btn action-btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalAddAgent">
            <i class="fas fa-user-plus"></i> Créer le premier agent
        </button>
        @endcan
    @endif
</div>
@endif
</div>{{-- /viewTable --}}

{{-- ═══════════════════════════════════════════════════════════
     VUE CARTES
     ═══════════════════════════════════════════════════════════ --}}
<div id="viewCards" style="display:none;">
@if($agents->count() > 0)
<div class="agents-grid">
    @foreach($agents as $agent)
    @php
        $colors  = ['#0A4D8C','#059669','#7C3AED','#D97706','#0891B2','#DC2626'];
        $color   = $colors[$agent->id_agent % 6];
        $bpClass = match($agent->statut_agent) { 'Actif'=>'bp-actif','En_congé'=>'bp-conge','Suspendu'=>'bp-suspendu',default=>'bp-retraite' };
        $statutLabel = $agent->statut_agent === 'En_congé' ? 'En congé' : $agent->statut_agent;
    @endphp
    <div class="agent-card">
        <div class="agent-card-top">
            <div class="agent-card-badge">
                <span class="badge-pill {{ $bpClass }}" style="font-size:10px;">{{ $statutLabel }}</span>
            </div>
            <div class="agent-card-avatar-lg" style="background:{{ $color }};">
                @if($agent->photo)
                    <img src="{{ asset('storage/'.$agent->photo) }}" alt="">
                @else
                    {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
                @endif
            </div>
            <div style="font-size:14px;font-weight:700;">{{ $agent->prenom }} {{ $agent->nom }}</div>
            <div style="font-size:11.5px;color:var(--theme-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;">
                {{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') ?? 'Sans fonction' }}
            </div>
        </div>
        <div class="agent-card-body">
            <div class="agent-card-meta">
                <i class="fas fa-id-badge" style="color:#0A4D8C;width:14px;"></i>
                <code style="font-size:11px;font-weight:600;">{{ $agent->matricule }}</code>
            </div>
            @if($agent->service)
            <div class="agent-card-meta">
                <i class="fas fa-building-columns" style="color:#0A4D8C;width:14px;"></i>
                {{ $agent->service->nom_service }}
            </div>
            @endif
            @if($agent->date_prise_service)
            <div class="agent-card-meta">
                <i class="fas fa-calendar-check" style="color:#0A4D8C;width:14px;"></i>
                Dep. {{ $agent->date_prise_service->format('d/m/Y') }}
            </div>
            @endif
        </div>
        <div class="agent-card-footer">
            <a href="{{ route('rh.agents.show', $agent->id_agent) }}" class="btn-icon v" title="Voir">
                <i class="fas fa-eye"></i>
            </a>
            @can('update', $agent)
            <a href="{{ route('rh.agents.edit', $agent->id_agent) }}" class="btn-icon e" title="Modifier">
                <i class="fas fa-pen"></i>
            </a>
            @endcan
            @can('delete', $agent)
            <button type="button" class="btn-icon d" title="Archiver"
                    onclick="confirmArchive({{ $agent->id_agent }}, '{{ addslashes($agent->prenom.' '.$agent->nom) }}')">
                <i class="fas fa-archive"></i>
            </button>
            @endcan
        </div>
    </div>
    @endforeach
</div>
<div class="d-flex align-items-center justify-content-between mt-3 px-1">
    <span style="font-size:12px;color:var(--theme-text-muted);">
        {{ $agents->firstItem() }}–{{ $agents->lastItem() }} sur {{ $agents->total() }} agent(s)
    </span>
    {{ $agents->links('pagination::bootstrap-5') }}
</div>
@endif
</div>{{-- /viewCards --}}


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — CRÉER UN AGENT
     ═══════════════════════════════════════════════════════════════════════ --}}
@can('create', \App\Models\Agent::class)
<div class="modal fade" id="modalAddAgent" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- ── EN-TÊTE ── --}}
            <div class="modal-header-sirh">
                <div>
                    <div class="modal-icon" style="background:#EFF6FF;">
                        <i class="fas fa-user-plus" style="color:#0A4D8C;"></i>
                    </div>
                    @if(isset($compteACompleter) && $compteACompleter)
                        <h5>Compléter le dossier agent</h5>
                        <p>Compte : <strong>{{ $compteACompleter->login }}</strong> · {{ $compteACompleter->email }}</p>
                    @else
                        <h5>Enregistrer un nouvel agent</h5>
                        <p>Remplissez les informations du dossier RH. Les champs <span class="text-danger">*</span> sont obligatoires.</p>
                    @endif
                </div>
                <button type="button" class="btn-icon" data-bs-dismiss="modal" style="flex-shrink:0;">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            {{-- ── FORMULAIRE ── --}}
            <form method="POST" action="{{ route('rh.agents.store') }}" enctype="multipart/form-data"
                  x-data="agentModalForm()" id="formAddAgent"
                  @submit="$el.querySelector('[name=_tab]').value = tab">
            @csrf
            <input type="hidden" name="_tab" value="{{ old('_tab', 'identite') }}">
            @if(isset($compteACompleter) && $compteACompleter)
                <input type="hidden" name="user_id" value="{{ $compteACompleter->id }}">
            @endif

            <div class="modal-body modal-body-sirh">

                {{-- Bannière compte à compléter --}}
                @if(isset($compteACompleter) && $compteACompleter)
                <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:12px;">
                    <i class="fas fa-link" style="color:#1D4ED8;font-size:18px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:700;font-size:13px;color:#1E3A8A;">Complétion du dossier pour le compte</div>
                        <div style="font-size:12px;color:#3B82F6;">
                            Login : <strong>{{ $compteACompleter->login }}</strong> &nbsp;·&nbsp;
                            Rôle(s) : {{ $compteACompleter->roles->pluck('name')->join(', ') }}
                        </div>
                        <div style="font-size:11px;color:#6B7280;margin-top:2px;">Ce dossier sera automatiquement lié au compte une fois enregistré.</div>
                    </div>
                </div>
                @endif

                {{-- Résumé des erreurs --}}
                @if($errors->any())
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
                    <div style="font-size:12.5px;font-weight:700;color:#DC2626;margin-bottom:6px;">
                        <i class="fas fa-circle-exclamation me-1"></i>{{ $errors->count() }} erreur(s) à corriger :
                    </div>
                    <ul style="margin:0;padding-left:18px;font-size:12px;color:#991B1B;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- ── NAV TABS ── --}}
                <div class="modal-nav-tabs">
                    <button type="button"
                            :class="{ active: tab==='identite' }"
                            @click="tab='identite'">
                        <i class="fas fa-id-card me-1"></i> Identité
                        @if($errors->hasAny(['matricule','nom','prenom','date_naissance','lieu_naissance','sexe','situation_familiale','nationalite','statut_agent']))
                            <span style="width:7px;height:7px;border-radius:50%;background:#EF4444;display:inline-block;margin-left:4px;"></span>
                        @endif
                    </button>
                    <button type="button"
                            :class="{ active: tab==='coordonnees' }"
                            @click="tab='coordonnees'">
                        <i class="fas fa-lock me-1"></i> Coordonnées
                        <span style="font-size:9px;background:#FEE2E2;color:#991B1B;padding:1px 5px;border-radius:6px;margin-left:4px;">AES-256</span>
                        @if($errors->hasAny(['telephone','email','adresse','numero_assurance','cni','religion']))
                            <span style="width:7px;height:7px;border-radius:50%;background:#EF4444;display:inline-block;margin-left:4px;"></span>
                        @endif
                    </button>
                    <button type="button"
                            :class="{ active: tab==='professionnel' }"
                            @click="tab='professionnel'">
                        <i class="fas fa-briefcase me-1"></i> Professionnel
                        @if($errors->hasAny(['date_prise_service','fontion','grade','categorie_cp','famille_d_emploi','id_service','id_division']))
                            <span style="width:7px;height:7px;border-radius:50%;background:#EF4444;display:inline-block;margin-left:4px;"></span>
                        @endif
                    </button>
                    <button type="button"
                            :class="{ active: tab==='famille' }"
                            @click="tab='famille'">
                        <i class="fas fa-users me-1"></i> Famille
                        <span class="ms-1" style="font-size:10px;color:var(--theme-text-muted);">
                            (<span x-text="conjoints.length + enfants.length"></span>)
                        </span>
                    </button>
                </div>

                {{-- ════════════════════════════════
                     ONGLET 1 — IDENTITÉ
                     ════════════════════════════════ --}}
                <div x-show="tab==='identite'" x-transition>

                    {{-- Photo + Matricule --}}
                    <div class="d-flex align-items-center gap-4 mb-4 p-3" style="background:var(--theme-bg-secondary);border-radius:12px;">
                        <div style="flex-shrink:0;">
                            <label for="photoInputModal" class="photo-upload-zone" style="cursor:pointer;position:relative;">
                                <img id="photoPreviewModal" src="" alt=""
                                     style="display:none;width:90px;height:90px;object-fit:cover;border-radius:50%;position:absolute;inset:0;">
                                <div id="photoPlaceholderModal" style="text-align:center;">
                                    <i class="fas fa-camera" style="font-size:22px;color:#0A4D8C;"></i>
                                    <div style="font-size:10px;color:#6B7280;margin-top:4px;">Photo</div>
                                </div>
                                <div class="photo-upload-overlay"><i class="fas fa-camera"></i></div>
                            </label>
                            <input type="file" name="photo" id="photoInputModal" accept="image/jpeg,image/png"
                                   class="d-none" onchange="previewPhotoModal(this)">
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label-sm">Matricule <span class="text-danger">*</span></label>
                            <input type="text" name="matricule"
                                   class="form-control-sirh @error('matricule') is-invalid @enderror"
                                   value="{{ old('matricule') }}" placeholder="CHNP-00001"
                                   style="text-transform:uppercase;font-family:monospace;font-size:14px;font-weight:600;" required>
                            @error('matricule')
                                <div class="text-danger" style="font-size:12px;margin-top:3px;">{{ $message }}</div>
                            @enderror
                            <div style="font-size:11px;color:var(--theme-text-muted);margin-top:3px;">
                                <i class="fas fa-keyboard me-1"></i>Format : CHNP-XXXXX
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-sm">Nom de famille <span class="text-danger">*</span></label>
                            <input type="text" name="nom"
                                   class="form-control-sirh @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}" placeholder="DIALLO" style="text-transform:uppercase;">
                            @error('nom') <div class="text-danger" style="font-size:12px;margin-top:3px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom"
                                   class="form-control-sirh @error('prenom') is-invalid @enderror"
                                   value="{{ old('prenom') }}" placeholder="Amadou">
                            @error('prenom') <div class="text-danger" style="font-size:12px;margin-top:3px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Date de naissance <span class="text-danger">*</span></label>
                            <input type="date" name="date_naissance"
                                   class="form-control-sirh @error('date_naissance') is-invalid @enderror"
                                   value="{{ old('date_naissance') }}" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                            @error('date_naissance') <div class="text-danger" style="font-size:12px;margin-top:3px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" class="form-control-sirh"
                                   value="{{ old('lieu_naissance') }}" placeholder="Dakar">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Nationalité</label>
                            <input type="text" name="nationalite" class="form-control-sirh"
                                   value="{{ old('nationalite', 'Sénégalaise') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Sexe <span class="text-danger">*</span></label>
                            <select name="sexe" class="form-select-sirh @error('sexe') is-invalid @enderror">
                                <option value="">— Choisir —</option>
                                <option value="M" @selected(old('sexe')==='M')>Masculin</option>
                                <option value="F" @selected(old('sexe')==='F')>Féminin</option>
                            </select>
                            @error('sexe') <div class="text-danger" style="font-size:12px;margin-top:3px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Situation familiale</label>
                            <select name="situation_familiale" class="form-select-sirh">
                                <option value="">— Choisir —</option>
                                @foreach(['Célibataire','Marié','Divorcé','Veuf'] as $sf)
                                <option value="{{ $sf }}" @selected(old('situation_familiale')===$sf)>{{ $sf }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Statut agent</label>
                            <select name="statut_agent" class="form-select-sirh">
                                <option value="Actif" @selected(old('statut_agent','Actif')==='Actif')>Actif</option>
                                <option value="En_congé" @selected(old('statut_agent')==='En_congé')>En congé</option>
                                <option value="Suspendu" @selected(old('statut_agent')==='Suspendu')>Suspendu</option>
                                <option value="Retraité" @selected(old('statut_agent')==='Retraité')>Retraité</option>
                                <option value="Démissionnaire" @selected(old('statut_agent')==='Démissionnaire')>Démissionnaire</option>
                            </select>
                        </div>
                    </div>
                </div>{{-- /tab identité --}}

                {{-- ════════════════════════════════
                     ONGLET 2 — COORDONNÉES (AES-256)
                     ════════════════════════════════ --}}
                <div x-show="tab==='coordonnees'" x-transition>
                    <div class="p-3 mb-4" style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:10px;display:flex;align-items:flex-start;gap:10px;">
                        <i class="fas fa-shield-halved mt-1" style="color:#D97706;font-size:16px;flex-shrink:0;"></i>
                        <div style="font-size:12.5px;color:#92400E;">
                            <strong>Données protégées (Confidentialité CID) :</strong>
                            Téléphone, adresse, CNI et n° assurance sont stockés <strong>chiffrés AES-256</strong> en base de données.
                            Seuls les agents RH et DRH peuvent y accéder.
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 field-sensitive">
                            <label class="form-label-sm">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control-sirh"
                                   value="{{ old('telephone') }}" placeholder="+221 77 000 00 00">
                            <div class="hint-encrypted"><i class="fas fa-lock" style="font-size:9px;"></i> Stocké chiffré (AES-256)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Email professionnel</label>
                            <input type="email" name="email"
                                   class="form-control-sirh @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="a.diallo@chnp.sn">
                            @error('email') <div class="text-danger" style="font-size:12px;margin-top:3px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 field-sensitive">
                            <label class="form-label-sm">Adresse</label>
                            <textarea name="adresse" class="form-control-sirh" rows="2"
                                      placeholder="Quartier, Commune, Ville…">{{ old('adresse') }}</textarea>
                            <div class="hint-encrypted"><i class="fas fa-lock" style="font-size:9px;"></i> Stockée chiffrée (AES-256)</div>
                        </div>
                        <div class="col-md-6 field-sensitive">
                            <label class="form-label-sm">N° CNI</label>
                            <input type="text" name="cni" class="form-control-sirh"
                                   value="{{ old('cni') }}" placeholder="1 XXXXXXX XXXXX XX">
                            <div class="hint-encrypted"><i class="fas fa-lock" style="font-size:9px;"></i> Carte Nationale d'Identité — chiffrée</div>
                        </div>
                        <div class="col-md-6 field-sensitive">
                            <label class="form-label-sm">N° Assurance maladie</label>
                            <input type="text" name="numero_assurance" class="form-control-sirh"
                                   value="{{ old('numero_assurance') }}" placeholder="IPRES-XXXXXXXXX">
                            <div class="hint-encrypted"><i class="fas fa-lock" style="font-size:9px;"></i> Stocké chiffré (AES-256)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Religion</label>
                            <input type="text" name="religion" class="form-control-sirh"
                                   value="{{ old('religion') }}" placeholder="Islam, Christianisme…">
                            <div style="font-size:10.5px;color:#9CA3AF;margin-top:3px;display:flex;align-items:center;gap:4px;">
                                <i class="fas fa-info-circle" style="font-size:9px;color:#D97706;"></i>
                                Donnée personnelle sensible — accès restreint
                            </div>
                        </div>
                    </div>
                </div>{{-- /tab coordonnées --}}

                {{-- ════════════════════════════════
                     ONGLET 3 — PROFESSIONNEL
                     ════════════════════════════════ --}}
                <div x-show="tab==='professionnel'" x-transition>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label-sm">Date de prise de service</label>
                            <input type="date" name="date_prise_service" class="form-control-sirh"
                                   value="{{ old('date_prise_service') }}" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Fonction</label>
                            <input type="text" name="fontion" class="form-control-sirh"
                                   value="{{ old('fontion') }}" placeholder="Infirmier chef de poste">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Grade</label>
                            <input type="text" name="grade" class="form-control-sirh"
                                   value="{{ old('grade') }}" placeholder="IES2, A1, P2…">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Catégorie socio-professionnelle</label>
                            <select name="categorie_cp" class="form-select-sirh">
                                <option value="">— Choisir —</option>
                                @foreach([
                                    'Cadre_Superieur'      => 'Cadre Supérieur',
                                    'Cadre_Moyen'          => 'Cadre Moyen',
                                    'Technicien_Superieur' => 'Technicien Supérieur',
                                    'Technicien'           => 'Technicien',
                                    'Agent_Administratif'  => 'Agent Administratif',
                                    'Agent_de_Service'     => 'Agent de Service',
                                    'Commis_Administration'=> "Commis d'Administration",
                                    'Ouvrier'              => 'Ouvrier',
                                    'Sans_Diplome'         => 'Sans Diplôme',
                                ] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('categorie_cp')===$val)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Famille d'emploi</label>
                            <select name="famille_d_emploi" class="form-select-sirh">
                                <option value="">— Choisir —</option>
                                <option value="Corps_Médical"       @selected(old('famille_d_emploi')==='Corps_Médical')>Corps Médical</option>
                                <option value="Corps_Paramédical"   @selected(old('famille_d_emploi')==='Corps_Paramédical')>Corps Paramédical</option>
                                <option value="Corps_Administratif" @selected(old('famille_d_emploi')==='Corps_Administratif')>Corps Administratif</option>
                                <option value="Corps_Technique"     @selected(old('famille_d_emploi')==='Corps_Technique')>Corps Technique</option>
                                <option value="Corps_de_Soutien"    @selected(old('famille_d_emploi')==='Corps_de_Soutien')>Corps de Soutien</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Service</label>
                            <select name="id_service" class="form-select-sirh">
                                <option value="">— Aucun —</option>
                                @foreach($services as $s)
                                <option value="{{ $s->id_service }}" @selected(old('id_service')==$s->id_service)>{{ $s->nom_service }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Division</label>
                            <select name="id_division" class="form-select-sirh">
                                <option value="">— Aucune —</option>
                                @foreach($divisions as $d)
                                <option value="{{ $d->id_division }}" @selected(old('id_division')==$d->id_division)>{{ $d->nom_division }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Encart Triade CID --}}
                        <div class="col-12">
                            <div style="background:linear-gradient(135deg,#EFF6FF,#E0F2FE);border:1px solid #BFDBFE;border-radius:10px;padding:14px;">
                                <div style="font-size:12px;font-weight:700;color:#1E40AF;margin-bottom:8px;">
                                    <i class="fas fa-shield-alt me-1"></i> Triade CID — Garanties appliquées
                                </div>
                                <div class="row g-0" style="font-size:11.5px;color:#374151;">
                                    <div class="col-md-4 d-flex align-items-center gap-1 mb-1">
                                        <i class="fas fa-lock" style="color:#059669;width:14px;"></i>AES-256 données critiques
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center gap-1 mb-1">
                                        <i class="fas fa-database" style="color:#059669;width:14px;"></i>Transaction DB atomique
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center gap-1 mb-1">
                                        <i class="fas fa-history" style="color:#059669;width:14px;"></i>Audit trail automatique
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- /tab professionnel --}}

                {{-- ════════════════════════════════
                     ONGLET 4 — FAMILLE
                     ════════════════════════════════ --}}
                <div x-show="tab==='famille'" x-transition>

                    {{-- Conjoint --}}
                    <div class="mb-4">
                        <div class="form-section-label">
                            <i class="fas fa-heart" style="color:#7C3AED;"></i> Conjoint(e)
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:11px;color:var(--theme-text-muted);margin-left:6px;">max. 1</span>
                            <button type="button" class="ms-auto action-btn action-btn-outline"
                                    style="font-size:11px;padding:5px 11px;"
                                    @click="addConjoint"
                                    x-show="conjoints.length === 0">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <template x-for="(c, i) in conjoints" :key="i">
                            <div class="famille-item">
                                <button type="button" class="btn-remove" @click="removeConjoint(i)" title="Supprimer">
                                    <i class="fas fa-xmark"></i>
                                </button>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label-sm" style="font-size:11px;">Nom</label>
                                        <input type="text" :name="`conjoints[${i}][nom_conj]`" x-model="c.nom_conj"
                                               class="form-control-sirh" placeholder="FALL" style="text-transform:uppercase;">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm" style="font-size:11px;">Prénom</label>
                                        <input type="text" :name="`conjoints[${i}][prenom_conj]`" x-model="c.prenom_conj"
                                               class="form-control-sirh" placeholder="Fatou">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm" style="font-size:11px;">Date naissance</label>
                                        <input type="date" :name="`conjoints[${i}][date_naissance_conj]`" x-model="c.date_naissance_conj"
                                               class="form-control-sirh">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm" style="font-size:11px;">Lien</label>
                                        <select :name="`conjoints[${i}][type_lien]`" x-model="c.type_lien" class="form-select-sirh">
                                            <option value="Époux">Époux</option>
                                            <option value="Épouse">Épouse</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="conjoints.length === 0" style="font-size:12.5px;color:var(--theme-text-muted);">
                            Aucun conjoint enregistré.
                        </div>
                    </div>

                    {{-- Enfants --}}
                    <div>
                        <div class="form-section-label d-flex align-items-center">
                            <i class="fas fa-child" style="color:#059669;"></i> Enfants
                            <span class="ms-2" style="font-weight:400;text-transform:none;letter-spacing:0;font-size:11px;color:var(--theme-text-muted);">
                                (<span x-text="enfants.length"></span> enregistré(s))
                            </span>
                            <button type="button" class="ms-auto action-btn action-btn-outline"
                                    style="font-size:11px;padding:5px 11px;" @click="addEnfant">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <template x-for="(e, i) in enfants" :key="i">
                            <div class="famille-item">
                                <button type="button" class="btn-remove" @click="removeEnfant(i)">
                                    <i class="fas fa-xmark"></i>
                                </button>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label-sm" style="font-size:11px;">Prénom complet</label>
                                        <input type="text" :name="`enfants[${i}][prenom_complet]`" x-model="e.prenom_complet"
                                               class="form-control-sirh" placeholder="Moussa Ibrahima">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm" style="font-size:11px;">Date de naissance</label>
                                        <input type="date" :name="`enfants[${i}][date_naissance_enfant]`" x-model="e.date_naissance_enfant"
                                               class="form-control-sirh">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm" style="font-size:11px;">Lien</label>
                                        <select :name="`enfants[${i}][lien_filiation]`" x-model="e.lien_filiation" class="form-select-sirh">
                                            <option value="Fils">Fils</option>
                                            <option value="Fille">Fille</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="enfants.length === 0" style="font-size:12.5px;color:var(--theme-text-muted);">
                            Aucun enfant enregistré.
                        </div>
                    </div>

                </div>{{-- /tab famille --}}

            </div>{{-- /modal-body-sirh --}}

            {{-- ── PIED DU MODAL ── --}}
            <div class="modal-footer-sirh d-flex">
                <span style="font-size:12px;color:var(--theme-text-muted);flex:1;align-self:center;">
                    <i class="fas fa-shield-alt me-1" style="color:#059669;"></i>
                    Données chiffrées · Audit enregistré · Transaction atomique
                </span>
                <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">
                    <i class="fas fa-xmark"></i> Annuler
                </button>
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save"></i> Enregistrer l'agent
                </button>
            </div>

            </form>
        </div>
    </div>
</div>
@endcan

{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — CONFIRMATION ARCHIVE
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalArchive" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-header-sirh">
                <div>
                    <div class="modal-icon" style="background:#FEF2F2;">
                        <i class="fas fa-triangle-exclamation" style="color:#DC2626;"></i>
                    </div>
                    <h5>Confirmer l'archivage</h5>
                    <p>Archiver l'agent <strong id="agentNomArchive"></strong> ? Le compte sera désactivé. Cette action est réversible.</p>
                </div>
                <button type="button" class="btn-icon" data-bs-dismiss="modal"><i class="fas fa-xmark"></i></button>
            </div>
            <div class="modal-footer-sirh d-flex">
                <button type="button" class="action-btn action-btn-outline flex-fill justify-content-center" data-bs-dismiss="modal">
                    Annuler
                </button>
                <form id="formArchive" method="POST" class="flex-fill">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn w-100 justify-content-center"
                            style="background:#DC2626;color:#fff;border-radius:8px;">
                        <i class="fas fa-archive"></i> Archiver
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     TOASTS
     ═══════════════════════════════════════════════════════════════════════ --}}
@if(session('success') || session('error') || session('warning'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1200;">
    @if(session('success'))
    <div id="toastSuccess" class="toast align-items-center text-white border-0" role="alert"
         style="background:linear-gradient(135deg,#059669,#10B981);border-radius:12px;min-width:280px;box-shadow:0 8px 24px rgba(5,150,105,.35);">
        <div class="d-flex">
            <div class="toast-body" style="font-size:13px;font-weight:500;">
                <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div id="toastError" class="toast align-items-center text-white border-0" role="alert"
         style="background:linear-gradient(135deg,#DC2626,#EF4444);border-radius:12px;min-width:280px;box-shadow:0 8px 24px rgba(220,38,38,.35);">
        <div class="d-flex">
            <div class="toast-body" style="font-size:13px;font-weight:500;">
                <i class="fas fa-circle-xmark me-2"></i>{{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>
@endif

@push('scripts')
<script>
/* ── Toggle vue tableau / cartes ── */
const VIEW_KEY = 'sirh_agents_view';
function switchView(mode) {
    document.getElementById('viewTable').style.display = mode==='table' ? '' : 'none';
    document.getElementById('viewCards').style.display = mode==='cards' ? '' : 'none';
    document.getElementById('btnTableView').classList.toggle('active', mode==='table');
    document.getElementById('btnCardView').classList.toggle('active', mode==='cards');
    localStorage.setItem(VIEW_KEY, mode);
}
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(VIEW_KEY) || 'table';
    if (saved === 'cards') switchView('cards');
    /* Toasts */
    document.querySelectorAll('.toast').forEach(el => new bootstrap.Toast(el, { delay: 5500 }).show());
    /* Rouvrir modal si erreur de validation */
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('modalAddAgent')).show();
    @endif
});

/* ── Archive ── */
function confirmArchive(agentId, agentNom) {
    document.getElementById('agentNomArchive').textContent = agentNom;
    document.getElementById('formArchive').action = `/rh/agents/${agentId}`;
    new bootstrap.Modal(document.getElementById('modalArchive')).show();
}

/* ── Alpine.js : formulaire modal ── */
function agentModalForm() {
    return {
        tab: '{{ $errors->any() ? old('_tab','identite') : 'identite' }}',
        conjoints: @json(old('conjoints', [])),
        enfants:   @json(old('enfants', [])),
        addConjoint() {
            if (this.conjoints.length < 1)
                this.conjoints.push({ nom_conj:'', prenom_conj:'', date_naissance_conj:'', type_lien:'Épouse' });
        },
        removeConjoint(i) { this.conjoints.splice(i, 1); },
        addEnfant()       { this.enfants.push({ prenom_complet:'', date_naissance_enfant:'', lien_filiation:'Fils' }); },
        removeEnfant(i)   { this.enfants.splice(i, 1); },
    };
}

/* ── Aperçu photo ── */
function previewPhotoModal(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const prev = document.getElementById('photoPreviewModal');
            const ph   = document.getElementById('photoPlaceholderModal');
            prev.src = e.target.result;
            prev.style.display = 'block';
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@if($errors->any())
<script>
// Auto-ouvrir le modal si la validation a échoué
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('modalAddAgent'));
    modal.show();
});
</script>
@endif
@endpush

@endsection
