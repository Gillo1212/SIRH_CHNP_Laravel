@extends('layouts.master')

@section('title', 'Nouvelle Demande de Congé')
@section('page-title', 'Nouvelle Demande de Congé')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.conges.index') }}" style="color:#1565C0;">Mes congés</a></li>
    <li>Nouvelle demande</li>
@endsection

@push('styles')
<style>
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }
@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
.form-card { border-radius:14px;padding:28px 32px; }
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
.solde-badge { display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600; }
.info-box { border-radius:10px;padding:14px 16px;border-left:4px solid #3B82F6;background:#EFF6FF; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="form-card card border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#EFF6FF;">
                        <i class="fas fa-calendar-plus" style="color:#0A4D8C;font-size:20px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--theme-text);">Demande de congé</h5>
                        <p class="text-muted small mb-0">{{ $agent->nom_complet }} — {{ $agent->matricule }}</p>
                    </div>
                </div>

                <form action="{{ route('agent.conges.store') }}" method="POST" id="formConge">
                    @csrf

                    {{-- Type de congé --}}
                    <div class="mb-4">
                        <label class="form-label-custom">Type de congé <span class="text-danger">*</span></label>
                        <select name="id_type_conge" id="id_type_conge" class="form-select-custom form-select @error('id_type_conge') is-invalid @enderror" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($typesConge as $type)
                                <option value="{{ $type->id_type_conge }}"
                                    data-deductible="{{ $type->deductible ? '1' : '0' }}"
                                    data-solde="{{ $soldes->get($type->id_type_conge)?->solde_restant ?? 0 }}"
                                    data-max="{{ $type->nb_jours_droit }}"
                                    {{ old('id_type_conge') == $type->id_type_conge ? 'selected' : '' }}>
                                    {{ $type->libelle }}
                                    @if($type->deductible)
                                        ({{ $soldes->get($type->id_type_conge)?->solde_restant ?? 0 }} j disponibles)
                                    @else
                                        (non déductible)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_type_conge')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Indicateur solde dynamique --}}
                        <div id="solde-info" class="mt-2 d-none">
                            <span class="solde-badge" id="solde-badge" style="background:#D1FAE5;color:#065F46;">
                                <i class="fas fa-calendar-check"></i>
                                <span id="solde-text">—</span>
                            </span>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" id="date_debut"
                                class="form-control-custom form-control @error('date_debut') is-invalid @enderror"
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('date_debut') }}" required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" id="date_fin"
                                class="form-control-custom form-control @error('date_fin') is-invalid @enderror"
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('date_fin') }}" required>
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Calcul automatique --}}
                    <div id="calcul-jours" class="info-box mb-4 d-none">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-calculator" style="color:#3B82F6;"></i>
                            <span style="color:#1E40AF;font-size:13px;">
                                Durée calculée : <strong id="nb-jours-text">—</strong>
                            </span>
                        </div>
                    </div>

                    {{-- Motif --}}
                    <div class="mb-4">
                        <label class="form-label-custom">Motif (optionnel)</label>
                        <textarea name="motif" rows="3"
                            class="form-control-custom form-control @error('motif') is-invalid @enderror"
                            placeholder="Précisez si nécessaire…">{{ old('motif') }}</textarea>
                        @error('motif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Info workflow --}}
                    <div class="alert d-flex align-items-start gap-2 mb-4" style="border-radius:10px;background:#F0F9FF;border:1px solid #BAE6FD;">
                        <i class="fas fa-info-circle mt-1" style="color:#0284C7;"></i>
                        <div style="font-size:12px;color:#075985;">
                            <strong>Workflow de validation :</strong>
                            Votre demande sera d'abord transmise à votre Manager pour validation,
                            puis au service RH pour approbation finale.
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex gap-3">
                        <button type="submit" class="action-btn action-btn-primary">
                            <i class="fas fa-paper-plane"></i> Soumettre la demande
                        </button>
                        <a href="{{ route('agent.conges.index') }}" class="action-btn action-btn-outline">
                            <i class="fas fa-arrow-left"></i> Annuler
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

            // Vérifier contre solde
            const select = document.getElementById('id_type_conge');
            const opt = select.options[select.selectedIndex];
            if (opt && opt.dataset.deductible === '1') {
                const solde = parseInt(opt.dataset.solde || 0);
                const badge = document.getElementById('solde-badge');
                if (diff > solde) {
                    badge.style.background = '#FEE2E2';
                    badge.style.color = '#991B1B';
                } else {
                    badge.style.background = '#D1FAE5';
                    badge.style.color = '#065F46';
                }
            }
        } else {
            divCalc.classList.add('d-none');
        }
    } else {
        divCalc.classList.add('d-none');
    }
}

function updateSoldeInfo() {
    const select = document.getElementById('id_type_conge');
    const opt = select.options[select.selectedIndex];
    const div = document.getElementById('solde-info');
    const text = document.getElementById('solde-text');
    const badge = document.getElementById('solde-badge');

    if (opt && opt.value) {
        if (opt.dataset.deductible === '1') {
            const solde = parseInt(opt.dataset.solde || 0);
            const max = parseInt(opt.dataset.max || 0);
            text.textContent = `Solde disponible : ${solde} jour(s) sur ${max}`;
            badge.style.background = solde > 0 ? '#D1FAE5' : '#FEE2E2';
            badge.style.color = solde > 0 ? '#065F46' : '#991B1B';
            div.classList.remove('d-none');
        } else {
            text.textContent = 'Congé non déductible du solde';
            badge.style.background = '#EFF6FF';
            badge.style.color = '#1E40AF';
            div.classList.remove('d-none');
        }
        calculerJours();
    } else {
        div.classList.add('d-none');
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
document.getElementById('id_type_conge').addEventListener('change', updateSoldeInfo);
document.getElementById('date_debut').addEventListener('change', function() {
    const fin = document.getElementById('date_fin');
    if (fin.value < this.value) fin.value = this.value;
    fin.min = this.value;
    calculerJours();
});
document.getElementById('date_fin').addEventListener('change', calculerJours);
</script>
@endpush
