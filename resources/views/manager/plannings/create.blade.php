@extends('layouts.master')

@section('title', 'Créer un Planning')
@section('page-title', 'Créer un Planning')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.planning.index') }}" style="color:#1565C0;">Plannings</a></li>
    <li>Nouveau</li>
@endsection

@push('styles')
<style>
.ligne-row { background:#F8FAFC;border-radius:8px;padding:12px;margin-bottom:8px;position:relative; }
.ligne-row:hover { background:#EFF6FF; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-12 col-xl-10">

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header border-0 px-4 pt-4 pb-2">
        <h5 class="fw-bold mb-0"><i class="fas fa-calendar-plus me-2 text-primary"></i>Nouveau Planning — {{ $service->nom_service }}</h5>
        <p class="text-muted small mb-0">Le planning sera créé en brouillon. Transmettez-le à la RH quand il est prêt.</p>
    </div>
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger mb-4" style="border-radius:10px;">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('manager.planning.store') }}" method="POST" id="planningForm">
            @csrf

            {{-- Période --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-600 small">Période de début <span class="text-danger">*</span></label>
                    <input type="date" name="periode_debut" id="periodeDebut" value="{{ old('periode_debut') }}"
                           class="form-control @error('periode_debut') is-invalid @enderror" style="border-radius:8px;">
                    @error('periode_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label fw-600 small">Période de fin <span class="text-danger">*</span></label>
                    <input type="date" name="periode_fin" id="periodeFin" value="{{ old('periode_fin') }}"
                           class="form-control @error('periode_fin') is-invalid @enderror" style="border-radius:8px;">
                    @error('periode_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <div class="text-muted small" id="dureeInfo"></div>
                </div>
            </div>

            {{-- Lignes du planning --}}
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="fw-600 small">Lignes du planning</label>
                    <button type="button" id="addLigne" class="btn btn-sm btn-outline-primary" style="border-radius:6px;">
                        <i class="fas fa-plus me-1"></i>Ajouter une ligne
                    </button>
                </div>
                <div id="lignesContainer">
                    {{-- Les lignes seront ajoutées par JS --}}
                </div>
                <div id="noLignes" class="text-center text-muted py-4" style="border:2px dashed #E5E7EB;border-radius:8px;">
                    <i class="fas fa-calendar-plus fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                    <small>Cliquez sur « Ajouter une ligne » pour saisir les postes</small>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius:8px;">
                    <i class="fas fa-save"></i>Enregistrer en brouillon
                </button>
                <a href="{{ route('manager.planning.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Annuler</a>
            </div>
        </form>

    </div>
</div>

</div>
</div>
</div>
@endsection

@push('scripts')
<script>
const agents    = @json($agents->map(fn($a) => ['id' => $a->id_agent, 'nom' => $a->nom_complet]));
const postes    = @json($typesPoste->map(fn($p) => ['id' => $p->id_typeposte, 'nom' => $p->libelle_poste]));
let ligneIndex  = 0;

function createLigneHTML(idx) {
    const agentOpts  = agents.map(a => `<option value="${a.id}">${a.nom}</option>`).join('');
    const posteOpts  = postes.map(p => `<option value="${p.id}">${p.nom}</option>`).join('');
    return `
    <div class="ligne-row" id="ligne-${idx}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-500 mb-1">Agent</label>
                <select name="lignes[${idx}][id_agent]" class="form-select form-select-sm" required style="border-radius:6px;">
                    <option value="">— Agent —</option>${agentOpts}
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-500 mb-1">Date</label>
                <input type="date" name="lignes[${idx}][date_poste]" class="form-control form-control-sm" required style="border-radius:6px;">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-500 mb-1">Type poste</label>
                <select name="lignes[${idx}][id_typeposte]" class="form-select form-select-sm" required style="border-radius:6px;">
                    <option value="">— Poste —</option>${posteOpts}
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-500 mb-1">Heure début</label>
                <input type="time" name="lignes[${idx}][heure_debut]" class="form-control form-control-sm" required style="border-radius:6px;">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-500 mb-1">Heure fin</label>
                <input type="time" name="lignes[${idx}][heure_fin]" class="form-control form-control-sm" required style="border-radius:6px;">
            </div>
            <div class="col-12 col-md-1 d-flex align-items-end">
                <button type="button" onclick="removeLigne(${idx})" class="btn btn-sm btn-outline-danger w-100" style="border-radius:6px;">
                    <i class="fas fa-trash" style="font-size:11px;"></i>
                </button>
            </div>
        </div>
    </div>`;
}

document.getElementById('addLigne').addEventListener('click', function() {
    document.getElementById('noLignes').style.display = 'none';
    document.getElementById('lignesContainer').insertAdjacentHTML('beforeend', createLigneHTML(ligneIndex++));
});

function removeLigne(idx) {
    document.getElementById('ligne-' + idx).remove();
    if (!document.getElementById('lignesContainer').children.length) {
        document.getElementById('noLignes').style.display = '';
    }
}

// Calcul durée
function calcDuree() {
    const d = document.getElementById('periodeDebut').value;
    const f = document.getElementById('periodeFin').value;
    const info = document.getElementById('dureeInfo');
    if (d && f) {
        const diff = Math.round((new Date(f) - new Date(d)) / 86400000) + 1;
        info.textContent = diff > 0 ? `${diff} jour(s)` : '';
    }
}
document.getElementById('periodeDebut').addEventListener('change', calcDuree);
document.getElementById('periodeFin').addEventListener('change', calcDuree);
</script>
@endpush
