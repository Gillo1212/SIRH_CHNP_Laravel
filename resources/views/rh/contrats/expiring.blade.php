@extends('layouts.master')

@section('title', 'Contrats à renouveler')
@section('page-title', 'Alertes Contrats')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.contrats.index') }}" style="color:#1565C0;">Contrats</a></li>
    <li>Alertes expiration</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:26px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.red::before    { background:#DC2626; }
.kpi-card.orange::before { background:#EA580C; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.green::before  { background:#059669; }

.table-custom { width:100%;border-collapse:separate;border-spacing:0; }
.table-custom thead th { padding:10px 14px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
.table-custom tbody td { padding:13px 14px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.table-custom tbody tr:hover { background:var(--sirh-primary-hover); }
.table-custom tbody tr:last-child td { border-bottom:none; }

.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C; }
.action-btn-sm { padding:5px 10px;font-size:11px;gap:4px; }

.urgence-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap; }
.u-critical { background:#FEE2E2;color:#991B1B;animation:pulse 1.5s infinite; }
.u-high     { background:#FEE2E2;color:#991B1B; }
.u-medium   { background:#FEF3C7;color:#92400E; }
.u-low      { background:#ECFDF5;color:#065F46; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.6;} }

.countdown-bar { height:8px;border-radius:4px;background:var(--theme-bg-secondary);overflow:hidden;margin-top:4px; }
.countdown-fill { height:100%;border-radius:4px;transition:width 300ms; }

.filter-chip { display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:500;cursor:pointer;text-decoration:none;border:1.5px solid transparent;transition:all 180ms; }
.filter-chip.active   { background:#0A4D8C;color:#fff;border-color:#0A4D8C; }
.filter-chip.inactive { background:var(--theme-panel-bg);color:var(--theme-text);border-color:var(--theme-border); }
.filter-chip.inactive:hover { border-color:#0A4D8C;color:#0A4D8C; }

.modal-header-warning { background:linear-gradient(135deg,#D97706 0%,#F59E0B 100%);border:none; }
.modal-header-warning .modal-title { color:#fff;font-size:15px;font-weight:600; }
.modal-header-warning .btn-close { filter:invert(1); }
.form-label-up { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);margin-bottom:4px; }
.form-control-sirh,.form-select-sirh { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);padding:8px 12px; }
.form-control-sirh:focus,.form-select-sirh:focus { border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12); }

@keyframes toastIn { from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);} }

[data-theme="dark"] .form-control-sirh,[data-theme="dark"] .form-select-sirh { background:#161b22;border-color:#30363d;color:#e6edf3; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--theme-text);">
            <i class="fas fa-exclamation-triangle me-2" style="color:#D97706;"></i>
            Contrats à renouveler
        </h4>
        <p class="text-muted small mb-0">
            Contrats actifs dont l'échéance approche — action requise
        </p>
    </div>
    <a href="{{ route('rh.contrats.index') }}" class="action-btn action-btn-outline">
        <i class="fas fa-arrow-left"></i> Tous les contrats
    </a>
</div>

{{-- ─── KPIs URGENCE ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('rh.contrats.expiring', ['jours' => 7]) }}" class="text-decoration-none">
            <div class="kpi-card red border {{ $jours == 7 ? 'border-danger' : '' }}" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#EF4444;">{{ $stats['expiring_7'] }}</div>
                        <div class="kpi-label">< 7 jours</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEE2E2;color:#DC2626;">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
                @if($stats['expiring_7'] > 0)
                <div style="font-size:10px;margin-top:6px;color:#DC2626;font-weight:700;">
                    <i class="fas fa-circle me-1" style="font-size:7px;animation:pulse 1s infinite;"></i>
                    CRITIQUE
                </div>
                @endif
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('rh.contrats.expiring', ['jours' => 15]) }}" class="text-decoration-none">
            <div class="kpi-card orange border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#EA580C;">{{ $stats['expiring_15'] }}</div>
                        <div class="kpi-label">< 15 jours</div>
                    </div>
                    <div class="kpi-icon" style="background:#FFF7ED;color:#EA580C;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('rh.contrats.expiring', ['jours' => 30]) }}" class="text-decoration-none">
            <div class="kpi-card amber border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#F59E0B;">{{ $stats['expiring_30'] }}</div>
                        <div class="kpi-label">< 30 jours</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('rh.contrats.expiring', ['jours' => 60]) }}" class="text-decoration-none">
            <div class="kpi-card green border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#10B981;">{{ $stats['expiring_60'] }}</div>
                        <div class="kpi-label">< 60 jours</div>
                    </div>
                    <div class="kpi-icon" style="background:#ECFDF5;color:#059669;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ─── FILTRES CHIPS ─────────────────────────────────────────────── --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    <span class="text-muted small align-self-center me-1">Afficher :</span>
    @foreach([7 => '< 7 jours', 15 => '< 15 jours', 30 => '< 30 jours', 60 => '< 60 jours'] as $j => $label)
    <a href="{{ route('rh.contrats.expiring', ['jours' => $j]) }}"
       class="filter-chip {{ $jours == $j ? 'active' : 'inactive' }}">
        @if($j == 7) <i class="fas fa-fire"></i>
        @elseif($j == 15) <i class="fas fa-exclamation-circle"></i>
        @elseif($j == 30) <i class="fas fa-clock"></i>
        @else <i class="fas fa-calendar-alt"></i>
        @endif
        {{ $label }}
        <span style="background:rgba(0,0,0,.12);padding:1px 6px;border-radius:10px;font-size:10px;">
            {{ $stats['expiring_'.$j] }}
        </span>
    </a>
    @endforeach
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
                        <th>Type contrat</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Urgence</th>
                        <th style="text-align:right;width:120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contrats as $contrat)
                    @php
                        $agent = $contrat->agent;
                        $jr = $contrat->jours_restants;
                        $urgence = $contrat->urgence;
                        $uClass = match($urgence) {
                            'critical' => 'u-critical',
                            'high'     => 'u-high',
                            'medium'   => 'u-medium',
                            default    => 'u-low',
                        };
                        // Barre de progression (100% = 60j)
                        $progress = $jr !== null ? max(0, min(100, ($jr / 60) * 100)) : 100;
                        $barColor = match($urgence) {
                            'critical' => '#DC2626',
                            'high'     => '#EF4444',
                            'medium'   => '#F59E0B',
                            default    => '#10B981',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px;flex-shrink:0;">
                                    {{ strtoupper(substr($agent?->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($agent?->nom ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-600" style="color:var(--theme-text);font-size:13px;">
                                        {{ $agent?->nom_complet ?? '—' }}
                                    </div>
                                    <div class="text-muted" style="font-size:11px;">
                                        {{ $agent?->matricule }} · {{ $agent?->fonction }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--theme-text);">
                                {{ $agent?->service?->nom_service ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:var(--theme-bg-secondary);color:#0A4D8C;">
                                {{ $contrat->type_contrat }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--theme-text);">
                                {{ $contrat->date_debut?->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px;font-weight:600;color:{{ $jr <= 15 ? '#DC2626' : ($jr <= 30 ? '#D97706' : 'var(--theme-text)') }};">
                                {{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}
                            </span>
                        </td>
                        <td style="min-width:140px;">
                            <span class="urgence-badge {{ $uClass }}">
                                @if($urgence === 'critical') <i class="fas fa-fire" style="font-size:10px;"></i>
                                @else <i class="fas fa-clock" style="font-size:10px;"></i>
                                @endif
                                @if($jr !== null)
                                    {{ $jr <= 0 ? 'Expiré' : $jr . ' jours' }}
                                @else
                                    En cours
                                @endif
                            </span>
                            @if($jr !== null && $jr >= 0)
                            <div class="countdown-bar" style="max-width:120px;">
                                <div class="countdown-fill" style="width:{{ $progress }}%;background:{{ $barColor }};"></div>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        class="action-btn action-btn-sm btn-renouveler"
                                        style="background:linear-gradient(135deg,#D97706,#F59E0B);color:#fff;box-shadow:0 2px 8px rgba(217,119,6,.3);"
                                        data-id="{{ $contrat->id_contrat }}"
                                        data-agent="{{ $agent?->nom_complet }}"
                                        data-type="{{ $contrat->type_contrat }}"
                                        data-fin="{{ $contrat->date_fin?->format('d/m/Y') }}">
                                    <i class="fas fa-sync-alt"></i>
                                    Renouveler
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($contrats->hasPages())
        <div class="px-4 py-3 border-top" style="background:var(--theme-panel-bg);">
            {{ $contrats->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-5">
            <div style="width:64px;height:64px;background:#D1FAE5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fas fa-check-circle fa-2x" style="color:#059669;"></i>
            </div>
            <p class="fw-600 mb-1" style="color:var(--theme-text);">Aucun contrat expirant bientôt</p>
            <p class="text-muted small mb-3">
                Aucun contrat actif n'expire dans les {{ $jours }} prochains jours.
            </p>
            <a href="{{ route('rh.contrats.index') }}" class="action-btn action-btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
        @endif
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
                    <div class="alert mb-4" style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;color:#92400E;font-size:13px;">
                        <div class="fw-600 mb-1"><i class="fas fa-info-circle me-2"></i>Renouvellement de contrat</div>
                        <div>Le contrat actuel sera <strong>automatiquement clôturé</strong> et un nouveau contrat actif sera créé.</div>
                        <div class="mt-1" id="renouvelInfo" style="font-size:12px;opacity:.8;"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
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
                            <input type="date" name="date_fin" class="form-control-sirh form-control">
                            <div class="form-text" style="font-size:11px;">Laisser vide pour CDI/indéterminé</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-up">Motif / Observation</label>
                            <textarea name="observation" class="form-control-sirh form-control"
                                      rows="2" style="resize:none;"
                                      placeholder="Ex : Renouvellement annuel ordinaire…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:var(--theme-panel-bg);border-top:1px solid var(--theme-border);">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#D97706;color:#fff;">
                        <i class="fas fa-sync-alt me-1"></i>Renouveler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showToast(message, type = 'success') {
    const cfg = { success:{bg:'#10B981',icon:'fa-check-circle'}, error:{bg:'#EF4444',icon:'fa-exclamation-circle'} };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `<div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:420px;animation:toastIn .3s ease;"><i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i><span>${message}</span><button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 5000);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif

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

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('renouvelDateDebut').value = tomorrow.toISOString().split('T')[0];

    document.getElementById('formRenouveler').action = `/rh/contrats/${id}/renouveler`;
    new bootstrap.Modal(document.getElementById('modalRenouveler')).show();
});
</script>
@endpush
