@extends('layouts.master')
@section('title', 'Absences — Mon Service')
@section('page-title', 'Gestion des Absences')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li>Absences équipe</li>
@endsection

@push('styles')
<style>
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
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-danger { background:#DC2626;color:#fff; }
.action-btn-danger:hover { background:#B91C1C;color:#fff;box-shadow:0 4px 12px rgba(220,38,38,.25); }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:#6B7280; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;color:#6B7280; }
.abs-row { transition:background 150ms; }
.abs-row:hover { background:#F9FAFB !important; }
.btn-icon { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;transition:all 150ms;font-size:12px; }
.btn-icon-view { background:#EFF6FF;color:#1D4ED8; }
.btn-icon-view:hover { background:#DBEAFE; }
@keyframes toastIn  { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
@keyframes toastOut { from { opacity:1; } to { opacity:0;transform:translateX(40px); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Absences — {{ $service->nom_service }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Suivi des absences de votre équipe</p>
        </div>
        <button type="button" class="action-btn action-btn-danger"
                data-bs-toggle="modal" data-bs-target="#modal-create-absence">
            <i class="fas fa-plus"></i>Enregistrer une absence
        </button>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-user-minus" style="color:#DC2626;"></i></div>
                    <span style="background:#FEE2E2;color:#991B1B;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Ce mois</span>
                </div>
                <div class="kpi-value" style="color:#DC2626;">{{ $statsMois['total'] }}</div>
                <div class="kpi-label text-muted">Absences enregistrées</div>
                <div class="kpi-trend {{ $statsMois['total'] > 0 ? 'down' : 'up' }}">
                    <i class="fas fa-{{ $statsMois['total'] > 0 ? 'arrow-up' : 'check' }} me-1"></i>
                    {{ $statsMois['total'] > 0 ? 'À surveiller' : 'Aucune absence' }}
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
                    <span style="background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Justifiées</span>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $statsMois['justifiees'] }}</div>
                <div class="kpi-label text-muted">Justificatif fourni</div>
                <div class="kpi-trend up"><i class="fas fa-check me-1"></i>Documentées</div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-hospital" style="color:#D97706;"></i></div>
                    <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Maladie</span>
                </div>
                <div class="kpi-value" style="color:#D97706;">{{ $statsMois['maladie'] }}</div>
                <div class="kpi-label text-muted">Congés maladie</div>
                <div class="kpi-trend up"><i class="fas fa-notes-medical me-1"></i>Certificat requis</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('manager.absences.index') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="mois" class="form-select" style="width:auto;min-width:140px;">
                    <option value="">Tous les mois</option>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ request('mois')==$m ? 'selected' : '' }}>{{ now()->month($m)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
                <select name="annee" class="form-select" style="width:auto;min-width:100px;">
                    @for($y=now()->year;$y>=now()->year-2;$y--)
                        <option value="{{ $y }}" {{ request('annee')==$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="type" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les types</option>
                    @foreach(['Maladie','Personnelle','Professionnelle','Injustifiée'] as $t)
                        <option value="{{ $t }}" {{ request('type')==$t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <select name="agent" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les agents</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id_agent }}" {{ request('agent')==$ag->id_agent ? 'selected' : '' }}>{{ $ag->nom_complet }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['mois', 'annee', 'type', 'agent']))
                    <a href="{{ route('manager.absences.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-list me-2" style="color:#DC2626;"></i>Absences de l'équipe
                <span class="text-muted ms-2" style="font-size:12px;font-weight:400;">({{ $absences->total() }} résultats)</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Date</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Justifiée</th>
                            <th class="py-3 border-0 text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absence)
                            @php
                                $ag = $absence->demande->agent ?? null;
                                $initiales = strtoupper(substr($ag?->prenom ?? 'A',0,1).substr($ag?->nom ?? '',0,1));
                                $typeColors = [
                                    'Maladie'         => 'background:#FEF3C7;color:#92400E',
                                    'Personnelle'     => 'background:#DBEAFE;color:#1E40AF',
                                    'Professionnelle' => 'background:#EDE9FE;color:#5B21B6',
                                    'Injustifiée'     => 'background:#FEE2E2;color:#991B1B',
                                ];
                            @endphp
                            <tr class="abs-row" style="border-bottom:1px solid #F3F4F6;">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $initiales }}</div>
                                        <div>
                                            <div style="font-weight:600;color:var(--theme-text);">{{ $ag?->nom_complet ?? '—' }}</div>
                                            <div style="font-size:11px;color:#9CA3AF;">{{ $ag?->fonction ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 border-0" style="font-weight:500;color:var(--theme-text);">{{ $absence->date_absence->format('d/m/Y') }}</td>
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;{{ $typeColors[$absence->type_absence] ?? 'background:#F3F4F6;color:#374151' }};padding:3px 10px;border-radius:20px;font-weight:700;">{{ $absence->type_absence }}</span>
                                </td>
                                <td class="py-3 border-0">
                                    @if($absence->justifie)
                                        <span style="font-size:11px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-check me-1"></i>Oui</span>
                                    @else
                                        <span style="font-size:11px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-times me-1"></i>Non</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0 text-end pe-4">
                                    <a href="{{ route('manager.absences.show', $absence->id_absence) }}"
                                       class="btn-icon btn-icon-view" title="Voir le détail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted border-0">
                                    <i class="fas fa-calendar-check fa-2x mb-3 d-block" style="color:#D1D5DB;"></i>
                                    <p class="mb-1 fw-500">Aucune absence enregistrée</p>
                                    <p class="small mb-3">Votre équipe est au complet !</p>
                                    <button type="button" class="action-btn action-btn-danger" style="margin:0 auto;font-size:13px;padding:8px 16px;"
                                            data-bs-toggle="modal" data-bs-target="#modal-create-absence">
                                        <i class="fas fa-plus"></i>Enregistrer une absence
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
</div>

{{-- MODAL : ENREGISTRER UNE ABSENCE --}}
<div class="modal fade" id="modal-create-absence" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-minus" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Enregistrer une absence</h5>
                        <p class="text-muted small mb-0">{{ $service->nom_service }} — {{ now()->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manager.absences.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-7">
                            <label class="modal-label">Agent concerné <span class="text-danger">*</span></label>
                            <select name="id_agent" class="form-select form-select-sm" style="border-radius:7px;" required>
                                <option value="">— Sélectionner un agent —</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id_agent }}">
                                        {{ $agent->nom_complet }} ({{ $agent->matricule }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="modal-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_absence" value="{{ today()->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}"
                                   class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="modal-label">Type d'absence <span class="text-danger">*</span></label>
                        <select name="type_absence" class="form-select form-select-sm" style="border-radius:7px;" required>
                            <option value="">— Choisir —</option>
                            <option value="Maladie">Maladie (certificat médical requis)</option>
                            <option value="Personnelle">Personnelle</option>
                            <option value="Professionnelle">Professionnelle (formation, mission…)</option>
                            <option value="Injustifiée">Injustifiée</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check p-3" style="background:#F8FAFC;border-radius:8px;border:1px solid #E5E7EB;">
                            <input class="form-check-input" type="checkbox" name="justifie" id="justifieManager" value="1">
                            <label class="form-check-label small fw-600" for="justifieManager">
                                Absence justifiée
                                <span class="text-muted fw-normal ms-1">— justificatif fourni par l'agent</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="modal-label">Observations (optionnel)</label>
                        <textarea name="commentaire" rows="2" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"
                                  placeholder="Contexte, circonstances…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-danger">
                        <i class="fas fa-save"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showToast(message, type) {
    type = type || 'success';
    var cfg = { success:{bg:'#ECFDF5',color:'#065F46',icon:'check-circle',border:'#059669'}, error:{bg:'#FEF2F2',color:'#991B1B',icon:'times-circle',border:'#DC2626'}, warning:{bg:'#FFFBEB',color:'#92400E',icon:'exclamation-triangle',border:'#D97706'} };
    var c = cfg[type] || cfg.success;
    var t = document.createElement('div');
    t.style.cssText = 'background:'+c.bg+';color:'+c.color+';padding:14px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:500;min-width:280px;max-width:380px;animation:toastIn .3s ease;border-left:4px solid '+c.border+';pointer-events:all;';
    t.innerHTML = '<i class="fas fa-'+c.icon+'" style="flex-shrink:0;"></i><span>'+message+'</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;margin-left:auto;opacity:.7;padding:0;"><i class="fas fa-times"></i></button>';
    document.getElementById('toast-container').appendChild(t);
    setTimeout(function(){ t.style.animation='toastOut .3s ease forwards'; setTimeout(function(){ t.remove(); }, 300); }, 4000);
}
@if(session('success')) showToast(@json(session('success')), 'success'); @endif
@if(session('error'))   showToast(@json(session('error')),   'error');   @endif
@if(session('warning')) showToast(@json(session('warning')), 'warning'); @endif
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function(){
        new bootstrap.Modal(document.getElementById('modal-create-absence')).show();
    });
@endif
</script>
@endpush
<style>.fw-500{font-weight:500!important;}.fw-600{font-weight:600!important;}</style>
@endsection
