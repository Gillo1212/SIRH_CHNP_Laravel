@extends('layouts.master')

@section('title', $service->nom_service . ' — Détails')
@section('page-title', $service->nom_service)

@section('breadcrumb')
    <li><a href="{{ route('rh.services.index') }}" style="color:#1565C0;">Services</a></li>
    <li>{{ $service->nom_service }}</li>
@endsection

@push('styles')
<style>
.kpi-mini { border-radius:10px;padding:16px 20px;text-align:center; }
.kpi-mini .val { font-size:26px;font-weight:700;line-height:1.1; }
.kpi-mini .lbl { font-size:11px;font-weight:500;margin-top:2px; }
.agent-row { display:flex;align-items:center;padding:10px 0;border-bottom:1px solid var(--theme-border); }
.agent-row:last-child { border-bottom:none; }
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:8px 15px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-light { background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.25); }
.action-btn-light:hover { background:rgba(255,255,255,0.25);color:#fff; }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
.modal-input { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text); }
.modal-input:focus { border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12); }
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" style="border-radius:10px;border-left:4px solid #10B981;">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-4" style="border-radius:10px;border-left:4px solid #EF4444;">
            <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── EN-TÊTE SERVICE ──────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-white mb-1">{{ $service->nom_service }}</h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span style="background:rgba(255,255,255,0.2);color:white;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                            {{ $service->type_service }}
                        </span>
                        @if($service->division)
                            <span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.8);font-size:11px;padding:3px 10px;border-radius:20px;">
                                <i class="fas fa-sitemap me-1"></i>{{ $service->division->nom_division }}
                            </span>
                        @endif
                        @if($service->tel_service)
                            <span style="color:rgba(255,255,255,0.7);font-size:12px;">
                                <i class="fas fa-phone me-1"></i>{{ $service->tel_service }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('update', $service)
                    <button type="button" class="action-btn action-btn-light"
                            data-bs-toggle="modal" data-bs-target="#modalEditService">
                        <i class="fas fa-edit"></i>Modifier
                    </button>
                    @endcan
                    @can('assignerManager', $service)
                    <button type="button" class="action-btn action-btn-light"
                            data-bs-toggle="modal" data-bs-target="#modalAssignManager">
                        <i class="fas fa-user-tie"></i>Manager
                    </button>
                    @endcan
                    @can('delete', $service)
                    <button type="button" class="action-btn"
                            style="background:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.3);"
                            data-bs-toggle="modal" data-bs-target="#modalDeleteService">
                        <i class="fas fa-trash"></i>Supprimer
                    </button>
                    @endcan
                    <a href="{{ route('rh.services.index') }}" class="action-btn action-btn-light">
                        <i class="fas fa-arrow-left"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KPIs ─────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#EFF6FF;">
                <div class="val" style="color:#0A4D8C;">{{ $stats['total_agents'] }}</div>
                <div class="lbl text-muted">Agents total</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#ECFDF5;">
                <div class="val" style="color:#059669;">{{ $stats['active_agents'] }}</div>
                <div class="lbl text-muted">Actifs</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#FFFBEB;">
                <div class="val" style="color:#D97706;">{{ $stats['pending_leaves'] }}</div>
                <div class="lbl text-muted">Congés en attente</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#FEF2F2;">
                <div class="val" style="color:#DC2626;">{{ $stats['current_month_absences'] }}</div>
                <div class="lbl text-muted">Absences ce mois</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ── MANAGER ──────────────────────────────────────────────────── --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0"><i class="fas fa-user-tie me-2 text-primary"></i>Manager Responsable</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($service->manager && $service->manager->agent)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($service->manager->agent->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $service->manager->agent->nom_complet }}</div>
                                <div class="text-muted small">{{ $service->manager->agent->fonction ?? 'Manager' }}</div>
                                <div class="text-muted small">{{ $service->manager->login }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                            <small>Aucun manager assigné</small>
                        </div>
                    @endif

                    @can('assignerManager', $service)
                    <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2" style="border-radius:8px;"
                            data-bs-toggle="modal" data-bs-target="#modalAssignManager">
                        <i class="fas fa-user-edit me-1"></i>Changer de manager
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- ── GRAPHIQUE ABSENCES ───────────────────────────────────────── --}}
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius:14px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Absences — 6 derniers mois</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartAbsences" style="max-height:180px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── LISTE AGENTS ─────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mt-4" style="border-radius:14px;background:var(--theme-panel-bg);">
        <div class="card-header border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Agents du service ({{ $service->agents->count() }})</h6>
            <div class="d-flex gap-2">
                @can('viewAny', App\Models\Agent::class)
                <a href="{{ route('rh.agents.index', ['service' => $service->id_service]) }}"
                   class="btn btn-sm btn-outline-primary" style="border-radius:6px;">
                    <i class="fas fa-list me-1"></i>Gérer les agents
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            @forelse($service->agents as $agent)
                <div class="agent-row">
                    <div style="width:36px;height:36px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;color:#0A4D8C;font-weight:700;font-size:12px;flex-shrink:0;margin-right:12px;">
                        {{ strtoupper(substr($agent->prenom, 0, 1) . substr($agent->nom, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-500 small">{{ $agent->nom_complet }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }} — {{ $agent->fontion }}</div>
                    </div>
                    <span class="badge {{ $agent->statut_agent === 'actif' ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px;">
                        {{ ucfirst($agent->statut_agent) }}
                    </span>
                    <a href="{{ route('rh.agents.show', $agent->id_agent) }}" class="btn btn-sm btn-light ms-2" style="border-radius:6px;">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-users fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                    <small>Aucun agent dans ce service</small>
                </div>
            @endforelse
        </div>
    </div>

</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — MODIFIER LE SERVICE
     ═══════════════════════════════════════════════════════════════════════ --}}
@can('update', $service)
<div class="modal fade" id="modalEditService" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form action="{{ route('rh.services.update', $service->id_service) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-edit me-2 text-warning"></i>Modifier le service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="modal-label">Nom du service <span class="text-danger">*</span></label>
                        <input type="text" name="nom_service"
                               value="{{ old('nom_service', $service->nom_service) }}"
                               class="form-control modal-input @error('nom_service') is-invalid @enderror"
                               required>
                        @error('nom_service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select name="type_service" class="form-select modal-input @error('type_service') is-invalid @enderror" required>
                                @foreach(['Clinique', 'Administratif', 'Aide_diagnostic', 'Support'] as $type)
                                    <option value="{{ $type }}" {{ old('type_service', $service->type_service) == $type ? 'selected' : '' }}>
                                        {{ $type === 'Aide_diagnostic' ? 'Aide diagnostic' : $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="modal-label">Division</label>
                            <select name="id_division" class="form-select modal-input">
                                <option value="">— Aucune —</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id_division }}"
                                        {{ old('id_division', $service->id_division) == $div->id_division ? 'selected' : '' }}>
                                        {{ $div->nom_division }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="modal-label">Téléphone</label>
                            <input type="text" name="tel_service"
                                   value="{{ old('tel_service', $service->tel_service) }}"
                                   class="form-control modal-input"
                                   placeholder="+221 33 xxx xx xx">
                        </div>
                        <div class="col-6">
                            <label class="modal-label">Manager</label>
                            <select name="id_agent_manager" class="form-select modal-input">
                                <option value="">— Aucun —</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}"
                                        {{ old('id_agent_manager', $service->id_agent_manager) == $mgr->id ? 'selected' : '' }}>
                                        {{ $mgr->agent?->nom_complet ?? $mgr->login }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius:8px;font-size:13px;" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning d-flex align-items-center gap-2" style="border-radius:8px;font-size:13px;font-weight:500;">
                        <i class="fas fa-save"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — ASSIGNER / CHANGER DE MANAGER
     ═══════════════════════════════════════════════════════════════════════ --}}
@can('assignerManager', $service)
<div class="modal fade" id="modalAssignManager" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form action="{{ route('rh.services.assigner-manager', $service->id_service) }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-user-tie me-2" style="color:#7C3AED;"></i>Assigner manager
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-muted small mb-3">Service : <strong>{{ $service->nom_service }}</strong></p>
                    <label class="modal-label">Manager</label>
                    <select name="id_agent_manager" class="form-select modal-input">
                        <option value="">— Retirer le manager —</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}"
                                {{ $service->id_agent_manager == $mgr->id ? 'selected' : '' }}>
                                {{ $mgr->agent?->nom_complet ?? $mgr->login }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius:8px;font-size:13px;" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius:8px;font-size:13px;font-weight:500;">
                        <i class="fas fa-check"></i>Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — SUPPRIMER LE SERVICE (DRH uniquement)
     ═══════════════════════════════════════════════════════════════════════ --}}
@can('delete', $service)
<div class="modal fade" id="modalDeleteService" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form action="{{ route('rh.services.destroy', $service->id_service) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer le service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p style="font-size:14px;color:var(--theme-text);">
                        Supprimer définitivement<br>
                        <strong>« {{ $service->nom_service }} »</strong> ?
                    </p>
                    <p class="text-muted small mb-0">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius:8px;font-size:13px;" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger d-flex align-items-center gap-2" style="border-radius:8px;font-size:13px;font-weight:500;">
                        <i class="fas fa-trash"></i>Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ── GRAPHIQUE ABSENCES ─────────────────────────────────────────────── */
const absencesData = @json($absencesByMonth);
new Chart(document.getElementById('chartAbsences'), {
    type: 'bar',
    data: {
        labels: absencesData.map(d => d.label),
        datasets: [{
            label: 'Absences',
            data: absencesData.map(d => d.count),
            backgroundColor: 'rgba(217,119,6,0.15)',
            borderColor: '#D97706',
            borderWidth: 1.5,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display:false }, ticks: { color:'#9CA3AF', font:{ size:11 } } },
            y: { grid: { color:'#F3F4F6' }, ticks: { color:'#9CA3AF', font:{ size:11 } }, beginAtZero:true }
        }
    }
});

/* ── TOAST SESSION FLASH ────────────────────────────────────────────── */
function showToast(message, type) {
    const cfg = { success:{ bg:'#10B981', icon:'fa-check-circle' }, error:{ bg:'#EF4444', icon:'fa-exclamation-circle' } };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:400px;animation:toastIn .3s ease;">
            <i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i>
            <span>${message}</span>
            <button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button>
        </div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif

/* ── OUVRIR MODAL EDIT SI ERREURS DE VALIDATION ─────────────────────── */
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalEditService')).show();
    });
@endif
</script>
@endpush
