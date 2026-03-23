@extends('layouts.master')

@section('title', 'Nouveau ticket de support')
@section('page-title', 'Nouveau ticket')

@section('breadcrumb')
    <li><a href="{{ route('support.index') }}" style="color:#1565C0;">Support</a></li>
    <li><span style="color:#6B7280;">Nouveau ticket</span></li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card" style="border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;">
    <div class="card-header d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#0A4D8C,#1565C0);padding:20px 24px;">
        <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-headset" style="color:white;font-size:18px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="color:white;font-weight:700;">Ouvrir un ticket de support</h5>
            <div style="font-size:12px;color:rgba(255,255,255,0.75);">Notre équipe vous répondra dans les 24-48h ouvrées</div>
        </div>
    </div>

    <div class="card-body" style="padding:28px 32px;">
        <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                {{-- Sujet --}}
                <div class="col-12">
                    <label class="form-label">Sujet <span style="color:#EF4444;">*</span></label>
                    <input type="text" name="sujet" class="form-control @error('sujet') is-invalid @enderror"
                           value="{{ old('sujet') }}"
                           placeholder="Décrivez brièvement votre problème..."
                           maxlength="255" required>
                    @error('sujet')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Catégorie + Priorité --}}
                <div class="col-md-6">
                    <label class="form-label">Catégorie <span style="color:#EF4444;">*</span></label>
                    <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                        <option value="">Sélectionner...</option>
                        <option value="bug" {{ old('categorie') === 'bug' ? 'selected' : '' }}>🐛 Bug / Erreur</option>
                        <option value="question" {{ old('categorie') === 'question' ? 'selected' : '' }}>❓ Question</option>
                        <option value="amelioration" {{ old('categorie') === 'amelioration' ? 'selected' : '' }}>💡 Suggestion d'amélioration</option>
                        <option value="autre" {{ old('categorie') === 'autre' ? 'selected' : '' }}>📋 Autre</option>
                    </select>
                    @error('categorie')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Priorité <span style="color:#EF4444;">*</span></label>
                    <select name="priorite" class="form-select @error('priorite') is-invalid @enderror" required>
                        <option value="basse" {{ old('priorite', 'normale') === 'basse' ? 'selected' : '' }}>🟢 Basse — Pas urgent</option>
                        <option value="normale" {{ old('priorite', 'normale') === 'normale' ? 'selected' : '' }}>🔵 Normale — Peut attendre</option>
                        <option value="haute" {{ old('priorite') === 'haute' ? 'selected' : '' }}>🟡 Haute — Perturbe le travail</option>
                        <option value="urgente" {{ old('priorite') === 'urgente' ? 'selected' : '' }}>🔴 Urgente — Bloquant</option>
                    </select>
                    @error('priorite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <label class="form-label">
                        Description détaillée <span style="color:#EF4444;">*</span>
                        <span style="font-size:11px;color:#9CA3AF;font-weight:400;">(minimum 20 caractères)</span>
                    </label>
                    <textarea name="description" rows="6"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Décrivez le problème en détail : que faisiez-vous, quel message d'erreur avez-vous vu, quelle page était concernée..."
                              minlength="20" required style="height:auto;resize:vertical;">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div style="font-size:11.5px;color:#9CA3AF;margin-top:4px;">
                        <span id="charCount">0</span> caractères
                    </div>
                </div>

                {{-- Capture d'écran --}}
                <div class="col-12">
                    <label class="form-label">
                        Capture d'écran
                        <span style="font-size:11px;color:#9CA3AF;font-weight:400;">(optionnel — JPG, PNG, max 2 Mo)</span>
                    </label>
                    <input type="file" name="capture_ecran" accept="image/*"
                           class="form-control @error('capture_ecran') is-invalid @enderror"
                           style="height:auto;padding:10px;">
                    @error('capture_ecran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Info SLA --}}
                <div class="col-12">
                    <div class="d-flex gap-2" style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:12px 16px;">
                        <i class="fas fa-clock mt-1" style="color:#D97706;flex-shrink:0;font-size:13px;"></i>
                        <div style="font-size:12.5px;color:#92400E;">
                            <strong>Délais de réponse :</strong>
                            Urgente → 4h · Haute → 24h · Normale → 48h · Basse → 72h ouvrées
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="padding:9px 20px;font-size:13.5px;">
                        <i class="fas fa-paper-plane me-1"></i>Soumettre le ticket
                    </button>
                    <a href="{{ route('support.index') }}" class="btn btn-light" style="border:1px solid #E5E7EB;padding:9px 20px;font-size:13.5px;">
                        Annuler
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
const textarea = document.querySelector('textarea[name="description"]');
const counter = document.getElementById('charCount');
if (textarea && counter) {
    textarea.addEventListener('input', () => {
        const len = textarea.value.length;
        counter.textContent = len;
        counter.style.color = len < 20 ? '#EF4444' : '#10B981';
    });
    // Initial
    counter.textContent = textarea.value.length;
}
</script>
@endpush
