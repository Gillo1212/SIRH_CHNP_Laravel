@extends('layouts.master')

@section('title', 'Divisions — RH')
@section('page-title', 'Gestion des Divisions')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Divisions</li>
@endsection

@push('styles')
<style>
/* ── KPI CARDS ─────────────────────────────────────────────── */
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

/* ── ACTION BUTTONS ─────────────────────────────────────────── */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30);transform:translateY(-1px); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }

/* ── SECTION TITLE ──────────────────────────────────────────── */
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;padding-bottom:6px;color:var(--theme-text-muted); }

/* ── DIVISION PANEL ─────────────────────────────────────────── */
.division-panel { border-radius:12px;background:var(--theme-panel-bg);border:1px solid var(--theme-border);padding:20px 24px;transition:box-shadow 200ms; }
.division-panel:hover { box-shadow:0 4px 16px rgba(10,77,140,.08); }

.service-chip {
    display:inline-flex;align-items:center;gap:6px;
    background:var(--theme-bg-secondary);border:1px solid var(--theme-border);
    border-radius:8px;padding:6px 12px;font-size:12px;
    text-decoration:none;color:var(--theme-text);
    transition:all 180ms;
}
.service-chip:hover { background:#EFF6FF;border-color:#BFDBFE;color:#0A4D8C; }

/* ── MODAL LABELS ───────────────────────────────────────────── */
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
.modal-input { border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text); }
.modal-input:focus { border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12); }

/* ── TOAST ANIMATION ────────────────────────────────────────── */
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')

{{-- ── EN-TÊTE ─────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">Divisions Organisationnelles</h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            {{ $totaux['divisions'] }} division(s) · {{ $totaux['services'] }} service(s) · {{ $totaux['agents'] }} agent(s)
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('rh.services.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-hospital-alt"></i> Services
        </a>
        <button type="button" class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateDivision">
            <i class="fas fa-plus"></i> Nouvelle division
        </button>
    </div>
</div>

{{-- ── KPIs ─────────────────────────────────────────────────────────────── --}}
<div class="section-title">Vue d'ensemble</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="kpi-card blue" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);">
            <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-sitemap" style="color:#0A4D8C;"></i></div>
            <div class="kpi-value" style="color:#0A4D8C;">{{ $totaux['divisions'] }}</div>
            <div class="kpi-label">Divisions</div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="kpi-card green" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);">
            <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-hospital-alt" style="color:#059669;"></i></div>
            <div class="kpi-value" style="color:#059669;">{{ $totaux['services'] }}</div>
            <div class="kpi-label">Services</div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="kpi-card amber" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);">
            <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-users" style="color:#D97706;"></i></div>
            <div class="kpi-value" style="color:#D97706;">{{ $totaux['agents'] }}</div>
            <div class="kpi-label">Agents</div>
        </div>
    </div>
</div>

{{-- ── LISTE DES DIVISIONS ─────────────────────────────────────────────── --}}
<div class="section-title">Liste des divisions</div>
<div class="d-flex flex-column gap-3">
    @forelse($divisions as $division)
        <div class="division-panel">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:18px;flex-shrink:0;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:var(--theme-text);">{{ $division->nom_division }}</h6>
                        <span style="font-size:12px;color:var(--theme-text-muted);">
                            {{ $division->services->count() }} service(s) ·
                            {{ $division->services->sum('agents_count') }} agent(s)
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="action-btn action-btn-outline" style="padding:6px 12px;font-size:12px;"
                        onclick="openEditDiv({{ $division->id_division }}, @json($division->nom_division))">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn d-flex align-items-center gap-1" style="padding:6px 12px;font-size:12px;border-radius:8px;background:var(--theme-panel-bg);border:1px solid var(--theme-border);color:#DC2626;transition:all 180ms;"
                        onmouseover="this.style.background='#FEF2F2'"
                        onmouseout="this.style.background='var(--theme-panel-bg)'"
                        onclick="openDeleteDiv({{ $division->id_division }}, @json($division->nom_division))">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            @if($division->services->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    @foreach($division->services as $service)
                        <a href="{{ route('rh.services.show', $service->id_service) }}" class="service-chip">
                            <i class="fas fa-hospital-alt" style="color:#0A4D8C;font-size:10px;"></i>
                            <span>{{ $service->nom_service }}</span>
                            <span style="background:#EFF6FF;color:#1E40AF;border-radius:20px;padding:1px 7px;font-size:10px;font-weight:700;">
                                {{ $service->agents_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mb-0" style="font-size:12px;color:var(--theme-text-muted);">
                    <i class="fas fa-info-circle me-1"></i>Aucun service dans cette division
                </p>
            @endif
        </div>
    @empty
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius:12px;background:var(--theme-panel-bg);">
            <i class="fas fa-sitemap fa-3x mb-3 d-block" style="color:#D1D5DB;"></i>
            <h6 class="fw-bold">Aucune division</h6>
            <p class="text-muted small">Créez la première division organisationnelle.</p>
            <button type="button" class="btn btn-primary btn-sm mx-auto" style="border-radius:8px;width:fit-content;padding:8px 20px;"
                    data-bs-toggle="modal" data-bs-target="#modalCreateDivision">
                <i class="fas fa-plus me-1"></i>Créer une division
            </button>
        </div>
    @endforelse
</div>


{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — CRÉER UNE DIVISION
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCreateDivision" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form action="{{ route('rh.divisions.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Nouvelle division
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <label class="modal-label">Nom de la division <span class="text-danger">*</span></label>
                    <input type="text" name="nom_division" class="form-control modal-input" required
                           placeholder="ex: Division Médicale">
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — MODIFIER UNE DIVISION
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditDivision" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form id="formEditDiv" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-edit me-2 text-warning"></i>Modifier la division
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <label class="modal-label">Nom de la division <span class="text-danger">*</span></label>
                    <input type="text" id="editDivNom" name="nom_division" class="form-control modal-input" required>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     MODAL — SUPPRIMER UNE DIVISION
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDeleteDivision" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form id="formDeleteDiv" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p style="font-size:14px;color:var(--theme-text);">
                        Supprimer définitivement la division<br>
                        <strong id="deleteDivName"></strong> ?
                    </p>
                    <p class="text-muted small mb-0">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
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

@push('scripts')
<script>
/* ── TOAST ──────────────────────────────────────────────────────────── */
function showToast(message, type) {
    const cfg = {
        success: { bg:'#10B981', icon:'fa-check-circle' },
        error:   { bg:'#EF4444', icon:'fa-exclamation-circle' },
    };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" style="
            position:fixed;top:22px;right:22px;z-index:10000;
            background:${c.bg};color:#fff;border-radius:12px;
            padding:14px 20px;display:flex;align-items:center;gap:12px;
            box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;
            max-width:400px;animation:toastIn .3s ease;">
            <i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i>
            <span>${message}</span>
            <button onclick="document.getElementById('${id}').remove()"
                    style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button>
        </div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}

/* ── AUTO-TOAST session flash ───────────────────────────────────────── */
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif

/* ── MODAL ÉDITION DIVISION ─────────────────────────────────────────── */
function openEditDiv(id, nom) {
    document.getElementById('formEditDiv').action = '/rh/divisions/' + id;
    document.getElementById('editDivNom').value = nom || '';
    new bootstrap.Modal(document.getElementById('modalEditDivision')).show();
}

/* ── MODAL SUPPRESSION DIVISION ─────────────────────────────────────── */
function openDeleteDiv(id, nom) {
    document.getElementById('formDeleteDiv').action = '/rh/divisions/' + id;
    document.getElementById('deleteDivName').textContent = '« ' + nom + ' »';
    new bootstrap.Modal(document.getElementById('modalDeleteDivision')).show();
}
</script>
@endpush
