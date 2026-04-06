@extends('layouts.master')
@section('title', 'Agents actuellement en congé')
@section('page-title', 'Congés en cours')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>En congé actuellement</li>
@endsection

@push('styles')
<style>
.agent-row td { padding:14px 16px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.agent-row:hover td { background:var(--sirh-primary-hover); }
thead th { padding:10px 16px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
.solde-bar { height:5px;border-radius:3px;background:var(--theme-bg-secondary);overflow:hidden; }
.solde-bar-fill { height:100%;border-radius:3px;transition:width .5s ease; }
.retour-ok      { background:#D1FAE5;color:#065F46; }
.retour-today   { background:#DBEAFE;color:#1E40AF; }
.retour-depasse { background:#FEE2E2;color:#991B1B; }
.retour-badge   { padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">
                <i class="fas fa-umbrella-beach me-2" style="color:#F59E0B;"></i>Agents actuellement en congé
            </h4>
            <p class="text-muted small mb-0">Situation en temps réel au {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($agentsEnConge->count() > 0)
                <span style="background:#FEF3C7;color:#92400E;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;">
                    <i class="fas fa-user-clock me-1"></i>{{ $agentsEnConge->count() }} absent(s)
                </span>
            @endif
        </div>
    </div>

    {{-- Navigation --}}
    @include('rh.conges._nav', [
        'active'      => 'en-cours',
        'pendingCount' => \App\Models\Demande::where('type_demande','Conge')->where('statut_demande','Validé')->count(),
        'enCoursCount' => $agentsEnConge->count(),
    ])

    {{-- Filtre service --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4" style="border:1px solid var(--theme-border);">
        <form method="GET" action="{{ route('rh.conges.en-cours') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="service" class="form-select" style="width:auto;min-width:220px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ $serviceId == $svc->id_service ? 'selected' : '' }}>
                            {{ $svc->nom_service }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter me-1"></i>Filtrer
                </button>
                @if($serviceId)
                    <a href="{{ route('rh.conges.en-cours') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($agentsEnConge->isEmpty())
        {{-- État vide --}}
        <div class="text-center py-5" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;">
            <i class="fas fa-check-circle fa-3x mb-3" style="color:#10B981;"></i>
            <h5 style="color:var(--theme-text);">Aucun agent en congé en ce moment</h5>
            <p class="text-muted small">Tous les agents sont présents au service.</p>
        </div>
    @else
        {{-- Alertes retours dépassés --}}
        @php $depasses = $agentsEnConge->filter(fn($a) => $a->conge_en_cours?->conge && now()->isAfter($a->conge_en_cours->conge->date_fin)); @endphp
        @if($depasses->isNotEmpty())
            <div class="d-flex align-items-start gap-2 mb-4" style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:14px 18px;">
                <i class="fas fa-exclamation-triangle mt-1" style="color:#DC2626;flex-shrink:0;"></i>
                <div style="font-size:13px;color:#991B1B;">
                    <strong>{{ $depasses->count() }} agent(s) dont la date de retour est dépassée.</strong>
                    Vérifiez leur situation et mettez à jour leur statut si nécessaire.
                </div>
            </div>
        @endif

        {{-- Tableau --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
            <div class="card-header border-0 px-4 pt-3 pb-2 d-flex align-items-center justify-content-between" style="background:#FFFBEB;border-bottom:1px solid #FDE68A;">
                <h6 class="fw-bold mb-0" style="color:#92400E;">
                    <i class="fas fa-umbrella-beach me-2" style="color:#F59E0B;"></i>
                    {{ $agentsEnConge->count() }} agent(s) en congé
                </h6>
                <span class="text-muted" style="font-size:12px;">Soldes au {{ now()->format('d/m/Y') }} - Année {{ $annee }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:separate;border-spacing:0;">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Service</th>
                                <th>Type de congé</th>
                                <th class="text-center">Période</th>
                                <th class="text-center">Durée</th>
                                <th class="text-center">Retour prévu</th>
                                @foreach($typesConge as $type)
                                    <th class="text-center" style="min-width:130px;">Reliquat<br><span style="font-weight:400;font-size:9px;">{{ $type->libelle }}</span></th>
                                @endforeach
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agentsEnConge as $agent)
                                @php
                                    $congeEnCours   = $agent->conge_en_cours;
                                    $conge          = $congeEnCours?->conge;
                                    $agentSoldes    = $agent->soldeConges->keyBy('id_type_conge');
                                    $joursAvantRetour = $conge
                                        ? (int) now()->startOfDay()->diffInDays($conge->date_fin->startOfDay(), false)
                                        : null;
                                    $initiales = strtoupper(substr($agent->prenom, 0, 1) . substr($agent->nom, 0, 1));
                                @endphp
                                <tr class="agent-row">
                                    {{-- Agent --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                style="width:36px;height:36px;border-radius:50%;background:#FEF3C7;color:#92400E;font-size:13px;">
                                                {{ $initiales }}
                                            </div>
                                            <div>
                                                <div class="fw-600" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                                <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Service --}}
                                    <td>
                                        <span class="text-muted small">{{ $agent->service->nom_service ?? '-' }}</span>
                                    </td>

                                    {{-- Type de congé --}}
                                    <td>
                                        <span class="small fw-600" style="color:var(--theme-text);">
                                            {{ $conge?->typeConge?->libelle ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Période --}}
                                    <td class="text-center">
                                        @if($conge)
                                            <div class="small" style="color:var(--theme-text);">
                                                {{ $conge->date_debut?->format('d/m/Y') }}
                                            </div>
                                            <div style="font-size:10px;color:var(--theme-text-muted);">
                                                au {{ $conge->date_fin?->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Durée --}}
                                    <td class="text-center">
                                        <span class="fw-bold" style="color:#F59E0B;font-size:16px;">
                                            {{ $conge?->nbres_jours ?? '-' }}
                                        </span>
                                        @if($conge)
                                            <div style="font-size:10px;color:var(--theme-text-muted);">jour(s)</div>
                                        @endif
                                    </td>

                                    {{-- Retour prévu --}}
                                    <td class="text-center">
                                        @if($joursAvantRetour !== null)
                                            @if($joursAvantRetour > 0)
                                                <span class="retour-badge retour-ok">
                                                    <i class="fas fa-clock" style="font-size:9px;"></i>dans {{ $joursAvantRetour }} j.
                                                </span>
                                            @elseif($joursAvantRetour === 0)
                                                <span class="retour-badge retour-today">
                                                    <i class="fas fa-door-open" style="font-size:9px;"></i>Aujourd'hui
                                                </span>
                                            @else
                                                <span class="retour-badge retour-depasse">
                                                    <i class="fas fa-exclamation-triangle" style="font-size:9px;"></i>Dépassé de {{ abs($joursAvantRetour) }}j
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    {{-- Reliquats par type --}}
                                    @foreach($typesConge as $type)
                                        @php
                                            $s    = $agentSoldes->get($type->id_type_conge);
                                            $pct  = $s && $s->solde_initial > 0 ? round(($s->solde_restant / $s->solde_initial) * 100) : 0;
                                            $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                                        @endphp
                                        <td class="text-center">
                                            @if($s)
                                                <div class="fw-bold" style="font-size:15px;color:{{ $color }};">{{ $s->solde_restant }}j</div>
                                                <div class="solde-bar mx-auto my-1" style="width:60px;">
                                                    <div class="solde-bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                                                </div>
                                                <div style="font-size:10px;color:var(--theme-text-muted);">{{ $s->solde_pris }}j / {{ $s->solde_initial }}j</div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    {{-- Actions --}}
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if($congeEnCours)
                                                <a href="{{ route('rh.conges.show', $congeEnCours->id_demande) }}"
                                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#EFF6FF;border:1px solid #BFDBFE;color:#1E40AF;text-decoration:none;"
                                                   title="Voir la demande">
                                                    <i class="fas fa-eye" style="font-size:11px;"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('rh.agents.show', $agent->id_agent) }}"
                                               style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#F5F3FF;border:1px solid #DDD6FE;color:#5B21B6;text-decoration:none;"
                                               title="Dossier de l'agent">
                                                <i class="fas fa-user" style="font-size:11px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
