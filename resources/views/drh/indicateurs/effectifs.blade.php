@extends('layouts.master')
@section('title', 'Indicateur Effectifs — DRH')
@section('page-title', 'Indicateur : Effectifs')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Indicateurs</li>
    <li>Effectifs</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden;}
.kpi-card:hover{box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px);}
.kpi-card .kpi-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.kpi-card .kpi-value{font-size:28px;font-weight:700;line-height:1.1;margin-top:10px;}
.kpi-card .kpi-label{font-size:13px;color:#6B7280;margin-top:4px;}
.kpi-card::before{content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07;}
.kpi-card.blue::before{background:#0A4D8C;} .kpi-card.pink::before{background:#BE185D;} .kpi-card.green::before{background:#059669;} .kpi-card.amber::before{background:#D97706;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:14px;padding-bottom:6px;border-bottom:1px solid #F3F4F6;}
.chart-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;}
.nav-pills-sirh .nav-link{border-radius:8px;font-size:13px;font-weight:500;color:#6B7280;padding:7px 14px;}
.nav-pills-sirh .nav-link.active{background:#0A4D8C;color:#fff;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-users me-2" style="color:#0A4D8C;"></i>Indicateur — Effectifs
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Vue détaillée des effectifs du CHNP — {{ now()->isoFormat('MMMM YYYY') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Tableau de bord
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card blue" style="background:#EFF6FF;border:1px solid #DBEAFE;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-users" style="color:#0A4D8C;"></i></div>
                    <div style="font-size:13px;font-weight:600;color:#374151;">Effectif total actif</div>
                </div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $effectifTotal }}</div>
                <div class="kpi-label">Agents en poste</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card pink" style="background:#FDF2F8;border:1px solid #FBCFE8;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#FBCFE8;"><i class="fas fa-venus" style="color:#BE185D;"></i></div>
                    <div style="font-size:13px;font-weight:600;color:#374151;">Taux féminisation</div>
                </div>
                <div class="kpi-value" style="color:#BE185D;">{{ $tauxFeminis }}%</div>
                <div class="kpi-label">{{ $effectifFemmes }} femmes / {{ $effectifHommes }} hommes</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card green" style="background:#ECFDF5;border:1px solid #A7F3D0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#A7F3D0;"><i class="fas fa-building" style="color:#059669;"></i></div>
                    <div style="font-size:13px;font-weight:600;color:#374151;">Services actifs</div>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $parService->count() }}</div>
                <div class="kpi-label">Unités organisationnelles</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card amber" style="background:#FFFBEB;border:1px solid #FDE68A;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#FDE68A;"><i class="fas fa-user-plus" style="color:#D97706;"></i></div>
                    <div style="font-size:13px;font-weight:600;color:#374151;">Recrutés ce mois</div>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $recrutementsParMois[11] ?? 0 }}</div>
                <div class="kpi-label">{{ now()->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="chart-card">
                <div class="section-title">Recrutements par mois (12 derniers mois)</div>
                <canvas id="chartRecrutements" style="max-height:260px;"></canvas>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="chart-card">
                <div class="section-title">Répartition par catégorie</div>
                <canvas id="chartCategories" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Effectifs par service --}}
    <div class="chart-card mb-4">
        <div class="section-title">Effectifs par service</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:13px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="border-0 py-2 px-3">Service</th>
                        <th class="border-0 py-2 px-3">Division</th>
                        <th class="border-0 py-2 px-3 text-center">Agents actifs</th>
                        <th class="border-0 py-2 px-3">Répartition</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parService as $svc)
                    <tr>
                        <td class="py-2 px-3 border-0 fw-600">{{ $svc->nom_service }}</td>
                        <td class="py-2 px-3 border-0 text-muted">{{ $svc->divisions_count ?? '—' }}</td>
                        <td class="py-2 px-3 border-0 text-center">
                            <span style="font-weight:700;color:#0A4D8C;">{{ $svc->actifs_count }}</span>
                        </td>
                        <td class="py-2 px-3 border-0" style="width:180px;">
                            @php $pct = $effectifTotal > 0 ? round($svc->actifs_count / $effectifTotal * 100) : 0; @endphp
                            <div style="background:#E5E7EB;border-radius:4px;height:8px;overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:#0A4D8C;border-radius:4px;"></div>
                            </div>
                            <small class="text-muted">{{ $pct }}%</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Répartition par statut --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="section-title">Répartition par statut</div>
                @foreach($parStatut as $statut => $count)
                @php
                    $colors = ['actif'=>'#059669','en_conge'=>'#3B82F6','suspendu'=>'#EF4444','retraite'=>'#6B7280'];
                    $color  = $colors[$statut] ?? '#9CA3AF';
                    $pct    = ($parStatut->sum() > 0) ? round($count / $parStatut->sum() * 100) : 0;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:10px;height:10px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></div>
                    <div style="font-size:13px;color:#374151;flex:1;">{{ ucfirst(str_replace('_',' ', $statut)) }}</div>
                    <div style="font-weight:700;color:{{ $color }};">{{ $count }}</div>
                    <div style="font-size:12px;color:#9CA3AF;width:40px;text-align:right;">{{ $pct }}%</div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="section-title">Situation familiale</div>
                @foreach($parSitFam as $sit => $count)
                @php $pct = ($parSitFam->sum() > 0) ? round($count / $parSitFam->sum() * 100) : 0; @endphp
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="font-size:13px;color:#374151;">{{ $sit }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="font-weight:700;color:#0A4D8C;">{{ $count }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">({{ $pct }}%)</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels12  = @json($labelsParMois);
const data12    = @json($recrutementsParMois);
const catLabels = @json($parCategorie->keys()->values());
const catData   = @json($parCategorie->values());

new Chart(document.getElementById('chartRecrutements'), {
    type: 'bar',
    data: {
        labels: labels12,
        datasets: [{
            label: 'Recrutements',
            data: data12,
            backgroundColor: '#0A4D8C33',
            borderColor: '#0A4D8C',
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

const palette = ['#0A4D8C','#1565C0','#059669','#D97706','#EF4444','#7C3AED','#0891B2','#BE185D','#65A30D'];
new Chart(document.getElementById('chartCategories'), {
    type: 'doughnut',
    data: {
        labels: catLabels.map(l => l.replace('_',' ')),
        datasets: [{ data: catData, backgroundColor: palette, borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true, cutout: '55%',
        plugins: { legend: { position: 'right', labels: { font: { size: 11 }, color: '#6B7280', padding: 8, boxWidth: 10 } } }
    }
});
</script>
@endpush
