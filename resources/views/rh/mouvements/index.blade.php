@extends('layouts.master')
@section('title', $filtreActif ? $filtreActif . 's — Mouvements' : 'Tous les mouvements')
@section('page-title', 'Gestion des Mouvements')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">Ressources Humaines</a></li>
    <li>{{ $filtreActif ?? 'Mouvements' }}</li>
@endsection

@push('styles')
<style>
/* ── KPI CARDS ────────────────────────────────────────── */
.kpi-card {
    border-radius: 12px; padding: 18px 20px;
    transition: box-shadow 200ms, transform 200ms;
    position: relative; overflow: hidden;
}
.kpi-card:hover { box-shadow: 0 6px 20px rgba(10,77,140,0.10); transform: translateY(-2px); }
.kpi-card .kpi-icon { width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:22px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:4px;font-weight:500; }
.kpi-card .kpi-trend { font-size:11px;font-weight:600;margin-top:5px; }
.kpi-card .kpi-trend.up   { color:#10B981; }
.kpi-card .kpi-trend.down { color:#EF4444; }
.kpi-card .kpi-trend.neutral { color:#6B7280; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:70px;height:70px;border-radius:0 12px 0 70px;opacity:0.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.red::before    { background:#DC2626; }

/* ── BUTTONS ──────────────────────────────────────────── */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,0.3);transform:translateY(-1px); }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.action-btn-danger { background:#DC2626;color:#fff; }
.action-btn-danger:hover { background:#B91C1C;color:#fff; }
.action-btn-success { background:#059669;color:#fff; }
.action-btn-success:hover { background:#047857;color:#fff; }

/* ── BADGES ───────────────────────────────────────────── */
.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:9px;font-weight:400; }

/* filter-bar styles handled by master layout */

/* ── TABLE ────────────────────────────────────────────── */
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;padding-bottom:6px; }
.modal-label   { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:#6B7280; }
.mouv-row { transition:background 150ms; }
.mouv-row:hover { background:#F9FAFB!important; }
.btn-icon { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;transition:all 150ms;font-size:12px; }
.btn-icon-view    { background:#EFF6FF;color:#1D4ED8; } .btn-icon-view:hover    { background:#DBEAFE; }
.btn-icon-success { background:#D1FAE5;color:#065F46; } .btn-icon-success:hover { background:#A7F3D0; }
.btn-icon-danger  { background:#FEE2E2;color:#991B1B; } .btn-icon-danger:hover  { background:#FECACA; }
.btn-icon-edit    { background:#FEF3C7;color:#92400E; } .btn-icon-edit:hover    { background:#FDE68A; }

/* ── TOAST ────────────────────────────────────────────── */
@keyframes toastIn  { from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);} }
@keyframes toastOut { from{opacity:1;}to{opacity:0;transform:translateX(40px);} }

/* ── TYPE SELECTOR ────────────────────────────────────── */
.type-card { border:1.5px solid #E5E7EB;border-radius:10px;transition:all 180ms;cursor:pointer; }
.type-card:hover { border-color:#0A4D8C; }
.type-radio:checked + .type-card { border-color:var(--tc-color);background:var(--tc-bg);color:var(--tc-color); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    {{-- ── EN-TÊTE ─────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
                {{ $filtreActif ? $filtreActif . 's' : 'Mouvements du personnel' }}
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Affectations, mutations, retours et départs — {{ now()->isoFormat('D MMMM YYYY') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('rh.mouvements.export', request()->query()) }}" class="action-btn action-btn-outline">
                <i class="fas fa-file-excel"></i> Export Excel
                @if(request()->anyFilled(['search','type_mouvement','statut','service']))
                <span style="font-size:10px;background:#D1FAE5;color:#065F46;padding:1px 5px;border-radius:10px;font-weight:700;">filtré</span>
                @endif
            </a>
            @can('create', \App\Models\Mouvement::class)
            <button type="button" class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-mouvement">
                <i class="fas fa-plus"></i>Nouveau mouvement
            </button>
            @endcan
        </div>
    </div>

    {{-- ── KPIs (6 cartes) ────────────────────────────────────── --}}
    <div class="section-title" style="color:var(--theme-text);">Tableau de bord des mouvements</div>
    <div class="row g-3 mb-4">

        {{-- Total --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <a href="{{ route('rh.mouvements.index') }}" class="text-decoration-none">
                <div class="kpi-card blue" style="background:var(--theme-panel-bg);border:{{ !$filtreActif ? '2px solid #0A4D8C' : '1px solid var(--theme-border,#E5E7EB)' }};">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-exchange-alt" style="color:#0A4D8C;"></i></div>
                        <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">Total</span>
                    </div>
                    <div class="kpi-value" style="color:var(--theme-text);">{{ $stats['total'] }}</div>
                    <div class="kpi-label text-muted">Tous Mouvs.</div>
                    <div class="kpi-trend neutral"><i class="fas fa-database me-1"></i>Hist. complet</div>
                </div>
            </a>
        </div>

        {{-- Affectations --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <a href="{{ route('rh.mouvements.affectations') }}" class="text-decoration-none">
                <div class="kpi-card blue" style="background:var(--theme-panel-bg);border:{{ $filtreActif === 'Affectation initiale' ? '2px solid #1565C0' : '1px solid var(--theme-border,#E5E7EB)' }};">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-user-plus" style="color:#1565C0;"></i></div>
                        <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">Nouvelles</span>
                    </div>
                    <div class="kpi-value" style="color:#1565C0;">{{ $stats['affectations'] }}</div>
                    <div class="kpi-label text-muted">Affectations</div>
                    <div class="kpi-trend neutral"><i class="fas fa-arrow-right me-1"></i>1ères affec.</div>
                </div>
            </a>
        </div>

        {{-- Mutations --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <a href="{{ route('rh.mouvements.mutations') }}" class="text-decoration-none">
                <div class="kpi-card amber" style="background:var(--theme-panel-bg);border:{{ $filtreActif === 'Mutation' ? '2px solid #D97706' : '1px solid var(--theme-border,#E5E7EB)' }};">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-arrows-alt-h" style="color:#D97706;"></i></div>
                        <span class="badge-status" style="background:#FFFBEB;color:#92400E;">Transferts</span>
                    </div>
                    <div class="kpi-value" style="color:#D97706;">{{ $stats['mutations'] }}</div>
                    <div class="kpi-label text-muted">Mutations</div>
                    <div class="kpi-trend neutral"><i class="fas fa-random me-1"></i>CServ.</div>
                </div>
            </a>
        </div>

        {{-- Retours --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <a href="{{ route('rh.mouvements.retours') }}" class="text-decoration-none">
                <div class="kpi-card green" style="background:var(--theme-panel-bg);border:{{ $filtreActif === 'Retour' ? '2px solid #059669' : '1px solid var(--theme-border,#E5E7EB)' }};">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-undo-alt" style="color:#059669;"></i></div>
                        <span class="badge-status" style="background:#ECFDF5;color:#065F46;">Réintégrations</span>
                    </div>
                    <div class="kpi-value" style="color:#059669;">{{ $stats['retours'] }}</div>
                    <div class="kpi-label text-muted">Retours</div>
                    <div class="kpi-trend up"><i class="fas fa-check me-1"></i>Réintégrations</div>
                </div>
            </a>
        </div>

        {{-- Départs --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <a href="{{ route('rh.mouvements.departs') }}" class="text-decoration-none">
                <div class="kpi-card red" style="background:var(--theme-panel-bg);border:{{ $filtreActif === 'Départ' ? '2px solid #DC2626' : '1px solid var(--theme-border,#E5E7EB)' }};">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="kpi-icon" style="background:#FEF2F2;"><i class="fas fa-sign-out-alt" style="color:#DC2626;"></i></div>
                        <span class="badge-status" style="background:#FEF2F2;color:#991B1B;">Sorties</span>
                    </div>
                    <div class="kpi-value" style="color:#DC2626;">{{ $stats['departs'] }}</div>
                    <div class="kpi-label text-muted">Départs</div>
                    <div class="kpi-trend {{ $stats['departs'] > 0 ? 'down' : 'up' }}">
                        <i class="fas fa-{{ $stats['departs'] > 0 ? 'exclamation-triangle' : 'check' }} me-1"></i>
                        {{ $stats['departs'] > 0 ? 'À surveiller' : 'Aucun ce mois' }}
                    </div>
                </div>
            </a>
        </div>

        {{-- En attente --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="kpi-card amber" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border,#E5E7EB);">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-hourglass-half" style="color:#D97706;"></i></div>
                    <span class="badge-status" style="background:#FEF3C7;color:#92400E;">En attente</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['en_attente'] }}</div>
                <div class="kpi-label text-muted">À traiter</div>
                <div class="kpi-trend {{ $stats['en_attente'] > 0 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $stats['en_attente'] > 0 ? 'clock' : 'check' }} me-1"></i>
                    {{ $stats['en_attente'] > 0 ? 'Action requise' : 'Tout traité' }}
                </div>
            </div>
        </div>

    </div>

    {{-- ── FILTRES ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.mouvements.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control border-start-0" placeholder="Nom, prénom ou matricule…">
                    </div>
                </div>
                <select name="type_mouvement" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les types</option>
                    @foreach(\App\Models\Mouvement::TYPES as $key => $cfg)
                        <option value="{{ $key }}" {{ request('type_mouvement') == $key ? 'selected' : '' }}>
                            {{ $cfg['label'] }}
                        </option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:150px;">
                    <option value="">Tous les statuts</option>
                    @foreach(\App\Models\Mouvement::STATUTS as $key => $cfg)
                        <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>
                            {{ $cfg['label'] }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['search', 'type_mouvement', 'statut']))
                    <a href="{{ route('rh.mouvements.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── TABLEAU ─────────────────────────────────────────────── --}}
    <div class="section-title" style="color:var(--theme-text);">
        Liste des mouvements
        @if(request()->hasAny(['search','type_mouvement','statut']))
            <span style="font-weight:400;color:#6B7280;text-transform:none;letter-spacing:0;font-size:12px;">— filtres actifs</span>
        @endif
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);font-size:13.5px;">
                {{ $filtreActif ?? 'Tous les mouvements' }}
                <span class="text-muted ms-1" style="font-size:12px;font-weight:400;">({{ $mouvements->total() }})</span>
            </h6>
            <div class="d-flex gap-2">
                @if($stats['en_attente'] > 0)
                <span class="badge-status" style="background:#FEF3C7;color:#92400E;">
                    <i class="fas fa-clock me-1"></i>{{ $stats['en_attente'] }} en attente
                </span>
                @endif
                @if($stats['valide_drh'] > 0)
                <span class="badge-status" style="background:#D1FAE5;color:#065F46;">
                    <i class="fas fa-check-double me-1"></i>{{ $stats['valide_drh'] }} validé DRH
                </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Mouvement</th>
                            <th class="py-3 border-0">Date effet</th>
                            <th class="py-3 border-0">Statut</th>
                            <th class="py-3 border-0 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $m)
                            @php
                                $tc = \App\Models\Mouvement::TYPES[$m->type_mouvement]  ?? ['color'=>'#6B7280','bg'=>'#F3F4F6','icon'=>'fa-question','label'=>$m->type_mouvement];
                                $sc = \App\Models\Mouvement::STATUTS[$m->statut]        ?? ['color'=>'#6B7280','bg'=>'#F3F4F6','label'=>$m->statut];
                                $ini = strtoupper(substr($m->agent->prenom??'A',0,1).substr($m->agent->nom??'',0,1));
                            @endphp
                            <tr class="mouv-row" style="border-bottom:1px solid #F3F4F6;">

                                {{-- Agent --}}
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $ini }}</div>
                                        <div>
                                            <div style="font-weight:600;color:var(--theme-text);">{{ $m->agent->nom_complet }}</div>
                                            <div style="font-size:11px;color:#9CA3AF;">{{ $m->agent->matricule }} · {{ $m->agent->fontion }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;background:{{ $tc['bg'] }};color:{{ $tc['color'] }};padding:3px 10px;border-radius:20px;font-weight:700;">
                                        <i class="fas {{ $tc['icon'] }} me-1"></i>{{ $tc['label'] }}
                                    </span>
                                </td>

                                {{-- Direction --}}
                                <td class="py-3 border-0" style="font-size:12.5px;max-width:200px;">
                                    @if($m->serviceOrigine)
                                        <span class="text-muted">{{ $m->serviceOrigine->nom_service }}</span>
                                        <i class="fas fa-arrow-right mx-1" style="color:#D97706;font-size:10px;"></i>
                                    @endif
                                    @if($m->serviceDestination)
                                        <strong style="color:var(--theme-text);">{{ $m->serviceDestination->nom_service }}</strong>
                                    @elseif($m->type_mouvement === 'Départ')
                                        <span style="color:#DC2626;font-weight:600;">Départ définitif</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="py-3 border-0" style="font-weight:500;color:var(--theme-text);white-space:nowrap;">
                                    {{ $m->date_mouvement?->format('d/m/Y') ?? '—' }}
                                </td>

                                {{-- Statut --}}
                                <td class="py-3 border-0">
                                    <span class="badge-status" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                        {{ $sc['label'] }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3 border-0 text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <button type="button" class="btn-icon btn-icon-view" title="Détail"
                                                onclick="voirMouvement({{ $m->id_mouvement }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @can('update', $m)
                                        <button type="button" class="btn-icon btn-icon-edit" title="Modifier"
                                                onclick="editMouvement({{ $m->id_mouvement }})">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        @endcan
                                        @can('effectuer', $m)
                                        <form action="{{ route('rh.mouvements.effectuer', $m->id_mouvement) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Confirmer l\'exécution de ce mouvement validé par le DRH ?\nLe dossier de l\'agent sera mis à jour automatiquement.')">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-icon-success" title="Effectuer (validé DRH)">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @cannot('effectuer', $m)
                                        @if($m->statut === 'en_attente')
                                        <span title="En attente de validation DRH" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#FEF3C7;color:#D97706;font-size:11px;cursor:default;">
                                            <i class="fas fa-hourglass-half"></i>
                                        </span>
                                        @endif
                                        @endcannot
                                        @can('annuler', $m)
                                        <button type="button" class="btn-icon btn-icon-danger" title="Annuler"
                                                onclick="annulerMouvement({{ $m->id_mouvement }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 border-0">
                                    <div style="color:#D1D5DB;font-size:36px;margin-bottom:12px;"><i class="fas fa-exchange-alt"></i></div>
                                    <p class="mb-1 fw-500 text-muted">Aucun mouvement enregistré</p>
                                    <p class="small text-muted mb-3">Les mouvements du personnel apparaîtront ici</p>
                                    @can('create', \App\Models\Mouvement::class)
                                    <button type="button" class="action-btn action-btn-primary" style="margin:0 auto;font-size:13px;padding:8px 16px;"
                                            data-bs-toggle="modal" data-bs-target="#modal-create-mouvement">
                                        <i class="fas fa-plus"></i>Nouveau mouvement
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($mouvements->hasPages())
            <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">
                {{ $mouvements->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL : CRÉER UN MOUVEMENT                               --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@can('create', \App\Models\Mouvement::class)
<div class="modal fade" id="modal-create-mouvement" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#EFF6FF;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#DBEAFE;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-exchange-alt" style="color:#1565C0;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Nouveau mouvement</h5>
                        <p class="text-muted small mb-0">Affectation, mutation, retour ou départ de personnel</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.mouvements.store') }}" method="POST" id="form-create-mouvement">
                @csrf
                <div class="modal-body p-4">

                    {{-- Sélecteur type --}}
                    <div class="mb-4">
                        <label class="modal-label">Type de mouvement <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach(\App\Models\Mouvement::TYPES as $key => $cfg)
                            <div class="col-6 col-md-3">
                                <label style="display:block;cursor:pointer;margin:0;">
                                    <input type="radio" name="type_mouvement" value="{{ $key }}"
                                           class="type-radio visually-hidden"
                                           {{ old('type_mouvement') == $key ? 'checked' : '' }}
                                           onchange="onTypeChange('{{ $key }}', '{{ $cfg['color'] }}', '{{ $cfg['bg'] }}')">
                                    <div class="type-card p-3 text-center" id="tc-{{ Str::slug($key) }}"
                                         style="--tc-color:{{ $cfg['color'] }};--tc-bg:{{ $cfg['bg'] }};">
                                        <i class="fas {{ $cfg['icon'] }} mb-1 d-block" style="font-size:18px;"></i>
                                        <div style="font-size:12px;font-weight:600;line-height:1.3;">{{ $cfg['label'] }}</div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('type_mouvement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-8">
                            <label class="modal-label">Agent concerné <span class="text-danger">*</span></label>
                            <select name="id_agent" class="form-select form-select-sm" style="border-radius:7px;" required>
                                <option value="">— Sélectionner un agent —</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id_agent }}" {{ old('id_agent') == $agent->id_agent ? 'selected' : '' }}>
                                        {{ $agent->nom_complet }} ({{ $agent->matricule }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_agent')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="modal-label">Date d'effet <span class="text-danger">*</span></label>
                            <input type="date" name="date_mouvement" value="{{ old('date_mouvement', today()->format('Y-m-d')) }}"
                                   class="form-control form-control-sm" style="border-radius:7px;" required>
                            @error('date_mouvement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Services (conditionnels via JS) --}}
                    <div class="row g-3 mb-3" id="fields-services" style="display:none;">
                        <div class="col-12 col-md-6" id="field-service-origine" style="display:none;">
                            <label class="modal-label">Service d'origine <span class="text-danger">*</span></label>
                            <select name="id_service_origine" class="form-select form-select-sm" style="border-radius:7px;">
                                <option value="">— Service d'origine —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id_service }}" {{ old('id_service_origine') == $svc->id_service ? 'selected' : '' }}>
                                        {{ $svc->nom_service }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_service_origine')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6" id="field-service-destination" style="display:none;">
                            <label class="modal-label">Service de destination <span class="text-danger">*</span></label>
                            <select name="id_service" class="form-select form-select-sm" style="border-radius:7px;">
                                <option value="">— Service de destination —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id_service }}" {{ old('id_service') == $svc->id_service ? 'selected' : '' }}>
                                        {{ $svc->nom_service }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_service')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="modal-label">Motif / Observations</label>
                        <textarea name="motif" rows="3" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"
                                  placeholder="Précisez le contexte de ce mouvement…">{{ old('motif') }}</textarea>
                        @error('motif')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL : MODIFIER                                         --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-edit-mouvement" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FFFBEB;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FEF3C7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-pen" style="color:#D97706;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Modifier le mouvement</h5>
                        <p class="text-muted small mb-0" id="edit-subtitle">—</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-edit-mouvement" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Type de mouvement <span class="text-danger">*</span></label>
                            <select name="type_mouvement" id="edit-type" class="form-select form-select-sm" style="border-radius:7px;" required onchange="onEditTypeChange()">
                                @foreach(\App\Models\Mouvement::TYPES as $key => $cfg)
                                    <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Date d'effet <span class="text-danger">*</span></label>
                            <input type="date" name="date_mouvement" id="edit-date" class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6" id="edit-field-origine" style="display:none;">
                            <label class="modal-label">Service d'origine</label>
                            <select name="id_service_origine" id="edit-service-origine" class="form-select form-select-sm" style="border-radius:7px;">
                                <option value="">— Service d'origine —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id_service }}">{{ $svc->nom_service }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="edit-field-destination" style="display:none;">
                            <label class="modal-label">Service de destination</label>
                            <select name="id_service" id="edit-service-destination" class="form-select form-select-sm" style="border-radius:7px;">
                                <option value="">— Service de destination —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id_service }}">{{ $svc->nom_service }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="modal-label">Motif / Observations</label>
                        <textarea name="motif" id="edit-motif" rows="3" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL : DÉTAIL                                           --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-detail-mouvement" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" id="detail-header" style="background:#EFF6FF;">
                <div class="d-flex align-items-center gap-3">
                    <div id="detail-icon-wrap" style="width:44px;height:44px;background:#DBEAFE;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i id="detail-icon" class="fas fa-exchange-alt" style="color:#1565C0;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="detail-title">Mouvement</h5>
                        <p class="text-muted small mb-0" id="detail-subtitle">Chargement…</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detail-body">
                <div class="text-center py-4"><div class="spinner-border text-primary" style="width:2rem;height:2rem;"></div></div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL : ANNULER                                          --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-annuler" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-times-circle" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Annuler le mouvement</h5>
                        <p class="text-muted small mb-0">Cette action est irréversible</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-annuler" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <p class="text-muted mb-0" style="font-size:13.5px;">Êtes-vous sûr de vouloir annuler ce mouvement ? Il ne sera plus traitable.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Non, garder</button>
                    <button type="submit" class="action-btn action-btn-danger"><i class="fas fa-times"></i>Oui, annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Toast ────────────────────────────────────────────────
function showToast(msg, type) {
    type = type||'success';
    var c = {success:{bg:'#ECFDF5',fg:'#065F46',ic:'check-circle',bd:'#059669'},error:{bg:'#FEF2F2',fg:'#991B1B',ic:'times-circle',bd:'#DC2626'},warning:{bg:'#FFFBEB',fg:'#92400E',ic:'exclamation-triangle',bd:'#D97706'}}[type]||{bg:'#ECFDF5',fg:'#065F46',ic:'check-circle',bd:'#059669'};
    var t=document.createElement('div');
    t.style.cssText='background:'+c.bg+';color:'+c.fg+';padding:14px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:500;min-width:280px;max-width:380px;animation:toastIn .3s ease;border-left:4px solid '+c.bd+';pointer-events:all;';
    t.innerHTML='<i class="fas fa-'+c.ic+'" style="flex-shrink:0;"></i><span>'+msg+'</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;margin-left:auto;opacity:.7;padding:0;"><i class="fas fa-times"></i></button>';
    document.getElementById('toast-container').appendChild(t);
    setTimeout(function(){t.style.animation='toastOut .3s ease forwards';setTimeout(function(){t.remove();},300);},4000);
}
@if(session('success')) showToast(@json(session('success')),'success'); @endif
@if(session('error'))   showToast(@json(session('error')),'error');     @endif
@if(session('warning')) showToast(@json(session('warning')),'warning'); @endif

// ── Sélecteur de type (création) ────────────────────────
function onTypeChange(key, color, bg) {
    // Réinitialiser toutes les cartes
    document.querySelectorAll('.type-card').forEach(function(c) {
        c.style.background = '';
        c.style.borderColor = '#E5E7EB';
        c.style.color = 'inherit';
    });
    // Activer la carte sélectionnée
    var slug = key.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
    var card = document.getElementById('tc-' + slug);
    if (card) {
        card.style.background   = bg;
        card.style.borderColor  = color;
        card.style.color        = color;
    }
    // Afficher/masquer les champs services
    var showOrig = (key === 'Mutation' || key === 'Retour');
    var showDest = (key !== 'Départ');
    var fsv = document.getElementById('fields-services');
    var fo  = document.getElementById('field-service-origine');
    var fd  = document.getElementById('field-service-destination');
    fsv.style.display = (showOrig || showDest) ? '' : 'none';
    fo.style.display  = showOrig ? '' : 'none';
    fd.style.display  = showDest ? '' : 'none';
    if (!showOrig) { var s = fo.querySelector('select'); if(s) s.value = ''; }
    if (!showDest) { var s = fd.querySelector('select'); if(s) s.value = ''; }
}

// Restaurer la sélection sur erreur de validation
document.addEventListener('DOMContentLoaded', function() {
    @if(old('type_mouvement'))
    var key = @json(old('type_mouvement'));
    var radio = document.querySelector('input[name="type_mouvement"][value="'+key+'"]');
    if (radio) {
        radio.checked = true;
        var card = radio.closest('label').querySelector('.type-card');
        if (card) onTypeChange(key, getComputedStyle(card).getPropertyValue('--tc-color').trim(), getComputedStyle(card).getPropertyValue('--tc-bg').trim());
    }
    @if($errors->any())
    new bootstrap.Modal(document.getElementById('modal-create-mouvement')).show();
    @endif
    @endif
});

// ── Détail AJAX ─────────────────────────────────────────
function voirMouvement(id) {
    var m = new bootstrap.Modal(document.getElementById('modal-detail-mouvement'));
    document.getElementById('detail-body').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" style="width:2rem;height:2rem;"></div></div>';
    m.show();
    fetch('/rh/mouvements/'+id, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
    .then(function(r){return r.json();})
    .then(function(d){
        document.getElementById('detail-title').textContent     = d.type_label;
        document.getElementById('detail-subtitle').textContent  = d.agent.nom_complet + ' · ' + d.agent.matricule;
        document.getElementById('detail-icon').className        = 'fas '+d.type_icon;
        document.getElementById('detail-icon').style.color      = d.type_color;
        document.getElementById('detail-icon-wrap').style.background = d.type_bg;
        document.getElementById('detail-header').style.background    = d.type_bg;
        var dir = '';
        if (d.service_origine && d.service_destination) dir = '<span class="text-muted">'+d.service_origine+'</span> <i class="fas fa-arrow-right mx-2" style="color:#D97706;font-size:10px;"></i> <strong>'+d.service_destination+'</strong>';
        else if (d.service_destination) dir = '<strong>'+d.service_destination+'</strong>';
        else if (d.type_mouvement==='Départ') dir = '<span style="color:#DC2626;font-weight:600;">Départ définitif</span>';
        document.getElementById('detail-body').innerHTML =
            '<div class="row g-3">'+
            '<div class="col-12"><div class="p-3 rounded-3" style="background:#F8FAFC;border:1px solid #E5E7EB;"><div class="row g-2">'+
            '<div class="col-6 col-md-3"><div class="modal-label">Agent</div><div style="font-weight:600;font-size:13.5px;">'+d.agent.nom_complet+'</div></div>'+
            '<div class="col-6 col-md-3"><div class="modal-label">Matricule</div><div style="font-weight:600;font-size:13.5px;">'+d.agent.matricule+'</div></div>'+
            '<div class="col-6 col-md-3"><div class="modal-label">Fonction</div><div style="font-size:13px;color:#6B7280;">'+d.agent.fontion+'</div></div>'+
            '<div class="col-6 col-md-3"><div class="modal-label">Service actuel</div><div style="font-size:13px;color:#6B7280;">'+d.agent.service+'</div></div>'+
            '</div></div></div>'+
            '<div class="col-12 col-md-6"><div class="modal-label">Direction</div><div style="font-size:13.5px;">'+dir+'</div></div>'+
            '<div class="col-12 col-md-6"><div class="modal-label">Date d\'effet</div><div style="font-size:13.5px;font-weight:600;">'+(d.date_mouvement_fr||'—')+'</div></div>'+
            '<div class="col-12 col-md-6"><div class="modal-label">Statut</div><span class="badge-status" style="background:'+d.statut_bg+';color:'+d.statut_color+';">'+d.statut_label+'</span></div>'+
            '<div class="col-12 col-md-6"><div class="modal-label">Créé par</div><div style="font-size:13px;color:#6B7280;">'+d.cree_par_nom+'</div></div>'+
            (d.valide_par_nom!=='—'?'<div class="col-12 col-md-6"><div class="modal-label">Validé DRH</div><div style="font-size:13px;color:#6B7280;">'+d.valide_par_nom+(d.date_validation_fr?' · '+d.date_validation_fr:'')+'</div></div>':'')+
            (d.motif?'<div class="col-12"><div class="modal-label">Motif</div><div class="p-3 rounded-3" style="background:#FFFBEB;font-size:13px;">'+d.motif+'</div></div>':'')+
            '</div>';
    })
    .catch(function(){ document.getElementById('detail-body').innerHTML='<p class="text-danger text-center py-3">Erreur de chargement.</p>'; });
}

// ── Modifier ─────────────────────────────────────────────
function editMouvement(id) {
    fetch('/rh/mouvements/'+id, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
    .then(function(r){return r.json();})
    .then(function(d){
        document.getElementById('edit-subtitle').textContent = 'Modification pour '+d.agent.nom_complet;
        document.getElementById('form-edit-mouvement').action = '/rh/mouvements/'+id;
        document.getElementById('edit-type').value  = d.type_mouvement;
        document.getElementById('edit-date').value  = d.date_mouvement;
        document.getElementById('edit-motif').value = d.motif||'';
        onEditTypeChange();
        if (d.id_service_origine) document.getElementById('edit-service-origine').value    = d.id_service_origine;
        if (d.id_service)         document.getElementById('edit-service-destination').value = d.id_service;
        new bootstrap.Modal(document.getElementById('modal-edit-mouvement')).show();
    });
}
function onEditTypeChange() {
    var type   = document.getElementById('edit-type').value;
    var showOr = (type==='Mutation'||type==='Retour');
    var showDe = (type!=='Départ');
    document.getElementById('edit-field-origine').style.display      = showOr ? '' : 'none';
    document.getElementById('edit-field-destination').style.display  = showDe ? '' : 'none';
}

// ── Annuler ───────────────────────────────────────────────
function annulerMouvement(id) {
    document.getElementById('form-annuler').action = '/rh/mouvements/'+id+'/annuler';
    new bootstrap.Modal(document.getElementById('modal-annuler')).show();
}
</script>
@endpush
<style>.fw-500{font-weight:500!important;}.fw-600{font-weight:600!important;}</style>
@endsection
