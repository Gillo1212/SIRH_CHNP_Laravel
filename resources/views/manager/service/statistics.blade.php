@extends('layouts.master')

@section('title', 'Statistiques — ' . $service->nom_service)
@section('page-title', 'Statistiques du Service')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.service.index') }}" style="color:#1565C0;">Mon Service</a></li>
    <li>Statistiques</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ $service->nom_service }}</h4>
            <p class="text-muted small mb-0">Statistiques RH — {{ now()->isoFormat('MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('manager.service.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        @php
            $kpis = [
                ['val' => $stats['total_agents'],           'lbl' => 'Agents total',          'bg' => '#EFF6FF', 'color' => '#0A4D8C', 'icon' => 'fa-users'],
                ['val' => $stats['active_agents'],          'lbl' => 'Actifs',                'bg' => '#ECFDF5', 'color' => '#059669', 'icon' => 'fa-user-check'],
                ['val' => $stats['pending_leaves'],         'lbl' => 'Congés en attente',     'bg' => '#FFFBEB', 'color' => '#D97706', 'icon' => 'fa-clock'],
                ['val' => $stats['current_month_absences'], 'lbl' => 'Absences ce mois',      'bg' => '#FEF2F2', 'color' => '#DC2626', 'icon' => 'fa-user-minus'],
                ['val' => $stats['attendance_rate'] . '%',  'lbl' => 'Taux de présence',      'bg' => '#ECFDF5', 'color' => '#059669', 'icon' => 'fa-percentage'],
                ['val' => $stats['today_absences'],         'lbl' => 'Absents aujourd\'hui',  'bg' => '#FEF2F2', 'color' => '#DC2626', 'icon' => 'fa-calendar-times'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;background:{{ $kpi['bg'] }};">
                <i class="fas {{ $kpi['icon'] }} fa-lg mb-2" style="color:{{ $kpi['color'] }};"></i>
                <div style="font-size:22px;font-weight:700;color:{{ $kpi['color'] }};">{{ $kpi['val'] }}</div>
                <div class="text-muted small">{{ $kpi['lbl'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Graphique absences par mois --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Absences — 6 derniers mois</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartAbsences" style="max-height:220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Répartition statuts agents --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Répartition agents</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="chartStatuts" style="max-height:200px;"></canvas>
                    <div class="mt-3">
                        @php
                            $statutColors = ['actif' => '#059669', 'en_conge' => '#D97706', 'suspendu' => '#DC2626', 'retraite' => '#9CA3AF'];
                        @endphp
                        @foreach($agentsByStatut as $statut => $total)
                            <div class="d-flex align-items-center justify-content-between py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:10px;height:10px;border-radius:2px;background:{{ $statutColors[$statut] ?? '#9CA3AF' }};flex-shrink:0;"></div>
                                    <span class="small text-muted">{{ ucfirst($statut) }}</span>
                                </div>
                                <span class="fw-600 small">{{ $total }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Taux de présence visuel --}}
    <div class="card border-0 shadow-sm mt-4" style="border-radius:14px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Taux de présence — {{ now()->isoFormat('MMMM YYYY') }}</h6>
                <span style="font-size:24px;font-weight:700;color:{{ $stats['attendance_rate'] >= 90 ? '#059669' : ($stats['attendance_rate'] >= 75 ? '#D97706' : '#DC2626') }};">
                    {{ $stats['attendance_rate'] }}%
                </span>
            </div>
            <div class="progress" style="height:12px;border-radius:6px;">
                <div class="progress-bar" role="progressbar"
                     style="width:{{ $stats['attendance_rate'] }}%;border-radius:6px;background:{{ $stats['attendance_rate'] >= 90 ? '#059669' : ($stats['attendance_rate'] >= 75 ? '#D97706' : '#DC2626') }};">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <span class="text-muted small">0%</span>
                <span class="text-muted small">100%</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const absData = @json($absencesByMonth);
new Chart(document.getElementById('chartAbsences'), {
    type: 'bar',
    data: {
        labels: absData.map(d => d.label),
        datasets: [{
            label: 'Absences',
            data: absData.map(d => d.count),
            backgroundColor: 'rgba(217,119,6,0.15)',
            borderColor: '#D97706',
            borderWidth: 1.5,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
            y: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true }
        }
    }
});

const statuts = @json($agentsByStatut);
const colors  = { actif: '#059669', en_conge: '#D97706', suspendu: '#DC2626', retraite: '#9CA3AF' };
if (Object.keys(statuts).length > 0) {
    new Chart(document.getElementById('chartStatuts'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statuts).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
            datasets: [{
                data: Object.values(statuts),
                backgroundColor: Object.keys(statuts).map(s => colors[s] || '#9CA3AF'),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: { legend: { display: false } }
        }
    });
}
</script>
@endpush
