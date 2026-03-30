@extends('layouts.master')

@section('title', 'Enregistrer une absence — Major')
@section('page-title', 'Enregistrer une absence')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('major.absences.index') }}" style="color:#1565C0;">Absences</a></li>
    <li>Enregistrer</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-lg-6">

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-user-minus me-2 text-danger"></i>Nouvelle absence — {{ $service->nom_service }}</h6>
    </div>
    <div class="card-body px-4 pb-4">

        @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('major.absences.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-600" style="font-size:13px;">Agent <span class="text-danger">*</span></label>
                <select name="id_agent" class="form-select @error('id_agent') is-invalid @enderror" required>
                    <option value="">Sélectionner un agent</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id_agent }}" {{ old('id_agent') == $agent->id_agent ? 'selected' : '' }}>
                            {{ $agent->nom_complet }} ({{ $agent->matricule }})
                        </option>
                    @endforeach
                </select>
                @error('id_agent')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-600" style="font-size:13px;">Date d'absence <span class="text-danger">*</span></label>
                <input type="date" name="date_absence" class="form-control @error('date_absence') is-invalid @enderror"
                       value="{{ old('date_absence', today()->format('Y-m-d')) }}"
                       max="{{ today()->format('Y-m-d') }}" required>
                @error('date_absence')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-600" style="font-size:13px;">Type d'absence <span class="text-danger">*</span></label>
                <select name="type_absence" class="form-select @error('type_absence') is-invalid @enderror" required>
                    <option value="">Sélectionner un type</option>
                    @foreach(['Maladie','Personnelle','Professionnelle','Injustifiée'] as $type)
                        <option value="{{ $type }}" {{ old('type_absence') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('type_absence')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" name="justifie" id="justifie" class="form-check-input" value="1" {{ old('justifie') ? 'checked' : '' }}>
                    <label class="form-check-label fw-500" for="justifie" style="font-size:13px;">Absence justifiée</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-1"></i> Enregistrer
                </button>
                <a href="{{ route('major.absences.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
</div>
@endsection
