@extends('layouts.master')
@section('title', 'Prises en charge médicales')
@section('page-title', 'Prises en charge médicales')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Prises en charge</li>
@endsection

@push('styles')
<style>
.kpi-card{border-radius:12px;padding:16px 20px;border:1px solid;transition:box-shadow 180ms,transform 180ms;}
.badge-attente{background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-valide{background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-approuve{background:#DBEAFE;color:#1E40AF;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-rejete{background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-hospital me-2" style="color:#0A4D8C;"></i>Prises en charge médicales
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Gestion des demandes de prise en charge des agents</p>
        </div>
        <a href="{{ route('pec.create') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
            <i class="fas fa-plus me-1"></i>Nouvelle PEC
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats rapides --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#EFF6FF;border-color:#DBEAFE;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Total</div>
                <div style="font-size:26px;font-weight:700;color:#0A4D8C;margin-top:4px;">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FFFBEB;border-color:#FDE68A;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">En attente</div>
                <div style="font-size:26px;font-weight:700;color:#D97706;margin-top:4px;">{{ $stats['attente'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#ECFDF5;border-color:#A7F3D0;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Validées</div>
                <div style="font-size:26px;font-weight:700;color:#059669;margin-top:4px;">{{ $stats['validees'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card" style="background:#FEF2F2;border-color:#FECACA;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Rejetées</div>
                <div style="font-size:26px;font-weight:700;color:#DC2626;margin-top:4px;">{{ $stats['rejetees'] }}</div>
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($prises->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Agent</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Bénéficiaire</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Type PEC</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Date</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Statut</th>
                            <th class="border-0 py-3 px-4 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prises as $pec)
                        @php
                            $statut = $pec->demande?->statut_demande ?? 'En_attente';
                            $badgeMap = [
                                'En_attente' => 'badge-attente',
                                'Validé'     => 'badge-valide',
                                'Approuvé'   => 'badge-approuve',
                                'Rejeté'     => 'badge-rejete',
                            ];
                            $statutLabels = [
                                'En_attente' => 'En attente',
                                'Validé'     => 'Validée',
                                'Approuvé'   => 'Approuvée',
                                'Rejeté'     => 'Rejetée',
                            ];
                        @endphp
                        <tr style="border-bottom:1px solid #F3F4F6;">
                            <td class="py-3 px-4 border-0">
                                <div style="font-weight:600;color:var(--theme-text);">{{ $pec->demande?->agent?->nom_complet ?? '—' }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $pec->demande?->agent?->matricule }}</div>
                            </td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $pec->beneficiaireLibelle }}</td>
                            <td class="py-3 px-4 border-0">{{ $pec->type_prise ?? '—' }}</td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $pec->created_at?->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 border-0">
                                <span class="{{ $badgeMap[$statut] ?? 'badge-attente' }}">
                                    {{ $statutLabels[$statut] ?? $statut }}
                                </span>
                                @if($pec->exceptionnelle)
                                <span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:20px;font-size:9px;font-weight:700;margin-left:4px;">Excep.</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-0 text-end">
                                <a href="{{ route('pec.show', $pec->id_priseenche) }}"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#EFF6FF;color:#1D4ED8;text-decoration:none;"
                                   title="Voir">
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
                <i class="fas fa-hospital" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
                <p class="text-muted mb-0">Aucune prise en charge enregistrée.</p>
                <a href="{{ route('pec.create') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-plus me-1"></i>Créer une prise en charge
                </a>
            </div>
            @endif
        </div>
        @if($prises->hasPages())
        <div class="card-footer bg-transparent px-4 py-3">{{ $prises->links() }}</div>
        @endif
    </div>

</div>
@endsection
