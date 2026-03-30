@extends('layouts.master')

@section('title', 'Mon équipe — Major')
@section('page-title', 'Mon équipe')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li>Mon équipe</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

{{-- En-tête --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background:linear-gradient(135deg,#0A4D8C,#1565C0);">
    <div class="card-body p-4 text-white">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;opacity:.75;">Service</div>
        <h4 class="fw-bold mb-0 mt-1">{{ $service->nom_service }}</h4>
        <span class="badge mt-2" style="background:rgba(255,255,255,.2);">{{ $service->type_service }}</span>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4" style="color:#0A4D8C;">{{ $stats['total'] }}</div>
            <div class="text-muted" style="font-size:12px;">Total agents</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-success">{{ $stats['actifs'] }}</div>
            <div class="text-muted" style="font-size:12px;">Actifs</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-warning">{{ $stats['en_conge'] }}</div>
            <div class="text-muted" style="font-size:12px;">En congé</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-danger">{{ $stats['suspendus'] }}</div>
            <div class="text-muted" style="font-size:12px;">Suspendus</div>
        </div>
    </div>
</div>

{{-- Liste agents --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-users me-2" style="color:#0A4D8C;"></i>Liste des agents du service</h6>
        <small class="text-muted">Consultation uniquement</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-4" style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Agent</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Matricule</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Fonction</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Statut</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Absences (mois)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agents as $agent)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                @if($agent->photo)
                                    <img src="{{ asset('storage/'.$agent->photo) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#e8f0fe;font-size:13px;font-weight:700;color:#0A4D8C;">
                                        {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->nom, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-600">{{ $agent->nom_complet }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $agent->categorie_cp ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><code style="font-size:12px;">{{ $agent->matricule }}</code></td>
                        <td style="font-size:13px;">{{ $agent->fontion ?? '—' }}</td>
                        <td>
                            @php
                                $sc = match($agent->statut_agent) {
                                    'Actif'     => 'success',
                                    'En_congé'  => 'warning',
                                    'Suspendu'  => 'danger',
                                    'Retraité'  => 'secondary',
                                    default     => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $sc }}" style="font-size:11px;">{{ $agent->statut_agent }}</span>
                        </td>
                        <td>
                            @php $countAbsences = $agent->demandes->count(); @endphp
                            @if($countAbsences > 0)
                                <span class="badge bg-danger">{{ $countAbsences }}</span>
                            @else
                                <span class="text-muted" style="font-size:12px;">0</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-2x mb-3 d-block"></i>
                            Aucun agent dans ce service.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
@endsection
