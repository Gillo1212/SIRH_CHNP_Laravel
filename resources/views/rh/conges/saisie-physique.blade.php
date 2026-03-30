@extends('layouts.master')

@section('title', 'Saisie Physique — Congé')
@section('page-title', 'Saisie Physique')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>Saisie physique</li>
@endsection

@push('styles')
<style>
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-amber { background:#D97706;color:#fff; }
.action-btn-amber:hover { background:#B45309;color:#fff; }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }
.form-label-custom { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:5px; }
.form-control-custom, .form-select-custom {
    border-radius:8px;font-size:13px;padding:10px 14px;
    border:1.5px solid var(--theme-border);
    background:var(--theme-panel-bg);color:var(--theme-text);
    transition:border-color 200ms,box-shadow 200ms;
}
.form-control-custom:focus, .form-select-custom:focus {
    border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12);outline:none;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:14px;padding:28px 32px;">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:#FEF3C7;">
                        <i class="fas fa-pen-to-square" style="color:#D97706;font-size:22px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--theme-text);">Saisie physique — Congé</h5>
                        <p class="text-muted small mb-0">Pour les agents qui se présentent directement au bureau RH. Le congé est approuvé immédiatement.</p>
                    </div>
                </div>

                <div class="alert d-flex align-items-start gap-2 mb-4" style="border-radius:10px;background:#FEF3C7;border:1px solid #FDE68A;">
                    <i class="fas fa-exclamation-triangle mt-1" style="color:#D97706;"></i>
                    <div style="font-size:12px;color:#92400E;">
                        <strong>Saisie directe :</strong> Ce formulaire crée un congé <strong>directement approuvé</strong>,
                        sans passer par le workflow Agent → Manager → RH. Utilisez-le uniquement pour les demandes physiques.
                    </div>
                </div>

                <form action="{{ route('rh.conge-physique.store') }}" method="POST" id="formSaisiePhysique">
                    @csrf

                    {{-- Agent --}}
                    <div class="mb-4">
                        <label class="form-label-custom">Agent concerné <span class="text-danger">*</span></label>
                        <select name="id_agent" id="id_agent" class="form-select-custom form-select @error('id_agent') is-invalid @enderror" required>
                            <option value="">-- Sélectionner l'agent --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id_agent }}" {{ old('id_agent') == $agent->id_agent ? 'selected' : '' }}>
                                    {{ $agent->nom_complet }} ({{ $agent->matricule }}) — {{ $agent->service->nom_service ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_agent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Type de congé --}}
                    <div class="mb-4">
                        <label class="form-label-custom">Type de congé <span class="text-danger">*</span></label>
                        <select name="id_type_conge" id="id_type_conge" class="form-select-custom form-select @error('id_type_conge') is-invalid @enderror" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($typesConge as $type)
                                <option value="{{ $type->id_type_conge }}"
                                    data-deductible="{{ $type->deductible ? '1' : '0' }}"
                                    {{ old('id_type_conge') == $type->id_type_conge ? 'selected' : '' }}>
                                    {{ $type->libelle }}
                                    ({{ $type->deductible ? 'Déductible — '.$type->nb_jours_droit.'j max' : 'Non déductible' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_type_conge')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dates --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" id="date_debut"
                                class="form-control-custom form-control @error('date_debut') is-invalid @enderror"
                                value="{{ old('date_debut') }}" required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" id="date_fin"
                                class="form-control-custom form-control @error('date_fin') is-invalid @enderror"
                                value="{{ old('date_fin') }}" required>
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Calcul durée --}}
                    <div id="calcul-jours" class="mb-4 d-none">
                        <div class="d-flex align-items-center gap-2 p-3 rounded" style="background:#EFF6FF;border:1px solid #BFDBFE;">
                            <i class="fas fa-calculator" style="color:#3B82F6;"></i>
                            <span style="color:#1E40AF;font-size:13px;">
                                Durée calculée : <strong id="nb-jours-text">—</strong>
                            </span>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex gap-3">
                        <button type="submit" class="action-btn action-btn-amber">
                            <i class="fas fa-check-double"></i> Enregistrer et approuver
                        </button>
                        <a href="{{ route('rh.conges.index') }}" class="action-btn action-btn-outline">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function calculerJours() {
    const debut = document.getElementById('date_debut').value;
    const fin   = document.getElementById('date_fin').value;
    const divCalc = document.getElementById('calcul-jours');
    const nbJoursText = document.getElementById('nb-jours-text');
    if (debut && fin) {
        const d1 = new Date(debut);
        const d2 = new Date(fin);
        if (d2 >= d1) {
            const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
            nbJoursText.textContent = diff + ' jour(s)';
            divCalc.classList.remove('d-none');
        } else {
            divCalc.classList.add('d-none');
        }
    } else {
        divCalc.classList.add('d-none');
    }
}
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif
function showToast(message, type) {
    const cfg = { success:{bg:'#10B981',icon:'fa-check-circle'}, error:{bg:'#EF4444',icon:'fa-exclamation-circle'} };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `<div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:400px;animation:toastIn .3s ease;"><i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i><span>${message}</span><button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}
document.getElementById('date_debut').addEventListener('change', function() {
    const fin = document.getElementById('date_fin');
    if (fin.value && fin.value < this.value) fin.value = this.value;
    fin.min = this.value;
    calculerJours();
});
document.getElementById('date_fin').addEventListener('change', calculerJours);
</script>
@endpush
