@extends('layouts.master')

@section('title', 'FAQ')
@section('page-title', 'Foire aux questions')

@section('breadcrumb')
    <li><a href="{{ route('aide.index') }}" style="color:#1565C0;">Aide</a></li>
    <li><span style="color:#6B7280;">FAQ</span></li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="card" style="border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;">
    <div class="card-header d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#0A4D8C,#1565C0);padding:20px 24px;">
        <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-question-circle" style="color:white;font-size:18px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="color:white;font-weight:700;">Foire aux Questions</h5>
            <div style="font-size:12px;color:rgba(255,255,255,0.75);">Réponses aux questions les plus fréquentes</div>
        </div>
    </div>
    <div class="card-body" style="padding:28px 32px;">

        {{-- SECTION CONGÉS --}}
        <div id="conges" style="margin-bottom:32px;">
            <h6 style="color:#0A4D8C;font-weight:700;font-size:14px;margin-bottom:16px;display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:2px solid #EFF6FF;">
                <i class="fas fa-calendar-check"></i> Congés et absences
            </h6>
            <div class="accordion" id="faqConges">
                @foreach([
                    [
                        'q' => 'Comment faire une demande de congé ?',
                        'r' => 'Rendez-vous dans <strong>Mes Congés → Nouvelle demande</strong>. Sélectionnez le type de congé, les dates souhaitées et saisissez le motif. Votre demande sera transmise à votre manager pour validation, puis au service RH pour approbation finale.',
                    ],
                    [
                        'q' => 'Comment consulter mon solde de congés ?',
                        'r' => 'Votre solde de congés est affiché sur votre tableau de bord personnel. Vous pouvez également le consulter dans <strong>Mes Congés → Solde</strong>. Le solde est mis à jour automatiquement après chaque approbation.',
                    ],
                    [
                        'q' => 'Quel est le délai de traitement d\'une demande de congé ?',
                        'r' => 'Le circuit est : Agent → Manager (validation dans les 48h) → RH (approbation finale dans les 24h). Vous êtes notifié à chaque étape. En cas d\'urgence, contactez directement votre manager ou le service RH.',
                    ],
                    [
                        'q' => 'Comment justifier une absence ?',
                        'r' => 'Votre manager ou le service RH saisit votre absence dans le système. Vous pouvez soumettre votre justificatif directement depuis <strong>Mes Absences</strong> en téléchargeant le document (PDF, JPG, max 2 Mo).',
                    ],
                ] as $i => $item)
                <div class="accordion-item" style="border:1px solid #E5E7EB;border-radius:8px;margin-bottom:8px;overflow:hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#fc{{ $i }}"
                            style="font-size:13.5px;font-weight:500;color:#111827;background:#F9FAFB;padding:14px 16px;">
                            {{ $item['q'] }}
                        </button>
                    </h2>
                    <div id="fc{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqConges">
                        <div class="accordion-body" style="font-size:13.5px;color:#374151;line-height:1.7;padding:16px;">
                            {!! $item['r'] !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION DOCUMENTS --}}
        <div id="documents" style="margin-bottom:32px;">
            <h6 style="color:#059669;font-weight:700;font-size:14px;margin-bottom:16px;display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:2px solid #ECFDF5;">
                <i class="fas fa-file-alt"></i> Documents administratifs
            </h6>
            <div class="accordion" id="faqDocs">
                @foreach([
                    [
                        'q' => 'Comment obtenir une attestation de travail ?',
                        'r' => 'Depuis le menu <strong>Documents → Demander un document</strong>, sélectionnez "Attestation de travail", renseignez le motif et soumettez. Le service RH traitera votre demande dans les 48h ouvrées. Vous serez notifié quand le document est disponible au téléchargement.',
                    ],
                    [
                        'q' => 'Quels documents puis-je demander en ligne ?',
                        'r' => 'Vous pouvez demander : <ul style="margin-top:6px;"><li><strong>Attestation de travail</strong> — certifie votre emploi actuel</li><li><strong>Certificat de travail</strong> — lors d\'un départ</li><li><strong>Ordre de mission</strong> — pour les déplacements professionnels</li></ul>',
                    ],
                    [
                        'q' => 'Comment demander une prise en charge médicale ?',
                        'r' => 'Dans le menu <strong>Prise en charge</strong>, cliquez sur "Nouvelle demande". Indiquez le bénéficiaire (vous-même, conjoint ou enfant), l\'établissement médical et le motif. Les prises en charge standard sont traitées par le RH ; les cas exceptionnels nécessitent une validation DRH.',
                    ],
                ] as $i => $item)
                <div class="accordion-item" style="border:1px solid #E5E7EB;border-radius:8px;margin-bottom:8px;overflow:hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#fd{{ $i }}"
                            style="font-size:13.5px;font-weight:500;color:#111827;background:#F9FAFB;padding:14px 16px;">
                            {{ $item['q'] }}
                        </button>
                    </h2>
                    <div id="fd{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqDocs">
                        <div class="accordion-body" style="font-size:13.5px;color:#374151;line-height:1.7;padding:16px;">
                            {!! $item['r'] !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION COMPTE --}}
        <div id="compte">
            <h6 style="color:#D97706;font-weight:700;font-size:14px;margin-bottom:16px;display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:2px solid #FFFBEB;">
                <i class="fas fa-user-circle"></i> Compte et sécurité
            </h6>
            <div class="accordion" id="faqCompte">
                @foreach([
                    [
                        'q' => 'Comment modifier mon mot de passe ?',
                        'r' => 'Cliquez sur votre avatar en haut à droite → <strong>Sécurité</strong>. Saisissez votre mot de passe actuel, puis le nouveau (minimum 8 caractères, avec majuscule et chiffre). Confirmez et enregistrez.',
                    ],
                    [
                        'q' => 'Mon compte est verrouillé, que faire ?',
                        'r' => 'Après 5 tentatives de connexion échouées, le compte est automatiquement verrouillé. Contactez l\'administrateur système à <a href="mailto:admin@chnp.sn">admin@chnp.sn</a> ou le service RH pour le déverrouillage.',
                    ],
                    [
                        'q' => 'Comment changer la langue de l\'interface ?',
                        'r' => 'Rendez-vous dans <strong>Préférences</strong> (accessible depuis le menu de navigation ou votre profil). Dans la section "Langue", sélectionnez Français ou English et enregistrez. Le changement prend effet immédiatement.',
                    ],
                    [
                        'q' => 'Comment contacter le service RH pour un problème ?',
                        'r' => 'Vous pouvez contacter le service RH par email à <a href="mailto:rh@chnp.sn">rh@chnp.sn</a>, ou vous rendre physiquement au bureau RH du CHNP. Pour les problèmes techniques, utilisez le formulaire de <a href="{{ route(\'support.create\') }}">support en ligne</a>.',
                    ],
                ] as $i => $item)
                <div class="accordion-item" style="border:1px solid #E5E7EB;border-radius:8px;margin-bottom:8px;overflow:hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#fcc{{ $i }}"
                            style="font-size:13.5px;font-weight:500;color:#111827;background:#F9FAFB;padding:14px 16px;">
                            {{ $item['q'] }}
                        </button>
                    </h2>
                    <div id="fcc{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqCompte">
                        <div class="accordion-body" style="font-size:13.5px;color:#374151;line-height:1.7;padding:16px;">
                            {!! $item['r'] !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<div class="mt-3 text-center" style="font-size:13px;color:#9CA3AF;">
    Vous n'avez pas trouvé la réponse ?
    <a href="{{ route('support.create') }}" style="color:#1565C0;font-weight:500;">Ouvrir un ticket de support</a>
</div>

</div>
</div>
@endsection
