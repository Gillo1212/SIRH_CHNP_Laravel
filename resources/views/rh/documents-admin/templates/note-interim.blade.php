{{--
    NOTE D'INTERIM
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

    <div class="doc-title">NOTE DE SERVICE</div>

    {{-- Objet --}}
    <p style="margin: 20px 0;"><strong><u>Objet</u> : Intérim</strong></p>

    {{-- Corps --}}
    <div class="doc-body">
        <p>
            En raison de l'absence du <strong>{{ $titulaire_civilite ?? 'Professeur' }} {{ $titulaire_nom ?? '-' }}</strong>, Chef du service de {{ $service ?? '-' }} du {{ $etablissement['nom'] }} pour raisons de {{ $motif_absence ?? 'congés administratifs' }}, <strong>{{ $interimaire_civilite ?? 'Professeur' }} {{ $interimaire_nom ?? '-' }}</strong>, {{ $interimaire_fonction ?? '-' }}, sera chargé{{ $interimaire_sexe === 'F' ? 'e' : '' }} d'assurer l'intérim du poste durant la période allant du <strong>{{ $date_debut->isoFormat('D MMMM') }}</strong> au <strong>{{ $date_fin->isoFormat('D MMMM YYYY') }}</strong>.
        </p>

        <p style="margin-top: 20px;">
            À cet effet, nous demandons à tout le personnel de lui apporter toute l'assistance requise.
        </p>
    </div>

    {{-- Signature --}}
    <div class="doc-footer">
        <div class="doc-footer-row">
            <div class="doc-footer-left">
                <div class="doc-ampliations">
                    <div class="doc-ampliations-title">AMPLIATIONS :</div>
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
