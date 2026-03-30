@extends('layouts.master')
@section('title', 'Indicateur Absentéisme — DRH')
@section('page-title', 'Indicateur : Absentéisme')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Indicateurs</li>
    <li>Absentéisme</li>
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
                <i class="fas fa-user-clock me-2" style="color:#EF4444;"></i>Indicateur — Absentéisme
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Suivi des absences par type, service et période</p>
        </div>
        <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;border-color:#FECACA;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Absences ce mois</div>
                <div class="kpi-value" style="color:#DC2626;">{{ $absMonth }}</div>
                <div class="kpi-label">{{ now()->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FFFBEB;border-color:#FDE68A;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Taux absentéisme</div>
                <div class="kpi-value" style="color:#D97706;">{{ $tauxAbsMois }}%</div>
                <div class="kpi-label">Vs jours théoriques</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Total annuel</div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ array_sum($absParMois) }}</div>
                <div class="kpi-label">Absences sur 12 mois</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;border-color:#A7F3D0;">
                <div style="font-size:13px;font-weight:600;color:#374151;">Effectif total</div>
                <div class="kpi-value" style="color:#059669;">{{ $effectif }}</div>
                <div class="kpi-label">Agents actifs</div>
            </div>
        </div>
    </div>

    {{-- Chart évolution --}}
    <div class="chart-card mb-4">
        <div class="section-title">Évolution des absences (12 derniers mois)</div>
        <canvas id="chartAbsences" style="max-height:260px;"></canvas>
    </div>

    <div class="row g-3 mb-4">
        {{-- Types d'absence --}}
        <div class="col-12 col-lg-5">
            <div class="chart-card h-100">
                <div class="section-title">Répartition par type</div>
                <canvas id="chartTypes" style="max-height:240px;"></canvas>
            </div>
        </div>

        {{-- Top services --}}
        <div class="col-12 col-lg-7">
            <div class="chart-card h-100">
                <div class="section-title">Absences par service (ce mois)</div>
                @forelse($topServices as $svc)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="font-size:13px;font-weight:500;color:#374151;min-width:140px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $svc->nom_service }}
                    </div>
                    @php $max = $topServices->max('absences_count') ?: 1; $pct = round($svc->absences_count / $max * 100); @endphp
                    <div class="flex-grow-1" style="background:#F3F4F6;border-radius:4px;height:10px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $svc->absences_count > 5 ? '#EF4444' : '#F59E0B' }};border-radius:4px;"></div>
                    </div>
                    <span style="font-weight:700;color:#374151;width:28px;text-align:right;">{{ $svc->absences_count }}</span>
                </div>
                @empty
                <p class="text-muted" style="font-size:13px;">Aucune absence enregistrée ce mois.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartAbsences'), {
    type: 'bar',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Absences',
            data: @json($absParMois),
            backgroundColor: '#EF444433',
            borderColor: '#EF4444',
            borderWidth: 1.5,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#9CA3AF', font: { size: 10 } }, grid: { display: false } },
            y: { ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true, precision: 0 }
        }
    }
});

const typeLabels = @json($parType->keys()->values());
const typeData   = @json($parType->values());
new Chart(document.getElementById('chartTypes'), {
    type: 'doughnut',
    data: {
        labels: typeLabels,
        datasets: [{ data: typeData, backgroundColor: ['#EF4444','#F59E0B','#3B82F6','#8B5CF6','#10B981'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true, cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6B7280', padding: 8, boxWidth: 10 } } }
    }
});
</script>
@endpush
