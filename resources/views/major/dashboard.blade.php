@extends('layouts.master')

@section('title', 'Tableau de bord — Major')
@section('page-title', 'Gestion de mon service')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label { font-size:13px;margin-top:2px;font-weight:500; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:0.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.purple::before { background:#7C3AED; }
.badge-statut { padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

@if(session('error'))
    <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
@endif

@isset($noService)
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h5 class="fw-bold mb-2">Aucun service assigné</h5>
            <p class="text-muted">Vous n'êtes pas encore assigné à un service comme Major. Contactez le service RH.</p>
        </div>
    </div>
</div>
@else

{{-- En-tête service --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 100%);">
    <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;opacity:.75;">Votre service</div>
            <h3 class="fw-bold mb-1 mt-1">{{ $service->nom_service }}</h3>
            <span class="badge" style="background:rgba(255,255,255,.2);font-size:12px;">{{ $service->type_service }}</span>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('major.equipe') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);">
                <i class="fas fa-users me-1"></i> Mon équipe
            </a>
            <a href="{{ route('major.planning.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);">
                <i class="fas fa-calendar-week me-1"></i> Plannings
            </a>
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card blue border-0 shadow-sm">
            <div class="kpi-icon" style="background:#e8f0fe;color:#0A4D8C;"><i class="fas fa-users"></i></div>
            <div class="kpi-value" style="color:#0A4D8C;">{{ $totalAgents }}</div>
            <div class="kpi-label text-muted">Agents actifs</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card green border-0 shadow-sm">
            <div class="kpi-icon" style="background:#d1fae5;color:#059669;"><i class="fas fa-user-check"></i></div>
            <div class="kpi-value" style="color:#059669;">{{ $agentsPresents }}</div>
            <div class="kpi-label text-muted">Présents aujourd'hui</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card amber border-0 shadow-sm">
            <div class="kpi-icon" style="background:#fef3c7;color:#D97706;"><i class="fas fa-user-minus"></i></div>
            <div class="kpi-value" style="color:#D97706;">{{ $absencesMois }}</div>
            <div class="kpi-label text-muted">Absences ce mois</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card purple border-0 shadow-sm">
            <div class="kpi-icon" style="background:#ede9fe;color:#7C3AED;"><i class="fas fa-calendar-week"></i></div>
            <div class="kpi-value" style="color:#7C3AED;">{{ $planningsEnCours }}</div>
            <div class="kpi-label text-muted">Plannings en cours</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Planning semaine --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fas fa-calendar-week me-2" style="color:#0A4D8C;"></i>Planning cette semaine</h6>
                <a href="{{ route('major.planning.index') }}" class="btn btn-sm btn-outline-primary">Gérer</a>
            </div>
            <div class="card-body px-4 pb-4">
                @php
                    $jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
                @endphp
                @if($lignesSemaine->isEmpty())
                    <p class="text-muted text-center py-3"><i class="fas fa-calendar-times me-2"></i>Aucun planning validé cette semaine.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr>
                                @foreach($jours as $i => $jour)
                                    <th class="text-center" style="font-size:12px;font-weight:600;color:#6b7280;">{{ $jour }}</th>
                                @endforeach
                            </tr></thead>
                            <tbody><tr>
                                @for($i = 0; $i <= 6; $i++)
                                    <td class="align-top" style="min-width:90px;">
                                        @foreach($lignesSemaine->get($i, collect()) as $ligne)
                                            <div class="mb-1 p-1 rounded" style="background:#e8f0fe;font-size:11px;">
                                                <div class="fw-600">{{ $ligne->agent->prenom ?? '' }} {{ substr($ligne->agent->nom ?? '', 0, 1) }}.</div>
                                                <div class="text-muted">{{ $ligne->typePoste->libelle ?? '' }}</div>
                                            </div>
                                        @endforeach
                                    </td>
                                @endfor
                            </tr></tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Dernières absences --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fas fa-user-minus me-2 text-danger"></i>Absences récentes</h6>
                <a href="{{ route('major.absences.index') }}" class="btn btn-sm btn-outline-danger">Voir tout</a>
            </div>
            <div class="card-body px-4 pb-4">
                @forelse($dernieresAbsences as $absence)
                    <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#fee2e2;flex-shrink:0;">
                            <i class="fas fa-user-minus" style="font-size:13px;color:#dc2626;"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-600" style="font-size:13px;">{{ $absence->demande->agent->nom_complet ?? '—' }}</div>
                            <div class="text-muted" style="font-size:11px;">
                                {{ $absence->type_absence }} — {{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}
                            </div>
                        </div>
                        @if($absence->justifie)
                            <span class="badge bg-success" style="font-size:10px;">Justifiée</span>
                        @else
                            <span class="badge bg-warning text-dark" style="font-size:10px;">Non justifiée</span>
                        @endif
                    </div>
                @empty
                    <p class="text-muted text-center py-3" style="font-size:13px;">Aucune absence récente.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endisset
</div>
@endsection
