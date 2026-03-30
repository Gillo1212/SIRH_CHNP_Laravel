@extends('layouts.master')
@section('title', 'Dossier — ' . $agent->nom_complet)
@section('page-title', 'Dossier agent')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.equipe') }}" style="color:#1565C0;">Mon équipe</a></li>
    <li>{{ $agent->nom_complet }}</li>
@endsection

@push('styles')
<style>
.info-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;margin-bottom:3px;}
.info-value{font-size:14px;color:var(--theme-text);font-weight:500;}
.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:16px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4">
            <div style="width:64px;height:64px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;font-weight:700;color:#0A4D8C;font-size:22px;border:3px solid #DBEAFE;">
                {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->nom, 0, 1)) }}
            </div>
            <div>
                <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">{{ $agent->prenom }} {{ $agent->nom }}</h4>
                <p class="mb-0 text-muted" style="font-size:13px;">{{ $agent->matricule }} · {{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</p>
            </div>
        </div>
        <a href="{{ route('manager.equipe.dossiers') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour dossiers
        </a>
    </div>

    {{-- Alerte lecture seule --}}
    <div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="border-radius:10px;">
        <i class="fas fa-info-circle"></i>
        <span style="font-size:13px;">Consultation en lecture seule. Pour modifier ces informations, contactez le service RH.</span>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">

            {{-- Informations professionnelles --}}
            <div class="section-card">
                <div class="section-title">Informations professionnelles</div>
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <div class="info-label">Matricule</div>
                        <div class="info-value">{{ $agent->matricule }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="info-label">Famille d'emploi</div>
                        <div class="info-value">{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="info-label">Catégorie</div>
                        <div class="info-value">{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="info-label">Service</div>
                        <div class="info-value">{{ $agent->service->nom_service ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="info-label">Statut contrat</div>
                        <div class="info-value">
                            @php $sBadges = ['Actif'=>'#D1FAE5','En_congé'=>'#DBEAFE','Suspendu'=>'#FEE2E2','Retraité'=>'#F3F4F6','Démissionnaire'=>'#FEF3C7']; @endphp
                            <span style="background:{{ $sBadges[$agent->statut_agent] ?? '#F3F4F6' }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                                {{ str_replace('_', ' ', $agent->statut_agent) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contrat actuel --}}
            <div class="section-card">
                <div class="section-title">Contrat actuel</div>
                @if($agent->contratActif)
                <div class="row g-3">
                    <div class="col-6">
                        <div class="info-label">Type</div>
                        <div class="info-value">{{ $agent->contratActif->type_contrat }}</div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Statut</div>
                        <div class="info-value">{{ $agent->contratActif->statut_contrat }}</div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Début</div>
                        <div class="info-value">{{ $agent->contratActif->date_debut?->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Fin</div>
                        <div class="info-value">{{ $agent->contratActif->date_fin?->format('d/m/Y') ?? 'Indéterminée' }}</div>
                    </div>
                </div>
                @else
                <p class="text-muted mb-0" style="font-size:13px;">Aucun contrat actif.</p>
                @endif
            </div>

            {{-- Demandes récentes --}}
            <div class="section-card">
                <div class="section-title">Demandes récentes</div>
                @forelse($agent->demandes->take(8) as $demande)
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #F9FAFB;">
                    <div>
                        <span style="font-size:13px;font-weight:600;color:var(--theme-text);">{{ $demande->type_demande }}</span>
                        <span style="font-size:12px;color:#9CA3AF;margin-left:8px;">{{ $demande->created_at?->format('d/m/Y') }}</span>
                    </div>
                    @php
                        $sColors = ['En_attente'=>'#FEF3C7|#92400E','Validé'=>'#DBEAFE|#1E40AF','Approuvé'=>'#D1FAE5|#065F46','Rejeté'=>'#FEE2E2|#991B1B'];
                        [$bg, $fg] = explode('|', $sColors[$demande->statut_demande] ?? '#F3F4F6|#374151');
                    @endphp
                    <span style="background:{{ $bg }};color:{{ $fg }};padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                        {{ str_replace('_', ' ', $demande->statut_demande) }}
                    </span>
                </div>
                @empty
                <p class="text-muted mb-0" style="font-size:13px;">Aucune demande.</p>
                @endforelse
            </div>

        </div>
        <div class="col-12 col-lg-4">

            {{-- Infos personnelles (non sensibles) --}}
            <div class="section-card">
                <div class="section-title">Informations personnelles</div>
                <div class="mb-3">
                    <div class="info-label">Nom complet</div>
                    <div class="info-value">{{ $agent->prenom }} {{ $agent->nom }}</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Sexe</div>
                    <div class="info-value">{{ $agent->sexe === 'M' ? 'Masculin' : 'Féminin' }}</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Date de naissance</div>
                    <div class="info-value">{{ $agent->date_naissance?->format('d/m/Y') ?? '—' }}</div>
                </div>
                @if($agent->enfants->isNotEmpty())
                <div>
                    <div class="info-label">Enfants ({{ $agent->enfants->count() }})</div>
                    @foreach($agent->enfants as $enfant)
                    <div class="info-value" style="font-size:13px;">{{ $enfant->prenom }} {{ $enfant->nom }}</div>
                    @endforeach
                </div>
                @endif
                <div class="alert alert-warning mt-3 py-2 px-3" style="border-radius:8px;font-size:12px;">
                    <i class="fas fa-lock me-1"></i>Les données sensibles sont masquées par politique de confidentialité.
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
