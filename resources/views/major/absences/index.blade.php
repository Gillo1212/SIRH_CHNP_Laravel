@extends('layouts.master')

@section('title', 'Absences équipe — Major')
@section('page-title', 'Absences de mon équipe')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li>Absences</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

@if(session('success'))
    <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
@endif

{{-- Stats mois --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4" style="color:#0A4D8C;">{{ $statsMois['total'] }}</div>
            <div class="text-muted" style="font-size:12px;">Absences ce mois</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-success">{{ $statsMois['justifiees'] }}</div>
            <div class="text-muted" style="font-size:12px;">Justifiées</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-warning">{{ $statsMois['maladie'] }}</div>
            <div class="text-muted" style="font-size:12px;">Maladie</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0"><i class="fas fa-user-minus me-2 text-danger"></i>Absences — {{ $service->nom_service }}</h6>
        <a href="{{ route('major.absences.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Enregistrer une absence
        </a>
    </div>

    {{-- Filtres --}}
    <div class="card-body border-bottom pb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="agent" class="form-select form-select-sm">
                    <option value="">Tous les agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id_agent }}" {{ request('agent') == $agent->id_agent ? 'selected' : '' }}>
                            {{ $agent->nom_complet }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous types</option>
                    @foreach(['Maladie','Personnelle','Professionnelle','Injustifiée'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="mois" class="form-select form-select-sm">
                    <option value="">Tous mois</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="annee" class="form-control form-control-sm" placeholder="Année" value="{{ request('annee') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filtrer</button>
                <a href="{{ route('major.absences.index') }}" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-4" style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Agent</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Date</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Type</th>
                        <th style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6b7280;">Justifiée</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $absence)
                    <tr>
                        <td class="ps-4 fw-600">{{ $absence->demande->agent->nom_complet ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $tc = match($absence->type_absence) {
                                    'Maladie'        => 'danger',
                                    'Personnelle'    => 'info',
                                    'Professionnelle'=> 'primary',
                                    'Injustifiée'    => 'warning',
                                    default          => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $tc }}" style="font-size:11px;">{{ $absence->type_absence }}</span>
                        </td>
                        <td>
                            @if($absence->justifie)
                                <span class="badge bg-success" style="font-size:11px;">Oui</span>
                            @else
                                <span class="badge bg-secondary" style="font-size:11px;">Non</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            Aucune absence enregistrée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absences->hasPages())
            <div class="px-4 py-3 border-top">{{ $absences->links() }}</div>
        @endif
    </div>
</div>

</div>
@endsection
