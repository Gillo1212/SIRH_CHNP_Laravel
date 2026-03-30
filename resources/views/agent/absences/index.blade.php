@extends('layouts.master')
@section('title', 'Mes Absences')
@section('page-title', 'Mes Absences')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mes absences</li>
@endsection

@push('styles')
<style>
/* ── KPI Cards ─────────────────────────────────────────────── */
.kpi-card { border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label { font-size:13px;margin-top:2px;font-weight:500; }
.kpi-card .kpi-trend { font-size:12px;font-weight:600;margin-top:6px; }
.kpi-card .kpi-trend.up   { color:#10B981; }
.kpi-card .kpi-trend.down { color:#EF4444; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.red::before   { background:#DC2626; }
.kpi-card.green::before { background:#059669; }
.kpi-card.amber::before { background:#D97706; }
.kpi-card.blue::before  { background:#1D4ED8; }

/* ── Buttons ───────────────────────────────────────────────── */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.3); }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.btn-icon { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;transition:all 150ms;font-size:12px; }
.btn-icon-upload { background:#EFF6FF;color:#1D4ED8; }
.btn-icon-upload:hover { background:#DBEAFE; }

/* ── Table ─────────────────────────────────────────────────── */
.abs-row { transition:background 150ms; }
.abs-row:hover { background:#F9FAFB !important; }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:#6B7280; }

/* ── Toast ─────────────────────────────────────────────────── */
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
@keyframes toastOut { from { opacity:1; } to { opacity:0;transform:translateX(40px); } }

/* ── File drop ─────────────────────────────────────────────── */
.file-drop { border:2px dashed #D1D5DB;border-radius:10px;padding:28px 20px;text-align:center;cursor:pointer;transition:all 200ms; }
.file-drop:hover, .file-drop.dragover { border-color:#1565C0;background:#EFF6FF; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    {{-- ─── En-tête ─────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Mes absences</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Consultez, déclarez et justifiez vos absences</p>
        </div>
        <button type="button" class="action-btn action-btn-primary"
                data-bs-toggle="modal" data-bs-target="#modal-demander-absence">
            <i class="fas fa-plus"></i>Déclarer une absence
        </button>
    </div>

    {{-- ─── KPI Cards ───────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-user-minus" style="color:#DC2626;"></i></div>
                    <span style="background:#FEE2E2;color:#991B1B;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">{{ now()->year }}</span>
                </div>
                <div class="kpi-value" style="color:#DC2626;">{{ $statsAnnee['total'] }}</div>
                <div class="kpi-label text-muted">Total absences</div>
                <div class="kpi-trend {{ $statsAnnee['total'] > 5 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $statsAnnee['total'] > 5 ? 'arrow-up' : 'check' }} me-1"></i>
                    {{ $statsAnnee['total'] > 5 ? 'À surveiller' : 'Bonne présence' }}
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
                    <span style="background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">OK</span>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $statsAnnee['justifiees'] }}</div>
                <div class="kpi-label text-muted">Justifiées</div>
                <div class="kpi-trend up"><i class="fas fa-file-check me-1"></i>Documentées</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-paperclip" style="color:#D97706;"></i></div>
                    <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Action</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $statsAnnee['injustifiees'] }}</div>
                <div class="kpi-label text-muted">À justifier</div>
                <div class="kpi-trend {{ $statsAnnee['injustifiees'] > 0 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $statsAnnee['injustifiees'] > 0 ? 'exclamation-triangle' : 'check' }} me-1"></i>
                    {{ $statsAnnee['injustifiees'] > 0 ? 'Joindre un doc.' : 'Aucune' }}
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-hourglass-half" style="color:#1D4ED8;"></i></div>
                    <span style="background:#DBEAFE;color:#1E40AF;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">En cours</span>
                </div>
                <div class="kpi-value" style="color:#1D4ED8;">{{ $statsAnnee['en_attente'] }}</div>
                <div class="kpi-label text-muted">En attente</div>
                <div class="kpi-trend up"><i class="fas fa-clock me-1"></i>Validation RH</div>
            </div>
        </div>
    </div>

    {{-- ─── Bandeaux d'alerte contextuels ──────────────────── --}}
    @if($statsAnnee['injustifiees'] > 0)
    <div class="d-flex align-items-start gap-3 p-4 rounded-3 mb-4" style="background:#FFFBEB;border-left:4px solid #D97706;">
        <i class="fas fa-exclamation-triangle mt-1" style="color:#D97706;font-size:18px;flex-shrink:0;"></i>
        <div class="flex-grow-1">
            <div style="font-weight:600;color:#92400E;font-size:14px;margin-bottom:3px;">{{ $statsAnnee['injustifiees'] }} absence(s) non justifiée(s)</div>
            <p style="font-size:13px;color:#374151;margin:0;">Cliquez sur <strong><i class="fas fa-paperclip me-1"></i>Justifier</strong> dans le tableau pour soumettre un justificatif.</p>
        </div>
    </div>
    @endif

    @if($statsAnnee['en_attente'] > 0)
    <div class="d-flex align-items-start gap-3 p-4 rounded-3 mb-4" style="background:#EFF6FF;border-left:4px solid #1565C0;">
        <i class="fas fa-info-circle mt-1" style="color:#1565C0;font-size:18px;flex-shrink:0;"></i>
        <div>
            <div style="font-weight:600;color:#1D4ED8;font-size:14px;margin-bottom:3px;">{{ $statsAnnee['en_attente'] }} demande(s) en attente de validation</div>
            <p style="font-size:13px;color:#374151;margin:0;">Vos déclarations seront validées par votre responsable ou le service RH.</p>
        </div>
    </div>
    @endif

    {{-- ─── Table historique ─────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-history me-2" style="color:#1565C0;"></i>Historique
                <span class="text-muted ms-1" style="font-size:12px;font-weight:400;">({{ $absences->total() }} entrée(s))</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Date</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Statut demande</th>
                            <th class="py-3 border-0">Justification</th>
                            <th class="py-3 border-0">Justificatif</th>
                            <th class="py-3 border-0 text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absence)
                            @php
                                $demande = $absence->demande;
                                $statutDemande = $demande?->statut_demande ?? 'En_attente';
                                $pieces = $absence->piecesJustificatives ?? collect();
                                $hasPending = $pieces->where('valide', false)->isNotEmpty();
                                $hasValid   = $pieces->where('valide', true)->isNotEmpty();
                                $typeColors = [
                                    'Maladie'         => 'background:#FEF3C7;color:#92400E',
                                    'Personnelle'     => 'background:#DBEAFE;color:#1E40AF',
                                    'Professionnelle' => 'background:#EDE9FE;color:#5B21B6',
                                    'Injustifiée'     => 'background:#FEE2E2;color:#991B1B',
                                ];
                                $statutColors = [
                                    'En_attente' => 'background:#FEF3C7;color:#92400E',
                                    'Validé'     => 'background:#DBEAFE;color:#1E40AF',
                                    'Approuvé'   => 'background:#D1FAE5;color:#065F46',
                                    'Rejeté'     => 'background:#FEE2E2;color:#991B1B',
                                ];
                                $statutLabels = [
                                    'En_attente' => 'En attente',
                                    'Validé'     => 'Pré-validée',
                                    'Approuvé'   => 'Validée',
                                    'Rejeté'     => 'Refusée',
                                ];
                            @endphp
                            <tr class="abs-row" style="border-bottom:1px solid #F3F4F6;">
                                <td class="px-4 py-3 border-0">
                                    <div style="font-weight:600;color:var(--theme-text);">{{ $absence->date_absence->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $absence->date_absence->isoFormat('dddd') }}</div>
                                </td>
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;{{ $typeColors[$absence->type_absence] ?? 'background:#F3F4F6;color:#374151' }};padding:3px 10px;border-radius:20px;font-weight:700;">
                                        {{ $absence->type_absence }}
                                    </span>
                                </td>
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;{{ $statutColors[$statutDemande] ?? 'background:#F3F4F6;color:#374151' }};padding:3px 10px;border-radius:20px;font-weight:600;">
                                        @if($statutDemande === 'En_attente') <i class="fas fa-hourglass-half me-1"></i>
                                        @elseif($statutDemande === 'Approuvé') <i class="fas fa-check me-1"></i>
                                        @elseif($statutDemande === 'Rejeté') <i class="fas fa-times me-1"></i>
                                        @else <i class="fas fa-check-circle me-1"></i>
                                        @endif
                                        {{ $statutLabels[$statutDemande] ?? $statutDemande }}
                                    </span>
                                </td>
                                <td class="py-3 border-0">
                                    @if($absence->justifie)
                                        <span style="font-size:11px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;">
                                            <i class="fas fa-check me-1"></i>Justifiée
                                        </span>
                                    @else
                                        <span style="font-size:11px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-weight:600;">
                                            <i class="fas fa-times me-1"></i>Non justifiée
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 border-0">
                                    @if($hasValid)
                                        <span style="font-size:11px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;">
                                            <i class="fas fa-file-check me-1"></i>Validé
                                        </span>
                                    @elseif($hasPending)
                                        <span style="font-size:11px;background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-weight:600;">
                                            <i class="fas fa-clock me-1"></i>En vérification
                                        </span>
                                    @elseif(!$absence->justifie)
                                        <span style="font-size:11px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-weight:600;">
                                            <i class="fas fa-paperclip me-1"></i>À soumettre
                                        </span>
                                    @else
                                        <span style="font-size:11px;background:#F3F4F6;color:#6B7280;padding:3px 10px;border-radius:20px;font-weight:600;">—</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0 text-end pe-4">
                                    @if(!$absence->justifie && !$hasPending && $statutDemande !== 'Rejeté')
                                        <button type="button"
                                                class="btn-icon btn-icon-upload"
                                                title="Soumettre un justificatif"
                                                onclick="openJustifierModal({{ $absence->id_absence }}, '{{ $absence->date_absence->format('d/m/Y') }}', '{{ $absence->type_absence }}')">
                                            <i class="fas fa-paperclip"></i>
                                        </button>
                                    @else
                                        <span style="width:30px;display:inline-block;"></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted border-0">
                                    <div style="width:64px;height:64px;border-radius:50%;background:#D1FAE5;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;">
                                        <i class="fas fa-calendar-check" style="color:#059669;"></i>
                                    </div>
                                    <p class="fw-600 mb-1" style="color:var(--theme-text);">Aucune absence enregistrée</p>
                                    <p class="small mb-3">Bonne présence au travail !</p>
                                    <button type="button" class="action-btn action-btn-primary" style="margin:0 auto;font-size:13px;padding:8px 16px;"
                                            data-bs-toggle="modal" data-bs-target="#modal-demander-absence">
                                        <i class="fas fa-plus"></i>Déclarer une absence
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($absences->hasPages())
            <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">{{ $absences->links() }}</div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-2 mt-3" style="font-size:12px;color:#9CA3AF;">
        <i class="fas fa-shield-alt" style="color:#1565C0;"></i>
        <span>Vos données sont protégées conformément à la politique de confidentialité du CHNP (Triade CID).</span>
    </div>

</div>

{{-- ══════════════════════════════════════════════════
     MODAL : DÉCLARER UNE ABSENCE
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-demander-absence" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#EFF6FF;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#DBEAFE;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-clock" style="color:#1D4ED8;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Déclarer une absence</h5>
                        <p class="text-muted small mb-0">La demande sera soumise au service RH pour validation</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('agent.absences.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Date de l'absence <span class="text-danger">*</span></label>
                            <input type="date" name="date_absence"
                                   value="{{ today()->format('Y-m-d') }}"
                                   max="{{ today()->format('Y-m-d') }}"
                                   class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="modal-label">Type <span class="text-danger">*</span></label>
                            <select name="type_absence" class="form-select form-select-sm" style="border-radius:7px;" required>
                                <option value="">— Choisir —</option>
                                <option value="Maladie">Maladie</option>
                                <option value="Personnelle">Personnelle</option>
                                <option value="Professionnelle">Professionnelle (formation, mission…)</option>
                                <option value="Injustifiée">Injustifiée</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-label">Motif / Observations <span class="text-muted fw-normal">(optionnel)</span></label>
                        <textarea name="commentaire" rows="2" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"
                                  placeholder="Décrivez brièvement le contexte…"></textarea>
                    </div>
                    <div class="p-3 rounded-3" style="background:#FFFBEB;border-left:3px solid #D97706;">
                        <div class="d-flex gap-2 align-items-start" style="font-size:12px;">
                            <i class="fas fa-info-circle mt-1" style="color:#D97706;flex-shrink:0;"></i>
                            <span style="color:#92400E;">
                                Votre demande sera soumise à l'état <strong>En attente</strong> et validée par votre responsable ou le service RH.
                                Si vous avez un justificatif, vous pourrez l'ajouter après validation.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-paper-plane"></i>Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL : SOUMETTRE UN JUSTIFICATIF
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-justifier-absence" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#ECFDF5;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#D1FAE5;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-paperclip" style="color:#059669;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Soumettre un justificatif</h5>
                        <p class="text-muted small mb-0" id="justifier-absence-info">—</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-justifier-absence" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="modal-label">Type de pièce <span class="text-danger">*</span></label>
                        <select name="type_piece" class="form-select form-select-sm" style="border-radius:7px;" required>
                            <option value="">— Choisir le type de document —</option>
                            <option value="Certificat médical">Certificat médical</option>
                            <option value="Acte décès">Acte de décès (décès familial)</option>
                            <option value="Convocation">Convocation officielle</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="modal-label">Document justificatif <span class="text-danger">*</span></label>
                        <div class="file-drop" id="file-drop-zone" onclick="document.getElementById('fichier-input').click()">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#9CA3AF;"></i>
                            <p style="font-size:13px;font-weight:500;color:#374151;margin:0 0 4px;">
                                Cliquez ou glissez votre fichier ici
                            </p>
                            <p style="font-size:11px;color:#9CA3AF;margin:0;">PDF, JPG, PNG — max. 5 Mo</p>
                            <div id="file-name-preview" class="mt-2" style="display:none;">
                                <span style="background:#D1FAE5;color:#065F46;font-size:12px;padding:3px 10px;border-radius:20px;font-weight:600;">
                                    <i class="fas fa-file me-1"></i><span id="file-name-text"></span>
                                </span>
                            </div>
                        </div>
                        <input type="file" id="fichier-input" name="fichier"
                               accept=".pdf,.jpg,.jpeg,.png" class="d-none" required>
                    </div>
                    <div class="p-3 rounded-3" style="background:#EFF6FF;border-left:3px solid #1565C0;">
                        <div class="d-flex gap-2 align-items-start" style="font-size:12px;">
                            <i class="fas fa-shield-alt mt-1" style="color:#1565C0;flex-shrink:0;"></i>
                            <span style="color:#1E40AF;">
                                Le document sera stocké de façon sécurisée et soumis au service RH pour validation.
                                <strong>L'absence sera marquée comme justifiée</strong> après validation RH.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#059669;color:#fff;">
                        <i class="fas fa-upload"></i>Soumettre le justificatif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ─── TOAST ──────────────────────────────────────────────── */
function showToast(message, type) {
    type = type || 'success';
    var cfg = {
        success:{ bg:'#ECFDF5',color:'#065F46',icon:'check-circle',border:'#059669' },
        error:  { bg:'#FEF2F2',color:'#991B1B',icon:'times-circle', border:'#DC2626' },
        warning:{ bg:'#FFFBEB',color:'#92400E',icon:'exclamation-triangle',border:'#D97706' },
        info:   { bg:'#EFF6FF',color:'#1E40AF',icon:'info-circle',  border:'#1565C0' }
    };
    var c = cfg[type] || cfg.success;
    var t = document.createElement('div');
    t.style.cssText = 'background:'+c.bg+';color:'+c.color+';padding:14px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:500;min-width:280px;max-width:380px;animation:toastIn .3s ease;border-left:4px solid '+c.border+';pointer-events:all;';
    t.innerHTML = '<i class="fas fa-'+c.icon+'" style="flex-shrink:0;"></i><span>'+message+'</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;margin-left:auto;opacity:.7;padding:0;"><i class="fas fa-times"></i></button>';
    document.getElementById('toast-container').appendChild(t);
    setTimeout(function(){ t.style.animation='toastOut .3s ease forwards'; setTimeout(function(){ t.remove(); }, 300); }, 5000);
}

@if(session('success')) showToast(@json(session('success')), 'success'); @endif
@if(session('error'))   showToast(@json(session('error')),   'error');   @endif
@if(session('warning')) showToast(@json(session('warning')), 'warning'); @endif

/* ─── Auto-open modal si erreurs ─────────────────────────── */
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function(){
        new bootstrap.Modal(document.getElementById('modal-demander-absence')).show();
    });
@endif

/* ─── Modal justifier ────────────────────────────────────── */
function openJustifierModal(id, date, type) {
    document.getElementById('form-justifier-absence').action = '/agent/absences/' + id + '/justifier';
    document.getElementById('justifier-absence-info').textContent = 'Absence du ' + date + ' — ' + type;
    // reset file
    document.getElementById('fichier-input').value = '';
    document.getElementById('file-name-preview').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modal-justifier-absence')).show();
}

/* ─── File input preview ─────────────────────────────────── */
document.getElementById('fichier-input').addEventListener('change', function(){
    if (this.files.length > 0) {
        document.getElementById('file-name-text').textContent = this.files[0].name;
        document.getElementById('file-name-preview').style.display = 'block';
    }
});

// Drag & drop
var dropZone = document.getElementById('file-drop-zone');
dropZone.addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('dragover'); });
dropZone.addEventListener('dragleave', function(){ this.classList.remove('dragover'); });
dropZone.addEventListener('drop', function(e){
    e.preventDefault(); this.classList.remove('dragover');
    var files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('fichier-input').files = files;
        document.getElementById('file-name-text').textContent = files[0].name;
        document.getElementById('file-name-preview').style.display = 'block';
    }
});
</script>
@endpush

<style>.fw-600{font-weight:600!important;}</style>
@endsection
