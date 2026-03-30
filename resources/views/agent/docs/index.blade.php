@extends('layouts.master')
@section('title', 'Mes demandes de documents administratifs')
@section('page-title', 'Mes documents administratifs')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li>Documents administratifs</li>
@endsection

@push('styles')
<style>
.badge-en_attente{background:#FEF3C7;color:#92400E;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
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
                <i class="fas fa-file-alt me-2" style="color:#0A4D8C;"></i>Mes demandes de documents
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Attestations de travail, certificats et ordres de mission</p>
        </div>
        <a href="{{ route('agent.docs.create') }}" class="btn btn-primary btn-sm" style="border-radius:8px;">
            <i class="fas fa-plus me-1"></i>Nouvelle demande
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:16px 20px;margin-bottom:20px;font-size:13px;color:#1E40AF;">
        <i class="fas fa-info-circle me-2"></i>
        Soumettez votre demande en ligne. Le service RH vous préparera le document et vous serez notifié lorsqu'il sera prêt.
    </div>

    @if($demandes->isEmpty())
    <div class="text-center py-5" style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;">
        <i class="fas fa-file-alt" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
        <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">Aucune demande</div>
        <p class="text-muted mb-3" style="font-size:13px;">Vous n'avez pas encore soumis de demande de document administratif.</p>
        <a href="{{ route('agent.docs.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Faire une demande
        </a>
    </div>
    @else
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;">
        <table class="table mb-0" style="font-size:13px;">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Type de document</th>
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Motif</th>
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date</th>
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Statut</th>
                    <th class="border-0 py-3 px-4 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandes as $dem)
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td class="py-3 px-4 border-0">
                        <div style="font-weight:600;color:var(--theme-text);">{{ $dem->libelleType }}</div>
                    </td>
                    <td class="py-3 px-4 border-0 text-muted">{{ $dem->motif ? Str::limit($dem->motif, 50) : '—' }}</td>
                    <td class="py-3 px-4 border-0 text-muted">{{ $dem->created_at?->format('d/m/Y') }}</td>
                    <td class="py-3 px-4 border-0">
                        <span class="badge-{{ $dem->statut }}">{{ $dem->libelleStatut }}</span>
                    </td>
                    <td class="py-3 px-4 border-0 text-end">
                        <a href="{{ route('agent.docs.show', $dem->id) }}"
                           style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#EFF6FF;color:#1D4ED8;text-decoration:none;"
                           title="Voir">
                            <i class="fas fa-eye" style="font-size:11px;"></i>
                        </a>
                        @if($dem->statut === 'pret')
                        <a href="{{ route('agent.docs.download', $dem->id) }}"
                           style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:#D1FAE5;color:#065F46;text-decoration:none;margin-left:4px;"
                           title="Télécharger">
                            <i class="fas fa-download" style="font-size:11px;"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($demandes->hasPages())
        <div class="px-4 py-3 border-top">{{ $demandes->links() }}</div>
        @endif
    </div>
    @endif

</div>
@endsection
