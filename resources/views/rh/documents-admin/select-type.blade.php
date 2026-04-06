@extends('layouts.master')

@section('title', 'Générer un document - ' . $agent->nom_complet)
@section('page-title', 'Générer un document administratif')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('documents-admin.index') }}" style="color:#1565C0;">Documents</a></li>
    <li>Sélection</li>
@endsection

@push('styles')
<style>
.agent-card {
    background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
    border: 1px solid #BAE6FD;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
}
.agent-card-header { display: flex; align-items: center; gap: 16px; }
.agent-avatar {
    width: 56px; height: 56px; border-radius: 12px;
    background: #0A4D8C; color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 600;
}
.agent-info h4 { font-size: 18px; font-weight: 600; color: #0C4A6E; margin-bottom: 4px; }
.agent-info p { font-size: 13px; color: #0369A1; margin: 0; }

.category-section { margin-bottom: 32px; }
.category-header {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #E5E7EB;
}
.category-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.category-icon.attestations { background: #DBEAFE; color: #1D4ED8; }
.category-icon.conges { background: #D1FAE5; color: #059669; }
.category-icon.mouvements { background: #FEF3C7; color: #D97706; }
.category-icon.missions { background: #E0E7FF; color: #4F46E5; }
.category-icon.autres { background: #FCE7F3; color: #DB2777; }
.category-title { font-size: 16px; font-weight: 600; color: #1F2937; }

.doc-type-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
.doc-type-card {
    background: white; border: 1px solid #E5E7EB; border-radius: 10px;
    padding: 16px; transition: all 0.2s ease; cursor: pointer; text-decoration: none; display: block;
}
.doc-type-card:hover {
    border-color: #0A4D8C; box-shadow: 0 4px 12px rgba(10, 77, 140, 0.15); transform: translateY(-2px);
}
.doc-type-card-title { font-size: 14px; font-weight: 600; color: #1F2937; margin-bottom: 4px; }
.doc-type-card-desc { font-size: 12px; color: #6B7280; }
.doc-type-card-arrow { float: right; color: #9CA3AF; transition: transform 0.2s ease; }
.doc-type-card:hover .doc-type-card-arrow { transform: translateX(4px); color: #0A4D8C; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">

            <div class="agent-card">
                <div class="agent-card-header">
                    <div class="agent-avatar">{{ strtoupper(substr($agent->prenom, 0, 1) . substr($agent->nom, 0, 1)) }}</div>
                    <div class="agent-info">
                        <h4>{{ $agent->nom_complet }}</h4>
                        <p>
                            <i class="fas fa-id-badge me-1"></i>{{ $agent->matricule }} 
                            &nbsp;•&nbsp; <i class="fas fa-briefcase me-1"></i>{{ $agent->fonction ?? 'Agent' }}
                            @if($agent->service) &nbsp;•&nbsp; <i class="fas fa-building me-1"></i>{{ $agent->service->nom_service }} @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Choisir le type de document</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Sélectionnez le document administratif à générer.</p>
                </div>
                <a href="{{ route('documents-admin.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>

            @foreach($categories as $key => $category)
            <div class="category-section">
                <div class="category-header">
                    <div class="category-icon {{ $key }}"><i class="{{ $category['icon'] }}"></i></div>
                    <div class="category-title">{{ $category['label'] }}</div>
                </div>
                <div class="doc-type-grid">
                    @foreach($category['types'] as $type)
                    <a href="{{ route('documents-admin.formulaire', ['agentId' => $agent->id_agent, 'type' => $type]) }}" class="doc-type-card">
                        <span class="doc-type-card-arrow"><i class="fas fa-chevron-right"></i></span>
                        <div class="doc-type-card-title">{{ $types[$type] ?? $type }}</div>
                        <div class="doc-type-card-desc">
                            @switch($type)
                                @case('attestation_travail') Atteste l'emploi de l'agent @break
                                @case('certificat_travail') Certifie l'ancienneté @break
                                @case('decision_conge_administratif') Décision de congé avec visas @break
                                @case('attestation_jouissance_conge') Autorise la jouissance du congé @break
                                @case('attestation_cessation_maternite') Cessation pour maternité @break
                                @case('note_affectation') Affectation à un service @break
                                @case('note_interim') Désigne un intérimaire @break
                                @case('ordre_mission') Autorisation de déplacement @break
                                @case('autorisation_sortie_territoire') Sortie hors du pays @break
                                @case('attestation_prime_motivation') Prime de motivation @break
                                @case('attestation_prise_service') Prise effective de service @break
                                @case('attestation_stage') Stage effectué @break
                                @default Document administratif
                            @endswitch
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>
@endsection
