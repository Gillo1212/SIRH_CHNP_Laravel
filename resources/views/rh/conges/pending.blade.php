@extends('layouts.master')

@section('title', 'Congés à Approuver')
@section('page-title', 'Congés à Approuver')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>À approuver</li>
@endsection

@push('styles')
<style>
.demande-card { border-radius:12px;transition:box-shadow 200ms;border-left:4px solid #3B82F6; }
.demande-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10); }
.avatar-placeholder { width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0; }
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

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Congés à approuver</h4>
            <p class="text-muted small mb-0">Demandes validées par les Managers, en attente d'approbation finale RH</p>
        </div>
        @if($pending->count() > 0)
            <span class="badge rounded-pill" style="background:#DBEAFE;color:#1E40AF;padding:8px 14px;font-size:13px;">
                <i class="fas fa-user-check me-1"></i>{{ $pending->count() }} à approuver
            </span>
        @endif
    </div>

    @if($pending->count() > 0)
        <div class="row g-3">
            @foreach($pending as $demande)
                @php $conge = $demande->conge; $agent = $demande->agent; @endphp
                <div class="col-12">
                    <div class="demande-card card border-0 shadow-sm" style="background:var(--theme-panel-bg);">
                        <div class="card-body py-3 px-4">
                            <div class="row align-items-center g-3">
                                {{-- Agent --}}
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($agent->photo)
                                            <img src="{{ asset('storage/'.$agent->photo) }}" class="avatar-placeholder" style="object-fit:cover;">
                                        @else
                                            <div class="avatar-placeholder" style="background:#EFF6FF;color:#0A4D8C;">
                                                {{ strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold small" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->matricule }}</div>
                                            <div class="text-muted" style="font-size:11px;">{{ $agent->service->nom_service ?? '—' }} — {{ $agent->fonction ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Détails --}}
                                <div class="col-lg-4">
                                    <div class="fw-600 small" style="color:var(--theme-text);">{{ $conge->typeConge->libelle ?? '—' }}</div>
                                    <div class="text-muted small">
                                        Du <strong>{{ $conge->date_debut?->format('d/m/Y') }}</strong>
                                        au <strong>{{ $conge->date_fin?->format('d/m/Y') }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                        <span class="badge" style="background:#DBEAFE;color:#1E40AF;font-size:11px;">
                                            <i class="fas fa-calendar me-1"></i>{{ $conge->nbres_jours }} jour(s)
                                        </span>
                                        <span class="badge" style="background:#D1FAE5;color:#065F46;font-size:10px;">
                                            <i class="fas fa-user-check me-1"></i>Manager validé
                                        </span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        Demandé le {{ $demande->created_at->format('d/m/Y') }}
                                        | Validé le {{ \Carbon\Carbon::parse($demande->date_traitement)->format('d/m/Y') }}
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="col-lg-4 d-flex gap-2 justify-content-lg-end flex-wrap">
                                    <a href="{{ route('rh.conges.show', $demande->id_demande) }}" class="btn btn-sm d-flex align-items-center gap-2" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;padding:8px 14px;font-size:12px;color:var(--theme-text);">
                                        <i class="fas fa-eye"></i> Voir détail
                                    </a>

                                    {{-- Approuver directement --}}
                                    <form action="{{ route('rh.conges.approuver', $demande->id_demande) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm d-flex align-items-center gap-2"
                                            style="background:#10B981;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;"
                                            onclick="return confirm('Approuver le congé de {{ $agent->nom_complet }} ({{ $conge->nbres_jours }}j) et déduire du solde ?')">
                                            <i class="fas fa-check-double"></i> Approuver
                                        </button>
                                    </form>

                                    {{-- Rejeter --}}
                                    <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                        style="background:#FEE2E2;color:#991B1B;border:none;border-radius:8px;padding:8px 14px;font-size:12px;"
                                        data-bs-toggle="modal" data-bs-target="#rejetModal{{ $demande->id_demande }}">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal rejet RH --}}
                <div class="modal fade" id="rejetModal{{ $demande->id_demande }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius:14px;">
                            <div class="modal-header border-0">
                                <h6 class="modal-title fw-bold">
                                    <i class="fas fa-times-circle me-2 text-danger"></i>Rejeter la demande
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('rh.conges.rejeter', $demande->id_demande) }}" method="POST">
                                @csrf
                                <div class="modal-body px-4">
                                    <p class="text-muted small mb-3">
                                        Rejet du congé de <strong>{{ $agent->nom_complet }}</strong> — {{ $conge->nbres_jours }}j.
                                    </p>
                                    <div class="mb-3">
                                        <label class="form-label fw-600 small">Motif du rejet <span class="text-danger">*</span></label>
                                        <textarea name="motif_refus" rows="3" class="form-control" style="border-radius:8px;font-size:13px;"
                                            placeholder="Expliquez le motif (minimum 10 caractères)…" required minlength="10"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times me-1"></i>Rejeter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x mb-3 d-block" style="color:#10B981;opacity:.5;"></i>
                <h5 class="fw-bold" style="color:var(--theme-text);">Aucun congé en attente d'approbation</h5>
                <p class="text-muted small">Toutes les demandes ont été traitées.</p>
                <a href="{{ route('rh.conges.index') }}" class="btn btn-sm mt-2" style="background:#0A4D8C;color:#fff;border:none;border-radius:8px;">
                    Voir l'historique complet
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
