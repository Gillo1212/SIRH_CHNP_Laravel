@extends('layouts.master')
@section('title', 'Prise en charge #' . $prise->id_priseenche)
@section('page-title', 'Détail prise en charge')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('pec.index') }}" style="color:#1565C0;">Prises en charge</a></li>
    <li>PEC #{{ $prise->id_priseenche }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-hospital me-2" style="color:#0A4D8C;"></i>Prise en charge #{{ $prise->id_priseenche }}
            </h4>
        </div>
        <a href="{{ route('pec.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">
        {{-- Informations --}}
        <div class="col-12 col-lg-8">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    Informations
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Agent</div>
                        <div style="font-size:14px;font-weight:600;color:var(--theme-text);">{{ $prise->demande?->agent?->nom_complet ?? '-' }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $prise->demande?->agent?->matricule }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Bénéficiaire</div>
                        <div style="font-size:14px;font-weight:500;color:var(--theme-text);">{{ $prise->beneficiaireLibelle }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Type PEC</div>
                        <div style="font-size:14px;color:var(--theme-text);">{{ $prise->type_prise ?? '-' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Date de la demande</div>
                        <div style="font-size:14px;color:var(--theme-text);">
                            {{ $prise->demande?->created_at?->format('d/m/Y') ?? '-' }}
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Raison médicale</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ $prise->raison_medical ?? '-' }}</div>
                    </div>
                    @if($prise->ayant_droit === 'Conjoint')
                    <div class="col-12">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:6px;">Justificatif (certificat de mariage)</div>
                        @if($prise->justificatif_path)
                        <div class="d-flex align-items-center gap-2">
                            <span style="background:#D1FAE5;color:#065F46;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                                <i class="fas fa-paperclip me-1"></i>Certificat de mariage joint
                            </span>
                            <a href="{{ route('pec.justificatif', $prise->id_priseenche) }}" class="btn btn-outline-primary btn-sm" style="font-size:12px;">
                                <i class="fas fa-download me-1"></i>Consulter / Télécharger
                            </a>
                        </div>
                        @else
                        <span style="background:#FEE2E2;color:#991B1B;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                            <i class="fas fa-exclamation-circle me-1"></i>Aucun justificatif fourni
                        </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statut & Actions --}}
        <div class="col-12 col-lg-4">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">Statut</div>
                @php
                    $statut = $prise->demande?->statut_demande ?? 'En_attente';
                    $statusConfig = [
                        'En_attente' => ['bg'=>'#FEF3C7','color'=>'#92400E','label'=>'En attente'],
                        'Validé'     => ['bg'=>'#D1FAE5','color'=>'#065F46','label'=>'Validée RH'],
                        'Approuvé'   => ['bg'=>'#DBEAFE','color'=>'#1E40AF','label'=>'Approuvée DRH'],
                        'Rejeté'     => ['bg'=>'#FEE2E2','color'=>'#991B1B','label'=>'Rejetée'],
                    ];
                    $sc = $statusConfig[$statut] ?? $statusConfig['En_attente'];
                @endphp
                <div style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:12px 16px;border-radius:8px;text-align:center;font-weight:700;font-size:14px;margin-bottom:16px;">
                    {{ $sc['label'] }}
                </div>
                <div style="font-size:12px;color:#9CA3AF;">Enregistrée le {{ $prise->created_at?->format('d/m/Y') }}</div>

                @if($prise->demande?->statut_demande === 'Rejeté' && $prise->demande?->motif_refus)
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:10px 14px;margin-top:12px;font-size:12px;color:#991B1B;">
                    <strong>Motif :</strong> {{ $prise->demande->motif_refus }}
                </div>
                @endif

                @if($statut === 'En_attente')
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalValider">
                        <i class="fas fa-check me-1"></i>Valider la PEC
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalRejeter">
                        <i class="fas fa-times me-1"></i>Rejeter la PEC
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ===== MODAL VALIDATION ===== --}}
<div class="modal fade" id="modalValider" tabindex="-1" aria-labelledby="modalValiderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content" style="border-radius:14px;border:0;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#059669,#10B981);border-radius:14px 14px 0 0;padding:18px 24px;">
                <h5 class="modal-title fw-bold text-white" id="modalValiderLabel">
                    <i class="fas fa-check-circle me-2"></i>Confirmer la validation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;background:#D1FAE5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-hospital" style="font-size:22px;color:#059669;"></i>
                </div>
                <p style="font-size:14px;color:var(--theme-text);margin-bottom:4px;">
                    Vous êtes sur le point de <strong>valider</strong> la prise en charge
                </p>
                <p style="font-size:13px;color:#9CA3AF;">
                    PEC #{{ $prise->id_priseenche }} - {{ $prise->demande?->agent?->nom_complet }}
                </p>
                <p style="font-size:12px;color:#6B7280;margin-bottom:0;">
                    Cette action est définitive. La demande passera au statut <strong>Validée RH</strong>.
                </p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #F3F4F6;padding:16px 24px;gap:8px;">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <form action="{{ route('pec.update', $prise->id_priseenche) }}" method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="valider">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check me-1"></i>Oui, valider
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL REJET ===== --}}
<div class="modal fade" id="modalRejeter" tabindex="-1" aria-labelledby="modalRejeterLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:14px;border:0;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#DC2626,#EF4444);border-radius:14px 14px 0 0;padding:18px 24px;">
                <h5 class="modal-title fw-bold text-white" id="modalRejeterLabel">
                    <i class="fas fa-times-circle me-2"></i>Rejeter la prise en charge
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pec.update', $prise->id_priseenche) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="rejeter">
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3" style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:12px 14px;">
                        <i class="fas fa-exclamation-triangle" style="font-size:20px;color:#DC2626;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#991B1B;">PEC #{{ $prise->id_priseenche }}</div>
                            <div style="font-size:12px;color:#9CA3AF;">{{ $prise->demande?->agent?->nom_complet }}</div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">
                            Motif du rejet <span style="font-weight:400;text-transform:none;">(optionnel)</span>
                        </label>
                        <textarea name="motif_rejet" rows="3" class="form-control"
                            style="font-size:13px;"
                            placeholder="Préciser la raison du rejet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #F3F4F6;padding:16px 24px;gap:8px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times me-1"></i>Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
