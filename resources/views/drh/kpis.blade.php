@extends('layouts.master')

@section('title', 'KPIs Stratégiques — DRH')
@section('page-title', 'KPIs Stratégiques')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>KPIs Stratégiques</li>
@endsection

@push('styles')
<style>
.kpi-section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9CA3AF;
    margin-bottom: 14px;
    padding-bottom: 6px;
    border-bottom: 1px solid #F3F4F6;
}
.kpi-big-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 24px;
    transition: box-shadow 200ms, transform 200ms;
}
.kpi-big-card:hover {
    box-shadow: 0 6px 20px rgba(10,77,140,0.08);
    transform: translateY(-2px);
}
.kpi-number {
    font-size: 36px;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}
.kpi-unit {
    font-size: 14px;
    font-weight: 600;
    color: #6B7280;
    margin-left: 4px;
}
.kpi-sublabel {
    font-size: 13px;
    color: #6B7280;
    margin-top: 4px;
}
.indicator-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #F3F4F6;
}
.indicator-row:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

{{-- En-tête --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">KPIs Stratégiques</h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            Vue détaillée des indicateurs clés de performance RH — {{ now()->isoFormat('MMMM YYYY') }}
        </p>
    </div>
    <a href="{{ route('drh.dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour
    </a>
</div>

{{-- KPIs Effectifs --}}
<div class="kpi-section-title">Effectifs & Structure</div>
<div class="row g-3 mb-4">
    @php
        try { $effectifTotal   = \App\Models\Agent::where('statut', 'actif')->count(); }  catch(\Exception $e) { $effectifTotal = 0; }
        try { $effectifFemmes  = \App\Models\Agent::where('statut', 'actif')->where('sexe','F')->count(); } catch(\Exception $e) { $effectifFemmes = 0; }
        try { $effectifHommes  = \App\Models\Agent::where('statut', 'actif')->where('sexe','M')->count(); } catch(\Exception $e) { $effectifHommes = 0; }
        $tauxFeminisation = $effectifTotal > 0 ? round(($effectifFemmes / $effectifTotal) * 100, 1) : 0;
    @endphp

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-big-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-users" style="color:#0A4D8C;font-size:18px;"></i>
                </div>
                <div style="font-size:13px;font-weight:600;color:#374151;">Effectif total</div>
            </div>
            <div><span class="kpi-number">{{ $effectifTotal }}</span><span class="kpi-unit">agents</span></div>
            <div class="kpi-sublabel">Personnel actif en poste</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-big-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;background:#FDF2F8;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-venus" style="color:#BE185D;font-size:18px;"></i>
                </div>
                <div style="font-size:13px;font-weight:600;color:#374151;">Taux de féminisation</div>
            </div>
            <div><span class="kpi-number">{{ $tauxFeminisation }}</span><span class="kpi-unit">%</span></div>
            <div class="kpi-sublabel">{{ $effectifFemmes }} femmes / {{ $effectifHommes }} hommes</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-big-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;background:#FEF3C7;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-user-clock" style="color:#D97706;font-size:18px;"></i>
                </div>
                <div style="font-size:13px;font-weight:600;color:#374151;">Taux d'absentéisme</div>
            </div>
            @php
                try {
                    $absences = \App\Models\Absence::whereMonth('date_absence', now()->month)
                        ->whereYear('date_absence', now()->year)->count();
                } catch(\Exception $e) { $absences = 0; }
                $tauxAbsenteisme = $effectifTotal > 0 ? round(($absences / max($effectifTotal,1)) * 100, 1) : 0;
            @endphp
            <div><span class="kpi-number">{{ $tauxAbsenteisme }}</span><span class="kpi-unit">%</span></div>
            <div class="kpi-sublabel">{{ $absences }} absence(s) ce mois</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-big-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-file-contract" style="color:#DC2626;font-size:18px;"></i>
                </div>
                <div style="font-size:13px;font-weight:600;color:#374151;">Contrats expirants</div>
            </div>
            @php
                try {
                    $contratsExpiring = \App\Models\Contrat::where('date_fin','<=',now()->addDays(60))
                        ->where('date_fin','>=',now())->where('statut_contrat','Actif')->count();
                } catch(\Exception $e) { $contratsExpiring = 0; }
            @endphp
            <div><span class="kpi-number">{{ $contratsExpiring }}</span><span class="kpi-unit">contrats</span></div>
            <div class="kpi-sublabel">À renouveler dans 60 jours</div>
        </div>
    </div>
</div>

{{-- Indicateurs par catégorie --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="kpi-big-card h-100">
            <div class="kpi-section-title">Répartition par catégorie</div>
            <canvas id="chartCategories" style="max-height:240px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="kpi-big-card h-100">
            <div class="kpi-section-title">Répartition par statut de contrat</div>
            <canvas id="chartStatuts" style="max-height:240px;"></canvas>
        </div>
    </div>
</div>

{{-- Indicateurs qualitatifs --}}
<div class="kpi-section-title">Indicateurs Qualitatifs</div>
<div class="kpi-big-card">
    @php
        try { $nServices = \App\Models\Service::count(); } catch(\Exception $e) { $nServices = 0; }
        try { $nDivisions = \App\Models\Division::count(); } catch(\Exception $e) { $nDivisions = 0; }
        try { $demandesEnAttente = \App\Models\Demande::whereIn('statut_demande',['En_attente','Validé'])->count(); } catch(\Exception $e) { $demandesEnAttente = 0; }
        try { $mouvements = \App\Models\Mouvement::whereMonth('created_at',now()->month)->count(); } catch(\Exception $e) { $mouvements = 0; }
    @endphp
    <div class="indicator-row">
        <div>
            <div style="font-size:14px;font-weight:600;color:#111827;">Nombre de services</div>
            <div style="font-size:12px;color:#9CA3AF;">Unités organisationnelles actives</div>
        </div>
        <div style="font-size:22px;font-weight:700;color:#0A4D8C;">{{ $nServices }}</div>
    </div>
    <div class="indicator-row">
        <div>
            <div style="font-size:14px;font-weight:600;color:#111827;">Nombre de divisions</div>
            <div style="font-size:12px;color:#9CA3AF;">Structure organisationnelle</div>
        </div>
        <div style="font-size:22px;font-weight:700;color:#0A4D8C;">{{ $nDivisions }}</div>
    </div>
    <div class="indicator-row">
        <div>
            <div style="font-size:14px;font-weight:600;color:#111827;">Demandes en attente</div>
            <div style="font-size:12px;color:#9CA3AF;">Toutes catégories confondues</div>
        </div>
        <div style="font-size:22px;font-weight:700;color:#D97706;">{{ $demandesEnAttente }}</div>
    </div>
    <div class="indicator-row">
        <div>
            <div style="font-size:14px;font-weight:600;color:#111827;">Mouvements ce mois</div>
            <div style="font-size:12px;color:#9CA3AF;">Affectations, mutations, départs</div>
        </div>
        <div style="font-size:22px;font-weight:700;color:#059669;">{{ $mouvements }}</div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = ['#0A4D8C','#1565C0','#059669','#D97706','#DC2626','#7C3AED','#0891B2','#BE185D','#65A30D'];

    new Chart(document.getElementById('chartCategories'), {
        type: 'bar',
        data: {
            labels: ['Cadre Sup.','Cadre Moy.','Tech. Sup.','Technicien','Ag. Admin.','Ag. Service','Commis','Ouvrier','Sans Dipl.'],
            datasets: [{
                label: 'Agents',
                data: [0,0,0,0,0,0,0,0,0],
                backgroundColor: colors.map(c => c + '33'),
                borderColor: colors,
                borderWidth: 1.5,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#9CA3AF', font: { size: 10 } }, grid: { display: false } },
                y: { ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('chartStatuts'), {
        type: 'doughnut',
        data: {
            labels: ['CDI','CDD','Stagiaire','Consultant'],
            datasets: [{
                data: [70, 20, 7, 3],
                backgroundColor: ['#0A4D8C','#1565C0','#D97706','#9CA3AF'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6B7280', padding: 10, boxWidth: 10 } }
            }
        }
    });
});
</script>
@endpush
