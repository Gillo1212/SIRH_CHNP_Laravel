@extends('layouts.master')
@section('title', 'Nouvelle demande de document')
@section('page-title', 'Demande de document administratif')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.docs.index') }}" style="color:#1565C0;">Mes documents</a></li>
    <li>Nouvelle demande</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
            <i class="fas fa-file-plus me-2" style="color:#0A4D8C;"></i>Nouvelle demande de document
        </h4>
        <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary btn-sm">
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

    {{-- Info --}}
    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:16px 20px;margin-bottom:24px;font-size:13px;color:#1E40AF;">
        <i class="fas fa-info-circle me-2"></i>
        Votre demande sera traitée par le service RH dans les meilleurs délais. Vous serez notifié par e-mail lorsque votre document sera prêt.
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;max-width:560px;">
        <div class="card-body p-4">
            <form action="{{ route('agent.docs.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Type de document *</label>
                    <div class="row g-2 mt-1">
                        <div class="col-12">
                            <label style="display:flex;align-items:center;gap:12px;background:#F9FAFB;border:2px solid {{ old('type_document') === 'attestation_travail' ? '#1D4ED8' : '#E5E7EB' }};border-radius:10px;padding:14px 16px;cursor:pointer;transition:border-color 120ms;" class="doc-option">
                                <input type="radio" name="type_document" value="attestation_travail"
                                    {{ old('type_document') === 'attestation_travail' ? 'checked' : '' }} required style="display:none;">
                                <div style="width:40px;height:40px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-file-contract" style="color:#1D4ED8;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;color:var(--theme-text);">Attestation de travail</div>
                                    <div style="font-size:12px;color:#9CA3AF;">Atteste de votre emploi au CHNP</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-12">
                            <label style="display:flex;align-items:center;gap:12px;background:#F9FAFB;border:2px solid {{ old('type_document') === 'certificat_travail' ? '#1D4ED8' : '#E5E7EB' }};border-radius:10px;padding:14px 16px;cursor:pointer;transition:border-color 120ms;" class="doc-option">
                                <input type="radio" name="type_document" value="certificat_travail"
                                    {{ old('type_document') === 'certificat_travail' ? 'checked' : '' }} required style="display:none;">
                                <div style="width:40px;height:40px;background:#F0FDF4;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-certificate" style="color:#059669;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;color:var(--theme-text);">Certificat de travail</div>
                                    <div style="font-size:12px;color:#9CA3AF;">Récapitulatif de votre parcours</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-12">
                            <label style="display:flex;align-items:center;gap:12px;background:#F9FAFB;border:2px solid {{ old('type_document') === 'ordre_mission' ? '#1D4ED8' : '#E5E7EB' }};border-radius:10px;padding:14px 16px;cursor:pointer;transition:border-color 120ms;" class="doc-option">
                                <input type="radio" name="type_document" value="ordre_mission"
                                    {{ old('type_document') === 'ordre_mission' ? 'checked' : '' }} required style="display:none;">
                                <div style="width:40px;height:40px;background:#FEF3C7;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-route" style="color:#D97706;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;color:var(--theme-text);">Ordre de mission</div>
                                    <div style="font-size:12px;color:#9CA3AF;">Pour déplacement officiel</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('type_document')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Motif de la demande</label>
                    <textarea name="motif" rows="3" class="form-control @error('motif') is-invalid @enderror"
                        placeholder="Précisez la raison de votre demande (optionnel)...">{{ old('motif') }}</textarea>
                    @error('motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Soumettre la demande
                    </button>
                    <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.doc-option').forEach(label => {
    label.addEventListener('click', function() {
        document.querySelectorAll('.doc-option').forEach(l => {
            l.style.borderColor = '#E5E7EB';
            l.style.background = '#F9FAFB';
        });
        this.style.borderColor = '#1D4ED8';
        this.style.background = '#EFF6FF';
        this.querySelector('input[type=radio]').checked = true;
    });
});
</script>
@endpush
