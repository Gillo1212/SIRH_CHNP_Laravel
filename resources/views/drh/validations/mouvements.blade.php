@extends('layouts.master')
@section('title', 'Validation Mouvements — DRH')
@section('page-title', 'Validation des Mouvements')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Validation mouvements</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:20px 24px;transition:box-shadow 200ms,transform 200ms;position:relative;overflow:hidden;}
.kpi-card:hover{box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px);}
.kpi-card .kpi-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.kpi-card .kpi-value{font-size:28px;font-weight:700;line-height:1.1;margin-top:12px;}
.kpi-card .kpi-label{font-size:13px;margin-top:2px;font-weight:500;}
.kpi-card::before{content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 12px 0 80px;opacity:.07;}
.kpi-card.amber::before{background:#D97706;} .kpi-card.green::before{background:#059669;} .kpi-card.blue::before{background:#1565C0;} .kpi-card.red::before{background:#DC2626;}
.action-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;}
.action-btn-primary{background:#0A4D8C;color:#fff;} .action-btn-primary:hover{background:#083d70;color:#fff;}
.action-btn-success{background:#059669;color:#fff;} .action-btn-success:hover{background:#047857;color:#fff;}
.action-btn-danger{background:#DC2626;color:#fff;} .action-btn-danger:hover{background:#B91C1C;color:#fff;}
.action-btn-outline{background:transparent;color:#374151;border:1px solid #E5E7EB;} .action-btn-outline:hover{background:#F9FAFB;}
.modal-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;color:#6B7280;}
.mouv-row{transition:background 150ms;}
.mouv-row:hover{background:#F9FAFB!important;}
.btn-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;transition:all 150ms;font-size:12px;}
.btn-icon-view{background:#EFF6FF;color:#1D4ED8;} .btn-icon-view:hover{background:#DBEAFE;}
.btn-icon-success{background:#D1FAE5;color:#065F46;} .btn-icon-success:hover{background:#A7F3D0;}
.btn-icon-danger{background:#FEE2E2;color:#991B1B;} .btn-icon-danger:hover{background:#FECACA;}
@keyframes toastIn{from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);}}
@keyframes toastOut{from{opacity:1;}to{opacity:0;transform:translateX(40px);}}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-stamp me-2" style="color:#0A4D8C;"></i>Validation des Mouvements
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Mouvements en attente de décision DRH</p>
        </div>
        @if($stats['en_attente'] > 0)
        <span style="background:#FEF3C7;color:#92400E;padding:8px 18px;border-radius:8px;font-weight:700;font-size:14px;">
            <i class="fas fa-clock me-2"></i>{{ $stats['en_attente'] }} mouvement(s) à valider
        </span>
        @else
        <span style="background:#D1FAE5;color:#065F46;padding:8px 18px;border-radius:8px;font-weight:700;font-size:14px;">
            <i class="fas fa-check me-2"></i>Aucune décision en attente
        </span>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-clock" style="color:#D97706;"></i></div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['en_attente'] }}</div>
                <div class="kpi-label text-muted">En attente</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-double" style="color:#059669;"></i></div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['valide_drh'] }}</div>
                <div class="kpi-label text-muted">Validés</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-user-plus" style="color:#1565C0;"></i></div>
                <div class="kpi-value" style="color:#1565C0;">{{ $stats['affectations'] }}</div>
                <div class="kpi-label text-muted">Aff. à valider</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-arrows-alt-h" style="color:#D97706;"></i></div>
                <div class="kpi-value" style="color:#D97706;">{{ $stats['mutations'] }}</div>
                <div class="kpi-label text-muted">Mut. à valider</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-sign-out-alt" style="color:#DC2626;"></i></div>
                <div class="kpi-value" style="color:#DC2626;">{{ $stats['departs'] }}</div>
                <div class="kpi-label text-muted">Départs à valider</div>
            </div>
        </div>
    </div>

    {{-- Filtre type --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('drh.validations.mouvements') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="type_mouvement" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les types</option>
                    @foreach(\App\Models\Mouvement::TYPES as $key => $cfg)
                        <option value="{{ $key }}" {{ request('type_mouvement') == $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['type_mouvement']))
                    <a href="{{ route('drh.validations.mouvements') }}" class="btn btn-outline-secondary" title="Réinitialiser">
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
                <i class="fas fa-list me-2" style="color:#0A4D8C;"></i>Mouvements en attente de décision
                <span class="text-muted ms-2" style="font-size:12px;font-weight:400;">({{ $mouvements->total() }} résultats)</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Mouvement</th>
                            <th class="py-3 border-0">Date effet</th>
                            <th class="py-3 border-0">Statut</th>
                            <th class="py-3 border-0">Créé par</th>
                            <th class="py-3 border-0 text-end pe-4">Décision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $m)
                            @php
                                $typeCfg   = \App\Models\Mouvement::TYPES[$m->type_mouvement] ?? ['color'=>'#6B7280','bg'=>'#F3F4F6','icon'=>'fa-question','label'=>$m->type_mouvement];
                                $statutCfg = \App\Models\Mouvement::STATUTS[$m->statut] ?? ['color'=>'#6B7280','bg'=>'#F3F4F6','label'=>$m->statut];
                                $initiales = strtoupper(substr($m->agent->prenom??'A',0,1).substr($m->agent->nom??'',0,1));
                            @endphp
                            <tr class="mouv-row" style="border-bottom:1px solid #F3F4F6;">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ $initiales }}</div>
                                        <div>
                                            <div style="font-weight:600;color:var(--theme-text);">{{ $m->agent->nom_complet }}</div>
                                            <div style="font-size:11px;color:#9CA3AF;">{{ $m->agent->matricule }} · {{ $m->agent->fontion }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;background:{{ $typeCfg['bg'] }};color:{{ $typeCfg['color'] }};padding:3px 10px;border-radius:20px;font-weight:700;">
                                        <i class="fas {{ $typeCfg['icon'] }} me-1"></i>{{ $typeCfg['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 border-0" style="font-size:12.5px;">
                                    @if($m->serviceOrigine)
                                        <span class="text-muted">{{ $m->serviceOrigine->nom_service }}</span>
                                        <i class="fas fa-arrow-right mx-1" style="color:#D97706;font-size:10px;"></i>
                                    @endif
                                    @if($m->serviceDestination)
                                        <strong>{{ $m->serviceDestination->nom_service }}</strong>
                                    @elseif($m->type_mouvement === 'Départ')
                                        <span style="color:#DC2626;font-weight:600;">Départ définitif</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0" style="font-weight:500;">{{ $m->date_mouvement?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-3 border-0">
                                    <span style="font-size:11px;background:{{ $statutCfg['bg'] }};color:{{ $statutCfg['color'] }};padding:3px 10px;border-radius:20px;font-weight:700;">
                                        {{ $statutCfg['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 border-0" style="font-size:12px;color:#6B7280;">{{ $m->createur?->name ?? '—' }}</td>
                                <td class="py-3 border-0 text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        @if($m->statut === 'en_attente')
                                        {{-- Valider --}}
                                        <form action="{{ route('drh.validations.valider-mouvement', $m->id_mouvement) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Valider ce mouvement ? Il pourra ensuite être effectué par l\'AgentRH.')">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-icon-success" title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        {{-- Rejeter --}}
                                        <button type="button" class="btn-icon btn-icon-danger" title="Rejeter"
                                                onclick="rejeterMouvement({{ $m->id_mouvement }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @elseif($m->statut === 'valide_drh')
                                        <span style="font-size:11px;color:#059669;font-weight:600;"><i class="fas fa-check-double me-1"></i>Décision prise</span>
                                        @endif
                                        {{-- Motif --}}
                                        @if($m->motif)
                                        <button type="button" class="btn-icon btn-icon-view" title="Voir le motif" onclick="voirMotif({{ $m->id_mouvement }}, @json($m->motif))">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted border-0">
                                    <i class="fas fa-check-circle fa-2x mb-3 d-block" style="color:#D1FAE5;"></i>
                                    <p class="mb-1 fw-500">Aucun mouvement en attente</p>
                                    <p class="small">Tous les mouvements ont été traités</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($mouvements->hasPages())
            <div class="card-footer bg-transparent px-4 py-3" style="border-top:1px solid #F3F4F6;">{{ $mouvements->links() }}</div>
        @endif
    </div>
</div>

{{-- MODAL : REJETER --}}
<div class="modal fade" id="modal-rejeter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-3" style="background:#FEF2F2;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-times-circle" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Rejeter le mouvement</h5>
                        <p class="text-muted small mb-0">Le mouvement sera annulé définitivement</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-rejeter" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <label class="modal-label">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea name="motif_rejet" rows="4" class="form-control form-control-sm" style="border-radius:7px;resize:vertical;"
                              placeholder="Expliquez la raison du rejet (minimum 10 caractères)…" required minlength="10" maxlength="500"></textarea>
                    <div class="text-muted mt-1" style="font-size:11px;">Ce motif sera enregistré dans l'audit trail.</div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="action-btn action-btn-danger">
                        <i class="fas fa-times"></i>Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL : MOTIF --}}
<div class="modal fade" id="modal-motif" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:#F8FAFC;">
                <h5 class="modal-title fw-bold mb-0"><i class="fas fa-file-alt me-2 text-muted"></i>Motif du mouvement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="p-3 rounded-3" style="background:#FFFBEB;font-size:13.5px;" id="motif-content">—</div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="action-btn action-btn-outline" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showToast(message, type) {
    type = type || 'success';
    var cfg = {
        success:{bg:'#ECFDF5',color:'#065F46',icon:'check-circle',border:'#059669'},
        error:  {bg:'#FEF2F2',color:'#991B1B',icon:'times-circle',border:'#DC2626'},
        warning:{bg:'#FFFBEB',color:'#92400E',icon:'exclamation-triangle',border:'#D97706'}
    };
    var c = cfg[type] || cfg.success;
    var t = document.createElement('div');
    t.style.cssText = 'background:'+c.bg+';color:'+c.color+';padding:14px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:500;min-width:280px;max-width:380px;animation:toastIn .3s ease;border-left:4px solid '+c.border+';pointer-events:all;';
    t.innerHTML = '<i class="fas fa-'+c.icon+'" style="flex-shrink:0;"></i><span>'+message+'</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;margin-left:auto;opacity:.7;padding:0;"><i class="fas fa-times"></i></button>';
    document.getElementById('toast-container').appendChild(t);
    setTimeout(function(){ t.style.animation='toastOut .3s ease forwards'; setTimeout(function(){ t.remove(); }, 300); }, 4000);
}
@if(session('success')) showToast(@json(session('success')), 'success'); @endif
@if(session('error'))   showToast(@json(session('error')),   'error');   @endif

function rejeterMouvement(id) {
    document.getElementById('form-rejeter').action = '/drh/validations/mouvements/' + id + '/rejeter';
    document.querySelector('#modal-rejeter textarea[name="motif_rejet"]').value = '';
    new bootstrap.Modal(document.getElementById('modal-rejeter')).show();
}

function voirMotif(id, motif) {
    document.getElementById('motif-content').textContent = motif || '—';
    new bootstrap.Modal(document.getElementById('modal-motif')).show();
}
</script>
@endpush
<style>.fw-500{font-weight:500!important;}.fw-600{font-weight:600!important;}</style>
@endsection
