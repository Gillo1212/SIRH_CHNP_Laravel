@extends('layouts.master')
@section('title', 'Nouvelle prise en charge')
@section('page-title', 'Nouvelle prise en charge')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('pec.index') }}" style="color:#1565C0;">Prises en charge</a></li>
    <li>Nouvelle</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-hospital-alt me-2" style="color:#0A4D8C;"></i>Nouvelle prise en charge
            </h4>
        </div>
        <a href="{{ route('pec.index') }}" class="btn btn-outline-secondary btn-sm">
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

    <div class="card border-0 shadow-sm" style="border-radius:12px;max-width:600px;">
        <div class="card-body p-4">
            <form action="{{ route('pec.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Agent *</label>
                    <select name="agent_id" class="form-select @error('agent_id') is-invalid @enderror" required>
                        <option value="">Sélectionner un agent...</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id_agent }}" {{ old('agent_id') == $agent->id_agent ? 'selected' : '' }}>
                            {{ $agent->nom_complet }} — {{ $agent->matricule }}
                        </option>
                        @endforeach
                    </select>
                    @error('agent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Bénéficiaire *</label>
                    <select name="ayant_droit" class="form-select @error('ayant_droit') is-invalid @enderror" required>
                        <option value="">Sélectionner...</option>
                        <option value="Agent" {{ old('ayant_droit') === 'Agent' ? 'selected' : '' }}>L'agent lui-même</option>
                        <option value="Conjoint" {{ old('ayant_droit') === 'Conjoint' ? 'selected' : '' }}>Conjoint(e)</option>
                        <option value="Enfant" {{ old('ayant_droit') === 'Enfant' ? 'selected' : '' }}>Enfant</option>
                    </select>
                    @error('ayant_droit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Type de prise en charge *</label>
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

                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Raison médicale *</label>
                    <textarea name="raison_medical" rows="3" class="form-control @error('raison_medical') is-invalid @enderror"
                        placeholder="Motif médical de la prise en charge...">{{ old('raison_medical') }}</textarea>
                    @error('raison_medical')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Date de début *</label>
                    <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror"
                        value="{{ old('date_debut', now()->toDateString()) }}" required>
                    @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="exceptionnelle" value="1" id="exceptionnelle"
                            {{ old('exceptionnelle') ? 'checked' : '' }}>
                        <label class="form-check-label" for="exceptionnelle" style="font-size:13px;">
                            PEC exceptionnelle (nécessite validation DRH)
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer
                    </button>
                    <a href="{{ route('pec.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
