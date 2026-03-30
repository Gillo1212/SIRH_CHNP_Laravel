@extends('layouts.master')

@section('title', 'Ma Famille')
@section('page-title', 'Ma Famille')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.profil') }}" style="color:#1565C0;">Mon profil</a></li>
    <li>Ma famille</li>
@endsection

@push('styles')
<style>
.s-title { font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--theme-border);display:flex;align-items:center;gap:8px; }
.fam-avatar { width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0; }
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-lg-8">

    {{-- Conjoints --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-body p-4">
            <div class="s-title">
                <i class="fas fa-ring" style="color:#0A4D8C;"></i>
                Conjoint(s)
                <span style="background:var(--theme-bg-secondary);color:var(--theme-text-muted);border-radius:10px;padding:1px 8px;font-size:11px;">{{ $agent->conjoints->count() }}</span>
            </div>

            @forelse($agent->conjoints as $conjoint)
            <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="fam-avatar" style="background:#EFF6FF;color:#0A4D8C;">
                    {{ strtoupper(substr($conjoint->prenom_conj,0,1).substr($conjoint->nom_conj,0,1)) }}
                </div>
                <div>
                    <div class="fw-600" style="color:var(--theme-text);font-size:14px;">
                        {{ $conjoint->prenom_conj }} {{ $conjoint->nom_conj }}
                    </div>
                    <div style="font-size:12px;color:var(--theme-text-muted);">
                        @if($conjoint->type_lien) {{ $conjoint->type_lien }} · @endif
                        @if($conjoint->date_naissance_conj)
                            Né(e) le {{ $conjoint->date_naissance_conj->format('d/m/Y') }}
                            ({{ $conjoint->age }} ans)
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4" style="color:var(--theme-text-muted);">
                <i class="fas fa-ring fa-2x mb-2 d-block" style="opacity:.2;"></i>
                <small>Aucun conjoint enregistré</small>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Enfants --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-body p-4">
            <div class="s-title">
                <i class="fas fa-child" style="color:#10B981;"></i>
                Enfant(s)
                <span style="background:var(--theme-bg-secondary);color:var(--theme-text-muted);border-radius:10px;padding:1px 8px;font-size:11px;">{{ $agent->enfants->count() }}</span>
            </div>

            @forelse($agent->enfants as $enfant)
            <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="fam-avatar" style="background:#ECFDF5;color:#059669;">
                    {{ strtoupper(substr($enfant->prenom_complet, 0, 2)) }}
                </div>
                <div>
                    <div class="fw-600" style="color:var(--theme-text);font-size:14px;">
                        {{ $enfant->prenom_complet }}
                    </div>
                    <div style="font-size:12px;color:var(--theme-text-muted);">
                        @if($enfant->lien_filiation) {{ $enfant->lien_filiation }} · @endif
                        @if($enfant->date_naissance_enfant)
                            Né(e) le {{ $enfant->date_naissance_enfant->format('d/m/Y') }}
                            ({{ $enfant->age }} ans)
                            @if($enfant->est_mineur)
                                <span style="background:#FEF3C7;color:#92400E;padding:1px 6px;border-radius:6px;font-size:10px;">Mineur</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4" style="color:var(--theme-text-muted);">
                <i class="fas fa-child fa-2x mb-2 d-block" style="opacity:.2;"></i>
                <small>Aucun enfant enregistré</small>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Note --}}
    <div class="alert d-flex align-items-start gap-3" style="border-radius:10px;background:#F0F9FF;border:1px solid #BAE6FD;">
        <i class="fas fa-info-circle mt-1" style="color:#0284C7;font-size:16px;"></i>
        <div style="font-size:13px;color:#075985;">
            <strong>Mise à jour de situation familiale</strong><br>
            Pour toute modification (naissance, mariage, divorce…), contactez le service RH avec les justificatifs.
            <div class="mt-2">
                <a href="{{ route('agent.docs.create') }}" class="action-btn action-btn-primary" style="font-size:12px;padding:7px 14px;">
                    <i class="fas fa-paper-plane"></i> Contacter le RH
                </a>
            </div>
        </div>
    </div>

</div>
</div>
</div>
@endsection
