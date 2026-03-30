@extends('layouts.master')
@section('title', 'GED — Dossiers Agents')
@section('page-title', 'Dossiers Agents')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.ged.index') }}" style="color:#1565C0;">GED</a></li>
    <li>Dossiers</li>
@endsection

@push('styles')
<style>
/* ── TOOLBAR ──────────────────────────────────────────────────── */
.ged-toolbar { background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
.ged-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.ged-btn-primary { background:#0A4D8C;color:#fff; }
.ged-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.ged-btn-outline { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.ged-btn-outline:hover { background:#F9FAFB;border-color:#D1D5DB; }

/* ── SEARCH BAR ───────────────────────────────────────────────── */
.ged-search { flex:1;min-width:220px;height:38px;border:1px solid #E5E7EB;border-radius:8px;padding:0 14px 0 38px;font-size:14px;background:#F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 12px center;outline:none; }
.ged-search:focus { border-color:#0A4D8C;background-color:#fff; }
.filter-select { height:38px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13.5px;background:#F9FAFB;outline:none;color:#374151; }
.filter-select:focus { border-color:#0A4D8C; }

/* ── SECTION SERVICE ─────────────────────────────────────────── */
.svc-section { margin-bottom: 40px; }
.svc-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    background: #F8FAFF;
    border: 1px solid #DBEAFE;
    border-radius: 10px;
    margin-bottom: 20px;
}
.svc-badge {
    min-width: 26px; height: 26px;
    border-radius: 50%;
    color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.svc-name { font-size: 13px; font-weight: 700; color: #1E3A5F; letter-spacing: .02em; text-transform: uppercase; }
.svc-count { font-size: 12px; color: #6B7280; margin-left: auto; }

/* ── CARTE DOSSIER PHYSIQUE ──────────────────────────────────── */
/* Col avec marge haute pour l'onglet saillant */
.dossier-col { padding-top: 26px; }

/* La carte elle-même */
.dossier-card {
    position: relative;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-left: 4px solid var(--svc-color, #0A4D8C);
    border-radius: 0 10px 10px 0;
    display: block;
    color: inherit;
    text-decoration: none;
    transition: box-shadow 200ms, border-color 200ms;
    height: 100%;
}
.dossier-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.10);
    border-color: var(--svc-color, #0A4D8C);
    text-decoration: none;
    color: inherit;
}
.dossier-card.no-dossier {
    border-left-style: solid;
    border-top-style: dashed;
    border-right-style: dashed;
    border-bottom-style: dashed;
    cursor: default;
    opacity: .75;
}

/* Onglet saillant avec les initiales (comme un dossier suspendu) */
.dossier-initials-tab {
    position: absolute;
    top: -26px;
    left: -4px;                       /* flush avec la bordure gauche */
    background: var(--svc-color, #0A4D8C);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    padding: 5px 18px 4px 8px;
    border-radius: 5px 5px 0 0;
    white-space: nowrap;
    line-height: 1;
    /* Coin coupé côté droit — effet onglet de classeur */
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 100%, 0 100%);
}

/* Corps de la carte */
.dossier-body { padding: 14px 16px 12px; }

.dossier-name { font-size: 13.5px; font-weight: 700; color: #111827; line-height: 1.3; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.dossier-mat  { font-size: 11.5px; color: #6B7280; margin-top: 1px; }
.dossier-sub  { font-size: 11.5px; color: #9CA3AF; margin-top: 1px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }

.dossier-divider { height: 1px; background: #F3F4F6; margin: 10px 0; }

/* Compteurs */
.dossier-stats { display: flex; }
.dossier-stat  { text-align: center; flex: 1; padding: 4px 0; }
.dossier-stat + .dossier-stat { border-left: 1px solid #F3F4F6; }
.dossier-stat .val      { font-size: 17px; font-weight: 700; color: #0A4D8C; line-height: 1; }
.dossier-stat .val.gray { color: #6B7280; }
.dossier-stat .val.dark { color: #374151; }
.dossier-stat .lbl      { font-size: 10px; color: #9CA3AF; margin-top: 2px; }

/* Barre de complétude */
.dossier-fill { height: 4px; background: #F3F4F6; border-radius: 2px; overflow: hidden; margin-top: 10px; }
.dossier-fill-inner { height: 100%; background: var(--svc-color, #0A4D8C); opacity:.75; border-radius: 2px; transition: width .4s; }

.dossier-footer { display:flex;align-items:center;justify-content:space-between;margin-top:6px; }
.dossier-etagere { font-size:10.5px;color:#6B7280;background:#F3F4F6;padding:2px 7px;border-radius:4px; }

/* Badges statut */
.db-badge           { font-size:10px;padding:2px 8px;border-radius:20px;font-weight:600;white-space:nowrap; }
.db-badge-actif     { background:#ECFDF5;color:#059669; }
.db-badge-archive   { background:#F3F4F6;color:#6B7280; }
.db-badge-cloture   { background:#FEF2F2;color:#DC2626; }
.db-badge-nodossier { background:#FEF3C7;color:#92400E; }

/* EMPTY STATE */
.empty-state { text-align:center;padding:60px 20px;color:#9CA3AF; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 mb-3" style="border-radius:10px;background:#ECFDF5;color:#065F46;">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── TOOLBAR ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between">
            <form method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
                <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="Nom, prénom, matricule…"
                               value="{{ request('q') }}">
                    </div>
                </div>
                <select name="service" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ request('service') == $svc->id_service ? 'selected' : '' }}>
                            {{ $svc->nom_service }}
                        </option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:150px;">
                    <option value="">Tous les statuts</option>
                    <option value="Actif"        {{ request('statut') === 'Actif'        ? 'selected' : '' }}>Actif</option>
                    <option value="Archivé"      {{ request('statut') === 'Archivé'      ? 'selected' : '' }}>Archivé</option>
                    <option value="Clôturé"      {{ request('statut') === 'Clôturé'      ? 'selected' : '' }}>Clôturé</option>
                    <option value="sans_dossier" {{ request('statut') === 'sans_dossier' ? 'selected' : '' }}>Sans dossier</option>
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['q', 'service', 'statut']))
                    <a href="{{ route('rh.ged.dossiers') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('rh.ged.documents.create') }}" class="ged-btn ged-btn-primary ms-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Déposer un document
        </a>
        </div>
    </div>

    {{-- ── COMPTEUR ─────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <span style="font-size:13px;color:#6B7280;">
            <strong style="color:#111827;">{{ $agents->count() }}</strong> agent{{ $agents->count() > 1 ? 's' : '' }}
            — <strong style="color:#111827;">{{ $dossiersByService->count() }}</strong> service{{ $dossiersByService->count() > 1 ? 's' : '' }}
        </span>
    </div>

    {{-- ── DOSSIERS PAR SERVICE ───────────────────────────────── --}}
    @php
        $palette = ['#0A4D8C','#BE123C','#059669','#7C3AED','#B45309','#0F766E','#0891B2','#DC2626'];
        $svcIdx  = 0;
    @endphp

    @if($dossiersByService->isEmpty())
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            <p class="fw-500 mb-0 mt-3">Aucun agent trouvé</p>
            @if(request()->hasAny(['q','service','statut']))
                <a href="{{ route('rh.ged.dossiers') }}" class="ged-btn ged-btn-outline mt-3 d-inline-flex">Réinitialiser les filtres</a>
            @endif
        </div>
    @else
        @foreach($dossiersByService as $serviceName => $agentsInService)
        @php
            $svcColor = $palette[$svcIdx % count($palette)];
            $svcIdx++;
            $dossiersActifs = $agentsInService->filter(fn($a) => $a->dossier?->statut_da === 'Actif')->count();
        @endphp
        <div class="svc-section">

            {{-- En-tête section service --}}
            <div class="svc-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span class="svc-name">{{ $serviceName }}</span>
                <span class="svc-badge" style="background:{{ $svcColor }};">{{ $agentsInService->count() }}</span>
                <span class="svc-count">{{ $dossiersActifs }} dossier{{ $dossiersActifs > 1 ? 's' : '' }} actif{{ $dossiersActifs > 1 ? 's' : '' }}</span>
            </div>

            {{-- Grille dossiers physiques --}}
            <div class="row g-3">
                @foreach($agentsInService as $agent)
                @php
                    $dossier  = $agent->dossier;           /* peut être null */
                    $statut   = $dossier?->statut_da ?? 'Sans dossier';
                    $taux     = $dossier?->taux_remplissage ?? 0;
                    $initials = strtoupper(substr($agent->prenom ?? '?', 0, 1))
                              . strtoupper(substr($agent->nom   ?? '',  0, 1));
                    $badgeCls = match($statut) {
                        'Actif'   => 'db-badge-actif',
                        'Archivé' => 'db-badge-archive',
                        'Clôturé' => 'db-badge-cloture',
                        default   => 'db-badge-nodossier',
                    };
                    $docsActifs  = $dossier?->documents_actifs_count ?? 0;
                    $docsTotal   = $dossier?->documents_count ?? 0;
                    $docsArchive = $docsTotal - $docsActifs;
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6 dossier-col">
                    @if($dossier)
                        <a href="{{ route('rh.ged.dossier.show', $dossier->id_dossier) }}"
                           class="dossier-card" style="--svc-color:{{ $svcColor }};">
                    @else
                        <div class="dossier-card no-dossier" style="--svc-color:{{ $svcColor }};">
                    @endif

                        {{-- Onglet initiales (dossier suspendu) --}}
                        <div class="dossier-initials-tab">{{ $initials }}</div>

                        <div class="dossier-body">

                            {{-- Identité + badge --}}
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="dossier-name">{{ $agent->nom_complet }}</div>
                                    <div class="dossier-mat">{{ $agent->matricule }}</div>
                                    <div class="dossier-sub">{{ $agent->service?->nom_service }} · {{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') }}</div>
                                </div>
                                <span class="db-badge {{ $badgeCls }}">{{ $statut }}</span>
                            </div>

                            <div class="dossier-divider"></div>

                            @if($dossier)
                                {{-- Compteurs documents --}}
                                <div class="dossier-stats">
                                    <div class="dossier-stat">
                                        <div class="val">{{ $docsActifs }}</div>
                                        <div class="lbl">Actifs</div>
                                    </div>
                                    <div class="dossier-stat">
                                        <div class="val gray">{{ $docsArchive }}</div>
                                        <div class="lbl">Archivés</div>
                                    </div>
                                    <div class="dossier-stat">
                                        <div class="val dark">{{ $docsTotal }}</div>
                                        <div class="lbl">Total</div>
                                    </div>
                                </div>

                                {{-- Barre de complétude --}}
                                <div class="dossier-fill">
                                    <div class="dossier-fill-inner" style="width:{{ $taux }}%;"></div>
                                </div>
                                <div class="dossier-footer">
                                    @if($dossier->etagere)
                                        <span class="dossier-etagere">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:2px;"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                            {{ $dossier->etagere->nom_etagere }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    <span style="font-size:10.5px;color:#9CA3AF;">{{ $taux }}%</span>
                                </div>
                            @else
                                {{-- Pas encore de dossier GED --}}
                                <div style="text-align:center;padding:8px 0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" style="display:block;margin:0 auto 6px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                    <span style="font-size:11.5px;color:#9CA3AF;">Aucun dossier GED</span>
                                </div>
                            @endif

                        </div>

                    @if($dossier)
                        </a>
                    @else
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

        </div>
        @endforeach
    @endif

</div>
@endsection
