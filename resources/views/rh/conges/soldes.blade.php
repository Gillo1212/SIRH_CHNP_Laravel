@extends('layouts.master')

@section('title', 'Soldes de Congés')
@section('page-title', 'Soldes de Congés')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>Soldes</li>
@endsection

@push('styles')
<style>
.solde-bar { height:6px;border-radius:3px;background:var(--theme-bg-secondary);overflow:hidden; }
.solde-bar-fill { height:100%;border-radius:3px;transition:width .5s ease; }
.agent-row td { padding:14px 16px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.agent-row:hover td { background:var(--sirh-primary-hover); }
thead th { padding:10px 16px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
.modal-input { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text); }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Soldes de congés</h4>
            <p class="text-muted small mb-0">Consultez et gérez les soldes de congés par agent</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            {{-- Sélection année --}}
            <form method="GET" action="{{ route('rh.conges.soldes') }}" class="d-flex gap-2 align-items-center">
                <select name="annee" class="form-select form-select-sm" style="border-radius:8px;width:100px;" onchange="this.form.submit()">
                    @foreach($annees as $a)
                        <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" class="action-btn action-btn-primary"
                data-bs-toggle="modal" data-bs-target="#initSoldesModal">
                <i class="fas fa-plus"></i> Initialiser soldes
            </button>
        </div>
    </div>

    {{-- Tableau soldes --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($soldes->count() > 0)
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:separate;border-spacing:0;">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Service</th>
                                @foreach($typesConge->where('deductible', true) as $type)
                                    <th class="text-center" style="min-width:130px;">{{ $type->libelle }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                                @php $agentSoldes = $soldes->get($agent->id_agent, collect()); @endphp
                                @if($agentSoldes->count() > 0)
                                    <tr class="agent-row">
                                        <td>
                                            <div class="fw-600" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $agent->service->nom_service ?? '—' }}</span>
                                        </td>
                                        @foreach($typesConge->where('deductible', true) as $type)
                                            @php
                                                $s = $agentSoldes->firstWhere('id_type_conge', $type->id_type_conge);
                                                $pct = $s && $s->solde_initial > 0
                                                    ? round(($s->solde_restant / $s->solde_initial) * 100)
                                                    : 0;
                                                $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                                            @endphp
                                            <td class="text-center">
                                                @if($s)
                                                    <div class="fw-bold" style="font-size:16px;color:{{ $color }};">{{ $s->solde_restant }}j</div>
                                                    <div class="solde-bar mx-auto my-1" style="width:80px;">
                                                        <div class="solde-bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                                                    </div>
                                                    <div class="text-muted" style="font-size:10px;">{{ $s->solde_pris }}j pris / {{ $s->solde_initial }}j</div>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-database fa-3x mb-3 d-block" style="color:#0A4D8C;opacity:.2;"></i>
                    <p class="text-muted mb-2">Aucun solde initialisé pour {{ $annee }}.</p>
                    <button type="button" class="action-btn action-btn-primary"
                        data-bs-toggle="modal" data-bs-target="#initSoldesModal">
                        <i class="fas fa-plus"></i> Initialiser les soldes
                    </button>
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
                        Initialise les soldes de congés déductibles pour un agent selon les droits définis par type.
                        Si un solde existe déjà, il ne sera pas écrasé.
                    </p>
                    <div class="mb-3">
                        <label class="modal-label">Agent <span class="text-danger">*</span></label>
                        <select name="id_agent" class="form-select modal-input" required>
                            <option value="">-- Sélectionner un agent --</option>
                            @foreach($agents as $a)
                                <option value="{{ $a->id_agent }}">{{ $a->nom_complet }} ({{ $a->matricule }})</option>
                            @endforeach
                        </select>
                    </div>
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
                                <strong>{{ $t->libelle }}</strong> ({{ $t->nb_jours_droit }}j){{ !$loop->last ? ',' : '' }}
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

@endsection

@push('scripts')
<script>
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
