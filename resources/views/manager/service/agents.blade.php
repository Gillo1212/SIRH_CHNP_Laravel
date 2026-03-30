@extends('layouts.master')

@section('title', 'Équipe — ' . $service->nom_service)
@section('page-title', 'Mon Équipe')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.service.index') }}" style="color:#1565C0;">Mon Service</a></li>
    <li>Équipe</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $service->nom_service }}</h4>
            <p class="text-muted small mb-0">{{ $agents->count() }} agent(s) dans votre service</p>
        </div>
        <a href="{{ route('manager.service.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    {{-- Stats rapides --}}
    <div class="row g-3 mb-4">
        @php
            $actifs   = $agents->where('statut', 'actif')->count();
            $enConge  = $agents->where('statut', 'en_conge')->count();
            $autres   = $agents->count() - $actifs - $enConge;
        @endphp
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <div style="font-size:24px;font-weight:700;color:#059669;">{{ $actifs }}</div>
                <div class="text-muted small">Actifs</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <div style="font-size:24px;font-weight:700;color:#D97706;">{{ $enConge }}</div>
                <div class="text-muted small">En congé</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <div style="font-size:24px;font-weight:700;color:#6B7280;">{{ $autres }}</div>
                <div class="text-muted small">Autres</div>
            </div>
        </div>
    </div>

    {{-- Table agents --}}
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr style="background:#F8FAFC;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#6B7280;">
                            <th class="px-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Matricule</th>
                            <th class="py-3 border-0">Fonction</th>
                            <th class="py-3 border-0">Contrat</th>
                            <th class="py-3 border-0">Statut</th>
                            <th class="py-3 border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                            <tr style="border-bottom:1px solid #F3F4F6;">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;flex-shrink:0;">
                                            {{ strtoupper(substr($agent->prenom, 0, 1) . substr($agent->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-600">{{ $agent->nom_complet }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 border-0 text-muted">{{ $agent->matricule }}</td>
                                <td class="py-3 border-0">{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') ?? '—' }}</td>
                                <td class="py-3 border-0">
                                    @if($agent->contratActif)
                                        <span style="font-size:11px;background:#ECFDF5;color:#065F46;padding:2px 8px;border-radius:20px;font-weight:600;">
                                            {{ $agent->contratActif->type_contrat ?? 'Actif' }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="py-3 border-0">
                                    @php
                                        $statuts = [
                                            'actif'     => ['bg' => '#ECFDF5', 'color' => '#065F46', 'label' => 'Actif'],
                                            'en_conge'  => ['bg' => '#FFFBEB', 'color' => '#92400E', 'label' => 'En congé'],
                                            'suspendu'  => ['bg' => '#FEF2F2', 'color' => '#991B1B', 'label' => 'Suspendu'],
                                            'retraite'  => ['bg' => '#F3F4F6', 'color' => '#6B7280', 'label' => 'Retraité'],
                                        ];
                                        $s = $statuts[$agent->statut_agent] ?? ['bg' => '#F3F4F6', 'color' => '#374151', 'label' => ucfirst($agent->statut_agent)];
                                    @endphp
                                    <span style="font-size:11px;background:{{ $s['bg'] }};color:{{ $s['color'] }};padding:2px 8px;border-radius:20px;font-weight:600;">
                                        {{ $s['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 border-0">
                                    <a href="{{ route('manager.equipe.show', $agent->id_agent) }}" class="btn btn-sm btn-light" style="border-radius:6px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted border-0">
                                    <i class="fas fa-users fa-2x mb-2 d-block" style="color:#D1D5DB;"></i>
                                    Aucun agent dans ce service
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
