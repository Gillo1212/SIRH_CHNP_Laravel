@extends('layouts.master')
@section('title', 'Mes demandes de documents administratifs')
@section('page-title', 'Mes documents administratifs')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li>Documents administratifs</li>
@endsection

@push('styles')
<style>
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.status-en_attente { background:#FEF3C7; color:#92400E; }
.status-en_cours   { background:#DBEAFE; color:#1E40AF; }
.status-pret       { background:#D1FAE5; color:#065F46; }
.status-rejete     { background:#FEE2E2; color:#991B1B; }

.kpi-mini { border-radius: 12px; border: 1px solid #E5E7EB; background: #fff; padding: 16px 20px; }
.kpi-mini .kpi-val { font-size: 28px; font-weight: 700; line-height: 1; margin-bottom: 2px; }
.kpi-mini .kpi-lbl { font-size: 12px; color: #6B7280; }

.doc-row { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-bottom: 1px solid #F3F4F6; transition: background 120ms; }
.doc-row:last-child { border-bottom: none; }
.doc-row:hover { background: #F9FAFB; }
.doc-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-alt me-2" style="color:#0A4D8C;"></i>Mes demandes de documents
            </h4>
            <p class="mb-0 text-muted" style="font-size:13px;">Attestations, certificats, ordres de mission et plus</p>
        </div>
        <a href="{{ route('agent.docs.create') }}" class="btn btn-primary" style="border-radius:8px;">
            <i class="fas fa-plus me-1"></i>Nouvelle demande
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-mini">
                <div class="kpi-val" style="color:#1D4ED8;">{{ $stats['total'] }}</div>
                <div class="kpi-lbl">Total demandes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini">
                <div class="kpi-val" style="color:#D97706;">{{ $stats['en_attente'] }}</div>
                <div class="kpi-lbl">En attente</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini">
                <div class="kpi-val" style="color:#2563EB;">{{ $stats['en_cours'] }}</div>
                <div class="kpi-lbl">En cours</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini">
                <div class="kpi-val" style="color:#059669;">{{ $stats['pret'] }}</div>
                <div class="kpi-lbl">Prêts à télécharger</div>
            </div>
        </div>
    </div>

    {{-- Filtre statut + bannière info --}}
    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('agent.docs.index') }}"
               class="btn btn-sm {{ !request('statut') ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="border-radius:20px;">Tous</a>
            @foreach(['en_attente' => 'En attente', 'en_cours' => 'En cours', 'pret' => 'Prêts', 'rejete' => 'Annulés'] as $val => $lbl)
            <a href="{{ route('agent.docs.index', ['statut' => $val]) }}"
               class="btn btn-sm {{ request('statut') === $val ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="border-radius:20px;">{{ $lbl }}</a>
            @endforeach
        </div>
        @if($stats['pret'] > 0)
        <span class="badge bg-success ms-auto" style="font-size:12px; padding:6px 12px;">
            <i class="fas fa-download me-1"></i>{{ $stats['pret'] }} document(s) prêt(s) à télécharger
        </span>
        @endif
    </div>

    {{-- Liste des demandes --}}
    @if($demandes->isEmpty())
    <div class="text-center py-5" style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;">
        <i class="fas fa-file-alt fa-3x mb-3" style="color:#D1D5DB;"></i>
        <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">
            @if(request('statut')) Aucune demande dans cette catégorie
            @else Vous n'avez pas encore soumis de demande
            @endif
        </div>
        <a href="{{ route('agent.docs.create') }}" class="btn btn-primary btn-sm mt-3">
            <i class="fas fa-plus me-1"></i>Faire une demande
        </a>
    </div>
    @else
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;">
        @foreach($demandes as $dem)
        @php
            $icons = [
                'attestation_travail' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'icon' => 'fa-file-contract'],
                'certificat_travail'  => ['bg' => '#F0FDF4', 'color' => '#059669', 'icon' => 'fa-certificate'],
                'ordre_mission'       => ['bg' => '#FEF3C7', 'color' => '#D97706', 'icon' => 'fa-route'],
                'decision_conge_administratif' => ['bg' => '#FDF4FF', 'color' => '#9333EA', 'icon' => 'fa-calendar-check'],
                'attestation_jouissance_conge' => ['bg' => '#FDF4FF', 'color' => '#9333EA', 'icon' => 'fa-calendar'],
                'attestation_cessation_maternite' => ['bg' => '#FFF1F2', 'color' => '#E11D48', 'icon' => 'fa-heart'],
                'note_affectation'    => ['bg' => '#F0F9FF', 'color' => '#0284C7', 'icon' => 'fa-exchange-alt'],
                'note_interim'        => ['bg' => '#F0F9FF', 'color' => '#0284C7', 'icon' => 'fa-user-clock'],
                'autorisation_sortie_territoire' => ['bg' => '#FFF7ED', 'color' => '#EA580C', 'icon' => 'fa-plane'],
                'attestation_prime_motivation'  => ['bg' => '#F0FDF4', 'color' => '#059669', 'icon' => 'fa-award'],
                'attestation_prise_service'     => ['bg' => '#F8FAFF', 'color' => '#4F46E5', 'icon' => 'fa-user-check'],
                'attestation_stage'             => ['bg' => '#FFFBEB', 'color' => '#B45309', 'icon' => 'fa-graduation-cap'],
            ];
            $ic = $icons[$dem->type_document] ?? ['bg' => '#F3F4F6', 'color' => '#6B7280', 'icon' => 'fa-file'];
        @endphp
        <div class="doc-row">
            <div class="doc-icon" style="background:{{ $ic['bg'] }};">
                <i class="fas {{ $ic['icon'] }}" style="color:{{ $ic['color'] }};font-size:16px;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:600;font-size:14px;color:var(--theme-text);">{{ $dem->libelleType }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">
                    Soumise le {{ $dem->created_at?->format('d/m/Y') }}
                    @if($dem->motif) · {{ Str::limit($dem->motif, 40) }}@endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="status-badge status-{{ $dem->statut }}">
                    @if($dem->statut === 'en_attente')<i class="fas fa-clock"></i>
                    @elseif($dem->statut === 'en_cours')<i class="fas fa-spinner fa-spin"></i>
                    @elseif($dem->statut === 'pret')<i class="fas fa-check-circle"></i>
                    @else<i class="fas fa-times-circle"></i>
                    @endif
                    {{ $dem->libelleStatut }}
                </span>
                <a href="{{ route('agent.docs.show', $dem->id) }}"
                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#EFF6FF;color:#1D4ED8;text-decoration:none;"
                   title="Voir le détail">
                    <i class="fas fa-chevron-right" style="font-size:11px;"></i>
                </a>
                @if($dem->statut === 'pret')
                <a href="{{ route('agent.docs.download', $dem->id) }}"
                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#D1FAE5;color:#065F46;text-decoration:none;"
                   title="Accéder au document">
                    <i class="fas fa-download" style="font-size:11px;"></i>
                </a>
                @endif
            </div>
        </div>
        @endforeach
        @if($demandes->hasPages())
        <div class="px-4 py-3 border-top">{{ $demandes->links() }}</div>
        @endif
    </div>
    @endif

</div>
@endsection
