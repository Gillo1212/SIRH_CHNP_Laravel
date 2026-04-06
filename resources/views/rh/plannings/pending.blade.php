@extends('layouts.master')

@section('title', 'Plannings reçus')
@section('page-title', 'Plannings - Vue informative')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.plannings.index') }}" style="color:#1565C0;">Plannings</a></li>
    <li>À valider</li>
@endsection

@push('styles')
<style>
.panel { background:white;border-radius:12px;padding:20px;border:1px solid #F3F4F6;box-shadow:0 1px 4px rgba(0,0,0,.04); }
.action-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-primary { background:#0A4D8C;color:white; }
.action-btn-primary:hover { background:#1565C0;color:white;box-shadow:0 4px 12px rgba(10,77,140,.3);transform:translateY(-1px); }
.action-btn-outline { background:white;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;color:#9CA3AF; }
.planning-pending-card {
    background:white; border-radius:14px; border:1px solid #F3F4F6;
    box-shadow:0 1px 4px rgba(0,0,0,.04); transition:box-shadow 200ms,transform 200ms;
    overflow:hidden;
}
.planning-pending-card:hover { box-shadow:0 4px 16px rgba(10,77,140,.08);transform:translateY(-1px); }
.card-header-stripe { height:4px;background:linear-gradient(90deg,#D97706,#F59E0B); }
.agent-avatar { width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;flex-shrink:0; }
.stat-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#F3F4F6;color:#374151; }
[data-theme="dark"] .panel { background:#161b22;border-color:#30363d; }
[data-theme="dark"] .planning-pending-card { background:#161b22;border-color:#30363d; }
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

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0" style="color:#111827;">
                Plannings reçus
                <span style="margin-left:8px;padding:3px 12px;border-radius:20px;background:#EFF6FF;color:#1D4ED8;font-size:14px;font-weight:700;">{{ $count }}</span>
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Plannings des services - reçus à titre informatif (validation par les Managers)</p>
        </div>
        <a href="{{ route('rh.plannings.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-th-list"></i>Tous les plannings
        </a>
    </div>

    {{-- Bandeau d'information --}}
    @if($count > 0)
        <div style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #BFDBFE;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-info-circle" style="color:#1D4ED8;font-size:16px;"></i>
            </div>
            <div>
                <div style="font-weight:600;color:#1E40AF;font-size:14px;">{{ $count }} planning(s) reçus à titre informatif</div>
                <div style="font-size:12px;color:#2563EB;">Les plannings sont validés par les Managers de service. Vous pouvez les consulter ci-dessous.</div>
            </div>
        </div>
    @endif

    {{-- Liste des plannings en attente --}}
    @forelse($plannings as $planning)
        @php
            $nbAgents = $planning->lignes_count > 0
                ? \App\Models\LignePlanning::where('id_planning', $planning->id_planning)->distinct('id_agent')->count('id_agent')
                : 0;
        @endphp
        <div class="planning-pending-card mb-3">
            <div class="card-header-stripe"></div>
            <div class="p-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">

                    {{-- Infos planning --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div style="width:42px;height:42px;border-radius:10px;background:#FFFBEB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-calendar-week" style="color:#D97706;font-size:16px;"></i>
                            </div>
                            <div>
                                <div class="fw-bold" style="color:#111827;font-size:15px;">
                                    {{ $planning->service->nom_service ?? 'Service inconnu' }}
                                </div>
                                <div style="font-size:13px;color:#6B7280;">
                                    {{ $planning->periode_debut->isoFormat('D MMMM') }} → {{ $planning->periode_fin->isoFormat('D MMMM YYYY') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
                            <span class="stat-chip">
                                <i class="fas fa-calendar" style="color:#6B7280;"></i>{{ $planning->duree_jours }} jour(s)
                            </span>
                            <span class="stat-chip">
                                <i class="fas fa-list" style="color:#6B7280;"></i>{{ $planning->lignes_count }} ligne(s)
                            </span>
                            <span class="stat-chip">
                                <i class="fas fa-users" style="color:#6B7280;"></i>{{ $nbAgents }} agent(s)
                            </span>
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#FFFBEB;color:#D97706;">
                                <i class="fas fa-paper-plane" style="font-size:9px;"></i>Transmis le {{ $planning->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @php
                            $badge = match($planning->statut_planning) {
                                'Validé'   => ['bg'=>'#ECFDF5','c'=>'#059669','ic'=>'fa-check-double'],
                                'Rejeté'   => ['bg'=>'#FEF2F2','c'=>'#DC2626','ic'=>'fa-times-circle'],
                                default    => ['bg'=>'#FFFBEB','c'=>'#D97706','ic'=>'fa-paper-plane'],
                            };
                        @endphp
                        <span style="padding:4px 12px;border-radius:20px;background:{{ $badge['bg'] }};color:{{ $badge['c'] }};font-size:11px;font-weight:600;">
                            <i class="fas {{ $badge['ic'] }} me-1" style="font-size:9px;"></i>{{ $planning->statut_planning }}
                        </span>
                        <a href="{{ route('rh.plannings.show', $planning->id_planning) }}"
                           class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                            <i class="fas fa-eye"></i>Voir le détail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="panel text-center py-5">
            <div style="width:80px;height:80px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <i class="fas fa-calendar-check fa-2x" style="color:#1D4ED8;"></i>
            </div>
            <h5 class="fw-bold mb-1" style="color:#111827;">Aucun planning reçu</h5>
            <p class="text-muted mb-4" style="font-size:13px;">Aucun planning transmis pour le moment.</p>
            <a href="{{ route('rh.plannings.index') }}" class="action-btn action-btn-outline">
                <i class="fas fa-th-list"></i>Voir tous les plannings
            </a>
        </div>
    @endforelse

    @if($plannings->hasPages())
        <div class="mt-4">{{ $plannings->links() }}</div>
    @endif

</div>

@endsection
