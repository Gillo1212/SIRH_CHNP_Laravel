@extends('layouts.master')
@section('title', 'Mon équipe')
@section('page-title', 'Mon Équipe')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li>Mon équipe</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:16px 20px;border:1px solid;transition:box-shadow 180ms,transform 180ms;}
.kpi-card:hover{box-shadow:0 4px 16px rgba(10,77,140,.10);transform:translateY(-2px);}
.agent-row{transition:background 150ms;}
.agent-row:hover{background:#F9FAFB!important;}
.badge-actif{background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-conge{background:#DBEAFE;color:#1E40AF;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-suspendu{background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-retraite{background:#F3F4F6;color:#374151;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-users me-2" style="color:#0A4D8C;"></i>Mon Équipe
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Service : <strong>{{ $service->nom_service }}</strong>
                @if($service->division) · {{ $service->division->nom_division }} @endif
            </p>
        </div>
        <a href="{{ route('manager.equipe.dossiers') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-folder me-1"></i>Voir les dossiers
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Total équipe</div>
                <div style="font-size:28px;font-weight:700;color:#0A4D8C;margin-top:6px;">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;border-color:#A7F3D0;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Actifs</div>
                <div style="font-size:28px;font-weight:700;color:#059669;margin-top:6px;">{{ $stats['actifs'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#BFDBFE;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">En congé</div>
                <div style="font-size:28px;font-weight:700;color:#1D4ED8;margin-top:6px;">{{ $stats['en_conge'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;border-color:#FECACA;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Suspendus</div>
                <div style="font-size:28px;font-weight:700;color:#DC2626;margin-top:6px;">{{ $stats['suspendus'] }}</div>
            </div>
        </div>
    </div>

    {{-- Tableau agents --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="background:#fff;border-bottom:1px solid #F3F4F6;">
            <h6 class="mb-0 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-list me-2" style="color:#0A4D8C;"></i>Liste des agents
            </h6>
        </div>
        <div class="card-body p-0">
            @if($agents->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Agent</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Fonction</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Contrat</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Statut</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                        <tr class="agent-row">
                            <td class="py-3 px-4 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    @if($agent->photo)
                                    <img src="{{ Storage::url($agent->photo) }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                    @else
                                    <div style="width:36px;height:36px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;color:#0A4D8C;font-size:13px;">
                                        {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->nom, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600;color:var(--theme-text);">{{ $agent->prenom }} {{ $agent->nom }}</div>
                                        <div style="font-size:11px;color:#9CA3AF;">{{ $agent->matricule }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 border-0" style="color:var(--theme-text);">{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') }}</td>
                            <td class="py-3 px-4 border-0">
                                @if($agent->contratActif)
                                <span style="font-size:11px;background:#EFF6FF;color:#1E40AF;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $agent->contratActif->type_contrat }}</span>
                                @else
                                <span class="text-muted" style="font-size:12px;">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-0">
                                @php
                                    $sBadges = ['actif'=>'badge-actif','en_conge'=>'badge-conge','suspendu'=>'badge-suspendu','retraite'=>'badge-retraite'];
                                    $sLabels = ['actif'=>'Actif','en_conge'=>'En congé','suspendu'=>'Suspendu','retraite'=>'Retraité'];
                                @endphp
                                <span class="{{ $sBadges[$agent->statut_agent] ?? 'badge-retraite' }}">
                                    {{ $sLabels[$agent->statut_agent] ?? $agent->statut_agent }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0 text-end">
                                <a href="{{ route('manager.equipe.show', $agent->id_agent) }}"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#EFF6FF;color:#1D4ED8;text-decoration:none;"
                                   title="Voir le dossier">
                                    <i class="fas fa-eye" style="font-size:12px;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-users" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
                <p class="mb-0 text-muted">Aucun agent dans votre service.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
