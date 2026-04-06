@extends('layouts.master')
@section('title', 'Congés équipe - Major')
@section('page-title', 'Congés de mon équipe')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Major</a></li>
    <li>Congés équipe</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px;font-weight:700;line-height:1.1;margin-top:12px; }
.kpi-card .kpi-label { font-size:13px;margin-top:2px;font-weight:500; }
.kpi-card::before { content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07; }
.kpi-card.blue::before  { background:#0A4D8C; }
.kpi-card.amber::before { background:#D97706; }
.kpi-card.green::before { background:#059669; }
.kpi-card.indigo::before{ background:#4338CA; }
.conge-row { transition:background 150ms; }
.conge-row:hover { background:#F9FAFB !important; }
.badge-statut { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.btn-avis { display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:7px;font-size:11px;font-weight:600;border:1px solid #BFDBFE;background:#EFF6FF;color:#1E40AF;cursor:pointer;transition:all 150ms; }
.btn-avis:hover { background:#DBEAFE;border-color:#93C5FD; }
.btn-avis.has-avis { background:#D1FAE5;border-color:#6EE7B7;color:#065F46; }
.btn-avis.has-avis:hover { background:#A7F3D0; }
/* Toast */
#toast-container { position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px; }
.toast-msg { padding:12px 18px;border-radius:10px;font-size:13px;font-weight:500;box-shadow:0 4px 16px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;animation:slideInUp .25s ease; }
.toast-success { background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0; }
.toast-error   { background:#FEF2F2;color:#991B1B;border:1px solid #FECACA; }
@keyframes slideInUp { from { transform:translateY(20px);opacity:0; } to { transform:translateY(0);opacity:1; } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Congés - {{ $service->nom_service }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Consultation et avis terrain sur les demandes de congé de votre équipe
                <span class="ms-2 badge" style="background:#D1FAE5;color:#065F46;font-size:10px;">Avis consultatif</span>
            </p>
        </div>
        <a href="{{ route('major.equipe') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-users me-1"></i>Mon équipe
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-file-alt" style="color:#0A4D8C;"></i></div>
                    <span style="background:#DBEAFE;color:#1E40AF;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Total</span>
                </div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['total'] }}</div>
                <div class="kpi-label text-muted">Demandes</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-hourglass-half" style="color:#D97706;"></i></div>
                    <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">En attente</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['en_attente'] }}</div>
                <div class="kpi-label text-muted">En cours de traitement</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-double" style="color:#059669;"></i></div>
                    <span style="background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Validés</span>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['valide'] }}</div>
                <div class="kpi-label text-muted">Approuvés / Validés RH</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card indigo" style="background:#EEF2FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#E0E7FF;"><i class="fas fa-calendar-check" style="color:#4338CA;"></i></div>
                    <span style="background:#E0E7FF;color:#3730A3;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Maintenant</span>
                </div>
                <div class="kpi-value" style="color:#4338CA;">{{ $stats['en_cours'] }}</div>
                <div class="kpi-label text-muted">En cours actuellement</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('major.conges.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="agent" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id_agent }}" {{ request('agent') == $agent->id_agent ? 'selected' : '' }}>
                            {{ $agent->prenom }} {{ $agent->nom }}
                        </option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les statuts</option>
                    <option value="En_attente" {{ request('statut') === 'En_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="Approuvé"   {{ request('statut') === 'Approuvé'   ? 'selected' : '' }}>Approuvé (Manager)</option>
                    <option value="Validé"     {{ request('statut') === 'Validé'     ? 'selected' : '' }}>Validé (RH)</option>
                    <option value="Rejeté"     {{ request('statut') === 'Rejeté'     ? 'selected' : '' }}>Rejeté</option>
                </select>
                <select name="mois" class="form-select" style="width:auto;min-width:140px;">
                    <option value="">Tous les mois</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
                <select name="annee" class="form-select" style="width:auto;min-width:100px;">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ request('annee') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['agent', 'statut', 'mois', 'annee']))
                    <a href="{{ route('major.conges.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-umbrella-beach me-2" style="color:#0A4D8C;"></i>Demandes de congé
                <span class="text-muted ms-2" style="font-size:12px;font-weight:400;">({{ $demandes->total() }} résultats)</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Période</th>
                            <th class="py-3 border-0">Durée</th>
                            <th class="py-3 border-0">Demandé le</th>
                            <th class="py-3 border-0">Statut</th>
                            <th class="py-3 border-0">Mon avis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $demande)
                            @php
                                $agent = $demande->agent;
                                $conge = $demande->conge;
                                $initiales = strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1));

                                [$badgeBg, $badgeColor, $badgeIcon, $badgeLabel] = match($demande->statut_demande) {
                                    'En_attente' => ['#FEF3C7', '#92400E', 'fa-hourglass-half', 'En attente'],
                                    'Approuvé'   => ['#DBEAFE', '#1E40AF', 'fa-user-check',     'Approuvé (Manager)'],
                                    'Validé'     => ['#D1FAE5', '#065F46', 'fa-check-double',   'Validé (RH)'],
                                    'Rejeté'     => ['#FEE2E2', '#991B1B', 'fa-times-circle',   'Rejeté'],
                                    default      => ['#F3F4F6', '#374151', 'fa-circle',          $demande->statut_demande],
                                };

                                $isEnCours = $conge &&
                                    $conge->date_debut <= now() &&
                                    $conge->date_fin   >= now();
                            @endphp
                            <tr class="conge-row" style="border-bottom:1px solid #F3F4F6;">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $initiales }}</div>
                                        <div>
                                            <div style="font-weight:600;color:var(--theme-text);">{{ $agent->nom_complet ?? '-' }}</div>
                                            <div style="font-size:11px;color:#9CA3AF;">{{ $agent->matricule ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 border-0" style="color:var(--theme-text);">
                                    {{ $conge->typeConge->libelle ?? '-' }}
                                </td>
                                <td class="py-3 border-0">
                                    @if($conge)
                                        <div style="font-weight:500;color:var(--theme-text);">
                                            {{ $conge->date_debut->format('d/m/Y') }}
                                            <span class="text-muted mx-1">→</span>
                                            {{ $conge->date_fin->format('d/m/Y') }}
                                        </div>
                                        @if($isEnCours)
                                            <span style="font-size:10px;padding:2px 8px;border-radius:20px;background:#D1FAE5;color:#065F46;font-weight:600;">En cours</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0">
                                    @if($conge && $conge->nbres_jours)
                                        <span style="font-size:13px;font-weight:700;color:#0A4D8C;">{{ $conge->nbres_jours }} j</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0" style="font-size:12px;color:#6B7280;">
                                    {{ $demande->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3 border-0">
                                    <span class="badge-statut" style="background:{{ $badgeBg }};color:{{ $badgeColor }};">
                                        <i class="fas {{ $badgeIcon }}" style="font-size:9px;"></i>
                                        {{ $badgeLabel }}
                                    </span>
                                    @if($demande->statut_demande === 'Rejeté' && $demande->motif_refus)
                                        <div style="font-size:11px;color:#9CA3AF;margin-top:3px;" title="{{ $demande->motif_refus }}">
                                            <i class="fas fa-comment-alt me-1"></i>{{ Str::limit($demande->motif_refus, 40) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 border-0">
                                    <button type="button"
                                        class="btn-avis {{ $demande->avis_major ? 'has-avis' : '' }}"
                                        onclick="openAvisModal({{ $demande->id_demande }}, '{{ addslashes($agent->nom_complet ?? '') }}', '{{ addslashes($demande->avis_major ?? '') }}')"
                                        title="{{ $demande->avis_major ? 'Modifier mon avis' : 'Donner mon avis' }}">
                                        <i class="fas {{ $demande->avis_major ? 'fa-comment-dots' : 'fa-comment' }}"></i>
                                        {{ $demande->avis_major ? 'Modifier' : 'Donner avis' }}
                                    </button>
                                    @if($demande->avis_major_at)
                                        <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">
                                            {{ $demande->avis_major_at->format('d/m H:i') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted border-0">
                                    <i class="fas fa-calendar-times fa-2x mb-3 d-block" style="color:#D1D5DB;"></i>
                                    <p class="mb-1 fw-500">Aucune demande de congé trouvée</p>
                                    <p class="small mb-0">Modifiez les filtres ou attendez de nouvelles demandes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($demandes->hasPages())
            <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">{{ $demandes->links() }}</div>
        @endif
    </div>

    {{-- Note --}}
    <div class="mt-3 d-flex align-items-start gap-2" style="background:#EFF6FF;border-radius:10px;padding:12px 16px;border:1px solid #BFDBFE;">
        <i class="fas fa-info-circle mt-1" style="color:#2563EB;flex-shrink:0;"></i>
        <p class="mb-0" style="font-size:12px;color:#1E40AF;">
            <strong>Votre rôle :</strong> vous pouvez donner votre avis (commentaire terrain) sur chaque demande de congé.
            La validation reste effectuée par le Manager puis par la RH.
            Si un agent est absent sans congé validé, enregistrez une
            <a href="{{ route('major.absences.index') }}" style="color:#1D4ED8;">absence</a>.
        </p>
    </div>

</div>

{{-- Modal : Donner / Modifier avis --}}
<div class="modal fade" id="modal-avis" tabindex="-1" aria-labelledby="modal-avis-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background:#EFF6FF;">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="modal-avis-label" style="color:#0A4D8C;">
                        <i class="fas fa-comment-dots me-2"></i>Mon avis terrain
                    </h6>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#6B7280;" id="avis-agent-name"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-avis" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-1">
                        <label class="form-label fw-600" style="font-size:13px;color:#374151;">
                            Votre commentaire <span class="text-danger">*</span>
                        </label>
                        <textarea id="avis-textarea" name="avis_major" rows="4" maxlength="500"
                            class="form-control" style="border-radius:8px;resize:none;font-size:13px;"
                            placeholder="Ex : Agent indispensable cette semaine - préférable de décaler de 2 jours. / Pas d'impact opérationnel, avis favorable."></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size:11px;color:#9CA3AF;">Visible par le Manager et la RH lors de la validation.</span>
                            <span id="char-count" style="font-size:11px;color:#9CA3AF;">0 / 500</span>
                        </div>
                    </div>
                    <div class="mt-3 p-3 rounded" style="background:#FFFBEB;border:1px solid #FDE68A;">
                        <p class="mb-0" style="font-size:11.5px;color:#92400E;">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Rappel :</strong> Votre avis est consultatif. Il n'approuve ni ne rejette la demande.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-paper-plane me-1"></i>Enregistrer l'avis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast-container"></div>

@push('scripts')
<script>
function openAvisModal(demandeId, agentName, currentAvis) {
    document.getElementById('avis-agent-name').textContent = agentName;
    document.getElementById('form-avis').action = '/major/conges/' + demandeId + '/avis';
    const textarea = document.getElementById('avis-textarea');
    textarea.value = currentAvis;
    updateCharCount(textarea);
    new bootstrap.Modal(document.getElementById('modal-avis')).show();
}

function updateCharCount(el) {
    document.getElementById('char-count').textContent = el.value.length + ' / 500';
}

document.getElementById('avis-textarea').addEventListener('input', function() {
    updateCharCount(this);
});

@if(session('success'))
    showToast('{{ session('success') }}', 'success');
@endif
@if(session('error'))
    showToast('{{ session('error') }}', 'error');
@endif

@if($errors->any())
    const modal = new bootstrap.Modal(document.getElementById('modal-avis'));
    modal.show();
@endif

function showToast(msg, type) {
    const container = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = 'toast-msg toast-' + type;
    el.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i>' + msg;
    container.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
</script>
@endpush

@endsection
