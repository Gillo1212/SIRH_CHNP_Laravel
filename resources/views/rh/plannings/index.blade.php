@extends('layouts.master')

@section('title', 'Tous les Plannings')
@section('page-title', 'Gestion des Plannings')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Plannings</li>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<style>
.kpi-card { border-radius:12px;padding:18px 20px;transition:box-shadow 200ms,transform 200ms; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.kpi-icon { width:40px;height:40px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;margin-bottom:10px; }
.kpi-value { font-size:26px;font-weight:700;line-height:1.1; }
.kpi-label { font-size:12px;font-weight:500;margin-top:3px;color:#6B7280; }
.panel { background:white;border-radius:12px;padding:20px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
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
#eventPopover { display:none;position:fixed;z-index:9999;background:white;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.15);padding:14px 18px;min-width:240px;pointer-events:none;border:1px solid #E5E7EB; }
.legend-dot { width:10px;height:10px;border-radius:2px;flex-shrink:0; }
.badge-status { display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600; }
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

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">Tous les Plannings</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.plannings.pending') }}" class="action-btn action-btn-primary position-relative">
                <i class="fas fa-clock"></i>À valider
                @if($stats['transmis'] > 0)
                    <span style="position:absolute;top:-6px;right:-6px;background:#DC2626;color:white;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1;">
                        {{ $stats['transmis'] }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="section-title">Tableau de bord</div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card" style="background:#EFF6FF;border:1px solid #BFDBFE;">
                <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-calendar-alt" style="color:#0A4D8C;"></i></div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['total'] }}</div>
                <div class="kpi-label">Total plannings</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-card" style="background:#FFFBEB;border:1px solid #FDE68A;">
                <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-hourglass-half" style="color:#D97706;"></i></div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['transmis'] }}</div>
                <div class="kpi-label">En attente validation</div>
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
            <div class="kpi-card" style="background:#F9FAFB;border:1px solid #E5E7EB;">
                <div class="kpi-icon" style="background:#F3F4F6;"><i class="fas fa-pencil-alt" style="color:#6B7280;"></i></div>
                <div class="kpi-value" style="color:#6B7280;">{{ $stats['brouillons'] }}</div>
                <div class="kpi-label">Brouillons (managers)</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('rh.plannings.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="service_id" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ request('service_id') == $svc->id_service ? 'selected' : '' }}>
                            {{ $svc->nom_service }}
                        </option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:150px;">
                    <option value="">Tous les statuts</option>
                    @foreach(['Brouillon','Transmis','Validé','Rejeté'] as $st)
                        <option value="{{ $st }}" {{ request('statut') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['service_id', 'statut']))
                    <a href="{{ route('rh.plannings.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabs --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <button class="tab-btn active" id="btnCalendar" onclick="switchTab('calendar')">
            <i class="fas fa-calendar-alt"></i>Calendrier global
        </button>
        <button class="tab-btn" id="btnListe" onclick="switchTab('liste')">
            <i class="fas fa-th-list"></i>Liste
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Tab : Calendrier                                              --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div id="tabCalendar">
        <div class="panel">
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <div class="fw-600" style="color:#111827;font-size:14px;">Vue calendrier — Plannings validés &amp; transmis</div>
                    <div style="font-size:12px;color:#9CA3AF;">Tous les services confondus</div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @foreach([['Jour','#3B82F6'],['Nuit','#4F46E5'],['Garde','#F59E0B'],['Repos','#9CA3AF'],['Astreinte','#8B5CF6'],['Permanence','#0D9488']] as [$lib,$col])
                    <div class="d-flex align-items-center gap-1" style="font-size:11px;font-weight:500;color:#6B7280;">
                        <div class="legend-dot" style="background:{{ $col }};"></div>{{ $lib }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="calendarRH" style="min-height:500px;"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Tab : Liste                                                   --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div id="tabListe" style="display:none;">
        <div class="panel">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#9CA3AF;">
                            <th class="px-4 py-3 border-0">Service</th>
                            <th class="py-3 border-0">Période</th>
                            <th class="py-3 border-0">Durée</th>
                            <th class="py-3 border-0">Lignes</th>
                            <th class="py-3 border-0">Statut</th>
                            <th class="py-3 border-0" style="width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plannings as $planning)
                            @php
                                $sc = match($planning->statut_planning) {
                                    'Brouillon' => ['bg'=>'#F3F4F6','c'=>'#6B7280'],
                                    'Transmis'  => ['bg'=>'#FFFBEB','c'=>'#D97706'],
                                    'Validé'    => ['bg'=>'#ECFDF5','c'=>'#059669'],
                                    'Rejeté'    => ['bg'=>'#FEF2F2','c'=>'#DC2626'],
                                    default     => ['bg'=>'#F3F4F6','c'=>'#6B7280'],
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #F3F4F6;transition:background 150ms;" onmouseover="this.style.background='#FAFBFF'" onmouseout="this.style.background=''">
                                <td class="px-4 py-3 border-0">
                                    <div style="font-weight:500;color:#111827;">{{ $planning->service->nom_service ?? '—' }}</div>
                                </td>
                                <td class="py-3 border-0" style="color:#374151;">
                                    {{ $planning->periode_debut->format('d/m/Y') }} → {{ $planning->periode_fin->format('d/m/Y') }}
                                </td>
                                <td class="py-3 border-0" style="color:#9CA3AF;">{{ $planning->duree_jours }}j</td>
                                <td class="py-3 border-0" style="color:#9CA3AF;">{{ $planning->lignes_count }}</td>
                                <td class="py-3 border-0">
                                    <span class="badge-status" style="background:{{ $sc['bg'] }};color:{{ $sc['c'] }};">
                                        {{ $planning->statut_planning }}
                                    </span>
                                </td>
                                <td class="py-3 border-0">
                                    <a href="{{ route('rh.plannings.show', $planning->id_planning) }}"
                                       class="action-btn action-btn-outline" style="font-size:12px;padding:5px 10px;">
                                        <i class="fas fa-eye"></i>Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5" style="color:#9CA3AF;">
                                    <i class="fas fa-calendar fa-2x mb-2 d-block"></i>
                                    Aucun planning trouvé avec ces filtres.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($plannings->hasPages())
                <div class="mt-4">{{ $plannings->links() }}</div>
            @endif
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
    calendar = new FullCalendar.Calendar(document.getElementById('calendarRH'), {
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
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const pop   = document.getElementById('eventPopover');
            document.getElementById('popoverContent').innerHTML = `
                <div style="font-weight:600;color:#111827;font-size:13px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">${info.event.title}</div>
                <div style="font-size:12px;color:#6B7280;line-height:1.9;">
                    <div><i class="fas fa-user me-2" style="width:14px;color:#9CA3AF;"></i>${props.agent}</div>
                    <div><i class="fas fa-hospital me-2" style="width:14px;color:#9CA3AF;"></i>${props.service}</div>
                    <div><i class="fas fa-clock me-2" style="width:14px;color:#9CA3AF;"></i>${props.heureDebut} → ${props.heureFin}</div>
                    <div><i class="fas fa-tag me-2" style="width:14px;color:#9CA3AF;"></i>${props.typePoste}</div>
                    <div><i class="fas fa-circle me-2" style="width:14px;color:#9CA3AF;"></i>${props.statut}</div>
                </div>
                <a href="/rh/plannings/${props.planningId}" style="display:block;margin-top:10px;font-size:11px;color:#0A4D8C;font-weight:600;">
                    <i class="fas fa-external-link-alt me-1"></i>Voir le planning
                </a>
            `;
            const x = Math.min(info.jsEvent.clientX + 12, window.innerWidth - 280);
            const y = Math.min(info.jsEvent.clientY - 10, window.innerHeight - 200);
            pop.style.left  = x + 'px';
            pop.style.top   = y + 'px';
            pop.style.display = 'block';
            pop.style.pointerEvents = 'auto';
        },
    });
    calendar.render();

    document.addEventListener('click', function(e) {
        const pop = document.getElementById('eventPopover');
        if (!e.target.closest('#eventPopover') && !e.target.closest('.fc-event')) {
            pop.style.display = 'none';
            pop.style.pointerEvents = 'none';
        }
    });
});

function switchTab(tab) {
    document.getElementById('tabCalendar').style.display = tab === 'calendar' ? '' : 'none';
    document.getElementById('tabListe').style.display    = tab === 'liste'    ? '' : 'none';
    document.getElementById('btnCalendar').className     = 'tab-btn' + (tab === 'calendar' ? ' active' : '');
    document.getElementById('btnListe').className        = 'tab-btn' + (tab === 'liste'    ? ' active' : '');
    if (tab === 'calendar' && calendar) setTimeout(() => calendar.updateSize(), 100);
}
</script>
@endpush
