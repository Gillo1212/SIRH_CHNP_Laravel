@extends('layouts.master')

@section('title', 'Soldes de Congés')
@section('page-title', 'Soldes de Congés')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>Soldes</li>
@endsection

@push('styles')
<style>
.solde-bar { height:6px;border-radius:3px;background:var(--theme-bg-secondary);overflow:hidden; }
.solde-bar-fill { height:100%;border-radius:3px;transition:width .5s ease; }
.agent-row td { padding:14px 16px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.agent-row:hover td { background:var(--sirh-primary-hover); }
thead th { padding:10px 16px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" style="border-radius:10px;border-left:4px solid #10B981;">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-4" style="border-radius:10px;border-left:4px solid #EF4444;">
            <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Soldes de congés</h4>
            <p class="text-muted small mb-0">Consultez et gérez les soldes de congés par agent</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            {{-- Sélection année --}}
            <form method="GET" action="{{ route('rh.conges.soldes') }}" class="d-flex gap-2 align-items-center">
                <select name="annee" class="form-select form-select-sm" style="border-radius:8px;width:100px;" onchange="this.form.submit()">
                    @foreach($annees as $a)
                        <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" class="btn btn-sm d-flex align-items-center gap-2" style="background:#0A4D8C;color:#fff;border:none;border-radius:8px;padding:8px 14px;"
                data-bs-toggle="modal" data-bs-target="#initSoldesModal">
                <i class="fas fa-plus"></i> Initialiser soldes
            </button>
        </div>
    </div>

    {{-- Tableau soldes --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($soldes->count() > 0)
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:separate;border-spacing:0;">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Service</th>
                                @foreach($typesConge->where('deductible', true) as $type)
                                    <th class="text-center" style="min-width:130px;">{{ $type->libelle }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                                @php $agentSoldes = $soldes->get($agent->id_agent, collect()); @endphp
                                @if($agentSoldes->count() > 0)
                                    <tr class="agent-row">
                                        <td>
                                            <div class="fw-600" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $agent->service->nom_service ?? '—' }}</span>
                                        </td>
                                        @foreach($typesConge->where('deductible', true) as $type)
                                            @php
                                                $s = $agentSoldes->firstWhere('id_type_conge', $type->id_type_conge);
                                                $pct = $s && $s->solde_initial > 0
                                                    ? round(($s->solde_restant / $s->solde_initial) * 100)
                                                    : 0;
                                                $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                                            @endphp
                                            <td class="text-center">
                                                @if($s)
                                                    <div class="fw-bold" style="font-size:16px;color:{{ $color }};">{{ $s->solde_restant }}j</div>
                                                    <div class="solde-bar mx-auto my-1" style="width:80px;">
                                                        <div class="solde-bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                                                    </div>
                                                    <div class="text-muted" style="font-size:10px;">{{ $s->solde_pris }}j pris / {{ $s->solde_initial }}j</div>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-database fa-3x mb-3 d-block" style="color:#0A4D8C;opacity:.2;"></i>
                    <p class="text-muted mb-2">Aucun solde initialisé pour {{ $annee }}.</p>
                    <button type="button" class="btn btn-sm" style="background:#0A4D8C;color:#fff;border:none;border-radius:8px;"
                        data-bs-toggle="modal" data-bs-target="#initSoldesModal">
                        <i class="fas fa-plus me-1"></i>Initialiser les soldes
                    </button>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal Initialiser soldes --}}
<div class="modal fade" id="initSoldesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-calendar-plus me-2" style="color:#0A4D8C;"></i>Initialiser les soldes de congés
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.conges.soldes.init') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <p class="text-muted small mb-3">
                        Initialise les soldes de congés déductibles pour un agent selon les droits définis par type.
                        Si un solde existe déjà, il ne sera pas écrasé.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Agent <span class="text-danger">*</span></label>
                        <select name="id_agent" class="form-select" style="border-radius:8px;font-size:13px;" required>
                            <option value="">-- Sélectionner un agent --</option>
                            @foreach($agents as $a)
                                <option value="{{ $a->id_agent }}">{{ $a->nom_complet }} ({{ $a->matricule }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Année <span class="text-danger">*</span></label>
                        <select name="annee" class="form-select" style="border-radius:8px;font-size:13px;" required>
                            @foreach($annees as $a)
                                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert" style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;font-size:12px;">
                        <i class="fas fa-info-circle me-1" style="color:#3B82F6;"></i>
                        <span style="color:#1E40AF;">
                            Les types de congés déductibles suivants seront initialisés :
                            @foreach($typesConge->where('deductible', true) as $t)
                                <strong>{{ $t->libelle }}</strong> ({{ $t->nb_jours_droit }}j){{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm" style="background:#0A4D8C;color:#fff;border:none;border-radius:8px;">
                        <i class="fas fa-check me-1"></i>Initialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
