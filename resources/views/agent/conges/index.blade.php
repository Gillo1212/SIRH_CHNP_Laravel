@extends('layouts.master')

@section('title', 'Mes Congés')
@section('page-title', 'Mes Congés')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mes congés</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:26px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }

.badge-statut { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-en_attente { background:#FEF3C7;color:#92400E; }
.badge-valide     { background:#DBEAFE;color:#1E40AF; }
.badge-approuve   { background:#D1FAE5;color:#065F46; }
.badge-rejete     { background:#FEE2E2;color:#991B1B; }

.solde-card { border-radius:10px;padding:14px 16px;border-left:4px solid; }
.conge-row:hover { background:var(--sirh-primary-hover); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:10px;border-left:4px solid #10B981;">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:10px;border-left:4px solid #EF4444;">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Mes congés</h4>
            <p class="text-muted small mb-0">Gérez vos demandes de congé et consultez vos soldes</p>
        </div>
        <a href="{{ route('agent.conges.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="background:#0A4D8C;border:none;border-radius:8px;padding:9px 18px;">
            <i class="fas fa-plus"></i> Nouvelle demande
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#F59E0B;">{{ $stats['en_attente'] }}</div>
                        <div class="kpi-label">En attente</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#3B82F6;">{{ $stats['validees'] }}</div>
                        <div class="kpi-label">Validées Manager</div>
                    </div>
                    <div class="kpi-icon" style="background:#DBEAFE;color:#1D4ED8;"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#10B981;">{{ $stats['approuvees'] }}</div>
                        <div class="kpi-label">Approuvées</div>
                    </div>
                    <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#EF4444;">{{ $stats['rejetees'] }}</div>
                        <div class="kpi-label">Rejetées</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEE2E2;color:#DC2626;"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Soldes de congés --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header border-0 pb-0 pt-3 px-3" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-calendar-check me-2" style="color:#0A4D8C;"></i>
                        Mes soldes {{ date('Y') }}
                    </h6>
                </div>
                <div class="card-body p-3">
                    @forelse($soldes as $solde)
                        @php
                            $pct = $solde->solde_initial > 0
                                ? round(($solde->solde_restant / $solde->solde_initial) * 100)
                                : 0;
                            $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                        @endphp
                        <div class="solde-card mb-2" style="background:var(--theme-bg-secondary);border-left-color:{{ $color }};">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-600 small" style="color:var(--theme-text);">{{ $solde->typeConge->libelle ?? '—' }}</span>
                                <span class="fw-bold" style="color:{{ $color }};font-size:14px;">{{ $solde->solde_restant }}j</span>
                            </div>
                            <div class="progress" style="height:4px;border-radius:2px;">
                                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted" style="font-size:10px;">Pris : {{ $solde->solde_pris }}j</span>
                                <span class="text-muted" style="font-size:10px;">Initial : {{ $solde->solde_initial }}j</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-calendar-xmark fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            <small>Aucun solde disponible.<br>Contactez le service RH.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Historique des demandes --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header border-0 pb-0 pt-3 px-3" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-history me-2" style="color:#0A4D8C;"></i>
                        Historique de mes demandes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($demandes as $demande)
                        @php
                            $conge = $demande->conge;
                            $statut = $demande->statut_demande;
                            $badgeClass = match($statut) {
                                'En_attente' => 'badge-en_attente',
                                'Validé'     => 'badge-valide',
                                'Approuvé'   => 'badge-approuve',
                                'Rejeté'     => 'badge-rejete',
                                default      => 'badge-en_attente',
                            };
                            $icon = match($statut) {
                                'En_attente' => 'fa-clock',
                                'Validé'     => 'fa-user-check',
                                'Approuvé'   => 'fa-check-double',
                                'Rejeté'     => 'fa-times',
                                default      => 'fa-clock',
                            };
                        @endphp
                        <div class="conge-row d-flex align-items-center px-3 py-3 border-bottom" style="transition:background 150ms;">
                            <div class="me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:var(--theme-bg-secondary);">
                                    <i class="fas {{ $icon }} small" style="color:#0A4D8C;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-600 small" style="color:var(--theme-text);">
                                        {{ $conge->typeConge->libelle ?? 'Type inconnu' }}
                                    </span>
                                    <span class="badge-statut {{ $badgeClass }}">
                                        <i class="fas {{ $icon }}" style="font-size:9px;"></i>
                                        {{ str_replace('_', ' ', $statut) }}
                                    </span>
                                </div>
                                @if($conge)
                                    <div class="text-muted" style="font-size:12px;">
                                        Du {{ $conge->date_debut->format('d/m/Y') }} au {{ $conge->date_fin->format('d/m/Y') }}
                                        — <strong>{{ $conge->nbres_jours }} jour(s)</strong>
                                    </div>
                                @endif
                                <div class="text-muted" style="font-size:11px;">
                                    Demandé le {{ $demande->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                            <div class="ms-2">
                                <a href="{{ route('agent.conges.show', $demande->id_demande) }}" class="btn btn-sm" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:6px;font-size:11px;color:var(--theme-text);">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-umbrella-beach fa-3x mb-3 d-block" style="opacity:.2;color:#0A4D8C;"></i>
                            <p class="mb-1 fw-500">Aucune demande de congé</p>
                            <small>Soumettez votre première demande en cliquant sur "Nouvelle demande"</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
