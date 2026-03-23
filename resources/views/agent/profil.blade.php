@extends('layouts.master')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Dossier Personnel')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mon profil</li>
@endsection

@push('styles')
<style>
.info-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:#6B7280; margin-bottom:2px; }
.info-value { font-size:14px; font-weight:500; }
.section-card { border-radius:12px; margin-bottom:1.5rem; }
.encrypted-badge {
    display:inline-flex;align-items:center;gap:4px;
    font-size:11px;padding:2px 8px;border-radius:12px;
    background:rgba(245,158,11,0.12);color:#D97706;font-weight:600;
}
[data-theme="dark"] .info-label { color:#6e7681; }
[data-theme="dark"] .info-value { color:#e6edf3; }
[data-theme="dark"] .encrypted-badge { background:rgba(245,158,11,0.2);color:#fbbf24; }
</style>
@endpush

@section('content')

{{-- En-tête profil ─────────────────────────────────────── --}}
<div class="panel mb-4 p-4">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <x-agent-avatar :agent="$agent" :size="80" />
        <div class="flex-grow-1">
            <h4 class="fw-700 mb-1" style="font-size:20px;">{{ $agent->prenom }} {{ $agent->nom }}</h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <code style="font-size:13px;background:rgba(10,77,140,0.08);color:#0A4D8C;padding:2px 8px;border-radius:6px;">
                    {{ $agent->matricule }}
                </code>
                <span style="color:#6B7280;font-size:13px;">{{ $agent->fonction }}</span>
                @if($agent->service)
                    <span style="color:#6B7280;font-size:13px;">· {{ $agent->service->nom_service }}</span>
                @endif
            </div>
            <div class="mt-2">
                @php $st = strtolower($agent->statut ?? 'actif'); @endphp
                @if($st === 'actif')
                    <span class="badge-statut badge-actif"><i class="fas fa-circle me-1" style="font-size:7px;"></i>Actif</span>
                @elseif($st === 'en_conge')
                    <span class="badge-statut badge-conge"><i class="fas fa-umbrella-beach me-1"></i>En congé</span>
                @elseif($st === 'suspendu')
                    <span class="badge-statut" style="background:#FEE2E2;color:#991B1B;"><i class="fas fa-ban me-1"></i>Suspendu</span>
                @else
                    <span class="badge-statut badge-retraite">{{ ucfirst($agent->statut) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Informations personnelles ──────────────────────── --}}
    <div class="col-lg-6">
        <div class="panel section-card p-4">
            <h6 class="fw-600 mb-3" style="font-size:14px;">
                <i class="fas fa-user me-2" style="color:#0A4D8C;"></i>Informations personnelles
            </h6>
            <div class="row g-3">
                <div class="col-6">
                    <div class="info-label">Nom</div>
                    <div class="info-value">{{ $agent->nom }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">Prénom</div>
                    <div class="info-value">{{ $agent->prenom }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">Date de naissance</div>
                    <div class="info-value">{{ $agent->date_naissance?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">Lieu de naissance</div>
                    <div class="info-value">{{ $agent->lieu_naissance ?? '—' }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">Sexe</div>
                    <div class="info-value">{{ $agent->sexe === 'M' ? 'Masculin' : 'Féminin' }}</div>
                </div>
                <div class="col-6">
                    <div class="info-label">Situation familiale</div>
                    <div class="info-value">{{ $agent->situation_familiale ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="info-label">Nationalité</div>
                    <div class="info-value">{{ $agent->nationalite ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coordonnées ──────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="panel section-card p-4">
            <h6 class="fw-600 mb-3" style="font-size:14px;">
                <i class="fas fa-address-card me-2" style="color:#0A4D8C;"></i>Coordonnées
                <span class="encrypted-badge ms-2"><i class="fas fa-lock"></i>Données protégées</span>
            </h6>
            <div class="row g-3">
                <div class="col-12">
                    <div class="info-label">Téléphone</div>
                    <div class="info-value">
                        @if($agent->telephone)
                            <i class="fas fa-lock text-warning me-1" style="font-size:11px;" title="Chiffré AES-256"></i>
                            {{ $agent->telephone_masque }}
                        @else
                            <span style="color:#9CA3AF;">Non renseigné</span>
                        @endif
                    </div>
                </div>
                <div class="col-12">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $agent->email ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="info-label">Adresse</div>
                    <div class="info-value">
                        @if($agent->adresse)
                            <i class="fas fa-lock text-warning me-1" style="font-size:11px;" title="Chiffré AES-256"></i>
                            {{ $agent->adresse_masquee }}
                        @else
                            <span style="color:#9CA3AF;">Non renseignée</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informations professionnelles ────────────────────── --}}
    <div class="col-12">
        <div class="panel section-card p-4">
            <h6 class="fw-600 mb-3" style="font-size:14px;">
                <i class="fas fa-briefcase me-2" style="color:#0A4D8C;"></i>Informations professionnelles
            </h6>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="info-label">Matricule</div>
                    <div class="info-value font-monospace" style="color:#0A4D8C;">{{ $agent->matricule }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-label">Date recrutement</div>
                    <div class="info-value">{{ $agent->date_recrutement?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-label">Fonction</div>
                    <div class="info-value">{{ $agent->fonction ?? '—' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-label">Grade</div>
                    <div class="info-value">{{ $agent->grade ?? '—' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-label">Catégorie</div>
                    <div class="info-value">{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-label">Service</div>
                    <div class="info-value">{{ $agent->service?->nom_service ?? '—' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-label">Division</div>
                    <div class="info-value">{{ $agent->division?->nom_division ?? '—' }}</div>
                </div>
                @if($agent->contratActif)
                <div class="col-6 col-md-3">
                    <div class="info-label">Type contrat</div>
                    <div class="info-value">{{ $agent->contratActif->type_contrat ?? '—' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Famille ────────────────────────────────────────────── --}}
    @if($agent->conjoints->count() || $agent->enfants->count())
    <div class="col-12">
        <div class="panel section-card p-4">
            <h6 class="fw-600 mb-3" style="font-size:14px;">
                <i class="fas fa-users me-2" style="color:#0A4D8C;"></i>Famille
                <a href="{{ route('agent.famille') }}" class="btn btn-sm btn-outline-primary ms-2" style="font-size:12px;padding:2px 10px;">
                    Voir détails
                </a>
            </h6>
            <div class="row g-2">
                @foreach($agent->conjoints as $conjoint)
                <div class="col-auto">
                    <span style="font-size:13px;background:rgba(10,77,140,0.08);padding:4px 12px;border-radius:20px;color:#0A4D8C;">
                        <i class="fas fa-ring me-1"></i>{{ $conjoint->prenom }} {{ $conjoint->nom }}
                    </span>
                </div>
                @endforeach
                @foreach($agent->enfants as $enfant)
                <div class="col-auto">
                    <span style="font-size:13px;background:rgba(16,185,129,0.08);padding:4px 12px;border-radius:20px;color:#059669;">
                        <i class="fas fa-child me-1"></i>{{ $enfant->prenom }} {{ $enfant->nom }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.badge-statut { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-actif  { background:#D1FAE5;color:#065F46; }
.badge-conge  { background:#FEF3C7;color:#92400E; }
.badge-retraite { background:#F3F4F6;color:#374151; }
[data-theme="dark"] .badge-actif  { background:rgba(16,185,129,0.18);color:#34d399; }
[data-theme="dark"] .badge-conge  { background:rgba(245,158,11,0.18);color:#fbbf24; }
[data-theme="dark"] .badge-retraite { background:rgba(107,114,128,0.18);color:#9ca3af; }
</style>
@endpush

@endsection
