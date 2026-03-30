@extends('layouts.master')

@section('title', 'Planning — ' . ($planning->service->nom_service ?? '') . ' · ' . $planning->periode_debut->format('d/m/Y'))
@section('page-title', 'Détail du Planning')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.plannings.index') }}" style="color:#1565C0;">Plannings</a></li>
    @if($planning->statut_planning === 'Transmis')
        <li><a href="{{ route('rh.plannings.pending') }}" style="color:#1565C0;">À valider</a></li>
    @endif
    <li>{{ $planning->service->nom_service ?? 'Planning' }}</li>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<style>
.panel { background:white;border-radius:12px;padding:20px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.tab-btn { padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;border:1px solid #E5E7EB;background:white;color:#6B7280;cursor:pointer;transition:all 180ms;display:inline-flex;align-items:center;gap:6px; }
.tab-btn.active { background:#0A4D8C;color:white;border-color:#0A4D8C; }
.tab-btn:hover:not(.active) { background:#F9FAFB; }
.badge-poste { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.fc .fc-toolbar-title { font-size:15px !important;font-weight:600;color:#111827; }
.fc .fc-button { border-radius:7px !important;font-size:12px !important;font-weight:500 !important;padding:5px 12px !important; }
.fc .fc-button-primary { background:#0A4D8C !important;border-color:#0A4D8C !important; }
.fc .fc-button-primary:hover { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-daygrid-event { border-radius:5px;font-size:11px;padding:1px 5px;font-weight:500; }
.fc .fc-day-today { background:rgba(10,77,140,.03) !important; }
.fc .fc-day-today .fc-daygrid-day-number { color:#0A4D8C;font-weight:700; }
#eventPopover { display:none;position:fixed;z-index:9999;background:white;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.15);padding:14px 18px;min-width:230px;pointer-events:none;border:1px solid #E5E7EB; }
.legend-dot { width:10px;height:10px;border-radius:2px;flex-shrink:0; }
.stat-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#F3F4F6;color:#374151; }
[data-theme="dark"] .panel { background:#161b22;border-color:#30363d; }
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

    {{-- ── En-tête ────────────────────────────────────────────────── --}}
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
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="fw-bold mb-0" style="color:#111827;">
                        {{ $planning->service->nom_service ?? 'Service inconnu' }}
                    </h5>
                    <span style="padding:4px 12px;border-radius:20px;background:{{ $sc['bg'] }};color:{{ $sc['c'] }};font-size:12px;font-weight:600;">
                        <i class="fas {{ $sc['ic'] }} me-1" style="font-size:10px;"></i>{{ $planning->statut_planning }}
                    </span>
                </div>
                <div style="font-size:13px;color:#6B7280;">
                    <i class="fas fa-calendar me-1"></i>
                    {{ $planning->periode_debut->isoFormat('D MMMM') }} → {{ $planning->periode_fin->isoFormat('D MMMM YYYY') }}
                    &nbsp;·&nbsp; {{ $planning->duree_jours }} jour(s)
                    &nbsp;·&nbsp; {{ $planning->lignes->count() }} ligne(s)
                    &nbsp;·&nbsp; {{ $agentsUniques }} agent(s)
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($planning->statut_planning === 'Transmis')
                    <button type="button" class="action-btn" style="background:#059669;color:white;border:none;"
                            onclick="openModalValider()">
                        <i class="fas fa-check-double"></i>Valider
                    </button>
                    <button type="button" class="action-btn" style="background:#DC2626;color:white;border:none;"
                            onclick="openModalRejeter()">
                        <i class="fas fa-times-circle"></i>Rejeter
                    </button>
                @endif
                <a href="{{ route('rh.plannings.pending') }}" class="action-btn action-btn-outline">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>

        @if($planning->statut_planning === 'Validé')
            <div class="mt-3" style="background:#ECFDF5;border-left:4px solid #059669;border-radius:8px;padding:12px 16px;font-size:13px;color:#065F46;">
                <i class="fas fa-check-double me-1"></i>
                Ce planning a été <strong>validé</strong>. Il est maintenant visible par les agents concernés.
            </div>
        @elseif($planning->statut_planning === 'Transmis')
            <div class="mt-3" style="background:#FFFBEB;border-left:4px solid #D97706;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400E;">
                <i class="fas fa-hourglass-half me-1"></i>
                Ce planning est <strong>en attente de votre validation</strong>.
                Vérifiez les lignes ci-dessous avant de valider ou rejeter.
            </div>
        @endif
    </div>

    {{-- Stats postes --}}
    @if($statsPoste->count())
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <span style="font-size:12px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:.06em;">Répartition :</span>
        @php
            $colorMap = ['Jour'=>['#EFF6FF','#1E40AF'],'Nuit'=>['#EEF2FF','#3730A3'],'Garde'=>['#FFFBEB','#92400E'],'Repos'=>['#F3F4F6','#374151'],'Astreinte'=>['#F5F3FF','#5B21B6'],'Permanence'=>['#F0FDFA','#134E4A']];
        @endphp
        @foreach($statsPoste as $type => $cnt)
            @php $cc = $colorMap[$type] ?? ['#F3F4F6','#374151']; @endphp
            <span class="stat-chip" style="background:{{ $cc[0] }};color:{{ $cc[1] }};">{{ $type }} : <strong>{{ $cnt }}</strong></span>
        @endforeach
    </div>
    @endif

    {{-- Tabs --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <button class="tab-btn active" id="btnCalendar" onclick="switchTab('calendar')">
            <i class="fas fa-calendar-alt"></i>Calendrier
        </button>
        <button class="tab-btn" id="btnDetail" onclick="switchTab('detail')">
            <i class="fas fa-table"></i>Détail par date
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Tab : Calendrier                                              --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tabCalendar">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <div class="fw-600" style="color:#111827;font-size:14px;">Vue calendrier</div>
                    <div style="font-size:12px;color:#9CA3AF;">Postes de la période</div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @foreach([['Jour','#3B82F6'],['Nuit','#4F46E5'],['Garde','#F59E0B'],['Repos','#9CA3AF'],['Astreinte','#8B5CF6'],['Permanence','#0D9488']] as [$lib,$col])
                    <div class="d-flex align-items-center gap-1" style="font-size:11px;font-weight:500;color:#6B7280;">
                        <div class="legend-dot" style="background:{{ $col }};"></div>{{ $lib }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="calendarShow" style="min-height:450px;"></div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- Tab : Détail par date                                        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tabDetail" style="display:none;">
        @forelse($lignesParDate as $date => $lignes)
            <div class="panel mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fw-600" style="color:#111827;font-size:14px;">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd D MMMM YYYY') }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $lignes->count() }} agent(s)</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="background:#F8FAFC;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#9CA3AF;">
                                <th class="px-3 py-2 border-0">Agent</th>
                                <th class="py-2 border-0">Poste</th>
                                <th class="py-2 border-0">Début</th>
                                <th class="py-2 border-0">Fin</th>
                                <th class="py-2 border-0">Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lignes as $ligne)
                                @php
                                    $typeLib = $ligne->typePoste->libelle ?? '—';
                                    $cpMap = ['Jour'=>['#EFF6FF','#1E40AF'],'Nuit'=>['#EEF2FF','#3730A3'],'Garde'=>['#FFFBEB','#92400E'],'Repos'=>['#F3F4F6','#374151'],'Astreinte'=>['#F5F3FF','#5B21B6'],'Permanence'=>['#F0FDFA','#134E4A']];
                                    $cp = $cpMap[$typeLib] ?? ['#F3F4F6','#374151'];
                                    $hd = is_string($ligne->heure_debut) ? substr($ligne->heure_debut,0,5) : $ligne->heure_debut->format('H:i');
                                    $hf = is_string($ligne->heure_fin)   ? substr($ligne->heure_fin,0,5)   : $ligne->heure_fin->format('H:i');
                                @endphp
                                <tr style="border-bottom:1px solid #F3F4F6;">
                                    <td class="px-3 py-2 border-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:10px;flex-shrink:0;">
                                                {{ substr($ligne->agent->prenom ?? '?', 0, 1) }}{{ substr($ligne->agent->nom ?? '', 0, 1) }}
                                            </div>
                                            <span style="font-weight:500;color:#111827;">{{ $ligne->agent->nom_complet ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2 border-0">
                                        <span class="badge-poste" style="background:{{ $cp[0] }};color:{{ $cp[1] }};">{{ $typeLib }}</span>
                                    </td>
                                    <td class="py-2 border-0" style="color:#374151;font-variant-numeric:tabular-nums;">{{ $hd }}</td>
                                    <td class="py-2 border-0" style="color:#374151;font-variant-numeric:tabular-nums;">{{ $hf }}</td>
                                    <td class="py-2 border-0" style="color:#9CA3AF;">{{ $ligne->nb_heures }}h</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="panel text-center py-5">
                <i class="fas fa-calendar-day fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                <h6 class="fw-bold mb-1">Planning vide</h6>
                <p class="text-muted small">Aucune ligne de planning enregistrée.</p>
            </div>
        @endforelse
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                              --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal : Valider ─────────────────────────────────────────────── --}}
@if($planning->statut_planning === 'Transmis')
<div class="modal fade" id="modalValider" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form action="{{ route('rh.plannings.valider', $planning->id_planning) }}" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;border-radius:50%;background:#ECFDF5;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-check-double" style="color:#059669;font-size:18px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Valider ce planning ?</h5>
                            <p class="text-muted mb-0" style="font-size:12px;">{{ $planning->service->nom_service ?? '' }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div style="background:#F9FAFB;border-radius:10px;padding:14px 16px;margin-bottom:14px;font-size:13px;color:#374151;">
                        <div class="fw-600 mb-1">Récapitulatif</div>
                        <div style="color:#6B7280;line-height:1.8;">
                            <div><i class="fas fa-calendar me-2" style="width:14px;"></i>{{ $planning->periode_debut->isoFormat('D MMMM') }} → {{ $planning->periode_fin->isoFormat('D MMMM YYYY') }}</div>
                            <div><i class="fas fa-list me-2" style="width:14px;"></i>{{ $planning->lignes->count() }} ligne(s) · {{ $agentsUniques }} agent(s)</div>
                        </div>
                    </div>
                    <div style="background:#ECFDF5;border-left:3px solid #059669;border-radius:6px;padding:10px 12px;font-size:12px;color:#065F46;">
                        <i class="fas fa-info-circle me-1"></i>
                        En validant, le planning sera mis en vigueur et visible par les agents.
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#059669;color:white;border:none;">
                        <i class="fas fa-check-double"></i>Confirmer la validation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal : Rejeter ──────────────────────────────────────────────── --}}
<div class="modal fade" id="modalRejeter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form action="{{ route('rh.plannings.rejeter', $planning->id_planning) }}" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;border-radius:50%;background:#FEF2F2;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-times-circle" style="color:#DC2626;font-size:18px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Rejeter ce planning</h5>
                            <p class="text-muted mb-0" style="font-size:12px;">Le manager devra corriger et soumettre à nouveau</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div>
                        <label class="form-label fw-600" style="font-size:13px;">
                            Motif du rejet <span class="text-danger">*</span>
                        </label>
                        <textarea name="motif_rejet" class="form-control" rows="4" required
                                  placeholder="Expliquez clairement les raisons du rejet et ce que le manager doit corriger..."
                                  style="border-radius:8px;font-size:13px;resize:vertical;"></textarea>
                        <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">Minimum 10 caractères. Ce message sera transmis au manager.</div>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn" style="background:#DC2626;color:white;border:none;">
                        <i class="fas fa-times-circle"></i>Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div id="eventPopover"><div id="popoverContent"></div></div>

@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
const calendarEvents = @json($calendarEvents);
let calendar;

document.addEventListener('DOMContentLoaded', function () {
    calendar = new FullCalendar.Calendar(document.getElementById('calendarShow'), {
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
        validRange: {
            start: '{{ $planning->periode_debut->format('Y-m-d') }}',
            end:   '{{ $planning->periode_fin->copy()->addDay()->format('Y-m-d') }}'
        },
        events: calendarEvents,
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const pop   = document.getElementById('eventPopover');
            document.getElementById('popoverContent').innerHTML = `
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
    calendar.render();

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#eventPopover') && !e.target.closest('.fc-event')) {
            document.getElementById('eventPopover').style.display = 'none';
        }
    });
});

function switchTab(tab) {
    document.getElementById('tabCalendar').style.display = tab === 'calendar' ? '' : 'none';
    document.getElementById('tabDetail').style.display   = tab === 'detail'   ? '' : 'none';
    document.getElementById('btnCalendar').className     = 'tab-btn' + (tab === 'calendar' ? ' active' : '');
    document.getElementById('btnDetail').className       = 'tab-btn' + (tab === 'detail'   ? ' active' : '');
    if (tab === 'calendar' && calendar) setTimeout(() => calendar.updateSize(), 100);
}

function openModalValider() { new bootstrap.Modal(document.getElementById('modalValider')).show(); }
function openModalRejeter() { new bootstrap.Modal(document.getElementById('modalRejeter')).show(); }
</script>
@endpush
