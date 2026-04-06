@extends('layouts.master')

@section('title', 'Planning - ' . $planning->periode_debut->isoFormat('D MMM') . ' au ' . $planning->periode_fin->isoFormat('D MMM YYYY'))
@section('page-title', 'Détail du Planning')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Major</a></li>
    <li><a href="{{ route('major.planning.index') }}" style="color:#1565C0;">Plannings</a></li>
    <li>{{ $planning->periode_debut->format('d/m/Y') }} → {{ $planning->periode_fin->format('d/m/Y') }}</li>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<style>
.panel { background:white;border-radius:12px;padding:20px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB;color:#111827; }
.tab-btn { padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;border:1px solid #E5E7EB;background:white;color:#6B7280;cursor:pointer;transition:all 180ms;display:inline-flex;align-items:center;gap:6px; }
.tab-btn.active { background:#0A4D8C;color:white;border-color:#0A4D8C; }
.tab-btn:hover:not(.active) { background:#F9FAFB; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;color:#9CA3AF; }
.fc .fc-toolbar-title { font-size:15px !important;font-weight:600;color:#111827; }
.fc .fc-button { border-radius:7px !important;font-size:12px !important;font-weight:500 !important;padding:5px 12px !important; }
.fc .fc-button-primary { background:#0A4D8C !important;border-color:#0A4D8C !important; }
.fc .fc-button-primary:hover { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-daygrid-event { border-radius:5px;font-size:11px;padding:1px 5px;font-weight:500; }
.fc .fc-day-today { background:rgba(10,77,140,.03) !important; }
.fc .fc-day-today .fc-daygrid-day-number { color:#0A4D8C;font-weight:700; }
.ligne-row { border-bottom:1px solid #F3F4F6;transition:background 150ms; }
.ligne-row:hover { background:#FAFBFF; }
.ligne-row:last-child { border-bottom:none; }
.badge-poste { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.delete-ligne-btn { opacity:0;transition:opacity 150ms;background:none;border:none;color:#DC2626;cursor:pointer;padding:4px 8px;border-radius:6px; }
.ligne-row:hover .delete-ligne-btn { opacity:1; }
#eventPopover { display:none;position:fixed;z-index:9999;background:white;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.15);padding:14px 18px;min-width:230px;pointer-events:none;border:1px solid #E5E7EB; }
.legend-dot { width:10px;height:10px;border-radius:2px;flex-shrink:0; }
.stat-chip { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:500; }
[data-theme="dark"] .panel { background:#161b22;border-color:#30363d; }
[data-theme="dark"] .ligne-row:hover { background:#1c2128; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-dismissible d-flex align-items-center gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #10B981;background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-dismissible d-flex align-items-center gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #EF4444;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;">
            <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-dismissible d-flex align-items-start gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #EF4444;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;">
            <i class="fas fa-exclamation-circle mt-1"></i>
            <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- En-tête Planning --}}
    @php
        $sc = match($planning->statut_planning) {
            'Brouillon' => ['bg'=>'#F3F4F6','c'=>'#6B7280','ic'=>'fa-pencil-alt'],
            'Transmis'  => ['bg'=>'#FFFBEB','c'=>'#D97706','ic'=>'fa-paper-plane'],
            'Validé'    => ['bg'=>'#ECFDF5','c'=>'#059669','ic'=>'fa-check-double'],
            'Rejeté'    => ['bg'=>'#FEF2F2','c'=>'#DC2626','ic'=>'fa-times-circle'],
            default     => ['bg'=>'#F3F4F6','c'=>'#6B7280','ic'=>'fa-circle'],
        };
    @endphp

    <div class="panel mb-4">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold mb-0" style="color:#111827;">
                        Planning du {{ $planning->periode_debut->isoFormat('D MMMM') }}
                        au {{ $planning->periode_fin->isoFormat('D MMMM YYYY') }}
                    </h5>
                    <span style="padding:4px 12px;border-radius:20px;background:{{ $sc['bg'] }};color:{{ $sc['c'] }};font-size:12px;font-weight:600;white-space:nowrap;">
                        <i class="fas {{ $sc['ic'] }} me-1" style="font-size:10px;"></i>{{ $planning->statut_planning }}
                    </span>
                </div>
                <div style="font-size:13px;color:#6B7280;">
                    {{ $service->nom_service }} ·
                    {{ $planning->duree_jours }} jour(s) ·
                    {{ $planning->lignes->count() }} ligne(s)
                </div>
                {{-- Stats postes --}}
                @if($statsPoste->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach($statsPoste as $poste => $nb)
                            @php
                                $col = match($poste) {
                                    'Jour'       => ['bg'=>'#EFF6FF','c'=>'#1D4ED8'],
                                    'Nuit'       => ['bg'=>'#EEF2FF','c'=>'#4338CA'],
                                    'Garde'      => ['bg'=>'#FFFBEB','c'=>'#B45309'],
                                    'Repos'      => ['bg'=>'#F3F4F6','c'=>'#6B7280'],
                                    'Astreinte'  => ['bg'=>'#F5F3FF','c'=>'#6D28D9'],
                                    'Permanence' => ['bg'=>'#ECFDF5','c'=>'#065F46'],
                                    default      => ['bg'=>'#F3F4F6','c'=>'#374151'],
                                };
                            @endphp
                            <span class="stat-chip" style="background:{{ $col['bg'] }};color:{{ $col['c'] }};">
                                {{ $poste }} <strong>{{ $nb }}</strong>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('major.planning.index') }}" class="action-btn action-btn-outline">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
                @if($planning->est_modifiable)
                    <button type="button" class="action-btn action-btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalAjouterLigne">
                        <i class="fas fa-plus-circle"></i>Ajouter une ligne
                    </button>
                    <button type="button" class="action-btn action-btn-primary"
                        onclick="openModalTransmettre(
                            {{ $planning->id_planning }},
                            '{{ $planning->periode_debut->format('d/m/Y') }}',
                            '{{ $planning->periode_fin->format('d/m/Y') }}',
                            {{ $planning->lignes->count() }}
                        )">
                        <i class="fas fa-paper-plane"></i>Transmettre au Manager
                    </button>
                @endif
                @if($planning->est_brouillon)
                    <button type="button" class="action-btn" style="background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;"
                        onclick="openModalSupprimer({{ $planning->id_planning }})">
                        <i class="fas fa-trash-alt"></i>Supprimer
                    </button>
                @endif
            </div>
        </div>

        @if($planning->statut_planning === 'Rejeté' && $planning->motif_rejet)
            <div class="mt-3" style="background:#FEF2F2;border-left:4px solid #DC2626;border-radius:8px;padding:12px 16px;font-size:13px;color:#991B1B;">
                <div class="fw-600 mb-1"><i class="fas fa-exclamation-circle me-2"></i>Motif de rejet</div>
                {{ $planning->motif_rejet }}
            </div>
        @endif

        @if($planning->statut_planning === 'Transmis')
            <div class="mt-3" style="background:#FFFBEB;border-left:4px solid #F59E0B;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400E;">
                <i class="fas fa-clock me-2"></i>Planning transmis - en attente de validation par le Manager.
            </div>
        @endif

        @if($planning->statut_planning === 'Validé')
            <div class="mt-3" style="background:#ECFDF5;border-left:4px solid #10B981;border-radius:8px;padding:12px 16px;font-size:13px;color:#065F46;">
                <i class="fas fa-check-double me-2"></i>Planning validé par le Manager. En attente de transmission au service RH.
            </div>
        @endif

        @if($planning->statut_planning === 'Diffusé')
            <div class="mt-3" style="background:#EFF6FF;border-left:4px solid #1D4ED8;border-radius:8px;padding:12px 16px;font-size:13px;color:#1E40AF;">
                <i class="fas fa-share-square me-2"></i>Planning validé et transmis au service RH - opérationnel.
            </div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <button class="tab-btn active" id="btnTableau" onclick="switchTab('tableau')">
            <i class="fas fa-table"></i>Tableau des postes
        </button>
        <button class="tab-btn" id="btnCalendar" onclick="switchTab('calendar')">
            <i class="fas fa-calendar-alt"></i>Calendrier
        </button>
    </div>

    {{-- Tab : Tableau par date --}}
    <div id="tabTableau">
        <div class="panel">
            @if($lignesParDate->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-2x mb-3" style="color:#D1D5DB;"></i>
                    <p class="text-muted mb-0" style="font-size:13px;">Aucune ligne dans ce planning. Ajoutez des postes.</p>
                </div>
            @else
                @foreach($lignesParDate as $date => $lignes)
                    @php
                        $carbon = \Carbon\Carbon::parse($date);
                        $isWeekend = $carbon->isWeekend();
                    @endphp
                    <div class="mb-4">
                        <div class="section-title d-flex align-items-center gap-2">
                            {{ $carbon->isoFormat('dddd D MMMM YYYY') }}
                            @if($isWeekend)
                                <span style="font-size:10px;padding:2px 8px;border-radius:20px;background:#FEF3C7;color:#B45309;font-weight:600;">WE</span>
                            @endif
                            <span class="ms-auto" style="font-size:10px;">{{ $lignes->count() }} poste(s)</span>
                        </div>
                        <div style="border-radius:8px;overflow:hidden;border:1px solid #F3F4F6;">
                            @foreach($lignes as $ligne)
                                @php
                                    $posteLib = $ligne->typePoste->libelle ?? 'Autre';
                                    $badgeStyle = match($posteLib) {
                                        'Jour'       => 'background:#EFF6FF;color:#1D4ED8;',
                                        'Nuit'       => 'background:#EEF2FF;color:#4338CA;',
                                        'Garde'      => 'background:#FFFBEB;color:#B45309;',
                                        'Repos'      => 'background:#F3F4F6;color:#6B7280;',
                                        'Astreinte'  => 'background:#F5F3FF;color:#6D28D9;',
                                        'Permanence' => 'background:#ECFDF5;color:#065F46;',
                                        default      => 'background:#F3F4F6;color:#374151;',
                                    };
                                @endphp
                                <div class="ligne-row d-flex align-items-center px-4 py-2 gap-3">
                                    {{-- Avatar --}}
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:32px;height:32px;background:#E8F0FE;font-size:12px;font-weight:700;color:#0A4D8C;">
                                        {{ strtoupper(substr($ligne->agent->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($ligne->agent->nom ?? '', 0, 1)) }}
                                    </div>
                                    {{-- Nom --}}
                                    <div class="flex-grow-1" style="min-width:0;">
                                        <div class="fw-600" style="font-size:13px;color:#111827;">
                                            {{ $ligne->agent->nom_complet ?? '-' }}
                                        </div>
                                        <div style="font-size:11px;color:#9CA3AF;">
                                            {{ $ligne->agent->famille_d_emploi ?? $ligne->agent->fonction ?? '' }}
                                        </div>
                                    </div>
                                    {{-- Type poste --}}
                                    <span class="badge-poste" style="{{ $badgeStyle }}">{{ $posteLib }}</span>
                                    {{-- Horaires --}}
                                    <div style="font-size:12px;color:#6B7280;white-space:nowrap;">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ substr($ligne->heure_debut, 0, 5) }} → {{ substr($ligne->heure_fin, 0, 5) }}
                                    </div>
                                    {{-- Bouton supprimer --}}
                                    @if($planning->est_modifiable)
                                        <form method="POST" action="{{ route('major.planning.lignes.destroy', [$planning->id_planning, $ligne->id_ligne]) }}"
                                              onsubmit="return confirm('Supprimer cette ligne ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="delete-ligne-btn" title="Supprimer">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Tab : Calendrier --}}
    <div id="tabCalendar" style="display:none;">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                <div style="font-size:12px;color:#9CA3AF;">Vue calendrier du planning</div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @foreach([['Jour','#3B82F6'],['Nuit','#4F46E5'],['Garde','#F59E0B'],['Repos','#9CA3AF'],['Astreinte','#8B5CF6'],['Permanence','#0D9488']] as [$lib, $col])
                    <div class="d-flex align-items-center gap-1" style="font-size:11px;font-weight:500;color:#6B7280;">
                        <div class="legend-dot" style="background:{{ $col }};"></div>{{ $lib }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="calendarDetail" style="min-height:400px;"></div>
        </div>
    </div>


</div>{{-- /container --}}

{{-- Modal : Ajouter une ligne --}}
@if($planning->est_modifiable)
<div class="modal fade" id="modalAjouterLigne" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form method="POST" action="{{ route('major.planning.lignes.store', $planning->id_planning) }}">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-plus-circle" style="color:#0A4D8C;font-size:18px;"></i>
                        <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Ajouter un poste au planning</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Agent <span class="text-danger">*</span></label>
                            <select name="id_agent" class="form-select" required style="border-radius:8px;font-size:13px;">
                                <option value="">- Choisir un agent -</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id_agent }}">{{ $agent->prenom }} {{ $agent->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Type de poste <span class="text-danger">*</span></label>
                            <select name="id_typeposte" class="form-select" required style="border-radius:8px;font-size:13px;">
                                <option value="">- Type -</option>
                                @foreach($typesPoste as $type)
                                    <option value="{{ $type->id_typeposte }}">{{ $type->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:13px;">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_poste" class="form-control" required
                                   min="{{ $planning->periode_debut->format('Y-m-d') }}"
                                   max="{{ $planning->periode_fin->format('Y-m-d') }}"
                                   style="border-radius:8px;font-size:13px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:13px;">Heure début <span class="text-danger">*</span></label>
                            <input type="time" name="heure_debut" class="form-control" required value="08:00"
                                   style="border-radius:8px;font-size:13px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600" style="font-size:13px;">Heure fin <span class="text-danger">*</span></label>
                            <input type="time" name="heure_fin" class="form-control" required value="16:00"
                                   style="border-radius:8px;font-size:13px;">
                        </div>
                    </div>
                    <div class="mt-3" style="background:#EFF6FF;border-radius:8px;padding:10px 14px;font-size:12px;color:#1E40AF;">
                        <i class="fas fa-info-circle me-1"></i>
                        La date doit être comprise entre le <strong>{{ $planning->periode_debut->format('d/m/Y') }}</strong>
                        et le <strong>{{ $planning->periode_fin->format('d/m/Y') }}</strong>.
                        Seuls les agents de votre service sont disponibles.
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-plus"></i>Ajouter le poste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal : Confirmer Transmission --}}
<div class="modal fade" id="modalTransmettre" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="formTransmettre" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-paper-plane" style="color:#D97706;font-size:18px;"></i>
                        <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Transmettre au Manager ?</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div id="transmettreInfo" class="mb-3" style="font-size:13px;color:#374151;background:#F9FAFB;border-radius:8px;padding:10px 14px;"></div>
                    <div style="background:#FFFBEB;border-left:3px solid #F59E0B;border-radius:6px;padding:10px 12px;font-size:12px;color:#92400E;">
                        <i class="fas fa-info-circle me-1"></i>
                        Une fois transmis, le planning ne peut plus être modifié.
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-paper-plane"></i>Transmettre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Confirmer Suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="formSupprimer" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-trash-alt" style="color:#DC2626;font-size:18px;"></i>
                        <h5 class="modal-title fw-bold mb-0" style="color:#DC2626;">Supprimer ce planning ?</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:12px 24px 16px;">
                    <p class="text-muted mb-0" style="font-size:13px;">Cette action est irréversible. Toutes les lignes seront supprimées.</p>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;background:#DC2626;color:white;border:none;cursor:pointer;">
                        <i class="fas fa-trash-alt"></i>Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="eventPopover"><div id="popoverContent"></div></div>

@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
const calendarEvents = @json($calendarEvents);
let calendar;

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('calendarDetail');
    calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        initialDate: '{{ $planning->periode_debut->format('Y-m-d') }}',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', list: 'Liste' },
        events: calendarEvents,
        eventDidMount: function(info) {
            const props = info.event.extendedProps;
            info.el.title = `${props.agent} · ${props.typePoste} · ${props.heureDebut}→${props.heureFin}`;
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const pop   = document.getElementById('eventPopover');
            const cont  = document.getElementById('popoverContent');
            cont.innerHTML = `
                <div style="font-weight:600;color:#111827;font-size:13px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">${info.event.title}</div>
                <div style="font-size:12px;color:#6B7280;line-height:1.9;">
                    <div><i class="fas fa-user me-2" style="width:14px;color:#9CA3AF;"></i>${props.agent}</div>
                    <div><i class="fas fa-clock me-2" style="width:14px;color:#9CA3AF;"></i>${props.heureDebut} → ${props.heureFin}</div>
                    <div><i class="fas fa-tag me-2" style="width:14px;color:#9CA3AF;"></i>${props.typePoste}</div>
                </div>
            `;
            const x = Math.min(info.jsEvent.clientX + 12, window.innerWidth - 260);
            const y = Math.min(info.jsEvent.clientY - 10, window.innerHeight - 160);
            pop.style.left  = x + 'px';
            pop.style.top   = y + 'px';
            pop.style.display = 'block';
            setTimeout(() => pop.style.display = 'none', 5000);
        },
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#eventPopover') && !e.target.closest('.fc-event')) {
            document.getElementById('eventPopover').style.display = 'none';
        }
    });
});

function switchTab(tab) {
    ['tableau','calendar'].forEach(function(t) {
        const el = document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1));
        const btn = document.getElementById('btn' + t.charAt(0).toUpperCase() + t.slice(1));
        if (el) el.style.display = (t === tab) ? '' : 'none';
        if (btn) btn.className = 'tab-btn' + (t === tab ? ' active' : '');
    });
    if (tab === 'calendar' && calendar) {
        setTimeout(() => calendar.render(), 50);
    }
}

function openModalTransmettre(id, debut, fin, nbLignes) {
    document.getElementById('formTransmettre').action = `/major/planning/${id}/transmettre`;
    document.getElementById('transmettreInfo').innerHTML =
        `<i class="fas fa-calendar-week me-2" style="color:#0A4D8C;"></i>
         Planning du <strong>${debut}</strong> au <strong>${fin}</strong> &nbsp;·&nbsp; <strong>${nbLignes}</strong> ligne(s)`;
    new bootstrap.Modal(document.getElementById('modalTransmettre')).show();
}

function openModalSupprimer(id) {
    document.getElementById('formSupprimer').action = `/major/planning/${id}`;
    new bootstrap.Modal(document.getElementById('modalSupprimer')).show();
}
</script>
@endpush
