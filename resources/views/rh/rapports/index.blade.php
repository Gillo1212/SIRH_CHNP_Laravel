@extends('layouts.master')
@section('title', 'Rapports RH')
@section('page-title', 'Rapports & Analyses')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Rapports</li>
@endsection

@push('styles')
<style>
/* ── Reprend le design system du dashboard ─── */
.kpi-card{border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden;}
.kpi-card:hover{box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px);}
.kpi-card .kpi-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.kpi-card .kpi-value{font-size:26px;font-weight:700;line-height:1.1;margin-top:10px;}
.kpi-card .kpi-label{font-size:12px;margin-top:2px;font-weight:500;}
.kpi-card::before{content:'';position:absolute;top:0;right:0;width:70px;height:70px;border-radius:0 12px 0 70px;opacity:.07;}
.kpi-card.blue::before{background:#0A4D8C;} .kpi-card.green::before{background:#059669;}
.kpi-card.amber::before{background:#D97706;} .kpi-card.red::before{background:#DC2626;}
.kpi-card.purple::before{background:#7C3AED;}

.panel,.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;}
.kpi-mini{background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:14px 18px;text-align:center;}
.kpi-mini .val{font-size:24px;font-weight:700;}
.kpi-mini .lbl{font-size:12px;color:#9CA3AF;margin-top:2px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #F3F4F6;}
.stat-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F9FAFB;}
.stat-row:last-child{border-bottom:none;}
.kpi-stat{text-align:center;padding:16px;}
.kpi-stat .val{font-size:26px;font-weight:700;}
.kpi-stat .lbl{font-size:12px;color:#9CA3AF;margin-top:3px;}

/* ── Navigation onglets ─── */
.rh-tabs{display:flex;gap:4px;background:#F3F4F6;padding:4px;border-radius:10px;margin-bottom:20px;}
.rh-tab{flex:1;text-align:center;padding:9px 14px;border-radius:7px;font-size:13px;font-weight:500;color:#6B7280;text-decoration:none;transition:all 180ms;white-space:nowrap;}
.rh-tab:hover{background:#fff;color:#374151;}
.rh-tab.active{background:#fff;color:#0A4D8C;font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.08);}
.rh-tab i{margin-right:6px;}

.badge-status{display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.data-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F9FAFB;}
.data-row:last-child{border-bottom:none;}

@media(max-width:576px){.rh-tab span{display:none;}}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ───────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:#111827;">
                <i class="fas fa-chart-bar me-2" style="color:#0A4D8C;"></i>Rapports & Analyses
            </h4>
            <p class="mb-0" style="font-size:13px;color:#6B7280;">
                {{ now()->isoFormat('dddd D MMMM YYYY') }} — Service des Ressources Humaines
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.absences.export') }}" class="btn btn-sm" style="background:#F3F4F6;color:#374151;border:none;border-radius:8px;font-size:12.5px;font-weight:500;">
                <i class="fas fa-file-csv me-1" style="color:#059669;"></i>Export absences
            </a>
            <a href="{{ route('rh.rapports.export') }}" class="btn btn-sm" style="background:#0A4D8C;color:#fff;border:none;border-radius:8px;font-size:12.5px;font-weight:500;">
                <i class="fas fa-file-export me-1"></i>Exports avancés
            </a>
        </div>
    </div>

    {{-- ── KPIs ──────────────────────────────────────────────────── --}}
    <div class="section-title">Vue d'ensemble</div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-col">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-users" style="color:#0A4D8C;"></i></div>
                    <span class="badge-status" style="background:#DBEAFE;color:#1E40AF;">Actifs</span>
                </div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['agents'] }}</div>
                <div class="kpi-label" style="color:#1E40AF;">Agents actifs</div>
            </div>
        </div>
        <div class="col-6 col-xl-col">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-file-contract" style="color:#059669;"></i></div>
                    <span class="badge-status" style="background:#D1FAE5;color:#065F46;">En cours</span>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['contrats_actifs'] }}</div>
                <div class="kpi-label" style="color:#065F46;">Contrats actifs</div>
            </div>
        </div>
        <div class="col-6 col-xl-col">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-user-slash" style="color:#DC2626;"></i></div>
                    <span class="badge-status" style="background:#FEE2E2;color:#991B1B;">Ce mois</span>
                </div>
                <div class="kpi-value" style="color:#DC2626;">{{ $stats['absences_mois'] }}</div>
                <div class="kpi-label" style="color:#991B1B;">Absences</div>
            </div>
        </div>
        <div class="col-6 col-xl-col">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-hourglass-half" style="color:#D97706;"></i></div>
                    <span class="badge-status" style="background:#FEF3C7;color:#92400E;">En cours</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['conges_en_cours'] }}</div>
                <div class="kpi-label" style="color:#92400E;">Congés</div>
            </div>
        </div>
        <div class="col-6 col-xl-col">
            <div class="kpi-card purple" style="background:#F5F3FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#EDE9FE;"><i class="fas fa-exchange-alt" style="color:#7C3AED;"></i></div>
                    <span class="badge-status" style="background:#EDE9FE;color:#5B21B6;">Ce mois</span>
                </div>
                <div class="kpi-value" style="color:#7C3AED;">{{ $stats['mouvements_mois'] }}</div>
                <div class="kpi-label" style="color:#5B21B6;">Mouvements</div>
            </div>
        </div>
    </div>

    {{-- ── Navigation onglets ────────────────────────────────────── --}}
    <nav class="rh-tabs">
        <a class="rh-tab {{ $view === 'mensuel' ? 'active' : '' }}" href="{{ route('rh.rapports.index', ['view' => 'mensuel']) }}">
            <i class="fas fa-calendar-alt"></i><span>Rapport mensuel</span>
        </a>
        <a class="rh-tab {{ $view === 'effectifs' ? 'active' : '' }}" href="{{ route('rh.rapports.index', ['view' => 'effectifs']) }}">
            <i class="fas fa-users"></i><span>Effectifs</span>
        </a>
        <a class="rh-tab {{ $view === 'statistiques' ? 'active' : '' }}" href="{{ route('rh.rapports.index', ['view' => 'statistiques']) }}">
            <i class="fas fa-chart-pie"></i><span>Statistiques</span>
        </a>
        <a class="rh-tab {{ $view === 'graphiques' ? 'active' : '' }}" href="{{ route('rh.rapports.index', ['view' => 'graphiques']) }}">
            <i class="fas fa-sliders-h"></i><span>Constructeur</span>
        </a>
    </nav>

    {{-- ── Contenu de l'onglet ───────────────────────────────────── --}}
    @if($view === 'mensuel')
        @include('rh.rapports.partials.mensuel')
    @elseif($view === 'effectifs')
        @include('rh.rapports.partials.effectifs')
    @elseif($view === 'statistiques')
        @include('rh.rapports.partials.statistiques')
    @elseif($view === 'graphiques')
        @include('rh.rapports.partials.graphiques')
    @endif

</div>
@endsection

@push('styles')
<style>
/* 5 colonnes égales sur grand écran */
@media(min-width:1200px){
    .col-xl-col{flex:0 0 20%;max-width:20%;}
}
</style>
@endpush
