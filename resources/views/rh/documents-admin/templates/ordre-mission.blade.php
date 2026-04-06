{{--
    ORDRE DE MISSION
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

    <div class="doc-title">ORDRE DE MISSION</div>

    {{-- Numéro --}}
    <p style="text-align: center; margin: 15px 0;">
        <strong>N° {{ $numero_ordre ?? '........' }}/MSHP/CHNP/DIR</strong>
    </p>

    {{-- Corps --}}
    <div class="doc-body">
        <p>
            Le Directeur du {{ $etablissement['nom'] }} autorise <strong>{{ $agent->sexe === 'F' ? 'Madame' : 'Monsieur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? 'Agent' }}, matricule de solde <strong>{{ $agent->matricule }}</strong>, à se rendre à <strong>{{ $destination ?? '-' }}</strong> pour {{ $objet_mission ?? 'mission de service' }}.
        </p>

        <p style="margin-top: 15px;">
            <strong>Période :</strong> du {{ $date_debut->isoFormat('D MMMM YYYY') }} au {{ $date_fin->isoFormat('D MMMM YYYY') }}
        </p>

        <p style="margin-top: 15px;">
            <strong>Moyen de transport :</strong> {{ $moyen_transport ?? 'Véhicule de service' }}
        </p>

        @if(isset($frais_mission))
        <p style="margin-top: 15px;">
            <strong>Frais de mission :</strong> {{ $frais_mission }}
        </p>
        @endif
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
