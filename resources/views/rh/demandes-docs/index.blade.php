@extends('layouts.master')
@section('title', 'Demandes de documents administratifs')
@section('page-title', 'Demandes de documents')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Demandes documents</li>
@endsection

@push('styles')
<style>
.badge-attente{background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-en_cours{background:#DBEAFE;color:#1E40AF;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-pret{background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-rejete{background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-alt me-2" style="color:#0A4D8C;"></i>Demandes de documents administratifs
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Traitement des demandes d'attestations, certificats et ordres de mission</p>
        </div>
        <a href="{{ route('rh.demandes-docs.pending') }}" class="btn btn-warning btn-sm" style="border-radius:8px;">
            <i class="fas fa-clock me-1"></i>En attente
            @if($stats['en_attente'] > 0)
            <span class="badge bg-danger ms-1">{{ $stats['en_attente'] }}</span>
            @endif
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div style="background:#EFF6FF;border:1px solid #DBEAFE;border-radius:12px;padding:16px 20px;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Total</div>
                <div style="font-size:26px;font-weight:700;color:#0A4D8C;margin-top:4px;">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:12px;padding:16px 20px;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">En attente</div>
                <div style="font-size:26px;font-weight:700;color:#D97706;margin-top:4px;">{{ $stats['en_attente'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:12px;padding:16px 20px;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Prêts</div>
                <div style="font-size:26px;font-weight:700;color:#059669;margin-top:4px;">{{ $stats['pret'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:16px 20px;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#6B7280;">Rejetés</div>
                <div style="font-size:26px;font-weight:700;color:#DC2626;margin-top:4px;">{{ $stats['rejete'] }}</div>
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @if($demandes->isNotEmpty())
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">#</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Agent</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Type document</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date demande</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Statut</th>
                            <th class="border-0 py-3 px-4 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandes as $dem)
                        <tr style="border-bottom:1px solid #F3F4F6;">
                            <td class="py-3 px-4 border-0 text-muted">#{{ $dem->id }}</td>
                            <td class="py-3 px-4 border-0">
                                <div style="font-weight:600;color:var(--theme-text);">{{ $dem->agent?->nom_complet ?? '—' }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $dem->agent?->matricule }}</div>
                            </td>
                            <td class="py-3 px-4 border-0">{{ $dem->libelleType }}</td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $dem->created_at?->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 border-0">
                                <span class="badge-{{ $dem->statut }}">{{ $dem->libelleStatut }}</span>
                            </td>
                            <td class="py-3 px-4 border-0 text-end">
                                <a href="{{ route('rh.demandes-docs.show', $dem->id) }}"
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
                <i class="fas fa-file-alt" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
                <p class="text-muted mb-0">Aucune demande de document enregistrée.</p>
            </div>
            @endif
        </div>
        @if($demandes->hasPages())
        <div class="card-footer bg-transparent px-4 py-3">{{ $demandes->links() }}</div>
        @endif
    </div>

</div>
@endsection
