{{--
    ATTESTATION DE PRISE DE SERVICE
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

    <div class="doc-title">ATTESTATION DE PRISE DE SERVICE</div>

    {{-- Référence --}}
    <div class="doc-reference">
        <em><u>Réf</u> : note de service n°{{ $reference_note ?? '...........' }}/MSHP/DRH/DGP/BFP/at du {{ $date_note ?? '../../....' }}</em>
    </div>

    {{-- Corps --}}
    <div class="doc-body" style="margin-top: 25px;">
        <p>
            Je soussigné, <strong>{{ $directeur['civilite'] }} {{ $directeur['nom'] }}</strong>, {{ $directeur['titre'] }}, atteste que <strong>{{ $agent->sexe === 'F' ? 'Docteur' : 'Docteur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? 'médecin' }}, inscrit au Diplôme d'Études Spécialisées (DES) de {{ $specialite ?? '-' }}, matricule de solde n° <strong>{{ $agent->matricule }}</strong>, suivant note de service citée en référence a effectivement pris service le <strong>{{ $date_prise_service->isoFormat('D MMMM YYYY') }}</strong>.
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
