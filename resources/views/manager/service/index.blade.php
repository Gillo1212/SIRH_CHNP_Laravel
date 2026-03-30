@extends('layouts.master')

@section('title', 'Mon Service — ' . $service->nom_service)
@section('page-title', 'Mon Service')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li>Mon Service</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px;padding:20px 24px;position:relative;overflow:hidden;transition:box-shadow 200ms,transform 200ms; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.1);transform:translateY(-2px); }
.kpi-card .kpi-icon { width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-card .kpi-val { font-size:28px;font-weight:700;line-height:1.1;margin-top:10px; }
.kpi-card .kpi-lbl { font-size:12px;font-weight:500;margin-top:2px; }
.demande-item { display:flex;align-items:center;padding:10px 0;border-bottom:1px solid #F3F4F6; }
.demande-item:last-child { border-bottom:none; }
.avatar-sm { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" style="border-radius:10px;border-left:4px solid #10B981;">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- En-tête service --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-white mb-1">
                        <i class="fas fa-hospital-alt me-2"></i>{{ $service->nom_service }}
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span style="background:rgba(255,255,255,0.2);color:white;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                            {{ $service->type_service }}
                        </span>
                        @if($service->division)
                            <span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.8);font-size:11px;padding:3px 10px;border-radius:20px;">
                                <i class="fas fa-sitemap me-1"></i>{{ $service->division->nom_division }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('manager.service.agents') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:8px;">
                        <i class="fas fa-users me-1"></i>Mon équipe
                    </a>
                    <a href="{{ route('manager.service.statistics') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:8px;">
                        <i class="fas fa-chart-bar me-1"></i>Statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-users" style="color:#1E40AF;"></i></div>
                </div>
                <div class="kpi-val" style="color:#0A4D8C;">{{ $stats['total_agents'] }}</div>
                <div class="kpi-lbl text-muted">Agents</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FFFBEB;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-clock" style="color:#D97706;"></i></div>
                    @if($stats['pending_leaves'] > 0)
                        <span style="background:#FEF3C7;color:#92400E;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">Action</span>
                    @endif
                </div>
                <div class="kpi-val" style="color:#D97706;">{{ $stats['pending_leaves'] }}</div>
                <div class="kpi-lbl text-muted">Congés à valider</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-user-minus" style="color:#DC2626;"></i></div>
                </div>
                <div class="kpi-val" style="color:#DC2626;">{{ $stats['today_absences'] }}</div>
                <div class="kpi-lbl text-muted">Absents aujourd'hui</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-percentage" style="color:#059669;"></i></div>
                </div>
                <div class="kpi-val" style="color:#059669;">{{ $stats['attendance_rate'] }}%</div>
                <div class="kpi-lbl text-muted">Taux présence</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Demandes en attente --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-clipboard-list me-2 text-warning"></i>Congés à valider
                        @if($demandesPending->count() > 0)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">{{ $demandesPending->count() }}</span>
                        @endif
                    </h6>
                    <a href="{{ route('manager.conges.pending') }}" class="text-primary small fw-500">Tout voir →</a>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($demandesPending as $demande)
                        @php $agent = $demande->agent; $conge = $demande->conge; @endphp
                        <div class="demande-item">
                            <div class="avatar-sm me-3" style="background:#EFF6FF;color:#0A4D8C;">
                                {{ strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-500 small">{{ $agent->nom_complet }}</div>
                                <div class="text-muted" style="font-size:11px;">
                                    {{ $conge?->typeConge?->libelle ?? 'Congé' }} ·
                                    @if($conge) {{ $conge->date_debut?->format('d/m') }} → {{ $conge->date_fin?->format('d/m/Y') }} @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1 flex-shrink-0">
                                <form action="{{ route('manager.conges.valider', $demande->id_demande) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm" style="background:#ECFDF5;color:#059669;border:none;border-radius:6px;padding:4px 8px;"
                                            title="Valider">
                                        <i class="fas fa-check" style="font-size:11px;"></i>
                                    </button>
                                </form>
                                <button class="btn btn-sm" style="background:#FEF2F2;color:#DC2626;border:none;border-radius:6px;padding:4px 8px;"
                                        data-bs-toggle="modal" data-bs-target="#rejetModal{{ $demande->id_demande }}" title="Rejeter">
                                    <i class="fas fa-times" style="font-size:11px;"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Modal rejet --}}
                        <div class="modal fade" id="rejetModal{{ $demande->id_demande }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius:14px;">
                                    <div class="modal-header border-0">
                                        <h6 class="modal-title fw-bold"><i class="fas fa-times-circle text-danger me-2"></i>Rejeter le congé</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('manager.conges.rejeter', $demande->id_demande) }}" method="POST">
                                        @csrf
                                        <div class="modal-body px-4">
                                            <p class="text-muted small mb-3">Motif de rejet pour <strong>{{ $agent->nom_complet }}</strong></p>
                                            <textarea name="motif_refus" rows="3" class="form-control" style="border-radius:8px;font-size:13px;"
                                                placeholder="Motif obligatoire (min 10 caractères)…" required minlength="10"></textarea>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-sm btn-danger">Confirmer le rejet</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-double fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                            <small>Aucune demande en attente</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Absences aujourd'hui --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0"><i class="fas fa-user-minus me-2 text-danger"></i>Absences — Aujourd'hui</h6>
                    <a href="{{ route('manager.absences.create') }}" class="btn btn-sm btn-outline-danger" style="border-radius:6px;font-size:12px;">
                        <i class="fas fa-plus me-1"></i>Enregistrer
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($absencesAujourdhui as $absence)
                        <div class="demande-item">
                            <div class="avatar-sm me-3" style="background:#FEF2F2;color:#DC2626;">
                                {{ strtoupper(substr($absence->demande->agent->prenom ?? 'A', 0, 1) . substr($absence->demande->agent->nom ?? '', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-500 small">{{ $absence->demande->agent->nom_complet ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $absence->type_absence }}</div>
                            </div>
                            <span class="badge {{ $absence->justifie ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px;">
                                {{ $absence->justifie ? 'Justifiée' : 'Non justifiée' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-check fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                            <small>Aucune absence aujourd'hui</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div class="mt-4 p-4" style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;">
        <div class="fw-600 mb-3" style="color:#0A4D8C;">Actions rapides</div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('manager.conges.pending') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-clipboard-check"></i>Valider congés
            </a>
            <a href="{{ route('manager.absences.create') }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-user-minus"></i>Enregistrer absence
            </a>
            <a href="{{ route('manager.planning.create') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-calendar-plus"></i>Créer planning
            </a>
            <a href="{{ route('manager.service.agents') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-users"></i>Mon équipe
            </a>
            <a href="{{ route('manager.service.statistics') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-chart-bar"></i>Statistiques
            </a>
        </div>
    </div>

</div>
@endsection
