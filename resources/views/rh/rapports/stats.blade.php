@extends('layouts.master')
@section('title', 'Statistiques RH')
@section('page-title', 'Statistiques RH')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.rapports.index') }}" style="color:#1565C0;">Rapports</a></li>
    <li>Statistiques</li>
@endsection

@push('styles')
<style>
.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:16px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;}
.stat-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F9FAFB;}
.stat-row:last-child{border-bottom:none;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-chart-pie me-2" style="color:#7C3AED;"></i>Statistiques RH
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Indicateurs clés — {{ now()->isoFormat('MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('rh.rapports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Rapports
        </a>
    </div>

    {{-- Charts row 1 --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="section-card h-100">
                <div class="section-title">Évolution des absences (12 mois)</div>
                <canvas id="chartAbsences" style="max-height:260px;"></canvas>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="section-card h-100">
                <div class="section-title">Types d'absences (année)</div>
                <canvas id="chartAbsTypes" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Contrats --}}
        <div class="col-12 col-lg-4">
            <div class="section-card h-100">
                <div class="section-title">Contrats</div>
                @foreach([['actifs','Actifs','#059669'],['expiring','Expirant (60j)','#D97706'],['expires','Expirés','#EF4444'],['clotured','Clôturés','#6B7280']] as [$k,$l,$c])
                <div class="stat-row">
                    <div style="font-size:13px;color:#374151;">{{ $l }}</div>
                    <span style="font-weight:700;color:{{ $c }};font-size:18px;">{{ $statsContrats[$k] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Effectifs par service --}}
        <div class="col-12 col-lg-8">
            <div class="section-card h-100">
                <div class="section-title">Effectifs par service (top 10)</div>
                @foreach($effParService as $svc)
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="font-size:12px;color:#374151;min-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $svc->nom_service }}</div>
                    @php $max = $effParService->max('actifs') ?: 1; $pct = round($svc->actifs / $max * 100); @endphp
                    <div class="flex-grow-1" style="background:#F3F4F6;border-radius:4px;height:10px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:#0A4D8C;border-radius:4px;"></div>
                    </div>
                    <span style="font-weight:700;color:#0A4D8C;width:28px;text-align:right;">{{ $svc->actifs }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mouvements ce mois --}}
    <div class="section-card">
        <div class="section-title">Mouvements ce mois</div>
        @if($mouvMois->isNotEmpty())
        <div class="d-flex flex-wrap gap-2">
            @foreach($mouvMois as $type => $count)
            <div style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:12px 20px;text-align:center;">
                <div style="font-size:22px;font-weight:700;color:#0A4D8C;">{{ $count }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">{{ $type }}</div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-muted mb-0" style="font-size:13px;">Aucun mouvement ce mois.</p>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartAbsences'), {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Absences',
            data: @json($absParMois),
            borderColor: '#EF4444',
            backgroundColor: '#EF444422',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointRadius: 4,
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

const absTypeLabels = @json($absByType->keys()->values());
const absTypeData   = @json($absByType->values());
new Chart(document.getElementById('chartAbsTypes'), {
    type: 'doughnut',
    data: {
        labels: absTypeLabels,
        datasets: [{ data: absTypeData, backgroundColor: ['#EF4444','#F59E0B','#3B82F6','#8B5CF6','#10B981'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true, cutout: '55%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6B7280', padding: 8, boxWidth: 10 } } }
    }
});
</script>
@endpush
