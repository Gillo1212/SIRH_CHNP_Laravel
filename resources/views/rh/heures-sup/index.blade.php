@extends('layouts.master')
@section('title', 'Vérification des heures supplémentaires')
@section('page-title', 'Heures Supplémentaires')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Heures supplémentaires</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label { font-size:13px;margin-top:2px;font-weight:500; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.blue::before   { background:#0A4D8C; }
.kpi-card.purple::before { background:#7C3AED; }
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.red::before    { background:#DC2626; }
.hs-row { transition:background 150ms; }
.hs-row:hover { background:#F9FAFB !important; }
.ecart-ok  { background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700; }
.ecart-bad { background:#FEE2E2;color:#991B1B;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700; }
#toast-container { position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px; }
.toast-msg { padding:12px 18px;border-radius:10px;font-size:13px;font-weight:500;box-shadow:0 4px 16px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;animation:slideInUp .25s ease; }
.toast-success { background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0; }
.toast-error   { background:#FEF2F2;color:#991B1B;border:1px solid #FECACA; }
@keyframes slideInUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Vérification des heures supplémentaires</h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            Contrôle de conformité des déclarations soumises par les Majors - aucune modification des données
        </p>
    </div>

    {{-- Bandeau d'information --}}
    <div class="d-flex align-items-start gap-2 mb-4" style="background:#EFF6FF;border-radius:10px;padding:14px 18px;border:1px solid #BFDBFE;">
        <i class="fas fa-info-circle mt-1" style="color:#2563EB;flex-shrink:0;font-size:16px;"></i>
        <div style="font-size:13px;color:#1E40AF;">
            <strong>Rôle de la RH :</strong> vérifier que les heures déclarées par les Majors correspondent
            au dépassement réel constaté dans le planning (durée du poste - 8h standard).
            La RH ne modifie pas les données - elle confirme la conformité ou signale une anomalie au Major.
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-list" style="color:#0A4D8C;"></i></div>
                    <span style="background:#DBEAFE;color:#1E40AF;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Total</span>
                </div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['total'] }}</div>
                <div class="kpi-label text-muted">Déclarations</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-search" style="color:#D97706;"></i></div>
                    <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">À vérifier</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['a_verifier'] }}</div>
                <div class="kpi-label text-muted">En attente de vérification</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-double" style="color:#059669;"></i></div>
                    <span style="background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Conformes</span>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['conformes'] }}</div>
                <div class="kpi-label text-muted">Vérifiées conformes</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-exclamation-triangle" style="color:#DC2626;"></i></div>
                    <span style="background:#FEE2E2;color:#991B1B;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Anomalies</span>
                </div>
                <div class="kpi-value" style="color:#DC2626;">{{ $stats['anomalies'] }}</div>
                <div class="kpi-label text-muted">Renvoyées au Major</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.heures-sup.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="service" class="form-select" style="width:auto;min-width:200px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ request('service') == $svc->id_service ? 'selected' : '' }}>
                            {{ $svc->nom_service }}
                        </option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les statuts</option>
                    <option value="Déclaré"  {{ request('statut') === 'Déclaré'  ? 'selected' : '' }}>Déclaré (à vérifier)</option>
                    <option value="Conforme" {{ request('statut') === 'Conforme' ? 'selected' : '' }}>Conforme</option>
                    <option value="Anomalie" {{ request('statut') === 'Anomalie' ? 'selected' : '' }}>Anomalie</option>
                </select>
                <select name="periode" class="form-select" style="width:auto;min-width:140px;">
                    <option value="">Toutes périodes</option>
                    <option value="Trimestre" {{ request('periode') === 'Trimestre' ? 'selected' : '' }}>Trimestre</option>
                    <option value="Semestre"  {{ request('periode') === 'Semestre'  ? 'selected' : '' }}>Semestre</option>
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['service', 'statut', 'periode']))
                    <a href="{{ route('rh.heures-sup.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-clipboard-check me-2" style="color:#7C3AED;"></i>
                Déclarations à vérifier
                <span class="text-muted ms-2" style="font-size:12px;font-weight:400;">({{ $heuresSup->total() }} résultats)</span>
            </h6>
            @if($stats['a_verifier'] > 0)
                <span style="background:#FEF3C7;color:#92400E;font-size:12px;font-weight:600;padding:4px 12px;border-radius:20px;">
                    <i class="fas fa-search me-1"></i>{{ $stats['a_verifier'] }} à vérifier
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Service / Major</th>
                            <th class="py-3 border-0">Poste planifié</th>
                            <th class="py-3 border-0">Durée planning</th>
                            <th class="py-3 border-0">Dépassement réel</th>
                            <th class="py-3 border-0">Déclaré par Major</th>
                            <th class="py-3 border-0">Écart</th>
                            <th class="py-3 border-0">Statut</th>
                            <th class="py-3 border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($heuresSup as $hs)
                            @php
                                $ligne   = $hs->lignePlanning;
                                $agent   = $ligne?->agent;
                                $service = $ligne?->planning?->service;
                                $major   = $service?->agentMajor;
                                $initiales = strtoupper(substr($agent?->prenom ?? 'A', 0, 1) . substr($agent?->nom ?? '', 0, 1));

                                // Calcul conformité
                                $dureePlanning    = $ligne?->nb_heures ?? 0;
                                $depassementReel  = max(0, $dureePlanning - 8);
                                $depassementDeclare = (float) $hs->nb_heures;
                                $ecart = round($depassementDeclare - $depassementReel, 2);
                                $estConforme = abs($ecart) < 0.01;

                                [$hsBg, $hsColor, $hsIcon, $hsLabel] = match($hs->statut_hs) {
                                    'Déclaré'  => ['#FEF3C7', '#92400E', 'fa-search',           'À vérifier'],
                                    'Conforme' => ['#D1FAE5', '#065F46', 'fa-check-double',     'Conforme'],
                                    'Anomalie' => ['#FEE2E2', '#991B1B', 'fa-exclamation-triangle', 'Anomalie'],
                                    default    => ['#F3F4F6', '#374151', 'fa-circle',            $hs->statut_hs],
                                };
                            @endphp
                            <tr class="hs-row" style="border-bottom:1px solid #F3F4F6;">
                                {{-- Agent --}}
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $initiales }}</div>
                                        <div>
                                            <div style="font-weight:600;color:var(--theme-text);">{{ $agent?->nom_complet ?? '-' }}</div>
                                            <div style="font-size:10px;color:#9CA3AF;">{{ $agent?->matricule ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Service / Major --}}
                                <td class="py-3 border-0">
                                    <div style="font-weight:500;color:var(--theme-text);font-size:12px;">{{ $service?->nom_service ?? '-' }}</div>
                                    <div style="font-size:11px;color:#9CA3AF;">
                                        <i class="fas fa-user-nurse me-1"></i>
                                        Major : {{ $major?->name ?? 'Non assigné' }}
                                    </div>
                                </td>

                                {{-- Poste --}}
                                <td class="py-3 border-0">
                                    <div style="font-weight:500;color:var(--theme-text);">{{ $ligne?->typePoste?->libelle ?? '-' }}</div>
                                    <div style="font-size:11px;color:#9CA3AF;">
                                        {{ $ligne?->date_poste?->format('d/m/Y') }}
                                        @if($ligne?->heure_debut && $ligne?->heure_fin)
                                            · {{ \Carbon\Carbon::parse($ligne->heure_debut)->format('H:i') }}
                                            <i class="fas fa-arrow-right" style="font-size:8px;"></i>
                                            {{ \Carbon\Carbon::parse($ligne->heure_fin)->format('H:i') }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Durée planning --}}
                                <td class="py-3 border-0">
                                    <span style="font-size:15px;font-weight:700;color:#374151;">{{ number_format($dureePlanning, 1) }}<span style="font-size:11px;color:#9CA3AF;"> h</span></span>
                                    <div style="font-size:10px;color:#9CA3AF;">Durée réelle du poste</div>
                                </td>

                                {{-- Dépassement réel (attendu) --}}
                                <td class="py-3 border-0">
                                    @if($depassementReel > 0)
                                        <span style="font-size:15px;font-weight:700;color:#D97706;">{{ number_format($depassementReel, 1) }}<span style="font-size:11px;color:#9CA3AF;"> h</span></span>
                                        <div style="font-size:10px;color:#9CA3AF;">Au-delà des 8h régl.</div>
                                    @else
                                        <span style="font-size:12px;color:#9CA3AF;">Aucun dépassement</span>
                                    @endif
                                </td>

                                {{-- Déclaré par Major --}}
                                <td class="py-3 border-0">
                                    <span style="font-size:18px;font-weight:700;color:#7C3AED;">{{ number_format($depassementDeclare, 1) }}</span>
                                    <span style="font-size:12px;color:#9CA3AF;"> h</span>
                                    <div style="font-size:10px;color:#9CA3AF;">{{ $hs->periode }}</div>
                                </td>

                                {{-- Écart --}}
                                <td class="py-3 border-0">
                                    @if($estConforme)
                                        <span class="ecart-ok"><i class="fas fa-check me-1" style="font-size:9px;"></i>Conforme</span>
                                    @elseif($ecart > 0)
                                        <span class="ecart-bad"><i class="fas fa-arrow-up me-1" style="font-size:9px;"></i>+{{ number_format($ecart, 1) }}h</span>
                                        <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">Surestimation</div>
                                    @else
                                        <span class="ecart-bad"><i class="fas fa-arrow-down me-1" style="font-size:9px;"></i>{{ number_format($ecart, 1) }}h</span>
                                        <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">Sous-estimation</div>
                                    @endif
                                </td>

                                {{-- Statut --}}
                                <td class="py-3 border-0">
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $hsBg }};color:{{ $hsColor }};">
                                        <i class="fas {{ $hsIcon }}" style="font-size:9px;"></i>
                                        {{ $hsLabel }}
                                    </span>
                                    @if($hs->statut_hs === 'Anomalie' && $hs->note_verification)
                                        <div style="font-size:10px;color:#991B1B;margin-top:4px;max-width:150px;" title="{{ $hs->note_verification }}">
                                            <i class="fas fa-comment-alt me-1"></i>{{ Str::limit($hs->note_verification, 40) }}
                                        </div>
                                    @endif
                                    <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">
                                        {{ $hs->created_at->format('d/m/Y') }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3 border-0">
                                    @if($hs->statut_hs === 'Déclaré')
                                        <div class="d-flex gap-1 flex-wrap">
                                            {{-- Conforme --}}
                                            <form action="{{ route('rh.heures-sup.conforme', $hs->id_hsup) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:7px;font-size:11px;font-weight:600;border:1px solid #A7F3D0;background:#ECFDF5;color:#065F46;cursor:pointer;"
                                                    title="Vérification réussie : aucune altération détectée">
                                                    <i class="fas fa-check-double" style="font-size:9px;"></i> Conforme
                                                </button>
                                            </form>
                                            {{-- Anomalie --}}
                                            <button type="button"
                                                style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:600;border:1px solid #FECACA;background:#FEF2F2;color:#991B1B;cursor:pointer;"
                                                title="Signaler un écart au Major"
                                                onclick="openAnomalieModal({{ $hs->id_hsup }})">
                                                <i class="fas fa-exclamation-triangle" style="font-size:9px;"></i> Anomalie
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size:11px;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted border-0">
                                    <i class="fas fa-clipboard-check fa-2x mb-3 d-block" style="color:#D1D5DB;"></i>
                                    <p class="mb-1 fw-500">Aucune déclaration à vérifier</p>
                                    <p class="small mb-0">Les Majors déclarent les heures supplémentaires depuis leur espace terrain.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($heuresSup->hasPages())
            <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">
                {{ $heuresSup->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Modal Anomalie --}}
<div class="modal fade" id="modalAnomalie" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="border-bottom:1px solid #F3F4F6;padding:20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:#FEE2E2;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-exclamation-triangle" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">Signaler une anomalie</h6>
                        <p class="mb-0 text-muted" style="font-size:12px;">Le Major sera notifié pour corriger sa déclaration</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAnomalie" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3" style="background:#FEF2F2;border-radius:8px;padding:12px 14px;border:1px solid #FECACA;">
                        <p class="mb-0" style="font-size:12px;color:#991B1B;">
                            <i class="fas fa-info-circle me-1"></i>
                            La déclaration reste dans le système - le Major la supprimera et soumettra une nouvelle déclaration corrigée.
                        </p>
                    </div>
                    <label class="form-label" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                        Description de l'anomalie constatée *
                    </label>
                    <textarea name="note" class="form-control" rows="4" required minlength="10" maxlength="500"
                        placeholder="Ex : Les heures déclarées (5h) ne correspondent pas au dépassement réel du planning (3h). Poste de nuit du 12/03 : 20h00 - 07h00 = 11h, soit 3h de dépassement sur 8h standard."
                        style="font-size:13px;resize:none;"></textarea>
                    <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Minimum 10 caractères. Soyez précis pour faciliter la correction.</div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #F3F4F6;padding:16px 24px;gap:8px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-exclamation-triangle me-1"></i>Signaler l'anomalie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container"></div>

@push('scripts')
<script>
function openAnomalieModal(hsupId) {
    const form = document.getElementById('formAnomalie');
    form.action = '/rh/heures-sup/' + hsupId + '/anomalie';
    new bootstrap.Modal(document.getElementById('modalAnomalie')).show();
}

function showToast(msg, type) {
    const el = document.createElement('div');
    el.className = 'toast-msg toast-' + type;
    el.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i>' + msg;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), 4500);
}
@if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
@if(session('error'))   showToast('{{ session('error') }}', 'error');   @endif
</script>
@endpush

@endsection
