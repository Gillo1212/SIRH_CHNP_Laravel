@extends('layouts.master')

@section('title', 'Modifier — ' . $division->nom_division)
@section('page-title', 'Modifier une Division')

@section('breadcrumb')
    <li><a href="{{ route('rh.divisions.index') }}" style="color:#1565C0;">Divisions</a></li>
    <li>{{ $division->nom_division }}</li>
    <li>Modifier</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-12 col-lg-6">

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header border-0 px-4 pt-4 pb-2">
        <h5 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-warning"></i>Modifier : {{ $division->nom_division }}</h5>
        <p class="text-muted small mb-0">{{ $division->services->count() }} service(s) dans cette division</p>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('rh.divisions.update', $division->id_division) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-600 small">Nom de la division <span class="text-danger">*</span></label>
                <input type="text" name="nom_division" value="{{ old('nom_division', $division->nom_division) }}"
                       class="form-control @error('nom_division') is-invalid @enderror" style="border-radius:8px;">
                @error('nom_division')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning d-flex align-items-center gap-2" style="border-radius:8px;">
                    <i class="fas fa-save"></i>Enregistrer
                </button>
                <a href="{{ route('rh.divisions.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Annuler</a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
</div>
@endsection
