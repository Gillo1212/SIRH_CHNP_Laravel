@extends('layouts.master')
@section('title', 'GED — Recherche')
@section('page-title', 'Recherche de documents')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.ged.index') }}" style="color:#1565C0;">GED</a></li>
    <li>Recherche</li>
@endsection

@push('styles')
<style>
.ged-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.ged-btn-primary { background:#0A4D8C;color:#fff; }
.ged-btn-primary:hover { background:#1565C0;color:#fff; }
.ged-btn-outline { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.ged-btn-outline:hover { background:#F9FAFB; }

.search-hero { background:linear-gradient(135deg,#0A4D8C,#1565C0);border-radius:16px;padding:28px 32px;color:#fff;margin-bottom:24px; }
.search-input-lg { height:52px;border-radius:12px;border:none;padding:0 20px;font-size:16px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,.15);outline:none; }
.filter-bar { background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:16px 20px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:20px; }
.filter-select { height:36px;border:1px solid #E5E7EB;border-radius:7px;padding:0 12px;font-size:13px;outline:none;background:#fff; }
.filter-select:focus { border-color:#0A4D8C; }

.doc-row { transition:background 150ms;cursor:pointer; }
.doc-row:hover { background:#F9FAFB !important; }
.doc-icon-cell { width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.badge-conf { font-size:10px;padding:3px 8px;border-radius:20px;font-weight:600; }
.badge-conf-public       { background:#ECFDF5;color:#059669; }
.badge-conf-interne      { background:#EFF6FF;color:#1D4ED8; }
.badge-conf-confidentiel { background:#FFFBEB;color:#D97706; }
.badge-conf-secret       { background:#FEF2F2;color:#DC2626; }

.highlight { background:#FEF9C3;border-radius:3px;padding:1px 2px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Hero recherche --}}
    <div class="search-hero">
        <div class="mb-3" style="font-size:14px;opacity:.85;">
            <i class="ri-search-line me-1"></i> Recherche dans tous les documents de la GED
        </div>
        <form method="GET" action="{{ route('rh.ged.search') }}">
            <div class="d-flex gap-2">
                <input type="text" name="q" class="search-input-lg" placeholder="Titre, référence, mots-clés…" value="{{ $terme }}">
                <button type="submit" class="ged-btn ged-btn-primary" style="padding:0 24px;font-size:15px;border-radius:12px;">
                    <i class="ri-search-line"></i> Chercher
                </button>
            </div>
        </form>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.ged.search') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($terme) <input type="hidden" name="q" value="{{ $terme }}"> @endif
                <select name="type" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les types</option>
                    @foreach($types as $key => $ti)
                        <option value="{{ $key }}" {{ $type === $key ? 'selected' : '' }}>{{ $ti['label'] }}</option>
                    @endforeach
                </select>
                <select name="niveau" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Toutes confidentialités</option>
                    @foreach($niveaux as $key => $ni)
                        <option value="{{ $key }}" {{ $niveau === $key ? 'selected' : '' }}>{{ $ni['label'] }}</option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:130px;">
                    <option value="Actif" {{ $statut === 'Actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="Archivé" {{ $statut === 'Archivé' ? 'selected' : '' }}>Archivés</option>
                    <option value="tous" {{ $statut === 'tous' ? 'selected' : '' }}>Tous</option>
                </select>
                <select name="service" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ $service == $svc->id_service ? 'selected' : '' }}>{{ $svc->nom_service }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['type', 'niveau', 'service']) || (isset($statut) && $statut !== 'Actif'))
                    <a href="{{ route('rh.ged.search') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Résultats --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div style="font-size:14px;color:#374151;">
            @if($terme)
                Résultats pour « <strong>{{ $terme }}</strong> » —
            @endif
            <strong>{{ $documents->total() }}</strong> document(s) trouvé(s)
        </div>
        <a href="{{ route('rh.ged.documents.create') }}" class="ged-btn ged-btn-primary" style="padding:7px 14px;font-size:13px;">
            <i class="ri-upload-2-line"></i> Déposer
        </a>
    </div>

    @if($documents->isEmpty())
        <div class="text-center py-5 text-muted" style="background:#fff;border:1px dashed #E5E7EB;border-radius:14px;">
            <i class="ri-search-line d-block mb-3" style="font-size:48px;opacity:.3;"></i>
            <p class="fw-500">Aucun document ne correspond à votre recherche</p>
            @if($terme)
                <p style="font-size:13px;">Essayez d'autres mots-clés ou supprimez les filtres</p>
            @endif
        </div>
    @else
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 ps-4" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Document</th>
                            <th class="border-0 py-3" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Agent</th>
                            <th class="border-0 py-3" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Type</th>
                            <th class="border-0 py-3" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Confidentialité</th>
                            <th class="border-0 py-3" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Statut</th>
                            <th class="border-0 py-3" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Date</th>
                            <th class="border-0 py-3 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        @php
                            $ti = $doc->type_info;
                            $ci = $doc->confidentialite_info;
                        @endphp
                        <tr class="doc-row border-top" onclick="window.location='{{ route('rh.ged.documents.show', $doc->id_document) }}'">
                            <td class="py-2 ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="doc-icon-cell" style="background:{{ $ti['color'] }}18;">
                                        @if($doc->est_pdf)
                                            <i class="ri-file-pdf-2-line" style="color:#DC2626;"></i>
                                        @elseif($doc->est_image)
                                            <i class="ri-image-line" style="color:#0891B2;"></i>
                                        @else
                                            <i class="{{ $ti['icon'] }}" style="color:{{ $ti['color'] }};"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-500" style="color:#111827;">
                                            @if($terme)
                                                {!! str_ireplace($terme, '<mark class="highlight">'.e($terme).'</mark>', e($doc->titre)) !!}
                                            @else
                                                {{ $doc->titre }}
                                            @endif
                                        </div>
                                        <div style="font-size:11px;color:#9CA3AF;">{{ $doc->reference }} · v{{ $doc->version }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2" style="color:#374151;">
                                {{ $doc->dossier?->agent?->nom_complet ?? '—' }}
                                <div style="font-size:11px;color:#9CA3AF;">{{ $doc->dossier?->agent?->service?->nom_service }}</div>
                            </td>
                            <td class="py-2">
                                <span class="badge rounded-pill" style="background:{{ $ti['color'] }}18;color:{{ $ti['color'] }};font-size:11px;">
                                    {{ $ti['label'] }}
                                </span>
                            </td>
                            <td class="py-2">
                                <span class="badge-conf badge-conf-{{ strtolower($doc->niveau_confidentialite) }}">
                                    <i class="{{ $ci['icon'] }} me-1"></i>{{ $ci['label'] }}
                                </span>
                            </td>
                            <td class="py-2">
                                <span class="badge {{ \App\Models\Document::STATUTS[$doc->statut_document]['badge'] ?? 'bg-secondary' }}" style="font-size:11px;">
                                    {{ $doc->statut_document }}
                                </span>
                            </td>
                            <td class="py-2 text-muted">{{ $doc->date_creation?->format('d/m/Y') ?? '—' }}</td>
                            <td class="py-2 pe-4" onclick="event.stopPropagation()">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('rh.ged.documents.show', $doc->id_document) }}" class="btn btn-sm btn-light" title="Voir" style="color:#1D4ED8;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('rh.ged.documents.download', $doc->id_document) }}" class="btn btn-sm btn-light" title="Télécharger" style="color:#059669;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $documents->links() }}</div>
    @endif

</div>
@endsection
