{{--
    AUTORISATION DE SORTIE DU TERRITOIRE
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

    <div class="doc-title">AUTORISATION DE SORTIE DU TERRITOIRE NATIONAL</div>

    {{-- Corps --}}
    <div class="doc-body" style="margin-top: 30px;">
        <p>
            Une autorisation de sortie du territoire national, couvrant la période allant du <strong>{{ $date_debut->isoFormat('D') }}<sup>{{ $date_debut->isoFormat('Do') == '1er' ? 'er' : '' }}</sup></strong> au <strong>{{ $date_fin->isoFormat('D MMMM YYYY') }} inclus</strong> est accordée à <strong>{{ $agent->sexe === 'F' ? 'Madame' : 'Monsieur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? 'Agent' }}, {{ $agent->responsabilite ?? '' }}, matricule de solde <strong>{{ $agent->matricule }}</strong>, en service au {{ $etablissement['nom'] }}.
        </p>

        <p style="margin-top: 20px;">
            {{ $agent->sexe === 'F' ? 'Cette dernière' : 'Ce dernier' }} doit effectuer {{ $motif ?? 'un stage de perfectionnement' }} au <strong>{{ $destination ?? '-' }}</strong> dans le cadre de {{ $cadre ?? 'la coopération' }}.
        </p>

        <p style="margin-top: 25px;">
            En foi de quoi, la présente autorisation lui est accordée pour servir et faire valoir ce que de droit.
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
