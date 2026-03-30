@extends('layouts.master')

@section('title', 'Plannings — ' . $service->nom_service)
@section('page-title', 'Gestion des Plannings')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li>Plannings</li>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<style>
/* ── KPI Cards ─────────────────────────────────── */
.kpi-card {
    border-radius: 12px; padding: 18px 20px;
    transition: box-shadow 200ms, transform 200ms;
}
.kpi-card:hover { box-shadow: 0 6px 20px rgba(10,77,140,0.10); transform: translateY(-2px); }
.kpi-icon { width:40px;height:40px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;margin-bottom:10px; }
.kpi-value { font-size:26px;font-weight:700;line-height:1.1; }
.kpi-label { font-size:12px;font-weight:500;margin-top:3px;color:#6B7280; }

/* ── Tabs ──────────────────────────────────────── */
.tab-btn {
    padding:8px 18px; border-radius:8px; font-size:13px; font-weight:500;
    border:1px solid #E5E7EB; background:white; color:#6B7280;
    cursor:pointer; transition:all 180ms; text-decoration:none;
    display:inline-flex; align-items:center; gap:6px;
}
.tab-btn.active { background:#0A4D8C;color:white;border-color:#0A4D8C; }
.tab-btn:hover:not(.active) { background:#F3F4F6; color:#374151; }

/* ── Panel ─────────────────────────────────────── */
.panel { background:white; border-radius:12px; padding:20px; border:1px solid #F3F4F6; box-shadow:0 1px 4px rgba(0,0,0,.04); }

/* ── Action btns ───────────────────────────────── */
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB;color:#111827; }

/* ── Planning cards ────────────────────────────── */
.planning-card { background:white;border-radius:14px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04);transition:box-shadow 200ms,transform 200ms; padding:20px; }
.planning-card:hover { box-shadow:0 4px 16px rgba(10,77,140,.08);transform:translateY(-1px); }

/* ── Calendar ──────────────────────────────────── */
.fc .fc-toolbar-title { font-size:15px !important;font-weight:600;color:#111827; }
.fc .fc-button { border-radius:7px !important;font-size:12px !important;font-weight:500 !important;padding:5px 12px !important; }
.fc .fc-button-primary { background:#0A4D8C !important;border-color:#0A4D8C !important; }
.fc .fc-button-primary:hover { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-daygrid-event { border-radius:5px;font-size:11px;padding:1px 5px;font-weight:500; }
.fc .fc-event-title { font-weight:500; }
.fc .fc-col-header-cell-cushion { font-size:12px;font-weight:600;color:#6B7280; }
.fc .fc-daygrid-day-number { font-size:12px;color:#374151;font-weight:500; }
.fc .fc-day-today .fc-daygrid-day-number { color:#0A4D8C;font-weight:700; }
.fc .fc-day-today { background:rgba(10,77,140,0.03) !important; }

/* ── Section title ─────────────────────────────── */
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;padding-bottom:6px;color:#9CA3AF; }

/* ── Event Popover ─────────────────────────────── */
#eventPopover {
    display:none; position:fixed; z-index:9999; background:white;
    border-radius:10px; box-shadow:0 10px 40px rgba(0,0,0,0.15);
    padding:14px 18px; min-width:230px; pointer-events:none;
    border:1px solid #E5E7EB;
}

/* ── Legend ─────────────────────────────────────── */
.legend-dot { width:10px;height:10px;border-radius:2px;flex-shrink:0; }

/* ── Dark mode ──────────────────────────────────── */
[data-theme="dark"] .panel { background:#161b22;border-color:#30363d; }
[data-theme="dark"] .planning-card { background:#161b22;border-color:#30363d; }
[data-theme="dark"] .tab-btn { background:#21262d;color:#c9d1d9;border-color:#30363d; }
[data-theme="dark"] .tab-btn.active { background:#0A4D8C;color:white;border-color:#0A4D8C; }
[data-theme="dark"] #eventPopover { background:#161b22;border-color:#30363d; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── Alertes ─────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-dismissible d-flex align-items-center gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #10B981;background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-dismissible d-flex align-items-center gap-2 mb-4"
             style="border-radius:10px;border-left:4px solid #EF4444;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── En-tête ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Plannings — {{ $service->nom_service }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                {{ now()->isoFormat('dddd D MMMM YYYY') }} ·
                <span style="color:#0A4D8C;font-weight:500;">{{ $agents->count() }} agent(s) actif(s)</span>
            </p>
        </div>
        <button class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelPlanning">
            <i class="fas fa-calendar-plus"></i>Nouveau planning
        </button>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────── --}}
    <div class="section-title">Tableau de bord plannings</div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card" style="background:#F9FAFB;border:1px solid #E5E7EB;">
                <div class="kpi-icon" style="background:#F3F4F6;"><i class="fas fa-pencil-alt" style="color:#6B7280;"></i></div>
                <div class="kpi-value" style="color:#6B7280;">{{ $stats['brouillons'] }}</div>
                <div class="kpi-label">Brouillons</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card" style="background:#FFFBEB;border:1px solid #FDE68A;">
                <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-paper-plane" style="color:#D97706;"></i></div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['transmis'] }}</div>
                <div class="kpi-label">En attente RH</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card" style="background:#ECFDF5;border:1px solid #A7F3D0;">
                <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-double" style="color:#059669;"></i></div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['valides'] }}</div>
                <div class="kpi-label">Validés</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card" style="background:#FEF2F2;border:1px solid #FECACA;">
                <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-times-circle" style="color:#DC2626;"></i></div>
                <div class="kpi-value" style="color:#DC2626;">{{ $stats['rejetes'] }}</div>
                <div class="kpi-label">Rejetés</div>
            </div>
        </div>
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <button class="tab-btn active" id="btnCalendar" onclick="switchTab('calendar')">
            <i class="fas fa-calendar-alt"></i>Calendrier
        </button>
        <button class="tab-btn" id="btnListe" onclick="switchTab('liste')">
            <i class="fas fa-th-list"></i>Liste des plannings
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Tab : Calendrier                                           --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="tabCalendar">
        <div class="panel">
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <div class="fw-600" style="color:#111827;font-size:14px;">Calendrier des postes — {{ $service->nom_service }}</div>
                    <div style="font-size:12px;color:#9CA3AF;">Visualisation de tous les postes planifiés</div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @foreach([
                        ['Jour','#3B82F6'], ['Nuit','#4F46E5'], ['Garde','#F59E0B'],
                        ['Repos','#9CA3AF'], ['Astreinte','#8B5CF6'], ['Permanence','#0D9488']
                    ] as [$lib, $col])
                    <div class="d-flex align-items-center gap-1" style="font-size:11px;font-weight:500;color:#6B7280;">
                        <div class="legend-dot" style="background:{{ $col }};"></div>{{ $lib }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="calendarMain" style="min-height:480px;"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Tab : Liste                                                --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="tabListe" style="display:none;">
        @if($plannings->isEmpty())
            <div class="panel text-center py-5">
                <div style="width:72px;height:72px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-calendar-times fa-2x" style="color:#D1D5DB;"></i>
                </div>
                <h6 class="fw-bold mb-1" style="color:#111827;">Aucun planning créé</h6>
                <p class="text-muted mb-3" style="font-size:13px;">Créez votre premier planning mensuel pour organiser les postes de votre équipe.</p>
                <button class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelPlanning">
                    <i class="fas fa-plus"></i>Créer un planning
                </button>
            </div>
        @else
            <div class="row g-3">
                @foreach($plannings as $planning)
                    @php
                        $sc = match($planning->statut_planning) {
                            'Brouillon' => ['bg'=>'#F3F4F6','c'=>'#6B7280','ic'=>'fa-pencil-alt'],
                            'Transmis'  => ['bg'=>'#FFFBEB','c'=>'#D97706','ic'=>'fa-paper-plane'],
                            'Validé'    => ['bg'=>'#ECFDF5','c'=>'#059669','ic'=>'fa-check-double'],
                            'Rejeté'    => ['bg'=>'#FEF2F2','c'=>'#DC2626','ic'=>'fa-times-circle'],
                            default     => ['bg'=>'#F3F4F6','c'=>'#6B7280','ic'=>'fa-circle'],
                        };
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="planning-card">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <div class="fw-bold mb-1" style="color:#111827;font-size:14px;">
                                        <i class="fas fa-calendar-week me-1" style="color:#0A4D8C;font-size:12px;"></i>
                                        {{ $planning->periode_debut->isoFormat('D MMM') }} — {{ $planning->periode_fin->isoFormat('D MMM YYYY') }}
                                    </div>
                                    <div style="font-size:12px;color:#9CA3AF;">
                                        {{ $planning->duree_jours }} jour(s) ·
                                        {{ $planning->lignes_count }} ligne(s)
                                    </div>
                                </div>
                                <span style="padding:3px 10px;border-radius:20px;background:{{ $sc['bg'] }};color:{{ $sc['c'] }};font-size:11px;font-weight:600;white-space:nowrap;flex-shrink:0;">
                                    <i class="fas {{ $sc['ic'] }} me-1" style="font-size:9px;"></i>{{ $planning->statut_planning }}
                                </span>
                            </div>

                            @if($planning->statut_planning === 'Rejeté' && $planning->motif_rejet)
                                <div style="background:#FEF2F2;border-left:3px solid #DC2626;border-radius:6px;padding:8px 12px;margin-bottom:12px;font-size:12px;color:#991B1B;">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ Str::limit($planning->motif_rejet, 90) }}
                                </div>
                            @endif

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('manager.planning.show', $planning->id_planning) }}"
                                   class="action-btn action-btn-outline" style="font-size:12px;padding:6px 12px;">
                                    <i class="fas fa-eye"></i>Voir
                                </a>
                                @if($planning->est_modifiable)
                                    <button type="button" class="action-btn action-btn-primary" style="font-size:12px;padding:6px 12px;"
                                        onclick="openModalTransmettre(
                                            {{ $planning->id_planning }},
                                            '{{ $planning->periode_debut->format('d/m/Y') }}',
                                            '{{ $planning->periode_fin->format('d/m/Y') }}',
                                            {{ $planning->lignes_count }}
                                        )">
                                        <i class="fas fa-paper-plane"></i>Transmettre
                                    </button>
                                @endif
                                @if($planning->est_brouillon)
                                    <button type="button"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;cursor:pointer;transition:all 180ms;"
                                        onclick="openModalSupprimer({{ $planning->id_planning }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($plannings->hasPages())
                <div class="mt-4">{{ $plannings->links() }}</div>
            @endif
        @endif
    </div>

</div>{{-- /container --}}

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                              --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal : Nouveau Planning ──────────────────────────────────────── --}}
<div class="modal fade" id="modalNouvelPlanning" tabindex="-1" aria-labelledby="labelNouvelPlanning" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form action="{{ route('manager.planning.store') }}" method="POST" id="formCreerPlanning">
                @csrf
                {{-- Header --}}
                <div class="modal-header border-0" style="padding:24px 24px 12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;border-radius:10px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-calendar-plus" style="color:#0A4D8C;font-size:17px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="labelNouvelPlanning" style="color:#111827;">Nouveau Planning</h5>
                            <p class="text-muted mb-0" style="font-size:12px;">Service : {{ $service->nom_service }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body" style="padding:16px 24px;">
                    <p class="text-muted mb-4" style="font-size:13px;">
                        Définissez la période couverte par ce planning. Vous pourrez ensuite ajouter les lignes (postes) individuellement.
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-600" style="font-size:13px;">Début de période <span class="text-danger">*</span></label>
                            <input type="date" name="periode_debut" id="np_debut" class="form-control" required
                                   style="border-radius:8px;font-size:13px;" onchange="calcNpDuree()">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-600" style="font-size:13px;">Fin de période <span class="text-danger">*</span></label>
                            <input type="date" name="periode_fin" id="np_fin" class="form-control" required
                                   style="border-radius:8px;font-size:13px;" onchange="calcNpDuree()">
                        </div>
                    </div>

                    <div id="npDureeInfo" class="mt-3" style="display:none;background:#EFF6FF;border-radius:8px;padding:10px 14px;">
                        <div style="font-size:12px;color:#1E40AF;font-weight:500;">
                            <i class="fas fa-info-circle me-1"></i>
                            Durée calculée : <strong id="npDureeJours"></strong> jours
                        </div>
                    </div>

                    @if($agents->isEmpty())
                        <div class="alert alert-warning mt-3 mb-0" style="border-radius:8px;font-size:12px;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Attention :</strong> Aucun agent actif dans votre service. Contactez la RH avant de créer un planning.
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0" style="padding:8px 24px 24px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save"></i>Créer le planning
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal : Confirmer Transmission ──────────────────────────────── --}}
<div class="modal fade" id="modalTransmettre" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="formTransmettre" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-paper-plane" style="color:#D97706;font-size:18px;"></i>
                        <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Transmettre à la RH ?</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div id="transmettreInfo" class="mb-3" style="font-size:13px;color:#374151;background:#F9FAFB;border-radius:8px;padding:10px 14px;"></div>
                    <div style="background:#FFFBEB;border-left:3px solid #F59E0B;border-radius:6px;padding:10px 12px;font-size:12px;color:#92400E;">
                        <i class="fas fa-info-circle me-1"></i>
                        Une fois transmis, le planning ne peut plus être modifié tant que la RH ne l'a pas validé ou rejeté.
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

{{-- ── Modal : Confirmer Suppression ───────────────────────────────── --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form id="formSupprimer" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-trash-alt" style="color:#DC2626;font-size:18px;"></i>
                        <h5 class="modal-title fw-bold mb-0" style="color:#DC2626;">Supprimer ce planning ?</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:12px 24px 16px;">
                    <p class="text-muted mb-0" style="font-size:13px;">Cette action est irréversible. Toutes les lignes du planning seront définitivement supprimées.</p>
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

{{-- ── Event popover ───────────────────────────────────────────────── --}}
<div id="eventPopover">
    <div id="popoverContent"></div>
</div>

@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
const calendarEvents = @json($calendarEvents);
let calendar;

document.addEventListener('DOMContentLoaded', function () {
    // ── FullCalendar ────────────────────────────────────────────
    const el = document.getElementById('calendarMain');
    calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
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
                <div style="font-weight:600;color:#111827;font-size:13px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    ${info.event.title}
                </div>
                <div style="font-size:12px;color:#6B7280;line-height:1.9;">
                    <div><i class="fas fa-user me-2" style="width:14px;color:#9CA3AF;"></i>${props.agent}</div>
                    <div><i class="fas fa-clock me-2" style="width:14px;color:#9CA3AF;"></i>${props.heureDebut} → ${props.heureFin}</div>
                    <div><i class="fas fa-tag me-2" style="width:14px;color:#9CA3AF;"></i>${props.typePoste}</div>
                    <div><i class="fas fa-circle me-2" style="width:14px;color:#9CA3AF;"></i>${props.statut}</div>
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
    calendar.render();

    // Clic hors popover
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#eventPopover') && !e.target.closest('.fc-event')) {
            document.getElementById('eventPopover').style.display = 'none';
        }
    });
});

// ── Tabs ──────────────────────────────────────────────────────
function switchTab(tab) {
    document.getElementById('tabCalendar').style.display = tab === 'calendar' ? '' : 'none';
    document.getElementById('tabListe').style.display    = tab === 'liste'    ? '' : 'none';
    document.getElementById('btnCalendar').className     = 'tab-btn' + (tab === 'calendar' ? ' active' : '');
    document.getElementById('btnListe').className        = 'tab-btn' + (tab === 'liste'    ? ' active' : '');
    if (tab === 'calendar' && calendar) {
        setTimeout(() => calendar.updateSize(), 100);
    }
}

// ── Modal : Transmettre ───────────────────────────────────────
function openModalTransmettre(id, debut, fin, nbLignes) {
    document.getElementById('formTransmettre').action = `/manager/planning/${id}/transmettre`;
    document.getElementById('transmettreInfo').innerHTML =
        `<i class="fas fa-calendar-week me-2" style="color:#0A4D8C;"></i>
         Planning du <strong>${debut}</strong> au <strong>${fin}</strong> &nbsp;·&nbsp; <strong>${nbLignes}</strong> ligne(s)`;
    new bootstrap.Modal(document.getElementById('modalTransmettre')).show();
}

// ── Modal : Supprimer ─────────────────────────────────────────
function openModalSupprimer(id) {
    document.getElementById('formSupprimer').action = `/manager/planning/${id}`;
    new bootstrap.Modal(document.getElementById('modalSupprimer')).show();
}

// ── Calcul durée planning ──────────────────────────────────────
function calcNpDuree() {
    const d = document.getElementById('np_debut').value;
    const f = document.getElementById('np_fin').value;
    if (d && f) {
        const start = new Date(d), end = new Date(f);
        const diff  = Math.round((end - start) / 86400000) + 1;
        if (diff > 0) {
            document.getElementById('npDureeJours').textContent = diff;
            document.getElementById('npDureeInfo').style.display = '';
        } else {
            document.getElementById('npDureeInfo').style.display = 'none';
        }
    }
}
</script>
@endpush
