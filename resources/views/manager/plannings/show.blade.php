@extends('layouts.master')

@section('title', 'Planning — ' . $planning->periode_debut->isoFormat('D MMM') . ' au ' . $planning->periode_fin->isoFormat('D MMM YYYY'))
@section('page-title', 'Détail du Planning')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.planning.index') }}" style="color:#1565C0;">Plannings</a></li>
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

    {{-- ── Alertes ────────────────────────────────────────────────── --}}
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
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── En-tête Planning ────────────────────────────────────────── --}}
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
                    <i class="fas fa-hospital-alt me-1"></i>{{ $service->nom_service }} &nbsp;·&nbsp;
                    <i class="fas fa-calendar me-1"></i>{{ $planning->duree_jours }} jour(s) &nbsp;·&nbsp;
                    <i class="fas fa-list me-1"></i>{{ $planning->lignes->count() }} ligne(s) &nbsp;·&nbsp;
                    <i class="fas fa-users me-1"></i>{{ $planning->lignes->pluck('id_agent')->unique()->count() }} agent(s)
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($planning->est_modifiable)
                    <button class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjouterLigne">
                        <i class="fas fa-plus"></i>Ajouter une ligne
                    </button>
                    <button class="action-btn"
                            style="background:#FFFBEB;color:#D97706;border:1px solid #FDE68A;"
                            onclick="openModalTransmettre()">
                        <i class="fas fa-paper-plane"></i>Transmettre à la RH
                    </button>
                @endif
                <a href="{{ route('manager.planning.index') }}" class="action-btn action-btn-outline">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>

        @if($planning->statut_planning === 'Rejeté' && $planning->motif_rejet)
            <div class="mt-3" style="background:#FEF2F2;border-left:4px solid #DC2626;border-radius:8px;padding:12px 16px;">
                <div style="font-size:13px;font-weight:600;color:#DC2626;margin-bottom:4px;">
                    <i class="fas fa-times-circle me-1"></i>Planning rejeté par la RH
                </div>
                <div style="font-size:13px;color:#374151;">{{ $planning->motif_rejet }}</div>
                <div class="mt-2" style="font-size:12px;color:#9CA3AF;">
                    Corrigez les lignes concernées, puis retransmettez à la RH.
                </div>
            </div>
        @endif

        @if($planning->statut_planning === 'Validé')
            <div class="mt-3" style="background:#ECFDF5;border-left:4px solid #059669;border-radius:8px;padding:12px 16px;font-size:13px;color:#065F46;">
                <i class="fas fa-check-double me-1"></i>
                Ce planning a été <strong>validé</strong> par le service RH. Il est maintenant en vigueur.
            </div>
        @elseif($planning->statut_planning === 'Transmis')
            <div class="mt-3" style="background:#FFFBEB;border-left:4px solid #D97706;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400E;">
                <i class="fas fa-hourglass-half me-1"></i>
                Ce planning est <strong>en attente de validation</strong> par la RH.
            </div>
        @endif
    </div>

    {{-- ── Stats postes ───────────────────────────────────────────── --}}
    @if($statsPoste->count())
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <span style="font-size:12px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:.06em;">Répartition :</span>
        @php
            $colorMap = ['Jour'=>['#EFF6FF','#1E40AF'],'Nuit'=>['#EEF2FF','#3730A3'],'Garde'=>['#FFFBEB','#92400E'],'Repos'=>['#F3F4F6','#374151'],'Astreinte'=>['#F5F3FF','#5B21B6'],'Permanence'=>['#F0FDFA','#134E4A']];
        @endphp
        @foreach($statsPoste as $type => $count)
            @php $cc = $colorMap[$type] ?? ['#F3F4F6','#374151']; @endphp
            <span class="stat-chip" style="background:{{ $cc[0] }};color:{{ $cc[1] }};">
                {{ $type }} : <strong>{{ $count }}</strong>
            </span>
        @endforeach
    </div>
    @endif

    {{-- ── Tabs ────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <button class="tab-btn active" id="btnCalendar" onclick="switchTab('calendar')">
            <i class="fas fa-calendar-alt"></i>Calendrier
        </button>
        <button class="tab-btn" id="btnDetail" onclick="switchTab('detail')">
            <i class="fas fa-table"></i>Détail par date
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Tab : Calendrier                                              --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div id="tabCalendar">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <div class="fw-600" style="color:#111827;font-size:14px;">Vue calendrier</div>
                    <div style="font-size:12px;color:#9CA3AF;">Postes planifiés sur la période</div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @foreach([['Jour','#3B82F6'],['Nuit','#4F46E5'],['Garde','#F59E0B'],['Repos','#9CA3AF'],['Astreinte','#8B5CF6'],['Permanence','#0D9488']] as [$lib,$col])
                    <div class="d-flex align-items-center gap-1" style="font-size:11px;font-weight:500;color:#6B7280;">
                        <div class="legend-dot" style="background:{{ $col }};"></div>{{ $lib }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="calendarDetail" style="min-height:450px;"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Tab : Détail par date                                         --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div id="tabDetail" style="display:none;">
        @forelse($lignesParDate as $date => $lignes)
            <div class="panel mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fw-600" style="color:#111827;font-size:14px;">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd D MMMM YYYY') }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $lignes->count() }} agent(s) planifié(s)</div>
                    </div>
                    @if($planning->est_modifiable)
                        <button class="action-btn" style="font-size:12px;padding:6px 12px;background:#EFF6FF;color:#0A4D8C;border:1px solid #BFDBFE;"
                                data-bs-toggle="modal" data-bs-target="#modalAjouterLigne"
                                onclick="document.getElementById('al_date').value = '{{ $date }}'">
                            <i class="fas fa-plus"></i>Ajouter
                        </button>
                    @endif
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
                                @if($planning->est_modifiable)
                                    <th class="py-2 border-0" style="width:50px;"></th>
                                @endif
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
                                <tr class="ligne-row">
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
                                    @if($planning->est_modifiable)
                                        <td class="py-2 border-0">
                                            <form action="{{ route('manager.planning.lignes.destroy', [$planning->id_planning, $ligne->id_ligne]) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="delete-ligne-btn" title="Supprimer cette ligne"
                                                        onclick="return confirm('Supprimer cette ligne ?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="panel text-center py-5">
                <div style="width:72px;height:72px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-calendar-day fa-2x" style="color:#D1D5DB;"></i>
                </div>
                <h6 class="fw-bold mb-1" style="color:#111827;">Planning vide</h6>
                <p class="text-muted mb-3" style="font-size:13px;">Aucune ligne de planning. Commencez par ajouter des postes.</p>
                @if($planning->est_modifiable)
                    <button class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjouterLigne">
                        <i class="fas fa-plus"></i>Ajouter la première ligne
                    </button>
                @endif
            </div>
        @endforelse
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                              --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal : Ajouter une ligne ───────────────────────────────────── --}}
@if($planning->est_modifiable)
<div class="modal fade" id="modalAjouterLigne" tabindex="-1" aria-labelledby="labelAjouterLigne" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form action="{{ route('manager.planning.lignes.store', $planning->id_planning) }}" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:42px;height:42px;border-radius:10px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-plus" style="color:#0A4D8C;font-size:17px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="labelAjouterLigne" style="color:#111827;">Ajouter une ligne</h5>
                            <p class="text-muted mb-0" style="font-size:12px;">
                                Période : {{ $planning->periode_debut->format('d/m/Y') }} → {{ $planning->periode_fin->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:12px 24px;">
                    <div class="row g-3">
                        {{-- Agent --}}
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:13px;">Agent <span class="text-danger">*</span></label>
                            <select name="id_agent" class="form-select" required style="border-radius:8px;font-size:13px;">
                                <option value="">— Sélectionner un agent —</option>
                                @foreach($agents as $ag)
                                    <option value="{{ $ag->id_agent }}">{{ $ag->nom }} {{ $ag->prenom }} — {{ $ag->fontion }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Type de poste --}}
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:13px;">Type de poste <span class="text-danger">*</span></label>
                            <select name="id_typeposte" id="al_typeposte" class="form-select" required style="border-radius:8px;font-size:13px;" onchange="updatePosteColor()">
                                <option value="">— Sélectionner —</option>
                                @foreach($typesPoste as $tp)
                                    <option value="{{ $tp->id_typeposte }}" data-libelle="{{ $tp->libelle }}">{{ $tp->libelle }}{{ $tp->description ? ' — ' . $tp->description : '' }}</option>
                                @endforeach
                            </select>
                            <div id="posteColorBar" style="height:3px;border-radius:2px;margin-top:6px;background:#E5E7EB;transition:background 200ms;"></div>
                        </div>
                        {{-- Date --}}
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:13px;">Date du poste <span class="text-danger">*</span></label>
                            <input type="date" name="date_poste" id="al_date" class="form-control" required
                                   min="{{ $planning->periode_debut->format('Y-m-d') }}"
                                   max="{{ $planning->periode_fin->format('Y-m-d') }}"
                                   style="border-radius:8px;font-size:13px;">
                            <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">
                                Entre le {{ $planning->periode_debut->format('d/m/Y') }} et le {{ $planning->periode_fin->format('d/m/Y') }}
                            </div>
                        </div>
                        {{-- Heures --}}
                        <div class="col-6">
                            <label class="form-label fw-600" style="font-size:13px;">Heure début <span class="text-danger">*</span></label>
                            <input type="time" name="heure_debut" id="al_hd" class="form-control" required
                                   style="border-radius:8px;font-size:13px;" onchange="calcDureePoste()">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-600" style="font-size:13px;">Heure fin <span class="text-danger">*</span></label>
                            <input type="time" name="heure_fin" id="al_hf" class="form-control" required
                                   style="border-radius:8px;font-size:13px;" onchange="calcDureePoste()">
                        </div>
                    </div>
                    <div id="dureePosteInfo" class="mt-2" style="display:none;font-size:12px;color:#6B7280;"></div>
                </div>
                <div class="modal-footer border-0" style="padding:8px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-plus"></i>Ajouter la ligne
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ── Modal : Transmettre ──────────────────────────────────────────── --}}
@if($planning->est_modifiable)
<div class="modal fade" id="modalTransmettre" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <form action="{{ route('manager.planning.transmettre', $planning->id_planning) }}" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:24px 24px 4px;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-paper-plane" style="color:#D97706;font-size:20px;"></i>
                        <h5 class="modal-title fw-bold mb-0" style="color:#111827;">Transmettre à la RH</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:16px 24px;">
                    <div class="mb-3" style="background:#F9FAFB;border-radius:10px;padding:14px 16px;">
                        <div style="font-size:13px;color:#374151;margin-bottom:4px;">
                            <i class="fas fa-calendar-week me-2" style="color:#0A4D8C;"></i>
                            <strong>{{ $planning->periode_debut->isoFormat('D MMMM') }}</strong>
                            → <strong>{{ $planning->periode_fin->isoFormat('D MMMM YYYY') }}</strong>
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;">
                            {{ $planning->duree_jours }} jour(s) · {{ $planning->lignes->count() }} ligne(s) · {{ $planning->lignes->pluck('id_agent')->unique()->count() }} agent(s)
                        </div>
                    </div>
                    @if($planning->lignes->count() === 0)
                        <div style="background:#FEF2F2;border-left:3px solid #DC2626;border-radius:6px;padding:10px 12px;font-size:12px;color:#991B1B;">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <strong>Impossible de transmettre :</strong> ce planning ne contient aucune ligne.
                        </div>
                    @else
                        <div style="background:#FFFBEB;border-left:3px solid #F59E0B;border-radius:6px;padding:10px 12px;font-size:12px;color:#92400E;">
                            <i class="fas fa-info-circle me-1"></i>
                            Une fois transmis, ce planning ne pourra plus être modifié jusqu'à la décision de la RH.
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0" style="padding:4px 24px 24px;gap:8px;">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-primary" {{ $planning->lignes->count() === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i>Transmettre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Event popover --}}
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
        validRange: {
            start: '{{ $planning->periode_debut->format('Y-m-d') }}',
            end:   '{{ $planning->periode_fin->copy()->addDay()->format('Y-m-d') }}'
        },
        events: calendarEvents,
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
    if (tab === 'calendar' && calendar) {
        setTimeout(() => calendar.updateSize(), 100);
    }
}

function openModalTransmettre() {
    new bootstrap.Modal(document.getElementById('modalTransmettre')).show();
}

// Couleur du type de poste
const posteColors = {
    'Jour':'#3B82F6','Nuit':'#4F46E5','Garde':'#F59E0B',
    'Repos':'#9CA3AF','Astreinte':'#8B5CF6','Permanence':'#0D9488'
};
function updatePosteColor() {
    const sel = document.getElementById('al_typeposte');
    const lib = sel.options[sel.selectedIndex]?.dataset.libelle || '';
    const bar = document.getElementById('posteColorBar');
    bar.style.background = posteColors[lib] || '#E5E7EB';
}

// Durée du poste
function calcDureePoste() {
    const hd = document.getElementById('al_hd')?.value;
    const hf = document.getElementById('al_hf')?.value;
    const el = document.getElementById('dureePosteInfo');
    if (!hd || !hf) { el.style.display = 'none'; return; }
    const [dh, dm] = hd.split(':').map(Number);
    const [fh, fm] = hf.split(':').map(Number);
    let diff = (fh * 60 + fm) - (dh * 60 + dm);
    if (diff <= 0) diff += 24 * 60; // poste de nuit
    const h = Math.floor(diff / 60), m = diff % 60;
    const isNight = hf < hd;
    el.innerHTML = `<i class="fas fa-clock me-1" style="color:#3B82F6;"></i>Durée : <strong>${h}h${m > 0 ? m + 'min' : ''}</strong>${isNight ? ' <span style="color:#4F46E5;font-weight:600;">(poste de nuit — fin le lendemain)</span>' : ''}`;
    el.style.display = '';
}
</script>
@endpush
