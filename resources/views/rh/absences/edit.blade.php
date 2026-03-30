@extends('layouts.master')
@section('title', 'Modifier l\'Absence')
@section('page-title', 'Modifier l\'Absence')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.absences.index') }}" style="color:#1565C0;">Absences</a></li>
    <li><a href="{{ route('rh.absences.show', $absence->id_absence) }}" style="color:#1565C0;">Détail</a></li>
    <li>Modifier</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-12 col-xl-8">

    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-header border-0 px-4 pt-4 pb-3 d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:#FEF3C7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-edit" style="color:#D97706;font-size:18px;"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">Modifier l'absence</h5>
                @php $agent = $absence->demande->agent ?? null; @endphp
                <p class="text-muted small mb-0">
                    {{ $agent?->nom_complet ?? '—' }} &mdash; {{ $absence->date_absence->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="card-body p-4">

            @if($errors->any())
                <div class="alert alert-danger mb-4" style="border-radius:10px;border-left:4px solid #EF4444;">
                    <div class="fw-600 mb-1 small"><i class="fas fa-exclamation-circle me-2"></i>Erreurs de validation</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Agent (lecture seule) --}}
            <div class="p-3 mb-4" style="background:#F8FAFC;border-radius:10px;border:1px solid #E5E7EB;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:50%;background:#FEF2F2;color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($agent?->prenom ?? 'A', 0, 1) . substr($agent?->nom ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-600" style="font-size:14px;">{{ $agent?->nom_complet ?? '—' }}</div>
                        <div class="text-muted small">{{ $agent?->matricule }} &bull; {{ $agent?->service?->nom_service ?? '—' }}</div>
                    </div>
                    <div class="ms-auto">
                        <span class="badge bg-secondary" style="font-size:10px;">Agent non modifiable</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('rh.absences.update', $absence->id_absence) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Date + Type --}}
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label fw-600 small">Date de l'absence <span class="text-danger">*</span></label>
                        <input type="date" name="date_absence"
                               value="{{ old('date_absence', $absence->date_absence->format('Y-m-d')) }}"
                               max="{{ today()->format('Y-m-d') }}"
                               class="form-control @error('date_absence') is-invalid @enderror"
                               style="border-radius:8px;font-size:13px;" required>
                        @error('date_absence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-7">
                        <label class="form-label fw-600 small">Type d'absence <span class="text-danger">*</span></label>
                        <select name="type_absence"
                                class="form-select @error('type_absence') is-invalid @enderror"
                                style="border-radius:8px;font-size:13px;" required>
                            <option value="">— Choisir le type —</option>
                            @foreach([
                                'Maladie'         => 'Maladie (certificat médical requis)',
                                'Personnelle'     => 'Personnelle',
                                'Professionnelle' => 'Professionnelle (formation, mission…)',
                                'Injustifiée'     => 'Injustifiée'
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('type_absence', $absence->type_absence) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_absence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Justification --}}
                <div class="p-3 mb-3" style="background:#F8FAFC;border-radius:10px;border:1px solid #E5E7EB;">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="justifie" id="justifie" value="1"
                               {{ old('justifie', $absence->justifie) ? 'checked' : '' }}>
                        <label class="form-check-label fw-600 small" for="justifie">
                            Absence justifiée
                            <span class="text-muted fw-400 ms-1">— un document justificatif a été fourni par l'agent</span>
                        </label>
                    </div>
                </div>

                {{-- Commentaire --}}
                <div class="mb-4">
                    <label class="form-label fw-600 small">
                        Observations <span class="text-muted fw-400">(optionnel)</span>
                    </label>
                    <textarea name="commentaire" rows="3"
                              class="form-control @error('commentaire') is-invalid @enderror"
                              style="border-radius:8px;font-size:13px;resize:vertical;"
                              placeholder="Contexte, circonstances, informations complémentaires…">{{ old('commentaire', $absence->commentaire) }}</textarea>
                    @error('commentaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Bandeau CID --}}
                <div class="alert border-0 mb-4" style="border-radius:10px;background:#FFF7ED;">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="fas fa-history mt-1" style="color:#D97706;flex-shrink:0;"></i>
                        <div style="font-size:13px;">
                            <strong style="color:#92400E;">Traçabilité (Intégrité CID) :</strong>
                            Cette modification sera consignée dans le journal d'audit avec votre identité et l'horodatage.
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning d-flex align-items-center gap-2" style="border-radius:8px;">
                        <i class="fas fa-save"></i>Enregistrer les modifications
                    </button>
                    <a href="{{ route('rh.absences.show', $absence->id_absence) }}" class="btn btn-outline-secondary" style="border-radius:8px;">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
</div>

<style>.fw-600{font-weight:600!important;}</style>
@endsection
