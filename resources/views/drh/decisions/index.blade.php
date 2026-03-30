@extends('layouts.master')
@section('title', 'Décisions RH — DRH')
@section('page-title', 'Décisions RH')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>Décisions RH</li>
@endsection

@push('styles')
<style>
.decision-row{padding:16px 24px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;transition:background 150ms;}
.decision-row:hover{background:#FAFAFA;}
.decision-row:last-child{border-bottom:none;}
.badge-d{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;margin-bottom:20px;}
.section-header{padding:14px 24px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;}
.kpi-box{border-radius:12px;padding:14px 20px;text-align:center;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" style="border-radius:10px;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Décisions RH</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Validation et signature des décisions de mouvement du personnel</p>
        </div>
        @if($stats['en_attente'] > 0)
        <span style="background:#FEF3C7;color:#92400E;padding:8px 18px;border-radius:8px;font-weight:700;font-size:14px;">
            <i class="fas fa-exclamation-circle me-1"></i>{{ $stats['en_attente'] }} action(s) requise(s)
        </span>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-box" style="background:#FFFBEB;border:1px solid #FDE68A;">
                <div style="font-size:26px;font-weight:700;color:#D97706;">{{ $stats['a_valider'] }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:3px;">À valider</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-box" style="background:#EFF6FF;border:1px solid #BFDBFE;">
                <div style="font-size:26px;font-weight:700;color:#1565C0;">{{ $stats['a_signer'] }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:3px;">À signer</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-box" style="background:#ECFDF5;border:1px solid #A7F3D0;">
                <div style="font-size:26px;font-weight:700;color:#059669;">{{ $stats['signees'] }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:3px;">Décisions signées</div>
            </div>
        </div>
    </div>

    {{-- ─── SECTION 1 : À VALIDER ─────────────────────────────── --}}
    <div class="section-card">
        <div class="section-header">
            <div style="font-weight:600;color:var(--theme-text);font-size:15px;">
                <i class="fas fa-clock me-2" style="color:#D97706;"></i>Étape 1 — Mouvements soumis par le RH
            </div>
            <span class="badge-d" style="background:#FEF3C7;color:#92400E;">{{ $stats['a_valider'] }}</span>
        </div>

        @forelse($aValider as $mouvement)
        <div class="decision-row">
            <div>
                <div style="font-weight:600;font-size:14px;color:var(--theme-text);">
                    {{ $mouvement->agent?->nom_complet ?? '—' }}
                    <span style="font-size:12px;color:#9CA3AF;font-weight:400;margin-left:8px;">{{ $mouvement->agent?->matricule }}</span>
                </div>
                <div style="font-size:13px;color:#6B7280;margin-top:3px;">
                    <span style="font-weight:600;color:#1565C0;">{{ $mouvement->type_mouvement }}</span>
                    @if($mouvement->serviceDestination)
                    → {{ $mouvement->serviceDestination->nom_service }}
                    @endif
                    · Date effet : {{ $mouvement->date_mouvement?->format('d/m/Y') ?? '—' }}
                </div>
                @if($mouvement->motif)
                <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">{{ Str::limit($mouvement->motif, 90) }}</div>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                {{-- Valider → valide_drh --}}
                <form action="{{ route('drh.validations.valider-mouvement', $mouvement->id_mouvement) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Valider ce mouvement ?\nIl passera ensuite à l\'étape de signature.')">
                    @csrf
                    <button type="submit" style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:8px;background:#1565C0;color:#fff;border:none;font-size:13px;font-weight:600;cursor:pointer;">
                        <i class="fas fa-check"></i>Valider
                    </button>
                </form>
                {{-- Rejeter --}}
                <button type="button"
                        style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:8px;background:#FEE2E2;color:#DC2626;border:none;font-size:13px;font-weight:600;cursor:pointer;"
                        onclick="rejeterMouvement({{ $mouvement->id_mouvement }})">
                    <i class="fas fa-times"></i>Rejeter
                </button>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:32px 20px;color:#9CA3AF;">
            <i class="fas fa-check-circle" style="font-size:32px;color:#D1D5DB;display:block;margin-bottom:10px;"></i>
            Aucun mouvement en attente de validation.
        </div>
        @endforelse
    </div>

    {{-- ─── SECTION 2 : À SIGNER ──────────────────────────────── --}}
    <div class="section-card">
        <div class="section-header">
            <div style="font-weight:600;color:var(--theme-text);font-size:15px;">
                <i class="fas fa-signature me-2" style="color:#059669;"></i>Étape 2 — Décisions à signer
            </div>
            <span class="badge-d" style="background:#DBEAFE;color:#1E40AF;">{{ $stats['a_signer'] }}</span>
        </div>

        @forelse($aSignerPaginated as $mouvement)
        <div class="decision-row">
            <div>
                <div style="font-weight:600;font-size:14px;color:var(--theme-text);">
                    {{ $mouvement->agent?->nom_complet ?? '—' }}
                    <span style="font-size:12px;color:#9CA3AF;font-weight:400;margin-left:8px;">{{ $mouvement->agent?->matricule }}</span>
                </div>
                <div style="font-size:13px;color:#6B7280;margin-top:3px;">
                    <span style="font-weight:600;color:#059669;">{{ $mouvement->type_mouvement }}</span>
                    @if($mouvement->serviceDestination)
                    → {{ $mouvement->serviceDestination->nom_service }}
                    @endif
                    · Validé le {{ $mouvement->date_validation?->format('d/m/Y') ?? '—' }}
                    @if($mouvement->validateur)
                    <span style="color:#9CA3AF;">par {{ $mouvement->validateur->name }}</span>
                    @endif
                </div>
                @if($mouvement->motif)
                <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">{{ Str::limit($mouvement->motif, 90) }}</div>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('rh.docs-admin.decision', $mouvement->id_mouvement) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;font-size:13px;font-weight:500;text-decoration:none;">
                    <i class="fas fa-file-alt"></i>Aperçu
                </a>
                <form action="{{ route('drh.validations.signer', $mouvement->id_mouvement) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Signer la décision pour {{ $mouvement->agent?->nom_complet }} ?\n\nUne référence officielle sera générée et le mouvement sera effectué dans le système.')">
                    @csrf
                    <button type="submit" style="display:inline-flex;align-items:center;gap:8px;padding:8px 18px;border-radius:8px;background:#059669;color:#fff;border:none;font-size:13px;font-weight:600;cursor:pointer;">
                        <i class="fas fa-signature"></i>Signer
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:32px 20px;color:#9CA3AF;">
            <i class="fas fa-pen-alt" style="font-size:32px;color:#D1D5DB;display:block;margin-bottom:10px;"></i>
            Aucune décision en attente de signature.
            @if($stats['a_valider'] > 0)
            <div style="font-size:13px;margin-top:6px;">Validez d'abord les mouvements ci-dessus.</div>
            @endif
        </div>
        @endforelse

        @if($aSignerPaginated->hasPages())
        <div style="padding:12px 24px;border-top:1px solid #F3F4F6;">{{ $aSignerPaginated->links() }}</div>
        @endif
    </div>

    {{-- ─── HISTORIQUE ─────────────────────────────────────────── --}}
    <div class="section-card">
        <div class="section-header">
            <div style="font-weight:600;color:var(--theme-text);font-size:15px;">
                <i class="fas fa-history me-2" style="color:#6B7280;"></i>Décisions signées — historique récent
            </div>
        </div>
        @forelse($mouvementsEffectues as $m)
        <div class="decision-row">
            <div>
                <div style="font-weight:600;font-size:13px;color:var(--theme-text);">{{ $m->agent?->nom_complet ?? '—' }}</div>
                <div style="font-size:12px;color:#6B7280;">
                    {{ $m->type_mouvement }}
                    · <span style="font-weight:700;color:#1565C0;">{{ $m->decision_generee }}</span>
                    @if($m->date_signature)
                    · {{ $m->date_signature->format('d/m/Y à H:i') }}
                    @if($m->signataire) par <strong>{{ $m->signataire->name }}</strong>@endif
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('rh.docs-admin.decision', $m->id_mouvement) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;font-size:12px;font-weight:500;text-decoration:none;">
                    <i class="fas fa-download"></i>PDF
                </a>
                <span class="badge-d" style="background:#D1FAE5;color:#065F46;">Signée</span>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:28px 20px;color:#9CA3AF;font-size:13px;">
            <i class="fas fa-history" style="font-size:28px;color:#D1D5DB;display:block;margin-bottom:8px;"></i>
            Aucune décision dans l'historique.
        </div>
        @endforelse
    </div>

</div>

{{-- Modal rejet --}}
<div class="modal fade" id="modal-rejet" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #F3F4F6;padding:20px 24px;">
                <h5 class="modal-title fw-bold" style="color:var(--theme-text);">Rejeter le mouvement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-rejet" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px 24px;">
                    <label class="form-label fw-500" style="font-size:13.5px;">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea name="motif_rejet" class="form-control" rows="3" required minlength="10"
                              placeholder="Expliquer la raison du rejet…" style="border-radius:8px;font-size:13.5px;"></textarea>
                </div>
                <div class="modal-footer" style="border-top:1px solid #F3F4F6;padding:16px 24px;gap:8px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times me-1"></i>Confirmer le rejet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rejeterMouvement(id) {
    document.getElementById('form-rejet').action = '/drh/validations/mouvements/' + id + '/rejeter';
    new bootstrap.Modal(document.getElementById('modal-rejet')).show();
}
</script>
@endpush
