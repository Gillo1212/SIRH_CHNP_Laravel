@extends('layouts.master')
@section('title', 'Historique des prises en charge')
@section('page-title', 'Historique PEC')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('pec.index') }}" style="color:#1565C0;">Prises en charge</a></li>
    <li>Historique</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">
            <i class="fas fa-history me-2" style="color:#0A4D8C;"></i>Historique des prises en charge
        </h4>
        <a href="{{ route('pec.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($prises->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Agent</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Bénéficiaire</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Type PEC</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Statut</th>
                            <th class="border-0 py-3 px-4 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prises as $pec)
                        @php
                            $statut = $pec->demande?->statut_demande ?? 'En_attente';
                            $badgeClass = match($statut) {
                                'Validé'   => 'badge-valide',
                                'Approuvé' => 'badge-approuve',
                                'Rejeté'   => 'badge-rejete',
                                default    => 'badge-attente',
                            };
                            $statutLabel = match($statut) {
                                'Validé'   => 'Validée',
                                'Approuvé' => 'Approuvée DRH',
                                'Rejeté'   => 'Rejetée',
                                default    => 'En attente',
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #F3F4F6;">
                            <td class="py-3 px-4 border-0">
                                <div style="font-weight:600;">{{ $pec->demande?->agent?->nom_complet ?? '—' }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $pec->demande?->agent?->matricule }}</div>
                            </td>
                            <td class="py-3 px-4 border-0 text-muted">{{ ucfirst($pec->ayant_droit ?? '—') }}</td>
                            <td class="py-3 px-4 border-0">{{ $pec->type_prise ?? '—' }}</td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $pec->created_at?->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 border-0">
                                <span style="padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;
                                    background:{{ $statut === 'Validé' ? '#D1FAE5' : ($statut === 'Approuvé' ? '#DBEAFE' : '#FEE2E2') }};
                                    color:{{ $statut === 'Validé' ? '#065F46' : ($statut === 'Approuvé' ? '#1E40AF' : '#991B1B') }};">
                                    {{ $statutLabel }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0 text-end">
                                <a href="{{ route('pec.show', $pec->id_priseenche) }}"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#EFF6FF;color:#1D4ED8;text-decoration:none;">
                                    <i class="fas fa-eye" style="font-size:11px;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-history" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
                <p class="text-muted mb-0">Aucune prise en charge dans l'historique.</p>
            </div>
            @endif
        </div>
        @if($prises->hasPages())
        <div class="card-footer bg-transparent px-4 py-3">{{ $prises->links() }}</div>
        @endif
    </div>

</div>
@endpush

@push('styles')
<style>
.badge-valide{background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-approuve{background:#DBEAFE;color:#1E40AF;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-rejete{background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-attente{background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
</style>
@endpush
@endsection
