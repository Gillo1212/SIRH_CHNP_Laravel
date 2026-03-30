{{--
  Modal de création d'un nouvel agent
  Zone de formulaire scrollable - Boutons toujours visibles
--}}

<style>
/* ════════════════════════════════════════
   MODAL STRUCTURE
   ════════════════════════════════════════ */
#modalCreateAgent .modal-dialog {
    max-width: 750px;
}
#modalCreateAgent .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

/* Header */
.create-modal-header {
    padding: 20px 24px;
    background: #fff;
    border-bottom: 1px solid #E5E7EB;
}
.create-modal-header .header-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}
.create-modal-header .header-icon i {
    font-size: 20px;
    color: #0A4D8C;
}
.create-modal-header h5 {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 4px 0;
}
.create-modal-header p {
    font-size: 13px;
    color: #6B7280;
    margin: 0;
}
.create-modal-header .btn-close {
    position: absolute;
    top: 20px;
    right: 20px;
}

/* Tabs */
.create-modal-tabs {
    display: flex;
    padding: 0 24px;
    background: #fff;
    border-bottom: 2px solid #E5E7EB;
}
.create-tab-btn {
    padding: 12px 16px;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #6B7280;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.create-tab-btn:hover {
    color: #374151;
    background: #F9FAFB;
}
.create-tab-btn.active {
    color: #0A4D8C;
    border-bottom-color: #0A4D8C;
    font-weight: 600;
}
.tab-badge-lock {
    width: 18px;
    height: 18px;
    border-radius: 9px;
    background: #FEE2E2;
    color: #DC2626;
    font-size: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ════════════════════════════════════════
   ZONE SCROLLABLE - C'EST ICI LA CLÉ
   ════════════════════════════════════════ */
.create-modal-scroll-area {
    background: #F3F4F6;
    padding: 14px 18px;
    height: 380px;              /* Hauteur fixe */
    overflow-y: scroll;         /* Scrollbar toujours visible */
}

/* Style de la scrollbar */
.create-modal-scroll-area::-webkit-scrollbar {
    width: 10px;
}
.create-modal-scroll-area::-webkit-scrollbar-track {
    background: #E5E7EB;
    border-radius: 5px;
}
.create-modal-scroll-area::-webkit-scrollbar-thumb {
    background: #0A4D8C;
    border-radius: 5px;
}
.create-modal-scroll-area::-webkit-scrollbar-thumb:hover {
    background: #1565C0;
}

/* Footer - Toujours visible */
.create-modal-footer {
    padding: 16px 24px;
    background: #fff;
    border-top: 1px solid #E5E7EB;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* ════════════════════════════════════════
   FORM ELEMENTS
   ════════════════════════════════════════ */
.form-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #E5E7EB;
    padding: 14px;
    margin-bottom: 12px;
}
.form-card:last-child {
    margin-bottom: 0;
}
.form-card-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #F3F4F6;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-label-sm {
    font-size: 11px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 3px;
    display: block;
}
.required {
    color: #DC2626;
}
.form-input {
    width: 100%;
    padding: 6px 10px;
    border: 1.5px solid #E5E7EB;
    border-radius: 6px;
    font-size: 12.5px;
    color: #111827;
    background: #fff;
}
.form-input:focus {
    outline: none;
    border-color: #0A4D8C;
    box-shadow: 0 0 0 3px rgba(10, 77, 140, 0.1);
}
.form-hint {
    font-size: 10px;
    color: #9CA3AF;
    margin-top: 2px;
}

/* Sensitive notice */
.sensitive-notice {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #FFF7ED;
    border: 1px solid #FED7AA;
    border-radius: 8px;
    margin-bottom: 12px;
    font-size: 11.5px;
    color: #92400E;
}

/* Famille items */
.famille-item {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 10px;
    position: relative;
}
.famille-item .btn-remove {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1px solid #FECACA;
    background: #FEF2F2;
    color: #DC2626;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
}
.famille-item .btn-remove:hover {
    background: #DC2626;
    color: #fff;
}
.btn-add-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border: 1px dashed #D1D5DB;
    border-radius: 8px;
    background: #fff;
    color: #6B7280;
    font-size: 12px;
    cursor: pointer;
}
.btn-add-item:hover {
    border-color: #0A4D8C;
    color: #0A4D8C;
    background: #EFF6FF;
}
.empty-famille {
    font-size: 13px;
    color: #9CA3AF;
    text-align: center;
    padding: 20px 0;
}
.empty-famille i {
    font-size: 24px;
    color: #E5E7EB;
    display: block;
    margin-bottom: 8px;
}

/* Buttons */
.btn-modal {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-modal-secondary {
    background: #fff;
    border: 1px solid #E5E7EB;
    color: #374151;
}
.btn-modal-secondary:hover {
    background: #F3F4F6;
}
.btn-modal-primary {
    background: linear-gradient(135deg, #0A4D8C, #1565C0);
    border: none;
    color: #fff;
}
.btn-modal-primary:hover {
    background: linear-gradient(135deg, #1565C0, #1976D2);
}

/* Hide inactive tabs */
[x-cloak] { display: none !important; }
</style>

{{-- MODAL --}}
<div class="modal fade" id="modalCreateAgent" tabindex="-1" aria-hidden="true" x-data="{ currentTab: 'identite', conjoints: [], enfants: [] }">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="create-modal-header position-relative">
                <div class="header-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h5>Enregistrer un nouvel agent</h5>
                <p>Les champs <span class="text-danger">*</span> sont obligatoires.</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- TABS --}}
            <div class="create-modal-tabs">
                <button type="button" class="create-tab-btn" :class="{ 'active': currentTab === 'identite' }" @click="currentTab = 'identite'">
                    <i class="fas fa-id-card"></i> Identité
                </button>
                <button type="button" class="create-tab-btn" :class="{ 'active': currentTab === 'donnees_sensibles' }" @click="currentTab = 'donnees_sensibles'">
                    <i class="fas fa-lock"></i> Données sensibles
                    <span class="tab-badge-lock"><i class="fas fa-shield-halved"></i></span>
                </button>
                <button type="button" class="create-tab-btn" :class="{ 'active': currentTab === 'pro' }" @click="currentTab = 'pro'">
                    <i class="fas fa-briefcase"></i> Professionnel
                </button>
                <button type="button" class="create-tab-btn" :class="{ 'active': currentTab === 'famille' }" @click="currentTab = 'famille'">
                    <i class="fas fa-users"></i> Famille
                </button>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('rh.agents.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- ════════════════════════════════════════
                     ZONE SCROLLABLE - Scrollbar ici
                     ════════════════════════════════════════ --}}
                <div class="create-modal-scroll-area">

                    {{-- TAB IDENTITÉ --}}
                    <div x-show="currentTab === 'identite'" x-cloak>
                        <div class="form-card">
                            <div class="form-card-title">
                                <i class="fas fa-user" style="color:#0A4D8C;"></i> Informations personnelles
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label-sm">Matricule <span class="required">*</span></label>
                                    <input type="text" name="matricule" class="form-input" value="{{ old('matricule') }}" placeholder="CHNP-00001" style="text-transform:uppercase;">
                                    <div class="form-hint">Saisir le matricule de l'agent</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Nom de famille <span class="required">*</span></label>
                                    <input type="text" name="nom" class="form-input" value="{{ old('nom') }}" placeholder="DIALLO" style="text-transform:uppercase;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Prénom <span class="required">*</span></label>
                                    <input type="text" name="prenom" class="form-input" value="{{ old('prenom') }}" placeholder="Amadou">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Date de naissance <span class="required">*</span></label>
                                    <input type="date" name="date_naissance" class="form-input" value="{{ old('date_naissance') }}"
                                           max="{{ now()->subYears(18)->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Lieu de naissance</label>
                                    <input type="text" name="lieu_naissance" class="form-input" value="{{ old('lieu_naissance') }}" placeholder="Dakar">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-sm">Sexe <span class="required">*</span></label>
                                    <select name="sexe" class="form-input">
                                        <option value="">—</option>
                                        <option value="M" @selected(old('sexe')==='M')>M</option>
                                        <option value="F" @selected(old('sexe')==='F')>F</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Situation familiale</label>
                                    <select name="situation_familiale" class="form-input">
                                        <option value="">— Choisir —</option>
                                        <option value="Célibataire" @selected(old('situation_familiale')==='Célibataire')>Célibataire</option>
                                        <option value="Marié" @selected(old('situation_familiale')==='Marié')>Marié(e)</option>
                                        <option value="Divorcé" @selected(old('situation_familiale')==='Divorcé')>Divorcé(e)</option>
                                        <option value="Veuf" @selected(old('situation_familiale')==='Veuf')>Veuf/Veuve</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Nationalité</label>
                                    <input type="text" name="nationalite" class="form-input" value="{{ old('nationalite', 'Sénégalaise') }}" placeholder="Sénégalaise">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Statut agent</label>
                                    <select name="statut_agent" class="form-input">
                                        <option value="Actif" selected>Actif</option>
                                        <option value="En_congé">En congé</option>
                                        <option value="Suspendu">Suspendu</option>
                                        <option value="Retraité">Retraité</option>
                                        <option value="Démissionnaire">Démissionnaire</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB DONNÉES SENSIBLES --}}
                    <div x-show="currentTab === 'donnees_sensibles'" x-cloak>
                        <div class="sensitive-notice">
                            <i class="fas fa-shield-halved"></i>
                            <span>L'adresse, le téléphone et le CNI sont stockés <strong>chiffrés</strong> (AES-256). La religion est une donnée personnelle sensible.</span>
                        </div>
                        <div class="form-card">
                            <div class="form-card-title">
                                <i class="fas fa-lock" style="color:#D97706;"></i> Données personnelles sensibles
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-sm">
                                        Adresse <i class="fas fa-lock" style="font-size:9px;color:#D97706;" title="Chiffré AES-256"></i>
                                    </label>
                                    <input type="text" name="adresse" class="form-input" value="{{ old('adresse') }}" placeholder="HLM Grand Yoff, Dakar">
                                    <div class="form-hint"><i class="fas fa-lock" style="font-size:9px;"></i> Stocké chiffré en base (AES-256)</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Email personnel</label>
                                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="agent@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">
                                        Téléphone <i class="fas fa-lock" style="font-size:9px;color:#D97706;" title="Chiffré AES-256"></i>
                                    </label>
                                    <input type="tel" name="telephone" class="form-input" value="{{ old('telephone') }}" placeholder="+221 77 000 00 00">
                                    <div class="form-hint"><i class="fas fa-lock" style="font-size:9px;"></i> Stocké chiffré en base (AES-256)</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">
                                        N° CNI <i class="fas fa-lock" style="font-size:9px;color:#D97706;" title="Chiffré AES-256"></i>
                                    </label>
                                    <input type="text" name="cni" class="form-input" value="{{ old('cni') }}" placeholder="1 XXXXXXX XXXXX XX">
                                    <div class="form-hint"><i class="fas fa-lock" style="font-size:9px;"></i> Carte Nationale d'Identité — Chiffré AES-256</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Religion</label>
                                    <input type="text" name="religion" class="form-input" value="{{ old('religion') }}" placeholder="Islam, Christianisme…">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle" style="font-size:9px;color:#D97706;"></i>
                                        Donnée personnelle sensible — accès restreint
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB PROFESSIONNEL --}}
                    <div x-show="currentTab === 'pro'" x-cloak>
                        <div class="form-card">
                            <div class="form-card-title">
                                <i class="fas fa-briefcase" style="color:#0A4D8C;"></i> Informations professionnelles
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-sm">Date de prise de service</label>
                                    <input type="date" name="date_prise_service" class="form-input" value="{{ old('date_prise_service') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Fonction</label>
                                    <input type="text" name="fontion" class="form-input" value="{{ old('fontion') }}" placeholder="Infirmier chef de poste">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Grade</label>
                                    <input type="text" name="grade" class="form-input" value="{{ old('grade') }}" placeholder="A1, P2, T3…">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Catégorie socio-professionnelle</label>
                                    <select name="categorie_cp" class="form-input">
                                        <option value="">— Choisir —</option>
                                        <option value="Cadre_Superieur">Cadre Supérieur</option>
                                        <option value="Cadre_Moyen">Cadre Moyen</option>
                                        <option value="Technicien_Superieur">Technicien Supérieur</option>
                                        <option value="Technicien">Technicien</option>
                                        <option value="Agent_Administratif">Agent Administratif</option>
                                        <option value="Agent_de_Service">Agent de Service</option>
                                        <option value="Commis_Administration">Commis d'Administration</option>
                                        <option value="Ouvrier">Ouvrier</option>
                                        <option value="Sans_Diplome">Sans Diplôme</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Famille d'emploi</label>
                                    <select name="famille_d_emploi" class="form-input">
                                        <option value="">— Choisir —</option>
                                        <option value="Corps_Médical">Corps Médical</option>
                                        <option value="Corps_Paramédical">Corps Paramédical</option>
                                        <option value="Corps_Administratif">Corps Administratif</option>
                                        <option value="Corps_Technique">Corps Technique</option>
                                        <option value="Corps_de_Soutien">Corps de Soutien</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Service</label>
                                    <select name="id_service" class="form-input">
                                        <option value="">— Aucun —</option>
                                        @foreach($services ?? [] as $s)
                                        <option value="{{ $s->id_service }}">{{ $s->nom_service }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Division</label>
                                    <select name="id_division" class="form-input">
                                        <option value="">— Aucune —</option>
                                        @foreach($divisions ?? [] as $d)
                                        <option value="{{ $d->id_division }}">{{ $d->nom_division }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB FAMILLE --}}
                    <div x-show="currentTab === 'famille'" x-cloak>
                        <div class="form-card">
                            <div class="form-card-title">
                                <i class="fas fa-heart" style="color:#EC4899;"></i> Conjoint(e)
                                <button type="button" class="btn-add-item ms-auto" @click="if(conjoints.length < 1) conjoints.push({nom:'',prenom:'',date:'',lien:'Épouse'})">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </div>
                            <template x-for="(c, i) in conjoints" :key="i">
                                <div class="famille-item">
                                    <button type="button" class="btn-remove" @click="conjoints.splice(i,1)"><i class="fas fa-times"></i></button>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label-sm" style="font-size:11px;">Nom</label>
                                            <input type="text" :name="`conjoints[${i}][nom_conj]`" class="form-input">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-sm" style="font-size:11px;">Prénom</label>
                                            <input type="text" :name="`conjoints[${i}][prenom_conj]`" class="form-input">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-sm" style="font-size:11px;">Date naissance</label>
                                            <input type="date" :name="`conjoints[${i}][date_naissance_conj]`" class="form-input">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-sm" style="font-size:11px;">Lien</label>
                                            <select :name="`conjoints[${i}][type_lien]`" class="form-input">
                                                <option value="Époux">Époux</option>
                                                <option value="Épouse">Épouse</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="conjoints.length === 0" class="empty-famille">
                                <i class="fas fa-heart-crack"></i> Aucun conjoint
                            </div>
                        </div>

                        <div class="form-card">
                            <div class="form-card-title">
                                <i class="fas fa-child" style="color:#059669;"></i> Enfants
                                <button type="button" class="btn-add-item ms-auto" @click="enfants.push({prenom:'',date:'',lien:'Fils'})">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </div>
                            <template x-for="(e, i) in enfants" :key="i">
                                <div class="famille-item">
                                    <button type="button" class="btn-remove" @click="enfants.splice(i,1)"><i class="fas fa-times"></i></button>
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <label class="form-label-sm" style="font-size:11px;">Prénom complet</label>
                                            <input type="text" :name="`enfants[${i}][prenom_complet]`" class="form-input">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-sm" style="font-size:11px;">Date naissance</label>
                                            <input type="date" :name="`enfants[${i}][date_naissance_enfant]`" class="form-input">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-sm" style="font-size:11px;">Lien</label>
                                            <select :name="`enfants[${i}][lien_filiation]`" class="form-input">
                                                <option value="Fils">Fils</option>
                                                <option value="Fille">Fille</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="enfants.length === 0" class="empty-famille">
                                <i class="fas fa-baby"></i> Aucun enfant
                            </div>
                        </div>
                    </div>

                </div>
                {{-- FIN ZONE SCROLLABLE --}}

                {{-- FOOTER - Toujours visible --}}
                <div class="create-modal-footer">
                    <button type="button" class="btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-modal btn-modal-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>