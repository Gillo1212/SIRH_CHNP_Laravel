{{--
    Layout partiel pour les documents administratifs officiels du CHNP.
    Fidèle aux modèles administratifs du Sénégal.
    
    @author Gilbert - Mémoire M2 SIRH CHNP
--}}

@extends('layouts.master')

@section('title', $titre_document ?? 'Document administratif')
@section('page-title', $titre_document ?? 'Document administratif')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('documents-admin.index') }}" style="color:#1565C0;">Documents</a></li>
    <li>{{ $titre_document ?? 'Document' }}</li>
@endsection

@push('styles')
<style>
/* Container */
.doc-container {
    max-width: 800px;
    margin: 0 auto;
}

/* Barre d'actions */
.doc-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding: 16px;
    background: #F8FAFC;
    border-radius: 12px;
    border: 1px solid #E2E8F0;
}

.doc-actions-left h5 {
    font-size: 16px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 4px;
}

.doc-actions-left p {
    font-size: 13px;
    color: #64748B;
    margin: 0;
}

.doc-actions-buttons {
    display: flex;
    gap: 8px;
}

/* Preview banner */
.preview-banner {
    background: #FEF3C7;
    border: 1px solid #F59E0B;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.preview-banner i { color: #D97706; font-size: 20px; }
.preview-banner-text { flex: 1; }
.preview-banner-title { font-weight: 600; color: #92400E; }
.preview-banner-desc { font-size: 13px; color: #A16207; }

/* Document */
.doc-preview {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 50px 60px;
    font-family: 'Times New Roman', serif;
    font-size: 13px;
    line-height: 1.6;
    color: #000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* En-tête document avec logos */
.doc-header-logos {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.doc-logo-ministere {
    width: 70px;
    height: auto;
}

.doc-logo-chnp {
    width: 90px;
    height: auto;
}

/* En-tête deux colonnes (gauche : République/Ministère, droite : CHNP/date) */
.doc-header-bicolonne {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.doc-header-col-gauche {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    font-size: 11px;
    line-height: 1.5;
    max-width: 48%;
}

.doc-header-col-droite {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    font-size: 11px;
    line-height: 1.5;
    max-width: 48%;
}

.doc-header-col-gauche img,
.doc-header-col-droite img {
    width: 70px;
    height: auto;
    margin-bottom: 6px;
}

.doc-header-col-droite img {
    width: 80px;
}

.doc-header-text {
    text-align: center;
    font-size: 12px;
    line-height: 1.4;
    margin-bottom: 20px;
}

.doc-etablissement {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    margin: 20px 0;
}

.doc-date-lieu {
    text-align: right;
    font-size: 12px;
    margin: 5px 0 15px;
}

/* Titre du document */
.doc-title {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    text-decoration: underline;
    margin: 25px 0;
    text-align: center;
    line-height: 1.5;
}

.doc-reference {
    font-size: 11px;
    font-style: italic;
    margin-top: 8px;
    text-align: center;
}

/* Signataire intro */
.doc-signataire-intro {
    text-align: center;
    margin: 20px 0;
    font-style: italic;
}

/* Visas */
.doc-visas {
    margin: 20px 0;
    text-align: justify;
}

.doc-visas p {
    margin: 6px 0;
    font-size: 12px;
    text-indent: 0;
}

/* Corps */
.doc-body {
    margin: 20px 0;
    text-align: justify;
}

.doc-body p {
    margin-bottom: 12px;
    text-indent: 0;
}

/* Décide */
.doc-decide {
    text-align: center;
    font-weight: bold;
    text-decoration: underline;
    margin: 30px 0 25px;
    font-size: 13px;
}

/* Articles */
.doc-article {
    margin-bottom: 15px;
    text-align: justify;
}

.doc-article-title {
    font-weight: bold;
    text-decoration: underline;
}

/* Signature */
.doc-footer {
    margin-top: 40px;
}

.doc-footer-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.doc-footer-left { 
    max-width: 45%; 
}

.doc-footer-right { 
    text-align: center; 
    max-width: 45%; 
}

.doc-lieu-date {
    font-size: 12px;
    margin-bottom: 15px;
}

.doc-signataire-titre {
    font-weight: bold;
    text-decoration: underline;
    margin-bottom: 60px;
}

.doc-signataire-nom {
    font-weight: bold;
}

/* Ampliations */
.doc-ampliations {
    margin-top: 20px;
}

.doc-ampliations-title {
    font-weight: bold;
    text-decoration: underline;
    font-size: 12px;
    margin-bottom: 8px;
}

.doc-ampliations ul {
    list-style: disc;
    padding-left: 25px;
    font-size: 11px;
    margin: 0;
}

.doc-ampliations li {
    margin-bottom: 3px;
}

/* Attestation style (corps centré) */
.doc-attestation-intro {
    text-align: justify;
    margin: 25px 0;
}

.doc-attestation-body {
    text-align: justify;
    margin: 20px 0;
}

.doc-attestation-closing {
    text-align: justify;
    margin-top: 25px;
}

/* Impression */
@media print {
    /* Masquer TOUT */
    body * {
        visibility: hidden;
    }

    /* Afficher uniquement le contenu du document */
    .doc-preview,
    .doc-preview * {
        visibility: visible !important;
    }

    /* Positionner le document en haut à gauche */
    .doc-preview {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 15mm 20mm !important;
        border: none !important;
        box-shadow: none !important;
        background: white !important;
    }

    /* Reset body */
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    /* Éviter les coupures de page */
    .doc-article,
    .doc-visas p {
        page-break-inside: avoid;
    }

    /* Forcer les couleurs */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="doc-container">
        
        {{-- Barre d'actions --}}
        <div class="doc-actions">
            <div class="doc-actions-left">
                <h5>{{ $titre_document ?? 'Document administratif' }}</h5>
                <p>Pour : {{ $agent->nom_complet ?? 'Agent' }}
                    @if(isset($reference) && !($preview ?? false))
                        - Réf. : {{ $reference }}
                    @endif
                </p>
            </div>
            <div class="doc-actions-buttons">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print me-1"></i>Imprimer / PDF
                </button>

                @if(isset($preview) && $preview)
                    {{-- Mode prévisualisation : valider et générer --}}
                    <form action="{{ route('documents-admin.generer', ['agentId' => $agent->id_agent, 'type' => $demande->type_document]) }}" method="POST" class="d-inline">
                        @csrf
                        @foreach($demande->donnees_specifiques ?? [] as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ is_array($value) ? json_encode($value) : $value }}">
                        @endforeach
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check me-1"></i>Valider et générer
                        </button>
                    </form>
                @else
                    @if(isset($isAgentView))
                        {{-- Vue agent : impression uniquement --}}
                        <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Mes documents
                        </a>
                    @else
                        {{-- Mode document généré RH : modifier, dupliquer, annuler --}}
                        @isset($demande)
                            @if($demande->statut === 'pret')
                                <a href="{{ route('documents-admin.modifier', $demande->id) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                                <a href="{{ route('documents-admin.duplicate', $demande->id) }}"
                                   class="btn btn-outline-secondary btn-sm" title="Créer un nouveau document identique">
                                    <i class="fas fa-copy me-1"></i>Dupliquer
                                </a>
                                <form action="{{ route('documents-admin.annuler', $demande->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Annuler ce document ? Cette action est irréversible.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-ban me-1"></i>Annuler
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="fas fa-ban me-1"></i>Document annulé
                                </span>
                                <a href="{{ route('documents-admin.duplicate', $demande->id) }}"
                                   class="btn btn-outline-secondary btn-sm" title="Créer un nouveau document depuis ce modèle">
                                    <i class="fas fa-copy me-1"></i>Recréer
                                </a>
                            @endif
                        @endisset
                        <a href="{{ route('documents-admin.historique') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-history me-1"></i>Historique
                        </a>
                        <a href="{{ route('documents-admin.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                    @endif
                @endif
            </div>
        </div>

        @if(isset($preview) && $preview)
        <div class="preview-banner">
            <i class="fas fa-eye"></i>
            <div class="preview-banner-text">
                <div class="preview-banner-title">Mode prévisualisation</div>
                <div class="preview-banner-desc">Vérifiez le document avant de le valider.</div>
            </div>
        </div>
        @endif

        <div class="doc-preview">
            @yield('document-content')
        </div>

    </div>
</div>
@endsection
