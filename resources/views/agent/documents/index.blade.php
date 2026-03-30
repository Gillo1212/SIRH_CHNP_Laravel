@extends('layouts.master')
@section('title', 'Mes Documents')
@section('page-title', 'Mes Documents')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mes documents</li>
@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   VUE AGENT — DOSSIER PERSONNEL
   ══════════════════════════════════════════════════════════════ */

/* HERO DOSSIER */
.dossier-hero {
    background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 60%,#1976D2 100%);
    border-radius:20px;padding:28px 32px;color:#fff;margin-bottom:28px;
    position:relative;overflow:hidden;box-shadow:0 12px 36px rgba(10,77,140,.30);
}
.dossier-hero::before { content:'';position:absolute;top:-50px;right:-50px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.07); }
.dossier-hero::after  { content:'';position:absolute;bottom:-60px;right:20%;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05); }
.dh-stat { text-align:center; }
.dh-stat .val { font-size:28px;font-weight:700;line-height:1; }
.dh-stat .lbl { font-size:12px;opacity:.8;margin-top:3px; }
.dh-divider { width:1px;background:rgba(255,255,255,.2);align-self:stretch;margin:0 8px; }

/* EMPTY STATE */
.empty-dossier { background:#fff;border:1px dashed #D1D5DB;border-radius:16px;padding:60px;text-align:center; }

/* TYPE SECTION */
.type-section { margin-bottom:28px; }
.type-header { display:flex;align-items:center;gap:10px;margin-bottom:12px;padding:10px 16px;background:#fff;border-radius:10px;border:1px solid #E5E7EB; }
.type-icon { width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0; }
.type-label { font-size:13px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em; }
.type-count { font-size:12px;background:#F3F4F6;color:#6B7280;padding:2px 8px;border-radius:20px;font-weight:600;margin-left:auto; }

/* DOCUMENT CARD */
.doc-card {
    background:#fff;border:1px solid #E5E7EB;border-radius:14px;
    padding:16px 20px;display:flex;align-items:center;gap:14px;
    transition:all 200ms;cursor:pointer;text-decoration:none;
}
.doc-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);border-color:#BFDBFE;transform:translateY(-1px); }
.doc-card .dc-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0; }
.doc-card .dc-body { flex:1;min-width:0; }
.doc-card .dc-title { font-size:14px;font-weight:600;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.doc-card .dc-meta { font-size:12px;color:#9CA3AF;margin-top:3px; }
.doc-card .dc-actions { display:flex;gap:6px;flex-shrink:0; }

.badge-conf { font-size:10px;padding:3px 8px;border-radius:20px;font-weight:600; }
.badge-conf-public  { background:#ECFDF5;color:#059669; }
.badge-conf-interne { background:#EFF6FF;color:#1D4ED8; }

/* FORMAT PILL */
.format-pill { font-size:10px;padding:2px 7px;border-radius:4px;font-weight:700;background:#F3F4F6;color:#374151; }

/* ACTION BTN */
.action-btn { width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;cursor:pointer;transition:all 150ms;border:none; }
.btn-view     { background:#EFF6FF;color:#1D4ED8; }
.btn-view:hover { background:#DBEAFE; }
.btn-dl   { background:#ECFDF5;color:#059669; }
.btn-dl:hover { background:#D1FAE5; }

/* SIDEBAR INFO */
.info-card { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:20px; }
.info-card .section-hd { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6B7280;margin-bottom:14px; }
.info-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #F3F4F6;font-size:13px; }
.info-row:last-child { border-bottom:none; }
.info-row .il { color:#6B7280; }
.info-row .iv { font-weight:600;color:#111827; }

/* COMPLÉTUDE */
.completude-item { display:flex;align-items:center;gap:8px;padding:6px 0;font-size:13px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    @if(!$dossier)
        {{-- Dossier non encore créé --}}
        <div class="empty-dossier">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#0A4D8C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="d-block mb-3 mx-auto" style="opacity:.2;">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/>
            </svg>
            <h5 class="fw-700 mb-2" style="color:#374151;">Votre dossier documentaire n'est pas encore créé</h5>
            <p class="text-muted mb-0" style="font-size:14px;max-width:400px;margin:0 auto;">
                Vos documents apparaîtront ici une fois que l'équipe RH aura initialisé votre dossier.
                Contactez votre service RH pour plus d'informations.
            </p>
        </div>
    @else

        {{-- ── HERO DOSSIER ──────────────────────────────────── --}}
        <div class="dossier-hero">
            <div class="d-flex align-items-center gap-4 flex-wrap" style="position:relative;z-index:1;">
                <div>
                    <div style="font-size:13px;opacity:.75;margin-bottom:4px;display:flex;align-items:center;gap:5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Mon dossier personnel
                    </div>
                    <div class="fw-700" style="font-size:20px;">{{ $agent->nom_complet }}</div>
                    <div style="font-size:13px;opacity:.8;margin-top:4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>{{ $agent->matricule }}
                        &nbsp;·&nbsp;
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>{{ $agent->service?->nom_service }}
                    </div>
                    <div style="font-size:12px;opacity:.7;margin-top:3px;display:flex;align-items:center;gap:5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>Réf. {{ $dossier->reference }}
                    </div>
                </div>
                <div class="ms-auto d-flex gap-3 flex-wrap">
                    <div class="dh-stat">
                        <div class="val">{{ $documents->count() }}</div>
                        <div class="lbl">Documents</div>
                    </div>
                    <div class="dh-divider"></div>
                    <div class="dh-stat">
                        <div class="val">{{ $parType->count() }}</div>
                        <div class="lbl">Types</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- ── COL PRINCIPALE : Documents ─────────────────── --}}
            <div class="col-xl-8">

                @if($documents->isEmpty())
                    <div class="empty-dossier">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#0A4D8C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="d-block mb-3 mx-auto" style="opacity:.2;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <h6 class="fw-600 mb-2">Votre dossier est vide</h6>
                        <p class="text-muted mb-0" style="font-size:13px;">
                            Aucun document n'a encore été archivé par le service RH.
                        </p>
                    </div>
                @else
                    @foreach($parType as $type => $docs)
                    @php $typeInfo = \App\Models\Document::TYPES[$type] ?? ['label'=>$type,'icon'=>'file','color'=>'#6B7280']; @endphp
                    <div class="type-section">
                        <div class="type-header">
                            <div class="type-icon" style="background:{{ $typeInfo['color'] }}18;">
                                {{-- Icône générique dossier --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="{{ $typeInfo['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <span class="type-label">{{ $typeInfo['label'] }}</span>
                            <span class="type-count">{{ $docs->count() }}</span>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @foreach($docs as $doc)
                            <div class="doc-card">
                                {{-- Icône format fichier --}}
                                <div class="dc-icon" style="background:{{ $typeInfo['color'] }}15;">
                                    @if($doc->est_pdf)
                                        {{-- PDF --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                                    @elseif($doc->est_image)
                                        {{-- Image --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0891B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    @else
                                        {{-- Autre fichier --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $typeInfo['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    @endif
                                </div>

                                {{-- Infos --}}
                                <div class="dc-body">
                                    <div class="dc-title">{{ $doc->titre }}</div>
                                    <div class="dc-meta d-flex align-items-center gap-2 flex-wrap">
                                        <span class="format-pill">{{ strtoupper($doc->extension) }}</span>
                                        @if($doc->date_creation)
                                            <span>{{ $doc->date_creation->format('d/m/Y') }}</span>
                                        @endif
                                        @if($doc->version !== '1.0')
                                            <span>v{{ $doc->version }}</span>
                                        @endif
                                        @if($doc->taille_fichier)
                                            <span>{{ $doc->taille_humain }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Badge confidentialité --}}
                                <span class="badge-conf badge-conf-{{ strtolower($doc->niveau_confidentialite) }}">
                                    {{ $doc->niveau_confidentialite }}
                                </span>

                                {{-- Actions --}}
                                <div class="dc-actions">
                                    <a href="{{ route('agent.documents.show', $doc->id_document) }}"
                                       class="action-btn btn-view" title="Visualiser" onclick="event.stopPropagation()">
                                        {{-- Œil — visualiser --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('agent.documents.download', $doc->id_document) }}"
                                       class="action-btn btn-dl" title="Télécharger" onclick="event.stopPropagation()">
                                        {{-- Flèche téléchargement --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="7 10 12 15 17 10"/>
                                            <line x1="12" y1="15" x2="12" y2="3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- ── COL DROITE : Infos dossier ──────────────────── --}}
            <div class="col-xl-4">

                {{-- Informations dossier --}}
                <div class="info-card mb-3">
                    <div class="section-hd" style="display:flex;align-items:center;gap:5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                        Votre dossier
                    </div>
                    <div class="info-row">
                        <span class="il">Référence</span>
                        <span class="iv">{{ $dossier->reference }}</span>
                    </div>
                    <div class="info-row">
                        <span class="il">Statut</span>
                        <span class="iv">
                            <span class="badge {{ $dossier->statut_badge }}" style="font-size:11px;">{{ $dossier->statut_da }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="il">Étagère</span>
                        <span class="iv">{{ $dossier->etagere?->nom_etagere ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="il">Documents</span>
                        <span class="iv">{{ $documents->count() }} fichier(s)</span>
                    </div>
                    <div class="info-row">
                        <span class="il">Créé le</span>
                        <span class="iv">{{ $dossier->date_creation?->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Complétude --}}
                @php
                    $typesPrincipaux = [
                        'Contrat'       => 'Contrat de travail',
                        'Piece_identite'=> "Pièce d'identité",
                        'Diplome'       => 'Diplôme',
                        'Attestation'   => 'Attestation',
                    ];
                @endphp
                <div class="info-card mb-3">
                    <div class="section-hd" style="display:flex;align-items:center;gap:5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Complétude
                    </div>
                    @foreach($typesPrincipaux as $tp => $tpLabel)
                    @php $present = $documents->where('type_document', $tp)->isNotEmpty(); @endphp
                    <div class="completude-item">
                        @if($present)
                            {{-- Coché --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                        @else
                            {{-- Vide --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/></svg>
                        @endif
                        <span style="color:{{ $present ? '#111827' : '#9CA3AF' }};font-size:13px;">{{ $tpLabel }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Aide --}}
                <div class="info-card" style="background:#F0F9FF;border-color:#BAE6FD;">
                    <div class="section-hd" style="color:#0369A1;display:flex;align-items:center;gap:5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0369A1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        À savoir
                    </div>
                    <ul class="mb-0 ps-3" style="font-size:12.5px;color:#0C4A6E;line-height:1.8;">
                        <li>Vous ne pouvez consulter que vos documents <strong>Publics</strong> et <strong>Internes</strong></li>
                        <li>Les documents <strong>Confidentiels</strong> et <strong>Secrets</strong> sont gérés par le service RH</li>
                        <li>Pour ajouter un document, contactez votre service RH</li>
                    </ul>
                </div>

            </div>
        </div>

    @endif
</div>
@endsection
