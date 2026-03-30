@extends('layouts.master')
@section('title', 'Rapport Effectifs — DRH')
@section('page-title', 'Rapport Effectifs')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Rapports</li>
    <li>Effectifs</li>
@endsection

@push('styles')
<style>
.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:16px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;}
.kpi-big{border-radius:12px;padding:20px 24px;border:1px solid;text-align:center;}
.kpi-big .val{font-size:36px;font-weight:700;}
.kpi-big .lbl{font-size:13px;color:#6B7280;margin-top:4px;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-users me-2" style="color:#0A4D8C;"></i>Rapport Effectifs — Direction
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Vue consolidée des effectifs pour la direction</p>
        </div>
        <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
        </a>
    </div>

    {{-- KPIs principaux --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="kpi-big" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div class="val" style="color:#0A4D8C;">{{ $totalActifs }}</div>
                <div class="lbl">Effectif total actif</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-big" style="background:#FDF2F8;border-color:#FBCFE8;">
                <div class="val" style="color:#BE185D;">{{ $parSexe['F'] }}</div>
                <div class="lbl">Femmes ({{ $totalActifs > 0 ? round($parSexe['F']/$totalActifs*100) : 0 }}%)</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="kpi-big" style="background:#EFF6FF;border-color:#BFDBFE;">
                <div class="val" style="color:#1D4ED8;">{{ $parSexe['M'] }}</div>
                <div class="lbl">Hommes ({{ $totalActifs > 0 ? round($parSexe['M']/$totalActifs*100) : 0 }}%)</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Par service --}}
        <div class="col-12 col-lg-7">
            <div class="section-card h-100">
                <div class="section-title">Effectifs par service</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size:13px;">
                        <thead><tr style="background:#F9FAFB;">
                            <th class="border-0 py-2 px-3">Service</th>
                            <th class="border-0 py-2 px-3">Division</th>
                            <th class="border-0 py-2 px-3 text-center">Agents</th>
                            <th class="border-0 py-2 px-3">%</th>
                        </tr></thead>
                        <tbody>
                            @foreach($parService as $svc)
                            @php $pct = $totalActifs > 0 ? round($svc->actifs / $totalActifs * 100) : 0; @endphp
                            <tr>
                                <td class="py-2 px-3 border-0 fw-600">{{ $svc->nom_service }}</td>
                                <td class="py-2 px-3 border-0 text-muted">{{ $svc->divisions_count ?? '—' }}</td>
                                <td class="py-2 px-3 border-0 text-center" style="font-weight:700;color:#0A4D8C;">{{ $svc->actifs }}</td>
                                <td class="py-2 px-3 border-0">
                                    <div style="background:#E5E7EB;border-radius:4px;height:8px;overflow:hidden;width:80px;">
                                        <div style="height:100%;width:{{ $pct }}%;background:#0A4D8C;border-radius:4px;"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Par statut + Par catégorie --}}
        <div class="col-12 col-lg-5">
            <div class="section-card mb-3">
                <div class="section-title">Par statut</div>
                @foreach($parStatut as $s => $c)
                @php $colors = ['actif'=>'#059669','en_conge'=>'#1D4ED8','suspendu'=>'#DC2626','retraite'=>'#6B7280']; @endphp
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #F9FAFB;">
                    <span style="font-size:13px;color:#374151;">{{ ucfirst(str_replace('_',' ',$s)) }}</span>
                    <span style="font-weight:700;color:{{ $colors[$s] ?? '#374151' }};">{{ $c }}</span>
                </div>
                @endforeach
            </div>
            <div class="section-card">
                <div class="section-title">Par catégorie (actifs)</div>
                @foreach($parCategorie as $cat => $c)
                <div class="d-flex align-items-center justify-content-between py-1" style="border-bottom:1px solid #F9FAFB;">
                    <span style="font-size:12px;color:#374151;">{{ str_replace('_',' ',$cat) }}</span>
                    <span style="font-weight:700;color:#0A4D8C;">{{ $c }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
