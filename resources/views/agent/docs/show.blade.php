@extends('layouts.master')
@section('title', 'Demande #' . $demande->id)
@section('page-title', 'Détail demande')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.docs.index') }}" style="color:#1565C0;">Mes documents</a></li>
    <li>Demande #{{ $demande->id }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
            <i class="fas fa-file-alt me-2" style="color:#0A4D8C;"></i>{{ $demande->libelleType }}
        </h4>
        <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('info'))<div class="alert alert-info alert-dismissible fade show" style="border-radius:10px;"><i class="fas fa-info-circle me-2"></i>{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    Détails
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Type</div>
                        <div style="font-size:14px;font-weight:600;color:var(--theme-text);">{{ $demande->libelleType }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Soumise le</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ $demande->created_at?->format('d/m/Y à H:i') }}</div>
                    </div>
                    @if($demande->motif)
                    <div class="col-12">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Motif</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ $demande->motif }}</div>
                    </div>
                    @endif
                    @if($demande->motif_rejet)
                    <div class="col-12">
                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:12px;">
                            <div style="font-size:11px;font-weight:700;color:#991B1B;margin-bottom:4px;">Motif de rejet</div>
                            <div style="font-size:13px;color:#991B1B;">{{ $demande->motif_rejet }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">Statut</div>
                @php
                    $statusConfig = [
                        'en_attente' => ['bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'fa-clock','msg'=>'Votre demande est en attente de traitement par le service RH.'],
                        'en_cours'   => ['bg'=>'#DBEAFE','color'=>'#1E40AF','icon'=>'fa-spinner','msg'=>'Votre demande est en cours de traitement.'],
                        'pret'       => ['bg'=>'#D1FAE5','color'=>'#065F46','icon'=>'fa-check-circle','msg'=>'Votre document est prêt ! Vous pouvez le télécharger.'],
                        'rejete'     => ['bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'fa-times-circle','msg'=>'Votre demande a été rejetée.'],
                    ];
                    $sc = $statusConfig[$demande->statut] ?? $statusConfig['en_attente'];
                @endphp
                <div style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:12px 16px;border-radius:8px;text-align:center;font-weight:700;font-size:14px;margin-bottom:16px;">
                    <i class="fas {{ $sc['icon'] }} me-2"></i>{{ $demande->libelleStatut }}
                </div>
                <p style="font-size:12px;color:#6B7280;text-align:center;">{{ $sc['msg'] }}</p>

                @if($demande->statut === 'pret')
                <a href="{{ route('agent.docs.download', $demande->id) }}" class="btn btn-success w-100 btn-sm">
                    <i class="fas fa-download me-1"></i>Accéder au document
                </a>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
