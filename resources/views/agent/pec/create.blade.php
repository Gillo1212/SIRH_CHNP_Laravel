@extends('layouts.master')
@section('title', 'Nouvelle demande de prise en charge')
@section('page-title', 'Demande de prise en charge')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.pec.index') }}" style="color:#1565C0;">Mes PEC</a></li>
    <li>Nouvelle demande</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
            <i class="fas fa-hospital-alt me-2" style="color:#0A4D8C;"></i>Nouvelle demande de prise en charge
        </h4>
        <a href="{{ route('agent.pec.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:10px;">
        <ul class="mb-0" style="font-size:13px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:16px 20px;margin-bottom:24px;font-size:13px;color:#1E40AF;">
        <i class="fas fa-info-circle me-2"></i>
        Votre demande sera examinée par le service RH. Vous pouvez demander une prise en charge pour vous-même, votre conjoint(e) ou vos enfants.
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;max-width:600px;">
        <div class="card-body p-4">
            <form action="{{ route('agent.pec.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Bénéficiaire *</label>
                    <div class="d-flex gap-2">
                        @foreach(['Agent' => 'Moi-même', 'Conjoint' => 'Conjoint(e)', 'Enfant' => 'Enfant'] as $val => $label)
                        <label style="flex:1;text-align:center;background:#F9FAFB;border:2px solid {{ old('ayant_droit') === $val ? '#1D4ED8' : '#E5E7EB' }};border-radius:10px;padding:12px 8px;cursor:pointer;font-size:13px;font-weight:{{ old('ayant_droit') === $val ? '700' : '500' }};color:{{ old('ayant_droit') === $val ? '#1D4ED8' : 'var(--theme-text)' }};" class="beneficiaire-option">
                            <input type="radio" name="ayant_droit" value="{{ $val }}" style="display:none;" {{ old('ayant_droit') === $val ? 'checked' : '' }} required>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    @error('ayant_droit')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3" id="bloc-justificatif" style="display:none;">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Certificat de mariage *</label>
                    <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:10px;padding:12px 16px;margin-bottom:12px;font-size:12px;color:#92400E;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pour une prise en charge du conjoint, vous devez obligatoirement joindre votre certificat de mariage.
                    </div>
                    <input type="file" name="justificatif" class="form-control @error('justificatif') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text">Formats acceptés : PDF, JPG, PNG. Taille max : 5 Mo</div>
                    @error('justificatif')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Type de soin *</label>
                    <select name="type_prise" class="form-select @error('type_prise') is-invalid @enderror" required>
                        <option value="">Sélectionner...</option>
                        <option value="Consultation" {{ old('type_prise') === 'Consultation' ? 'selected' : '' }}>Consultation médicale</option>
                        <option value="Hospitalisation" {{ old('type_prise') === 'Hospitalisation' ? 'selected' : '' }}>Hospitalisation</option>
                        <option value="Médicaments" {{ old('type_prise') === 'Médicaments' ? 'selected' : '' }}>Médicaments</option>
                        <option value="Analyses" {{ old('type_prise') === 'Analyses' ? 'selected' : '' }}>Analyses médicales</option>
                        <option value="Chirurgie" {{ old('type_prise') === 'Chirurgie' ? 'selected' : '' }}>Chirurgie</option>
                        <option value="Autre" {{ old('type_prise') === 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('type_prise')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Raison médicale *</label>
                    <textarea name="raison_medical" rows="4" class="form-control @error('raison_medical') is-invalid @enderror"
                        placeholder="Décrivez la raison médicale de votre demande...">{{ old('raison_medical') }}</textarea>
                    @error('raison_medical')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Soumettre la demande
                    </button>
                    <a href="{{ route('agent.pec.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const blocJustificatif = document.getElementById('bloc-justificatif');

document.querySelectorAll('.beneficiaire-option').forEach(label => {
    label.addEventListener('click', function() {
        document.querySelectorAll('.beneficiaire-option').forEach(l => {
            l.style.borderColor = '#E5E7EB';
            l.style.fontWeight = '500';
            l.style.color = 'var(--theme-text)';
            l.style.background = '#F9FAFB';
        });
        this.style.borderColor = '#1D4ED8';
        this.style.fontWeight = '700';
        this.style.color = '#1D4ED8';
        this.style.background = '#EFF6FF';
        const radio = this.querySelector('input[type=radio]');
        radio.checked = true;
        blocJustificatif.style.display = (radio.value === 'Conjoint') ? 'block' : 'none';
    });
});

// Restaurer l'état si erreur de validation (old value)
(function() {
    const checked = document.querySelector('.beneficiaire-option input[type=radio]:checked');
    if (checked && checked.value === 'Conjoint') {
        blocJustificatif.style.display = 'block';
        const lbl = checked.closest('.beneficiaire-option');
        lbl.style.borderColor = '#1D4ED8';
        lbl.style.fontWeight = '700';
        lbl.style.color = '#1D4ED8';
        lbl.style.background = '#EFF6FF';
    }
})();
</script>
@endpush
