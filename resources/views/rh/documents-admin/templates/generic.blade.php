{{--
    TEMPLATE GÉNÉRIQUE POUR DOCUMENTS ADMINISTRATIFS
    Conforme au modèle officiel CHNP avec logos
--}}

@extends('rh.documents-admin.templates._layout')

@section('document-content')
    {{-- En-tête avec logos --}}
    <div class="doc-header-row">
        <div class="doc-header-left">
            <img src="{{ asset('images/logos/logo-ministere-sante.png') }}" alt="Logo Ministère" class="logo-ministere">
            <br>
            RÉPUBLIQUE DU SÉNÉGAL<br>
            Un Peuple – Un But – Une Foi<br>
            -----------<br>
            MINISTÈRE DE LA SANTÉ<br>
            ET DE L'HYGIÈNE PUBLIQUE
        </div>
        <div class="doc-header-right">
            <img src="{{ asset('images/logos/logo-chnp.jpeg') }}" alt="Logo CHNP" class="logo-chnp">
            <div class="date-lieu">Pikine, le {{ $date_document->isoFormat('D MMMM YYYY') }}</div>
        </div>
    </div>

    <div class="doc-header-center">
        <div class="doc-etablissement">{{ $etablissement['nom'] }}</div>
    </div>

    <div class="doc-title">{{ strtoupper($titre_document ?? 'DOCUMENT ADMINISTRATIF') }}</div>

    {{-- Corps --}}
    <div class="doc-body" style="margin-top: 30px;">
        {!! $contenu ?? '' !!}
    </div>

    {{-- Signature --}}
    <div class="doc-footer">
        <div class="doc-footer-row">
            <div class="doc-footer-left">
                @if(isset($ampliations) && count($ampliations) > 0)
                <div class="doc-ampliations">
                    <div class="doc-ampliations-title">Ampliations :</div>
                    <ul>
                        @foreach($ampliations as $ampliation)
                            <li>{{ $ampliation }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="doc-footer-right">
                <div class="doc-signataire-titre">LE DIRECTEUR</div>
                <div class="doc-signataire-nom">{{ $directeur['civilite'] ?? '' }} {{ $directeur['nom'] ?? '' }}</div>
            </div>
        </div>
    </div>
@endsection
