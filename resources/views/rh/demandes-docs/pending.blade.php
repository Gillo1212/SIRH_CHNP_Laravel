@extends('layouts.master')
@section('title', 'Demandes en attente')
@section('page-title', 'Demandes documents — En attente')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.demandes-docs.index') }}" style="color:#1565C0;">Demandes documents</a></li>
    <li>En attente</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-clock me-2" style="color:#D97706;"></i>Demandes en attente de traitement
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">{{ $demandes->total() }} demande(s) à traiter</p>
        </div>
        <a href="{{ route('rh.demandes-docs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($demandes->isEmpty())
    <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:12px;padding:40px 24px;text-align:center;">
        <i class="fas fa-check-double" style="font-size:40px;color:#059669;margin-bottom:12px;display:block;"></i>
        <div style="font-size:15px;font-weight:600;color:#065F46;margin-bottom:6px;">Aucune demande en attente</div>
        <div style="font-size:13px;color:#6B7280;">Toutes les demandes ont été traitées.</div>
    </div>
    @else
    @foreach($demandes as $dem)
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:12px;transition:box-shadow 180ms;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div style="font-weight:600;font-size:14px;color:var(--theme-text);">
                    {{ $dem->agent?->nom_complet ?? '—' }}
                    <span style="font-size:12px;color:#9CA3AF;font-weight:400;margin-left:8px;">{{ $dem->agent?->matricule }}</span>
                </div>
                <div style="font-size:13px;color:#6B7280;margin-top:3px;">
                    <span style="font-weight:600;color:#1D4ED8;">{{ $dem->libelleType }}</span>
                    · Demandé le {{ $dem->created_at?->format('d/m/Y') }}
                </div>
                @if($dem->motif)
                <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">{{ Str::limit($dem->motif, 80) }}</div>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('rh.demandes-docs.show', $dem->id) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-eye me-1"></i>Voir
                </a>
                <form action="{{ route('rh.demandes-docs.traiter', $dem->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Marquer cette demande comme traitée ?')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check me-1"></i>Traiter
                    </button>
                </form>
                <form action="{{ route('rh.demandes-docs.rejeter', $dem->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Rejeter cette demande ?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-times me-1"></i>Rejeter
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @if($demandes->hasPages())
    <div class="mt-3">{{ $demandes->links() }}</div>
    @endif
    @endif

</div>
@endsection
