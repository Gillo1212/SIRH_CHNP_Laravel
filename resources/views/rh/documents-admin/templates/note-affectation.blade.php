{{--
    NOTE D'AFFECTATION
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
    <p style="margin: 20px 0;"><strong><u>Objet</u> : Affectation</strong></p>

    {{-- Corps --}}
    <div class="doc-body">
        <p>
            <strong>{{ $agent->sexe === 'F' ? 'Madame' : 'Monsieur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? 'Agent' }}, {{ $agent->statut ?? 'Contractuel' }} du Ministère de la Santé et de l'Hygiène publique est affecté{{ $agent->sexe === 'F' ? 'e' : '' }} au <strong>service de {{ $service_affectation ?? '-' }}</strong> dudit Centre en complément d'effectifs.
        </p>

        <p style="margin-top: 20px;">
            Le chef du Service des Ressources Humaines, le chef du Service des Soins Infirmiers et le chef du service de {{ $service_affectation ?? '-' }} sont chargés chacun en ce qui le concerne de l'exécution de la présente note qui prend effet à compter de sa date de signature.
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
