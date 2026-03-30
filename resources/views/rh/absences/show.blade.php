@extends('layouts.master')
@section('title', 'Détail Absence')
@section('page-title', 'Détail de l\'Absence')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.absences.index') }}" style="color:#1565C0;">Absences</a></li>
    <li>Détail</li>
@endsection

@push('styles')
<style>
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.action-btn-danger { background:#DC2626;color:#fff; }
.action-btn-danger:hover { background:#B91C1C;color:#fff; }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:#6B7280; }
.info-row { display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid #F3F4F6; }
.info-row:last-child { border-bottom:none; }
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
@keyframes toastOut { from { opacity:1; } to { opacity:0;transform:translateX(40px); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    @php $agent = $absence->demande->agent ?? null; @endphp

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Absence du {{ $absence->date_absence->format('d/m/Y') }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">{{ $agent?->nom_complet }} &mdash; {{ $agent?->service?->nom_service ?? '—' }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('rh.absences.index') }}" class="action-btn action-btn-outline">
                <i class="fas fa-arrow-left"></i>Retour
            </a>
            <button type="button" class="action-btn" style="background:#D97706;color:#fff;"
                    data-bs-toggle="modal" data-bs-target="#modal-edit">
                <i class="fas fa-edit"></i>Modifier
            </button>
            <button type="button" class="action-btn action-btn-danger"
                    data-bs-toggle="modal" data-bs-target="#modal-delete">
                <i class="fas fa-trash"></i>Supprimer
            </button>
        </div>
    </div>

    <div class="row g-4">

        {{-- Colonne gauche : Agent --}}
        <div class="col-12 col-lg-4">

            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4 text-center">
                    <div style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;margin:0 auto 14px;box-shadow:0 4px 15px rgba(10,77,140,.25);">
                        {{ strtoupper(substr($agent?->prenom ?? 'A',0,1).substr($agent?->nom ?? '',0,1)) }}
                    </div>
                    <h6 class="fw-bold mb-1" style="color:var(--theme-text);">{{ $agent?->nom_complet ?? '—' }}</h6>
                    <div class="text-muted small mb-1">{{ $agent?->fonction ?? '—' }}</div>
                    <div style="font-size:11px;color:#9CA3AF;margin-bottom:12px;">{{ $agent?->matricule }}</div>
                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                        <span style="background:#EFF6FF;color:#1E40AF;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                            <i class="fas fa-building me-1"></i>{{ $agent?->service?->nom_service ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">
                        <i class="fas fa-chart-bar me-1" style="color:#1565C0;"></i>Bilan {{ now()->year }}
                    </h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div style="font-size:22px;font-weight:700;color:#DC2626;">{{ $statsAgent['total'] }}</div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">Total</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:22px;font-weight:700;color:#059669;">{{ $statsAgent['justifiees'] }}</div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">Justifiées</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:22px;font-weight:700;color:#D97706;">{{ $statsAgent['injustifiees'] }}</div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">Non just.</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($historiqueAgent->count() > 0)
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">Absences récentes</h6>
                </div>
                <div class="card-body px-4 pb-3">
                    @foreach($historiqueAgent->take(6) as $hist)
                        @php
                            $hc=['Maladie'=>['#FEF3C7','#D97706'],'Personnelle'=>['#DBEAFE','#1D4ED8'],'Professionnelle'=>['#EDE9FE','#7C3AED'],'Injustifiée'=>['#FEE2E2','#DC2626']];
                            [$hbg,$hcolor] = $hc[$hist->type_absence] ?? ['#F3F4F6','#374151'];
                        @endphp
                        <div class="d-flex align-items-center justify-content-between py-2" style="{{ !$loop->last ? 'border-bottom:1px solid #F3F4F6;' : '' }}">
                            <a href="{{ route('rh.absences.show', $hist->id_absence) }}" style="font-size:12px;font-weight:500;color:var(--theme-text);text-decoration:none;">
                                {{ $hist->date_absence->format('d/m/Y') }}
                                @if($hist->id_absence == $absence->id_absence)
                                    <span style="font-size:10px;color:#9CA3AF;">(actuelle)</span>
                                @endif
                            </a>
                            <span style="font-size:10px;background:{{ $hbg }};color:{{ $hcolor }};padding:2px 8px;border-radius:20px;font-weight:600;">{{ $hist->type_absence }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Colonne droite : Détail --}}
        <div class="col-12 col-lg-8">

            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3 d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:10px;background:#FEE2E2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-minus" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--theme-text);">Informations de l'absence</h5>
                        @php
                            $typeColors=['Maladie'=>'#FEF3C7;color:#92400E','Personnelle'=>'#DBEAFE;color:#1E40AF','Professionnelle'=>'#EDE9FE;color:#5B21B6','Injustifiée'=>'#FEE2E2;color:#991B1B'];
                            $ts = $typeColors[$absence->type_absence] ?? '#F3F4F6;color:#374151';
                        @endphp
                        <span style="font-size:12px;background:{{ $ts }};padding:2px 10px;border-radius:20px;font-weight:600;">{{ $absence->type_absence }}</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="info-row">
                        <span class="text-muted small">Date</span>
                        <span style="font-weight:600;color:var(--theme-text);">{{ $absence->date_absence->isoFormat('dddd D MMMM YYYY') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-muted small">Type</span>
                        <span style="font-weight:600;color:var(--theme-text);">{{ $absence->type_absence }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-muted small">Justification</span>
                        @if($absence->justifie)
                            <span style="font-size:12px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-check me-1"></i>Justifiée</span>
                        @else
                            <span style="font-size:12px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-times me-1"></i>Non justifiée</span>
                        @endif
                    </div>
                    <div class="info-row">
                        <span class="text-muted small">Enregistrée le</span>
                        <span style="font-weight:500;color:var(--theme-text);">{{ $absence->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if($absence->commentaire)
                    <div class="mt-3 p-3" style="background:#F8FAFC;border-radius:8px;border-left:3px solid #3B82F6;">
                        <div class="modal-label">Observations</div>
                        <p style="font-size:13px;color:var(--theme-text);margin:0;">{{ $absence->commentaire }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Pièces justificatives soumises par l'agent --}}
            @php $pieces = $absence->piecesJustificatives ?? collect(); @endphp
            @if($pieces->count() > 0)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-paperclip me-2" style="color:#059669;"></i>Justificatifs soumis par l'agent
                    </h6>
                    <span style="background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">{{ $pieces->count() }} document(s)</span>
                </div>
                <div class="card-body px-4 pb-4">
                    @foreach($pieces as $piece)
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:#F8FAFC;border:1px solid #E5E7EB;">
                            <div style="width:38px;height:38px;border-radius:8px;background:{{ $piece->est_pdf ? '#FEF2F2' : '#EFF6FF' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-{{ $piece->est_pdf ? 'file-pdf' : 'file-image' }}" style="color:{{ $piece->est_pdf ? '#DC2626' : '#1D4ED8' }};font-size:16px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-weight:600;font-size:13px;color:var(--theme-text);">{{ $piece->type_piece }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $piece->nom_fichier }} &bull; Déposé le {{ $piece->date_depot->format('d/m/Y à H:i') }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($piece->valide)
                                    <span style="font-size:11px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;">
                                        <i class="fas fa-check me-1"></i>Validé
                                    </span>
                                    <form action="{{ route('rh.absences.pieces.rejeter', [$absence->id_absence, $piece->id_piece]) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-icon" style="background:#FEF3C7;color:#D97706;" title="Annuler la validation">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:11px;background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-weight:600;">
                                        <i class="fas fa-hourglass me-1"></i>En attente
                                    </span>
                                    <form action="{{ route('rh.absences.pieces.valider', [$absence->id_absence, $piece->id_piece]) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-icon" style="background:#D1FAE5;color:#059669;" title="Valider ce justificatif">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ Storage::url($piece->fichier_url) }}" target="_blank"
                                   class="btn-icon" style="background:#EFF6FF;color:#1D4ED8;" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Gestion justificatif --}}
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-file-check me-2" style="color:#1565C0;"></i>Validation du justificatif
                    </h6>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($absence->justifie)
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:#ECFDF5;">
                            <i class="fas fa-check-circle" style="color:#059669;font-size:22px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-weight:600;color:#065F46;font-size:14px;">Justificatif validé</div>
                                <div style="font-size:12px;color:#6B7280;">Document fourni et validé par le service RH.</div>
                            </div>
                        </div>
                        <button type="button" class="action-btn action-btn-outline" style="font-size:13px;padding:7px 14px;"
                                data-bs-toggle="modal" data-bs-target="#modal-rejeter">
                            <i class="fas fa-undo"></i>Annuler la validation
                        </button>
                    @else
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:#FFFBEB;">
                            <i class="fas fa-exclamation-circle" style="color:#D97706;font-size:22px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-weight:600;color:#92400E;font-size:14px;">En attente de justificatif</div>
                                <div style="font-size:12px;color:#6B7280;">
                                    @if($pieces->where('valide',false)->count() > 0)
                                        {{ $pieces->where('valide',false)->count() }} pièce(s) soumise(s) par l'agent — en attente de votre validation.
                                    @else
                                        Aucun document justificatif n'a encore été fourni par l'agent.
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="action-btn" style="background:#059669;color:#fff;font-size:13px;padding:8px 14px;"
                                    data-bs-toggle="modal" data-bs-target="#modal-valider-just">
                                <i class="fas fa-check"></i>Valider l'absence comme justifiée
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL MODIFIER --}}
<div class="modal fade" id="modal-edit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FFF7ED;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FED7AA;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-edit" style="color:#D97706;font-size:18px;"></i></div>
                    <div><h5 class="modal-title fw-bold mb-0">Modifier l'absence</h5><p class="text-muted small mb-0">{{ $agent?->nom_complet }}</p></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.absences.update', $absence->id_absence) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_absence" value="{{ old('date_absence', $absence->date_absence->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select name="type_absence" class="form-select form-select-sm" style="border-radius:7px;" required>
                                @foreach(['Maladie','Personnelle','Professionnelle','Injustifiée'] as $t)
                                    <option value="{{ $t }}" {{ old('type_absence', $absence->type_absence) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check p-3" style="background:#F8FAFC;border-radius:8px;border:1px solid #E5E7EB;">
                            <input class="form-check-input" type="checkbox" name="justifie" id="editJustifie" value="1" {{ old('justifie', $absence->justifie) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-600" for="editJustifie">Absence justifiée</label>
                        </div>
                    </div>
                    <div>
                        <label class="modal-label">Observations</label>
                        <textarea name="commentaire" rows="2" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;">{{ old('commentaire', $absence->commentaire) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#D97706;color:#fff;"><i class="fas fa-save"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL VALIDER JUSTIFICATIF --}}
<div class="modal fade" id="modal-valider-just" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#ECFDF5;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#D1FAE5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-check-circle" style="color:#059669;font-size:18px;"></i></div>
                    <h5 class="modal-title fw-bold mb-0 small">Valider le justificatif</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3"><p style="font-size:13.5px;" class="mb-0">Confirmer la validation du justificatif pour cette absence ?</p></div>
            <form action="{{ route('rh.absences.valider-justificatif', $absence->id_absence) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#059669;color:#fff;padding:8px 16px;font-size:13px;"><i class="fas fa-check"></i>Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REJETER / ANNULER VALIDATION --}}
<div class="modal fade" id="modal-rejeter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FFFBEB;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#FEF3C7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-undo" style="color:#D97706;font-size:18px;"></i></div>
                    <h5 class="modal-title fw-bold mb-0 small">Annuler la validation</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.absences.rejeter-justificatif', $absence->id_absence) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body px-4 py-3">
                    <label class="modal-label">Motif (optionnel)</label>
                    <textarea name="motif_refus" rows="2" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;" placeholder="Raison de l'annulation…"></textarea>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#D97706;color:#fff;padding:8px 16px;font-size:13px;"><i class="fas fa-undo"></i>Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL SUPPRIMER --}}
<div class="modal fade" id="modal-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-trash-alt" style="color:#DC2626;font-size:18px;"></i></div>
                    <h5 class="modal-title fw-bold mb-0 small">Supprimer l'absence</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p style="font-size:13.5px;" class="mb-1">Supprimer l'absence du <strong>{{ $absence->date_absence->format('d/m/Y') }}</strong> de <strong>{{ $agent?->nom_complet }}</strong> ?</p>
                <p class="text-muted small mb-0">Cette action est <strong>irréversible</strong>.</p>
            </div>
            <form action="{{ route('rh.absences.destroy', $absence->id_absence) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-danger" style="padding:8px 16px;font-size:13px;"><i class="fas fa-trash"></i>Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showToast(message, type) {
    type = type || 'success';
    var cfg = {
        success:{ bg:'#ECFDF5',color:'#065F46',icon:'check-circle',border:'#059669' },
        error:  { bg:'#FEF2F2',color:'#991B1B',icon:'times-circle', border:'#DC2626' },
        warning:{ bg:'#FFFBEB',color:'#92400E',icon:'exclamation-triangle',border:'#D97706' }
    };
    var c = cfg[type] || cfg.success;
    var t = document.createElement('div');
    t.style.cssText = 'background:'+c.bg+';color:'+c.color+';padding:14px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:500;min-width:280px;max-width:380px;animation:toastIn .3s ease;border-left:4px solid '+c.border+';pointer-events:all;';
    t.innerHTML = '<i class="fas fa-'+c.icon+'" style="flex-shrink:0;"></i><span>'+message+'</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;margin-left:auto;opacity:.7;padding:0;"><i class="fas fa-times"></i></button>';
    document.getElementById('toast-container').appendChild(t);
    setTimeout(function(){ t.style.animation='toastOut .3s ease forwards'; setTimeout(function(){ t.remove(); }, 300); }, 4000);
}
@if(session('success')) showToast(@json(session('success')), 'success'); @endif
@if(session('error'))   showToast(@json(session('error')),   'error');   @endif
@if(session('warning')) showToast(@json(session('warning')), 'warning'); @endif
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function(){
        new bootstrap.Modal(document.getElementById('modal-edit')).show();
    });
@endif
</script>
@endpush
<style>.fw-600{font-weight:600!important;}</style>
@endsection
