@extends('layouts.master')
@section('title', 'Prises en charge médicales')
@section('page-title', 'Prises en charge médicales')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Prises en charge</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:16px 20px;border:1px solid;transition:box-shadow 180ms,transform 180ms;}
.badge-attente{background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-valide{background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-approuve{background:#DBEAFE;color:#1E40AF;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-rejete{background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-hospital me-2" style="color:#0A4D8C;"></i>Prises en charge médicales
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Gestion des demandes de prise en charge des agents</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" style="border-radius:8px;" data-bs-toggle="modal" data-bs-target="#modalCreatePec">
            <i class="fas fa-plus me-1"></i>Nouvelle PEC
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats rapides --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Total</div>
                <div style="font-size:26px;font-weight:700;color:#0A4D8C;margin-top:4px;">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FFFBEB;border-color:#FDE68A;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">En attente</div>
                <div style="font-size:26px;font-weight:700;color:#D97706;margin-top:4px;">{{ $stats['attente'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;border-color:#A7F3D0;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Validées</div>
                <div style="font-size:26px;font-weight:700;color:#059669;margin-top:4px;">{{ $stats['validees'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;border-color:#FECACA;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Rejetées</div>
                <div style="font-size:26px;font-weight:700;color:#DC2626;margin-top:4px;">{{ $stats['rejetees'] }}</div>
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($prises->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Agent</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Bénéficiaire</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Type PEC</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Date</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Statut</th>
                            <th class="border-0 py-3 px-4 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prises as $pec)
                        @php
                            $statut = $pec->demande?->statut_demande ?? 'En_attente';
                            $badgeMap = [
                                'En_attente' => 'badge-attente',
                                'Validé'     => 'badge-valide',
                                'Approuvé'   => 'badge-approuve',
                                'Rejeté'     => 'badge-rejete',
                            ];
                            $statutLabels = [
                                'En_attente' => 'En attente',
                                'Validé'     => 'Validée',
                                'Approuvé'   => 'Approuvée',
                                'Rejeté'     => 'Rejetée',
                            ];
                        @endphp
                        <tr style="border-bottom:1px solid #F3F4F6;">
                            <td class="py-3 px-4 border-0">
                                <div style="font-weight:600;color:var(--theme-text);">{{ $pec->demande?->agent?->nom_complet ?? '-' }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $pec->demande?->agent?->matricule }}</div>
                            </td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $pec->beneficiaireLibelle }}</td>
                            <td class="py-3 px-4 border-0">{{ $pec->type_prise ?? '-' }}</td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $pec->created_at?->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 border-0">
                                <span class="{{ $badgeMap[$statut] ?? 'badge-attente' }}">
                                    {{ $statutLabels[$statut] ?? $statut }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0 text-end">
                                <a href="{{ route('pec.show', $pec->id_priseenche) }}"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#EFF6FF;color:#1D4ED8;text-decoration:none;"
                                   title="Voir">
                                    <i class="fas fa-eye" style="font-size:11px;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-hospital" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
                <p class="text-muted mb-0">Aucune prise en charge enregistrée.</p>
                <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalCreatePec">
                    <i class="fas fa-plus me-1"></i>Créer une prise en charge
                </button>
            </div>
            @endif
        </div>
        @if($prises->hasPages())
        <div class="card-footer bg-transparent px-4 py-3">{{ $prises->links() }}</div>
        @endif
    </div>

</div>

{{-- ===== MODAL NOUVELLE PEC ===== --}}
<div class="modal fade" id="modalCreatePec" tabindex="-1" aria-labelledby="modalCreatePecLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:0;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="background:linear-gradient(135deg,#0A4D8C,#1565C0);border-radius:14px 14px 0 0;padding:18px 24px;">
                <h5 class="modal-title fw-bold text-white" id="modalCreatePecLabel">
                    <i class="fas fa-hospital-alt me-2"></i>Nouvelle prise en charge médicale
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pec.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">

                    @if($errors->any())
                    <div class="alert alert-danger" style="border-radius:8px;font-size:13px;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Agent *</label>
                            <select name="agent_id" class="form-select @error('agent_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un agent...</option>
                                @foreach($agents as $agent)
                                <option value="{{ $agent->id_agent }}" {{ old('agent_id') == $agent->id_agent ? 'selected' : '' }}>
                                    {{ $agent->nom_complet }} - {{ $agent->matricule }}
                                </option>
                                @endforeach
                            </select>
                            @error('agent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Bénéficiaire *</label>
                            <select name="ayant_droit" id="ayantDroit" class="form-select @error('ayant_droit') is-invalid @enderror" required>
                                <option value="">Sélectionner...</option>
                                <option value="Agent"   {{ old('ayant_droit') === 'Agent'   ? 'selected' : '' }}>L'agent lui-même</option>
                                <option value="Conjoint"{{ old('ayant_droit') === 'Conjoint' ? 'selected' : '' }}>Conjoint(e)</option>
                                <option value="Enfant"  {{ old('ayant_droit') === 'Enfant'   ? 'selected' : '' }}>Enfant</option>
                            </select>
                            @error('ayant_droit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Type de prise en charge *</label>
                            <select name="type_prise" class="form-select @error('type_prise') is-invalid @enderror" required>
                                <option value="">Sélectionner...</option>
                                <option value="Consultation"   {{ old('type_prise') === 'Consultation'   ? 'selected' : '' }}>Consultation médicale</option>
                                <option value="Hospitalisation"{{ old('type_prise') === 'Hospitalisation' ? 'selected' : '' }}>Hospitalisation</option>
                                <option value="Médicaments"    {{ old('type_prise') === 'Médicaments'     ? 'selected' : '' }}>Médicaments</option>
                                <option value="Analyses"       {{ old('type_prise') === 'Analyses'        ? 'selected' : '' }}>Analyses médicales</option>
                                <option value="Chirurgie"      {{ old('type_prise') === 'Chirurgie'       ? 'selected' : '' }}>Chirurgie</option>
                                <option value="Autre"          {{ old('type_prise') === 'Autre'           ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type_prise')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Raison médicale *</label>
                            <textarea name="raison_medical" rows="3" class="form-control @error('raison_medical') is-invalid @enderror"
                                placeholder="Motif médical de la prise en charge...">{{ old('raison_medical') }}</textarea>
                            @error('raison_medical')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Date de début *</label>
                            <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror"
                                value="{{ old('date_debut', now()->toDateString()) }}" required>
                            @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>


                        {{-- Justificatif - visible uniquement si Conjoint --}}
                        <div class="col-12" id="justificatifGroup" style="display:none;">
                            <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:12px 16px;">
                                <label class="form-label mb-2" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#92400E;">
                                    <i class="fas fa-paperclip me-1"></i>Certificat de mariage *
                                </label>
                                <input type="file" name="justificatif" class="form-control form-control-sm @error('justificatif') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <div style="font-size:11px;color:#92400E;margin-top:6px;">
                                    Obligatoire pour une PEC conjoint. Formats acceptés : PDF, JPG, PNG (max 5 Mo).
                                </div>
                                @error('justificatif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #F3F4F6;padding:16px 24px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Enregistrer la PEC
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Afficher/masquer le champ justificatif selon le bénéficiaire
document.getElementById('ayantDroit').addEventListener('change', function () {
    document.getElementById('justificatifGroup').style.display =
        this.value === 'Conjoint' ? 'block' : 'none';
});

// Réouvrir le modal si erreurs de validation
@if($errors->any())
    var modal = new bootstrap.Modal(document.getElementById('modalCreatePec'));
    modal.show();
    // Afficher justificatif si l'ancienne valeur était Conjoint
    @if(old('ayant_droit') === 'Conjoint')
        document.getElementById('justificatifGroup').style.display = 'block';
    @endif
@endif
</script>
@endpush

@endsection
