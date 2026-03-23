@extends('layouts.master')

@section('title', 'Mon Espace — Agent')
@section('page-title', 'Mon Espace Personnel')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
@endsection

@push('styles')
<style>
.kpi-card {
    border-radius:12px;padding:20px 24px;
    transition:box-shadow 200ms,transform 200ms;
    position:relative;overflow:hidden;
}
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,0.10);transform:translateY(-2px); }
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
.kpi-card.purple::before { background:#7C3AED; }

.action-btn { display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,0.3);transform:translateY(-1px); }

.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;padding-bottom:6px; }
.panel { border-radius:12px;padding:20px; }
.data-row { display:flex;align-items:center;justify-content:space-between;padding:12px 0; }
.data-row:last-child { border-bottom: none !important; }
.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }

/* Profil card */
.avatar-cercle { width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:28px;font-weight:700;box-shadow:0 4px 15px rgba(10,77,140,0.3);margin:0 auto 12px; }

/* Soldes congés */
.solde-bloc { border-radius:10px;padding:14px;text-align:center;transition:all 180ms; }
.solde-bloc:hover { box-shadow:0 3px 12px rgba(0,0,0,0.08);transform:translateY(-2px); }
.solde-number { font-size:26px;font-weight:700;line-height:1; }
.progress-thin { height:5px;border-radius:3px;overflow:hidden;margin-top:6px; }
.progress-fill { height:100%;border-radius:3px; }

/* Planning semaine */
.planning-cell { border-radius:8px;padding:10px 6px;text-align:center;transition:all 180ms; }
.planning-cell.today { border-color:#1565C0;background:#EFF6FF;box-shadow:0 0 0 2px rgba(21,101,192,0.2); }
</style>
@endpush

@section('content')

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">
            Bonjour, {{ Auth::user()->agent->prenom ?? 'Agent' }} 👋
        </h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            {{ now()->isoFormat('dddd D MMMM YYYY') }}
            — {{ Auth::user()->agent->service->nom ?? 'Votre service' }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="#" class="action-btn action-btn-outline">
            <i class="fas fa-file-alt"></i> Mes documents
        </a>
        <a href="#" class="action-btn action-btn-primary">
            <i class="fas fa-umbrella-beach"></i> Demander un congé
        </a>
    </div>
</div>

{{-- ─── PROFIL + SOLDES ──────────────────────────────────────────── --}}
<div class="section-title">Mon dossier</div>
<div class="row g-3 mb-4">

    {{-- Carte profil --}}
    <div class="col-12 col-lg-3">
        <div class="panel text-center">
            <div class="avatar-cercle">
                @if(Auth::user()->agent && Auth::user()->agent->photo)
                    <img src="{{ asset('storage/'.Auth::user()->agent->photo) }}" alt="Photo" class="rounded-circle" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ strtoupper(substr(Auth::user()->agent->prenom ?? 'U',0,1).substr(Auth::user()->agent->nom ?? 'A',0,1)) }}
                @endif
            </div>
            <div style="font-size:16px;font-weight:700;color:#111827;">
                {{ Auth::user()->agent->prenom ?? 'Prénom' }} {{ Auth::user()->agent->nom ?? 'Nom' }}
            </div>
            <div style="font-size:12px;color:#9CA3AF;margin:4px 0 10px;">
                <i class="fas fa-id-card me-1"></i>{{ Auth::user()->agent->matricule ?? 'CHNP-XXXXX' }}
            </div>
            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;margin-bottom:14px;">
                <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">
                    <i class="fas fa-building me-1"></i>{{ Auth::user()->agent->service->nom ?? 'Service' }}
                </span>
                <span class="badge-status" style="background:#F3F4F6;color:#374151;">
                    {{ Auth::user()->agent->fonction ?? 'Fonction' }}
                </span>
            </div>
            <a href="#" class="action-btn action-btn-outline w-100" style="justify-content:center;">
                <i class="fas fa-user-edit"></i> Modifier mon profil
            </a>
        </div>
    </div>

    {{-- Soldes congés --}}
    <div class="col-12 col-lg-9">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;"><i class="fas fa-umbrella-beach me-2" style="color:#D97706;"></i>Mes soldes de congés</div>
                <a href="#" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">Historique <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-3 mb-3">
                @php
                    $soldes = [
                        ['Annuels','18','30','#0A4D8C','#EFF6FF','fa-sun'],
                        ['Maladie','15','15','#059669','#ECFDF5','fa-briefcase-medical'],
                        ['Exceptionnels','5','10','#D97706','#FFFBEB','fa-star'],
                        ['Récupération','3','—','#7C3AED','#F5F3FF','fa-clock'],
                    ];
                @endphp
                @foreach($soldes as [$label,$val,$total,$color,$bg,$icon])
                <div class="col-6 col-md-3">
                    <div class="solde-bloc">
                        <i class="fas {{ $icon }} mb-2" style="color:{{ $color }};font-size:20px;"></i>
                        <div class="solde-number" style="color:{{ $color }};">{{ $val }}</div>
                        <div style="font-size:11px;color:#9CA3AF;margin-top:2px;">{{ $label }}</div>
                        @if($total !== '—')
                            <div style="font-size:10px;color:#D1D5DB;">/ {{ $total }} jours</div>
                            <div class="progress-thin">
                                <div class="progress-fill" style="width:{{ round(intval($val)/intval($total)*100) }}%;background:{{ $color }};"></div>
                            </div>
                        @else
                            <div style="font-size:10px;color:#D1D5DB;">heures cumulées</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <a href="#" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> Nouvelle demande de congé
            </a>
        </div>
    </div>
</div>

{{-- ─── PLANNING + DEMANDES ──────────────────────────────────────── --}}
<div class="section-title">Planning & suivi</div>
<div class="row g-3 mb-4">
    {{-- Planning semaine --}}
    <div class="col-12 col-lg-7">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;">Mon planning — Semaine {{ now()->weekOfYear }}</div>
                    <div style="font-size:12px;color:#9CA3AF;">{{ now()->startOfWeek()->isoFormat('D MMM') }} — {{ now()->endOfWeek()->isoFormat('D MMM YYYY') }}</div>
                </div>
                <a href="#" class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                    <i class="fas fa-calendar-week"></i> Mois
                </a>
            </div>
            @php
                $planning = [['Lun','jour','#3B82F6','07:00-15:00'],['Mar','jour','#3B82F6','07:00-15:00'],['Mer','nuit','#6366F1','19:00-07:00'],['Jeu','nuit','#6366F1','19:00-07:00'],['Ven','repos','#9CA3AF','—'],['Sam','repos','#9CA3AF','—'],['Dim','garde','#EF4444','07:00-19:00']];
                $today = now()->dayOfWeekIso - 1;
            @endphp
            <div class="row g-2">
                @foreach($planning as $i => [$jour,$type,$color,$horaire])
                <div class="col">
                    <div class="planning-cell {{ $i === $today ? 'today' : '' }}">
                        <div style="font-size:10px;font-weight:600;color:{{ $i===$today?'#1565C0':'#9CA3AF' }};margin-bottom:4px;">{{ $jour }}</div>
                        <div style="font-size:13px;font-weight:700;color:{{ $i===$today?'#0A4D8C':'#374151' }};margin-bottom:8px;">{{ now()->startOfWeek()->addDays($i)->format('d') }}</div>
                        <span style="display:block;background:{{ $color }};color:white;border-radius:6px;font-size:10px;padding:3px 4px;font-weight:600;">
                            @switch($type)
                                @case('jour')  <i class="fas fa-sun"></i>       @break
                                @case('nuit')  <i class="fas fa-moon"></i>      @break
                                @case('garde') <i class="fas fa-heartbeat"></i> @break
                                @case('repos') <i class="fas fa-bed"></i>       @break
                            @endswitch
                        </span>
                        @if($horaire !== '—')
                            <div style="font-size:9px;color:#9CA3AF;margin-top:4px;">{{ $horaire }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div style="background:#ECFDF5;border-radius:8px;padding:10px 14px;margin-top:14px;font-size:12.5px;color:#065F46;">
                <i class="fas fa-calendar-check me-2"></i>
                <strong>Prochaine garde :</strong> Dimanche — 07:00 à 19:00
            </div>
        </div>
    </div>

    {{-- Mes demandes en cours --}}
    <div class="col-12 col-lg-5">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Mes demandes en cours</div>
                <span class="badge-status" style="background:#FEF3C7;color:#92400E;">2</span>
            </div>
            <div class="data-row">
                <div>
                    <div style="font-size:13px;font-weight:500;color:#111827;"><i class="fas fa-sun me-1" style="color:#0A4D8C;"></i>Congé annuel</div>
                    <div style="font-size:11px;color:#9CA3AF;">01/04 — 10/04/2026 · 8 jours ouvrés</div>
                    <div style="font-size:11px;color:#6B7280;margin-top:2px;">Soumise le 10/03</div>
                </div>
                <span class="badge-status" style="background:#FEF3C7;color:#92400E;">
                    <i class="fas fa-hourglass-half me-1"></i>Manager
                </span>
            </div>
            <div class="data-row">
                <div>
                    <div style="font-size:13px;font-weight:500;color:#111827;"><i class="fas fa-star me-1" style="color:#D97706;"></i>Congé exceptionnel</div>
                    <div style="font-size:11px;color:#9CA3AF;">20/03/2026 · 1 jour (Mariage)</div>
                    <div style="font-size:11px;color:#6B7280;margin-top:2px;">Approuvée Manager</div>
                </div>
                <span class="badge-status" style="background:#DBEAFE;color:#1E40AF;">
                    <i class="fas fa-user-tie me-1"></i>RH
                </span>
            </div>
            <a href="#" class="action-btn action-btn-outline w-100 mt-3" style="justify-content:center;font-size:12px;">
                <i class="fas fa-history"></i> Historique complet
            </a>
        </div>
    </div>
</div>

{{-- ─── DOCUMENTS + ALERTES ─────────────────────────────────────── --}}
<div class="section-title">Documents & notifications</div>
<div class="row g-3 mb-4">
    {{-- Documents récents --}}
    <div class="col-12 col-lg-8">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;"><i class="fas fa-folder-open me-2" style="color:#3B82F6;"></i>Mes documents</div>
                <a href="#" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">Voir tous <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            @foreach([['fa-file-pdf','text-danger','Contrat de travail','Contrat','15/09/2020'],['fa-file-pdf','text-danger','Bulletin de paie — Février 2026','Paie','28/02/2026'],['fa-file-pdf','text-danger','Attestation de travail','Attestation','05/03/2026'],['fa-file-image','text-info','Certificat médical','Médical','12/03/2026']] as [$icon,$cls,$nom,$cat,$date])
            <div class="data-row">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas {{ $icon }} {{ $cls }} fa-lg"></i>
                    <div>
                        <div style="font-size:13px;font-weight:500;color:#111827;">{{ $nom }}</div>
                        <div style="font-size:11px;color:#9CA3AF;">{{ $cat }} · {{ $date }}</div>
                    </div>
                </div>
                <a href="#" class="action-btn action-btn-outline" style="padding:6px 12px;font-size:12px;">
                    <i class="fas fa-download"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Notifications --}}
    <div class="col-12 col-lg-4">
        <div class="panel h-100">
            <div class="fw-600 mb-3" style="color:#111827;"><i class="fas fa-bell me-2" style="color:#D97706;"></i>Notifications</div>
            <div style="background:#D1FAE5;border-left:3px solid #059669;border-radius:6px;padding:10px 12px;margin-bottom:10px;">
                <div style="font-size:12.5px;font-weight:600;color:#065F46;"><i class="fas fa-check-circle me-1"></i>Congé approuvé !</div>
                <div style="font-size:11px;color:#059669;margin-top:2px;">Congé exceptionnel du 20/03 approuvé par le RH.</div>
                <div style="font-size:10px;color:#9CA3AF;margin-top:4px;">Il y a 2 heures</div>
            </div>
            <div style="background:#DBEAFE;border-left:3px solid #3B82F6;border-radius:6px;padding:10px 12px;">
                <div style="font-size:12.5px;font-weight:600;color:#1E40AF;"><i class="fas fa-calendar-check me-1"></i>Planning mis à jour</div>
                <div style="font-size:11px;color:#2563EB;margin-top:2px;">Planning d'Avril publié par votre manager.</div>
                <div style="font-size:10px;color:#9CA3AF;margin-top:4px;">Hier</div>
            </div>
        </div>
    </div>
</div>

{{-- ─── ACTIONS RAPIDES ──────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;">
    <div class="fw-600 mb-3" style="color:#0A4D8C;">Actions rapides</div>
    <div class="d-flex flex-wrap gap-2">
        <a href="#" class="action-btn action-btn-primary"><i class="fas fa-umbrella-beach"></i> Demander un congé</a>
        <a href="#" class="action-btn action-btn-danger"><i class="fas fa-user-clock"></i> Déclarer une absence</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-file-certificate"></i> Demander attestation</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-calendar-alt"></i> Mon planning complet</a>
    </div>
</div>

@endsection
