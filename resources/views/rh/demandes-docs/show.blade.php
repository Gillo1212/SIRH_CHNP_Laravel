@extends('layouts.master')
@section('title', 'Demande #' . $demande->id . ' — ' . $demande->libelleType)
@section('page-title', 'Détail demande document')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.demandes-docs.index') }}" style="color:#1565C0;">Demandes documents</a></li>
    <li>#{{ $demande->id }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-alt me-2" style="color:#0A4D8C;"></i>Demande #{{ $demande->id }} — {{ $demande->libelleType }}
            </h4>
        </div>
        <a href="{{ route('rh.demandes-docs.index') }}" class="btn btn-outline-secondary btn-sm">
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
                    Informations de la demande
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Agent</div>
                        <div style="font-size:14px;font-weight:600;color:var(--theme-text);">{{ $demande->agent?->nom_complet ?? '—' }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $demande->agent?->matricule }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Service</div>
                        <div style="font-size:13px;color:var(--theme-text);">{{ $demande->agent?->service?->nom_service ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Type de document</div>
                        <div style="font-size:14px;color:var(--theme-text);font-weight:500;">{{ $demande->libelleType }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:3px;">Date de demande</div>
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
                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:12px 16px;">
                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#991B1B;margin-bottom:4px;">Motif de rejet</div>
                            <div style="font-size:13px;color:#991B1B;">{{ $demande->motif_rejet }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Générer le document si prêt --}}
            @if($demande->statut === 'pret')
            <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:12px;padding:20px 24px;">
                <div style="font-weight:600;color:#065F46;margin-bottom:12px;"><i class="fas fa-check-circle me-2"></i>Document prêt à être généré</div>
                <div class="d-flex gap-2 flex-wrap">
                    @php $agent = $demande->agent; @endphp
                    @if($demande->type_document === 'attestation_travail')
                    <a href="{{ route('documents-admin.attestation', $agent->id_agent) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print me-1"></i>Générer l'attestation de travail
                    </a>
                    @elseif($demande->type_document === 'certificat_travail')
                    <a href="{{ route('documents-admin.certificat', $agent->id_agent) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print me-1"></i>Générer le certificat de travail
                    </a>
                    @elseif($demande->type_document === 'ordre_mission')
                    <a href="{{ route('documents-admin.ordre-mission', $agent->id_agent) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print me-1"></i>Générer l'ordre de mission
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-12 col-lg-4">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">Statut</div>
                @php
                    $statusConfig = [
                        'en_attente' => ['bg'=>'#FEF3C7','color'=>'#92400E'],
                        'en_cours'   => ['bg'=>'#DBEAFE','color'=>'#1E40AF'],
                        'pret'       => ['bg'=>'#D1FAE5','color'=>'#065F46'],
                        'rejete'     => ['bg'=>'#FEE2E2','color'=>'#991B1B'],
                    ];
                    $sc = $statusConfig[$demande->statut] ?? $statusConfig['en_attente'];
                @endphp
                <div style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:12px 16px;border-radius:8px;text-align:center;font-weight:700;font-size:14px;margin-bottom:16px;">
                    {{ $demande->libelleStatut }}
                </div>

                @if($demande->traitePar)
                <div style="font-size:12px;color:#9CA3AF;margin-bottom:4px;">
                    Traité par : <span style="color:var(--theme-text);font-weight:500;">{{ $demande->traitePar->name ?? $demande->traitePar->login }}</span>
                </div>
                <div style="font-size:12px;color:#9CA3AF;">
                    Le {{ $demande->date_traitement?->format('d/m/Y') }}
                </div>
                @endif

                @if(in_array($demande->statut, ['en_attente', 'en_cours']))
                <div class="d-grid gap-2 mt-3">
                    <form action="{{ route('rh.demandes-docs.traiter', $demande->id) }}" method="POST"
                          onsubmit="return confirm('Marquer cette demande comme prête ?')">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-check me-1"></i>Marquer comme prête
                        </button>
                    </form>
                    <form action="{{ route('rh.demandes-docs.rejeter', $demande->id) }}" method="POST" class="mt-1"
                          onsubmit="return confirm('Rejeter cette demande ?')">
                        @csrf
                        <input type="text" name="motif_rejet" class="form-control form-control-sm mb-1" placeholder="Motif du rejet">
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
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
