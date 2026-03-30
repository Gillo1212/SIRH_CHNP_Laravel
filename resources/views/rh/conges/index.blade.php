@extends('layouts.master')

@section('title', 'Gestion des Congés')
@section('page-title', 'Gestion des Congés')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Congés</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:26px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.red::before    { background:#DC2626; }
.badge-statut { display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:10.5px;font-weight:600;white-space:nowrap; }
.badge-en_attente { background:#FEF3C7;color:#92400E; }
.badge-valide     { background:#DBEAFE;color:#1E40AF; }
.badge-approuve   { background:#D1FAE5;color:#065F46; }
.badge-rejete     { background:#FEE2E2;color:#991B1B; }
.table-custom { width:100%;border-collapse:separate;border-spacing:0; }
.table-custom thead th { padding:10px 14px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
.table-custom tbody td { padding:12px 14px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
.table-custom tbody tr:hover { background:var(--sirh-primary-hover); }
/* filter-bar styles handled by master layout */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30);transform:translateY(-1px); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }
.action-btn-amber { background:#D97706;color:#fff; }
.action-btn-amber:hover { background:#B45309;color:#fff; }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Gestion des congés</h4>
            <p class="text-muted small mb-0">Historique de toutes les demandes de congé</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($stats['valides'] > 0)
                <a href="{{ route('rh.conges.pending') }}" class="action-btn" style="background:#FEF3C7;color:#92400E;">
                    <i class="fas fa-clock"></i>
                    {{ $stats['valides'] }} en attente d'approbation
                </a>
            @endif
            <a href="{{ route('rh.conge-physique') }}" class="action-btn action-btn-outline">
                <i class="fas fa-pen-to-square"></i> Saisie physique
            </a>
            <a href="{{ route('rh.conges.soldes') }}" class="action-btn action-btn-primary">
                <i class="fas fa-chart-bar"></i> Soldes
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md col-lg">
            <div class="kpi-card blue border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div><div class="kpi-value" style="color:var(--theme-text);">{{ $stats['total'] }}</div><div class="kpi-label">Total</div></div>
                    <div class="kpi-icon" style="background:var(--theme-bg-secondary);color:#0A4D8C;"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="kpi-card amber border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div><div class="kpi-value" style="color:#F59E0B;">{{ $stats['en_attente'] }}</div><div class="kpi-label">En attente</div></div>
                    <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="kpi-card blue border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div><div class="kpi-value" style="color:#3B82F6;">{{ $stats['valides'] }}</div><div class="kpi-label">À approuver</div></div>
                    <div class="kpi-icon" style="background:#DBEAFE;color:#1D4ED8;"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="kpi-card green border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div><div class="kpi-value" style="color:#10B981;">{{ $stats['approuves'] }}</div><div class="kpi-label">Approuvés</div></div>
                    <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md col-lg">
            <div class="kpi-card red border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div><div class="kpi-value" style="color:#EF4444;">{{ $stats['rejetes'] }}</div><div class="kpi-label">Rejetés</div></div>
                    <div class="kpi-icon" style="background:#FEE2E2;color:#DC2626;"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.conges.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Nom, prénom, matricule…" value="{{ request('search') }}">
                    </div>
                </div>
                <select name="statut" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les statuts</option>
                    <option value="En_attente" {{ request('statut') === 'En_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="Validé" {{ request('statut') === 'Validé' ? 'selected' : '' }}>Validé Manager</option>
                    <option value="Approuvé" {{ request('statut') === 'Approuvé' ? 'selected' : '' }}>Approuvé</option>
                    <option value="Rejeté" {{ request('statut') === 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
                </select>
                <select name="type_conge" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les types</option>
                    @foreach($typesConge as $t)
                        <option value="{{ $t->id_type_conge }}" {{ request('type_conge') == $t->id_type_conge ? 'selected' : '' }}>{{ $t->libelle }}</option>
                    @endforeach
                </select>
                <select name="service" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id_service }}" {{ request('service') == $s->id_service ? 'selected' : '' }}>{{ $s->nom_service }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['search', 'statut', 'type_conge', 'service']))
                    <a href="{{ route('rh.conges.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($demandes->count() > 0)
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Type de congé</th>
                                <th>Période</th>
                                <th>Durée</th>
                                <th>Date demande</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandes as $demande)
                                @php
                                    $conge  = $demande->conge;
                                    $agent  = $demande->agent;
                                    $statut = $demande->statut_demande;
                                    $badgeClass = match($statut) {
                                        'En_attente' => 'badge-en_attente',
                                        'Validé'     => 'badge-valide',
                                        'Approuvé'   => 'badge-approuve',
                                        'Rejeté'     => 'badge-rejete',
                                        default      => '',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-600" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                        <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                    </td>
                                    <td>
                                        <span style="font-size:12px;color:var(--theme-text);">{{ $conge->typeConge->libelle ?? '—' }}</span>
                                    </td>
                                    <td>
                                        @if($conge)
                                            <span style="font-size:12px;">{{ $conge->date_debut?->format('d/m/Y') }}</span>
                                            <span class="text-muted"> → </span>
                                            <span style="font-size:12px;">{{ $conge->date_fin?->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong style="color:#0A4D8C;">{{ $conge->nbres_jours ?? '—' }}j</strong>
                                    </td>
                                    <td>
                                        <span style="font-size:12px;">{{ $demande->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-statut {{ $badgeClass }}">{{ str_replace('_', ' ', $statut) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('rh.conges.show', $demande->id_demande) }}" class="btn btn-sm" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:6px;font-size:11px;color:var(--theme-text);">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($demandes->hasPages())
                    <div class="px-4 py-3 border-top" style="background:var(--theme-panel-bg);">
                        {{ $demandes->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x mb-3 d-block" style="color:#0A4D8C;opacity:.2;"></i>
                    <p class="text-muted mb-0">Aucune demande de congé trouvée.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function showToast(message, type) {
    const cfg = { success:{bg:'#10B981',icon:'fa-check-circle'}, error:{bg:'#EF4444',icon:'fa-exclamation-circle'} };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `<div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:400px;animation:toastIn .3s ease;"><i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i><span>${message}</span><button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif
</script>
@endpush
