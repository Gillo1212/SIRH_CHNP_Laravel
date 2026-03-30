@extends('layouts.master')
@section('title', 'Dossier — ' . ($dossier->agent?->nom_complet ?? 'Agent'))
@section('page-title', 'Dossier Agent')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.ged.index') }}" style="color:#1565C0;">GED</a></li>
    <li><a href="{{ route('rh.ged.dossiers') }}" style="color:#1565C0;">Dossiers</a></li>
    <li>{{ $dossier->reference }}</li>
@endsection

@push('styles')
<style>
/* ── HERO ──────────────────────────────────────────────────────── */
.dossier-hero {
    background: linear-gradient(135deg, #0A4D8C 0%, #1565C0 50%, #1976D2 100%);
    border-radius: 20px; padding: 28px 36px; color: #fff;
    position: relative; overflow: hidden; margin-bottom: 28px;
    box-shadow: 0 12px 40px rgba(10,77,140,.30);
}
.dossier-hero::before { content:''; position:absolute; top:-40px; right:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.07); }
.dossier-hero::after  { content:''; position:absolute; bottom:-60px; left:30%; width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,.05); }
.dh-avatar { width:80px;height:80px;border-radius:50%;border:3px solid rgba(255,255,255,.4);object-fit:cover;flex-shrink:0;font-size:28px;font-weight:700;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center; }
.dh-stat { text-align:center; }
.dh-stat .val { font-size:26px;font-weight:700; }
.dh-stat .lbl { font-size:12px;opacity:.8;margin-top:2px; }
.dh-divider { width:1px;background:rgba(255,255,255,.2);align-self:stretch;margin:0 12px; }

/* ── BOUTONS TOOLBAR ─────────────────────────────────────────── */
.ged-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.ged-btn-primary { background:#0A4D8C;color:#fff; }
.ged-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.ged-btn-outline { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.ged-btn-outline:hover { background:#F9FAFB; }

/* ══ GRILLE D'ENVELOPPES ════════════════════════════════════════ */

/* Chaque cell avec marge haute pour l'onglet saillant */
.env-cell { padding-top: 22px; }

/* Carte enveloppe cliquable */
.env-card-btn {
    position: relative;
    width: 100%;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-top: 3px solid var(--env-color, #374151);
    border-radius: 0 10px 10px 10px;
    padding: 16px 16px 14px;
    cursor: pointer;
    transition: box-shadow 200ms, border-color 200ms;
    text-align: left;
    outline: none;
}
.env-card-btn:hover, .env-card-btn.is-open {
    box-shadow: 0 6px 20px rgba(0,0,0,.09);
}
.env-card-btn.is-open {
    border-color: var(--env-color, #374151);
    background: #FAFBFF;
}

/* Onglet saillant en haut à gauche */
.env-card-onglet {
    position: absolute;
    top: -22px;
    left: 0;
    height: 22px;
    width: 90px;
    background: var(--env-color, #374151);
    border-radius: 6px 6px 0 0;
    clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 100%, 0 100%);
}

/* Icône catégorie */
.env-card-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    background: color-mix(in srgb, var(--env-color, #374151) 12%, #fff);
    display: flex; align-items: center; justify-content: center;
    color: var(--env-color, #374151);
    margin-bottom: 12px;
}
.env-card-label {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
    margin-bottom: 10px;
}
.env-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}
.env-card-count {
    font-size: 11.5px;
    color: #6B7280;
}
.env-card-arrow {
    color: var(--env-color, #374151);
    transition: transform 200ms;
    flex-shrink: 0;
}
.env-card-btn.is-open .env-card-arrow {
    transform: rotate(90deg);
}

/* ── PANNEAU DOCUMENTS (collapsible) ─────────────────────────── */
.env-docs-panel {
    display: none;
    background: #fff;
    border: 1.5px solid var(--env-color, #E5E7EB);
    border-top: 3px solid var(--env-color, #374151);
    border-radius: 0 12px 12px 12px;
    margin-top: 2px;
    margin-bottom: 16px;
    overflow: hidden;
}
.env-docs-panel.is-open { display: block; }

.env-docs-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: color-mix(in srgb, var(--env-color, #374151) 7%, #fff);
    border-bottom: 1px solid #F3F4F6;
    font-size: 13px;
    font-weight: 700;
    color: #111827;
}
.env-docs-header .close-env {
    margin-left: auto;
    width: 26px; height: 26px;
    border-radius: 6px;
    border: none; background: transparent;
    display: flex; align-items: center; justify-content: center;
    color: #6B7280; cursor: pointer;
    transition: background 140ms;
}
.env-docs-header .close-env:hover { background: rgba(0,0,0,.06); }

/* ── LIGNES DOCUMENT dans l'enveloppe ─────────────────────────── */
.doc-row-env {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    transition: background 140ms;
}
.doc-row-env:hover { background: #F9FAFB; }
.doc-row-env + .doc-row-env { border-top: 1px solid #F3F4F6; }

.doc-row-env .dr-body { flex: 1; min-width: 0; }
.doc-row-env .dr-title { font-size: 13.5px; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.doc-row-env .dr-meta  { font-size: 11.5px; color: #9CA3AF; margin-top: 2px; }

/* Actions */
.act-btn {
    width: 30px; height: 30px; border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; cursor: pointer; transition: all 140ms;
    background: transparent; flex-shrink: 0;
    text-decoration: none;
}
.act-btn:hover { background: #F3F4F6; }
.act-btn.blue   { color: #1D4ED8; }
.act-btn.blue:hover   { background: #EFF6FF; }
.act-btn.green  { color: #059669; }
.act-btn.green:hover  { background: #ECFDF5; }
.act-btn.gray   { color: #6B7280; }
.act-btn.gray:hover   { background: #F3F4F6; }
.act-btn.amber  { color: #D97706; }
.act-btn.amber:hover  { background: #FFFBEB; }

/* Badges */
.badge-conf { font-size:10px;padding:2px 7px;border-radius:20px;font-weight:600; }
.badge-conf-public       { background:#ECFDF5;color:#059669; }
.badge-conf-interne      { background:#EFF6FF;color:#1D4ED8; }
.badge-conf-confidentiel { background:#FFFBEB;color:#D97706; }
.badge-conf-secret       { background:#FEF2F2;color:#DC2626; }
.badge-st-actif   { background:#ECFDF5;color:#059669;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:600; }
.badge-st-archive { background:#F3F4F6;color:#6B7280;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:600; }

/* Empty */
.env-empty { text-align:center;padding:24px;color:#CBD5E1;font-size:13px; }

/* ── LOCK CONFIDENTIEL ───────────────────────────────────────── */
.lock-banner {
    background: #FFFBEB; border: 1px solid #FDE68A;
    border-radius: 8px; padding: 8px 14px; font-size: 12px;
    color: #92400E; display: flex; align-items: center; gap: 8px;
    margin: 0 16px 10px;
}

/* ── SIDEBAR CARDS ───────────────────────────────────────────── */
.info-card { background:#fff;border:1px solid #F3F4F6;border-radius:14px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.04); }
.info-card-hd { font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:14px; }

/* ── AUDIT ───────────────────────────────────────────────────── */
.audit-item { display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #F3F4F6; }
.audit-item:last-child { border-bottom:none; }
.audit-dot { width:8px;height:8px;border-radius:50%;background:#0A4D8C;flex-shrink:0;margin-top:5px; }
.audit-body { font-size:12.5px; }
.audit-body .av-user { font-weight:600;color:#111827; }
.audit-body .av-time { color:#9CA3AF; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 mb-3" style="border-radius:10px;background:#ECFDF5;color:#065F46;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── HERO (inchangé) ──────────────────────────────────────── --}}
    @php $agent = $dossier->agent; @endphp
    <div class="dossier-hero">
        <div class="d-flex align-items-center gap-4 flex-wrap" style="position:relative;z-index:1;">
            <div class="dh-avatar">
                @if($agent?->photo)
                    <img src="{{ asset('storage/'.$agent->photo) }}" alt="" class="dh-avatar">
                @else
                    {{ strtoupper(substr($agent?->prenom??'?',0,1)) }}{{ strtoupper(substr($agent?->nom??'',0,1)) }}
                @endif
            </div>
            <div class="flex-grow-1">
                <div class="fw-700" style="font-size:22px;">{{ $agent?->nom_complet ?? 'Agent inconnu' }}</div>
                <div style="font-size:14px;opacity:.8;margin-top:4px;">
                    <span class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:3px;"><rect x="2" y="4" width="20" height="16" rx="2"/><circle cx="8" cy="12" r="2"/><path d="M14 10h4M14 14h4"/></svg>{{ $agent?->matricule }}
                    </span>
                    <span class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:3px;"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>{{ $agent?->service?->nom_service }}
                    </span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:3px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>{{ $agent?->fonction }}
                    </span>
                </div>
                <div style="font-size:12px;opacity:.7;margin-top:4px;">
                    {{ $dossier->etagere?->nom_etagere }} &nbsp;·&nbsp; {{ $dossier->reference }} &nbsp;·&nbsp; Créé le {{ $dossier->date_creation?->format('d/m/Y') }}
                </div>
            </div>
            <div class="d-flex gap-3">
                <div class="dh-stat">
                    <div class="val">{{ $statsDoc['actifs'] }}</div>
                    <div class="lbl">Actifs</div>
                </div>
                <div class="dh-divider"></div>
                <div class="dh-stat">
                    <div class="val">{{ $statsDoc['archives'] }}</div>
                    <div class="lbl">Archivés</div>
                </div>
                <div class="dh-divider"></div>
                <div class="dh-stat">
                    <div class="val">{{ $statsDoc['total'] }}</div>
                    <div class="lbl">Total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TOOLBAR ──────────────────────────────────────────────── --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('rh.ged.documents.create', ['dossier' => $dossier->id_dossier]) }}" class="ged-btn ged-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Déposer un document
        </a>
        @if($agent)
        <a href="{{ route('rh.agents.show', $agent->id_agent) }}" class="ged-btn ged-btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Dossier RH
        </a>
        @endif
        <a href="{{ route('rh.ged.dossiers') }}" class="ged-btn ged-btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
    </div>

    <div class="row g-4">

        {{-- ── COL PRINCIPALE : Grille d'enveloppes + panneaux docs ── --}}
        <div class="col-xl-8">

            @if($documents->isEmpty())
                <div class="text-center py-5 text-muted" style="background:#fff;border:1px dashed #E5E7EB;border-radius:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" style="margin-bottom:16px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <p class="fw-500 mb-3">Ce dossier est vide</p>
                    <a href="{{ route('rh.ged.documents.create', ['dossier' => $dossier->id_dossier]) }}" class="ged-btn ged-btn-primary">
                        Déposer le premier document
                    </a>
                </div>
            @else
                @php
                    $docsFlat = $documents->flatten();

                    $envCategories = [
                        'identite' => [
                            'label' => 'Identité & Administratif',
                            'color' => '#1a56a0',
                            'types' => ['Piece_identite', 'Domiciliation', 'Autre'],
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><circle cx="8" cy="12" r="2"/><path d="M14 10h4M14 14h4"/></svg>',
                        ],
                        'carriere' => [
                            'label' => 'Carrière & Contrats',
                            'color' => '#534AB7',
                            'types' => ['Contrat', 'Attestation', 'Décision', 'Nomination', 'Ordre_mission', 'PV', 'Fiche_evaluation'],
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
                        ],
                        'medical' => [
                            'label' => 'Médical & Santé',
                            'color' => '#1e7e34',
                            'types' => ['Certificat_medical'],
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 4-4-8-2 4H2"/></svg>',
                        ],
                        'formation' => [
                            'label' => 'Formations & Diplômes',
                            'color' => '#0F6E56',
                            'types' => ['Diplome'],
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
                        ],
                    ];

                    // Groupe chaque doc dans sa catégorie
                    $docsByCategory = [];
                    $typesCouverts  = [];
                    foreach ($envCategories as $key => $cat) {
                        $docsByCategory[$key] = $docsFlat->filter(fn($d) => in_array($d->type_document, $cat['types']));
                        $typesCouverts = array_merge($typesCouverts, $cat['types']);
                    }
                    // Docs confidentiels
                    $docsConf = $docsFlat->filter(fn($d) => in_array($d->niveau_confidentialite, ['Confidentiel', 'Secret']));
                @endphp

                {{-- ── GRILLE D'ENVELOPPES (3 colonnes) ──────────────────── --}}
                <div class="row g-3">
                    @foreach($envCategories as $catKey => $cat)
                    @php $catDocs = $docsByCategory[$catKey]; @endphp
                    <div class="col-xl-4 col-md-6 env-cell">
                        <button class="env-card-btn"
                                style="--env-color:{{ $cat['color'] }};"
                                id="env-btn-{{ $catKey }}"
                                onclick="toggleEnv('{{ $catKey }}')"
                                type="button">
                            <div class="env-card-onglet"></div>
                            <div class="env-card-icon">{!! $cat['icon'] !!}</div>
                            <div class="env-card-label">{{ $cat['label'] }}</div>
                            <div class="env-card-footer">
                                <span class="env-card-count">
                                    {{ $catDocs->count() }} document{{ $catDocs->count() > 1 ? 's' : '' }}
                                </span>
                                <svg class="env-card-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </div>
                        </button>
                    </div>
                    @endforeach

                    @if($docsConf->isNotEmpty())
                    <div class="col-xl-4 col-md-6 env-cell">
                        <button class="env-card-btn"
                                style="--env-color:#d97706;"
                                id="env-btn-confidentiel"
                                onclick="toggleEnv('confidentiel')"
                                type="button">
                            <div class="env-card-onglet"></div>
                            <div class="env-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <div class="env-card-label">Confidentiel — Accès restreint</div>
                            <div class="env-card-footer">
                                <span class="env-card-count">{{ $docsConf->count() }} document{{ $docsConf->count() > 1 ? 's' : '' }}</span>
                                <svg class="env-card-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </div>
                        </button>
                    </div>
                    @endif
                </div>

                {{-- ── PANNEAUX DE DOCUMENTS (collapsibles) ──────────────── --}}
                @foreach($envCategories as $catKey => $cat)
                @php $catDocs = $docsByCategory[$catKey]; @endphp
                <div id="env-panel-{{ $catKey }}" class="env-docs-panel mt-3" style="--env-color:{{ $cat['color'] }};">
                    <div class="env-docs-header">
                        <span style="color:{{ $cat['color'] }};">{!! $cat['icon'] !!}</span>
                        <span>{{ $cat['label'] }}</span>
                        <button type="button" class="close-env" onclick="toggleEnv('{{ $catKey }}')" title="Fermer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    @if($catDocs->isEmpty())
                        <div class="env-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#E5E7EB" stroke-width="1.5" style="display:block;margin:0 auto 8px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Aucun document dans cette catégorie —
                            <a href="{{ route('rh.ged.documents.create', ['dossier' => $dossier->id_dossier]) }}" style="color:{{ $cat['color'] }};font-size:12px;">Ajouter</a>
                        </div>
                    @else
                        @foreach($catDocs as $doc)
                        @include('rh.documents._doc-row', ['doc' => $doc])
                        @endforeach
                    @endif
                </div>
                @endforeach

                {{-- Panneau Confidentiel --}}
                @if($docsConf->isNotEmpty())
                <div id="env-panel-confidentiel" class="env-docs-panel mt-3" style="--env-color:#d97706;">
                    <div class="env-docs-header">
                        <span style="color:#d97706;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <span>Confidentiel — Accès restreint</span>
                        <button type="button" class="close-env" onclick="toggleEnv('confidentiel')" title="Fermer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="lock-banner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Ces documents sont marqués <strong>Confidentiel</strong> ou <strong>Secret</strong>. Seuls les rôles autorisés peuvent y accéder.
                    </div>
                    @foreach($docsConf as $doc)
                    @include('rh.documents._doc-row', ['doc' => $doc])
                    @endforeach
                </div>
                @endif

            @endif
        </div>

        {{-- ── COL DROITE : Infos + Complétude + Audit ──────────────── --}}
        <div class="col-xl-4">

            {{-- Infos dossier --}}
            <div class="info-card">
                <div class="info-card-hd">Informations dossier</div>
                <dl class="mb-0" style="font-size:13px;">
                    <dt class="text-muted fw-normal">Référence</dt>
                    <dd class="fw-600 mb-2">{{ $dossier->reference }}</dd>

                    <dt class="text-muted fw-normal">Statut</dt>
                    <dd class="mb-2">
                        <span class="badge {{ $dossier->statut_badge }}">{{ $dossier->statut_da }}</span>
                    </dd>

                    <dt class="text-muted fw-normal">Étagère</dt>
                    <dd class="mb-2">{{ $dossier->etagere?->reference_complete ?? '—' }}</dd>

                    <dt class="text-muted fw-normal">Service</dt>
                    <dd class="mb-2">{{ $dossier->etagere?->service?->nom_service ?? '—' }}</dd>

                    <dt class="text-muted fw-normal">Créé le</dt>
                    <dd class="mb-0">{{ $dossier->date_creation?->format('d/m/Y à H:i') }}</dd>
                </dl>
            </div>

            {{-- Taux de complétude --}}
            <div class="info-card">
                <div class="info-card-hd">Complétude du dossier</div>
                @php
                    $typesPrincipaux = ['Contrat','Piece_identite','Diplome','Certificat_medical'];
                    $docsFlat2       = $documents->flatten();
                @endphp
                @foreach($typesPrincipaux as $tp)
                @php
                    $tpInfo  = \App\Models\Document::TYPES[$tp] ?? ['label'=>$tp];
                    $present = $docsFlat2->where('type_document', $tp)->where('statut_document','Actif')->count() > 0;
                @endphp
                <div class="d-flex align-items-center gap-2 mb-2" style="font-size:13px;">
                    @if($present)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#059669" stroke="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                    @endif
                    <span style="color:{{ $present ? '#111827' : '#9CA3AF' }};">{{ $tpInfo['label'] }}</span>
                    @if(!$present)
                        <a href="{{ route('rh.ged.documents.create', ['dossier'=>$dossier->id_dossier]) }}"
                           style="font-size:11px;color:#0A4D8C;margin-left:auto;">Ajouter</a>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Audit trail --}}
            <div class="info-card">
                <div class="info-card-hd">Historique récent</div>
                @forelse($activites as $act)
                <div class="audit-item">
                    <div class="audit-dot mt-1"></div>
                    <div class="audit-body">
                        <span class="av-user">{{ $act->causer?->name ?? $act->causer?->login ?? 'Système' }}</span>
                        <span class="text-muted"> — </span>
                        <span>{{ $act->description }}</span>
                        <div class="av-time mt-1">{{ $act->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted" style="font-size:13px;padding:12px 0;">Aucune activité enregistrée</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ══ MODAL VISUALISEUR ══════════════════════════════════════════ --}}
<div class="modal fade" id="viewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:90vw;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;height:90vh;">
            <div class="modal-header border-0" style="background:#111827;padding:14px 20px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary rounded-2 p-2" id="viewerIconWrap"></div>
                    <div>
                        <h5 class="modal-title mb-0 text-white" id="viewerModalLabel" style="font-size:15px;"></h5>
                        <div class="text-muted" id="viewerExt" style="font-size:12px;"></div>
                    </div>
                </div>
                <div class="d-flex gap-2 ms-auto">
                    <a href="#" id="viewerDownloadBtn" class="btn btn-sm btn-outline-light">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Télécharger
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0" style="background:#2D2D2D;height:calc(90vh - 70px);">
                <div id="viewerContent" style="height:100%;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ── Toggle enveloppe : ouvre le panneau doc correspondant ── */
function toggleEnv(key) {
    const btn   = document.getElementById('env-btn-' + key);
    const panel = document.getElementById('env-panel-' + key);
    if (!btn || !panel) return;

    const isOpen = panel.classList.contains('is-open');

    // Fermer toutes les enveloppes ouvertes
    document.querySelectorAll('.env-docs-panel.is-open').forEach(p => p.classList.remove('is-open'));
    document.querySelectorAll('.env-card-btn.is-open').forEach(b => b.classList.remove('is-open'));

    // Ouvrir celle-ci si elle était fermée
    if (!isOpen) {
        panel.classList.add('is-open');
        btn.classList.add('is-open');
        // Scroll doux vers le panneau
        setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
    }
}

/* ── Visualiseur document ── */
function openViewer(docId, titre, ext, previewUrl) {
    document.getElementById('viewerModalLabel').textContent = titre;
    document.getElementById('viewerExt').textContent = ext.toUpperCase();
    const content = document.getElementById('viewerContent');
    content.innerHTML = '';
    const imgExts = ['jpg','jpeg','png','gif','webp'];
    if (ext.toLowerCase() === 'pdf') {
        content.innerHTML = `<iframe src="${previewUrl}" style="width:100%;height:100%;border:none;" title="${titre}"></iframe>`;
    } else if (imgExts.includes(ext.toLowerCase())) {
        content.innerHTML = `<div style="height:100%;display:flex;align-items:center;justify-content:center;padding:20px;"><img src="${previewUrl}" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,.5);" alt="${titre}"></div>`;
    } else {
        content.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:#9CA3AF;gap:16px;"><svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#4B5563" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><p style="font-size:15px;">Prévisualisation non disponible (${ext.toUpperCase()})</p><a href="${previewUrl}" target="_blank" class="btn btn-outline-light">Ouvrir dans un nouvel onglet</a></div>`;
    }
    document.getElementById('viewerDownloadBtn').href = previewUrl.replace('/preview', '/download');
    new bootstrap.Modal(document.getElementById('viewerModal')).show();
}
</script>
@endpush
