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
        <div class="col-12 col-lg-8">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    Informations
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Agent</div>
                        <div style="font-size:14px;font-weight:600;color:var(--theme-text);">{{ $prise->demande?->agent?->nom_complet ?? '—' }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $prise->demande?->agent?->matricule }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Bénéficiaire</div>
                        <div style="font-size:14px;font-weight:500;color:var(--theme-text);">{{ $prise->beneficiaireLibelle }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Type PEC</div>
                        <div style="font-size:14px;color:var(--theme-text);">{{ $prise->type_prise ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Date de la demande</div>
                        <div style="font-size:14px;color:var(--theme-text);">
                            {{ $prise->demande?->created_at?->format('d/m/Y') ?? '—' }}
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Raison médicale</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ $prise->raison_medical ?? '—' }}</div>
                    </div>
                    @if($prise->exceptionnelle)
                    <div class="col-12">
                        <span style="background:#FEF3C7;color:#92400E;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                            <i class="fas fa-star me-1"></i>PEC Exceptionnelle
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
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
                    <form action="{{ route('pec.update', $prise->id_priseenche) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="valider">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-check me-1"></i>Valider la PEC
                        </button>
                    </form>
                    <form action="{{ route('pec.update', $prise->id_priseenche) }}" method="POST" class="mt-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="rejeter">
                        <input type="text" name="motif_rejet" class="form-control form-control-sm mb-1" placeholder="Motif du rejet (optionnel)">
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-times me-1"></i>Rejeter
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
