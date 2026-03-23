@extends('layouts.master')

@section('title', 'Ma Famille')
@section('page-title', 'Ma Famille')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.profil') }}" style="color:#1565C0;">Mon profil</a></li>
    <li>Ma famille</li>
@endsection

@section('content')

{{-- Conjoints ─────────────────────────────────────────── --}}
<div class="panel p-4 mb-4">
    <h6 class="fw-600 mb-3" style="font-size:14px;">
        <i class="fas fa-ring me-2" style="color:#0A4D8C;"></i>Conjoint(s)
        <span class="ms-2" style="font-size:12px;color:#9CA3AF;">({{ $agent->conjoints->count() }})</span>
    </h6>

    @forelse($agent->conjoints as $conjoint)
    <div class="d-flex align-items-start gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:42px;height:42px;background:#EFF6FF;color:#0A4D8C;font-weight:700;font-size:14px;">
            {{ strtoupper(substr($conjoint->prenom,0,1).substr($conjoint->nom,0,1)) }}
        </div>
        <div>
            <div style="font-weight:600;font-size:14px;">{{ $conjoint->prenom }} {{ $conjoint->nom }}</div>
            <div style="font-size:12px;color:#6B7280;">
                @if($conjoint->date_naissance)
                    Né(e) le {{ $conjoint->date_naissance->format('d/m/Y') }}
                @endif
                @if($conjoint->profession)
                    · {{ $conjoint->profession }}
                @endif
            </div>
        </div>
    </div>
    @empty
    <p class="text-muted mb-0" style="font-size:13px;">Aucun conjoint enregistré.</p>
    @endforelse
</div>

{{-- Enfants ───────────────────────────────────────────── --}}
<div class="panel p-4">
    <h6 class="fw-600 mb-3" style="font-size:14px;">
        <i class="fas fa-child me-2" style="color:#059669;"></i>Enfant(s)
        <span class="ms-2" style="font-size:12px;color:#9CA3AF;">({{ $agent->enfants->count() }})</span>
    </h6>

    @forelse($agent->enfants as $enfant)
    <div class="d-flex align-items-start gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:42px;height:42px;background:#ECFDF5;color:#059669;font-weight:700;font-size:14px;">
            {{ strtoupper(substr($enfant->prenom,0,1).substr($enfant->nom,0,1)) }}
        </div>
        <div>
            <div style="font-weight:600;font-size:14px;">{{ $enfant->prenom }} {{ $enfant->nom }}</div>
            <div style="font-size:12px;color:#6B7280;">
                @if(isset($enfant->date_naissance))
                    Né(e) le {{ $enfant->date_naissance->format('d/m/Y') }}
                @endif
                @if(isset($enfant->lien_parente))
                    · {{ $enfant->lien_parente }}
                @endif
            </div>
        </div>
    </div>
    @empty
    <p class="text-muted mb-0" style="font-size:13px;">Aucun enfant enregistré.</p>
    @endforelse
</div>

@endsection
