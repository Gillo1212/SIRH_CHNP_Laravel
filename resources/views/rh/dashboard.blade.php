@extends('layouts.master')

@section('title', 'Tableau de bord — Ressources Humaines')
@section('page-title', 'Gestion des Ressources Humaines')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
@endsection

@push('styles')
<style>
.kpi-card {
    border-radius: 12px; padding: 20px 24px;
    transition: box-shadow 200ms, transform 200ms;
    position: relative; overflow: hidden;
}
.kpi-card:hover { box-shadow: 0 6px 20px rgba(10,77,140,0.10); transform: translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value  { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label  { font-size:13px;margin-top:2px;font-weight:500; }
.kpi-card .kpi-trend  { font-size:12px;font-weight:600;margin-top:6px; }
.kpi-card .kpi-trend.up   { color:#10B981; }
.kpi-card .kpi-trend.down { color:#EF4444; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:0.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.red::before    { background:#DC2626; }
.kpi-card.purple::before { background:#7C3AED; }

.action-btn { display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,0.3);transform:translateY(-1px); }

.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;padding-bottom:6px; }
.panel { border-radius:12px;padding:20px; }
.data-row { display:flex;align-items:center;justify-content:space-between;padding:12px 0; }
.data-row:last-child { border-bottom: none !important; }
.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-pending { background:#FEF3C7;color:#92400E; }
.badge-urgent  { background:#FEE2E2;color:#991B1B; }
.badge-ok      { background:#D1FAE5;color:#065F46; }

/* ── THÈME SOMBRE ─────────────────────────────────────────── */
[data-theme="dark"] .badge-ok { background: rgba(63,185,80,0.2) !important; color: #3fb950 !important; }
</style>
@endpush

@section('content')

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">
            Bonjour, {{ Auth::user()->agent->prenom ?? 'Agent RH' }} 👋
        </h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            {{ now()->isoFormat('dddd D MMMM YYYY') }} — Service des Ressources Humaines
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="#" class="action-btn action-btn-outline">
            <i class="fas fa-file-export"></i> Exporter
        </a>
        <a href="#" class="action-btn action-btn-primary">
            <i class="fas fa-user-plus"></i> Nouvel agent
        </a>
    </div>
</div>

{{-- ─── KPIs ─────────────────────────────────────────────────────── --}}
<div class="section-title">KPIs Ressources Humaines</div>
<div class="row g-3 mb-4">
    @php
        try { $totalAgents     = \App\Models\Agent::where('statut','actif')->count(); }                 catch(\Exception $e) { $totalAgents = 0; }
        try { $contratsExpiring = \App\Models\Contrat::where('date_fin','<=',now()->addDays(60))->where('date_fin','>=',now())->where('statut_contrat','Actif')->count(); } catch(\Exception $e) { $contratsExpiring = 0; }
        try { $pendingLeaves   = \App\Models\Demande::where('type_demande','Conge')->whereIn('statut_demande',['En_attente','Validé'])->count(); } catch(\Exception $e) { $pendingLeaves = 0; }
        try { $absencesToday   = \App\Models\Absence::whereDate('date_absence', today())->count(); }    catch(\Exception $e) { $absencesToday = 0; }
        try { $enConge         = \App\Models\Agent::where('statut','en_conge')->count(); }              catch(\Exception $e) { $enConge = 0; }
    @endphp

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-users" style="color:#0A4D8C;"></i></div>
                <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">Actifs</span>
            </div>
            <div class="kpi-value">{{ $totalAgents }}</div>
            <div class="kpi-label">Agents actifs</div>
            <div class="kpi-trend up"><i class="fas fa-arrow-up me-1"></i>Personnel en poste</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card red">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FEF2F2;"><i class="fas fa-file-contract" style="color:#DC2626;"></i></div>
                <span class="badge-status badge-urgent">< 60 jours</span>
            </div>
            <div class="kpi-value">{{ $contratsExpiring }}</div>
            <div class="kpi-label">Contrats à renouveler</div>
            <div class="kpi-trend {{ $contratsExpiring > 0 ? 'down' : 'up' }}">
                <i class="fas fa-{{ $contratsExpiring > 0 ? 'exclamation-triangle' : 'check' }} me-1"></i>
                {{ $contratsExpiring > 0 ? 'Action requise' : 'Aucun urgent' }}
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-hourglass-half" style="color:#D97706;"></i></div>
                <span class="badge-status badge-pending">En attente</span>
            </div>
            <div class="kpi-value">{{ $pendingLeaves }}</div>
            <div class="kpi-label">Demandes de congés</div>
            <div class="kpi-trend neutral"><i class="fas fa-calendar-check me-1"></i>À traiter</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-umbrella-beach" style="color:#059669;"></i></div>
                <span class="badge-status badge-ok">Validés</span>
            </div>
            <div class="kpi-value">{{ $enConge }}</div>
            <div class="kpi-label">Agents en congé</div>
            <div class="kpi-trend neutral"><i class="fas fa-user-clock me-1"></i>{{ $absencesToday }} absent(s) aujourd'hui</div>
        </div>
    </div>
</div>

{{-- ─── GRAPHIQUES ───────────────────────────────────────────────── --}}
<div class="section-title">Analyses</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;">Effectifs par service</div>
                    <div style="font-size:12px;color:#9CA3AF;">Répartition actuelle</div>
                </div>
            </div>
            <canvas id="chartServices" style="max-height:240px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="panel h-100">
            <div class="fw-600 mb-1" style="color:#111827;">Demandes de congés</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:14px;">12 derniers mois</div>
            <canvas id="chartConges" style="max-height:220px;"></canvas>
        </div>
    </div>
</div>

{{-- ─── TABLEAUX ─────────────────────────────────────────────────── --}}
<div class="section-title">Suivi opérationnel</div>
<div class="row g-3 mb-4">
    {{-- Congés en attente --}}
    <div class="col-12 col-lg-6">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Demandes de congés en attente</div>
                <a href="#" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            @foreach([['M','Mamadou Diallo','Annuel','15/03 — 22/03','Validé Manager','#FEF3C7','#92400E'],['F','Fatou Ndiaye','Maladie','18/03 — 20/03','En attente','#FEF3C7','#92400E'],['A','Awa Sow','Maternité','01/04 — 01/07','En attente','#FEF3C7','#92400E']] as [$init,$nom,$type,$dates,$statut,$bg,$col])
            <div class="data-row">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;">{{ $init }}</div>
                    <div>
                        <div style="font-size:13px;font-weight:500;color:#111827;">{{ $nom }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $type }} · {{ $dates }}</div>
                    </div>
                </div>
                <span class="badge-status" style="background:{{ $bg }};color:{{ $col }};">{{ $statut }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Contrats à renouveler --}}
    <div class="col-12 col-lg-6">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">
                    <i class="fas fa-exclamation-triangle me-1" style="color:#D97706;"></i>Contrats à renouveler
                </div>
                <a href="#" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            @foreach([['Ibrahima Fall','CDD','25/03/2026','12 jours','#FEE2E2','#991B1B'],['Ousmane Ba','CDD','15/04/2026','33 jours','#FEF3C7','#92400E'],['Aminata Diop','Interim','30/04/2026','48 jours','#FEF3C7','#92400E'],['Moussa Sarr','CDD','10/05/2026','58 jours','#FEF3C7','#92400E']] as [$nom,$type,$date,$jours,$bg,$col])
            <div class="data-row">
                <div>
                    <div style="font-size:13px;font-weight:500;color:#111827;">{{ $nom }}</div>
                    <div style="font-size:12px;color:#9CA3AF;">{{ $type }} · Expire le {{ $date }}</div>
                </div>
                <span class="badge-status" style="background:{{ $bg }};color:{{ $col }};">{{ $jours }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── ACTIONS RAPIDES ──────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;">
    <div class="fw-600 mb-3" style="color:#0A4D8C;">Actions rapides</div>
    <div class="d-flex flex-wrap gap-2">
        <a href="#" class="action-btn action-btn-primary"><i class="fas fa-user-plus"></i> Nouvel agent</a>
        <a href="{{ route('rh.conge-physique') }}" class="action-btn action-btn-outline"><i class="fas fa-umbrella-beach"></i> Saisir congé</a>
        <a href="{{ route('rh.mouvements.index') }}" class="action-btn action-btn-outline"><i class="fas fa-exchange-alt"></i> Mouvement</a>
        <a href="{{ route('rh.docs-admin.index') }}" class="action-btn action-btn-outline"><i class="fas fa-file-alt"></i> Documents admin.</a>
        <a href="{{ route('pec.create') }}" class="action-btn action-btn-outline"><i class="fas fa-heartbeat"></i> Prise en charge</a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const colors = isDark ? {
        primary:'#58a6ff', secondary:'#79c0ff', green:'#3fb950', amber:'#d29922', red:'#f85149',
        grid:'#30363d', text:'#8d96a0', border:'#161b22'
    } : {
        primary:'#0A4D8C', secondary:'#1565C0', green:'#059669', amber:'#D97706', red:'#DC2626',
        grid:'#F3F4F6', text:'#9CA3AF', border:'#fff'
    };

    new Chart(document.getElementById('chartServices'), {
        type: 'bar',
        data: {
            labels: ['Urgences','Pédiatrie','Chirurgie','Cardiologie','Radiologie','Maternité','RH','Administration'],
            datasets: [{ label: 'Agents', data: [42,35,28,22,18,15,10,8],
                backgroundColor: isDark ? 'rgba(88,166,255,0.15)' : 'rgba(10,77,140,0.13)',
                borderColor: colors.primary, borderWidth: 1.5, borderRadius: 4 }]
        },
        options: {
            indexAxis: 'y', responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } }, beginAtZero: true },
                y: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11 } } }
            }
        }
    });

    new Chart(document.getElementById('chartConges'), {
        type: 'doughnut',
        data: {
            labels: ['Annuel','Maladie','Maternité','Exceptionnel'],
            datasets: [{ data: [55,25,12,8], backgroundColor: [colors.primary,colors.red,colors.amber,'#7C3AED'],
                borderWidth: 2, borderColor: colors.border }]
        },
        options: {
            responsive: true, cutout: '60%',
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, color: colors.text, padding: 8, boxWidth: 10 } } }
        }
    });
});
</script>
@endpush
