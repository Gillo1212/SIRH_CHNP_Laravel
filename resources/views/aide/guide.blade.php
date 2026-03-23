@extends('layouts.master')

@section('title', 'Guide utilisateur')
@section('page-title', 'Guide utilisateur')

@section('breadcrumb')
    <li><a href="{{ route('aide.index') }}" style="color:#1565C0;">Aide</a></li>
    <li><span style="color:#6B7280;">Guide utilisateur</span></li>
@endsection

@section('content')
<div class="row">

    {{-- Navigation latérale du guide --}}
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card" style="border-radius:12px;border:1px solid #E5E7EB;position:sticky;top:80px;">
            <div class="card-header" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;padding:14px 16px;">
                <span style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;">Sommaire</span>
            </div>
            <div class="card-body" style="padding:10px 8px;">
                @foreach([
                    ['anchor' => 'connexion', 'icon' => 'sign-in-alt', 'label' => 'Connexion'],
                    ['anchor' => 'tableau-bord', 'icon' => 'tachometer-alt', 'label' => 'Tableau de bord'],
                    ['anchor' => 'conges', 'icon' => 'calendar-check', 'label' => 'Demande de congé'],
                    ['anchor' => 'documents', 'icon' => 'file-alt', 'label' => 'Documents admin.'],
                    ['anchor' => 'pec', 'icon' => 'heartbeat', 'label' => 'Prise en charge'],
                    ['anchor' => 'profil', 'icon' => 'user-cog', 'label' => 'Profil & Sécurité'],
                    ['anchor' => 'preferences', 'icon' => 'sliders-h', 'label' => 'Préférences'],
                ] as $item)
                <a href="#{{ $item['anchor'] }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;text-decoration:none;color:#374151;font-size:13px;transition:all 150ms;"
                   onmouseover="this.style.background='#EFF6FF';this.style.color='#0A4D8C'"
                   onmouseout="this.style.background='transparent';this.style.color='#374151'">
                    <i class="fas fa-{{ $item['icon'] }}" style="width:16px;text-align:center;color:#6B7280;"></i>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Contenu du guide --}}
    <div class="col-lg-9">
        <div class="card" style="border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;">
            <div class="card-header d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#059669,#10B981);padding:20px 24px;">
                <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-book-open" style="color:white;font-size:18px;"></i>
                </div>
                <div>
                    <h5 class="mb-0" style="color:white;font-weight:700;">Guide utilisateur SIRH CHNP</h5>
                    <div style="font-size:12px;color:rgba(255,255,255,0.8);">Prise en main du Système d'Information RH</div>
                </div>
            </div>
            <div class="card-body" style="padding:32px;">

                {{-- SECTION 1 : Connexion --}}
                <div id="connexion" style="margin-bottom:36px;">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">1</span>
                        Connexion au SIRH
                    </h5>
                    <ol style="font-size:13.5px;color:#374151;line-height:2;padding-left:20px;">
                        <li>Ouvrez votre navigateur et accédez à l'adresse du SIRH CHNP.</li>
                        <li>Saisissez votre <strong>identifiant</strong> (login fourni par le service RH).</li>
                        <li>Saisissez votre <strong>mot de passe</strong>.</li>
                        <li>Cliquez sur <strong>Se connecter</strong>.</li>
                    </ol>
                    <div class="alert alert-info d-flex gap-2" style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;margin-top:12px;">
                        <i class="fas fa-info-circle mt-1" style="color:#1565C0;flex-shrink:0;"></i>
                        <div style="font-size:13px;color:#1E40AF;">
                            Après 5 tentatives échouées, votre compte sera automatiquement verrouillé. Contactez l'administrateur pour le déverrouillage.
                        </div>
                    </div>
                </div>

                <hr style="border-color:#F3F4F6;margin-bottom:36px;">

                {{-- SECTION 2 : Tableau de bord --}}
                <div id="tableau-bord" style="margin-bottom:36px;">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">2</span>
                        Le tableau de bord
                    </h5>
                    <p style="font-size:13.5px;color:#374151;line-height:1.7;">
                        Après connexion, vous accédez à votre tableau de bord personnalisé. Il affiche :
                    </p>
                    <ul style="font-size:13.5px;color:#374151;line-height:2;padding-left:20px;">
                        <li>Vos <strong>informations personnelles</strong> (nom, matricule, service)</li>
                        <li>Votre <strong>solde de congés</strong> restant</li>
                        <li>Vos <strong>demandes en cours</strong> (congés, documents, PEC)</li>
                        <li>Les <strong>notifications récentes</strong></li>
                        <li>L'<strong>accès rapide</strong> aux actions les plus fréquentes</li>
                    </ul>
                </div>

                <hr style="border-color:#F3F4F6;margin-bottom:36px;">

                {{-- SECTION 3 : Congés --}}
                <div id="conges" style="margin-bottom:36px;">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">3</span>
                        Faire une demande de congé
                    </h5>
                    <p style="font-size:13.5px;color:#374151;line-height:1.7;">Le circuit de validation se déroule en 3 étapes :</p>
                    <div class="row g-3 mb-4">
                        @foreach([
                            ['step' => '1', 'title' => 'Demande Agent', 'desc' => 'Vous soumettez la demande via Mes Congés → Nouvelle demande.', 'color' => '#0A4D8C', 'bg' => '#EFF6FF'],
                            ['step' => '2', 'title' => 'Validation Manager', 'desc' => 'Votre manager reçoit une notification et valide ou refuse.', 'color' => '#D97706', 'bg' => '#FFFBEB'],
                            ['step' => '3', 'title' => 'Approbation RH', 'desc' => 'Le service RH finalise l\'approbation et met à jour votre solde.', 'color' => '#059669', 'bg' => '#ECFDF5'],
                        ] as $step)
                        <div class="col-md-4">
                            <div style="padding:14px;border-radius:10px;background:{{ $step['bg'] }};text-align:center;">
                                <div style="width:32px;height:32px;background:{{ $step['color'] }};border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;color:white;font-weight:700;font-size:14px;">{{ $step['step'] }}</div>
                                <div style="font-size:13px;font-weight:600;color:{{ $step['color'] }};margin-bottom:4px;">{{ $step['title'] }}</div>
                                <div style="font-size:12px;color:#6B7280;line-height:1.4;">{{ $step['desc'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="font-size:13.5px;color:#374151;line-height:1.7;">
                        <strong>Étapes pour faire une demande :</strong>
                        <ol style="margin-top:8px;line-height:2;padding-left:20px;">
                            <li>Dans le menu gauche, cliquez sur <strong>Mes Congés</strong>.</li>
                            <li>Cliquez sur le bouton <strong>Nouvelle demande</strong>.</li>
                            <li>Sélectionnez le <strong>type de congé</strong> (annuel, maladie, etc.).</li>
                            <li>Choisissez les <strong>dates de début et de fin</strong>.</li>
                            <li>Ajoutez un <strong>motif</strong> si nécessaire.</li>
                            <li>Cliquez sur <strong>Soumettre la demande</strong>.</li>
                        </ol>
                    </div>
                </div>

                <hr style="border-color:#F3F4F6;margin-bottom:36px;">

                {{-- SECTION 4 : Documents --}}
                <div id="documents" style="margin-bottom:36px;">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">4</span>
                        Documents administratifs
                    </h5>
                    <p style="font-size:13.5px;color:#374151;line-height:1.7;">
                        Vous pouvez demander des documents en ligne depuis <strong>Documents → Demander un document</strong>.
                        Le service RH génère le document et vous notifie pour le téléchargement.
                    </p>
                </div>

                <hr style="border-color:#F3F4F6;margin-bottom:36px;">

                {{-- SECTION 5 : PEC --}}
                <div id="pec" style="margin-bottom:36px;">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">5</span>
                        Prise en charge médicale
                    </h5>
                    <p style="font-size:13.5px;color:#374151;line-height:1.7;">
                        La prise en charge médicale peut être demandée pour vous-même, votre conjoint ou vos enfants
                        enregistrés dans votre dossier. Renseignez l'établissement médical et le motif médical.
                        Les prises en charge exceptionnelles (montant élevé) nécessitent une validation DRH supplémentaire.
                    </p>
                </div>

                <hr style="border-color:#F3F4F6;margin-bottom:36px;">

                {{-- SECTION 6 : Profil --}}
                <div id="profil" style="margin-bottom:36px;">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">6</span>
                        Profil et sécurité
                    </h5>
                    <p style="font-size:13.5px;color:#374151;line-height:1.7;">
                        Depuis votre menu profil (avatar en haut à droite), accédez à <strong>Mon profil</strong>
                        pour consulter vos informations personnelles. Pour modifier votre mot de passe, rendez-vous
                        dans la section <strong>Sécurité</strong>. Il est recommandé de changer votre mot de passe
                        lors de votre première connexion.
                    </p>
                </div>

                <hr style="border-color:#F3F4F6;margin-bottom:36px;">

                {{-- SECTION 7 : Préférences --}}
                <div id="preferences">
                    <h5 style="color:#0A4D8C;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <span style="width:28px;height:28px;background:#EFF6FF;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#0A4D8C;flex-shrink:0;">7</span>
                        Préférences
                    </h5>
                    <p style="font-size:13.5px;color:#374151;line-height:1.7;">
                        Personnalisez votre expérience depuis <a href="{{ route('preferences.index') }}" style="color:#1565C0;">Préférences</a> :
                    </p>
                    <ul style="font-size:13.5px;color:#374151;line-height:2;padding-left:20px;">
                        <li><strong>Langue</strong> : Français ou English</li>
                        <li><strong>Thème</strong> : Clair ou Sombre</li>
                        <li><strong>Notifications</strong> : Email et/ou système</li>
                        <li><strong>Affichage</strong> : Nombre d'éléments par page, format de date</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
