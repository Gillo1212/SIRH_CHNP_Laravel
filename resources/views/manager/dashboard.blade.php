@extends('layouts.master')

@section('title', 'Tableau de bord — Manager')
@section('page-title', 'Gestion de mon équipe')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
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

.action-btn { display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,0.3);transform:translateY(-1px); }

.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;padding-bottom:6px; }
.panel { border-radius:12px;padding:20px; }
.data-row { display:flex;align-items:center;justify-content:space-between;padding:12px 0; }
.data-row:last-child { border-bottom: none !important; }
.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }

/* Planning semaine */
.planning-cell { border-radius:8px;padding:10px 6px;text-align:center;transition:all 180ms; }
.planning-cell:hover { box-shadow:0 3px 10px rgba(0,0,0,0.08); }
.planning-cell.today { border-color:#1565C0;background:#EFF6FF;box-shadow:0 0 0 2px rgba(21,101,192,0.2); }
</style>
@endpush

@section('content')

{{-- ─── ALERTE AUCUN SERVICE ─────────────────────────────────────── --}}
@if(!empty($noService))
<div style="max-width:520px;margin:80px auto;text-align:center;">
    <div style="width:72px;height:72px;border-radius:50%;background:#FEF3C7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px;">
        <i class="fas fa-exclamation-triangle" style="color:#D97706;"></i>
    </div>
    <h5 style="font-weight:700;margin-bottom:8px;">Aucun service assigné</h5>
    <p style="font-size:13px;color:var(--theme-text-muted);margin-bottom:24px;">
        Votre compte Manager n'est pas encore assigné à un service.<br>
        Contactez l'Administrateur ou un Agent RH pour finaliser votre configuration.
    </p>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-user me-2"></i>Mon profil
    </a>
</div>
@else
{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">
            Bonjour, {{ Auth::user()->agent->prenom ?? 'Manager' }} 👋
        </h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            {{ now()->isoFormat('dddd D MMMM YYYY') }}
            — Service {{ Auth::user()->agent->service->nom ?? 'de votre équipe' }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="#" class="action-btn action-btn-outline">
            <i class="fas fa-calendar-plus"></i> Nouveau planning
        </a>
        <a href="#" class="action-btn action-btn-primary">
            <i class="fas fa-clipboard-check"></i> Valider congés
            @php
                try { $pendingCount = \App\Models\Demande::where('type_demande','Conge')->where('statut_demande','En_attente')->count(); }
                catch(\Exception $e) { $pendingCount = 0; }
            @endphp
            @if($pendingCount > 0)
                <span style="background:white;color:#0A4D8C;border-radius:20px;font-size:11px;font-weight:700;padding:1px 7px;margin-left:2px;">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>
</div>

{{-- ─── KPIs ÉQUIPE ──────────────────────────────────────────────── --}}
<div class="section-title">Mon équipe — Vue d'ensemble</div>
<div class="row g-3 mb-4">
    @php
        try { $totalEquipe   = \App\Models\Agent::where('statut','actif')->count(); }                catch(\Exception $e) { $totalEquipe = 0; }
        try { $presents      = \App\Models\Agent::where('statut','actif')->count(); }                catch(\Exception $e) { $presents = 0; }
        try { $absents       = \App\Models\Absence::whereDate('date_absence',today())->count(); }     catch(\Exception $e) { $absents = 0; }
        try { $enConge       = \App\Models\Agent::where('statut','en_conge')->count(); }             catch(\Exception $e) { $enConge = 0; }
    @endphp

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-users" style="color:#0A4D8C;"></i></div>
                <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">Mon service</span>
            </div>
            <div class="kpi-value">{{ $totalEquipe }}</div>
            <div class="kpi-label">Membres de l'équipe</div>
            <div class="kpi-trend up"><i class="fas fa-building me-1"></i>Effectif total</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-user-check" style="color:#059669;"></i></div>
                <span class="badge-status" style="background:#ECFDF5;color:#065F46;">Aujourd'hui</span>
            </div>
            <div class="kpi-value">{{ max(0, $presents - $absents - $enConge) }}</div>
            <div class="kpi-label">Présents aujourd'hui</div>
            <div class="kpi-trend up"><i class="fas fa-arrow-up me-1"></i>En poste</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card red">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FEF2F2;"><i class="fas fa-user-minus" style="color:#DC2626;"></i></div>
                <span class="badge-status" style="background:#FEE2E2;color:#991B1B;">Aujourd'hui</span>
            </div>
            <div class="kpi-value">{{ $absents }}</div>
            <div class="kpi-label">Absents</div>
            <div class="kpi-trend {{ $absents > 0 ? 'down' : 'up' }}">
                <i class="fas fa-{{ $absents > 0 ? 'exclamation-circle' : 'check' }} me-1"></i>
                {{ $absents > 0 ? 'À surveiller' : 'Aucune absence' }}
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-clipboard-check" style="color:#D97706;"></i></div>
                <span class="badge-status" style="background:#FEF3C7;color:#92400E;">En attente</span>
            </div>
            <div class="kpi-value">{{ $pendingCount }}</div>
            <div class="kpi-label">Congés à valider</div>
            <div class="kpi-trend {{ $pendingCount > 0 ? 'neutral' : 'up' }}">
                <i class="fas fa-{{ $pendingCount > 0 ? 'hourglass-half' : 'check-circle' }} me-1"></i>
                {{ $pendingCount > 0 ? 'Action requise' : 'À jour' }}
            </div>
        </div>
    </div>
</div>

{{-- ─── PLANNING SEMAINE ─────────────────────────────────────────── --}}
<div class="section-title">Planning de la semaine</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;">Planning — Semaine {{ now()->weekOfYear }}</div>
                    <div style="font-size:12px;color:#9CA3AF;">
                        Du {{ now()->startOfWeek()->isoFormat('D MMM') }} au {{ now()->endOfWeek()->isoFormat('D MMM YYYY') }}
                    </div>
                </div>
                <a href="#" class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                    <i class="fas fa-calendar-alt"></i> Mois complet
                </a>
            </div>
            @php
                $jours = [
                    ['Lun','jour','#3B82F6','07:00-15:00'],
                    ['Mar','jour','#3B82F6','07:00-15:00'],
                    ['Mer','nuit','#6366F1','19:00-07:00'],
                    ['Jeu','nuit','#6366F1','19:00-07:00'],
                    ['Ven','repos','#9CA3AF','—'],
                    ['Sam','repos','#9CA3AF','—'],
                    ['Dim','garde','#EF4444','07:00-19:00'],
                ];
                $today = now()->dayOfWeekIso - 1;
            @endphp
            <div class="row g-2">
                @foreach($jours as $i => [$jour, $type, $color, $horaire])
                <div class="col">
                    <div class="planning-cell {{ $i === $today ? 'today' : '' }}">
                        <div style="font-size:10px;font-weight:600;color:{{ $i === $today ? '#1565C0' : '#9CA3AF' }};margin-bottom:4px;">{{ $jour }}</div>
                        <div style="font-size:13px;font-weight:700;color:{{ $i === $today ? '#0A4D8C' : '#374151' }};margin-bottom:8px;">
                            {{ now()->startOfWeek()->addDays($i)->format('d') }}
                        </div>
                        <span style="display:block;background:{{ $color }};color:white;border-radius:6px;font-size:10px;padding:3px 4px;font-weight:600;">
                            @switch($type)
                                @case('jour')  <i class="fas fa-sun"></i>   @break
                                @case('nuit')  <i class="fas fa-moon"></i>  @break
                                @case('garde') <i class="fas fa-heartbeat"></i> @break
                                @case('repos') <i class="fas fa-bed"></i>   @break
                            @endswitch
                        </span>
                        @if($horaire !== '—')
                            <div style="font-size:9px;color:#9CA3AF;margin-top:4px;">{{ $horaire }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div style="background:#EFF6FF;border-radius:8px;padding:10px 14px;margin-top:14px;font-size:12.5px;color:#1E40AF;">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Prochaine garde :</strong> Dimanche — 07:00 à 19:00 · Service des Urgences
            </div>
        </div>
    </div>

    {{-- Demandes à valider --}}
    <div class="col-12 col-lg-4">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Demandes à valider</div>
                @if($pendingCount > 0)
                    <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">{{ $pendingCount }}</span>
                @endif
            </div>
            @php
                $demandesDemo = [
                    ['M','Mamadou Diallo','Congé annuel','01/04 — 10/04','8 jours'],
                    ['F','Fatou Cissé','Congé maladie','20/03 — 22/03','2 jours'],
                    ['A','Awa Ba','Congé exceptionnel','25/03','1 jour'],
                ];
            @endphp
            @if($pendingCount === 0)
                <div style="text-align:center;padding:30px 0;color:#9CA3AF;">
                    <i class="fas fa-check-double fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                    Aucune demande en attente
                </div>
            @else
                @foreach($demandesDemo as [$init,$nom,$type,$dates,$duree])
                <div class="data-row">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;flex-shrink:0;">{{ $init }}</div>
                        <div>
                            <div style="font-size:12.5px;font-weight:500;color:#111827;">{{ $nom }}</div>
                            <div style="font-size:11px;color:#9CA3AF;">{{ $type }} · {{ $dates }}</div>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-size:12px;font-weight:600;color:#0A4D8C;">{{ $duree }}</div>
                        <a href="#" style="font-size:10px;color:#059669;font-weight:600;text-decoration:none;">Valider →</a>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- ─── GRAPHIQUE ABSENTÉISME + ÉQUIPE ──────────────────────────── --}}
<div class="section-title">Statistiques équipe</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
        <div class="panel">
            <div class="fw-600 mb-1" style="color:#111827;">Absentéisme — 6 derniers mois</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:14px;">Nombre de jours d'absence par mois</div>
            <canvas id="chartAbsenteisme" style="max-height:200px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="panel h-100">
            <div class="fw-600 mb-3" style="color:#111827;">Répartition postes</div>
            <canvas id="chartPostes" style="max-height:200px;"></canvas>
        </div>
    </div>
</div>

{{-- ─── ACTIONS RAPIDES ──────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;">
    <div class="fw-600 mb-3" style="color:#0A4D8C;">Actions rapides</div>
    <div class="d-flex flex-wrap gap-2">
        <a href="#" class="action-btn action-btn-primary"><i class="fas fa-clipboard-check"></i> Valider les congés</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-calendar-plus"></i> Créer un planning</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-user-minus"></i> Enregistrer absence</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-user-friends"></i> Voir mon équipe</a>
    </div>
</div>

@endif {{-- @else noService --}}

@endsection

@if(empty($noService))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = { primary:'#0A4D8C', green:'#059669', amber:'#D97706', red:'#DC2626', grid:'#F3F4F6', text:'#9CA3AF' };

    new Chart(document.getElementById('chartAbsenteisme'), {
        type: 'bar',
        data: {
            labels: ['Oct','Nov','Déc','Jan','Fév','Mar'],
            datasets: [{ label: 'Jours absence', data: [3,5,2,8,4,{{ $absents }}], backgroundColor: 'rgba(217,119,6,0.15)', borderColor: colors.amber, borderWidth: 1.5, borderRadius: 4 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11 } } },
                y: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('chartPostes'), {
        type: 'doughnut',
        data: {
            labels: ['Poste Jour','Poste Nuit','Garde','Repos','Astreinte'],
            datasets: [{ data: [35,28,15,15,7], backgroundColor: ['#3B82F6','#6366F1',colors.red,colors.text,'#F59E0B'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, cutout: '58%',
            plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, color: colors.text, padding: 8, boxWidth: 10 } } }
        }
    });
});
</script>
@endpush
@endif
