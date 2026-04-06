{{--
    ATTESTATION DE CESSATION DE SERVICE POUR CONGE MATERNITE
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

    <div class="doc-title">ATTESTATION DE CESSATION DE SERVICE<br>POUR CONGÉ DE MATERNITÉ</div>

    {{-- Référence --}}
    <div class="doc-reference">
        <em><u>Réf</u> : Décision N°{{ $reference_decision ?? '...........' }}MSAS/CHNP/DIR/SRH du {{ $date_decision ?? '../../....' }}</em>
    </div>

    {{-- Corps --}}
    <div class="doc-body" style="margin-top: 25px;">
        <p>
            Je soussignée, <strong>{{ $directeur['civilite'] }} {{ $directeur['nom'] }}</strong>, {{ $directeur['titre'] }}, atteste que <strong>Madame {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? 'infirmière d\'État' }}, matricule de solde <strong>{{ $agent->matricule }}</strong> en service au {{ $etablissement['nom'] }}, bénéficiaire d'un congé de maternité de <strong>{{ $duree_semaines ?? 'quatorze (14)' }} semaines</strong> suivant décision citée en référence est autorisée à cesser service le <strong>{{ $date_cessation->isoFormat('D MMMM YYYY') }}</strong>.
        </p>

        <p style="margin-top: 25px;">
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
