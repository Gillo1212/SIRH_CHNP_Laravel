@extends('layouts.master')
@section('title', 'GED — Déposer un document')
@section('page-title', 'Déposer un document')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.ged.index') }}" style="color:#1565C0;">GED</a></li>
    @if($dossier)
        <li><a href="{{ route('rh.ged.dossier.show', $dossier->id_dossier) }}" style="color:#1565C0;">{{ $dossier->reference }}</a></li>
    @endif
    <li>Déposer</li>
@endsection

@push('styles')
<style>
/* ── UPLOAD AREA ─────────────────────────────────────────────── */
.upload-zone {
    border: 2px dashed #D1D5DB; border-radius: 16px; padding: 40px;
    text-align: center; transition: all 220ms; cursor: pointer;
    background: #FAFAFA; position: relative;
}
.upload-zone:hover, .upload-zone.drag-over {
    border-color: #0A4D8C; background: #EFF6FF;
}
.upload-zone .upload-icon { font-size: 48px; color: #D1D5DB; transition: color 220ms; }
.upload-zone:hover .upload-icon, .upload-zone.drag-over .upload-icon { color: #0A4D8C; }
.upload-zone #fileInput { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.file-preview { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; }

/* ── FORM CARDS ──────────────────────────────────────────────── */
.form-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; padding: 24px; margin-bottom: 20px; }
.form-label-sm { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6B7280; margin-bottom: 6px; }
.form-control-ged { border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px 14px; font-size: 14px; transition: border-color 180ms; }
.form-control-ged:focus { border-color: #0A4D8C; box-shadow: 0 0 0 3px rgba(10,77,140,.1); outline: none; }

/* ── CONFIDENCE SELECTOR ─────────────────────────────────────── */
.conf-option { border: 2px solid #E5E7EB; border-radius: 10px; padding: 12px 16px; cursor: pointer; transition: all 180ms; display: flex; align-items: center; gap: 10px; }
.conf-option:hover { border-color: #D1D5DB; background: #F9FAFB; }
.conf-option.selected { border-color: currentColor; }
.conf-option input[type=radio] { display: none; }
.conf-public       { color: #059669; }
.conf-public.selected    { background: #ECFDF5; border-color: #059669; }
.conf-interne      { color: #1D4ED8; }
.conf-interne.selected   { background: #EFF6FF; border-color: #1D4ED8; }
.conf-confidentiel { color: #D97706; }
.conf-confidentiel.selected { background: #FFFBEB; border-color: #D97706; }
.conf-secret       { color: #DC2626; }
.conf-secret.selected    { background: #FEF2F2; border-color: #DC2626; }

/* ── TYPE SELECTOR ───────────────────────────────────────────── */
.type-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px; }
.type-option { border: 2px solid #E5E7EB; border-radius: 10px; padding: 10px 12px; cursor: pointer; transition: all 180ms; text-align: center; }
.type-option:hover { border-color: #D1D5DB; background: #F9FAFB; }
.type-option.selected { background: #EFF6FF; border-color: #1D4ED8; }
.type-option input { display: none; }
.type-option .to-icon { font-size: 22px; margin-bottom: 4px; }
.type-option .to-label { font-size: 11px; font-weight: 600; color: #374151; }

/* ── AGENT SEARCH ────────────────────────────────────────────── */
.agent-option { display: flex; align-items: center; gap: 10px; padding: 8px 12px; cursor: pointer; }
.agent-option:hover { background: #F9FAFB; }
.agent-avatar-sm { width: 32px; height: 32px; border-radius: 50%; background: #0A4D8C; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <form action="{{ route('rh.ged.documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        <div class="row g-4">

            {{-- ── COL PRINCIPALE ─────────────────────────────────── --}}
            <div class="col-xl-8">

                {{-- Zone dépôt fichier --}}
                <div class="form-card">
                    <div class="form-label-sm mb-3"><i class="ri-upload-cloud-2-line me-1"></i> Fichier à archiver</div>

                    <div class="upload-zone" id="dropZone">
                        <input type="file" name="fichier" id="fileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xls,.xlsx">
                        <div id="uploadPlaceholder">
                            <div class="upload-icon"><i class="ri-upload-cloud-2-line"></i></div>
                            <p class="fw-600 mt-3 mb-1" style="color:#374151;">Glissez votre document ici</p>
                            <p class="text-muted mb-3" style="font-size:13px;">ou cliquez pour sélectionner</p>
                            <span class="badge bg-light text-secondary" style="font-size:11px;">
                                PDF, Word, Excel, JPG, PNG — max 20 Mo
                            </span>
                        </div>
                        <div id="filePreview" class="file-preview d-none">
                            <div id="fileIcon" style="font-size:36px;"></div>
                            <div class="flex-grow-1 text-start">
                                <div id="fileName" class="fw-600" style="font-size:14px;color:#111827;"></div>
                                <div id="fileSize" style="font-size:12px;color:#6B7280;"></div>
                            </div>
                            <button type="button" onclick="clearFile()" class="btn btn-sm btn-light">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>
                    @error('fichier')
                        <div class="text-danger mt-2" style="font-size:13px;"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Informations document --}}
                <div class="form-card">
                    <div class="form-label-sm mb-3"><i class="ri-information-line me-1"></i> Informations du document</div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-sm">Titre du document <span class="text-danger">*</span></label>
                            <input type="text" name="titre" class="form-control-ged w-100"
                                   placeholder="Ex: Contrat à durée indéterminée — Dr. Diallo"
                                   value="{{ old('titre') }}" required>
                            @error('titre') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-sm">Date du document</label>
                            <input type="date" name="date_creation" class="form-control-ged w-100"
                                   value="{{ old('date_creation', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-sm">Version</label>
                            <input type="text" name="version" class="form-control-ged w-100"
                                   placeholder="1.0" value="{{ old('version', '1.0') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label-sm">Mots-clés (séparés par des virgules)</label>
                            <input type="text" name="mots_cles" class="form-control-ged w-100"
                                   placeholder="contrat, CDI, 2026, médecin…"
                                   value="{{ old('mots_cles') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label-sm">Description / Notes</label>
                            <textarea name="description" class="form-control-ged w-100" rows="3"
                                      placeholder="Informations complémentaires sur ce document…">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Type de document --}}
                <div class="form-card">
                    <div class="form-label-sm mb-3"><i class="ri-file-list-3-line me-1"></i> Type de document <span class="text-danger">*</span></div>
                    <div class="type-grid">
                        @foreach($types as $key => $typeInfo)
                        <label class="type-option {{ old('type_document') === $key ? 'selected' : '' }}" data-value="{{ $key }}">
                            <input type="radio" name="type_document" value="{{ $key }}"
                                   {{ old('type_document') === $key ? 'checked' : '' }} required>
                            <div class="to-icon"><i class="{{ $typeInfo['icon'] }}" style="color:{{ $typeInfo['color'] }};"></i></div>
                            <div class="to-label">{{ $typeInfo['label'] }}</div>
                        </label>
                        @endforeach
                    </div>
                    @error('type_document') <div class="text-danger mt-2" style="font-size:12px;">{{ $message }}</div> @enderror
                </div>

            </div>

            {{-- ── COL DROITE ──────────────────────────────────────── --}}
            <div class="col-xl-4">

                {{-- Agent --}}
                <div class="form-card">
                    <div class="form-label-sm mb-3"><i class="ri-user-line me-1"></i> Agent concerné <span class="text-danger">*</span></div>

                    @if($dossier)
                        <input type="hidden" name="agent_id" value="{{ $dossier->agent->id_agent }}">
                        <div class="file-preview" style="border-color:#D1FAE5;">
                            <div class="agent-avatar-sm">
                                {{ strtoupper(substr($dossier->agent->prenom, 0, 1)) }}{{ strtoupper(substr($dossier->agent->nom, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-600" style="font-size:14px;">{{ $dossier->agent->nom_complet }}</div>
                                <div style="font-size:12px;color:#6B7280;">
                                    {{ $dossier->agent->matricule }} — {{ $dossier->agent->service?->nom_service }}
                                </div>
                            </div>
                        </div>
                    @else
                        <select name="agent_id" class="form-control-ged w-100" required>
                            <option value="">-- Sélectionner un agent --</option>
                            @foreach($agents as $ag)
                                <option value="{{ $ag->id_agent }}" {{ old('agent_id') == $ag->id_agent ? 'selected' : '' }}>
                                    {{ $ag->nom_complet }} — {{ $ag->matricule }} ({{ $ag->service?->nom_service }})
                                </option>
                            @endforeach
                        </select>
                        @error('agent_id') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- Niveau de confidentialité (CID) --}}
                <div class="form-card">
                    <div class="form-label-sm mb-3">
                        <i class="ri-lock-line me-1"></i> Confidentialité (CID) <span class="text-danger">*</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @foreach($niveaux as $key => $nInfo)
                        <label class="conf-option conf-{{ strtolower($key) }} {{ old('niveau_confidentialite','Interne') === $key ? 'selected' : '' }}"
                               data-value="{{ $key }}">
                            <input type="radio" name="niveau_confidentialite" value="{{ $key }}"
                                   {{ old('niveau_confidentialite','Interne') === $key ? 'checked' : '' }} required>
                            <i class="{{ $nInfo['icon'] }}" style="font-size:18px;"></i>
                            <div>
                                <div class="fw-600" style="font-size:13px;">{{ $nInfo['label'] }}</div>
                                <div style="font-size:11px;opacity:.75;">
                                    @switch($key)
                                        @case('Public') Accessible par tous les rôles @break
                                        @case('Interne') RH, Manager, DRH, Admin @break
                                        @case('Confidentiel') RH, DRH et Admin uniquement @break
                                        @case('Secret') DRH et Admin uniquement @break
                                    @endswitch
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('niveau_confidentialite') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
                </div>

                {{-- Bouton soumettre --}}
                <button type="submit" class="btn w-100 fw-600 py-3" id="submitBtn"
                        style="background:#0A4D8C;color:#fff;border-radius:12px;font-size:15px;">
                    <i class="ri-upload-cloud-2-line me-2"></i>
                    <span id="submitText">Archiver le document</span>
                </button>

                <a href="{{ $dossier ? route('rh.ged.dossier.show', $dossier->id_dossier) : route('rh.ged.dossiers') }}"
                   class="btn w-100 mt-2" style="border:1px solid #E5E7EB;color:#374151;border-radius:12px;">
                    Annuler
                </a>

                {{-- Note CID --}}
                <div class="mt-3 p-3" style="background:#EFF6FF;border-radius:10px;border:1px solid #DBEAFE;">
                    <div style="font-size:12px;color:#1D4ED8;">
                        <i class="ri-shield-check-line me-1"></i>
                        <strong>Triade CID</strong> — Ce document sera stocké de façon sécurisée.
                        Choisissez le niveau de confidentialité approprié pour contrôler l'accès.
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── TYPE SELECTOR ────────────────────────────────────────────
document.querySelectorAll('.type-option').forEach(opt => {
    opt.addEventListener('click', () => {
        document.querySelectorAll('.type-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        opt.querySelector('input').checked = true;
    });
});

// ── CONF SELECTOR ────────────────────────────────────────────
document.querySelectorAll('.conf-option').forEach(opt => {
    opt.addEventListener('click', () => {
        document.querySelectorAll('.conf-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        opt.querySelector('input').checked = true;
    });
});

// ── FILE UPLOAD ──────────────────────────────────────────────
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const placeholder = document.getElementById('uploadPlaceholder');
const preview = document.getElementById('filePreview');
const fileIcons = { pdf:'ri-file-pdf-2-line', doc:'ri-file-word-line', docx:'ri-file-word-line', xls:'ri-file-excel-line', xlsx:'ri-file-excel-line', jpg:'ri-image-line', jpeg:'ri-image-line', png:'ri-image-line' };
const fileColors= { pdf:'#DC2626', doc:'#1D4ED8', docx:'#1D4ED8', xls:'#059669', xlsx:'#059669', jpg:'#0891B2', jpeg:'#0891B2', png:'#0891B2' };

function showFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    const icon = fileIcons[ext] || 'ri-file-line';
    const color = fileColors[ext] || '#6B7280';
    const size = file.size >= 1048576 ? (file.size/1048576).toFixed(1) + ' Mo' : (file.size/1024).toFixed(0) + ' Ko';

    document.getElementById('fileIcon').innerHTML = `<i class="${icon}" style="color:${color};"></i>`;
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = `${ext.toUpperCase()} · ${size}`;

    placeholder.classList.add('d-none');
    preview.classList.remove('d-none');
    dropZone.style.padding = '16px';
}

function clearFile() {
    fileInput.value = '';
    placeholder.classList.remove('d-none');
    preview.classList.add('d-none');
    dropZone.style.padding = '40px';
}

fileInput.addEventListener('change', () => { if (fileInput.files[0]) showFile(fileInput.files[0]); });

['dragover','dragenter'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('drag-over'); }));
['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => { dropZone.classList.remove('drag-over'); }));
dropZone.addEventListener('drop', ev => {
    ev.preventDefault();
    if (ev.dataTransfer.files[0]) {
        const dt = new DataTransfer();
        dt.items.add(ev.dataTransfer.files[0]);
        fileInput.files = dt.files;
        showFile(ev.dataTransfer.files[0]);
    }
});

// ── SUBMIT ───────────────────────────────────────────────────
document.getElementById('uploadForm').addEventListener('submit', () => {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitText').textContent = 'Archivage en cours…';
});
</script>
@endpush
