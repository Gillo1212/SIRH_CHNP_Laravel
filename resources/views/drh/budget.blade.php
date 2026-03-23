@extends('layouts.master')

@section('title', 'Suivi Budgétaire RH — DRH')
@section('page-title', 'Suivi Budgétaire RH')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>Suivi Budgétaire</li>
@endsection

@push('styles')
<style>
.budget-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 22px 24px;
    transition: box-shadow 180ms, transform 180ms;
}
.budget-card:hover {
    box-shadow: 0 6px 20px rgba(10,77,140,0.08);
    transform: translateY(-2px);
}
.section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9CA3AF;
    margin-bottom: 14px;
    padding-bottom: 6px;
    border-bottom: 1px solid #F3F4F6;
}
.budget-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #F9FAFB;
}
.budget-row:last-child { border-bottom: none; }
.progress-bar-custom {
    height: 6px;
    border-radius: 3px;
    background: #E5E7EB;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, #0A4D8C, #1565C0);
    transition: width 800ms ease;
}
</style>
@endpush

@section('content')

{{-- En-tête --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">Suivi Budgétaire RH</h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            Tableau de bord budgétaire de la Direction des Ressources Humaines — {{ now()->isoFormat('MMMM YYYY') }}
        </p>
    </div>
    <a href="{{ route('drh.dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour
    </a>
</div>

{{-- Alerte configuration --}}
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-info-circle"></i>
        <span>
            Les données salariales nécessitent la configuration du module de paie.
            Seules les données structurelles (effectifs, contrats) sont disponibles pour l'instant.
        </span>
    </div>
</div>

{{-- KPIs budgétaires --}}
<div class="section-label">Indicateurs budgétaires</div>
<div class="row g-3 mb-4">
    @php
        try { $effectifTotal = \App\Models\Agent::where('statut', 'actif')->count(); } catch(\Exception $e) { $effectifTotal = 0; }
        try {
            $contratsActifs = \App\Models\Contrat::where('statut_contrat','Actif')->count();
            $contratsCDD    = \App\Models\Contrat::where('statut_contrat','Actif')->where('type_contrat','CDD')->count();
            $contratsCDI    = \App\Models\Contrat::where('statut_contrat','Actif')->where('type_contrat','CDI')->count();
        } catch(\Exception $e) { $contratsActifs = 0; $contratsCDD = 0; $contratsCDI = 0; }
    @endphp

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="budget-card text-center">
            <div style="font-size:13px;font-weight:600;color:#6B7280;margin-bottom:8px;">Masse salariale estimée</div>
            <div style="font-size:28px;font-weight:700;color:#111827;">— XOF</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">Module paie requis</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="budget-card text-center">
            <div style="font-size:13px;font-weight:600;color:#6B7280;margin-bottom:8px;">Coût moyen / agent</div>
            <div style="font-size:28px;font-weight:700;color:#111827;">— XOF</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">Basé sur salaire de base</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="budget-card text-center">
            <div style="font-size:13px;font-weight:600;color:#6B7280;margin-bottom:8px;">Contrats actifs</div>
            <div style="font-size:28px;font-weight:700;color:#0A4D8C;">{{ $contratsActifs }}</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">CDI: {{ $contratsCDI }} | CDD: {{ $contratsCDD }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="budget-card text-center">
            <div style="font-size:13px;font-weight:600;color:#6B7280;margin-bottom:8px;">Effectif budgétisé</div>
            <div style="font-size:28px;font-weight:700;color:#059669;">{{ $effectifTotal }}</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">Agents actifs en poste</div>
        </div>
    </div>
</div>

{{-- Répartition budgétaire estimée --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
        <div class="budget-card">
            <div class="section-label">Évolution de l'effectif (12 mois)</div>
            <canvas id="chartEvolution" style="max-height:240px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="budget-card h-100">
            <div class="section-label">Structure des charges RH estimées</div>
            <div class="budget-row">
                <div>
                    <div style="font-size:13.5px;font-weight:500;color:#111827;">Salaires de base</div>
                    <div class="progress-bar-custom mt-1" style="width:180px;">
                        <div class="progress-fill" style="width:70%;"></div>
                    </div>
                </div>
                <span style="font-size:13px;font-weight:600;color:#0A4D8C;">~70%</span>
            </div>
            <div class="budget-row">
                <div>
                    <div style="font-size:13.5px;font-weight:500;color:#111827;">Charges sociales</div>
                    <div class="progress-bar-custom mt-1" style="width:180px;">
                        <div class="progress-fill" style="width:20%;background:linear-gradient(90deg,#059669,#10B981);"></div>
                    </div>
                </div>
                <span style="font-size:13px;font-weight:600;color:#059669;">~20%</span>
            </div>
            <div class="budget-row">
                <div>
                    <div style="font-size:13.5px;font-weight:500;color:#111827;">Primes et indemnités</div>
                    <div class="progress-bar-custom mt-1" style="width:180px;">
                        <div class="progress-fill" style="width:7%;background:linear-gradient(90deg,#D97706,#F59E0B);"></div>
                    </div>
                </div>
                <span style="font-size:13px;font-weight:600;color:#D97706;">~7%</span>
            </div>
            <div class="budget-row">
                <div>
                    <div style="font-size:13.5px;font-weight:500;color:#111827;">Formation</div>
                    <div class="progress-bar-custom mt-1" style="width:180px;">
                        <div class="progress-fill" style="width:3%;background:linear-gradient(90deg,#7C3AED,#8B5CF6);"></div>
                    </div>
                </div>
                <span style="font-size:13px;font-weight:600;color:#7C3AED;">~3%</span>
            </div>
            <div style="margin-top:16px;padding:12px;background:#FEF3C7;border-radius:8px;font-size:12px;color:#92400E;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Données estimatives — connecter le module paie pour les chiffres réels.
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = [];
    const now = new Date();
    for (let i = 11; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        labels.push(d.toLocaleDateString('fr-FR', { month: 'short', year: '2-digit' }));
    }
    new Chart(document.getElementById('chartEvolution'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Effectif actif',
                data: [0,0,0,0,0,0,0,0,0,0,0,{{ $effectifTotal }}],
                borderColor: '#0A4D8C',
                backgroundColor: 'rgba(10,77,140,0.06)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#0A4D8C',
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { size: 11 } } },
                y: { grid: { color: '#F3F4F6' }, ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
