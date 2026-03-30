@extends('layouts.master')
@section('title', 'Prévisions départs — DRH')
@section('page-title', 'Prévisions des départs')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Rapports</li>
    <li>Prévisions départs</li>
@endsection

@push('styles')
<style>
.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:16px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-chart-line me-2" style="color:#7C3AED;"></i>Prévisions des départs
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Anticipation des départs retraite et fins de contrat</p>
        </div>
        <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:20px;text-align:center;">
                <div style="font-size:32px;font-weight:700;color:#DC2626;">{{ $procheRetraite->count() }}</div>
                <div style="font-size:13px;color:#6B7280;margin-top:4px;">Agents ≥ 55 ans</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:12px;padding:20px;text-align:center;">
                <div style="font-size:32px;font-weight:700;color:#D97706;">{{ $contratsExpirants->count() }}</div>
                <div style="font-size:13px;color:#6B7280;margin-top:4px;">Contrats expirants (90j)</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div style="background:#EFF6FF;border:1px solid #DBEAFE;border-radius:12px;padding:20px;text-align:center;">
                <div style="font-size:32px;font-weight:700;color:#0A4D8C;">{{ $procheRetraite->where('date_naissance', '<=', now()->subYears(60)->toDateString())->count() }}</div>
                <div style="font-size:13px;color:#6B7280;margin-top:4px;">Agents ≥ 60 ans</div>
            </div>
        </div>
    </div>

    {{-- Évolution --}}
    <div class="section-card mb-4">
        <div class="section-title">Évolution recrutements vs départs (12 mois)</div>
        <canvas id="chartPrevisions" style="max-height:260px;"></canvas>
    </div>

    {{-- Agents proches retraite --}}
    <div class="section-card mb-4">
        <div class="section-title" style="color:#DC2626;">Agents de 55 ans et plus</div>
        @if($procheRetraite->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:13px;">
                <thead><tr style="background:#FEF2F2;">
                    <th class="border-0 py-2 px-3">Agent</th>
                    <th class="border-0 py-2 px-3">Service</th>
                    <th class="border-0 py-2 px-3">Fonction</th>
                    <th class="border-0 py-2 px-3 text-center">Âge</th>
                    <th class="border-0 py-2 px-3">Contrat</th>
                </tr></thead>
                <tbody>
                    @foreach($procheRetraite as $agent)
                    @php $age = $agent->date_naissance?->diffInYears(now()); @endphp
                    <tr>
                        <td class="py-2 px-3 border-0 fw-600">{{ $agent->nom_complet }}</td>
                        <td class="py-2 px-3 border-0 text-muted">{{ $agent->service?->nom_service ?? '—' }}</td>
                        <td class="py-2 px-3 border-0">{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</td>
                        <td class="py-2 px-3 border-0 text-center">
                            <span style="font-weight:700;color:{{ $age >= 60 ? '#DC2626' : '#D97706' }};">{{ $age }} ans</span>
                        </td>
                        <td class="py-2 px-3 border-0">{{ $agent->contratActif?->type_contrat ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted mb-0" style="font-size:13px;">Aucun agent de 55 ans ou plus actuellement.</p>
        @endif
    </div>

    {{-- Contrats expirants --}}
    <div class="section-card">
        <div class="section-title" style="color:#D97706;">Contrats expirant dans 90 jours</div>
        @if($contratsExpirants->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:13px;">
                <thead><tr style="background:#FFFBEB;">
                    <th class="border-0 py-2 px-3">Agent</th>
                    <th class="border-0 py-2 px-3">Service</th>
                    <th class="border-0 py-2 px-3">Type contrat</th>
                    <th class="border-0 py-2 px-3 text-center">Fin contrat</th>
                    <th class="border-0 py-2 px-3 text-center">Jours restants</th>
                </tr></thead>
                <tbody>
                    @foreach($contratsExpirants as $c)
                    @php $jours = now()->diffInDays($c->date_fin, false); @endphp
                    <tr>
                        <td class="py-2 px-3 border-0 fw-600">{{ $c->agent?->nom_complet ?? '—' }}</td>
                        <td class="py-2 px-3 border-0 text-muted">{{ $c->agent?->service?->nom_service ?? '—' }}</td>
                        <td class="py-2 px-3 border-0">{{ $c->type_contrat }}</td>
                        <td class="py-2 px-3 border-0 text-center">{{ $c->date_fin?->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 border-0 text-center">
                            <span style="font-weight:700;color:{{ $jours <= 30 ? '#DC2626' : '#D97706' }};">{{ $jours }}j</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted mb-0" style="font-size:13px;">Aucun contrat expirant dans les 90 prochains jours.</p>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartPrevisions'), {
    type: 'bar',
    data: {
        labels: @json($projLabels),
        datasets: [
            { label: 'Recrutements', data: @json($projRecrutements), backgroundColor: '#0A4D8C55', borderColor: '#0A4D8C', borderWidth: 1.5, borderRadius: 4 },
            { label: 'Départs', data: @json($projDeparts), backgroundColor: '#EF444455', borderColor: '#EF4444', borderWidth: 1.5, borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { font: { size: 12 }, color: '#6B7280' } } },
        scales: {
            x: { ticks: { color: '#9CA3AF', font: { size: 10 } }, grid: { display: false } },
            y: { ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true, precision: 0 }
        }
    }
});
</script>
@endpush
