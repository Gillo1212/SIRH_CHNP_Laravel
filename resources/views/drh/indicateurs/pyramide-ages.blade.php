@extends('layouts.master')
@section('title', 'Pyramide des âges — DRH')
@section('page-title', 'Indicateur : Pyramide des âges')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Indicateurs</li>
    <li>Pyramide des âges</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:20px 24px;border:1px solid;transition:box-shadow 200ms,transform 200ms;}
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
                <i class="fas fa-chart-bar me-2" style="color:#7C3AED;"></i>Pyramide des âges
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Répartition du personnel par tranches d'âge</p>
        </div>
        <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#F5F3FF;border-color:#DDD6FE;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Âge moyen</div>
                <div class="kpi-value" style="color:#7C3AED;">{{ $ageMoyen }} ans</div>
                <div class="kpi-label">Personnel actif</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;border-color:#FECACA;">
                <div style="font-size:13px;font-weight:600;color:#374151;">≥ 55 ans</div>
                <div class="kpi-value" style="color:#DC2626;">{{ $agents55Plus->count() }}</div>
                <div class="kpi-label">Agents proches retraite</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;border-color:#A7F3D0;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Tranche dominante</div>
                @php $maxTranche = collect($tranches)->sortByDesc(fn($v) => $v[0])->keys()->first(); @endphp
                <div class="kpi-value" style="color:#059669;">{{ $maxTranche }}</div>
                <div class="kpi-label">{{ collect($tranches)->max(fn($v) => $v[0]) }} agents</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Âge médian</div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $ageMedian }} ans</div>
                <div class="kpi-label">Médiane</div>
            </div>
        </div>
    </div>

    {{-- Pyramide chart --}}
    <div class="chart-card mb-4">
        <div class="section-title">Distribution par tranche d'âge</div>
        <canvas id="chartPyramide" style="max-height:300px;"></canvas>
    </div>

    {{-- Tableau détaillé --}}
    <div class="chart-card mb-4">
        <div class="section-title">Détail par tranche d'âge</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:13px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="border-0 py-2 px-3">Tranche</th>
                        <th class="border-0 py-2 px-3 text-center">Nombre d'agents</th>
                        <th class="border-0 py-2 px-3">Répartition</th>
                        <th class="border-0 py-2 px-3 text-center">%</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = array_sum(array_column($tranches, 0)); @endphp
                    @foreach($tranches as $label => [$count, $color])
                    @php $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                    <tr>
                        <td class="py-2 px-3 border-0 fw-600">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $color }};margin-right:8px;"></span>
                            {{ $label }}
                        </td>
                        <td class="py-2 px-3 border-0 text-center" style="font-weight:700;color:{{ $color }};">{{ $count }}</td>
                        <td class="py-2 px-3 border-0" style="width:200px;">
                            <div style="background:#F3F4F6;border-radius:4px;height:8px;overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:4px;"></div>
                            </div>
                        </td>
                        <td class="py-2 px-3 border-0 text-center text-muted">{{ $pct }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Agents proches retraite --}}
    @if($agents55Plus->isNotEmpty())
    <div class="chart-card">
        <div class="section-title" style="color:#DC2626;">Agents de 55 ans et plus — Anticipation départs retraite</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:13px;">
                <thead>
                    <tr style="background:#FEF2F2;">
                        <th class="border-0 py-2 px-3">Agent</th>
                        <th class="border-0 py-2 px-3">Service</th>
                        <th class="border-0 py-2 px-3 text-center">Date naissance</th>
                        <th class="border-0 py-2 px-3 text-center">Âge</th>
                        <th class="border-0 py-2 px-3">Fonction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents55Plus as $agent)
                    <tr>
                        <td class="py-2 px-3 border-0 fw-600">{{ $agent->nom_complet }}</td>
                        <td class="py-2 px-3 border-0 text-muted">{{ $agent->service->nom_service ?? '—' }}</td>
                        <td class="py-2 px-3 border-0 text-center text-muted">{{ $agent->date_naissance?->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 border-0 text-center">
                            @php $age = $agent->date_naissance?->diffInYears(now()); @endphp
                            <span style="font-weight:700;color:{{ $age >= 60 ? '#DC2626' : '#D97706' }};">{{ $age }} ans</span>
                        </td>
                        <td class="py-2 px-3 border-0 text-muted">{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@php
$tranchesChart = collect($tranches)->map(function($v, $k) {
    return ['label' => $k, 'count' => $v[0], 'color' => $v[1]];
})->values();
@endphp
const tranches = @json($tranchesChart);

new Chart(document.getElementById('chartPyramide'), {
    type: 'bar',
    data: {
        labels: tranches.map(t => t.label),
        datasets: [{
            label: 'Agents',
            data: tranches.map(t => t.count),
            backgroundColor: tranches.map(t => t.color + '55'),
            borderColor: tranches.map(t => t.color),
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#374151', font: { size: 12 } }, grid: { display: false } },
            y: { ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true, precision: 0 }
        }
    }
});
</script>
@endpush
