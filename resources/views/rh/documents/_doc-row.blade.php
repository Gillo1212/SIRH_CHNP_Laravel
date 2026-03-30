{{-- Partial : une ligne de document dans une enveloppe --}}
<div class="doc-row-env">
    {{-- Icône format fichier (SVG inline) --}}
    <x-ged-file-icon :format="$doc->format_fichier ?? $doc->extension" :size="38"/>

    {{-- Infos --}}
    <div class="dr-body">
        <div class="dr-title">{{ $doc->titre }}</div>
        <div class="dr-meta">
            {{ $doc->reference }} · v{{ $doc->version }}
            @if($doc->date_creation) · {{ $doc->date_creation->format('d/m/Y') }} @endif
            @if($doc->taille_fichier) · {{ $doc->taille_humain }} @endif
        </div>
    </div>

    {{-- Badges --}}
    <div class="d-flex flex-column align-items-end gap-1 me-1">
        <span class="badge-conf badge-conf-{{ strtolower($doc->niveau_confidentialite) }}">
            {{ $doc->niveau_confidentialite }}
        </span>
        <span class="{{ $doc->statut_document === 'Actif' ? 'badge-st-actif' : 'badge-st-archive' }}">
            {{ $doc->statut_document }}
        </span>
    </div>

    {{-- Boutons d'action (SVG inline, couleurs contextuelles) --}}
    <div class="d-flex gap-1">
        {{-- Voir --}}
        <button type="button" class="act-btn blue"
            onclick="openViewer({{ $doc->id_document }}, '{{ addslashes($doc->titre) }}', '{{ $doc->extension }}', '{{ route('rh.ged.documents.preview', $doc->id_document) }}')"
            title="Visualiser">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>

        {{-- Télécharger --}}
        <a href="{{ route('rh.ged.documents.download', $doc->id_document) }}" class="act-btn green" title="Télécharger">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>

        {{-- Détails --}}
        <a href="{{ route('rh.ged.documents.show', $doc->id_document) }}" class="act-btn gray" title="Détails">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </a>

        {{-- Archiver / Restaurer --}}
        @if($doc->statut_document === 'Actif')
        <form method="POST" action="{{ route('rh.ged.documents.archiver', $doc->id_document) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="act-btn gray" title="Archiver" onclick="return confirm('Archiver ce document ?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            </button>
        </form>
        @elseif($doc->statut_document === 'Archivé')
        <form method="POST" action="{{ route('rh.ged.documents.restaurer', $doc->id_document) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="act-btn green" title="Restaurer">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.52"/></svg>
            </button>
        </form>
        @endif
    </div>
</div>
