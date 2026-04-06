@extends('layouts.master')

@section('title', 'Documents administratifs')
@section('page-title', 'Génération de documents administratifs')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Documents administratifs</li>
@endsection

@push('styles')
<style>
.search-card { background: white; border: 1px solid #E5E7EB; border-radius: 12px; margin-bottom: 24px; }
.search-card-header { padding: 14px 20px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 8px; }
.search-card-header i { color: #0A4D8C; }
.search-card-header span { font-size: 14px; font-weight: 600; color: #374151; }
.search-card-body { padding: 16px 20px; }

.kpi-card { border-radius: 12px; }
.kpi-icon { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 kpi-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#EFF6FF;">
                        <i class="fas fa-file-alt text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total générés</div>
                        <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 kpi-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#F0FDF4;">
                        <i class="fas fa-calendar-check text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Ce mois</div>
                        <div class="fw-bold fs-4">{{ $stats['ce_mois'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 kpi-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#F0F9FF;">
                        <i class="fas fa-calendar-week" style="color:#0284C7;"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Cette semaine</div>
                        <div class="fw-bold fs-4">{{ $stats['cette_semaine'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 kpi-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#FFFBEB;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">En attente</div>
                        <div class="fw-bold fs-4">{{ $stats['en_attente'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('documents-admin.historique') }}" class="btn btn-outline-primary">
            <i class="fas fa-history me-1"></i>Historique des documents
        </a>
        @if($stats['en_attente'] > 0)
        <a href="{{ route('demandes-docs.pending') }}" class="btn btn-warning">
            <i class="fas fa-inbox me-1"></i>
            Demandes en attente
            <span class="badge bg-white text-warning ms-1">{{ $stats['en_attente'] }}</span>
        </a>
        @endif
    </div>

    {{-- Recherche + filtres --}}
    <div class="search-card">
        <div class="search-card-header">
            <i class="fas fa-search"></i>
            <span>Rechercher un agent</span>
            @if(request()->hasAny(['search', 'service_id', 'fonction']))
                <a href="{{ route('documents-admin.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                    <i class="fas fa-times me-1"></i>Effacer les filtres
                </a>
            @endif
        </div>
        <div class="search-card-body">
            <form method="GET" action="{{ route('documents-admin.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted text-uppercase" style="font-size:11px; letter-spacing:.5px;">Nom, prénom ou matricule</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted small"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               value="{{ request('search') }}"
                               placeholder="Ex: Ibrahima Ba, CHNP-00008…"
                               autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase" style="font-size:11px; letter-spacing:.5px;">Service</label>
                    <select name="service_id" class="form-select">
                        <option value="">Tous les services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id_service }}"
                                {{ request('service_id') == $service->id_service ? 'selected' : '' }}>
                                {{ $service->nom_service }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase" style="font-size:11px; letter-spacing:.5px;">Fonction</label>
                    <input type="text" name="fonction" class="form-control"
                           value="{{ request('fonction') }}"
                           placeholder="Infirmier, Médecin…">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste des agents --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-users me-2 text-primary"></i>
                Sélectionner un agent pour générer un document
            </h5>
            <span class="badge bg-secondary">{{ $agents->total() }} agent(s)</span>
        </div>
        <div class="card-body p-0">
            @if($agents->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0">Aucun agent ne correspond à vos critères de recherche.</p>
                    @if(request()->hasAny(['search', 'service_id', 'fonction']))
                        <a href="{{ route('documents-admin.index') }}" class="btn btn-outline-secondary btn-sm mt-3">
                            <i class="fas fa-times me-1"></i>Effacer les filtres
                        </a>
                    @endif
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th>Fonction</th>
                            <th>Service</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                        <tr>
                            <td><code>{{ $agent->matricule }}</code></td>
                            <td>
                                <strong>{{ $agent->nom_complet }}</strong>
                            </td>
                            <td class="text-muted small">{{ $agent->fonction ?? '-' }}</td>
                            <td>
                                @if($agent->service)
                                    <span class="badge rounded-pill" style="background:#EFF6FF; color:#1D4ED8; font-size:11px; font-weight:500;">
                                        {{ $agent->service->nom_service }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('documents-admin.select-type', $agent->id_agent) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-medical me-1"></i>Générer un document
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $agents->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
