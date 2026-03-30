@extends('layouts.master')
@section('title', 'Gestion des Absences')
@section('page-title', 'Absences du Personnel')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Absences</li>
@endsection

@push('styles')
<style>
/* ── KPI CARDS ──────────────────────────────────────────────── */
.kpi-card {
    border-radius:12px;padding:20px 24px;
    transition:box-shadow 200ms,transform 200ms;
    position:relative;overflow:hidden;
}
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label { font-size:13px;margin-top:2px;font-weight:500; }
.kpi-card .kpi-trend { font-size:12px;font-weight:600;margin-top:6px; }
.kpi-card .kpi-trend.up   { color:#10B981; }
.kpi-card .kpi-trend.down { color:#EF4444; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.red::before    { background:#DC2626; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.blue::before   { background:#1D4ED8; }

/* ── ACTION BUTTONS ─────────────────────────────────────────── */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30);transform:translateY(-1px); }
.action-btn-danger { background:#DC2626;color:#fff; }
.action-btn-danger:hover { background:#B91C1C;color:#fff;box-shadow:0 4px 12px rgba(220,38,38,.25);transform:translateY(-1px); }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB;border-color:#D1D5DB;color:#111827; }

/* ── SECTION TITLE ──────────────────────────────────────────── */
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;padding-bottom:6px;color:#6B7280; }

/* ── TABLE ROWS ─────────────────────────────────────────────── */
.absence-row { transition:background 150ms; }
.absence-row:hover { background:#F9FAFB !important; }

/* ── MODAL LABEL ────────────────────────────────────────────── */
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:#6B7280; }

/* ── TOAST ──────────────────────────────────────────────────── */
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
@keyframes toastOut { from { opacity:1; } to { opacity:0;transform:translateX(40px); } }

/* ── ICON BUTTONS ───────────────────────────────────────────── */
.btn-icon { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;transition:all 150ms;font-size:12px; }
.btn-icon-view   { background:#EFF6FF;color:#1D4ED8; }
.btn-icon-view:hover   { background:#DBEAFE; }
.btn-icon-edit   { background:#FFFBEB;color:#D97706; }
.btn-icon-edit:hover   { background:#FEF3C7; }
.btn-icon-check  { background:#ECFDF5;color:#059669; }
.btn-icon-check:hover  { background:#D1FAE5; }
.btn-icon-delete { background:#FEF2F2;color:#DC2626; }
.btn-icon-delete:hover { background:#FEE2E2; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ─── Toast Container ──────────────────────────────────── --}}
    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    {{-- ─── En-tête ───────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Gestion des Absences</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Suivi et analyse des absences du personnel hospitalier</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('rh.absences.export', request()->query()) }}"
               class="action-btn action-btn-outline">
                <i class="fas fa-file-excel"></i> Export Excel
                @if(request()->anyFilled(['mois','annee','type','service','agent','justifie']))
                <span style="font-size:10px;background:#D1FAE5;color:#065F46;padding:1px 5px;border-radius:10px;font-weight:700;">filtré</span>
                @endif
            </a>
            <button type="button" class="action-btn action-btn-danger"
                    data-bs-toggle="modal" data-bs-target="#modal-create-absence">
                <i class="fas fa-plus"></i>Enregistrer une absence
            </button>
        </div>
    </div>

    {{-- ─── KPI Cards ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-user-minus" style="color:#DC2626;"></i></div>
                    <span style="display:inline-flex;align-items:center;background:#FEE2E2;color:#991B1B;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">Ce mois</span>
                </div>
                <div class="kpi-value" style="color:#DC2626;">{{ $kpis['total_mois'] }}</div>
                <div class="kpi-label text-muted">Absences enregistrées</div>
                <div class="kpi-trend {{ $kpis['total_mois'] > 0 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $kpis['total_mois'] > 0 ? 'arrow-up' : 'check' }} me-1"></i>
                    {{ $kpis['total_mois'] > 0 ? 'À surveiller' : 'Aucune absence' }}
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-exclamation-triangle" style="color:#D97706;"></i></div>
                    <span style="display:inline-flex;align-items:center;background:#FEF3C7;color:#92400E;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">Attention</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $kpis['injustifiees_mois'] }}</div>
                <div class="kpi-label text-muted">Non justifiées</div>
                <div class="kpi-trend {{ $kpis['injustifiees_mois'] > 0 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $kpis['injustifiees_mois'] > 0 ? 'exclamation-triangle' : 'check' }} me-1"></i>
                    {{ $kpis['injustifiees_mois'] > 0 ? 'Action requise' : 'Aucune' }}
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-hospital" style="color:#059669;"></i></div>
                    <span style="display:inline-flex;align-items:center;background:#D1FAE5;color:#065F46;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">Maladie</span>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $kpis['maladie_mois'] }}</div>
                <div class="kpi-label text-muted">Congés maladie</div>
                <div class="kpi-trend up"><i class="fas fa-notes-medical me-1"></i>Certificat requis</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-chart-line" style="color:#1D4ED8;"></i></div>
                    <span style="display:inline-flex;align-items:center;background:#DBEAFE;color:#1E40AF;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">Taux</span>
                </div>
                <div class="kpi-value" style="color:#1D4ED8;">{{ $kpis['taux_absenteisme'] }}%</div>
                <div class="kpi-label text-muted">Taux d'absentéisme</div>
                <div class="kpi-trend {{ $kpis['taux_absenteisme'] > 5 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $kpis['taux_absenteisme'] > 5 ? 'arrow-up' : 'check' }} me-1"></i>
                    {{ $kpis['taux_absenteisme'] > 5 ? 'Élevé' : 'Normal' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Panneaux analytiques ───────────────────────────────── --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-2">
                    <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                        Répartition par type
                        <small class="fw-normal text-muted ms-1">— {{ now()->isoFormat('MMMM YYYY') }}</small>
                    </h6>
                </div>
                <div class="card-body px-4 pb-4">
                    @php
                        $typeConf = [
                            'Maladie'         => ['#FEF3C7','#D97706'],
                            'Personnelle'     => ['#DBEAFE','#1D4ED8'],
                            'Professionnelle' => ['#EDE9FE','#7C3AED'],
                            'Injustifiée'     => ['#FEE2E2','#DC2626'],
                        ];
                        $totalType = $parType->sum();
                    @endphp
                    @foreach($typeConf as $type => [$bg, $color])
                        @php $nb = $parType->get($type, 0); $pct = $totalType > 0 ? round($nb/$totalType*100) : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--theme-text);">{{ $type }}</span>
                                <span style="font-size:12px;background:{{ $bg }};color:{{ $color }};padding:1px 10px;border-radius:20px;font-weight:700;">{{ $nb }}</span>
                            </div>
                            <div style="height:6px;background:#F3F4F6;border-radius:99px;overflow:hidden;">
                                <div style="height:6px;width:{{ $pct }}%;background:{{ $color }};border-radius:99px;transition:width .4s;"></div>
                            </div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">{{ $pct }}% du total</div>
                        </div>
                    @endforeach
                    @if($totalType === 0)
                        <p class="text-muted small text-center mt-4 mb-0">Aucune absence ce mois</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-2">
                    <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                        Services les plus absents
                        <small class="fw-normal text-muted ms-1">— top 5</small>
                    </h6>
                </div>
                <div class="card-body px-4 pb-4">
                    @php $maxSvc = $parService->max() ?: 1; $colors = ['#EF4444','#F97316','#EAB308','#22C55E','#3B82F6']; $i=0; @endphp
                    @forelse($parService as $svc => $nb)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size:13px;font-weight:500;color:var(--theme-text);max-width:160px;" class="text-truncate">{{ $svc }}</span>
                                <span style="font-size:11px;background:#EFF6FF;color:#1D4ED8;padding:1px 10px;border-radius:20px;font-weight:700;">{{ $nb }}</span>
                            </div>
                            <div style="height:6px;background:#F3F4F6;border-radius:99px;overflow:hidden;">
                                <div style="height:6px;width:{{ round($nb/$maxSvc*100) }}%;background:{{ $colors[$i] ?? '#6B7280' }};border-radius:99px;"></div>
                            </div>
                        </div>
                        @php $i++; @endphp
                    @empty
                        <p class="text-muted small text-center mt-4 mb-0">Aucune donnée disponible</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-2">
                    <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">Accès rapide</h6>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column gap-2">
                    <a href="{{ route('rh.absences.index', ['justifie'=>'0']) }}"
                       class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#FEF2F2;transition:all 150ms;" onmouseover="this.style.boxShadow='0 3px 10px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                        <i class="fas fa-times-circle" style="color:#DC2626;font-size:20px;flex-shrink:0;"></i>
                        <div><div style="font-weight:600;color:#111827;font-size:13px;">Non justifiées</div><div style="font-size:11px;color:#6B7280;">Nécessitent une action</div></div>
                        <i class="fas fa-chevron-right ms-auto" style="color:#D1D5DB;font-size:11px;"></i>
                    </a>
                    <a href="{{ route('rh.absences.index', ['type'=>'Maladie']) }}"
                       class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#FFFBEB;transition:all 150ms;" onmouseover="this.style.boxShadow='0 3px 10px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                        <i class="fas fa-hospital" style="color:#D97706;font-size:20px;flex-shrink:0;"></i>
                        <div><div style="font-weight:600;color:#111827;font-size:13px;">Congés maladie</div><div style="font-size:11px;color:#6B7280;">Certificat médical requis</div></div>
                        <i class="fas fa-chevron-right ms-auto" style="color:#D1D5DB;font-size:11px;"></i>
                    </a>
                    <a href="{{ route('rh.absences.index', ['type'=>'Injustifiée']) }}"
                       class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none" style="background:#FFF7ED;transition:all 150ms;" onmouseover="this.style.boxShadow='0 3px 10px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                        <i class="fas fa-ban" style="color:#EA580C;font-size:20px;flex-shrink:0;"></i>
                        <div><div style="font-weight:600;color:#111827;font-size:13px;">Injustifiées</div><div style="font-size:11px;color:#6B7280;">Impact dossier disciplinaire</div></div>
                        <i class="fas fa-chevron-right ms-auto" style="color:#D1D5DB;font-size:11px;"></i>
                    </a>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-create-absence"
                       class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none border-0 text-start w-100" style="background:#EFF6FF;transition:all 150ms;cursor:pointer;" onmouseover="this.style.boxShadow='0 3px 10px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                        <i class="fas fa-plus-circle" style="color:#1565C0;font-size:20px;flex-shrink:0;"></i>
                        <div><div style="font-weight:600;color:#111827;font-size:13px;">Enregistrer</div><div style="font-size:11px;color:#6B7280;">Nouvelle absence agent</div></div>
                        <i class="fas fa-chevron-right ms-auto" style="color:#D1D5DB;font-size:11px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Filtres ────────────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.absences.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="service" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ request('service') == $svc->id_service ? 'selected' : '' }}>{{ $svc->nom_service }}</option>
                    @endforeach
                </select>
                <select name="agent" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les agents</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id_agent }}" {{ request('agent') == $ag->id_agent ? 'selected' : '' }}>{{ $ag->prenom }} {{ $ag->nom }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les types</option>
                    @foreach(['Maladie','Personnelle','Professionnelle','Injustifiée'] as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <select name="justifie" class="form-select" style="width:auto;min-width:130px;">
                    <option value="">Justification</option>
                    <option value="1" {{ request('justifie')==='1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('justifie')==='0' ? 'selected' : '' }}>Non</option>
                </select>
                <select name="mois" class="form-select" style="width:auto;min-width:130px;">
                    <option value="">Tous les mois</option>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ request('mois')==$m ? 'selected' : '' }}>{{ now()->month($m)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
                <select name="annee" class="form-select" style="width:auto;min-width:110px;">
                    <option value="">Toutes les années</option>
                    @for($y=now()->year;$y>=now()->year-3;$y--)
                        <option value="{{ $y }}" {{ request('annee')==$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['service', 'agent', 'type', 'justifie', 'mois', 'annee']))
                    <a href="{{ route('rh.absences.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ─── Table principale ───────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-list me-2" style="color:#1565C0;"></i>Liste des absences
                <span class="text-muted ms-2" style="font-size:12px;font-weight:400;">({{ $absences->total() }} résultats)</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Service</th>
                            <th class="py-3 border-0">Date</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Justifiée</th>
                            <th class="py-3 border-0 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absence)
                            @php
                                $ag  = $absence->demande->agent ?? null;
                                $svc = $ag?->service;
                                $initiales = strtoupper(substr($ag?->prenom ?? 'A',0,1).substr($ag?->nom ?? '',0,1));
                                $typeColors = [
                                    'Maladie'         => 'background:#FEF3C7;color:#92400E',
                                    'Personnelle'     => 'background:#DBEAFE;color:#1E40AF',
                                    'Professionnelle' => 'background:#EDE9FE;color:#5B21B6',
                                    'Injustifiée'     => 'background:#FEE2E2;color:#991B1B',
                                ];
                            @endphp
                            <tr class="absence-row" style="border-bottom:1px solid #F3F4F6;">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
                                            {{ $initiales }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:var(--theme-text);">{{ $ag?->nom_complet ?? '—' }}</div>
                                            <div style="font-size:11px;color:#9CA3AF;">{{ $ag?->matricule }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 border-0 text-muted" style="font-size:12px;">{{ $svc?->nom_service ?? '—' }}</td>
                                <td class="py-3 border-0" style="font-weight:500;color:var(--theme-text);">{{ $absence->date_absence->format('d/m/Y') }}</td>
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;{{ $typeColors[$absence->type_absence] ?? 'background:#F3F4F6;color:#374151' }};padding:3px 10px;border-radius:20px;font-weight:700;">
                                        {{ $absence->type_absence }}
                                    </span>
                                </td>
                                <td class="py-3 border-0">
                                    @if($absence->justifie)
                                        <span style="font-size:11px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-check me-1"></i>Oui</span>
                                    @else
                                        <span style="font-size:11px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-times me-1"></i>Non</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0 text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('rh.absences.show', $absence->id_absence) }}"
                                           class="btn-icon btn-icon-view" title="Voir le détail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-icon-edit" title="Modifier"
                                                onclick="openEditModal(
                                                    {{ $absence->id_absence }},
                                                    '{{ $absence->date_absence->format('Y-m-d') }}',
                                                    '{{ $absence->type_absence }}',
                                                    {{ $absence->justifie ? 1 : 0 }},
                                                    '{{ addslashes($absence->commentaire ?? '') }}',
                                                    '{{ $ag?->nom_complet ?? '' }}'
                                                )">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$absence->justifie)
                                            <button type="button" class="btn-icon btn-icon-check" title="Valider le justificatif"
                                                    onclick="openValiderModal({{ $absence->id_absence }}, '{{ $ag?->nom_complet ?? '' }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn-icon btn-icon-delete" title="Supprimer"
                                                onclick="openDeleteModal({{ $absence->id_absence }}, '{{ $absence->date_absence->format('d/m/Y') }}', '{{ $ag?->nom_complet ?? '' }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted border-0">
                                    <i class="fas fa-calendar-check fa-2x mb-3 d-block" style="color:#D1D5DB;"></i>
                                    <p class="mb-1 fw-500">Aucune absence trouvée</p>
                                    <p class="small mb-3">Modifiez les filtres ou enregistrez une nouvelle absence</p>
                                    <button type="button" class="action-btn action-btn-danger" style="margin:0 auto;font-size:13px;padding:8px 16px;"
                                            data-bs-toggle="modal" data-bs-target="#modal-create-absence">
                                        <i class="fas fa-plus"></i>Enregistrer une absence
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($absences->hasPages())
            <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">
                {{ $absences->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL : CRÉER UNE ABSENCE
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-create-absence" tabindex="-1" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-minus" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalCreateLabel">Enregistrer une absence</h5>
                        <p class="text-muted small mb-0">Saisie directe par le service RH</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.absences.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-5">
                            <label class="modal-label">Filtrer par service</label>
                            <select id="filterServiceCreate" class="form-select form-select-sm" style="border-radius:7px;">
                                <option value="">— Tous les services —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id_service }}">{{ $svc->nom_service }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-7">
                            <label class="modal-label">Agent concerné <span class="text-danger">*</span></label>
                            <select name="id_agent" id="selectAgentCreate" class="form-select form-select-sm" style="border-radius:7px;" required>
                                <option value="">— Sélectionner un agent —</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id_agent }}" data-service="{{ $agent->id_service }}">
                                        {{ $agent->nom_complet }} ({{ $agent->matricule }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-5">
                            <label class="modal-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_absence"
                                   value="{{ today()->format('Y-m-d') }}"
                                   max="{{ today()->format('Y-m-d') }}"
                                   class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                        <div class="col-12 col-md-7">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select name="type_absence" class="form-select form-select-sm" style="border-radius:7px;" required>
                                <option value="">— Choisir —</option>
                                <option value="Maladie">Maladie (certificat médical requis)</option>
                                <option value="Personnelle">Personnelle</option>
                                <option value="Professionnelle">Professionnelle (formation, mission…)</option>
                                <option value="Injustifiée">Injustifiée</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check p-3" style="background:#F8FAFC;border-radius:8px;border:1px solid #E5E7EB;">
                            <input class="form-check-input" type="checkbox" name="justifie" id="justifieCreate" value="1">
                            <label class="form-check-label small fw-600" for="justifieCreate">
                                Absence justifiée
                                <span class="text-muted fw-normal ms-1">— justificatif fourni par l'agent</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-label">Observations (optionnel)</label>
                        <textarea name="commentaire" rows="2" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"
                                  placeholder="Contexte, circonstances…"></textarea>
                    </div>
                    <div class="p-3 rounded-3" style="background:#EFF6FF;border-left:3px solid #1565C0;">
                        <div class="d-flex gap-2 align-items-start" style="font-size:12px;">
                            <i class="fas fa-shield-alt mt-1" style="color:#1565C0;flex-shrink:0;"></i>
                            <span style="color:#1E40AF;"><strong>Traçabilité CID :</strong> Cette absence sera enregistrée avec votre identité et l'horodatage dans le journal d'audit immuable.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-danger">
                        <i class="fas fa-save"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL : MODIFIER UNE ABSENCE
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-edit-absence" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FFF7ED;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FED7AA;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-edit" style="color:#D97706;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Modifier l'absence</h5>
                        <p class="text-muted small mb-0" id="edit-agent-name">—</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-edit-absence" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="edit-date" name="date_absence"
                                   max="{{ today()->format('Y-m-d') }}"
                                   class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select id="edit-type" name="type_absence" class="form-select form-select-sm" style="border-radius:7px;" required>
                                <option value="Maladie">Maladie</option>
                                <option value="Personnelle">Personnelle</option>
                                <option value="Professionnelle">Professionnelle</option>
                                <option value="Injustifiée">Injustifiée</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check p-3" style="background:#F8FAFC;border-radius:8px;border:1px solid #E5E7EB;">
                            <input class="form-check-input" type="checkbox" name="justifie" id="edit-justifie" value="1">
                            <label class="form-check-label small fw-600" for="edit-justifie">Absence justifiée</label>
                        </div>
                    </div>
                    <div>
                        <label class="modal-label">Observations</label>
                        <textarea id="edit-commentaire" name="commentaire" rows="2" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#D97706;color:#fff;">
                        <i class="fas fa-save"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL : VALIDER JUSTIFICATIF
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-valider-just" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#ECFDF5;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#D1FAE5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-check-circle" style="color:#059669;font-size:18px;"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-0 small">Valider le justificatif</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p style="font-size:13.5px;color:#374151;" class="mb-0">
                    Confirmer la validation du justificatif de <strong id="valider-agent-name"></strong> ?
                </p>
            </div>
            <form id="form-valider-just" method="POST">
                @csrf @method('PATCH')
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#059669;color:#fff;padding:8px 16px;font-size:13px;">
                        <i class="fas fa-check"></i>Valider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL : SUPPRIMER ABSENCE
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-delete-absence" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-trash-alt" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-0 small">Supprimer l'absence</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p style="font-size:13.5px;color:#374151;" class="mb-1">
                    Supprimer l'absence du <strong id="delete-date-absence"></strong> de <strong id="delete-agent-name"></strong> ?
                </p>
                <p class="text-muted small mb-0">Cette action est <strong>irréversible</strong>.</p>
            </div>
            <form id="form-delete-absence" method="POST">
                @csrf @method('DELETE')
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-danger" style="padding:8px 16px;font-size:13px;">
                        <i class="fas fa-trash"></i>Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ─── TOAST SYSTEM ──────────────────────────────────────────── */
function showToast(message, type) {
    type = type || 'success';
    var cfg = {
        success: { bg:'#ECFDF5', color:'#065F46', icon:'check-circle', border:'#059669' },
        error:   { bg:'#FEF2F2', color:'#991B1B', icon:'times-circle',  border:'#DC2626' },
        warning: { bg:'#FFFBEB', color:'#92400E', icon:'exclamation-triangle', border:'#D97706' },
        info:    { bg:'#EFF6FF', color:'#1E40AF', icon:'info-circle',   border:'#1565C0' }
    };
    var c = cfg[type] || cfg.success;
    var t = document.createElement('div');
    t.style.cssText = 'background:'+c.bg+';color:'+c.color+';padding:14px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:500;min-width:280px;max-width:380px;animation:toastIn .3s ease;border-left:4px solid '+c.border+';pointer-events:all;';
    t.innerHTML = '<i class="fas fa-'+c.icon+'" style="flex-shrink:0;"></i><span>'+message+'</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;margin-left:auto;opacity:.7;padding:0;"><i class="fas fa-times"></i></button>';
    document.getElementById('toast-container').appendChild(t);
    setTimeout(function(){ t.style.animation='toastOut .3s ease forwards'; setTimeout(function(){ t.remove(); }, 300); }, 4000);
}

/* ─── AUTO TOAST depuis session flash ───────────────────────── */
@if(session('success')) showToast(@json(session('success')), 'success'); @endif
@if(session('error'))   showToast(@json(session('error')),   'error');   @endif
@if(session('warning')) showToast(@json(session('warning')), 'warning'); @endif

/* ─── FILTRE SERVICE (modal create) ─────────────────────────── */
document.getElementById('filterServiceCreate').addEventListener('change', function(){
    var sid = this.value;
    document.querySelectorAll('#selectAgentCreate option').forEach(function(o){
        if (!o.value) return;
        o.style.display = (!sid || o.dataset.service == sid) ? '' : 'none';
    });
    document.getElementById('selectAgentCreate').value = '';
});

/* ─── MODAL EDIT ─────────────────────────────────────────────── */
function openEditModal(id, date, type, justifie, commentaire, agentName) {
    document.getElementById('form-edit-absence').action = '/rh/absences/' + id;
    document.getElementById('edit-date').value = date;
    document.getElementById('edit-type').value = type;
    document.getElementById('edit-justifie').checked = justifie == 1;
    document.getElementById('edit-commentaire').value = commentaire;
    document.getElementById('edit-agent-name').textContent = agentName;
    new bootstrap.Modal(document.getElementById('modal-edit-absence')).show();
}

/* ─── MODAL VALIDER JUSTIFICATIF ─────────────────────────────── */
function openValiderModal(id, agentName) {
    document.getElementById('form-valider-just').action = '/rh/absences/' + id + '/valider-justificatif';
    document.getElementById('valider-agent-name').textContent = agentName;
    new bootstrap.Modal(document.getElementById('modal-valider-just')).show();
}

/* ─── MODAL DELETE ───────────────────────────────────────────── */
function openDeleteModal(id, date, agentName) {
    document.getElementById('form-delete-absence').action = '/rh/absences/' + id;
    document.getElementById('delete-date-absence').textContent = date;
    document.getElementById('delete-agent-name').textContent = agentName;
    new bootstrap.Modal(document.getElementById('modal-delete-absence')).show();
}
</script>
@endpush

<style>.fw-500{font-weight:500!important;}.fw-600{font-weight:600!important;}</style>
@endsection
