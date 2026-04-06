{{--
    ATTESTATION DE JOUISSANCE DE CONGE
    Conforme au modèle officiel CHNP avec logos
--}}

@extends('rh.documents-admin.templates._layout')

@section('document-content')
    {{-- En-tête avec logos --}}
    <div class="doc-header-logos">
        <img src="{{ asset('images/logos/logo-ministere-sante.png') }}" alt="Ministère" class="doc-logo-ministere">
        <img src="{{ asset('images/logos/logo-chnp.jpeg') }}" alt="CHNP" class="doc-logo-chnp">
    </div>

    <div class="doc-header-text">
        RÉPUBLIQUE DU SÉNÉGAL<br>
        Un Peuple – Un But – Une Foi<br>
        -----------<br>
        MINISTÈRE DE LA SANTÉ<br>
        ET DE L'HYGIÈNE PUBLIQUE
    </div>

    <div class="doc-etablissement">{{ $etablissement['nom'] }}</div>

    <div class="doc-date-lieu">Pikine, le {{ $date_document->isoFormat('D MMMM YYYY') }}</div>

    <div class="doc-title">ATTESTATION DE JOUISSANCE DE CONGÉ</div>

    {{-- Référence --}}
    <div class="doc-reference">
        <em><u>Réf</u> : décision n°{{ $decision_reference ?? '...........' }}MSHP/CHNP/DIR/SRH du {{ isset($decision_date) ? $decision_date->isoFormat('D MMMM YYYY') : '../../....' }}</em>
    </div>

    {{-- Corps --}}
    <div class="doc-body" style="margin-top: 25px;">
        <p>
            Je soussigné, <strong>{{ $directeur['civilite'] }} {{ $directeur['nom'] }}</strong>, {{ $directeur['titre'] }}, atteste que <strong>{{ $agent->sexe === 'F' ? 'Madame' : 'Monsieur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? 'Agent' }}, bénéficiaire d'un congé administratif de <strong>{{ $duree_totale ?? 'trente (30)' }} jours</strong> suivant décision citée en référence, est autorisé{{ $agent->sexe === 'F' ? 'e' : '' }} à jouir de <strong>{{ $duree_jouissance ?? 'quinze (15)' }} jours</strong> dudit congé à compter du <strong>{{ $date_debut->isoFormat('D MMMM YYYY') }}</strong>.
        </p>

        <p style="margin-top: 20px;">
            L'intéressé{{ $agent->sexe === 'F' ? 'e' : '' }} reprendra service le <strong>{{ $date_reprise->isoFormat('D MMMM YYYY') }}</strong>.
        </p>

        <p style="margin-top: 20px;">
            En foi de quoi, la présente attestation lui est délivrée pour servir et faire valoir ce que de droit.
        </p>
    </div>

    {{-- Signature --}}
    <div class="doc-footer">
        <div class="doc-footer-row">
            <div class="doc-footer-left">
                <div class="doc-ampliations">
                    <div class="doc-ampliations-title">Ampliations :</div>
                    <ul>
                        @foreach($ampliations as $ampliation)
                            <li>{{ $ampliation }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="doc-footer-right">
                <div class="doc-signataire-titre">LE DIRECTEUR</div>
                <div class="doc-signataire-nom">{{ $directeur['civilite'] }} {{ $directeur['nom'] }}</div>
            </div>
        </div>
    </div>
@endsection
