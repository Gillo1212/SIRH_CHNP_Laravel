@extends('layouts.master')

@section('title', 'Soldes de Congés')
@section('page-title', 'Soldes de Congés')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>Soldes & Reliquats</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden;border:1px solid var(--theme-border); }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:26px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }
.solde-bar { height:5px;border-radius:3px;background:var(--theme-bg-secondary);overflow:hidden; }
.solde-bar-fill { height:100%;border-radius:3px;transition:width .5s ease; }
.agent-row td { padding:12px 14px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.agent-row:hover td { background:var(--sirh-primary-hover); }
thead th { padding:10px 14px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.action-btn-success { background:#10B981;color:#fff; }
.action-btn-success:hover { background:#059669;color:#fff; }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
.modal-input { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text); }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Soldes & Reliquats de congés</h4>
            <p class="text-muted small mb-0">Consultez, filtrez et gérez les soldes de congés de tous les agents - Année {{ $annee }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('rh.conges.soldes.export', array_filter(['annee'=>$annee,'search'=>$search,'service'=>$serviceId,'type_conge'=>$typeCongeId])) }}"
                class="action-btn action-btn-success">
                <i class="fas fa-file-excel"></i> Exporter Excel
            </a>
            <button type="button" class="action-btn action-btn-primary"
                data-bs-toggle="modal" data-bs-target="#initSoldesModal">
                <i class="fas fa-plus"></i> Initialiser soldes
            </button>
        </div>
    </div>

    {{-- Navigation --}}
    @include('rh.conges._nav', [
        'active'       => 'soldes',
        'pendingCount' => \App\Models\Demande::where('type_demande','Conge')->where('statut_demande','Validé')->count(),
        'enCoursCount' => \App\Models\Agent::where('statut_agent','En_Conge')->count(),
    ])

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#0A4D8C;">{{ $kpis['agents'] }}</div>
                        <div class="kpi-label">Agents concernés</div>
                    </div>
                    <div class="kpi-icon" style="background:#EFF6FF;color:#0A4D8C;"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#3B82F6;">{{ $kpis['total_initial'] }}</div>
                        <div class="kpi-label">Jours alloués (total)</div>
                    </div>
                    <div class="kpi-icon" style="background:#DBEAFE;color:#1D4ED8;"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#F59E0B;">{{ $kpis['total_pris'] }}</div>
                        <div class="kpi-label">Jours pris (total)</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="fas fa-calendar-minus"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#10B981;">{{ $kpis['total_reliquat'] }}</div>
                        <div class="kpi-label">Reliquat total (jours)</div>
                    </div>
                    <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fas fa-calendar-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.conges.soldes') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="flex-grow-1" style="min-width:220px;max-width:360px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Nom, prénom, matricule…" value="{{ $search }}">
                    </div>
                </div>
                <select name="annee" class="form-select" style="width:auto;min-width:100px;">
                    @foreach($annees as $a)
                        <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
                <select name="service" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id_service }}" {{ $serviceId == $s->id_service ? 'selected' : '' }}>
                            {{ $s->nom_service }}
                        </option>
                    @endforeach
                </select>
                <select name="type_conge" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les types</option>
                    @foreach($typesConge as $t)
                        <option value="{{ $t->id_type_conge }}" {{ $typeCongeId == $t->id_type_conge ? 'selected' : '' }}>
                            {{ $t->libelle }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if($search || $serviceId || $typeCongeId)
                    <a href="{{ route('rh.conges.soldes', ['annee' => $annee]) }}"
                        class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau soldes --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($agents->count() > 0)
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:separate;border-spacing:0;">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Service</th>
                                @foreach($typesConge as $type)
                                    @if(!$typeCongeId || $typeCongeId == $type->id_type_conge)
                                        <th class="text-center" style="min-width:150px;">{{ $type->libelle }}</th>
                                    @endif
                                @endforeach
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                                @php $agentSoldes = $soldes->get($agent->id_agent, collect()); @endphp
                                <tr class="agent-row">
                                    <td>
                                        <div class="fw-600" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                        <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $agent->service->nom_service ?? '-' }}</span>
                                    </td>
                                    @foreach($typesConge as $type)
                                        @if(!$typeCongeId || $typeCongeId == $type->id_type_conge)
                                            @php
                                                $s = $agentSoldes->firstWhere('id_type_conge', $type->id_type_conge);
                                                $pct = $s && $s->solde_initial > 0
                                                    ? round(($s->solde_restant / $s->solde_initial) * 100)
                                                    : 0;
                                                $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                                            @endphp
                                            <td class="text-center">
                                                @if($s)
                                                    <div class="fw-bold" style="font-size:15px;color:{{ $color }};">
                                                        {{ $s->solde_restant }}j
                                                        <span style="font-size:10px;color:var(--theme-text-muted);font-weight:400;">reliquat</span>
                                                    </div>
                                                    <div class="solde-bar mx-auto my-1" style="width:90px;">
                                                        <div class="solde-bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                                                    </div>
                                                    <div style="font-size:10px;color:var(--theme-text-muted);">
                                                        {{ $s->solde_pris }}j pris / {{ $s->solde_initial }}j
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-sm mt-1"
                                                        style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:6px;font-size:10px;color:var(--theme-text);padding:2px 8px;"
                                                        onclick="ouvrirAjustement({{ $s->id_solde }}, '{{ addslashes($agent->nom_complet) }}', '{{ addslashes($type->libelle) }}', {{ $s->solde_initial }}, {{ $s->solde_pris }})">
                                                        <i class="fas fa-edit" style="font-size:9px;"></i> Ajuster
                                                    </button>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                    <td class="text-center">
                                        <a href="{{ route('rh.agents.show', $agent->id_agent) }}"
                                            class="btn btn-sm"
                                            style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:6px;font-size:11px;color:#1E40AF;padding:4px 10px;">
                                            <i class="fas fa-user"></i> Dossier
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-2 border-top" style="background:var(--theme-bg-secondary);font-size:12px;color:var(--theme-text-muted);">
                    {{ $agents->count() }} agent(s) affiché(s)
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 d-block" style="color:#0A4D8C;opacity:.2;"></i>
                    <p class="text-muted mb-2">Aucun résultat pour ces critères.</p>
                    @if(!$search && !$serviceId && !$typeCongeId)
                        <button type="button" class="action-btn action-btn-primary"
                            data-bs-toggle="modal" data-bs-target="#initSoldesModal">
                            <i class="fas fa-plus"></i> Initialiser les soldes {{ $annee }}
                        </button>
                    @else
                        <a href="{{ route('rh.conges.soldes', ['annee' => $annee]) }}" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);">
                            <i class="fas fa-times"></i> Réinitialiser les filtres
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal Initialiser soldes --}}
<div class="modal fade" id="initSoldesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                    <i class="fas fa-calendar-plus me-2" style="color:#0A4D8C;"></i>Initialiser les soldes de congés
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.conges.soldes.init') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <p class="text-muted small mb-3">
                        Initialise les soldes de congés déductibles pour l'année sélectionnée.
                        Les soldes existants ne seront pas écrasés.
                    </p>
                    <div class="mb-3">
                        <label class="modal-label">Année <span class="text-danger">*</span></label>
                        <select name="annee" class="form-select modal-input" required>
                            @foreach($annees as $a)
                                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-3 rounded" style="background:#EFF6FF;border:1px solid #BFDBFE;font-size:12px;">
                        <i class="fas fa-info-circle me-1" style="color:#3B82F6;"></i>
                        <span style="color:#1E40AF;">
                            Types déductibles :
                            @foreach($typesConge->where('deductible', true) as $t)
                                <strong>{{ $t->libelle }}</strong> ({{ $t->nb_jours_droit }}j){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </span>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-check"></i> Initialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Ajustement solde --}}
<div class="modal fade" id="ajusterSoldeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                    <i class="fas fa-edit me-2" style="color:#F59E0B;"></i>Ajuster le solde
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAjustement" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body px-4 py-3">
                    <p class="small mb-3" id="ajust-label" style="color:var(--theme-text);"></p>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="modal-label">Jours accordés <span class="text-danger">*</span></label>
                            <input type="number" name="solde_initial" id="ajust-initial" class="form-control modal-input" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="modal-label">Jours pris <span class="text-danger">*</span></label>
                            <input type="number" name="solde_pris" id="ajust-pris" class="form-control modal-input" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-label">Motif de l'ajustement <span class="text-danger">*</span></label>
                        <textarea name="motif" rows="2" class="form-control modal-input"
                            placeholder="Ex : Correction suite à erreur de saisie, report de congés N-1…" required maxlength="300"></textarea>
                    </div>
                    <div class="p-2 rounded" style="background:#FEF3C7;border:1px solid #FCD34D;font-size:12px;color:#92400E;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Le reliquat sera recalculé automatiquement (Accordés − Pris).
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function ouvrirAjustement(soldeId, agent, type, initial, pris) {
    document.getElementById('ajust-label').textContent = agent + ' - ' + type;
    document.getElementById('ajust-initial').value = initial;
    document.getElementById('ajust-pris').value = pris;
    document.getElementById('formAjustement').action = '/rh/conges/soldes/' + soldeId + '/ajuster';
    const modal = new bootstrap.Modal(document.getElementById('ajusterSoldeModal'));
    modal.show();
}
function showToast(message, type) {
    const cfg = { success:{bg:'#10B981',icon:'fa-check-circle'}, error:{bg:'#EF4444',icon:'fa-exclamation-circle'} };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `<div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:400px;animation:toastIn .3s ease;"><i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i><span>${message}</span><button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif
</script>
@endpush
