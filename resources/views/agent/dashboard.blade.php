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
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:0.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.purple::before { background:#7C3AED; }

.action-btn { display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,0.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#0A4D8C;border:1.5px solid #BFDBFE; }
.action-btn-outline:hover { background:#EFF6FF;color:#0A4D8C; }

.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;padding-bottom:6px; }
.panel { border-radius:12px;padding:20px;background:white;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.data-row { display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #F9FAFB; }
.data-row:last-child { border-bottom:none; }
.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }

.avatar-cercle { width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:28px;font-weight:700;box-shadow:0 4px 15px rgba(10,77,140,0.3);margin:0 auto 12px;overflow:hidden; }

.solde-bloc { border-radius:10px;padding:14px;text-align:center;transition:all 180ms; }
.solde-bloc:hover { box-shadow:0 3px 12px rgba(0,0,0,0.08);transform:translateY(-2px); }
.solde-number { font-size:26px;font-weight:700;line-height:1; }
.progress-thin { height:5px;border-radius:3px;overflow:hidden;margin-top:6px; }
.progress-fill { height:100%;border-radius:3px; }

.planning-cell { border-radius:8px;padding:10px 6px;text-align:center;transition:all 180ms;border:1.5px solid transparent; }
.planning-cell.today { border-color:#1565C0;background:#EFF6FF;box-shadow:0 0 0 2px rgba(21,101,192,0.2); }
</style>
@endpush

@section('content')

@if(!empty($noAgent))
<div style="max-width:520px;margin:80px auto;text-align:center;">
    <div style="width:72px;height:72px;border-radius:50%;background:#FEF3C7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px;">
        <i class="fas fa-user-clock" style="color:#D97706;"></i>
    </div>
    <h5 style="font-weight:700;margin-bottom:8px;">Dossier agent non complété</h5>
    <p style="font-size:13px;color:var(--theme-text-muted);margin-bottom:24px;">
        Votre compte est créé mais votre dossier agent n'a pas encore été rempli par le service RH.
        Contactez l'Agent RH pour finaliser votre profil.
    </p>
</div>
@else

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">
            Bonjour, {{ $agent->prenom }} 
        </h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            {{ now()->isoFormat('dddd D MMMM YYYY') }}
            — {{ $agent->service->nom_service ?? 'Mon service' }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('agent.docs.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-file-alt"></i> Mes documents
        </a>
        <a href="{{ route('agent.conges.create') }}" class="action-btn action-btn-primary">
            <i class="fas fa-umbrella-beach"></i> Demander un congé
        </a>
    </div>
</div>

{{-- ─── KPI RAPIDES ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card blue" style="background:#EFF6FF;border:1px solid #BFDBFE;">
            <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-umbrella-beach" style="color:#0A4D8C;"></i></div>
            <div class="kpi-value" style="color:#0A4D8C;">{{ $demandesEnAttente }}</div>
            <div class="kpi-label" style="color:#1E40AF;">Demandes en attente</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card green" style="background:#ECFDF5;border:1px solid #A7F3D0;">
            <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
            <div class="kpi-value" style="color:#059669;">{{ $demandesApprouvees }}</div>
            <div class="kpi-label" style="color:#065F46;">Congés approuvés</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card amber" style="background:#FFFBEB;border:1px solid #FDE68A;">
            <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-user-clock" style="color:#D97706;"></i></div>
            <div class="kpi-value" style="color:#D97706;">{{ $absencesCeMois }}</div>
            <div class="kpi-label" style="color:#92400E;">Absences ce mois</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card purple" style="background:#F5F3FF;border:1px solid #DDD6FE;">
            <div class="kpi-icon" style="background:#EDE9FE;"><i class="fas fa-calendar-check" style="color:#7C3AED;"></i></div>
            <div class="kpi-value" style="color:#7C3AED;">
                @if($prochainPoste)
                    {{ $prochainPoste->date_poste->isoFormat('D MMM') }}
                @else
                    —
                @endif
            </div>
            <div class="kpi-label" style="color:#5B21B6;">Prochain poste</div>
        </div>
    </div>
</div>

{{-- ─── PROFIL + SOLDES ──────────────────────────────────────────── --}}
<div class="section-title" style="color:#9CA3AF;">Mon dossier</div>
<div class="row g-3 mb-4">

    {{-- Carte profil --}}
    <div class="col-12 col-lg-3">
        <div class="panel text-center">
            <div class="avatar-cercle">
                @if($agent->photo)
                    <img src="{{ asset('storage/'.$agent->photo) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
                @endif
            </div>
            <div style="font-size:16px;font-weight:700;color:#111827;">
                {{ $agent->prenom }} {{ $agent->nom }}
            </div>
            <div style="font-size:12px;color:#9CA3AF;margin:4px 0 10px;">
                <i class="fas fa-id-card me-1"></i>{{ $agent->matricule }}
            </div>
            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;margin-bottom:14px;">
                <span class="badge-status" style="background:#EFF6FF;color:#1E40AF;">
                    <i class="fas fa-building me-1"></i>{{ $agent->service->nom_service ?? '—' }}
                </span>
                <span class="badge-status" style="background:#F3F4F6;color:#374151;">
                    {{ $agent->fontion }}
                </span>
            </div>
            <a href="{{ route('agent.profil') }}" class="action-btn action-btn-outline w-100" style="justify-content:center;">
                <i class="fas fa-user-edit"></i> Mon profil
            </a>
            @if($contratActif)
            <a href="{{ route('agent.mon-contrat') }}" class="action-btn action-btn-outline w-100 mt-2" style="justify-content:center;">
                <i class="fas fa-file-contract"></i> Mon contrat
            </a>
            @endif
        </div>
    </div>

    {{-- Soldes congés --}}
    <div class="col-12 col-lg-9">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;"><i class="fas fa-umbrella-beach me-2" style="color:#D97706;"></i>Mes soldes de congés — {{ date('Y') }}</div>
                <a href="{{ route('agent.conges.index') }}" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">Historique <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            @if($soldesConges->isEmpty())
                <div class="text-center py-4" style="color:#9CA3AF;">
                    <i class="fas fa-calendar-times fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                    <div style="font-size:13px;">Aucun solde de congé pour cette année.</div>
                    <div style="font-size:12px;margin-top:4px;">Contactez le service RH pour initialiser vos soldes.</div>
                </div>
            @else
            @php
                $soldeColors = [
                    'Annuel'        => ['#0A4D8C','#EFF6FF','fa-sun'],
                    'Maladie'       => ['#059669','#ECFDF5','fa-briefcase-medical'],
                    'Maternité'     => ['#7C3AED','#F5F3FF','fa-baby'],
                    'Exceptionnel'  => ['#D97706','#FFFBEB','fa-star'],
                    'Récupération'  => ['#6B7280','#F3F4F6','fa-clock'],
                ];
            @endphp
            <div class="row g-3 mb-3">
                @foreach($soldesConges as $solde)
                @php
                    $libelle = $solde->typeConge->libelle ?? 'Autre';
                    // Match key by searching in soldeColors
                    $colorKey = collect(array_keys($soldeColors))->first(fn($k) => str_contains($libelle, $k)) ?? null;
                    [$color,$bg,$icon] = $soldeColors[$colorKey] ?? ['#9CA3AF','#F9FAFB','fa-calendar'];
                    $pct = $solde->solde_initial > 0 ? round($solde->solde_restant / $solde->solde_initial * 100) : 0;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="solde-bloc" style="background:{{ $bg }};border:1px solid {{ $bg }};">
                        <i class="fas {{ $icon }} mb-2" style="color:{{ $color }};font-size:20px;"></i>
                        <div class="solde-number" style="color:{{ $color }};">{{ $solde->solde_restant }}</div>
                        <div style="font-size:11px;color:#9CA3AF;margin-top:2px;">{{ $libelle }}</div>
                        <div style="font-size:10px;color:#D1D5DB;">/ {{ $solde->solde_initial }} jours</div>
                        <div class="progress-thin" style="background:rgba(0,0,0,.08);">
                            <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            <a href="{{ route('agent.conges.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> Nouvelle demande de congé
            </a>
        </div>
    </div>
</div>

{{-- ─── PLANNING + DEMANDES ──────────────────────────────────────── --}}
<div class="section-title" style="color:#9CA3AF;">Planning & suivi</div>
<div class="row g-3 mb-4">

    {{-- Planning semaine --}}
    <div class="col-12 col-lg-7">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;">Mon planning — Semaine {{ now()->weekOfYear }}</div>
                    <div style="font-size:12px;color:#9CA3AF;">
                        {{ now()->startOfWeek()->isoFormat('D MMM') }} — {{ now()->endOfWeek()->isoFormat('D MMM YYYY') }}
                    </div>
                </div>
                <a href="{{ route('agent.planning') }}" class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                    <i class="fas fa-calendar-week"></i> Planning complet
                </a>
            </div>
            @php
                $colorMapPlanning = [
                    'Jour'       => ['#3B82F6','#EFF6FF'],
                    'Nuit'       => ['#4F46E5','#EEF2FF'],
                    'Garde'      => ['#F59E0B','#FFFBEB'],
                    'Repos'      => ['#9CA3AF','#F3F4F6'],
                    'Astreinte'  => ['#8B5CF6','#F5F3FF'],
                    'Permanence' => ['#0D9488','#F0FDFA'],
                ];
                $iconMap = ['Jour'=>'fa-sun','Nuit'=>'fa-moon','Garde'=>'fa-heartbeat','Repos'=>'fa-bed','Astreinte'=>'fa-bell','Permanence'=>'fa-shield-alt'];
                $todayIdx = now()->dayOfWeekIso - 1;
                $joursLabels = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
            @endphp
            <div class="row g-2">
                @for($i = 0; $i <= 6; $i++)
                @php
                    $ligne = $planningCetteSemaine->get($i);
                    $isToday = $i === $todayIdx;
                    $typeLib = $ligne ? ($ligne->typePoste->libelle ?? 'Autre') : null;
                    [$clr,$bg2] = $colorMapPlanning[$typeLib] ?? ['#9CA3AF','#F3F4F6'];
                    $ico = $iconMap[$typeLib] ?? 'fa-question';
                    $dayDate = now()->startOfWeek()->addDays($i);
                    $hd = $ligne ? (is_string($ligne->heure_debut) ? substr($ligne->heure_debut,0,5) : $ligne->heure_debut->format('H:i')) : null;
                    $hf = $ligne ? (is_string($ligne->heure_fin) ? substr($ligne->heure_fin,0,5) : $ligne->heure_fin->format('H:i')) : null;
                @endphp
                <div class="col">
                    <div class="planning-cell {{ $isToday ? 'today' : '' }}" style="{{ !$isToday ? 'background:#FAFAFA;border-color:#F3F4F6;' : '' }}">
                        <div style="font-size:10px;font-weight:600;color:{{ $isToday ? '#1565C0' : '#9CA3AF' }};margin-bottom:4px;">{{ $joursLabels[$i] }}</div>
                        <div style="font-size:13px;font-weight:700;color:{{ $isToday ? '#0A4D8C' : '#374151' }};margin-bottom:8px;">{{ $dayDate->format('d') }}</div>
                        @if($ligne)
                            <span style="display:block;background:{{ $clr }};color:white;border-radius:6px;font-size:10px;padding:3px 4px;font-weight:600;">
                                <i class="fas {{ $ico }}"></i>
                            </span>
                            <div style="font-size:9px;color:#9CA3AF;margin-top:4px;">{{ $hd }}→{{ $hf }}</div>
                        @else
                            <span style="display:block;background:#F3F4F6;color:#D1D5DB;border-radius:6px;font-size:10px;padding:3px 4px;">
                                <i class="fas fa-minus"></i>
                            </span>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
            @if($prochainPoste)
            <div style="background:#ECFDF5;border-radius:8px;padding:10px 14px;margin-top:14px;font-size:12.5px;color:#065F46;">
                <i class="fas fa-calendar-check me-2"></i>
                <strong>Prochain poste :</strong>
                {{ $prochainPoste->date_poste->isoFormat('dddd D MMM') }} —
                @php
                    $hd2 = is_string($prochainPoste->heure_debut) ? substr($prochainPoste->heure_debut,0,5) : $prochainPoste->heure_debut->format('H:i');
                    $hf2 = is_string($prochainPoste->heure_fin) ? substr($prochainPoste->heure_fin,0,5) : $prochainPoste->heure_fin->format('H:i');
                @endphp
                {{ $hd2 }} à {{ $hf2 }}
                ({{ $prochainPoste->typePoste->libelle ?? '—' }})
            </div>
            @endif
        </div>
    </div>

    {{-- Mes demandes en cours --}}
    <div class="col-12 col-lg-5">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Mes demandes récentes</div>
                @if($demandesEnAttente > 0)
                    <span class="badge-status" style="background:#FEF3C7;color:#92400E;">{{ $demandesEnAttente }}</span>
                @endif
            </div>
            @forelse($mesDemandes as $demande)
            @php
                $statut = $demande->statut_demande;
                $statutMap = [
                    'En_attente' => ['#FEF3C7','#92400E','hourglass-half','Manager'],
                    'Validé'     => ['#DBEAFE','#1E40AF','user-tie','RH'],
                    'Approuvé'   => ['#D1FAE5','#065F46','check-circle','Approuvé'],
                    'Rejeté'     => ['#FEE2E2','#991B1B','times-circle','Rejeté'],
                ];
                [$sbg,$scol,$sico,$slib] = $statutMap[$statut] ?? ['#F3F4F6','#374151','question','?'];
                $typeLib2 = $demande->type_demande === 'Conge' ? ($demande->conge?->typeConge?->libelle ?? 'Congé') : 'Absence';
            @endphp
            <div class="data-row">
                <div>
                    <div style="font-size:13px;font-weight:500;color:#111827;">{{ $typeLib2 }}</div>
                    <div style="font-size:11px;color:#9CA3AF;">
                        Soumise le {{ $demande->created_at->format('d/m/Y') }}
                    </div>
                </div>
                <span class="badge-status" style="background:{{ $sbg }};color:{{ $scol }};">
                    <i class="fas fa-{{ $sico }} me-1"></i>{{ $slib }}
                </span>
            </div>
            @empty
            <div class="text-center py-4" style="color:#9CA3AF;">
                <i class="fas fa-inbox fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                <div style="font-size:13px;">Aucune demande récente</div>
            </div>
            @endforelse
            <a href="{{ route('agent.conges.index') }}" class="action-btn action-btn-outline w-100 mt-3" style="justify-content:center;font-size:12px;">
                <i class="fas fa-history"></i> Historique complet
            </a>
        </div>
    </div>
</div>

{{-- ─── ACTIONS RAPIDES ──────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;">
    <div class="fw-600 mb-3" style="color:#0A4D8C;">Actions rapides</div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('agent.conges.create') }}" class="action-btn action-btn-primary"><i class="fas fa-umbrella-beach"></i> Demander un congé</a>
        <a href="{{ route('agent.absences.index') }}" class="action-btn action-btn-outline"><i class="fas fa-user-clock"></i> Mes absences</a>
        <a href="{{ route('agent.docs.index') }}" class="action-btn action-btn-outline"><i class="fas fa-file-certificate"></i> Demander attestation</a>
        <a href="{{ route('agent.planning') }}" class="action-btn action-btn-outline"><i class="fas fa-calendar-alt"></i> Mon planning complet</a>
        <a href="{{ route('agent.profil') }}" class="action-btn action-btn-outline"><i class="fas fa-user-edit"></i> Mon profil</a>
    </div>
</div>

@endif {{-- noAgent --}}

@endsection
