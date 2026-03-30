@extends('layouts.master')

@section('title', 'Congés à Approuver')
@section('page-title', 'Congés à Approuver')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>À approuver</li>
@endsection

@push('styles')
<style>
.demande-card { border-radius:12px;transition:box-shadow 200ms;border-left:4px solid #3B82F6; }
.demande-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10); }
.avatar-placeholder { width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0; }
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:var(--theme-text-muted); }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Congés à approuver</h4>
            <p class="text-muted small mb-0">Demandes validées par les Managers, en attente d'approbation finale RH</p>
        </div>
        @if($pending->count() > 0)
            <span style="display:inline-flex;align-items:center;gap:6px;background:#DBEAFE;color:#1E40AF;padding:8px 14px;border-radius:20px;font-size:13px;font-weight:600;">
                <i class="fas fa-user-check"></i>{{ $pending->count() }} à approuver
            </span>
        @endif
    </div>

    @if($pending->count() > 0)
        <div class="row g-3">
            @foreach($pending as $demande)
                @php $conge = $demande->conge; $agent = $demande->agent; @endphp
                <div class="col-12">
                    <div class="demande-card card border-0 shadow-sm" style="background:var(--theme-panel-bg);">
                        <div class="card-body py-3 px-4">
                            <div class="row align-items-center g-3">
                                {{-- Agent --}}
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-placeholder" style="background:#EFF6FF;color:#0A4D8C;">
                                                {{ strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1)) }}
                                            </div>
                                        <div>
                                            <div class="fw-bold small" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->service->nom_service ?? '—' }} — {{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Détails --}}
                                <div class="col-lg-4">
                                    <div class="fw-600 small" style="color:var(--theme-text);">{{ $conge->typeConge->libelle ?? '—' }}</div>
                                    <div class="text-muted small">
                                        Du <strong>{{ $conge->date_debut?->format('d/m/Y') }}</strong>
                                        au <strong>{{ $conge->date_fin?->format('d/m/Y') }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                        <span class="badge" style="background:#DBEAFE;color:#1E40AF;font-size:11px;">
                                            <i class="fas fa-calendar me-1"></i>{{ $conge->nbres_jours }} jour(s)
                                        </span>
                                        <span class="badge" style="background:#D1FAE5;color:#065F46;font-size:10px;">
                                            <i class="fas fa-user-check me-1"></i>Manager validé
                                        </span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        Demandé le {{ $demande->created_at->format('d/m/Y') }}
                                        | Validé le {{ \Carbon\Carbon::parse($demande->date_traitement)->format('d/m/Y') }}
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="col-lg-4 d-flex gap-2 justify-content-lg-end flex-wrap">
                                    <a href="{{ route('rh.conges.show', $demande->id_demande) }}" class="btn btn-sm d-flex align-items-center gap-2" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;padding:8px 14px;font-size:12px;color:var(--theme-text);">
                                        <i class="fas fa-eye"></i> Voir détail
                                    </a>

                                    <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                        style="background:#10B981;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;"
                                        onclick="openApprouveModal({{ $demande->id_demande }}, '{{ addslashes($agent->nom_complet) }}', {{ $conge->nbres_jours }})">
                                        <i class="fas fa-check-double"></i> Approuver
                                    </button>

                                    <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                        style="background:#FEE2E2;color:#991B1B;border:none;border-radius:8px;padding:8px 14px;font-size:12px;"
                                        data-bs-toggle="modal" data-bs-target="#rejetModal{{ $demande->id_demande }}">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal rejet RH --}}
                <div class="modal fade" id="rejetModal{{ $demande->id_demande }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                                    <i class="fas fa-times-circle me-2 text-danger"></i>Rejeter la demande
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('rh.conges.rejeter', $demande->id_demande) }}" method="POST">
                                @csrf
                                <div class="modal-body px-4 py-3">
                                    <p class="text-muted small mb-3">
                                        Rejet du congé de <strong>{{ $agent->nom_complet }}</strong> — {{ $conge->nbres_jours }}j.
                                    </p>
                                    <div class="mb-3">
                                        <label class="modal-label">Motif du rejet <span class="text-danger">*</span></label>
                                        <textarea name="motif_refus" rows="3" class="form-control" style="border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);"
                                            placeholder="Expliquez le motif (minimum 10 caractères)…" required minlength="10"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                                    <button type="button" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger d-flex align-items-center gap-2" style="border-radius:8px;font-size:13px;padding:9px 16px;">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x mb-3 d-block" style="color:#10B981;opacity:.5;"></i>
                <h5 class="fw-bold" style="color:var(--theme-text);">Aucun congé en attente d'approbation</h5>
                <p class="text-muted small">Toutes les demandes ont été traitées.</p>
                <a href="{{ route('rh.conges.index') }}" class="action-btn action-btn-primary mt-2">
                    Voir l'historique complet
                </a>
            </div>
        </div>
    @endif

</div>

{{-- Modal Approbation partagé --}}
<div class="modal fade" id="modalApprouver" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form id="formApprouver" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-check-double me-2" style="color:#10B981;"></i>Approuver le congé
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p id="approuveBody" style="font-size:14px;color:var(--theme-text);margin:0;"></p>
                    <p class="text-muted small mt-2 mb-0">Le solde sera automatiquement déduit.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="action-btn" style="background:var(--theme-bg-secondary);color:var(--theme-text);border:1px solid var(--theme-border);" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn d-flex align-items-center gap-2" style="background:#10B981;color:#fff;border:none;border-radius:8px;font-size:13px;padding:9px 16px;">
                        <i class="fas fa-check-double"></i> Confirmer
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

function openApprouveModal(id, nom, jours) {
    document.getElementById('formApprouver').action = '/rh/conges/' + id + '/approuver';
    document.getElementById('approuveBody').textContent = 'Approuver le congé de ' + nom + ' (' + jours + ' jour(s)) et déduire du solde ?';
    new bootstrap.Modal(document.getElementById('modalApprouver')).show();
}
</script>
@endpush
