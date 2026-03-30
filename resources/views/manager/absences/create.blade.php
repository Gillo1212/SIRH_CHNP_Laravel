@extends('layouts.master')

@section('title', 'Enregistrer une Absence')
@section('page-title', 'Enregistrer une Absence')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.absences.index') }}" style="color:#1565C0;">Absences</a></li>
    <li>Enregistrer</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-12 col-lg-7">

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header border-0 px-4 pt-4 pb-2">
        <h5 class="fw-bold mb-0"><i class="fas fa-user-minus me-2 text-danger"></i>Enregistrer une absence</h5>
        <p class="text-muted small mb-0">Service : <strong>{{ $service->nom_service }}</strong></p>
    </div>
    <div class="card-body p-4">

        @if($errors->any())
            <div class="alert alert-danger mb-4" style="border-radius:10px;">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('manager.absences.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-600 small">Agent <span class="text-danger">*</span></label>
                <select name="id_agent" class="form-select @error('id_agent') is-invalid @enderror" style="border-radius:8px;">
                    <option value="">— Sélectionner un agent —</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id_agent }}" {{ old('id_agent') == $agent->id_agent ? 'selected' : '' }}>
                            {{ $agent->nom_complet }} ({{ $agent->matricule }})
                        </option>
                    @endforeach
                </select>
                @error('id_agent')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-600 small">Date d'absence <span class="text-danger">*</span></label>
                    <input type="date" name="date_absence" value="{{ old('date_absence', today()->format('Y-m-d')) }}"
                           max="{{ today()->format('Y-m-d') }}"
                           class="form-control @error('date_absence') is-invalid @enderror" style="border-radius:8px;">
                    @error('date_absence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-600 small">Type d'absence <span class="text-danger">*</span></label>
                    <select name="type_absence" class="form-select @error('type_absence') is-invalid @enderror" style="border-radius:8px;">
                        <option value="">— Choisir —</option>
                        @foreach(['Maladie', 'Personnelle', 'Professionnelle', 'Injustifiée'] as $t)
                            <option value="{{ $t }}" {{ old('type_absence') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('type_absence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="justifie" id="justifie" value="1"
                       {{ old('justifie') ? 'checked' : '' }}>
                <label class="form-check-label fw-500 small" for="justifie">
                    Absence justifiée (un document justificatif a été fourni)
                </label>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label fw-600 small">Commentaire</label>
                <textarea name="commentaire" rows="2" class="form-control" style="border-radius:8px;font-size:13px;"
                    placeholder="Observations supplémentaires (optionnel)…">{{ old('commentaire') }}</textarea>
            </div>

            <div class="alert alert-info mt-3" style="border-radius:10px;font-size:13px;">
                <i class="fas fa-info-circle me-2"></i>
                Cette absence sera enregistrée directement dans le dossier de l'agent et visible par la RH.
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-danger d-flex align-items-center gap-2" style="border-radius:8px;">
                    <i class="fas fa-save"></i>Enregistrer l'absence
                </button>
                <a href="{{ route('manager.absences.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Annuler</a>
            </div>
        </form>

    </div>
</div>

</div>
</div>
</div>
@endsection
