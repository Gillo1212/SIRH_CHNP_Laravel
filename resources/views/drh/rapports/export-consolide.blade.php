@extends('layouts.master')
@section('title', 'Export consolidé — DRH')
@section('page-title', 'Export consolidé')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Rapports</li>
    <li>Export consolidé</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-export me-2" style="color:#D97706;"></i>Export consolidé Direction
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Exports de données consolidées pour la direction générale</p>
        </div>
        <a href="{{ route('drh.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
        </a>
    </div>

    <div class="row g-4">
        @foreach([
            ['Bilan social annuel', 'Rapport annuel complet avec indicateurs sociaux, effectifs, absences et mouvements.', 'fa-balance-scale', '#EFF6FF', '#0A4D8C', route('drh.rapports.bilan')],
            ['Rapport effectifs', 'Répartition détaillée des effectifs par service, catégorie, sexe et statut.', 'fa-users', '#ECFDF5', '#059669', route('drh.rapports.effectifs')],
            ['Prévisions départs', 'Agents proches de la retraite et contrats expirant dans les 90 jours.', 'fa-chart-line', '#F5F3FF', '#7C3AED', route('drh.rapports.previsions')],
            ['KPIs stratégiques', 'Vue détaillée des indicateurs clés de performance RH du CHNP.', 'fa-tachometer-alt', '#FFFBEB', '#D97706', route('drh.kpis')],
        ] as [$title, $desc, $icon, $bg, $color, $url])
        <div class="col-12 col-md-6">
            <a href="{{ $url }}" style="text-decoration:none;">
                <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;transition:box-shadow 200ms,transform 200ms;"
                     onmouseenter="this.style.boxShadow='0 6px 20px rgba(10,77,140,.12)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='';this.style.transform=''">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:52px;height:52px;background:{{ $bg }};border-radius:12px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:22px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#111827;">{{ $title }}</div>
                            <div style="font-size:12px;color:#9CA3AF;">Rapport direction</div>
                        </div>
                    </div>
                    <p style="font-size:13px;color:#6B7280;margin:0;">{{ $desc }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="alert alert-info mt-4" style="border-radius:10px;">
        <i class="fas fa-info-circle me-2"></i>
        <span style="font-size:13px;">
            L'export direct Excel/PDF sera disponible dans la prochaine version.
            Les rapports sont consultables en ligne avec option d'impression (Ctrl+P).
        </span>
    </div>

</div>
@endsection
