@extends('layouts.master')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Dossier Personnel')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li>Mon profil</li>
@endsection

@push('styles')
<style>
/* ── Layout ─────────────────────────────────────────── */
.profile-header { border-radius:14px;padding:28px 32px;position:relative;overflow:hidden; }
.profile-header::before { content:'';position:absolute;top:0;right:0;width:200px;height:200px;border-radius:50%;background:rgba(10,77,140,.06);transform:translate(60px,-60px); }

/* Avatar upload */
.avatar-wrap { position:relative;display:inline-block;cursor:pointer; }
.avatar-img  { width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 16px rgba(10,77,140,.20); }
.avatar-placeholder { width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:700;border:3px solid #fff;box-shadow:0 4px 16px rgba(10,77,140,.20); }
.avatar-overlay { position:absolute;inset:0;border-radius:50%;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 200ms;color:#fff;font-size:18px; }
.avatar-wrap:hover .avatar-overlay { opacity:1; }

/* Tabs */
.profil-tabs { display:flex;gap:4px;border-bottom:2px solid var(--theme-border);margin-bottom:24px; }
.profil-tab { padding:10px 20px;font-size:13px;font-weight:600;color:var(--theme-text-muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;border-radius:6px 6px 0 0;transition:all 180ms;text-decoration:none;display:flex;align-items:center;gap:6px;background:none;border-left:none;border-right:none;border-top:none; }
.profil-tab:hover { color:var(--theme-text);background:var(--theme-bg-secondary); }
.profil-tab.active { color:#0A4D8C;border-bottom-color:#0A4D8C;background:rgba(10,77,140,.06); }

/* Fields */
.field-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:3px; }
.field-value { font-size:14px;font-weight:500;color:var(--theme-text); }
.field-empty { font-size:14px;color:var(--theme-text-muted);font-style:italic; }

/* Encrypted badge */
.enc-badge { display:inline-flex;align-items:center;gap:4px;font-size:10px;padding:2px 7px;border-radius:10px;background:rgba(245,158,11,.12);color:#D97706;font-weight:600; }

/* Section title */
.s-title { font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--theme-border);display:flex;align-items:center;gap:8px; }

/* Family chips */
.fam-card { border-radius:10px;padding:14px 16px;border-left:4px solid;background:var(--theme-bg-secondary); }
.fam-avatar { width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0; }

/* Action buttons */
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms;white-space:nowrap; }
.action-btn-primary { background:#0A4D8C;color:#fff; }
.action-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1.5px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C;border-color:#BFDBFE; }

/* Password form */
.pwd-input-wrap { position:relative; }
.pwd-toggle { position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--theme-text-muted);cursor:pointer;padding:0; }
.form-ctrl { border-radius:8px;font-size:13px;padding:10px 14px;border:1.5px solid var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);transition:border-color 200ms;width:100%; }
.form-ctrl:focus { border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.12);outline:none; }

/* Badge statuts */
.badge-statut { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-actif  { background:#D1FAE5;color:#065F46; }
.badge-conge  { background:#FEF3C7;color:#92400E; }
.badge-suspendu { background:#FEE2E2;color:#991B1B; }
.badge-retraite { background:#F3F4F6;color:#374151; }

@keyframes toastIn { from { opacity:0;transform:translateX(40px); } to { opacity:1;transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-xl-10">

{{-- ── En-tête Profil ─────────────────────────────────────── --}}
<div class="profile-header card border-0 shadow-sm mb-4" style="background:var(--theme-panel-bg);">
    <div class="d-flex align-items-center gap-4 flex-wrap">

        {{-- Avatar + upload --}}
        <form action="{{ route('agent.profil.photo') }}" method="POST" enctype="multipart/form-data" id="formPhoto">
            @csrf
            <div class="avatar-wrap" onclick="document.getElementById('inputPhoto').click()" title="Changer la photo">
                @if($agent->photo)
                    <img src="{{ asset('storage/'.$agent->photo) }}" class="avatar-img" alt="Photo profil">
                @else
                    <div class="avatar-placeholder">
                        {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
                    </div>
                @endif
                <div class="avatar-overlay"><i class="fas fa-camera"></i></div>
            </div>
            <input type="file" id="inputPhoto" name="photo" class="d-none" accept="image/jpeg,image/png,image/webp"
                onchange="document.getElementById('formPhoto').submit()">
        </form>

        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1" style="color:var(--theme-text);font-size:20px;">
                {{ $agent->prenom }} {{ $agent->nom }}
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <code style="font-size:12px;background:rgba(10,77,140,.08);color:#0A4D8C;padding:2px 8px;border-radius:6px;">
                    {{ $agent->matricule }}
                </code>
                @if($agent->famille_d_emploi)<span style="color:var(--theme-text-muted);font-size:13px;">{{ str_replace('_', ' ', $agent->famille_d_emploi) }}</span>@endif
                @if($agent->service)
                    <span style="color:var(--theme-text-muted);font-size:13px;">· {{ $agent->service->nom_service }}</span>
                @endif
            </div>
            @php $st = strtolower($agent->statut_agent ?? 'actif'); @endphp
            @if($st === 'actif')
                <span class="badge-statut badge-actif"><i class="fas fa-circle me-1" style="font-size:7px;"></i>Actif</span>
            @elseif($st === 'en_congé')
                <span class="badge-statut badge-conge"><i class="fas fa-umbrella-beach me-1"></i>En congé</span>
            @elseif($st === 'suspendu')
                <span class="badge-statut badge-suspendu"><i class="fas fa-ban me-1"></i>Suspendu</span>
            @else
                <span class="badge-statut badge-retraite">{{ ucfirst($agent->statut_agent ?? '') }}</span>
            @endif
        </div>

        {{-- Raccourcis --}}
        <div class="d-flex flex-column gap-2">
            <a href="{{ route('agent.conges.index') }}" class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                <i class="fas fa-umbrella-beach" style="color:#F59E0B;"></i> Mes congés
            </a>
            <a href="{{ route('agent.docs.index') }}" class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                <i class="fas fa-file-alt" style="color:#3B82F6;"></i> Mes documents
            </a>
            <a href="{{ route('agent.mon-parcours') }}" class="action-btn action-btn-outline" style="font-size:12px;padding:7px 14px;">
                <i class="fas fa-route" style="color:#10B981;"></i> Mon parcours
            </a>
        </div>
    </div>
</div>

{{-- ── Onglets ────────────────────────────────────────────── --}}
<div class="profil-tabs" id="profilTabs">
    <button class="profil-tab active" data-tab="dossier">
        <i class="fas fa-id-card"></i> Mon Dossier
    </button>
    <button class="profil-tab" data-tab="famille">
        <i class="fas fa-users"></i> Ma Famille
        @php $nbFamille = $agent->conjoints->count() + $agent->enfants->count(); @endphp
        @if($nbFamille > 0)
            <span style="background:#DBEAFE;color:#1E40AF;border-radius:10px;padding:1px 7px;font-size:10px;">{{ $nbFamille }}</span>
        @endif
    </button>
    <button class="profil-tab" data-tab="securite">
        <i class="fas fa-shield-alt"></i> Sécurité
    </button>
</div>

{{-- ── TAB : DOSSIER ─────────────────────────────────────── --}}
<div id="tab-dossier" class="tab-pane">
    <div class="row g-4">

        {{-- Infos personnelles --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-user" style="color:#0A4D8C;"></i> Informations personnelles
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="field-label">Nom</div>
                            <div class="field-value">{{ $agent->nom }}</div>
                        </div>
                        <div class="col-6">
                            <div class="field-label">Prénom</div>
                            <div class="field-value">{{ $agent->prenom }}</div>
                        </div>
                        <div class="col-6">
                            <div class="field-label">Date de naissance</div>
                            <div class="field-value">{{ $agent->date_naissance?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="field-label">Sexe</div>
                            <div class="field-value">{{ $agent->sexe === 'M' ? 'Masculin' : 'Féminin' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="field-label">Statut contrat</div>
                            <div class="field-value">{{ $agent->statut_agent === 'En_congé' ? 'En congé' : ($agent->statut_agent ?? '—') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="field-label">Religion</div>
                            <div class="field-value">
                                @if($agent->religion)
                                    {{ $agent->religion }}
                                @else
                                    <span class="field-empty">Non renseignée</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Coordonnées --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-address-card" style="color:#0A4D8C;"></i>
                        Coordonnées
                        <span class="enc-badge ms-1"><i class="fas fa-lock"></i> AES-256</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="field-label">Téléphone</div>
                            <div class="field-value">
                                @if($agent->telephone)
                                    <i class="fas fa-lock text-warning me-1" style="font-size:11px;" title="Chiffré AES-256"></i>
                                    {{ $agent->telephone_masque }}
                                @else
                                    <span class="field-empty">Non renseigné</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="field-label">
                                N° CNI
                                <i class="fas fa-lock text-warning ms-1" style="font-size:9px;" title="Chiffré AES-256"></i>
                            </div>
                            <div class="field-value">
                                @if($agent->cni)
                                    <i class="fas fa-lock text-warning me-1" style="font-size:11px;" title="Chiffré AES-256"></i>
                                    <span class="enc-badge me-1"><i class="fas fa-shield-halved"></i> AES-256</span>
                                    {{ $agent->cni_masque }}
                                @else
                                    <span class="field-empty">Non renseigné</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 p-2 rounded" style="background:rgba(245,158,11,.07);border:1px dashed #FDE68A;">
                        <p class="mb-0" style="font-size:11px;color:#92400E;">
                            <i class="fas fa-info-circle me-1"></i>
                            Ces données sont chiffrées (AES-256) en base de données. Pour les modifier, contactez le service RH.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations professionnelles --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-briefcase" style="color:#0A4D8C;"></i> Informations professionnelles
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="field-label">Matricule</div>
                            <div class="field-value font-monospace" style="color:#0A4D8C;">{{ $agent->matricule }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Famille d'emploi</div>
                            <div class="field-value">{{ $agent->famille_d_emploi ? str_replace('_', ' ', $agent->famille_d_emploi) : '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Catégorie CSP</div>
                            <div class="field-value">{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Statut agent</div>
                            <div class="field-value">{{ $agent->statut_agent === 'En_congé' ? 'En congé' : ($agent->statut_agent ?? '—') }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Service</div>
                            <div class="field-value">{{ $agent->service?->nom_service ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Division</div>
                            <div class="field-value">{{ $agent->division?->nom_division ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contrat actif --}}
        @if($agent->contratActif)
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-file-contract" style="color:#10B981;"></i> Contrat en cours
                    </div>
                    @php $contrat = $agent->contratActif; @endphp
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="field-label">Type</div>
                            <div class="field-value">{{ $contrat->type_contrat ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Début</div>
                            <div class="field-value">{{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Fin</div>
                            <div class="field-value">
                                @if($contrat->date_fin)
                                    {{ $contrat->date_fin->format('d/m/Y') }}
                                    @if($contrat->date_fin->isPast())
                                        <span style="color:#EF4444;font-size:11px;"> (expiré)</span>
                                    @elseif($contrat->date_fin->diffInDays(now()) < 60)
                                        <span style="color:#F59E0B;font-size:11px;"> (bientôt)</span>
                                    @endif
                                @else
                                    <span style="color:#10B981;">CDI / Permanent</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="field-label">Statut</div>
                            <div>
                                <span style="background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                                    {{ $contrat->statut_contrat ?? 'Actif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>{{-- /tab-dossier --}}

{{-- ── TAB : FAMILLE ────────────────────────────────────── --}}
<div id="tab-famille" class="tab-pane d-none">

    {{-- Conjoints --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-body p-4">
            <div class="s-title">
                <i class="fas fa-ring" style="color:#0A4D8C;"></i>
                Conjoint(s)
                <span style="background:var(--theme-bg-secondary);color:var(--theme-text-muted);border-radius:10px;padding:1px 8px;font-size:11px;">{{ $agent->conjoints->count() }}</span>
            </div>

            @forelse($agent->conjoints as $conjoint)
            <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="fam-avatar" style="background:#EFF6FF;color:#0A4D8C;">
                    {{ strtoupper(substr($conjoint->prenom_conj,0,1).substr($conjoint->nom_conj,0,1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="fw-600" style="color:var(--theme-text);font-size:14px;">
                        {{ $conjoint->prenom_conj }} {{ $conjoint->nom_conj }}
                    </div>
                    <div style="font-size:12px;color:var(--theme-text-muted);">
                        @if($conjoint->type_lien) {{ $conjoint->type_lien }} @endif
                        @if($conjoint->date_naissance_conj)
                            · Né(e) le {{ $conjoint->date_naissance_conj->format('d/m/Y') }}
                            <span style="color:var(--theme-text-muted);">({{ $conjoint->age }} ans)</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4" style="color:var(--theme-text-muted);">
                <i class="fas fa-ring fa-2x mb-2 d-block" style="opacity:.2;"></i>
                <small>Aucun conjoint enregistré</small>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Enfants --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
        <div class="card-body p-4">
            <div class="s-title">
                <i class="fas fa-child" style="color:#10B981;"></i>
                Enfant(s)
                <span style="background:var(--theme-bg-secondary);color:var(--theme-text-muted);border-radius:10px;padding:1px 8px;font-size:11px;">{{ $agent->enfants->count() }}</span>
            </div>

            @forelse($agent->enfants as $enfant)
            <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="fam-avatar" style="background:#ECFDF5;color:#059669;">
                    {{ strtoupper(substr($enfant->prenom_complet, 0, 2)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="fw-600" style="color:var(--theme-text);font-size:14px;">
                        {{ $enfant->prenom_complet }}
                    </div>
                    <div style="font-size:12px;color:var(--theme-text-muted);">
                        @if($enfant->lien_filiation) {{ $enfant->lien_filiation }} @endif
                        @if($enfant->date_naissance_enfant)
                            · Né(e) le {{ $enfant->date_naissance_enfant->format('d/m/Y') }}
                            <span>({{ $enfant->age }} ans)</span>
                            @if($enfant->est_mineur)
                                <span style="background:#FEF3C7;color:#92400E;padding:1px 6px;border-radius:6px;font-size:10px;">Mineur</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4" style="color:var(--theme-text-muted);">
                <i class="fas fa-child fa-2x mb-2 d-block" style="opacity:.2;"></i>
                <small>Aucun enfant enregistré</small>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Signalement modification --}}
    <div class="alert d-flex align-items-start gap-3" style="border-radius:10px;background:#F0F9FF;border:1px solid #BAE6FD;">
        <i class="fas fa-info-circle mt-1" style="color:#0284C7;font-size:16px;"></i>
        <div style="font-size:13px;color:#075985;">
            <strong>Modification de situation familiale</strong><br>
            Pour toute mise à jour (naissance, mariage, divorce…), veuillez contacter le service RH
            avec les justificatifs nécessaires, ou soumettre une demande de document administratif.
            <div class="mt-2">
                <a href="{{ route('agent.docs.create') }}" class="action-btn action-btn-primary" style="font-size:12px;padding:7px 14px;">
                    <i class="fas fa-paper-plane"></i> Contacter le RH
                </a>
            </div>
        </div>
    </div>

</div>{{-- /tab-famille --}}

{{-- ── TAB : SÉCURITÉ ───────────────────────────────────── --}}
<div id="tab-securite" class="tab-pane d-none">
    <div class="row g-4">

        {{-- Changer mot de passe --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-key" style="color:#0A4D8C;"></i> Changer mon mot de passe
                    </div>

                    <form action="{{ route('agent.profil.password') }}" method="POST" id="formPassword">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="field-label d-block mb-1">Mot de passe actuel <span class="text-danger">*</span></label>
                            <div class="pwd-input-wrap">
                                <input type="password" name="current_password" id="pwd_current"
                                    class="form-ctrl @error('current_password') is-invalid @enderror"
                                    placeholder="Votre mot de passe actuel" autocomplete="current-password">
                                <button type="button" class="pwd-toggle" onclick="togglePwd('pwd_current', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger mt-1" style="font-size:12px;"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="field-label d-block mb-1">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <div class="pwd-input-wrap">
                                <input type="password" name="password" id="pwd_new"
                                    class="form-ctrl @error('password') is-invalid @enderror"
                                    placeholder="Minimum 8 caractères" autocomplete="new-password">
                                <button type="button" class="pwd-toggle" onclick="togglePwd('pwd_new', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                            {{-- Indicateur force --}}
                            <div id="pwdStrength" class="mt-1 d-none">
                                <div style="height:4px;border-radius:2px;background:var(--theme-border);">
                                    <div id="pwdBar" style="height:100%;border-radius:2px;transition:all 300ms;width:0%;"></div>
                                </div>
                                <span id="pwdLabel" style="font-size:11px;"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="field-label d-block mb-1">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                            <div class="pwd-input-wrap">
                                <input type="password" name="password_confirmation" id="pwd_confirm"
                                    class="form-ctrl" placeholder="Répétez le nouveau mot de passe" autocomplete="new-password">
                                <button type="button" class="pwd-toggle" onclick="togglePwd('pwd_confirm', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="matchMsg" class="mt-1 d-none" style="font-size:12px;"></div>
                        </div>

                        <button type="submit" class="action-btn action-btn-primary">
                            <i class="fas fa-lock"></i> Modifier le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Infos sécurité compte --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-shield-alt" style="color:#10B981;"></i> Informations du compte
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="field-label">Identifiant de connexion</div>
                            <div class="field-value font-monospace">{{ auth()->user()->login ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="field-label">Rôle(s)</div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach(auth()->user()->getRoleNames() as $role)
                                    <span style="background:#DBEAFE;color:#1E40AF;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="field-label">Statut du compte</div>
                            <div>
                                @php $statut = auth()->user()->statut_compte ?? 'Actif'; @endphp
                                @if($statut === 'Actif')
                                    <span style="background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                                        <i class="fas fa-circle me-1" style="font-size:7px;"></i>Actif
                                    </span>
                                @else
                                    <span style="background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                                        {{ $statut }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if(auth()->user()->derniere_connexion)
                        <div class="col-12">
                            <div class="field-label">Dernière connexion</div>
                            <div class="field-value" style="font-size:13px;">
                                {{ auth()->user()->derniere_connexion->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Conseils sécurité --}}
            <div class="card border-0 shadow-sm mt-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4">
                    <div class="s-title">
                        <i class="fas fa-lightbulb" style="color:#F59E0B;"></i> Bonnes pratiques
                    </div>
                    <ul class="list-unstyled mb-0" style="font-size:13px;color:var(--theme-text-muted);">
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:#10B981;"></i>Minimum 8 caractères</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:#10B981;"></i>Mélangez lettres, chiffres et symboles</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:#10B981;"></i>Ne réutilisez pas vos anciens mots de passe</li>
                        <li><i class="fas fa-check-circle me-2" style="color:#10B981;"></i>Ne partagez jamais votre mot de passe</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>{{-- /tab-securite --}}

</div>{{-- col --}}
</div>{{-- row --}}
</div>{{-- container --}}
@endsection

@push('scripts')
<script>
// ── Toast ────────────────────────────────────────────────
function showToast(message, type) {
    const cfg = { success:{bg:'#10B981',icon:'fa-check-circle'}, error:{bg:'#EF4444',icon:'fa-exclamation-circle'} };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `<div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:400px;animation:toastIn .3s ease;"><i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i><span>${message}</span><button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4500);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
@endif
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => showToast('Veuillez corriger les erreurs ci-dessous.', 'error'));
@endif

// ── Tabs ────────────────────────────────────────────────
function activateTab(tabName) {
    document.querySelectorAll('.profil-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('d-none'));
    document.querySelector(`[data-tab="${tabName}"]`)?.classList.add('active');
    document.getElementById(`tab-${tabName}`)?.classList.remove('d-none');
    localStorage.setItem('profil_tab', tabName);
}
document.querySelectorAll('.profil-tab').forEach(btn => {
    btn.addEventListener('click', () => activateTab(btn.dataset.tab));
});

// Restaurer onglet actif (après soumission formulaire)
document.addEventListener('DOMContentLoaded', () => {
    @if(session('active_tab'))
        activateTab(@json(session('active_tab')));
    @elseif($errors->has('current_password') || $errors->has('password'))
        activateTab('securite');
    @else
        const saved = localStorage.getItem('profil_tab');
        if (saved) activateTab(saved);
    @endif
});

// ── Toggle mot de passe ──────────────────────────────────
function togglePwd(inputId, btn) {
    const input = document.getElementById(inputId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
}

// ── Force mot de passe ───────────────────────────────────
document.getElementById('pwd_new')?.addEventListener('input', function() {
    const v = this.value;
    const div = document.getElementById('pwdStrength');
    const bar = document.getElementById('pwdBar');
    const lbl = document.getElementById('pwdLabel');
    if (!v) { div.classList.add('d-none'); return; }
    div.classList.remove('d-none');
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const levels = [
        {w:'20%', bg:'#EF4444', txt:'Très faible'},
        {w:'40%', bg:'#F59E0B', txt:'Faible'},
        {w:'65%', bg:'#3B82F6', txt:'Moyen'},
        {w:'100%', bg:'#10B981', txt:'Fort'},
    ];
    const lvl = levels[Math.max(0, score - 1)];
    bar.style.width = lvl.w;
    bar.style.background = lvl.bg;
    lbl.textContent = lvl.txt;
    lbl.style.color = lvl.bg;
});

// ── Correspondance confirmation ──────────────────────────
document.getElementById('pwd_confirm')?.addEventListener('input', function() {
    const newPwd = document.getElementById('pwd_new').value;
    const msg = document.getElementById('matchMsg');
    if (!this.value) { msg.classList.add('d-none'); return; }
    msg.classList.remove('d-none');
    if (this.value === newPwd) {
        msg.innerHTML = '<i class="fas fa-check-circle me-1" style="color:#10B981;"></i><span style="color:#10B981;">Les mots de passe correspondent</span>';
    } else {
        msg.innerHTML = '<i class="fas fa-times-circle me-1" style="color:#EF4444;"></i><span style="color:#EF4444;">Les mots de passe ne correspondent pas</span>';
    }
});
</script>
@endpush
