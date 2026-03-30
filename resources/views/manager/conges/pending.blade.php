@extends('layouts.master')

@section('title', 'Congés à Valider')
@section('page-title', 'Congés à Valider')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li>Congés à valider</li>
@endsection

@push('styles')
<style>
.demande-card { border-radius:12px;transition:box-shadow 200ms,transform 200ms;border-left:4px solid transparent; }
.demande-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-1px); }
.demande-card.pending { border-left-color:#F59E0B; }
.badge-statut { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-valide { background:#DBEAFE;color:#1E40AF; }
.badge-rejete { background:#FEE2E2;color:#991B1B; }
.avatar-sm { width:40px;height:40px;border-radius:50%;object-fit:cover; }
.avatar-placeholder { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px; }
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Congés à valider</h4>
            <p class="text-muted small mb-0">Demandes de votre équipe en attente de votre validation</p>
        </div>
        @if($pending->count() > 0)
            <span style="display:inline-flex;align-items:center;gap:6px;background:#FEF3C7;color:#92400E;padding:8px 14px;border-radius:20px;font-size:13px;font-weight:600;">
                <i class="fas fa-clock"></i>{{ $pending->count() }} en attente
            </span>
        @endif
    </div>

    @if($pending->count() > 0)
        <div class="row g-3 mb-5">
            @foreach($pending as $demande)
                @php $conge = $demande->conge; $agent = $demande->agent; @endphp
                <div class="col-12">
                    <div class="demande-card card border-0 shadow-sm pending" style="background:var(--theme-panel-bg);">
                        <div class="card-body py-3 px-4">
                            <div class="row align-items-center g-3">
                                {{-- Agent info --}}
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-placeholder" style="background:#EFF6FF;color:#0A4D8C;">
                                                {{ strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1)) }}
                                            </div>
                                        <div>
                                            <div class="fw-bold small" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }} — {{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->service->nom_service ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Détails congé --}}
                                <div class="col-lg-4">
                                    <div class="fw-600 small" style="color:var(--theme-text);">{{ $conge->typeConge->libelle ?? '—' }}</div>
                                    <div class="text-muted small">
                                        Du {{ $conge->date_debut?->format('d/m/Y') }} au {{ $conge->date_fin?->format('d/m/Y') }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge" style="background:#FEF3C7;color:#92400E;font-size:11px;">
                                            <i class="fas fa-calendar me-1"></i>{{ $conge->nbres_jours }} jour(s)
                                        </span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        Demandé le {{ $demande->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="col-lg-4 d-flex gap-2 justify-content-lg-end flex-wrap">
                                    {{-- Valider --}}
                                    <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                        style="background:#10B981;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;"
                                        onclick="openValideModal({{ $demande->id_demande }}, '{{ addslashes($agent->nom_complet) }}', {{ $conge->nbres_jours }})">
                                        <i class="fas fa-check"></i> Valider
                                    </button>

                                    {{-- Rejeter --}}
                                    <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                        style="background:#FEE2E2;color:#991B1B;border:none;border-radius:8px;padding:8px 16px;font-size:12px;"
                                        data-bs-toggle="modal" data-bs-target="#rejetModal{{ $demande->id_demande }}">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Rejet --}}
                <div class="modal fade" id="rejetModal{{ $demande->id_demande }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                                    <i class="fas fa-times-circle me-2 text-danger"></i>Rejeter la demande de congé
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('manager.conges.rejeter', $demande->id_demande) }}" method="POST">
                                @csrf
                                <div class="modal-body px-4 py-3">
                                    <p class="text-muted small mb-3">
                                        Vous êtes sur le point de rejeter la demande de congé de <strong>{{ $agent->nom_complet }}</strong>
                                        ({{ $conge->nbres_jours }} jour(s)).
                                    </p>
                                    <div class="mb-3">
                                        <label class="modal-label">Motif du rejet <span class="text-danger">*</span></label>
                                        <textarea name="motif_refus" rows="3" class="form-control" style="border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);"
                                            placeholder="Expliquez le motif du rejet (minimum 10 caractères)…" required minlength="10"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                                    <button type="button" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger d-flex align-items-center gap-2" style="border-radius:8px;font-size:13px;padding:9px 16px;">
                                        <i class="fas fa-times"></i> Confirmer le rejet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm mb-5" style="border-radius:14px;">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x mb-3 d-block" style="color:#10B981;opacity:.5;"></i>
                <h5 class="fw-bold" style="color:var(--theme-text);">Aucune demande en attente</h5>
                <p class="text-muted small">Toutes les demandes de votre équipe ont été traitées.</p>
            </div>
        </div>
    @endif

    {{-- Récemment traitées --}}
    @if($traitees->count() > 0)
        <h6 class="fw-bold mb-3" style="color:var(--theme-text-muted);font-size:12px;text-transform:uppercase;letter-spacing:.05em;">
            Traitées récemment (7 derniers jours)
        </h6>
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-0">
                @foreach($traitees as $demande)
                    @php $conge = $demande->conge; $agent = $demande->agent; @endphp
                    <div class="d-flex align-items-center px-4 py-3 border-bottom gap-3">
                        <div class="avatar-placeholder flex-shrink-0" style="background:var(--theme-bg-secondary);color:var(--theme-text-muted);font-size:12px;">
                            {{ strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-600 small" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $conge->typeConge->libelle ?? '—' }} — {{ $conge->nbres_jours }} j</div>
                        </div>
                        <span class="badge-statut {{ $demande->statut_demande === 'Validé' ? 'badge-valide' : 'badge-rejete' }}">
                            <i class="fas {{ $demande->statut_demande === 'Validé' ? 'fa-check' : 'fa-times' }}" style="font-size:9px;"></i>
                            {{ $demande->statut_demande }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

{{-- Modal Validation partagé --}}
<div class="modal fade" id="modalValider" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form id="formValider" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-check me-2" style="color:#10B981;"></i>Confirmer la validation
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p id="valideBody" style="font-size:14px;color:var(--theme-text);margin:0;"></p>
                    <p class="text-muted small mt-2 mb-0">La demande sera transmise au service RH pour approbation finale.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn d-flex align-items-center gap-2" style="background:#10B981;color:#fff;border:none;border-radius:8px;font-size:13px;padding:9px 16px;">
                        <i class="fas fa-check"></i> Valider
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

function openValideModal(id, nom, jours) {
    document.getElementById('formValider').action = '/manager/conges/' + id + '/valider';
    document.getElementById('valideBody').textContent = 'Valider la demande de congé de ' + nom + ' (' + jours + ' jour(s)) ?';
    new bootstrap.Modal(document.getElementById('modalValider')).show();
}
</script>
@endpush
