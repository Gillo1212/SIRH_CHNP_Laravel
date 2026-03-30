@extends('layouts.master')
@section('title', 'Document — ' . $document->titre)
@section('page-title', 'Document')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.ged.index') }}" style="color:#1565C0;">GED</a></li>
    <li><a href="{{ route('rh.ged.dossier.show', $document->dossier->id_dossier) }}" style="color:#1565C0;">{{ $document->dossier->reference }}</a></li>
    <li>{{ $document->reference }}</li>
@endsection

@push('styles')
<style>
.ged-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.ged-btn-primary { background:#0A4D8C;color:#fff; }
.ged-btn-primary:hover { background:#1565C0;color:#fff; }
.ged-btn-outline { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.ged-btn-outline:hover { background:#F9FAFB; }
.ged-btn-danger  { background:#FEF2F2;color:#DC2626;border:1px solid #FECACA; }
.ged-btn-danger:hover { background:#DC2626;color:#fff; }
.ged-btn-amber   { background:#FFFBEB;color:#D97706;border:1px solid #FDE68A; }
.ged-btn-amber:hover { background:#D97706;color:#fff; }

/* VIEWER */
.doc-viewer-container {
    background:#2D2D2D;border-radius:16px;overflow:hidden;
    min-height:500px;display:flex;flex-direction:column;
}
.doc-viewer-toolbar {
    background:#1F1F1F;padding:12px 20px;display:flex;align-items:center;gap:12px;
    border-bottom:1px solid rgba(255,255,255,.1);
}
.doc-viewer-body { flex:1;overflow:auto; }
.doc-viewer-body iframe { width:100%;min-height:600px;border:none; }
.doc-viewer-body img { max-width:100%;display:block;margin:0 auto;padding:20px; }

/* METADATA */
.meta-card { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:24px;margin-bottom:16px; }
.meta-item { padding:8px 0;border-bottom:1px solid #F3F4F6;display:flex;gap:10px;align-items:flex-start;font-size:13px; }
.meta-item:last-child { border-bottom:none; }
.meta-item .mi-label { min-width:140px;color:#6B7280;flex-shrink:0;font-weight:500; }
.meta-item .mi-val   { color:#111827;font-weight:600; }

/* BADGE CONFIDENTIALITE */
.badge-conf { font-size:11px;padding:4px 10px;border-radius:20px;font-weight:600;display:inline-flex;align-items:center;gap:5px; }
.badge-conf-public       { background:#ECFDF5;color:#059669; }
.badge-conf-interne      { background:#EFF6FF;color:#1D4ED8; }
.badge-conf-confidentiel { background:#FFFBEB;color:#D97706; }
.badge-conf-secret       { background:#FEF2F2;color:#DC2626; }

/* TIMELINE */
.timeline-item { display:flex;gap:14px;padding:10px 0; }
.timeline-dot  { width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:4px; }
.timeline-dot.blue   { background:#0A4D8C; }
.timeline-dot.green  { background:#059669; }
.timeline-dot.amber  { background:#D97706; }
.timeline-dot.red    { background:#DC2626; }
.timeline-line { border-left:2px solid #F3F4F6;margin-left:4px;padding-left:18px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 mb-3" style="border-radius:10px;background:#ECFDF5;color:#065F46;">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ACTIONS BAR --}}
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <a href="{{ route('rh.ged.documents.download', $document->id_document) }}" class="ged-btn ged-btn-primary">
            <i class="ri-download-2-line"></i> Télécharger
        </a>

        @if($document->statut_document === 'Actif')
        <form method="POST" action="{{ route('rh.ged.documents.archiver', $document->id_document) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="ged-btn ged-btn-amber" onclick="return confirm('Archiver ce document ?')">
                <i class="ri-archive-line"></i> Archiver
            </button>
        </form>
        @elseif($document->statut_document === 'Archivé')
        <form method="POST" action="{{ route('rh.ged.documents.restaurer', $document->id_document) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="ged-btn ged-btn-outline" style="color:#059669;border-color:#D1FAE5;">
                <i class="ri-refresh-line"></i> Restaurer
            </button>
        </form>
        @endif

        @if($document->statut_document !== 'Détruit')
        <form method="POST" action="{{ route('rh.ged.documents.detruire', $document->id_document) }}" class="d-inline ms-auto">
            @csrf @method('PATCH')
            <button type="submit" class="ged-btn ged-btn-danger"
                onclick="return confirm('Marquer ce document comme détruit ? Cette action est irréversible.')">
                <i class="ri-delete-bin-line"></i> Détruire
            </button>
        </form>
        @endif

        <a href="{{ route('rh.ged.dossier.show', $document->dossier->id_dossier) }}" class="ged-btn ged-btn-outline">
            <i class="ri-arrow-left-line"></i> Retour au dossier
        </a>
    </div>

    <div class="row g-4">

        {{-- ── VISUALISEUR ─────────────────────────────────────── --}}
        <div class="col-xl-8">
            <div class="doc-viewer-container">
                <div class="doc-viewer-toolbar">
                    @php $typeInfo = $document->type_info; @endphp
                    <div class="rounded-2 p-2" style="background:{{ $typeInfo['color'] }}30;">
                        <i class="{{ $typeInfo['icon'] }}" style="color:{{ $typeInfo['color'] }};font-size:18px;"></i>
                    </div>
                    <div>
                        <div class="fw-600 text-white" style="font-size:14px;">{{ $document->titre }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $document->reference }} · v{{ $document->version }}</div>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        @php $ci = $document->confidentialite_info; @endphp
                        <span class="badge-conf badge-conf-{{ strtolower($document->niveau_confidentialite) }}">
                            <i class="{{ $ci['icon'] }}"></i>{{ $ci['label'] }}
                        </span>
                        @if($document->statut_document === 'Actif')
                            <span class="badge bg-success" style="font-size:11px;">Actif</span>
                        @elseif($document->statut_document === 'Archivé')
                            <span class="badge bg-secondary" style="font-size:11px;">Archivé</span>
                        @else
                            <span class="badge bg-danger" style="font-size:11px;">Détruit</span>
                        @endif
                    </div>
                </div>
                <div class="doc-viewer-body">
                    @if($document->statut_document === 'Détruit')
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                            <i class="ri-delete-bin-line" style="font-size:60px;opacity:.3;"></i>
                            <p class="mt-3 fw-500">Document détruit — prévisualisation indisponible</p>
                        </div>
                    @elseif($document->est_pdf)
                        <iframe src="{{ route('rh.ged.documents.preview', $document->id_document) }}"
                                title="{{ $document->titre }}" style="width:100%;min-height:600px;border:none;">
                        </iframe>
                    @elseif($document->est_image)
                        <div style="padding:20px;display:flex;justify-content:center;align-items:center;min-height:400px;">
                            <img src="{{ route('rh.ged.documents.preview', $document->id_document) }}"
                                 alt="{{ $document->titre }}"
                                 style="max-width:100%;max-height:600px;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,.4);">
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                            <i class="ri-file-text-line" style="font-size:60px;opacity:.3;color:#9CA3AF;"></i>
                            <p class="mt-3 fw-500" style="color:#9CA3AF;">Prévisualisation non disponible pour ce format</p>
                            <p style="font-size:13px;color:#6B7280;">Format : {{ strtoupper($document->extension) }}</p>
                            <a href="{{ route('rh.ged.documents.download', $document->id_document) }}"
                               class="ged-btn ged-btn-primary mt-2">
                                <i class="ri-download-2-line"></i> Télécharger pour ouvrir
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── MÉTADONNÉES + HISTORIQUE ─────────────────────────── --}}
        <div class="col-xl-4">

            {{-- Métadonnées --}}
            <div class="meta-card">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6B7280;margin-bottom:14px;">
                    <i class="ri-information-line me-1"></i> Métadonnées
                </div>

                <div class="meta-item">
                    <span class="mi-label">Référence</span>
                    <span class="mi-val">{{ $document->reference }}</span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Type</span>
                    <span class="mi-val">
                        <i class="{{ $typeInfo['icon'] }}" style="color:{{ $typeInfo['color'] }};"></i>
                        {{ $typeInfo['label'] }}
                    </span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Confidentialité</span>
                    <span class="mi-val">
                        <span class="badge-conf badge-conf-{{ strtolower($document->niveau_confidentialite) }}">
                            <i class="{{ $ci['icon'] }}"></i>{{ $ci['label'] }}
                        </span>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Statut</span>
                    <span class="mi-val">
                        @php
                            $si = \App\Models\Document::STATUTS[$document->statut_document] ?? [];
                        @endphp
                        <span class="badge {{ $si['badge'] ?? 'bg-secondary' }}" style="font-size:11px;">
                            {{ $document->statut_document }}
                        </span>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Date document</span>
                    <span class="mi-val">{{ $document->date_creation?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Archivé le</span>
                    <span class="mi-val">{{ $document->date_archivage?->format('d/m/Y à H:i') ?? '—' }}</span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Version</span>
                    <span class="mi-val">v{{ $document->version }}</span>
                </div>
                @if($document->format_fichier)
                <div class="meta-item">
                    <span class="mi-label">Format</span>
                    <span class="mi-val">{{ strtoupper($document->format_fichier ?: $document->extension) }}</span>
                </div>
                @endif
                @if($document->taille_fichier)
                <div class="meta-item">
                    <span class="mi-label">Taille</span>
                    <span class="mi-val">{{ $document->taille_humain }}</span>
                </div>
                @endif
                <div class="meta-item">
                    <span class="mi-label">Déposé par</span>
                    <span class="mi-val">{{ $document->uploadePar?->name ?? $document->uploadePar?->login ?? '—' }}</span>
                </div>
                <div class="meta-item">
                    <span class="mi-label">Agent</span>
                    <span class="mi-val">
                        @if($document->dossier?->agent)
                            <a href="{{ route('rh.ged.dossier.show', $document->dossier->id_dossier) }}" class="text-decoration-none">
                                {{ $document->dossier->agent->nom_complet }}
                            </a>
                        @else —
                        @endif
                    </span>
                </div>
                @if($document->mots_cles)
                <div class="meta-item">
                    <span class="mi-label">Mots-clés</span>
                    <span class="mi-val">
                        @foreach(explode(',', $document->mots_cles) as $mot)
                            <span class="badge bg-light text-secondary me-1" style="font-size:11px;">{{ trim($mot) }}</span>
                        @endforeach
                    </span>
                </div>
                @endif
                @if($document->description)
                <div class="meta-item">
                    <span class="mi-label">Description</span>
                    <span class="mi-val" style="font-weight:400;color:#374151;">{{ $document->description }}</span>
                </div>
                @endif
            </div>

            {{-- Historique / Audit trail --}}
            <div class="meta-card">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6B7280;margin-bottom:14px;">
                    <i class="ri-history-line me-1"></i> Historique (Audit CID)
                </div>
                <div class="timeline-line" style="border-color:#E5E7EB;">
                    @forelse($historique as $act)
                    <div class="timeline-item" style="margin-left:-22px;">
                        <div class="timeline-dot {{ match(true) {
                            str_contains($act->description ?? '', 'uploadé') => 'blue',
                            str_contains($act->description ?? '', 'archivé') => 'amber',
                            str_contains($act->description ?? '', 'restauré') => 'green',
                            str_contains($act->description ?? '', 'détruit') => 'red',
                            default => 'blue'
                        } }}"></div>
                        <div style="font-size:12.5px;flex:1;">
                            <span class="fw-600 text-dark">{{ $act->causer?->name ?? $act->causer?->login ?? 'Système' }}</span>
                            <span class="text-muted"> — {{ $act->description }}</span>
                            <div class="text-muted" style="font-size:11px;margin-top:2px;">
                                {{ $act->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted" style="font-size:13px;margin-left:-18px;">
                        <i class="ri-history-line d-block mb-1" style="font-size:20px;opacity:.3;"></i>
                        Aucune activité
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
