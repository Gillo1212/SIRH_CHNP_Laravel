@extends('layouts.master')

@section('title', 'Mon Planning')
@section('page-title', 'Mon Planning')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mon Planning</li>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<style>
.panel { background:white;border-radius:12px;padding:20px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.kpi-card { border-radius:12px;padding:16px 20px;transition:box-shadow 200ms,transform 200ms; }
.kpi-card:hover { box-shadow:0 4px 16px rgba(10,77,140,.08);transform:translateY(-2px); }
.kpi-icon { width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;margin-bottom:8px; }
.kpi-value { font-size:22px;font-weight:700;line-height:1.1; }
.kpi-label { font-size:12px;font-weight:500;margin-top:2px;color:#6B7280; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;color:#9CA3AF; }
.fc .fc-toolbar-title { font-size:15px !important;font-weight:600;color:#111827; }
.fc .fc-button { border-radius:7px !important;font-size:12px !important;font-weight:500 !important;padding:5px 12px !important; }
.fc .fc-button-primary { background:#0A4D8C !important;border-color:#0A4D8C !important; }
.fc .fc-button-primary:hover { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background:#1565C0 !important;border-color:#1565C0 !important; }
.fc .fc-daygrid-event { border-radius:5px;font-size:11px;padding:2px 6px;font-weight:600; }
.fc .fc-timegrid-event { border-radius:5px;font-size:11px; }
.fc .fc-day-today { background:rgba(10,77,140,.04) !important; }
.fc .fc-day-today .fc-daygrid-day-number { color:#0A4D8C;font-weight:700; }
#eventPopover { display:none;position:fixed;z-index:9999;background:white;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.15);padding:14px 18px;min-width:230px;pointer-events:none;border:1px solid #E5E7EB; }
.legend-dot { width:10px;height:10px;border-radius:2px;flex-shrink:0; }
.poste-row { display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #F3F4F6; }
.poste-row:last-child { border-bottom:none; }
.poste-date-badge { width:44px;height:44px;border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0; }
.poste-date-badge .day-num { font-size:16px;font-weight:700;line-height:1; }
.poste-date-badge .day-name { font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.05em; }
[data-theme="dark"] .panel { background:#161b22;border-color:#30363d; }
[data-theme="dark"] .poste-row { border-color:#30363d; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                Bonjour, {{ $agent->prenom }} 
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                {{ now()->isoFormat('dddd D MMMM YYYY') }} — Mon planning de travail
            </p>
        </div>
        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:8px 14px;font-size:12px;color:#1E40AF;font-weight:500;">
            <i class="fas fa-info-circle me-1"></i>
            Seuls les plannings validés par la RH sont affichés
        </div>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────── --}}
    <div class="section-title">Vue d'ensemble</div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="kpi-card" style="background:#EFF6FF;border:1px solid #BFDBFE;">
                <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-calendar-check" style="color:#0A4D8C;"></i></div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['ce_mois'] }}</div>
                <div class="kpi-label">Postes ce mois</div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="kpi-card" style="background:#F0FDF4;border:1px solid #BBF7D0;">
                <div class="kpi-icon" style="background:#DCFCE7;"><i class="fas fa-calendar-day" style="color:#16A34A;"></i></div>
                <div class="kpi-value" style="color:#16A34A;">{{ $stats['cette_semaine'] }}</div>
                <div class="kpi-label">Postes cette semaine</div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="kpi-card" style="background:#F9FAFB;border:1px solid #E5E7EB;">
                <div class="kpi-icon" style="background:#F3F4F6;"><i class="fas fa-list" style="color:#6B7280;"></i></div>
                <div class="kpi-value" style="color:#6B7280;">{{ $stats['total'] }}</div>
                <div class="kpi-label">Total postes planifiés</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ════════════════════════════════════════════════════════ --}}
        {{-- Calendrier                                              --}}
        {{-- ════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-xl-8">
            <div class="panel h-100">
                <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                    <div>
                        <div class="fw-600" style="color:#111827;font-size:14px;">Mon Calendrier de Travail</div>
                        <div style="font-size:12px;color:#9CA3AF;">Planning validé par le service RH</div>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @foreach([
                            ['Jour','#3B82F6'],['Nuit','#4F46E5'],['Garde','#F59E0B'],
                            ['Repos','#9CA3AF'],['Astreinte','#8B5CF6'],['Permanence','#0D9488']
                        ] as [$lib,$col])
                        <div class="d-flex align-items-center gap-1" style="font-size:11px;font-weight:500;color:#6B7280;">
                            <div class="legend-dot" style="background:{{ $col }};"></div>{{ $lib }}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div id="calendarAgent" style="min-height:480px;"></div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════ --}}
        {{-- Prochains postes                                        --}}
        {{-- ════════════════════════════════════════════════════════ --}}
        <div class="col-12 col-xl-4">
            <div class="panel h-100">
                <div class="fw-600 mb-1" style="color:#111827;font-size:14px;">Prochains postes</div>
                <div style="font-size:12px;color:#9CA3AF;margin-bottom:16px;">À partir d'aujourd'hui</div>

                @forelse($prochains as $ligne)
                    @php
                        $typeLib = $ligne->typePoste->libelle ?? '—';
                        $colorMap = [
                            'Jour'       => ['#EFF6FF','#1E40AF','#3B82F6'],
                            'Nuit'       => ['#EEF2FF','#3730A3','#4F46E5'],
                            'Garde'      => ['#FFFBEB','#92400E','#F59E0B'],
                            'Repos'      => ['#F3F4F6','#374151','#9CA3AF'],
                            'Astreinte'  => ['#F5F3FF','#5B21B6','#8B5CF6'],
                            'Permanence' => ['#F0FDFA','#134E4A','#0D9488'],
                        ];
                        $cc = $colorMap[$typeLib] ?? ['#F3F4F6','#374151','#9CA3AF'];
                        $hd = is_string($ligne->heure_debut) ? substr($ligne->heure_debut,0,5) : $ligne->heure_debut->format('H:i');
                        $hf = is_string($ligne->heure_fin)   ? substr($ligne->heure_fin,0,5)   : $ligne->heure_fin->format('H:i');
                        $isToday    = $ligne->date_poste->isToday();
                        $isTomorrow = $ligne->date_poste->isTomorrow();
                    @endphp
                    <div class="poste-row">
                        {{-- Date badge --}}
                        <div class="poste-date-badge" style="background:{{ $cc[0] }};">
                            <div class="day-num" style="color:{{ $cc[1] }};">{{ $ligne->date_poste->format('d') }}</div>
                            <div class="day-name" style="color:{{ $cc[2] }};opacity:.7;">{{ $ligne->date_poste->isoFormat('MMM') }}</div>
                        </div>
                        {{-- Info --}}
                        <div class="flex-grow-1 min-width-0">
                            <div style="font-weight:600;color:#111827;font-size:13px;display:flex;align-items:center;gap:6px;">
                                <span style="padding:2px 8px;border-radius:20px;background:{{ $cc[0] }};color:{{ $cc[1] }};font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">{{ $typeLib }}</span>
                                @if($isToday)
                                    <span style="padding:2px 8px;border-radius:20px;background:#0A4D8C;color:white;font-size:10px;font-weight:700;">Aujourd'hui</span>
                                @elseif($isTomorrow)
                                    <span style="padding:2px 8px;border-radius:20px;background:#7C3AED;color:white;font-size:10px;font-weight:700;">Demain</span>
                                @endif
                            </div>
                            <div style="font-size:12px;color:#6B7280;margin-top:3px;">
                                <i class="fas fa-clock me-1" style="font-size:10px;"></i>{{ $hd }} → {{ $hf }}
                                &nbsp;·&nbsp;
                                {{ $ligne->date_poste->isoFormat('dddd') }}
                            </div>
                            @if($ligne->planning && $ligne->planning->service)
                                <div style="font-size:11px;color:#9CA3AF;margin-top:1px;">
                                    <i class="fas fa-hospital-alt me-1" style="font-size:9px;"></i>{{ $ligne->planning->service->nom_service }}
                                </div>
                            @endif
                        </div>
                        {{-- Heures --}}
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-size:15px;font-weight:700;color:{{ $cc[1] }};">{{ $ligne->nb_heures }}h</div>
                            <div style="font-size:10px;color:#9CA3AF;">de travail</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <div style="width:60px;height:60px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <i class="fas fa-calendar-times fa-lg" style="color:#D1D5DB;"></i>
                        </div>
                        <div style="font-size:13px;font-weight:500;color:#6B7280;">Aucun poste à venir</div>
                        <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">
                            Votre planning n'a pas encore été transmis ou validé par la RH.
                        </div>
                    </div>
                @endforelse

                @if($stats['total'] === 0)
                    <div class="mt-3 pt-3" style="border-top:1px solid #F3F4F6;">
                        <div style="background:#FFFBEB;border-radius:8px;padding:12px;font-size:12px;color:#92400E;">
                            <i class="fas fa-lightbulb me-1" style="color:#D97706;"></i>
                            Votre manager n'a pas encore créé de planning pour votre service, ou le planning est en cours de validation par la RH.
                        </div>
                    </div>
                @endif
            </div>
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
    calendar = new FullCalendar.Calendar(document.getElementById('calendarAgent'), {
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
            info.el.title = `${props.typePoste} · ${props.heureDebut}→${props.heureFin}`;
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const pop   = document.getElementById('eventPopover');
            document.getElementById('popoverContent').innerHTML = `
                <div style="font-weight:600;color:#111827;font-size:13px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    ${props.typePoste}
                </div>
                <div style="font-size:12px;color:#6B7280;line-height:2;">
                    <div><i class="fas fa-clock me-2" style="width:14px;color:#9CA3AF;"></i>${props.heureDebut} → ${props.heureFin}</div>
                    <div><i class="fas fa-hourglass me-2" style="width:14px;color:#9CA3AF;"></i>${props.nbHeures}h de travail</div>
                    <div><i class="fas fa-hospital-alt me-2" style="width:14px;color:#9CA3AF;"></i>${props.service}</div>
                </div>
            `;
            const x = Math.min(info.jsEvent.clientX + 12, window.innerWidth - 270);
            const y = Math.min(info.jsEvent.clientY - 10, window.innerHeight - 180);
            pop.style.left  = x + 'px';
            pop.style.top   = y + 'px';
            pop.style.display = 'block';
            setTimeout(() => pop.style.display = 'none', 5000);
        },
        dayCellDidMount: function(info) {
            // Mettre en évidence aujourd'hui
            if (info.date.toDateString() === new Date().toDateString()) {
                info.el.style.borderRadius = '4px';
            }
        },
    });
    calendar.render();

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#eventPopover') && !e.target.closest('.fc-event')) {
            document.getElementById('eventPopover').style.display = 'none';
        }
    });
});
</script>
@endpush
