{{--
    CERTIFICAT DE TRAVAIL
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

    <div class="doc-title">CERTIFICAT DE TRAVAIL</div>

    {{-- Corps --}}
    <div class="doc-body" style="margin-top: 30px;">
        <p>
            Je soussigné, <strong>{{ $directeur['civilite'] }} {{ $directeur['nom'] }}</strong>, {{ $directeur['titre'] }}, certifie que <strong>{{ $agent->sexe === 'F' ? 'Madame' : 'Monsieur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}</strong>, {{ $agent->fonction ?? $agent->fonction ?? 'Agent' }}, fonctionnaire, matricule de solde n° <strong>{{ $agent->matricule }}</strong>, est en service audit Établissement depuis le <strong>{{ isset($date_entree) ? \Carbon\Carbon::parse($date_entree)->isoFormat('D MMMM YYYY') : '-' }}</strong>.
        </p>

        <p style="margin-top: 25px;">
            En foi de quoi, le présent certificat a été établi pour servir et faire valoir ce que de droit.
        </p>
    </div>

    {{-- Lieu et date --}}
    <p style="text-align: right; margin-top: 30px;">
        <strong>Fait à Pikine, le {{ $date_document->isoFormat('D MMMM YYYY') }}</strong>
    </p>

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
