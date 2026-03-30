@extends('layouts.master')
@section('title', 'Indicateur Turnover — DRH')
@section('page-title', 'Indicateur : Turnover')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Indicateurs</li>
    <li>Turnover</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:20px 24px;border:1px solid;transition:box-shadow 200ms,transform 200ms;}
.kpi-card:hover{box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px);}
.kpi-value{font-size:28px;font-weight:700;line-height:1.1;margin-top:10px;}
.kpi-label{font-size:13px;color:#6B7280;margin-top:4px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:14px;padding-bottom:6px;border-bottom:1px solid #F3F4F6;}
.chart-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-exchange-alt me-2" style="color:#D97706;"></i>Indicateur — Turnover
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Analyse des entrées et sorties du personnel — 12 derniers mois</p>
        </div>
        <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FFFBEB;border-color:#FDE68A;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Taux de turnover</div>
                <div class="kpi-value" style="color:#D97706;">{{ $tauxTurnover }}%</div>
                <div class="kpi-label">Sur 12 mois</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Recrutements</div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $totalRecrutements }}</div>
                <div class="kpi-label">Entrées sur 12 mois</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;border-color:#FECACA;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Départs</div>
                <div class="kpi-value" style="color:#DC2626;">{{ $totalDeparts }}</div>
                <div class="kpi-label">Sorties sur 12 mois</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;border-color:#A7F3D0;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Effectif actuel</div>
                <div class="kpi-value" style="color:#059669;">{{ $effectif }}</div>
                <div class="kpi-label">Agents actifs</div>
            </div>
        </div>
    </div>

    {{-- Chart évolution --}}
    <div class="chart-card mb-4">
        <div class="section-title">Évolution recrutements vs départs (12 mois)</div>
        <canvas id="chartTurnover" style="max-height:280px;"></canvas>
    </div>

    <div class="row g-3">
        {{-- Motifs départ --}}
        <div class="col-12 col-lg-5">
            <div class="chart-card h-100">
                <div class="section-title">Principaux motifs de départ</div>
                @forelse($motifs as $m)
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #F3F4F6;">
                    <div style="font-size:13px;color:#374151;">{{ $m->motif ?: 'Non précisé' }}</div>
                    <span style="background:#FEF3C7;color:#92400E;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ $m->total }}</span>
                </div>
                @empty
                <p class="text-muted" style="font-size:13px;">Aucun départ enregistré.</p>
                @endforelse
            </div>
        </div>

        {{-- Départs récents --}}
        <div class="col-12 col-lg-7">
            <div class="chart-card h-100">
                <div class="section-title">Départs récents</div>
                @forelse($departsRecents as $d)
                <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid #F3F4F6;">
                    <div style="width:36px;height:36px;background:#FEE2E2;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-times" style="color:#DC2626;font-size:13px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size:13px;font-weight:600;color:var(--theme-text);">
                            {{ $d->agent->nom_complet ?? '—' }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">
                            {{ $d->agent->service->nom_service ?? '—' }} · {{ $d->date_mouvement?->format('d/m/Y') }}
                        </div>
                    </div>
                    <span style="background:#FEE2E2;color:#991B1B;padding:2px 10px;border-radius:20px;font-size:11px;">{{ $d->motif }}</span>
                </div>
                @empty
                <p class="text-muted" style="font-size:13px;">Aucun départ récent.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartTurnover'), {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [
            {
                label: 'Recrutements',
                data: @json($recrutementsParMois),
                borderColor: '#0A4D8C',
                backgroundColor: '#0A4D8C22',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
            },
            {
                label: 'Départs',
                data: @json($departsParMois),
                borderColor: '#DC2626',
                backgroundColor: '#DC262622',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { font: { size: 12 }, color: '#6B7280', padding: 12 } } },
        scales: {
            x: { ticks: { color: '#9CA3AF', font: { size: 10 } }, grid: { display: false } },
            y: { ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true, precision: 0 }
        }
    }
});
</script>
@endpush
