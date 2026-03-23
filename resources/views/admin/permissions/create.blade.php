@extends('layouts.master')

@section('title', 'Nouvelle Permission')
@section('page-title', 'Nouvelle Permission')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.permissions.index') }}" style="color:#1565C0;">Permissions</a></li>
    <li>Nouvelle</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-700 mb-0" style="color:#111827;">
                        <i class="fas fa-key me-2" style="color:#0A4D8C;"></i>Créer une permission
                    </h4>
                    <div style="font-size:13px;color:#6B7280;margin-top:2px;">Format : minuscules et underscores uniquement</div>
                </div>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary fw-600">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>

            <div class="panel">
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf

                    {{-- Générateur de nom --}}
                    <div class="mb-4 p-3 rounded" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                        <h6 class="fw-700 mb-3" style="font-size:13px;color:#374151;">
                            <i class="fas fa-magic me-2 text-primary"></i>Générateur de nom
                        </h6>
                        <div class="row g-2 mb-2">
                            <div class="col">
                                <label class="form-label" style="font-size:12px;font-weight:600;color:#6B7280;">Module</label>
                                <select class="form-select form-select-sm" id="moduleSelect">
                                    <option value="">-- Choisir --</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                                    @endforeach
                                    <option value="custom">Personnalisé...</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label" style="font-size:12px;font-weight:600;color:#6B7280;">Action</label>
                                <select class="form-select form-select-sm" id="actionSelect">
                                    <option value="">-- Choisir --</option>
                                    <option value="voir">voir</option>
                                    <option value="creer">creer</option>
                                    <option value="modifier">modifier</option>
                                    <option value="supprimer">supprimer</option>
                                    <option value="valider">valider</option>
                                    <option value="approuver">approuver</option>
                                    <option value="rejeter">rejeter</option>
                                    <option value="exporter">exporter</option>
                                    <option value="custom">Personnalisée...</option>
                                </select>
                            </div>
                            <div class="col-auto align-self-end">
                                <button type="button" class="btn btn-sm btn-primary fw-600" id="generateBtn">
                                    <i class="fas fa-bolt me-1"></i>Générer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Nom de la permission --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-600" style="font-size:13px;">
                            Nom de la permission <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#F3F4F6;border-color:#E5E7EB;">
                                <i class="fas fa-key" style="color:#6B7280;font-size:13px;"></i>
                            </span>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="ex: voir_statistiques"
                                   autocomplete="off">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div style="font-size:12px;color:#6B7280;margin-top:4px;">
                            Minuscules et underscores uniquement. Exemple : <code>voir_rapports</code>, <code>exporter_donnees</code>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Après création, allez dans la <a href="{{ route('admin.permissions.matrix') }}" class="alert-link">Matrice Permissions</a>
                        pour assigner cette permission aux rôles souhaités.
                    </div>

                    {{-- Boutons --}}
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary fw-600 px-4">
                            <i class="fas fa-save me-2"></i>Créer la permission
                        </button>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary fw-600">
                            Annuler
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
document.getElementById('generateBtn').addEventListener('click', function () {
    const module = document.getElementById('moduleSelect').value;
    const action = document.getElementById('actionSelect').value;

    if (module && module !== 'custom' && action && action !== 'custom') {
        document.getElementById('name').value = action + '_' + module;
    } else {
        document.getElementById('name').focus();
    }
});

// Forcer minuscules + underscores
document.getElementById('name').addEventListener('input', function () {
    this.value = this.value.toLowerCase().replace(/[^a-z_]/g, '');
});
</script>
@endpush
