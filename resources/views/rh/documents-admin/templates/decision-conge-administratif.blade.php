{{--
    DECISION ACCORDANT UN CONGE ADMINISTRATIF A UN AGENT FONCTIONNAIRE DE L'ETAT
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

    <div class="doc-title">
        DÉCISION ACCORDANT UN CONGÉ ADMINISTRATIF<br>
        À UN AGENT FONCTIONNAIRE DE L'ÉTAT
    </div>

    {{-- Signataire --}}
    <div class="doc-signataire-intro">
        Le Directeur du {{ $etablissement['nom'] }},
    </div>

    {{-- Visas --}}
    <div class="doc-visas">
        <p>1- Vu la Constitution ;</p>
        <p>2- Vu la loi 61-33 du 15 juin 1961 relative au statut général des fonctionnaires modifiée ;</p>
        <p>3- Vu la loi 97-17 du 1<sup>er</sup> décembre 1997 portant Code du Travail ;</p>
        <p>4- Vu la loi 98-08 du 02 mars 1998, portant réforme hospitalière modifiée ;</p>
        <p>5- Vu le décret 95-264 du 10 mars 1995 portant délégation de pouvoir du Président de la République en matière d'Administration et de gestion du personnel ;</p>
        <p>6- Vu le décret n° 98-701 du 26 Août 1998 relatif à l'organisation des EPSH ;</p>
        <p>7- Vu le décret n° 98-702 du 26 Août 1998 portant organisation administrative et financière des EPS notamment en ses articles 14 et 16 ;</p>
        <p>8- Vu le décret n° 2007-317 du 1<sup>er</sup> mars 2007, portant création d'un établissement public de santé hospitalier de niveau III dénommé « Centre Hospitalier National de Pikine » ;</p>
        <p>9- Vu le {{ $directeur['decret'] }} portant nomination de <strong>{{ $directeur['civilite'] }} {{ $directeur['nom'] }}</strong> en qualité de {{ $directeur['titre'] }} ;</p>
        <p>10- Vu la demande de l'intéressé{{ $agent->sexe === 'F' ? 'e' : '' }}.</p>
    </div>

    {{-- Décide --}}
    <div class="doc-decide">Décide</div>

    {{-- Articles --}}
    <div class="doc-article">
        <p><span class="doc-article-title">Article 1</span> : Un congé administratif de <strong>{{ $duree_jours == 30 ? 'trente (30)' : $duree_jours }} jours</strong> est accordé à <strong>{{ $agent->sexe === 'F' ? 'Madame' : 'Monsieur' }} {{ $agent->prenom }} {{ strtoupper($agent->nom) }}, {{ $agent->fonction ?? 'Agent' }}</strong>, matricule de solde <strong>{{ $agent->matricule }}</strong>, en service au {{ $etablissement['nom'] }}, pour ses services effectués du <strong>{{ $periode_reference['debut']->isoFormat('D MMMM YYYY') }}</strong> au <strong>{{ $periode_reference['fin']->isoFormat('D MMMM YYYY') }}</strong>.</p>
    </div>

    <div class="doc-article">
        <p><span class="doc-article-title">Article 2</span> : À l'issue de ce congé, l'intéressé{{ $agent->sexe === 'F' ? 'e' : '' }} rejoindra son ancien poste d'affectation. Les certificats de cessation et de reprise de service lui seront délivrés par l'administration.</p>
    </div>

    <div class="doc-article">
        <p><span class="doc-article-title">Article 3</span> : L'intéressé{{ $agent->sexe === 'F' ? 'e' : '' }} sera considéré{{ $agent->sexe === 'F' ? 'e' : '' }} comme démissionnaire de son emploi si {{ $agent->sexe === 'F' ? 'elle' : 'il' }} ne reprend pas service à l'expiration de son congé.</p>
    </div>

    <div class="doc-article">
        <p><span class="doc-article-title">Article 4</span> : La présente décision qui prendra effet à la diligence du chef de service, sera enregistrée et publiée partout où besoin sera.</p>
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

    <div class="doc-reference">
        <em>Réf : décision n°{{ $reference }} du {{ $date_document->isoFormat('D/MM/YYYY') }}</em>
    </div>
@endsection
