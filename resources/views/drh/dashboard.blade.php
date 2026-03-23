@extends('layouts.master')

@section('title', 'Dashboard DRH')
@section('page-title', 'Direction des Ressources Humaines')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color: #1565C0;">Tableau de bord DRH</a></li>
@endsection

@push('styles')
<style>
.kpi-card {
    border-radius: 12px;
    padding: 20px 24px;
    transition: box-shadow 200ms, transform 200ms;
    position: relative;
    overflow: hidden;
}
.kpi-card:hover {
    box-shadow: 0 6px 20px rgba(10, 77, 140, 0.10);
    transform: translateY(-2px);
}
.kpi-card .kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.kpi-card .kpi-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1.1;
    margin-top: 12px;
}
.kpi-card .kpi-label {
    font-size: 13px;
    margin-top: 2px;
    font-weight: 500;
}
.kpi-card .kpi-trend {
    font-size: 12px;
    font-weight: 600;
    margin-top: 6px;
}
.kpi-card .kpi-trend.up { color: #10B981; }
.kpi-card .kpi-trend.down { color: #EF4444; }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 80px; height: 80px;
    border-radius: 0 12px 0 80px;
    opacity: 0.07;
}
.kpi-card.blue::before   { background: #0A4D8C; }
.kpi-card.green::before  { background: #059669; }
.kpi-card.amber::before  { background: #D97706; }
.kpi-card.red::before    { background: #DC2626; }
.kpi-card.purple::before { background: #7C3AED; }

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 180ms;
}
.action-btn-primary {
    background: #0A4D8C;
    color: white;
}
.action-btn-primary:hover {
    background: #1565C0;
    color: white;
    box-shadow: 0 4px 12px rgba(10, 77, 140, 0.3);
    transform: translateY(-1px);
}

.section-title-drh {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 12px;
    padding-bottom: 6px;
}
.alert-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
}
.alert-item:last-child { border-bottom: none !important; }
.alert-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
.decision-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
}
.decision-row:last-child { border-bottom: none !important; }
.badge-status {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-pending { background: #FEF3C7; color: #92400E; }
.badge-urgent  { background: #FEE2E2; color: #991B1B; }
</style>
@endpush

@section('content')

{{-- ─── EN-TÊTE ──────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-700" style="color: #111827;">
            Bonjour, {{ Auth::user()->agent->prenom ?? 'Directeur' }} 👋
        </h4>
        <p class="mb-0 text-muted" style="font-size: 13.5px;">
            {{ now()->isoFormat('dddd D MMMM YYYY') }} — Direction des Ressources Humaines
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('drh.rapports.bilan') }}" class="action-btn action-btn-outline">
            <i class="fas fa-file-export"></i> Bilan social
        </a>
        <a href="{{ route('drh.decisions.index') }}" class="action-btn action-btn-primary">
            <i class="fas fa-check-double"></i> Valider décisions
            {{-- badge dynamique --}}
        </a>
    </div>
</div>

{{-- ─── KPIs STRATÉGIQUES (LIGNE 1) ─────────────────────────── --}}
<div class="section-title-drh">KPIs Stratégiques</div>
<div class="row g-3 mb-4">

    {{-- Masse salariale mensuelle --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background: #EFF6FF;">
                    <i class="fas fa-coins" style="color: #0A4D8C;"></i>
                </div>
                <span class="badge-status badge-info">Mensuel</span>
            </div>
            <div class="kpi-value">— XOF</div>
            <div class="kpi-label">Masse salariale</div>
            <div class="kpi-trend neutral"><i class="fas fa-minus me-1"></i>Données à configurer</div>
        </div>
    </div>

    {{-- Effectif total --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background: #ECFDF5;">
                    <i class="fas fa-users" style="color: #059669;"></i>
                </div>
                <span class="badge-status" style="background:#ECFDF5; color:#065F46; font-size:11px; font-weight:600; padding:2px 10px; border-radius:20px;">Actifs</span>
            </div>
            @php
                try { $effectifTotal = \App\Models\Agent::where('statut', 'actif')->count(); }
                catch (\Exception $e) { $effectifTotal = 0; }
            @endphp
            <div class="kpi-value">{{ $effectifTotal }}</div>
            <div class="kpi-label">Effectif total actif</div>
            <div class="kpi-trend up"><i class="fas fa-arrow-up me-1"></i>Personnel en poste</div>
        </div>
    </div>

    {{-- Taux d'absentéisme --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background: #FFFBEB;">
                    <i class="fas fa-user-clock" style="color: #D97706;"></i>
                </div>
                <span class="badge-status" style="background:#FEF3C7; color:#92400E; font-size:11px; font-weight:600; padding:2px 10px; border-radius:20px;">Ce mois</span>
            </div>
            @php
                try {
                    $absencesMonth = \App\Models\Absence::whereMonth('date_absence', now()->month)
                        ->whereYear('date_absence', now()->year)->count();
                } catch (\Exception $e) { $absencesMonth = 0; }
                $tauxAbsenteisme = $effectifTotal > 0 ? round(($absencesMonth / max($effectifTotal, 1)) * 100, 1) : 0;
            @endphp
            <div class="kpi-value">{{ $tauxAbsenteisme }}%</div>
            <div class="kpi-label">Taux d'absentéisme</div>
            <div class="kpi-trend neutral"><i class="fas fa-calendar-times me-1"></i>{{ $absencesMonth }} absence(s) ce mois</div>
        </div>
    </div>

    {{-- Coût moyen par agent --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background: #F5F3FF;">
                    <i class="fas fa-calculator" style="color: #7C3AED;"></i>
                </div>
                <span class="badge-status" style="background:#F5F3FF; color:#4C1D95; font-size:11px; font-weight:600; padding:2px 10px; border-radius:20px;">Estimé</span>
            </div>
            <div class="kpi-value">— XOF</div>
            <div class="kpi-label">Coût moyen / agent</div>
            <div class="kpi-trend neutral"><i class="fas fa-info-circle me-1"></i>Données à configurer</div>
        </div>
    </div>
</div>

{{-- ─── KPIs OPÉRATIONNELS (LIGNE 2) ───────────────────────── --}}
<div class="section-title-drh">KPIs Opérationnels</div>
<div class="row g-3 mb-4">

    {{-- Contrats à renouveler --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card red">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background: #FEF2F2;">
                    <i class="fas fa-file-contract" style="color: #DC2626;"></i>
                </div>
                <div>
                    @php
                        try {
                            $contratsExpiring = \App\Models\Contrat::where('date_fin', '<=', now()->addDays(60))
                                ->where('date_fin', '>=', now())
                                ->where('statut_contrat', 'Actif')->count();
                        } catch (\Exception $e) { $contratsExpiring = 0; }
                    @endphp
                    <div class="kpi-value" style="font-size:22px;">{{ $contratsExpiring }}</div>
                    <div class="kpi-label">Contrats à renouveler <br><small>(< 60 jours)</small></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Demandes en attente --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background: #FFFBEB;">
                    <i class="fas fa-hourglass-half" style="color: #D97706;"></i>
                </div>
                <div>
                    @php
                        try {
                            $demandesEnAttente = \App\Models\Demande::whereIn('statut_demande', ['En_attente', 'Validé'])->count();
                        } catch (\Exception $e) { $demandesEnAttente = 0; }
                    @endphp
                    <div class="kpi-value" style="font-size:22px;">{{ $demandesEnAttente }}</div>
                    <div class="kpi-label">Demandes en attente<br><small>(toutes catégories)</small></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mouvements du mois --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background: #EFF6FF;">
                    <i class="fas fa-exchange-alt" style="color: #0A4D8C;"></i>
                </div>
                <div>
                    @php
                        try {
                            $mouvementsMois = \App\Models\Mouvement::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count();
                        } catch (\Exception $e) { $mouvementsMois = 0; }
                    @endphp
                    <div class="kpi-value" style="font-size:22px;">{{ $mouvementsMois }}</div>
                    <div class="kpi-label">Mouvements<br><small>ce mois</small></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Prises en charge en cours --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card green">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background: #ECFDF5;">
                    <i class="fas fa-heartbeat" style="color: #059669;"></i>
                </div>
                <div>
                    @php
                        try {
                            $pecEnCours = \App\Models\PriseEnCharge::where('statut', 'En_cours')->count();
                        } catch (\Exception $e) { $pecEnCours = 0; }
                    @endphp
                    <div class="kpi-value" style="font-size:22px;">{{ $pecEnCours }}</div>
                    <div class="kpi-label">Prises en charge<br><small>en cours</small></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── GRAPHIQUES + TABLEAUX ────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Évolution effectifs 12 mois --}}
    <div class="col-12 col-lg-8">
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;">Évolution des effectifs</div>
                    <div style="font-size:12px; color:#9CA3AF;">12 derniers mois</div>
                </div>
                <span class="badge-status badge-info">Actifs</span>
            </div>
            <canvas id="chartEffectifs" style="max-height: 240px;"></canvas>
        </div>
    </div>

    {{-- Répartition par type de contrat --}}
    <div class="col-12 col-lg-4">
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px; height:100%;">
            <div class="fw-600 mb-1" style="color:#111827;">Types de contrats</div>
            <div style="font-size:12px; color:#9CA3AF; margin-bottom:12px;">Répartition actuelle</div>
            <canvas id="chartContrats" style="max-height: 200px;"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    {{-- Absentéisme par service --}}
    <div class="col-12 col-lg-6">
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px;">
            <div class="fw-600 mb-1" style="color:#111827;">Absentéisme par service</div>
            <div style="font-size:12px; color:#9CA3AF; margin-bottom:12px;">Ce mois</div>
            <canvas id="chartAbsenteisme" style="max-height: 220px;"></canvas>
        </div>
    </div>

    {{-- Pyramide des âges --}}
    <div class="col-12 col-lg-6">
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px;">
            <div class="fw-600 mb-1" style="color:#111827;">Pyramide des âges</div>
            <div style="font-size:12px; color:#9CA3AF; margin-bottom:12px;">Tranches d'âge</div>
            <canvas id="chartAges" style="max-height: 220px;"></canvas>
        </div>
    </div>
</div>

{{-- ─── DÉCISIONS + ALERTES ─────────────────────────────────── --}}
<div class="row g-3">

    {{-- Décisions en attente --}}
    <div class="col-12 col-lg-6">
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Décisions en attente</div>
                <a href="{{ route('drh.decisions.index') }}" style="font-size:12px; color:#1565C0; text-decoration:none; font-weight:500;">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div style="color:#9CA3AF; text-align:center; padding:24px 0; font-size:13px;">
                <i class="fas fa-check-double fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                Aucune décision en attente de signature
            </div>
        </div>
    </div>

    {{-- Alertes RH --}}
    <div class="col-12 col-lg-6">
        <div style="background:#fff; border:1px solid #E5E7EB; border-radius:12px; padding:20px;">
            <div class="fw-600 mb-3" style="color:#111827;">Alertes RH</div>

            @if($contratsExpiring > 0)
            <div class="alert-item">
                <div class="alert-dot" style="background:#EF4444;"></div>
                <div>
                    <div style="font-size:13px; font-weight:500; color:#111827;">
                        {{ $contratsExpiring }} contrat(s) expirent dans 60 jours
                    </div>
                    <a href="#" style="font-size:12px; color:#1565C0;">Voir les contrats →</a>
                </div>
            </div>
            @endif

            @if($demandesEnAttente > 0)
            <div class="alert-item">
                <div class="alert-dot" style="background:#F59E0B;"></div>
                <div>
                    <div style="font-size:13px; font-weight:500; color:#111827;">
                        {{ $demandesEnAttente }} demande(s) en attente de traitement
                    </div>
                    <a href="#" style="font-size:12px; color:#1565C0;">Voir les demandes →</a>
                </div>
            </div>
            @endif

            @if($contratsExpiring === 0 && $demandesEnAttente === 0)
            <div style="color:#9CA3AF; text-align:center; padding:24px 0; font-size:13px;">
                <i class="fas fa-shield-alt fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                Aucune alerte critique
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ─── ACTIONS RAPIDES ─────────────────────────────────────── --}}
<div class="quick-actions-panel" style="margin-top: 24px;">
    <div class="fw-600 mb-3" style="color: #0A4D8C;">Actions rapides</div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('drh.decisions.index') }}" class="action-btn action-btn-primary">
            <i class="fas fa-signature"></i> Valider décisions en attente
        </a>
        <a href="{{ route('drh.rapports.bilan') }}" class="action-btn action-btn-outline">
            <i class="fas fa-file-chart-line"></i> Générer bilan social
        </a>
        <a href="{{ route('drh.kpis') }}" class="action-btn action-btn-outline">
            <i class="fas fa-tachometer-alt"></i> KPIs détaillés
        </a>
        <a href="{{ route('drh.budget') }}" class="action-btn action-btn-outline">
            <i class="fas fa-coins"></i> Suivi budgétaire
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Palette CHNP ──────────────────────────────
    const colors = {
        primary: '#0A4D8C',
        secondary: '#1565C0',
        light: '#93C5FD',
        green: '#059669',
        amber: '#D97706',
        red: '#DC2626',
        purple: '#7C3AED',
        grid: '#F3F4F6',
        text: '#9CA3AF',
    };

    // ── Graphique 1 : Évolution effectifs 12 mois ─
    const labelsMonths = [];
    const now = new Date();
    for (let i = 11; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        labelsMonths.push(d.toLocaleDateString('fr-FR', { month: 'short', year: '2-digit' }));
    }
    new Chart(document.getElementById('chartEffectifs'), {
        type: 'line',
        data: {
            labels: labelsMonths,
            datasets: [{
                label: 'Effectif actif',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, {{ $effectifTotal }}],
                borderColor: colors.primary,
                backgroundColor: 'rgba(10, 77, 140, 0.06)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: colors.primary,
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } } },
                y: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } }, beginAtZero: true }
            }
        }
    });

    // ── Graphique 2 : Répartition contrats (donut) ─
    new Chart(document.getElementById('chartContrats'), {
        type: 'doughnut',
        data: {
            labels: ['CDI', 'CDD', 'Stage', 'Autre'],
            datasets: [{
                data: [60, 25, 10, 5],
                backgroundColor: [colors.primary, colors.secondary, colors.amber, colors.light],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, color: colors.text, padding: 10, boxWidth: 10 }
                }
            }
        }
    });

    // ── Graphique 3 : Absentéisme par service ─────
    new Chart(document.getElementById('chartAbsenteisme'), {
        type: 'bar',
        data: {
            labels: ['Médecine', 'Chirurgie', 'Urgences', 'Administratif', 'RH'],
            datasets: [{
                label: 'Absences',
                data: [0, 0, 0, 0, 0],
                backgroundColor: 'rgba(217, 119, 6, 0.15)',
                borderColor: colors.amber,
                borderWidth: 1.5,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11 } } },
                y: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } }, beginAtZero: true }
            }
        }
    });

    // ── Graphique 4 : Pyramide des âges (bar horizontal) ─
    new Chart(document.getElementById('chartAges'), {
        type: 'bar',
        data: {
            labels: ['< 25 ans', '25-35 ans', '36-45 ans', '46-55 ans', '> 55 ans'],
            datasets: [{
                label: 'Effectif',
                data: [0, 0, 0, 0, 0],
                backgroundColor: [
                    'rgba(10, 77, 140, 0.15)',
                    'rgba(10, 77, 140, 0.30)',
                    'rgba(10, 77, 140, 0.50)',
                    'rgba(10, 77, 140, 0.70)',
                    'rgba(10, 77, 140, 0.90)',
                ],
                borderColor: colors.primary,
                borderWidth: 1.5,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } }, beginAtZero: true },
                y: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11 } } }
            }
        }
    });
});
</script>
@endpush
