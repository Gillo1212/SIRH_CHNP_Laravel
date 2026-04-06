@extends('layouts.master')

@section('title', 'Historique des documents générés')
@section('page-title', 'Historique des documents générés')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('documents-admin.index') }}" style="color:#1565C0;">Documents</a></li>
    <li>Historique</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Filtres --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Type de document</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Statut</label>
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="pret"   {{ request('statut') === 'pret'   ? 'selected' : '' }}>Prêt</option>
                        <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Agent</label>
                    <input type="text" name="agent" class="form-control form-control-sm"
                           value="{{ request('agent') }}" placeholder="Nom ou matricule">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Du</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-semibold">Au</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                    @if(request()->hasAny(['type', 'statut', 'agent', 'date_debut', 'date_fin']))
                        <a href="{{ route('documents-admin.historique') }}" class="btn btn-outline-secondary btn-sm" title="Effacer">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Documents générés</h5>
            <span class="badge bg-secondary">{{ $documents->total() }} document(s)</span>
        </div>
        <div class="card-body p-0">
            @if($documents->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                    <p>Aucun document généré.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Type de document</th>
                                <th>Agent</th>
                                <th>Service</th>
                                <th>Date génération</th>
                                <th>Généré par</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                            <tr class="{{ $doc->statut === 'rejete' ? 'table-danger' : '' }}">
                                <td>
                                    <code class="{{ $doc->statut === 'rejete' ? 'text-danger text-decoration-line-through' : 'text-primary' }}" style="font-size:11px;">
                                        {{ $doc->numero_reference ?? '-' }}
                                    </code>
                                    @if($doc->statut === 'rejete')
                                        <span class="badge bg-danger ms-1" style="font-size:10px;">Annulé</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border" style="font-size:11px;">
                                        {{ $doc->libelleType }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $doc->agent->nom_complet ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ $doc->agent->matricule ?? '' }}</small>
                                </td>
                                <td class="small text-muted">{{ $doc->agent->service->nom_service ?? '-' }}</td>
                                <td class="small">
                                    {{ $doc->date_traitement ? $doc->date_traitement->format('d/m/Y') : '-' }}<br>
                                    <span class="text-muted">{{ $doc->date_traitement ? $doc->date_traitement->format('H:i') : '' }}</span>
                                </td>
                                <td class="small text-muted">{{ $doc->traitePar->login ?? '-' }}</td>
                                <td class="text-center" style="white-space:nowrap;">
                                    {{-- Voir --}}
                                    <a href="{{ route('documents-admin.show-generated', $doc->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- Dupliquer --}}
                                    <a href="{{ route('documents-admin.duplicate', $doc->id) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Recréer un nouveau document">
                                        <i class="fas fa-copy"></i>
                                    </a>
                                    @if($doc->statut === 'pret')
                                        {{-- Modifier --}}
                                        <a href="{{ route('documents-admin.modifier', $doc->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier et regénérer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Annuler --}}
                                        <form action="{{ route('documents-admin.annuler', $doc->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Annuler le document {{ $doc->numero_reference }} ?\nCette action est irréversible.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler ce document">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
