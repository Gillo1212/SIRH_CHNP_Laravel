@extends('layouts.master')

@section('title', 'Mes Congés')
@section('page-title', 'Mes Congés')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mes congés</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:26px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-label { font-size:12px;margin-top:2px;font-weight:500;color:var(--theme-text-muted); }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.amber::before { background:#D97706; }
.kpi-card.blue::before  { background:#0A4D8C; }
.kpi-card.green::before { background:#059669; }
.kpi-card.red::before   { background:#DC2626; }
.kpi-card.teal::before  { background:#0D9488; }
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
.badge-statut { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-en_attente { background:#FEF3C7;color:#92400E; }
.badge-valide     { background:#DBEAFE;color:#1E40AF; }
.badge-approuve   { background:#D1FAE5;color:#065F46; }
.badge-rejete     { background:#FEE2E2;color:#991B1B; }
.solde-card { border-radius:10px;padding:16px;border-left:4px solid; }
thead th { padding:10px 14px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);background:var(--theme-bg-secondary);border-bottom:1px solid var(--theme-border); }
tbody td { padding:12px 14px;font-size:13px;border-bottom:1px solid var(--theme-border);vertical-align:middle; }
tbody tr:hover td { background:var(--sirh-primary-hover); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Mes congés</h4>
            <p class="text-muted small mb-0">{{ $agent->nom_complet }} - {{ $agent->matricule }}</p>
        </div>
        @if($agent->statut_agent === 'En_Conge')
            <span style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;"
                  title="Demande impossible pendant un congé en cours">
                <i class="fas fa-umbrella-beach"></i> En congé actuellement
            </span>
        @else
            <a href="{{ route('agent.conges.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        @endif
    </div>

    {{-- Bannière congé en cours --}}
    @if($agent->statut_agent === 'En_Conge')
        <div class="d-flex align-items-start gap-3 mb-4 p-3 rounded" style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;">
            <i class="fas fa-info-circle mt-1" style="color:#D97706;flex-shrink:0;"></i>
            <div style="font-size:13px;color:#92400E;">
                <strong>Vous êtes actuellement en congé.</strong>
                Toute nouvelle demande de congé est suspendue jusqu'à votre retour en service.
                Si vous avez besoin d'une <strong>prolongation</strong>, rapprochez-vous du service RH
                muni(e) d'un justificatif (certificat médical, attestation, etc.).
            </div>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-lg">
            <div class="kpi-card amber border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#F59E0B;">{{ $stats['en_attente'] }}</div>
                        <div class="kpi-label">En attente</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEF3C7;color:#D97706;"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="kpi-card blue border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#3B82F6;">{{ $stats['validees'] }}</div>
                        <div class="kpi-label">Validées Manager</div>
                    </div>
                    <div class="kpi-icon" style="background:#DBEAFE;color:#1D4ED8;"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="kpi-card green border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#10B981;">{{ $stats['approuvees'] }}</div>
                        <div class="kpi-label">Approuvées</div>
                    </div>
                    <div class="kpi-icon" style="background:#D1FAE5;color:#059669;"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="kpi-card red border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#EF4444;">{{ $stats['rejetees'] }}</div>
                        <div class="kpi-label">Rejetées</div>
                    </div>
                    <div class="kpi-icon" style="background:#FEE2E2;color:#DC2626;"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="kpi-card teal border" style="background:var(--theme-panel-bg);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value" style="color:#0D9488;">{{ $stats['jours_pris'] }}</div>
                        <div class="kpi-label">Jours posés</div>
                    </div>
                    <div class="kpi-icon" style="background:#CCFBF1;color:#0D9488;"><i class="fas fa-calendar-minus"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Soldes & Reliquats --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header border-0 px-4 pt-3 pb-2" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-calendar-check me-2" style="color:#0A4D8C;"></i>
                        Mes soldes {{ date('Y') }}
                    </h6>
                    <p class="text-muted mb-0" style="font-size:11px;">Le reliquat est le nombre de jours restants</p>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($soldes as $solde)
                        @php
                            $pct = $solde->solde_initial > 0
                                ? round(($solde->solde_restant / $solde->solde_initial) * 100)
                                : 0;
                            $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                        @endphp
                        <div class="solde-card mb-3" style="background:var(--theme-bg-secondary);border-left-color:{{ $color }};">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-600 small" style="color:var(--theme-text);">{{ $solde->typeConge->libelle ?? '-' }}</span>
                                <div class="text-end">
                                    <div class="fw-bold" style="color:{{ $color }};font-size:18px;line-height:1;">{{ $solde->solde_restant }}</div>
                                    <div style="font-size:10px;color:var(--theme-text-muted);">j. reliquat</div>
                                </div>
                            </div>
                            <div class="progress mb-2" style="height:5px;border-radius:3px;">
                                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="text-muted" style="font-size:10px;">
                                        <i class="fas fa-check-circle me-1" style="color:#10B981;"></i>Pris : <strong>{{ $solde->solde_pris }}j</strong>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-muted" style="font-size:10px;">
                                        Accordé : <strong>{{ $solde->solde_initial }}j</strong>
                                    </span>
                                </div>
                            </div>
                            @if($solde->solde_restant === 0)
                                <div class="mt-2" style="font-size:10px;color:#991B1B;font-weight:600;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Solde épuisé
                                </div>
                            @elseif($pct < 25)
                                <div class="mt-2" style="font-size:10px;color:#D97706;font-weight:600;">
                                    <i class="fas fa-exclamation-circle me-1"></i>Solde faible
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-xmark fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            <small>Aucun solde disponible.<br>Contactez le service RH.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Historique détaillé --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header border-0 px-4 pt-3 pb-2 d-flex align-items-center justify-content-between" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-history me-2" style="color:#0A4D8C;"></i>
                        Historique de mes congés
                    </h6>
                    <span class="badge" style="background:#EFF6FF;color:#1E40AF;font-size:11px;padding:4px 10px;">
                        {{ $demandes->count() }} demande(s)
                    </span>
                </div>
                @if($demandes->isEmpty())
                    <div class="card-body text-center py-5 text-muted" style="background:var(--theme-panel-bg);">
                        <i class="fas fa-umbrella-beach fa-3x mb-3 d-block" style="opacity:.2;color:#0A4D8C;"></i>
                        <p class="mb-1 fw-500">Aucune demande de congé</p>
                        <small>Soumettez votre première demande</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table style="width:100%;border-collapse:separate;border-spacing:0;">
                            <thead>
                                <tr>
                                    <th>Type de congé</th>
                                    <th class="text-center">Période</th>
                                    <th class="text-center">Jours</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Date demande</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($demandes as $demande)
                                    @php
                                        $conge  = $demande->conge;
                                        $statut = $demande->statut_demande;
                                        $badgeClass = match($statut) {
                                            'En_attente' => 'badge-en_attente',
                                            'Validé'     => 'badge-valide',
                                            'Approuvé'   => 'badge-approuve',
                                            'Rejeté'     => 'badge-rejete',
                                            default      => 'badge-en_attente',
                                        };
                                        $icon = match($statut) {
                                            'En_attente' => 'fa-clock',
                                            'Validé'     => 'fa-user-check',
                                            'Approuvé'   => 'fa-check-double',
                                            'Rejeté'     => 'fa-times',
                                            default      => 'fa-clock',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-600 small" style="color:var(--theme-text);">
                                                {{ $conge->typeConge->libelle ?? 'Type inconnu' }}
                                            </div>
                                            @if($conge?->justificatif_path)
                                                <div style="font-size:10px;color:#10B981;">
                                                    <i class="fas fa-paperclip me-1"></i>Certificat joint
                                                </div>
                                            @endif
                                        </td>
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
                                        <td class="text-center">
                                            <span class="fw-bold" style="font-size:15px;color:
                                                {{ $statut === 'Approuvé' ? '#10B981' : ($statut === 'Rejeté' ? '#EF4444' : 'var(--theme-text)') }};">
                                                {{ $conge?->nbres_jours ?? '-' }}
                                            </span>
                                            @if($conge)
                                                <div style="font-size:10px;color:var(--theme-text-muted);">jour(s)</div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-statut {{ $badgeClass }}">
                                                <i class="fas {{ $icon }}" style="font-size:9px;"></i>
                                                {{ str_replace('_', ' ', $statut) }}
                                            </span>
                                            @if($statut === 'Approuvé' && $conge?->date_approbation)
                                                <div style="font-size:10px;color:var(--theme-text-muted);margin-top:2px;">
                                                    le {{ $conge->date_approbation->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="small" style="color:var(--theme-text);">
                                                {{ $demande->created_at->format('d/m/Y') }}
                                            </div>
                                            <div style="font-size:10px;color:var(--theme-text-muted);">
                                                {{ $demande->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('agent.conges.show', $demande->id_demande) }}"
                                                class="btn btn-sm"
                                                style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:6px;font-size:11px;color:var(--theme-text);padding:4px 10px;">
                                                <i class="fas fa-eye me-1"></i>Détail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Résumé des congés approuvés par type --}}
                    @php
                        $approuves = $demandes->where('statut_demande', 'Approuvé');
                        $parType = $approuves->groupBy(fn($d) => $d->conge?->typeConge?->libelle ?? 'Inconnu');
                    @endphp
                    @if($approuves->count() > 0)
                        <div class="px-4 py-3 border-top" style="background:var(--theme-bg-secondary);">
                            <div class="small fw-bold mb-2" style="color:var(--theme-text-muted);text-transform:uppercase;letter-spacing:.05em;font-size:10px;">
                                <i class="fas fa-chart-bar me-1"></i>Récapitulatif des congés pris
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($parType as $typeLabel => $groupe)
                                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);">
                                        <i class="fas fa-calendar-check" style="color:#10B981;font-size:13px;"></i>
                                        <div>
                                            <div style="font-size:11px;font-weight:600;color:var(--theme-text);">{{ $typeLabel }}</div>
                                            <div style="font-size:12px;color:#10B981;font-weight:700;">
                                                {{ $groupe->sum(fn($d) => $d->conge?->nbres_jours ?? 0) }} jour(s) pris
                                                <span style="color:var(--theme-text-muted);font-weight:400;">- {{ $groupe->count() }} congé(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
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
