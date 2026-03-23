@extends('layouts.master')

@section('title', 'Modifier — '.$agent->nom_complet)
@section('page-title', 'Modifier le dossier agent')

@section('breadcrumb')
    <li><a href="{{ route('rh.agents.index') }}" style="color:#1565C0;">Personnel</a></li>
    <li><a href="{{ route('rh.agents.show', $agent->id_agent) }}" style="color:#1565C0;">{{ $agent->matricule }}</a></li>
    <li>Modifier</li>
@endsection

@push('styles')
<style>
/* ════════════════════════════════════════
   HEADER AGENT — INCHANGÉ
   ════════════════════════════════════════ */
.edit-agent-header {
    background: linear-gradient(135deg, #0A4D8C 0%, #1565C0 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(10, 77, 140, 0.3);
}
.edit-agent-header::after {
    content:'';position:absolute;right:-40px;top:-40px;
    width:150px;height:150px;border-radius:50%;
    background:rgba(255,255,255,.07);
}
.edit-avatar {
    width:72px;height:72px;border-radius:50%;
    border:3px solid rgba(255,255,255,.4);
    display:flex;align-items:center;justify-content:center;
    font-size:24px;font-weight:800;color:#fff;
    flex-shrink:0;overflow:hidden;
    background:rgba(255,255,255,.2);
    box-shadow: 0 4px 15px rgba(0,0,0,.2);
}
.edit-avatar img { width:100%;height:100%;object-fit:cover; }

/* ════════════════════════════════════════
   LAYOUT DEUX COLONNES AVEC SCROLL
   ════════════════════════════════════════ */
.edit-layout {
    display: flex;
    gap: 24px;
    align-items: flex-start;
}
.edit-main-col {
    flex: 1;
    min-width: 0;
    max-height: calc(100vh - 260px);
    overflow-y: auto;
    padding-right: 12px;
    scrollbar-width: thin;
    scrollbar-color: rgba(10, 77, 140, 0.25) transparent;
}
.edit-main-col::-webkit-scrollbar { width: 6px; }
.edit-main-col::-webkit-scrollbar-track { background: transparent; }
.edit-main-col::-webkit-scrollbar-thumb { 
    background: rgba(10, 77, 140, 0.25); 
    border-radius: 3px; 
}
.edit-main-col::-webkit-scrollbar-thumb:hover { 
    background: rgba(10, 77, 140, 0.4); 
}
.edit-sidebar-col {
    width: 320px;
    flex-shrink: 0;
    position: sticky;
    top: 20px;
}
@media (max-width: 1199.98px) {
    .edit-layout { flex-direction: column; }
    .edit-main-col { max-height: none; padding-right: 0; }
    .edit-sidebar-col { width: 100%; position: relative; top: 0; order: -1; }
}

/* ════════════════════════════════════════
   FORM CARDS
   ════════════════════════════════════════ */
.form-card {
    border-radius: 16px;
    overflow: hidden;
    background: var(--theme-panel-bg);
    border: 1px solid var(--theme-border);
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.form-card-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--theme-border);
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 700;
    background: var(--theme-bg-secondary);
    color: var(--theme-text);
}
.form-card-header .header-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}
.form-card-body {
    padding: 22px;
}

/* ════════════════════════════════════════
   FORM CONTROLS
   ════════════════════════════════════════ */
.form-label-custom {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
    color: var(--theme-text);
}
.form-control-custom, .form-select-custom {
    width: 100%;
    border-radius: 10px;
    padding: 11px 14px;
    font-size: 13px;
    border: 1.5px solid var(--theme-border);
    background: var(--theme-panel-bg);
    color: var(--theme-text);
    transition: all 150ms;
}
.form-control-custom:focus, .form-select-custom:focus {
    outline: none;
    border-color: #0A4D8C;
    box-shadow: 0 0 0 4px rgba(10,77,140,.1);
}
.form-control-custom.is-invalid, .form-select-custom.is-invalid {
    border-color: #EF4444;
}

/* ════════════════════════════════════════
   SECTION SEPARATORS
   ════════════════════════════════════════ */
.section-sep {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--theme-text-muted);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--theme-border);
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ════════════════════════════════════════
   SENSITIVE FIELDS + BOUTONS DÉCHIFFRER
   ════════════════════════════════════════ */
.sensitive-banner {
    background: linear-gradient(135deg, #FFF7ED, #FFEDD5);
    border: 1px solid #FED7AA;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 13px;
    color: #92400E;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.sensitive-banner i {
    font-size: 20px;
    color: #D97706;
}
.field-sensitive-wrap {
    position: relative;
}
.field-sensitive-wrap .form-control-custom {
    padding-right: 100px;
}
.btn-decrypt {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #D97706, #F59E0B);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 150ms;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
}
.btn-decrypt:hover {
    background: linear-gradient(135deg, #B45309, #D97706);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
    transform: translateY(-50%) scale(1.02);
}
.btn-decrypt.decrypted {
    background: linear-gradient(135deg, #059669, #10B981);
    box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
}
.field-locked {
    background: var(--theme-bg-secondary) !important;
    color: var(--theme-text-muted) !important;
}
.field-hint-lock {
    font-size: 10px;
    color: #D97706;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
}

/* ════════════════════════════════════════
   PHOTO UPLOAD
   ════════════════════════════════════════ */
.photo-upload-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
}
.photo-circle {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid var(--theme-border);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--theme-bg-secondary);
    transition: all 150ms;
}
.photo-circle:hover {
    border-color: #0A4D8C;
    transform: scale(1.02);
}
.photo-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.photo-circle-placeholder {
    font-size: 40px;
    color: #D1D5DB;
}
.photo-circle-overlay {
    position: absolute;
    inset: 0;
    background: rgba(10,77,140,.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 150ms;
    color: #fff;
    font-size: 22px;
}
.photo-circle:hover .photo-circle-overlay {
    opacity: 1;
}

/* ════════════════════════════════════════
   FAMILLE ITEMS
   ════════════════════════════════════════ */
.famille-row {
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 10px;
    position: relative;
}
.btn-remove-famille {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #DC2626;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 150ms;
    padding: 0;
}
.btn-remove-famille:hover {
    background: #DC2626;
    color: #fff;
    border-color: #DC2626;
}

/* ════════════════════════════════════════
   ACTION BUTTONS
   ════════════════════════════════════════ */
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 180ms;
}
.action-btn-primary {
    background: linear-gradient(135deg, #0A4D8C, #1565C0);
    color: #fff;
    box-shadow: 0 4px 15px rgba(10,77,140,.25);
}
.action-btn-primary:hover {
    background: linear-gradient(135deg, #1565C0, #1976D2);
    color: #fff;
    box-shadow: 0 6px 20px rgba(10,77,140,.35);
    transform: translateY(-1px);
}
.action-btn-outline {
    background: var(--theme-panel-bg);
    color: var(--theme-text);
    border: 1.5px solid var(--theme-border);
}
.action-btn-outline:hover {
    background: #EFF6FF;
    color: #0A4D8C;
    border-color: #BFDBFE;
}

/* ════════════════════════════════════════
   DARK MODE
   ════════════════════════════════════════ */
[data-theme="dark"] .edit-agent-header {
    background: linear-gradient(135deg, #0d1117, #0a4d8c);
}
[data-theme="dark"] .form-control-custom,
[data-theme="dark"] .form-select-custom {
    background: #0d1117;
    border-color: #30363d;
    color: #e6edf3;
}
[data-theme="dark"] .sensitive-banner {
    background: rgba(217,119,6,.1);
    border-color: rgba(217,119,6,.25);
    color: #fbbf24;
}
[data-theme="dark"] .field-locked {
    background: #161b22 !important;
}
</style>
@endpush

@section('content')

{{-- Header Agent —  INCHANGÉ --}}
<div class="edit-agent-header">
    <div class="edit-avatar">
        @if($agent->photo)
            <img src="{{ asset('storage/'.$agent->photo) }}" alt="">
        @else
            {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
        @endif
    </div>
    <div class="flex-grow-1" style="position:relative;z-index:1;">
        <div style="font-size:20px;font-weight:800;color:#fff;">{{ $agent->prenom }} {{ $agent->nom }}</div>
        <div style="font-size:14px;color:rgba(255,255,255,.85);margin-top:4px;">
            <code style="background:rgba(255,255,255,.18);padding:3px 10px;border-radius:8px;font-size:13px;color:#fff;letter-spacing:1.5px;font-weight:700;">{{ $agent->matricule }}</code>
            @if($agent->fonction) <span style="margin-left:8px;">· {{ $agent->fonction }}</span> @endif
        </div>
    </div>
    <div style="position:relative;z-index:1;">
        <a href="{{ route('rh.agents.show', $agent->id_agent) }}"
           style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px;padding:10px 18px;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:8px;font-weight:500;transition:all 150ms;">
            <i class="fas fa-arrow-left"></i> Annuler
        </a>
    </div>
</div>

{{-- FORMULAIRE --}}
<form method="POST" action="{{ route('rh.agents.update', $agent->id_agent) }}"
      enctype="multipart/form-data" x-data="editAgentForm()" id="formEditAgent">
@csrf @method('PUT')

<div class="edit-layout">

{{-- ═══════════ COLONNE PRINCIPALE AVEC SCROLL ═══════════ --}}
<div class="edit-main-col">

    {{-- IDENTITÉ --}}
    <div class="form-card mb-3">
        <div class="form-card-header">
            <div class="header-icon" style="background:#EFF6FF;">
                <i class="fas fa-id-card" style="color:#0A4D8C;"></i>
            </div>
            Identité civile
        </div>
        <div class="form-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom">Nom de famille <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control-custom @error('nom') is-invalid @enderror"
                           value="{{ old('nom', $agent->nom) }}" style="text-transform:uppercase;">
                    @error('nom') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" class="form-control-custom @error('prenom') is-invalid @enderror"
                           value="{{ old('prenom', $agent->prenom) }}">
                    @error('prenom') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Date de naissance <span class="text-danger">*</span></label>
                    <input type="date" name="date_naissance" class="form-control-custom @error('date_naissance') is-invalid @enderror"
                           value="{{ old('date_naissance', $agent->date_naissance?->format('Y-m-d')) }}"
                           max="{{ now()->subYears(18)->format('Y-m-d') }}">
                    @error('date_naissance') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Lieu de naissance <span class="text-danger">*</span></label>
                    <input type="text" name="lieu_naissance" class="form-control-custom @error('lieu_naissance') is-invalid @enderror"
                           value="{{ old('lieu_naissance', $agent->lieu_naissance) }}">
                    @error('lieu_naissance') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Nationalité</label>
                    <input type="text" name="nationalite" class="form-control-custom"
                           value="{{ old('nationalite', $agent->nationalite) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Sexe <span class="text-danger">*</span></label>
                    <select name="sexe" class="form-select-custom @error('sexe') is-invalid @enderror">
                        <option value="M" @selected(old('sexe', $agent->sexe)==='M')>Masculin</option>
                        <option value="F" @selected(old('sexe', $agent->sexe)==='F')>Féminin</option>
                    </select>
                    @error('sexe') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Situation familiale</label>
                    <select name="situation_familiale" class="form-select-custom">
                        <option value="">— Choisir —</option>
                        @foreach(['Célibataire','Marié','Divorcé','Veuf'] as $sf)
                        <option value="{{ $sf }}" @selected(old('situation_familiale', $agent->situation_familiale)===$sf)>{{ $sf }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Statut <span class="text-danger">*</span></label>
                    <select name="statut" class="form-select-custom @error('statut') is-invalid @enderror">
                        @foreach(['Actif','En_congé','Suspendu','Retraité'] as $st)
                        <option value="{{ $st }}" @selected(old('statut', $agent->statut)===$st)>
                            {{ $st === 'En_congé' ? 'En congé' : $st }}
                        </option>
                        @endforeach
                    </select>
                    @error('statut') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- COORDONNÉES SENSIBLES --}}
    <div class="form-card mb-3">
        <div class="form-card-header">
            <div class="header-icon" style="background:#FEE2E2;">
                <i class="fas fa-lock" style="color:#DC2626;"></i>
            </div>
            Coordonnées
            <span style="font-size:10px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:10px;margin-left:6px;font-weight:600;">
                <i class="fas fa-shield-halved" style="font-size:9px;"></i> AES-256
            </span>
        </div>
        <div class="form-card-body">
            <div class="sensitive-banner">
                <i class="fas fa-shield-halved"></i>
                <span>Données chiffrées. Cliquez sur <strong>Déchiffrer</strong> pour modifier.</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom">
                        Téléphone <i class="fas fa-lock" style="font-size:9px;color:#D97706;margin-left:4px;"></i>
                    </label>
                    <div class="field-sensitive-wrap">
                        <input type="text" name="telephone" id="field_telephone"
                               class="form-control-custom"
                               :class="{ 'field-locked': !decrypted.telephone }"
                               value="{{ old('telephone', $agent->telephone) }}"
                               placeholder="+221 77 000 00 00"
                               :readonly="!decrypted.telephone">
                        <button type="button" class="btn-decrypt"
                                :class="{ 'decrypted': decrypted.telephone }"
                                @click="toggleDecrypt('telephone')">
                            <i class="fas" :class="decrypted.telephone ? 'fa-eye' : 'fa-key'"></i>
                            <span x-text="decrypted.telephone ? 'Visible' : 'Déchiffrer'"></span>
                        </button>
                    </div>
                    <div class="field-hint-lock" x-show="!decrypted.telephone">
                        <i class="fas fa-lock"></i> Cliquez pour modifier
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Email professionnel</label>
                    <input type="email" name="email" class="form-control-custom @error('email') is-invalid @enderror"
                           value="{{ old('email', $agent->email) }}" placeholder="a.diallo@chnp.sn">
                    @error('email') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label-custom">
                        Adresse <i class="fas fa-lock" style="font-size:9px;color:#D97706;margin-left:4px;"></i>
                    </label>
                    <div class="field-sensitive-wrap" style="position:relative;">
                        <textarea name="adresse" id="field_adresse"
                                  class="form-control-custom" rows="2"
                                  :class="{ 'field-locked': !decrypted.adresse }"
                                  placeholder="Quartier, Commune, Ville…"
                                  :readonly="!decrypted.adresse"
                                  style="padding-right:110px;">{{ old('adresse', $agent->adresse) }}</textarea>
                        <button type="button" class="btn-decrypt" style="top:18px;transform:none;"
                                :class="{ 'decrypted': decrypted.adresse }"
                                @click="toggleDecrypt('adresse')">
                            <i class="fas" :class="decrypted.adresse ? 'fa-eye' : 'fa-key'"></i>
                            <span x-text="decrypted.adresse ? 'Visible' : 'Déchiffrer'"></span>
                        </button>
                    </div>
                    <div class="field-hint-lock" x-show="!decrypted.adresse">
                        <i class="fas fa-lock"></i> Cliquez pour modifier
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">
                        N° Assurance maladie <i class="fas fa-lock" style="font-size:9px;color:#D97706;margin-left:4px;"></i>
                    </label>
                    <div class="field-sensitive-wrap">
                        <input type="text" name="numero_assurance" id="field_assurance"
                               class="form-control-custom"
                               :class="{ 'field-locked': !decrypted.assurance }"
                               value="{{ old('numero_assurance', $agent->numero_assurance) }}"
                               placeholder="IPRES-XXXXXXXXX"
                               :readonly="!decrypted.assurance">
                        <button type="button" class="btn-decrypt"
                                :class="{ 'decrypted': decrypted.assurance }"
                                @click="toggleDecrypt('assurance')">
                            <i class="fas" :class="decrypted.assurance ? 'fa-eye' : 'fa-key'"></i>
                            <span x-text="decrypted.assurance ? 'Visible' : 'Déchiffrer'"></span>
                        </button>
                    </div>
                    <div class="field-hint-lock" x-show="!decrypted.assurance">
                        <i class="fas fa-lock"></i> Cliquez pour modifier
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- INFORMATIONS PROFESSIONNELLES --}}
    <div class="form-card mb-3">
        <div class="form-card-header">
            <div class="header-icon" style="background:#EFF6FF;">
                <i class="fas fa-briefcase" style="color:#0A4D8C;"></i>
            </div>
            Informations professionnelles
        </div>
        <div class="form-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-custom">Date de recrutement <span class="text-danger">*</span></label>
                    <input type="date" name="date_recrutement" class="form-control-custom @error('date_recrutement') is-invalid @enderror"
                           value="{{ old('date_recrutement', $agent->date_recrutement?->format('Y-m-d')) }}">
                    @error('date_recrutement') <div class="text-danger" style="font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Fonction</label>
                    <input type="text" name="fonction" class="form-control-custom"
                           value="{{ old('fonction', $agent->fonction) }}" placeholder="Infirmier chef de poste">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Grade</label>
                    <input type="text" name="grade" class="form-control-custom"
                           value="{{ old('grade', $agent->grade) }}" placeholder="IES2">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Catégorie socio-professionnelle</label>
                    <select name="categorie_cp" class="form-select-custom">
                        <option value="">— Choisir —</option>
                        @foreach([
                            'Cadre_Superieur'=>'Cadre Supérieur','Cadre_Moyen'=>'Cadre Moyen',
                            'Technicien_Superieur'=>'Technicien Supérieur','Technicien'=>'Technicien',
                            'Agent_Administratif'=>'Agent Administratif','Agent_de_Service'=>'Agent de Service',
                            'Commis_Administration'=>"Commis d'Administration",'Ouvrier'=>'Ouvrier','Sans_Diplome'=>'Sans Diplôme',
                        ] as $val => $label)
                        <option value="{{ $val }}" @selected(old('categorie_cp', $agent->categorie_cp)===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Service</label>
                    <select name="id_service" class="form-select-custom">
                        <option value="">— Aucun —</option>
                        @foreach($services as $s)
                        <option value="{{ $s->id_service }}" @selected(old('id_service', $agent->id_service)==$s->id_service)>{{ $s->nom_service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Division</label>
                    <select name="id_division" class="form-select-custom">
                        <option value="">— Aucune —</option>
                        @foreach($divisions as $d)
                        <option value="{{ $d->id_division }}" @selected(old('id_division', $agent->id_division)==$d->id_division)>{{ $d->nom_division }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- FAMILLE --}}
    <div class="form-card mb-3">
        <div class="form-card-header">
            <div class="header-icon" style="background:#F3E8FF;">
                <i class="fas fa-users" style="color:#7C3AED;"></i>
            </div>
            Famille
        </div>
        <div class="form-card-body">
            <div class="section-sep">
                <i class="fas fa-heart" style="color:#EC4899;"></i> Conjoint(e)
                <button type="button" class="ms-auto action-btn action-btn-outline"
                        style="font-size:11px;padding:6px 12px;border-radius:8px;"
                        @click="addConjoint" :disabled="conjoints.length >= 1">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </div>
            <template x-for="(c, i) in conjoints" :key="i">
                <div class="famille-row">
                    <button type="button" class="btn-remove-famille" @click="removeConjoint(i)">
                        <i class="fas fa-xmark"></i>
                    </button>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label-custom" style="font-size:11px;">Nom</label>
                            <input type="text" :name="`conjoints[${i}][nom_conj]`" x-model="c.nom_conj" class="form-control-custom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom" style="font-size:11px;">Prénom</label>
                            <input type="text" :name="`conjoints[${i}][prenom_conj]`" x-model="c.prenom_conj" class="form-control-custom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom" style="font-size:11px;">Date naissance</label>
                            <input type="date" :name="`conjoints[${i}][date_naissance_conj]`" x-model="c.date_naissance_conj" class="form-control-custom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom" style="font-size:11px;">Lien</label>
                            <select :name="`conjoints[${i}][type_lien]`" x-model="c.type_lien" class="form-select-custom">
                                <option value="Époux">Époux</option>
                                <option value="Épouse">Épouse</option>
                            </select>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="conjoints.length === 0" style="font-size:13px;color:var(--theme-text-muted);margin-bottom:20px;">
                Aucun conjoint enregistré.
            </div>

            <div class="section-sep mt-4">
                <i class="fas fa-child" style="color:#059669;"></i>
                Enfants (<span x-text="enfants.length"></span>)
                <button type="button" class="ms-auto action-btn action-btn-outline"
                        style="font-size:11px;padding:6px 12px;border-radius:8px;"
                        @click="addEnfant">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </div>
            <template x-for="(e, i) in enfants" :key="i">
                <div class="famille-row">
                    <button type="button" class="btn-remove-famille" @click="removeEnfant(i)">
                        <i class="fas fa-xmark"></i>
                    </button>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label-custom" style="font-size:11px;">Prénom complet</label>
                            <input type="text" :name="`enfants[${i}][prenom_complet]`" x-model="e.prenom_complet" class="form-control-custom">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom" style="font-size:11px;">Date de naissance</label>
                            <input type="date" :name="`enfants[${i}][date_naissance_enfant]`" x-model="e.date_naissance_enfant" class="form-control-custom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom" style="font-size:11px;">Lien</label>
                            <select :name="`enfants[${i}][lien_filiation]`" x-model="e.lien_filiation" class="form-select-custom">
                                <option value="Fils">Fils</option>
                                <option value="Fille">Fille</option>
                            </select>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="enfants.length === 0" style="font-size:13px;color:var(--theme-text-muted);">Aucun enfant enregistré.</div>
        </div>
    </div>

</div>

{{-- ═══════════ SIDEBAR ═══════════ --}}
<div class="edit-sidebar-col">
    <div class="form-card mb-3">
        <div class="form-card-header">
            <div class="header-icon" style="background:#EFF6FF;">
                <i class="fas fa-camera" style="color:#0A4D8C;"></i>
            </div>
            Photo de profil
        </div>
        <div class="form-card-body">
            <div class="photo-upload-wrap">
                <label for="photoInput" class="photo-circle">
                    @if($agent->photo)
                    <img id="photoPreview" src="{{ asset('storage/'.$agent->photo) }}" alt="">
                    @else
                    <div id="photoPreview" style="display:none;width:100%;height:100%;"></div>
                    <div class="photo-circle-placeholder" id="photoPlaceholder">
                        <i class="fas fa-user"></i>
                    </div>
                    @endif
                    <div class="photo-circle-overlay"><i class="fas fa-camera"></i></div>
                </label>
                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png"
                       class="d-none" onchange="previewPhoto(this)">
                <label for="photoInput" class="action-btn action-btn-outline" style="font-size:12px;padding:8px 16px;cursor:pointer;">
                    <i class="fas fa-upload"></i>
                    {{ $agent->photo ? 'Changer' : 'Choisir' }}
                </label>
                <div style="font-size:11px;color:#9CA3AF;text-align:center;">JPEG/PNG · Max 2 Mo</div>
            </div>
        </div>
    </div>

    <div class="form-card mb-3">
        <div class="form-card-header">
            <div class="header-icon" style="background:#ECFDF5;">
                <i class="fas fa-id-badge" style="color:#059669;"></i>
            </div>
            Matricule
        </div>
        <div class="form-card-body text-center">
            <div style="font-size:22px;font-weight:800;letter-spacing:2px;color:#0A4D8C;padding:10px 0;">
                {{ $agent->matricule }}
            </div>
            <div style="font-size:11px;color:#9CA3AF;display:flex;align-items:center;justify-content:center;gap:5px;">
                <i class="fas fa-lock" style="font-size:9px;"></i> Non modifiable
            </div>
        </div>
    </div>

    <div style="position:sticky;top:20px;">
        <button type="submit" class="action-btn action-btn-primary w-100 justify-content-center mb-3">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <a href="{{ route('rh.agents.show', $agent->id_agent) }}"
           class="action-btn action-btn-outline w-100 justify-content-center">
            <i class="fas fa-xmark"></i> Annuler
        </a>
        <div style="font-size:11px;color:var(--theme-text-muted);text-align:center;margin-top:14px;">
            <i class="fas fa-clock me-1"></i>
            Modifié {{ $agent->updated_at?->diffForHumans() ?? '—' }}
        </div>
    </div>
</div>

</div>
</form>

@endsection

@php
    $conjointsData = old('conjoints', $agent->conjoints?->map(fn($c) => [
        'nom_conj' => $c->nom_conj,
        'prenom_conj' => $c->prenom_conj,
        'date_naissance_conj' => $c->date_naissance_conj?->format('Y-m-d') ?? '',
        'type_lien' => $c->type_lien,
    ])?->values()?->toArray() ?? []);

    $enfantsData = old('enfants', $agent->enfants?->map(fn($e) => [
        'prenom_complet' => $e->prenom_complet,
        'date_naissance_enfant' => $e->date_naissance_enfant?->format('Y-m-d') ?? '',
        'lien_filiation' => $e->lien_filiation,
    ])?->values()?->toArray() ?? []);
@endphp

@push('scripts')
<script>
function editAgentForm() {
    return {
        conjoints: @json($conjointsData),
        enfants: @json($enfantsData),
        decrypted: { telephone: false, adresse: false, assurance: false },
        
        toggleDecrypt(field) {
            this.decrypted[field] = !this.decrypted[field];
            if (this.decrypted[field]) {
                const id = field === 'assurance' ? 'field_assurance' : 'field_' + field;
                setTimeout(() => document.getElementById(id)?.focus(), 50);
            }
        },
        addConjoint() {
            if (this.conjoints.length < 1)
                this.conjoints.push({ nom_conj:'', prenom_conj:'', date_naissance_conj:'', type_lien:'Épouse' });
        },
        removeConjoint(i) { this.conjoints.splice(i, 1); },
        addEnfant() { this.enfants.push({ prenom_complet:'', date_naissance_enfant:'', lien_filiation:'Fils' }); },
        removeEnfant(i) { this.enfants.splice(i, 1); },
    };
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            let preview = document.getElementById('photoPreview');
            const placeholder = document.getElementById('photoPlaceholder');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'photoPreview';
                img.src = e.target.result;
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                preview.replaceWith(img);
            }
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush