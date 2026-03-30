@extends('layouts.master')

@section('title', 'Mon Parcours Professionnel')
@section('page-title', 'Mon Parcours Professionnel')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.profil') }}" style="color:#1565C0;">Mon profil</a></li>
    <li>Mon parcours</li>
@endsection

@push('styles')
<style>
/* ── Layout ───────────────────────────────────── */
.parcours-header {
    border-radius:14px;
    padding:28px 32px;
    background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 60%,#1976D2 100%);
    color:#fff;
    position:relative;
    overflow:hidden;
    box-shadow:0 8px 28px rgba(10,77,140,.25);
}
.parcours-header::before {
    content:'';position:absolute;top:-40px;right:-40px;
    width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.07);
}
.ph-stat { text-align:center; }
.ph-stat .val { font-size:26px;font-weight:700;line-height:1; }
.ph-stat .lbl { font-size:11px;opacity:.75;margin-top:2px; }
.ph-divider { width:1px;background:rgba(255,255,255,.2);align-self:stretch;margin:0 8px; }

/* ── Timeline ─────────────────────────────────── */
.timeline { position:relative;padding-left:52px; }
.timeline::before {
    content:'';
    position:absolute;left:19px;top:0;bottom:0;
    width:2px;background:linear-gradient(to bottom,#BFDBFE,#E5E7EB);
    border-radius:2px;
}

.tl-item { position:relative;margin-bottom:28px; }
.tl-item:last-child { margin-bottom:0; }

/* Icône sur la timeline */
.tl-dot {
    position:absolute;left:-52px;top:16px;
    width:38px;height:38px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:15px;border:2px solid #fff;
    box-shadow:0 2px 8px rgba(0,0,0,.12);
    flex-shrink:0;
}

/* Carte mouvement */
.tl-card {
    border-radius:12px;padding:18px 20px;
    border:1.5px solid var(--theme-border);
    background:var(--theme-panel-bg);
    transition:box-shadow 160ms,border-color 160ms;
}
.tl-card:hover {
    box-shadow:0 6px 20px rgba(10,77,140,.10);
    border-color:#BFDBFE;
}
.tl-card-title {
    font-size:14px;font-weight:700;
    color:var(--theme-text);margin-bottom:4px;
}
.tl-card-meta {
    font-size:12px;color:var(--theme-text-muted);
    display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.tl-card-date {
    font-size:11px;font-weight:600;
    padding:3px 9px;border-radius:20px;
    background:#EFF6FF;color:#1D4ED8;
    display:inline-flex;align-items:center;gap:5px;
}
.tl-badge {
    display:inline-flex;align-items:center;gap:5px;
    padding:3px 9px;border-radius:20px;
    font-size:11px;font-weight:600;
}

/* ── Contrats ─────────────────────────────────── */
.contrat-card {
    border-radius:12px;padding:18px 22px;
    border-left:4px solid;
    background:var(--theme-panel-bg);
    border-top:1.5px solid var(--theme-border);
    border-right:1.5px solid var(--theme-border);
    border-bottom:1.5px solid var(--theme-border);
    margin-bottom:14px;
    transition:box-shadow 160ms;
}
.contrat-card:hover { box-shadow:0 4px 14px rgba(10,77,140,.09); }
.contrat-card:last-child { margin-bottom:0; }
.ctag { padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600; }

/* ── Empty ────────────────────────────────────── */
.empty-state {
    text-align:center;padding:52px 20px;
    border:1.5px dashed var(--theme-border);
    border-radius:14px;background:var(--theme-bg-secondary);
}
.empty-icon { font-size:40px;color:var(--theme-text-muted);margin-bottom:12px;opacity:.4; }

/* ── Section title ────────────────────────────── */
.s-title {
    font-size:12px;font-weight:700;text-transform:uppercase;
    letter-spacing:.05em;color:var(--theme-text-muted);
    margin-bottom:18px;padding-bottom:8px;
    border-bottom:1.5px solid var(--theme-border);
    display:flex;align-items:center;gap:8px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-xl-10">

{{-- ── En-tête ──────────────────────────────────────────────── --}}
<div class="parcours-header mb-4">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div class="flex-grow-1">
            <p class="mb-1 text-white-50" style="font-size:13px;">
                <i class="fas fa-user me-1"></i>{{ $agent->matricule }}
            </p>
            <h4 class="fw-bold mb-1" style="font-size:20px;">
                {{ $agent->prenom }} {{ $agent->nom }}
            </h4>
            <p class="mb-0 text-white-75" style="font-size:13px;opacity:.85;">
                {{ $agent->service?->nom_service ?? 'Service non défini' }}
                @if($agent->fontion) · {{ $agent->fontion }}@endif
            </p>
        </div>

        {{-- KPIs rapides --}}
        <div class="d-flex gap-3 align-items-center">
            @php
                $nbMouvements = $mouvements->count();
                $anneesSvc    = $agent->date_prise_service
                    ? (int) $agent->date_prise_service->diffInYears(now())
                    : null;
                $contrats     = $agent->contrats()->orderByDesc('date_debut')->get();
            @endphp

            <div class="ph-stat">
                <div class="val">{{ $nbMouvements }}</div>
                <div class="lbl">Mouvement{{ $nbMouvements > 1 ? 's' : '' }}</div>
            </div>
            <div class="ph-divider"></div>
            <div class="ph-stat">
                <div class="val">{{ $contrats->count() }}</div>
                <div class="lbl">Contrat{{ $contrats->count() > 1 ? 's' : '' }}</div>
            </div>
            @if($anneesSvc !== null)
            <div class="ph-divider"></div>
            <div class="ph-stat">
                <div class="val">{{ $anneesSvc }}</div>
                <div class="lbl">An{{ $anneesSvc > 1 ? 's' : '' }} de service</div>
            </div>
            @endif
        </div>
    </div>

    @if($agent->date_prise_service)
    <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.15);">
        <small class="text-white-50" style="font-size:11px;">
            <i class="fas fa-calendar-alt me-1"></i>
            Entrée en service le <strong class="text-white">{{ $agent->date_prise_service->format('d/m/Y') }}</strong>
        </small>
    </div>
    @endif
</div>

<div class="row g-4">

    {{-- ── Colonne gauche : Historique mouvements ───────────── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius:14px;background:var(--theme-panel-bg);">
            <div class="card-body p-4">
                <div class="s-title">
                    <i class="fas fa-route" style="color:#0A4D8C;"></i>
                    Historique des mouvements
                    @if($nbMouvements > 0)
                        <span style="background:#DBEAFE;color:#1D4ED8;border-radius:20px;padding:2px 9px;font-size:11px;margin-left:auto;">
                            {{ $nbMouvements }}
                        </span>
                    @endif
                </div>

                @if($mouvements->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-route"></i></div>
                        <p class="mb-0 fw-500" style="font-size:14px;color:var(--theme-text-muted);">
                            Aucun mouvement enregistré
                        </p>
                        <p class="mb-0 mt-1" style="font-size:12px;color:var(--theme-text-muted);">
                            Votre historique d'affectations apparaîtra ici.
                        </p>
                    </div>
                @else
                    <div class="timeline">
                        @foreach($mouvements as $mvt)
                            @php
                                $type = \App\Models\Mouvement::TYPES[$mvt->type_mouvement]
                                    ?? ['label' => $mvt->type_mouvement, 'icon' => 'fa-circle',
                                        'color' => '#6B7280', 'bg' => '#F3F4F6', 'description' => ''];
                                $statut = \App\Models\Mouvement::STATUTS[$mvt->statut]
                                    ?? ['label' => $mvt->statut, 'color' => '#6B7280', 'bg' => '#F3F4F6', 'icon' => 'fa-question'];
                            @endphp
                            <div class="tl-item">
                                {{-- Point timeline --}}
                                <div class="tl-dot" style="background:{{ $type['bg'] }};color:{{ $type['color'] }};">
                                    <i class="fas {{ $type['icon'] }}"></i>
                                </div>

                                <div class="tl-card">
                                    <div class="d-flex align-items-start gap-3 flex-wrap">
                                        <div class="flex-grow-1">
                                            <div class="tl-card-title">
                                                {{ $type['label'] }}
                                            </div>
                                            <div class="tl-card-meta mb-2">
                                                <span class="tl-card-date">
                                                    <i class="fas fa-calendar-day"></i>
                                                    {{ $mvt->date_mouvement?->format('d/m/Y') ?? '—' }}
                                                </span>
                                                <span class="tl-badge" style="background:{{ $statut['bg'] }};color:{{ $statut['color'] }};">
                                                    <i class="fas {{ $statut['icon'] }}" style="font-size:9px;"></i>
                                                    {{ $statut['label'] }}
                                                </span>
                                            </div>

                                            {{-- Services --}}
                                            <div class="d-flex align-items-center gap-2 flex-wrap mt-2" style="font-size:12px;">
                                                @if($mvt->serviceOrigine)
                                                    <span style="background:#FEF3C7;color:#92400E;padding:3px 9px;border-radius:8px;">
                                                        <i class="fas fa-door-open me-1" style="font-size:10px;"></i>
                                                        {{ $mvt->serviceOrigine->nom_service }}
                                                    </span>
                                                    <i class="fas fa-arrow-right" style="color:#9CA3AF;font-size:10px;"></i>
                                                @endif
                                                @if($mvt->serviceDestination)
                                                    <span style="background:#D1FAE5;color:#065F46;padding:3px 9px;border-radius:8px;">
                                                        <i class="fas fa-door-closed me-1" style="font-size:10px;"></i>
                                                        {{ $mvt->serviceDestination->nom_service }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Motif --}}
                                            @if($mvt->motif)
                                            <div class="mt-2 p-2 rounded" style="background:var(--theme-bg-secondary);font-size:12px;color:var(--theme-text-muted);border-left:3px solid {{ $type['color'] }};">
                                                <i class="fas fa-comment-alt me-1" style="font-size:10px;"></i>
                                                {{ Str::limit($mvt->motif, 120) }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Colonne droite : Contrats + Infos actuelles ──────── --}}
    <div class="col-lg-5">

        {{-- Situation actuelle --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;background:var(--theme-panel-bg);">
            <div class="card-body p-4">
                <div class="s-title">
                    <i class="fas fa-id-badge" style="color:#10B981;"></i> Situation actuelle
                </div>
                <div class="row g-3" style="font-size:13px;">
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px;">
                            Fonction
                        </div>
                        <div style="font-weight:500;color:var(--theme-text);">
                            {{ $agent->fontion ?? '—' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px;">
                            Grade
                        </div>
                        <div style="font-weight:500;color:var(--theme-text);">
                            {{ $agent->grade ?? '—' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px;">
                            Catégorie CSP
                        </div>
                        <div style="font-weight:500;color:var(--theme-text);">
                            {{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px;">
                            Famille d'emploi
                        </div>
                        <div style="font-weight:500;color:var(--theme-text);">
                            {{ $agent->famille_d_emploi ? str_replace('_', ' ', $agent->famille_d_emploi) : '—' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px;">
                            Service
                        </div>
                        <div style="font-weight:500;color:var(--theme-text);">
                            {{ $agent->service?->nom_service ?? '—' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px;">
                            Prise de service
                        </div>
                        <div style="font-weight:500;color:var(--theme-text);">
                            {{ $agent->date_prise_service?->format('d/m/Y') ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historique contrats --}}
        <div class="card border-0 shadow-sm" style="border-radius:14px;background:var(--theme-panel-bg);">
            <div class="card-body p-4">
                <div class="s-title">
                    <i class="fas fa-file-contract" style="color:#0A4D8C;"></i>
                    Historique des contrats
                    @if($contrats->count() > 0)
                        <span style="background:#DBEAFE;color:#1D4ED8;border-radius:20px;padding:2px 9px;font-size:11px;margin-left:auto;">
                            {{ $contrats->count() }}
                        </span>
                    @endif
                </div>

                @if($contrats->isEmpty())
                    <div class="empty-state" style="padding:30px 20px;">
                        <div class="empty-icon" style="font-size:28px;"><i class="fas fa-file-contract"></i></div>
                        <p class="mb-0" style="font-size:13px;color:var(--theme-text-muted);">Aucun contrat enregistré</p>
                    </div>
                @else
                    @foreach($contrats as $contrat)
                        @php
                            $statutInfo = \App\Models\Contrat::STATUTS[$contrat->statut_contrat]
                                ?? ['label' => $contrat->statut_contrat, 'color' => '#6B7280', 'bg' => '#F3F4F6'];
                            $borderColor = $contrat->statut_contrat === 'Actif' ? '#10B981'
                                : ($contrat->statut_contrat === 'Expiré' ? '#EF4444' : '#9CA3AF');
                        @endphp
                        <div class="contrat-card" style="border-left-color:{{ $borderColor }};">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:var(--theme-text);">
                                        {{ \App\Models\Contrat::TYPES[$contrat->type_contrat] ?? $contrat->type_contrat }}
                                    </div>
                                    <div style="font-size:11px;color:var(--theme-text-muted);margin-top:2px;">
                                        <i class="fas fa-clock me-1"></i>{{ $contrat->duree }}
                                    </div>
                                </div>
                                <span class="ctag" style="background:{{ $statutInfo['bg'] }};color:{{ $statutInfo['color'] }};white-space:nowrap;">
                                    {{ $statutInfo['label'] }}
                                </span>
                            </div>
                            <div class="d-flex gap-3 flex-wrap" style="font-size:12px;color:var(--theme-text-muted);">
                                <span>
                                    <i class="fas fa-play me-1" style="color:#10B981;font-size:9px;"></i>
                                    {{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}
                                </span>
                                @if($contrat->date_fin)
                                <span>
                                    <i class="fas fa-stop me-1" style="color:#EF4444;font-size:9px;"></i>
                                    {{ $contrat->date_fin->format('d/m/Y') }}
                                </span>
                                @else
                                <span style="color:#10B981;font-weight:500;">
                                    <i class="fas fa-infinity me-1" style="font-size:9px;"></i>CDI
                                </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>{{-- /row --}}

{{-- ── Actions --}}
<div class="d-flex gap-3 mt-4 justify-content-end flex-wrap">
    <a href="{{ route('agent.profil') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;
              font-size:13px;font-weight:500;background:var(--theme-panel-bg);color:var(--theme-text);
              border:1.5px solid var(--theme-border);text-decoration:none;transition:all 180ms;">
        <i class="fas fa-arrow-left"></i> Retour au profil
    </a>
    <a href="{{ route('agent.mon-contrat') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;
              font-size:13px;font-weight:500;background:#0A4D8C;color:#fff;
              border:1.5px solid #0A4D8C;text-decoration:none;transition:all 180ms;">
        <i class="fas fa-file-contract"></i> Mon contrat actif
    </a>
</div>

</div>{{-- /col --}}
</div>{{-- /row --}}
</div>{{-- /container --}}
@endsection
