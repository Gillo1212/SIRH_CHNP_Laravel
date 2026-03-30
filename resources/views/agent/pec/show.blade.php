@extends('layouts.master')
@section('title', 'Prise en charge #' . $pec->id_priseenche)
@section('page-title', 'Détail prise en charge')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.pec.index') }}" style="color:#1565C0;">Mes PEC</a></li>
    <li>#{{ $pec->id_priseenche }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
            <i class="fas fa-hospital me-2" style="color:#0A4D8C;"></i>Prise en charge — {{ $pec->type_prise ?? 'Médicale' }}
        </h4>
        <a href="{{ route('agent.pec.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('info'))<div class="alert alert-info alert-dismissible fade show" style="border-radius:10px;"><i class="fas fa-info-circle me-2"></i>{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    Détails de la demande
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Type de soin</div>
                        <div style="font-size:14px;font-weight:600;color:var(--theme-text);">{{ $pec->type_prise ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Bénéficiaire</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ ucfirst($pec->ayant_droit ?? '—') }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Date de demande</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ $pec->created_at?->format('d/m/Y') }}</div>
                    </div>
                    @if($pec->exceptionnelle)
                    <div class="col-12">
                        <span style="background:#FEF3C7;color:#92400E;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                            <i class="fas fa-star me-1"></i>PEC Exceptionnelle
                        </span>
                    </div>
                    @endif
                    <div class="col-12">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Raison médicale</div>
                        <div style="font-size:13px;color:var(--theme-text);background:#F9FAFB;border-radius:8px;padding:12px;">{{ $pec->raison_medical ?? '—' }}</div>
                    </div>
                    @if($pec->demande?->motif_refus)
                    <div class="col-12">
                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;font-weight:700;color:#991B1B;margin-bottom:4px;">Motif de rejet</div>
                            <div style="font-size:13px;color:#991B1B;">{{ $pec->demande->motif_refus }}</div>
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
                    $statut = $pec->demande?->statut_demande ?? 'En_attente';
                    $statusConfig = [
                        'En_attente' => ['bg'=>'#FEF3C7','color'=>'#92400E','label'=>'En attente','icon'=>'fa-clock','msg'=>'Votre demande est en attente d\'examen par le service RH.'],
                        'Validé'     => ['bg'=>'#D1FAE5','color'=>'#065F46','label'=>'Validée','icon'=>'fa-check-circle','msg'=>'Votre demande a été validée par le service RH.'],
                        'Approuvé'   => ['bg'=>'#DBEAFE','color'=>'#1E40AF','label'=>'Approuvée par DRH','icon'=>'fa-stamp','msg'=>'Votre prise en charge a été approuvée par le DRH.'],
                        'Rejeté'     => ['bg'=>'#FEE2E2','color'=>'#991B1B','label'=>'Rejetée','icon'=>'fa-times-circle','msg'=>'Votre demande a été rejetée.'],
                    ];
                    $sc = $statusConfig[$statut] ?? $statusConfig['En_attente'];
                @endphp
                <div style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:12px 16px;border-radius:8px;text-align:center;font-weight:700;font-size:14px;margin-bottom:12px;">
                    <i class="fas {{ $sc['icon'] }} me-2"></i>{{ $sc['label'] }}
                </div>
                <p style="font-size:12px;color:#6B7280;text-align:center;margin-bottom:0;">{{ $sc['msg'] }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
