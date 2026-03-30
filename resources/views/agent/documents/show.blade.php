@extends('layouts.master')
@section('title', 'Document — ' . $document->titre)
@section('page-title', $document->titre)

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.documents.index') }}" style="color:#1565C0;">Mes documents</a></li>
    <li>{{ $document->reference }}</li>
@endsection

@push('styles')
<style>
.btn-action { display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:8px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.btn-primary-ged { background:#0A4D8C;color:#fff; }
.btn-primary-ged:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.btn-outline-ged { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.btn-outline-ged:hover { background:#F9FAFB; }

/* VIEWER */
.viewer-wrap { background:#2D2D2D;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.25); }
.viewer-toolbar { background:#1F1F1F;padding:14px 20px;display:flex;align-items:center;gap:14px;border-bottom:1px solid rgba(255,255,255,.08); }
.viewer-body { min-height:500px;position:relative; }
.viewer-body iframe { width:100%;min-height:560px;border:none;display:block; }
.viewer-body img { max-width:100%;display:block;margin:0 auto;padding:24px;border-radius:4px; }
.no-preview { display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:300px;color:#9CA3AF;gap:12px; }

/* METADATA */
.meta-panel { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:22px; }
.mp-row { display:flex;justify-content:space-between;align-items:flex-start;padding:8px 0;border-bottom:1px solid #F3F4F6;font-size:13px;gap:8px; }
.mp-row:last-child { border-bottom:none; }
.mp-row .ml { color:#6B7280;min-width:120px;flex-shrink:0; }
.mp-row .mv { font-weight:600;color:#111827;text-align:right; }

.badge-conf { font-size:11px;padding:4px 10px;border-radius:20px;font-weight:600;display:inline-flex;align-items:center;gap:5px; }
.badge-conf-public  { background:#ECFDF5;color:#059669; }
.badge-conf-interne { background:#EFF6FF;color:#1D4ED8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- TOOLBAR --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('agent.documents.download', $document->id_document) }}" class="btn-action btn-primary-ged">
            <i class="ri-download-2-line"></i> Télécharger
        </a>
        <a href="{{ route('agent.documents.index') }}" class="btn-action btn-outline-ged">
            <i class="ri-arrow-left-line"></i> Retour
        </a>
    </div>

    <div class="row g-4">

        {{-- ── VISUALISEUR ─────────────────────────────────────── --}}
        <div class="col-xl-8">
            <div class="viewer-wrap">
                {{-- Toolbar viewer --}}
                <div class="viewer-toolbar">
                    @php $typeInfo = $document->type_info; @endphp
                    <div class="rounded-2 p-2" style="background:{{ $typeInfo['color'] }}30;">
                        <i class="{{ $typeInfo['icon'] }}" style="color:{{ $typeInfo['color'] }};font-size:18px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-600 text-white" style="font-size:14px;">{{ $document->titre }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">
                            {{ $document->reference }} · v{{ $document->version }}
                            @if($document->date_creation) · {{ $document->date_creation->format('d/m/Y') }} @endif
                        </div>
                    </div>
                    {{-- Bouton plein écran --}}
                    @if($document->est_pdf || $document->est_image)
                    <a href="{{ route('agent.documents.preview', $document->id_document) }}" target="_blank"
                       class="btn btn-sm" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);">
                        <i class="ri-fullscreen-line me-1"></i> Plein écran
                    </a>
                    @endif
                </div>

                {{-- Corps visualiseur --}}
                <div class="viewer-body">
                    @if($document->est_pdf)
                        <iframe src="{{ route('agent.documents.preview', $document->id_document) }}"
                                title="{{ $document->titre }}"
                                style="width:100%;min-height:560px;border:none;"></iframe>
                    @elseif($document->est_image)
                        <div style="background:#1A1A1A;min-height:400px;display:flex;align-items:center;justify-content:center;padding:20px;">
                            <img src="{{ route('agent.documents.preview', $document->id_document) }}"
                                 alt="{{ $document->titre }}"
                                 style="max-width:100%;max-height:600px;object-fit:contain;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,.5);">
                        </div>
                    @else
                        <div class="no-preview">
                            <i class="ri-file-text-line" style="font-size:60px;opacity:.25;"></i>
                            <p style="font-size:15px;">Prévisualisation indisponible pour ce format</p>
                            <p style="font-size:13px;color:#6B7280;">Format : <strong>{{ strtoupper($document->extension) }}</strong></p>
                            <a href="{{ route('agent.documents.download', $document->id_document) }}"
                               class="btn-action btn-primary-ged mt-2">
                                <i class="ri-download-2-line"></i> Télécharger pour consulter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── MÉTADONNÉES ──────────────────────────────────────── --}}
        <div class="col-xl-4">

            {{-- Infos document --}}
            <div class="meta-panel mb-3">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6B7280;margin-bottom:14px;">
                    <i class="ri-information-line me-1"></i> Informations
                </div>

                <div class="mp-row">
                    <span class="ml">Référence</span>
                    <span class="mv">{{ $document->reference }}</span>
                </div>
                <div class="mp-row">
                    <span class="ml">Type</span>
                    <span class="mv">
                        <i class="{{ $typeInfo['icon'] }}" style="color:{{ $typeInfo['color'] }};"></i>
                        {{ $typeInfo['label'] }}
                    </span>
                </div>
                <div class="mp-row">
                    <span class="ml">Version</span>
                    <span class="mv">v{{ $document->version }}</span>
                </div>
                <div class="mp-row">
                    <span class="ml">Date</span>
                    <span class="mv">{{ $document->date_creation?->format('d/m/Y') ?? '—' }}</span>
                </div>
                @if($document->taille_fichier)
                <div class="mp-row">
                    <span class="ml">Taille</span>
                    <span class="mv">{{ $document->taille_humain }}</span>
                </div>
                @endif
                <div class="mp-row">
                    <span class="ml">Format</span>
                    <span class="mv">{{ strtoupper($document->extension) }}</span>
                </div>
                <div class="mp-row">
                    <span class="ml">Accès</span>
                    <span class="mv">
                        @php $ci = $document->confidentialite_info; @endphp
                        <span class="badge-conf badge-conf-{{ strtolower($document->niveau_confidentialite) }}">
                            <i class="{{ $ci['icon'] }}"></i> {{ $ci['label'] }}
                        </span>
                    </span>
                </div>
                @if($document->mots_cles)
                <div class="mp-row">
                    <span class="ml">Mots-clés</span>
                    <span class="mv">
                        <div class="d-flex flex-wrap gap-1 justify-content-end">
                            @foreach(explode(',', $document->mots_cles) as $mot)
                                <span class="badge bg-light text-secondary" style="font-size:10px;">{{ trim($mot) }}</span>
                            @endforeach
                        </div>
                    </span>
                </div>
                @endif
                @if($document->description)
                <div class="mp-row flex-column gap-1">
                    <span class="ml" style="text-align:left;min-width:auto;">Note</span>
                    <span style="font-size:13px;color:#374151;font-weight:400;">{{ $document->description }}</span>
                </div>
                @endif
            </div>

            {{-- Note sécurité --}}
            <div class="meta-panel" style="background:#F0F9FF;border-color:#BAE6FD;">
                <div style="font-size:12px;color:#0369A1;line-height:1.7;">
                    <i class="ri-shield-check-line me-1" style="font-size:15px;"></i>
                    <strong>Document protégé</strong> — Ce document est stocké de façon sécurisée dans votre dossier GED.
                    Toute consultation est tracée conformément à la politique CID du CHNP.
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
