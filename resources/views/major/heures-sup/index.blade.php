@extends('layouts.master')
@section('title', 'Heures supplémentaires - ' . $service->nom_service)
@section('page-title', 'Heures Supplémentaires')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Major</a></li>
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
.kpi-card.amber::before  { background:#D97706; }
.kpi-card.green::before  { background:#059669; }
.kpi-card.purple::before { background:#7C3AED; }
.tab-btn { padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;border:1px solid #E5E7EB;background:white;color:#6B7280;cursor:pointer;transition:all 180ms;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
.tab-btn.active { background:#0A4D8C;color:white;border-color:#0A4D8C; }
.tab-btn:hover:not(.active) { background:#F3F4F6;color:#374151; }
.ligne-row { transition:background 150ms; }
.ligne-row:hover { background:#F9FAFB !important; }
.badge-sup { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700; }
.duree-chip { display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700; }
.btn-declarer { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all 150ms; }
/* Toast */
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
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Heures supplémentaires - {{ $service->nom_service }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Déclarez les dépassements horaires sur vos lignes de planning - le service RH vérifie la conformité
            </p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-calendar-week" style="color:#0A4D8C;"></i></div>
                    <span style="background:#DBEAFE;color:#1E40AF;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Postes</span>
                </div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['total_lignes'] }}</div>
                <div class="kpi-label text-muted">Lignes de planning</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card purple" style="background:#F5F3FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#EDE9FE;"><i class="fas fa-file-signature" style="color:#7C3AED;"></i></div>
                    <span style="background:#EDE9FE;color:#5B21B6;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Déclarations</span>
                </div>
                <div class="kpi-value" style="color:#7C3AED;">{{ $stats['total_declares'] }}</div>
                <div class="kpi-label text-muted">Déclarations soumises</div>
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
            <div class="kpi-card" style="background:#FEF2F2;border:1px solid #FECACA;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-exclamation-triangle" style="color:#DC2626;"></i></div>
                    <span style="background:#FEE2E2;color:#991B1B;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">À corriger</span>
                </div>
                <div class="kpi-value" style="color:#DC2626;">{{ $stats['a_corriger'] }}</div>
                <div class="kpi-label" style="color:#991B1B;">Anomalie signalée par RH</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="d-flex gap-2 mb-4">
        <button class="tab-btn active" id="tab-lignes-btn" onclick="switchTab('lignes')">
            <i class="fas fa-calendar-week"></i> Lignes de planning
        </button>
        <button class="tab-btn" id="tab-declarations-btn" onclick="switchTab('declarations')">
            <i class="fas fa-list-ul"></i> Mes déclarations
            @if($stats['a_corriger'] > 0)
                <span style="background:#FEE2E2;color:#991B1B;font-size:10px;padding:1px 7px;border-radius:20px;">{{ $stats['a_corriger'] }} à corriger</span>
            @endif
        </button>
    </div>

    {{-- ════════════════════════════════════════
         TAB 1 - LIGNES DE PLANNING
         ════════════════════════════════════════ --}}
    <div id="panel-lignes">

        {{-- Explication --}}
        <div class="d-flex align-items-start gap-2 mb-3" style="background:#EFF6FF;border-radius:10px;padding:12px 16px;border:1px solid #BFDBFE;">
            <i class="fas fa-info-circle mt-1" style="color:#2563EB;flex-shrink:0;"></i>
            <p class="mb-0" style="font-size:12px;color:#1E40AF;">
                <strong>Comment ça fonctionne :</strong> chaque ligne correspond à un poste planifié (agent + date + horaires).
                La durée réelle est calculée automatiquement. Cliquez sur <strong>Déclarer</strong> sur les lignes
                dont la durée dépasse le temps réglementaire <strong>(8h standard)</strong>.
            </p>
        </div>

        {{-- Filtres --}}
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <form method="GET" action="{{ route('major.heures-sup.index') }}">
                <input type="hidden" name="_tab" value="lignes">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="agent" class="form-select" style="width:auto;min-width:180px;">
                        <option value="">Tous les agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id_agent }}" {{ request('agent') == $agent->id_agent ? 'selected' : '' }}>
                                {{ $agent->prenom }} {{ $agent->nom }}
                            </option>
                        @endforeach
                    </select>
                    <select name="planning" class="form-select" style="width:auto;min-width:200px;">
                        <option value="">Tous les plannings</option>
                        @foreach($plannings as $p)
                            <option value="{{ $p->id_planning }}" {{ request('planning') == $p->id_planning ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($p->periode_debut)->format('d/m/Y') }}
                                → {{ \Carbon\Carbon::parse($p->periode_fin)->format('d/m/Y') }}
                                ({{ $p->statut_planning }})
                            </option>
                        @endforeach
                    </select>
                    <label class="d-flex align-items-center gap-2 ms-1" style="font-size:13px;cursor:pointer;user-select:none;">
                        <input type="checkbox" name="avec_sup" value="1" {{ request('avec_sup') ? 'checked' : '' }}
                            style="width:15px;height:15px;accent-color:#7C3AED;">
                        <span style="color:#374151;">Uniquement &gt; 8h</span>
                    </label>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    @if(request()->anyFilled(['agent', 'planning', 'avec_sup']))
                        <a href="{{ route('major.heures-sup.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tableau lignes --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                                <th class="px-4 py-3 border-0">Agent</th>
                                <th class="py-3 border-0">Type de poste</th>
                                <th class="py-3 border-0">Date</th>
                                <th class="py-3 border-0">Horaires</th>
                                <th class="py-3 border-0">Durée réelle</th>
                                <th class="py-3 border-0">Dépassement</th>
                                <th class="py-3 border-0">Statut HS</th>
                                <th class="py-3 border-0">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lignesAffichees = $lignes ?? ($lignesPaginated ?? collect());
                            @endphp
                            @forelse($lignesAffichees as $ligne)
                                @php
                                    $agent      = $ligne->agent;
                                    $initiales  = strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1));
                                    $duree      = $ligne->nb_heures;   // accesseur du modèle
                                    $depassement = max(0, $duree - 8);
                                    $heureSup   = $ligne->heureSup;

                                    // Couleur de la durée
                                    if ($duree > 10)     { $dureeBg = '#FEE2E2'; $dureeColor = '#991B1B'; }
                                    elseif ($duree > 8)  { $dureeBg = '#FEF3C7'; $dureeColor = '#92400E'; }
                                    else                 { $dureeBg = '#F3F4F6'; $dureeColor = '#374151'; }

                                    // Couleur du dépassement
                                    if ($depassement > 4)     { $supBg = '#FEE2E2'; $supColor = '#991B1B'; }
                                    elseif ($depassement > 0) { $supBg = '#FEF3C7'; $supColor = '#92400E'; }
                                    else                      { $supBg = '#D1FAE5'; $supColor = '#065F46'; }
                                @endphp
                                <tr class="ligne-row" style="border-bottom:1px solid #F3F4F6;">
                                    <td class="px-4 py-3 border-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $initiales }}</div>
                                            <div>
                                                <div style="font-weight:600;color:var(--theme-text);">{{ $agent->nom_complet ?? '-' }}</div>
                                                <div style="font-size:10px;color:#9CA3AF;">{{ $agent->matricule ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 border-0">
                                        <span style="font-size:12px;font-weight:500;color:var(--theme-text);">{{ $ligne->typePoste->libelle ?? '-' }}</span>
                                        <div style="font-size:10px;color:#9CA3AF;">
                                            Planning du {{ $ligne->planning->periode_debut->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="py-3 border-0" style="font-weight:500;color:var(--theme-text);">
                                        {{ $ligne->date_poste->format('d/m/Y') }}
                                        <div style="font-size:10px;color:#9CA3AF;">
                                            {{ $ligne->date_poste->isoFormat('dddd') }}
                                        </div>
                                    </td>
                                    <td class="py-3 border-0">
                                        <span style="font-size:12px;font-weight:600;color:#374151;">
                                            {{ \Carbon\Carbon::parse($ligne->heure_debut)->format('H:i') }}
                                            <span class="text-muted">→</span>
                                            {{ \Carbon\Carbon::parse($ligne->heure_fin)->format('H:i') }}
                                        </span>
                                        @php $hdebut = \Carbon\Carbon::parse($ligne->heure_debut); $hfin = \Carbon\Carbon::parse($ligne->heure_fin); @endphp
                                        @if($hfin->lessThan($hdebut))
                                            <div style="font-size:10px;color:#7C3AED;">Poste de nuit (J+1)</div>
                                        @endif
                                    </td>
                                    <td class="py-3 border-0">
                                        <span class="duree-chip" style="background:{{ $dureeBg }};color:{{ $dureeColor }};">
                                            {{ number_format($duree, 1) }}h
                                        </span>
                                    </td>
                                    <td class="py-3 border-0">
                                        @if($depassement > 0)
                                            <span class="badge-sup" style="background:{{ $supBg }};color:{{ $supColor }};">
                                                <i class="fas fa-plus" style="font-size:8px;"></i>
                                                {{ number_format($depassement, 1) }}h
                                            </span>
                                        @else
                                            <span style="font-size:11px;color:#9CA3AF;">Dans les normes</span>
                                        @endif
                                    </td>
                                    <td class="py-3 border-0">
                                        @if($heureSup)
                                            @php
                                                [$hsBg, $hsColor, $hsLabel] = match($heureSup->statut_hs) {
                                                    'Déclaré'  => ['#FEF3C7', '#92400E', 'En vérification RH'],
                                                    'Conforme' => ['#D1FAE5', '#065F46', 'Conforme'],
                                                    'Anomalie' => ['#FEE2E2', '#991B1B', 'Anomalie - à corriger'],
                                                    default    => ['#F3F4F6', '#374151', $heureSup->statut_hs],
                                                };
                                            @endphp
                                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $hsBg }};color:{{ $hsColor }};">
                                                <i class="fas fa-check" style="font-size:9px;"></i>
                                                {{ $hsLabel }}
                                            </span>
                                            <div style="font-size:10px;color:#9CA3AF;margin-top:2px;">
                                                {{ number_format($heureSup->nb_heures, 1) }}h déclarées
                                            </div>
                                        @else
                                            <span style="font-size:11px;color:#9CA3AF;">Non déclarées</span>
                                        @endif
                                    </td>
                                    <td class="py-3 border-0">
                                        @if(!$heureSup)
                                            <button type="button"
                                                class="btn-declarer"
                                                style="background:{{ $depassement > 0 ? '#7C3AED' : '#F3F4F6' }};color:{{ $depassement > 0 ? 'white' : '#6B7280' }};"
                                                onclick="openDeclareModal(
                                                    {{ $ligne->id_ligne }},
                                                    '{{ addslashes($agent->nom_complet ?? '') }}',
                                                    '{{ addslashes($ligne->typePoste->libelle ?? '') }}',
                                                    '{{ $ligne->date_poste->format('d/m/Y') }}',
                                                    {{ number_format($depassement, 1, '.', '') }}
                                                )">
                                                <i class="fas fa-plus-circle"></i>
                                                Déclarer
                                            </button>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted border-0">
                                        <i class="fas fa-calendar-times fa-2x mb-3 d-block" style="color:#D1D5DB;"></i>
                                        <p class="mb-1 fw-500">Aucune ligne de planning trouvée</p>
                                        <p class="small mb-0">Créez d'abord un planning depuis l'onglet Plannings.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($lignesPaginated && $lignesPaginated->hasPages())
                <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">
                    {{ $lignesPaginated->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════
         TAB 2 - DÉCLARATIONS
         ════════════════════════════════════════ --}}
    <div id="panel-declarations" style="display:none;">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header border-0 bg-transparent px-4 py-3">
                <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                    <i class="fas fa-list-ul me-2" style="color:#7C3AED;"></i>
                    Déclarations d'heures supplémentaires
                    <span class="text-muted ms-2" style="font-size:12px;font-weight:400;">({{ $declarations->count() }} total)</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                                <th class="px-4 py-3 border-0">Agent</th>
                                <th class="py-3 border-0">Poste planifié</th>
                                <th class="py-3 border-0">Heures sup déclarées</th>
                                <th class="py-3 border-0">Période</th>
                                <th class="py-3 border-0">Déclaré le</th>
                                <th class="py-3 border-0">Statut</th>
                                <th class="py-3 border-0">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($declarations as $hs)
                                @php
                                    $ligne = $hs->lignePlanning;
                                    $agent = $ligne?->agent;
                                    $initiales = strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1));
                                    [$hsBg, $hsColor, $hsIcon, $hsLabel] = match($hs->statut_hs) {
                                        'Déclaré'  => ['#FEF3C7', '#92400E', 'fa-clock',               'En vérification RH'],
                                        'Conforme' => ['#D1FAE5', '#065F46', 'fa-check-double',         'Conforme'],
                                        'Anomalie' => ['#FEE2E2', '#991B1B', 'fa-exclamation-triangle', 'Anomalie - à corriger'],
                                        default    => ['#F3F4F6', '#374151', 'fa-circle',               $hs->statut_hs],
                                    };
                                @endphp
                                <tr class="ligne-row" style="border-bottom:1px solid #F3F4F6;">
                                    <td class="px-4 py-3 border-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#7C3AED,#A78BFA);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $initiales }}</div>
                                            <div>
                                                <div style="font-weight:600;color:var(--theme-text);">{{ $agent?->nom_complet ?? '-' }}</div>
                                                <div style="font-size:10px;color:#9CA3AF;">{{ $agent?->matricule ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 border-0">
                                        <div style="font-weight:500;color:var(--theme-text);">{{ $ligne?->typePoste?->libelle ?? '-' }}</div>
                                        <div style="font-size:11px;color:#9CA3AF;">
                                            {{ $ligne?->date_poste?->format('d/m/Y') }}
                                            @if($ligne?->heure_debut && $ligne?->heure_fin)
                                                · {{ \Carbon\Carbon::parse($ligne->heure_debut)->format('H:i') }}→{{ \Carbon\Carbon::parse($ligne->heure_fin)->format('H:i') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 border-0">
                                        <span style="font-size:16px;font-weight:700;color:#7C3AED;">{{ number_format($hs->nb_heures, 1) }}</span>
                                        <span style="font-size:11px;color:#9CA3AF;">h</span>
                                    </td>
                                    <td class="py-3 border-0">
                                        <span style="background:#F3F4F6;color:#374151;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">{{ $hs->periode }}</span>
                                    </td>
                                    <td class="py-3 border-0" style="font-size:12px;color:#6B7280;">
                                        {{ $hs->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 border-0">
                                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $hsBg }};color:{{ $hsColor }};">
                                            <i class="fas {{ $hsIcon }}" style="font-size:9px;"></i>
                                            {{ $hsLabel }}
                                        </span>
                                        @if($hs->statut_hs === 'Anomalie' && $hs->note_verification)
                                            <div style="font-size:10px;color:#991B1B;margin-top:4px;max-width:180px;background:#FEF2F2;border-radius:6px;padding:4px 8px;border:1px solid #FECACA;">
                                                <i class="fas fa-comment-alt me-1"></i><strong>RH :</strong> {{ $hs->note_verification }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 border-0">
                                        @if(in_array($hs->statut_hs, ['Déclaré', 'Anomalie']))
                                            <form action="{{ route('major.heures-sup.destroy', $hs->id_hsup) }}" method="POST"
                                                  onsubmit="return confirm('{{ $hs->statut_hs === 'Anomalie' ? 'Supprimer cette déclaration pour la corriger ?' : 'Supprimer cette déclaration ?' }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:600;border:1px solid #FECACA;background:#FEF2F2;color:#991B1B;cursor:pointer;"
                                                    title="{{ $hs->statut_hs === 'Anomalie' ? 'Supprimer et soumettre une déclaration corrigée' : 'Supprimer la déclaration' }}">
                                                    <i class="fas fa-trash" style="font-size:9px;"></i>
                                                    {{ $hs->statut_hs === 'Anomalie' ? 'Corriger' : 'Supprimer' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted border-0">
                                        <i class="fas fa-clock fa-2x mb-3 d-block" style="color:#D1D5DB;"></i>
                                        <p class="mb-0">Aucune déclaration pour le moment.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     MODAL - Déclarer heures supplémentaires
     ══════════════════════════════════════ --}}
<div class="modal fade" id="modal-declarer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background:#F5F3FF;">
                <div>
                    <h6 class="modal-title fw-bold mb-0" style="color:#7C3AED;">
                        <i class="fas fa-clock me-2"></i>Déclarer des heures supplémentaires
                    </h6>
                    <p class="mb-0 mt-1" id="modal-subtitle" style="font-size:12px;color:#6B7280;"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('major.heures-sup.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_ligne" id="input-id-ligne">
                <div class="modal-body px-4 py-4">

                    {{-- Récapitulatif du poste --}}
                    <div class="p-3 rounded mb-3" style="background:#F5F3FF;border:1px solid #DDD6FE;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#7C3AED;margin-bottom:6px;">Poste concerné</div>
                        <div id="recap-agent"  style="font-size:13px;font-weight:600;color:#1F2937;"></div>
                        <div id="recap-poste"  style="font-size:12px;color:#6B7280;"></div>
                        <div id="recap-date"   style="font-size:12px;color:#6B7280;"></div>
                    </div>

                    {{-- Dépassement calculé --}}
                    <div class="p-3 rounded mb-3" id="bloc-depassement" style="display:none;background:#FFFBEB;border:1px solid #FDE68A;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#D97706;margin-bottom:4px;">
                            <i class="fas fa-calculator me-1"></i>Dépassement détecté automatiquement
                        </div>
                        <div id="depassement-txt" style="font-size:13px;color:#92400E;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:13px;color:#374151;">
                            Heures supplémentaires à déclarer <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="nb_heures" id="input-nb-heures"
                                class="form-control" step="0.5" min="0.5" max="24"
                                placeholder="Ex : 4.0" required style="font-size:13px;">
                            <span class="input-group-text" style="font-size:13px;background:#F3F4F6;">heures</span>
                        </div>
                        <div class="form-text" style="font-size:11px;">
                            Saisir les heures réellement travaillées <strong>au-delà</strong> des 8h réglementaires.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:13px;color:#374151;">
                            Période de référence <span class="text-danger">*</span>
                        </label>
                        <select name="periode" class="form-select" required style="font-size:13px;">
                            <option value="">- Sélectionner -</option>
                            <option value="Trimestre">Trimestre</option>
                            <option value="Semestre">Semestre</option>
                        </select>
                    </div>

                    <div class="p-3 rounded" style="background:#EDE9FE;border:1px solid #C4B5FD;">
                        <p class="mb-0" style="font-size:11.5px;color:#5B21B6;">
                            <i class="fas fa-info-circle me-1"></i>
                            Le <strong>taux de majoration</strong> (25% standard) et le <strong>montant</strong>
                            seront calculés par la RH lors de la validation financière.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm px-4" style="background:#7C3AED;color:white;border:none;">
                        <i class="fas fa-save me-1"></i>Enregistrer la déclaration
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
// ── Tabs ──────────────────────────────────────────────────────────
function switchTab(tab) {
    document.getElementById('panel-lignes').style.display      = tab === 'lignes'      ? '' : 'none';
    document.getElementById('panel-declarations').style.display = tab === 'declarations' ? '' : 'none';
    document.getElementById('tab-lignes-btn').classList.toggle('active',      tab === 'lignes');
    document.getElementById('tab-declarations-btn').classList.toggle('active', tab === 'declarations');
}

// ── Modal déclarer ────────────────────────────────────────────────
function openDeclareModal(idLigne, agentNom, posteLibelle, datePoste, depassement) {
    document.getElementById('input-id-ligne').value    = idLigne;
    document.getElementById('recap-agent').textContent = agentNom;
    document.getElementById('recap-poste').textContent = 'Poste : ' + posteLibelle;
    document.getElementById('recap-date').textContent  = 'Date : ' + datePoste;

    const inputH = document.getElementById('input-nb-heures');
    if (depassement > 0) {
        document.getElementById('bloc-depassement').style.display = '';
        document.getElementById('depassement-txt').textContent =
            'Durée planifiée - 8h standard = ' + depassement.toFixed(1) + 'h de dépassement détecté.';
        inputH.value = depassement.toFixed(1);
    } else {
        document.getElementById('bloc-depassement').style.display = 'none';
        inputH.value = '';
    }

    document.getElementById('modal-subtitle').textContent = agentNom + ' · ' + datePoste;
    new bootstrap.Modal(document.getElementById('modal-declarer')).show();
}

// ── Toasts ────────────────────────────────────────────────────────
function showToast(msg, type) {
    const el = document.createElement('div');
    el.className = 'toast-msg toast-' + type;
    el.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i>' + msg;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), 4500);
}

@if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
@if(session('error'))   showToast('{{ session('error') }}', 'error');   @endif

@if($errors->any())
    new bootstrap.Modal(document.getElementById('modal-declarer')).show();
@endif

// Restaurer le bon tab après rechargement
@if(request('_tab') === 'declarations')
    switchTab('declarations');
@endif
</script>
@endpush

@endsection
