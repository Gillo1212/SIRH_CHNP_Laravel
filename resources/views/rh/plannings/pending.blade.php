@extends('layouts.master')

@section('title', 'Plannings à valider')
@section('page-title', 'Validation des Plannings')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.plannings.index') }}" style="color:#1565C0;">Plannings</a></li>
    <li>À valider</li>
@endsection

@push('styles')
<style>
.panel { background:white;border-radius:12px;padding:20px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;color:#9CA3AF; }
.planning-pending-card {
    background:white; border-radius:14px; border:1px solid #F3F4F6;
    box-shadow:0 1px 4px rgba(0,0,0,.04); transition:box-shadow 200ms,transform 200ms;
    overflow:hidden;
}
.planning-pending-card:hover { box-shadow:0 4px 16px rgba(10,77,140,.08);transform:translateY(-1px); }
.card-header-stripe { height:4px;background:linear-gradient(90deg,#D97706,#F59E0B); }
.agent-avatar { width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;flex-shrink:0; }
.stat-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#F3F4F6;color:#374151; }
[data-theme="dark"] .panel { background:#161b22;border-color:#30363d; }
[data-theme="dark"] .planning-pending-card { background:#161b22;border-color:#30363d; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-dismissible d-flex align-items-center gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #10B981;background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-dismissible d-flex align-items-center gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #EF4444;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;">
            <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                Plannings à valider
                <span style="margin-left:8px;padding:3px 12px;border-radius:20px;background:#FFFBEB;color:#D97706;font-size:14px;font-weight:700;">{{ $count }}</span>
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Plannings transmis par les managers, en attente de votre validation</p>
        </div>
        <a href="{{ route('rh.plannings.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-th-list"></i>Tous les plannings
        </a>
    </div>

    {{-- Bandeau d'information --}}
    @if($count > 0)
        <div style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:1px solid #FDE68A;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#FEF3C7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-hourglass-half" style="color:#D97706;font-size:16px;"></i>
            </div>
            <div>
                <div style="font-weight:600;color:#92400E;font-size:14px;">{{ $count }} planning(s) en attente de validation</div>
                <div style="font-size:12px;color:#B45309;">Traitez les plannings ci-dessous pour permettre leur mise en vigueur.</div>
            </div>
        </div>
    @endif

    {{-- Liste des plannings en attente --}}
    @forelse($plannings as $planning)
        @php
            $nbAgents = $planning->lignes_count > 0
                ? \App\Models\LignePlanning::where('id_planning', $planning->id_planning)->distinct('id_agent')->count('id_agent')
                : 0;
        @endphp
        <div class="planning-pending-card mb-3">
            <div class="card-header-stripe"></div>
            <div class="p-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">

                    {{-- Infos planning --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div style="width:42px;height:42px;border-radius:10px;background:#FFFBEB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-calendar-week" style="color:#D97706;font-size:16px;"></i>
                            </div>
                            <div>
                                <div class="fw-bold" style="color:#111827;font-size:15px;">
                                    {{ $planning->service->nom_service ?? 'Service inconnu' }}
                                </div>
                                <div style="font-size:13px;color:#6B7280;">
                                    {{ $planning->periode_debut->isoFormat('D MMMM') }} → {{ $planning->periode_fin->isoFormat('D MMMM YYYY') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
                            <span class="stat-chip">
                                <i class="fas fa-calendar" style="color:#6B7280;"></i>{{ $planning->duree_jours }} jour(s)
                            </span>
                            <span class="stat-chip">
                                <i class="fas fa-list" style="color:#6B7280;"></i>{{ $planning->lignes_count }} ligne(s)
                            </span>
                            <span class="stat-chip">
                                <i class="fas fa-users" style="color:#6B7280;"></i>{{ $nbAgents }} agent(s)
                            </span>
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#FFFBEB;color:#D97706;">
                                <i class="fas fa-paper-plane" style="font-size:9px;"></i>Transmis le {{ $planning->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('rh.plannings.show', $planning->id_planning) }}"
                           class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                            <i class="fas fa-eye"></i>Voir le détail
                        </a>
                        <button type="button"
                                class="action-btn" style="font-size:12px;padding:7px 14px;background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;"
                                onclick="openModalValider({{ $planning->id_planning }}, '{{ $planning->service->nom_service ?? '' }}', '{{ $planning->periode_debut->format('d/m/Y') }}', '{{ $planning->periode_fin->format('d/m/Y') }}', {{ $planning->lignes_count }})">
                            <i class="fas fa-check-double"></i>Valider
                        </button>
                        <button type="button"
                                class="action-btn" style="font-size:12px;padding:7px 14px;background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;"
                                onclick="openModalRejeter({{ $planning->id_planning }}, '{{ $planning->service->nom_service ?? '' }}', '{{ $planning->periode_debut->format('d/m/Y') }}', '{{ $planning->periode_fin->format('d/m/Y') }}')">
                            <i class="fas fa-times-circle"></i>Rejeter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="panel text-center py-5">
            <div style="width:80px;height:80px;border-radius:50%;background:#ECFDF5;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <i class="fas fa-check-double fa-2x" style="color:#059669;"></i>
            </div>
            <h5 class="fw-bold mb-1" style="color:#111827;">Tout est à jour !</h5>
            <p class="text-muted mb-4" style="font-size:13px;">Aucun planning en attente de validation. Excellent travail.</p>
            <a href="{{ route('rh.plannings.index') }}" class="action-btn action-btn-outline">
                <i class="fas fa-th-list"></i>Voir tous les plannings
            </a>
        </div>
    @endforelse

    @if($plannings->hasPages())
        <div class="mt-4">{{ $plannings->links() }}</div>
    @endif

</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                              --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal : Valider ─────────────────────────────────────────────── --}}
<div class="modal fade" id="modalValider" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="formValider" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;border-radius:50%;background:#ECFDF5;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check-double" style="color:#059669;font-size:18px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Valider ce planning ?</h5>
                            <p class="text-muted mb-0" style="font-size:12px;">Cette action est définitive</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div id="validerInfo" style="background:#F9FAFB;border-radius:10px;padding:14px 16px;margin-bottom:14px;">
                    </div>
                    <div style="background:#ECFDF5;border-left:3px solid #059669;border-radius:6px;padding:10px 12px;font-size:12px;color:#065F46;">
                        <i class="fas fa-info-circle me-1"></i>
                        En validant, le planning sera mis en vigueur. Les agents pourront le consulter dans leur espace.
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#059669;color:white;border:none;">
                        <i class="fas fa-check-double"></i>Confirmer la validation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal : Rejeter ──────────────────────────────────────────────── --}}
<div class="modal fade" id="modalRejeter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="formRejeter" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;border-radius:50%;background:#FEF2F2;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-times-circle" style="color:#DC2626;font-size:18px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Rejeter ce planning</h5>
                            <p class="text-muted mb-0" style="font-size:12px;">Le manager devra corriger et soumettre à nouveau</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div id="rejeterInfo" style="background:#F9FAFB;border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#374151;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-600" style="font-size:13px;">
                            Motif du rejet <span class="text-danger">*</span>
                        </label>
                        <textarea name="motif_rejet" id="motifRejetText" class="form-control" rows="4" required
                                  placeholder="Expliquez clairement pourquoi ce planning est rejeté et ce que le manager doit corriger..."
                                  style="border-radius:8px;font-size:13px;resize:vertical;"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <div style="font-size:11px;color:#9CA3AF;">Minimum 10 caractères</div>
                            <div id="charCount" style="font-size:11px;color:#9CA3AF;">0/500</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#DC2626;color:white;border:none;">
                        <i class="fas fa-times-circle"></i>Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Modal Valider ──────────────────────────────────────────────────
function openModalValider(id, service, debut, fin, nbLignes) {
    document.getElementById('formValider').action = `/rh/plannings/${id}/valider`;
    document.getElementById('validerInfo').innerHTML = `
        <div style="font-weight:600;color:#111827;margin-bottom:4px;">
            <i class="fas fa-hospital-alt me-1" style="color:#0A4D8C;"></i>${service}
        </div>
        <div style="font-size:12px;color:#6B7280;">
            ${debut} → ${fin} &nbsp;·&nbsp; ${nbLignes} ligne(s)
        </div>
    `;
    new bootstrap.Modal(document.getElementById('modalValider')).show();
}

// ── Modal Rejeter ──────────────────────────────────────────────────
function openModalRejeter(id, service, debut, fin) {
    document.getElementById('formRejeter').action = `/rh/plannings/${id}/rejeter`;
    document.getElementById('rejeterInfo').innerHTML = `
        <i class="fas fa-hospital-alt me-1" style="color:#0A4D8C;"></i>
        <strong>${service}</strong> · ${debut} → ${fin}
    `;
    document.getElementById('motifRejetText').value = '';
    document.getElementById('charCount').textContent = '0/500';
    new bootstrap.Modal(document.getElementById('modalRejeter')).show();
}

// Compteur caractères
document.getElementById('motifRejetText')?.addEventListener('input', function() {
    const n = this.value.length;
    const el = document.getElementById('charCount');
    el.textContent = n + '/500';
    el.style.color = n < 10 ? '#DC2626' : n > 450 ? '#D97706' : '#9CA3AF';
});
</script>
@endpush
