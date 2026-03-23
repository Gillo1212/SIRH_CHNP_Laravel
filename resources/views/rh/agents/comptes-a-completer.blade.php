@extends('layouts.master')

@section('title', 'Comptes à compléter')
@section('page-title', 'Comptes à compléter')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.agents.index') }}" style="color:#1565C0;">Personnel</a></li>
    <li>Comptes à compléter</li>
@endsection

@push('styles')
<style>
[x-cloak] { display: none !important; }

/* ── Liste ─────────────────────────────────────────────── */
.compte-row {
    display: flex; align-items: center; gap: 16px;
    padding: 14px 20px; border-radius: 10px;
    border: 1px solid var(--theme-border);
    background: var(--theme-panel-bg);
    transition: box-shadow 150ms, border-color 150ms;
    margin-bottom: 10px;
}
.compte-row:hover { box-shadow: 0 4px 16px rgba(10,77,140,.08); border-color: #93C5FD; }
.compte-avatar {
    width: 42px; height: 42px; border-radius: 50%;
    background: linear-gradient(135deg, #1D4ED8, #3B82F6);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px; font-weight: 700; flex-shrink: 0;
}
.compte-login { font-size: 14px; font-weight: 600; }
.compte-meta  { font-size: 12px; color: var(--theme-text-muted); margin-top: 2px; }
.badge-pending {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    background: #FEF3C7; color: #92400E;
}
[data-theme="dark"] .badge-pending { background: rgba(245,158,11,0.2); color: #fbbf24; }
.action-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px; font-size: 12px;
    font-weight: 500; text-decoration: none; border: none; cursor: pointer;
    transition: all 160ms; white-space: nowrap;
}
.action-btn-primary { background: #0A4D8C; color: #fff; }
.action-btn-primary:hover { background: #1565C0; color: #fff; box-shadow: 0 4px 12px rgba(10,77,140,.30); }
.action-btn-outline {
    background: var(--theme-panel-bg); color: var(--theme-text);
    border: 1px solid var(--theme-border);
}
.action-btn-outline:hover { background: #EFF6FF; color: #0A4D8C; border-color: #BFDBFE; }
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon {
    width: 72px; height: 72px; border-radius: 50%;
    background: #F0FDF4; display: flex; align-items: center;
    justify-content: center; margin: 0 auto 16px; font-size: 28px; color: #10B981;
}

/* ── Modal backdrop ────────────────────────────────────── */
.modal-backdrop-sirh {
    position: fixed; inset: 0; z-index: 1055;
    background: rgba(0,0,0,.55); backdrop-filter: blur(2px);
    display: flex; align-items: flex-start; justify-content: center;
    padding: 24px 16px; overflow-y: auto;
}

/* ── Modal container ───────────────────────────────────── */
.modal-sirh {
    background: #fff;
    border-radius: 16px;
    width: 100%; max-width: 860px;
    box-shadow: 0 25px 60px rgba(0,0,0,.25);
    position: relative;
    margin: auto;
    overflow: hidden;
}

/* ── Modal header ──────────────────────────────────────── */
.modal-header-sirh {
    padding: 20px 24px 0;
    border-bottom: 1px solid #E5E7EB;
    background: #fff;
    padding-bottom: 0;
}
.modal-close-btn {
    position: absolute; top: 16px; right: 16px;
    width: 32px; height: 32px; border-radius: 8px;
    border: 1px solid #E5E7EB; background: #fff;
    color: #6B7280;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 14px; transition: all 150ms; z-index: 10;
}
.modal-close-btn:hover { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }

/* ── Compte card dans le modal ─────────────────────────── */
.modal-compte-card {
    margin: 16px 24px;
    background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
    border: 2px solid #93C5FD; border-radius: 12px; padding: 14px 18px;
    display: flex; align-items: center; gap: 14px;
}
[data-theme="dark"] .modal-compte-card {
    background: linear-gradient(135deg, rgba(29,78,216,0.15), rgba(37,99,235,0.1));
    border-color: rgba(147,197,253,0.3);
}
.modal-compte-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #1D4ED8, #2563EB);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 4px 12px rgba(29,78,216,.3);
}
.modal-matricule-badge {
    display: flex; align-items: center; gap: 8px;
    margin: 0 24px 16px;
    padding: 10px 14px;
    background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px;
}
[data-theme="dark"] .modal-matricule-badge {
    background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3);
}

/* ── Tabs dans le modal ────────────────────────────────── */
.modal-tabs {
    display: flex; border-bottom: 2px solid #E5E7EB;
    padding: 0 24px; gap: 2px; background: #fff;
}
.modal-tab-btn {
    padding: 12px 16px; border: none; background: none; cursor: pointer;
    font-size: 13px; font-weight: 500; color: #6B7280;
    border-bottom: 2px solid transparent; margin-bottom: -2px;
    display: flex; align-items: center; gap: 7px; transition: all .15s;
    white-space: nowrap;
}
.modal-tab-btn:hover { color: #374151; background: #F9FAFB; }
.modal-tab-btn.active { color: #0A4D8C; border-bottom-color: #0A4D8C; font-weight: 600; }
.tab-lock-badge {
    width: 16px; height: 16px; border-radius: 8px;
    background: #FEE2E2; color: #DC2626; font-size: 8px;
    display: flex; align-items: center; justify-content: center;
}

/* ── Corps du formulaire (zone scrollable, identique à create.blade.php) ── */
.modal-body {
    background: #F3F4F6;
    padding: 14px 18px;
    height: 380px;
    overflow-y: scroll;
}
.modal-body::-webkit-scrollbar { width: 10px; }
.modal-body::-webkit-scrollbar-track { background: #E5E7EB; border-radius: 5px; }
.modal-body::-webkit-scrollbar-thumb { background: #0A4D8C; border-radius: 5px; }
.modal-body::-webkit-scrollbar-thumb:hover { background: #1565C0; }

.form-card {
    background: #fff;
    border-radius: 10px; border: 1px solid #E5E7EB;
    padding: 16px; margin-bottom: 12px;
}
.form-card:last-child { margin-bottom: 0; }
.form-card-title {
    font-size: 10.5px; font-weight: 700; color: #6B7280;
    text-transform: uppercase; letter-spacing: .05em;
    margin-bottom: 12px; padding-bottom: 8px;
    border-bottom: 1px solid #F3F4F6;
    display: flex; align-items: center; gap: 8px;
}
.form-label-sm {
    font-size: 11px; font-weight: 600; color: #374151;
    margin-bottom: 4px; display: block;
}
.required { color: #DC2626; }
.form-input {
    width: 100%; padding: 6px 10px;
    border: 1.5px solid #E5E7EB;
    border-radius: 6px; font-size: 12.5px;
    color: #111827; background: #fff;
    transition: border-color .15s, box-shadow .15s;
}
.form-input:focus {
    outline: none; border-color: #0A4D8C;
    box-shadow: 0 0 0 3px rgba(10,77,140,.1);
}
.form-hint { font-size: 10px; color: #9CA3AF; margin-top: 3px; }
.field-error { font-size: 11px; color: #DC2626; margin-top: 3px; }
.sensitive-notice {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; background: #FFF7ED;
    border: 1px solid #FED7AA; border-radius: 8px;
    margin-bottom: 14px; font-size: 12px; color: #92400E;
}
.famille-item {
    background: #F9FAFB; border: 1px solid #E5E7EB;
    border-radius: 10px; padding: 14px; margin-bottom: 10px; position: relative;
}
.btn-remove {
    position: absolute; top: 10px; right: 10px;
    width: 26px; height: 26px; border-radius: 6px;
    border: 1px solid #FECACA; background: #FEF2F2;
    color: #DC2626; display: flex; align-items: center;
    justify-content: center; cursor: pointer; font-size: 10px;
}
.btn-remove:hover { background: #DC2626; color: #fff; }
.btn-add-item {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border: 1.5px dashed #D1D5DB;
    border-radius: 8px; background: #fff;
    color: #6B7280; font-size: 12px;
    cursor: pointer; transition: all .15s;
}
.btn-add-item:hover { border-color: #0A4D8C; color: #0A4D8C; background: #EFF6FF; }
.empty-famille { font-size: 13px; color: #9CA3AF; text-align: center; padding: 20px 0; }

/* ── Footer du modal ───────────────────────────────────── */
.modal-footer-sirh {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 24px; border-top: 1px solid #E5E7EB;
    background: #fff; gap: 12px;
}
.btn-modal-cancel {
    padding: 10px 20px; border-radius: 9px; font-size: 13px; font-weight: 600;
    background: #fff; border: 1.5px solid #E5E7EB;
    color: #374151; cursor: pointer; display: inline-flex;
    align-items: center; gap: 8px; transition: all .15s;
}
.btn-modal-cancel:hover { background: #F9FAFB; color: #111827; }
.btn-modal-save {
    padding: 10px 22px; border-radius: 9px; font-size: 13px; font-weight: 600;
    background: linear-gradient(135deg, #0A4D8C, #1565C0);
    border: none; color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 12px rgba(10,77,140,.3); transition: all .15s;
}
.btn-modal-save:hover {
    background: linear-gradient(135deg, #1565C0, #1976D2);
    transform: translateY(-1px); box-shadow: 0 6px 16px rgba(10,77,140,.35);
}
</style>
@endpush

@section('content')

@php
    $total = $comptes->count();

    /* ── Déterminer si on doit ré-ouvrir le modal ──────────
       Cas 1 : retour après erreur de validation (old('user_id'))
       Cas 2 : redirect depuis completerDossierForm (session flash)
    ──────────────────────────────────────────────────────── */
    $reopenUserId = old('user_id') ?? session('open_modal_user_id');
    $reopenUser   = $reopenUserId ? $comptes->firstWhere('id', (int) $reopenUserId) : null;

    $reopenJs = 'false'; // pas de ré-ouverture par défaut
    if ($reopenUser) {
        $reopenJs = json_encode([
            'id'     => $reopenUser->id,
            'login'  => $reopenUser->login,
            'email'  => $reopenUser->email ?? '',
            'role'   => $reopenUser->getRoleNames()->first() ?? '',
            'statut' => strtolower($reopenUser->statut_compte ?? 'actif'),
        ]);
    }
@endphp

{{-- ══════════════════════════════════════════════════════════
     COMPOSANT PRINCIPAL avec état du modal (Alpine.js)
══════════════════════════════════════════════════════════ --}}
<div x-data="{
    modalOpen: false,
    currentTab: 'identite',
    conjoints: [],
    enfants: [],
    compte: { id: null, login: '', email: '', role: '', statut: '' },

    openModal(id, login, email, role, statut) {
        this.compte      = { id, login, email, role, statut };
        this.currentTab  = 'identite';
        this.conjoints   = [];
        this.enfants     = [];
        this.modalOpen   = true;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.modalOpen = false;
        document.body.style.overflow = '';
    }
}"
x-init="
    @if($reopenUser)
    (function() {
        var d = {!! $reopenJs !!};
        $nextTick(() => openModal(d.id, d.login, d.email, d.role, d.statut));
    })();
    @endif
"
@keydown.escape.window="closeModal()">

{{-- ══════════════════════════════════════════════════════════
     EN-TÊTE
══════════════════════════════════════════════════════════ --}}
<div class="panel p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#FEF3C7,#FDE68A);display:flex;align-items:center;justify-content:center;font-size:20px;">
                ⏳
            </div>
            <div>
                <div style="font-size:16px;font-weight:700;">Comptes en attente de dossier RH</div>
                <div style="font-size:13px;color:var(--theme-text-muted);">
                    Ces comptes ont été créés par l'Admin mais le dossier RH n'est pas encore complété.
                    <span style="color:#D97706;font-weight:600;">Les comptes AdminSystème sont exclus.</span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if($total > 0)
            <div style="background:#FEF3C7;color:#92400E;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $total }} compte(s) à compléter
            </div>
            @endif
            <a href="{{ route('rh.agents.index') }}" class="action-btn action-btn-outline">
                <i class="fas fa-arrow-left"></i>Retour au personnel
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     LISTE DES COMPTES
══════════════════════════════════════════════════════════ --}}
<div class="panel p-4">

    @if($total > 0)

    {{-- Filtre rapide --}}
    <div class="mb-4">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9CA3AF;font-size:13px;"></i>
            <input type="text" id="searchComptes"
                   placeholder="Filtrer par identifiant, rôle..."
                   class="form-control"
                   style="padding-left:36px;border-radius:8px;font-size:13px;"
                   oninput="filterComptes(this.value)">
        </div>
    </div>

    <div id="comptesList">
        @foreach($comptes as $compte)
        @php
            $role   = $compte->getRoleNames()->first() ?? '';
            $statut = strtolower($compte->statut_compte ?? 'actif');
        @endphp
        <div class="compte-row"
             data-search="{{ strtolower($compte->login . ' ' . $role) }}">

            {{-- Avatar --}}
            <div class="compte-avatar">{{ strtoupper(substr($compte->login, 0, 1)) }}</div>

            {{-- Infos --}}
            <div class="flex-grow-1">
                <div class="compte-login">{{ $compte->login }}</div>
                <div class="compte-meta d-flex align-items-center gap-2 flex-wrap mt-1">
                    @if($compte->email)
                        <span><i class="fas fa-envelope me-1"></i>{{ $compte->email }}</span>
                    @endif
                    @if($role)
                        <span class="badge-pending">
                            <i class="fas fa-shield-alt" style="font-size:9px;"></i>{{ $role }}
                        </span>
                    @endif
                    <span><i class="fas fa-calendar me-1"></i>Créé {{ $compte->created_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Statut --}}
            <div class="d-none d-md-block">
                @if($statut === 'actif')
                    <span style="background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                        <i class="fas fa-circle me-1" style="font-size:7px;"></i>Actif
                    </span>
                @elseif($statut === 'suspendu')
                    <span style="background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                        <i class="fas fa-ban me-1"></i>Suspendu
                    </span>
                @else
                    <span style="background:#F3F4F6;color:#374151;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                        {{ ucfirst($compte->statut_compte) }}
                    </span>
                @endif
            </div>

            {{-- Bouton ouvrir le modal --}}
            <div class="flex-shrink-0">
                <button type="button"
                        class="action-btn action-btn-primary"
                        @click="openModal(
                            {{ $compte->id }},
                            '{{ addslashes($compte->login) }}',
                            '{{ addslashes($compte->email ?? '') }}',
                            '{{ addslashes($role) }}',
                            '{{ $statut }}'
                        )">
                    <i class="fas fa-folder-plus"></i>
                    <span class="d-none d-sm-inline">Compléter le dossier</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div id="noResult" style="display:none;text-align:center;padding:30px;color:var(--theme-text-muted);font-size:13px;">
        <i class="fas fa-search me-2"></i>Aucun compte ne correspond à votre recherche.
    </div>

    @else

    {{-- État vide --}}
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-check-circle"></i></div>
        <div style="font-size:16px;font-weight:600;color:var(--theme-text);margin-bottom:8px;">Tous les dossiers sont à jour !</div>
        <div style="font-size:13px;color:var(--theme-text-muted);margin-bottom:24px;">
            Aucun compte ne nécessite de complétion de dossier RH pour le moment.
        </div>
        <a href="{{ route('rh.agents.index') }}" class="action-btn action-btn-primary" style="display:inline-flex;">
            <i class="fas fa-users"></i>Voir tout le personnel
        </a>
    </div>

    @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL — COMPLÉTER LE DOSSIER RH
══════════════════════════════════════════════════════════ --}}
<div x-show="modalOpen"
     x-cloak
     class="modal-backdrop-sirh"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="closeModal()">

    <div class="modal-sirh"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 transform scale-95 -translate-y-4">

        {{-- Bouton fermer --}}
        <button type="button" class="modal-close-btn" @click="closeModal()">
            <i class="fas fa-times"></i>
        </button>

        {{-- ── En-tête du modal ──────────────────────────── --}}
        <div class="modal-header-sirh">
            <div class="d-flex align-items-center gap-3 pb-3">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-folder-plus" style="color:#fff;font-size:16px;"></i>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;">Compléter le dossier RH</div>
                    <div style="font-size:12px;color:var(--theme-text-muted);">
                        Rattachement au compte :
                        <code x-text="compte.login" style="color:#0A4D8C;font-size:12px;background:rgba(10,77,140,0.08);padding:1px 6px;border-radius:4px;"></code>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Carte compte ──────────────────────────────── --}}
        <div class="modal-compte-card">
            <div class="modal-compte-icon">
                <i class="fas fa-user-shield" style="color:#fff;font-size:18px;"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-size:11px;font-weight:700;color:#1D4ED8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">
                    Compte utilisateur existant
                </div>
                <div style="font-size:17px;font-weight:800;color:#1E3A8A;" x-text="compte.login"></div>
                <div style="font-size:12px;color:#3B82F6;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:4px;">
                    <span x-show="compte.email" x-text="compte.email"></span>
                    <span x-show="compte.role"
                          x-text="compte.role"
                          style="background:#1D4ED8;color:#fff;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;"></span>
                </div>
            </div>
            <div style="text-align:center;font-size:11px;font-weight:600;color:#1D4ED8;line-height:1.4;">
                <i class="fas fa-lock" style="font-size:16px;display:block;margin-bottom:4px;"></i>
                Compte verrouillé<br>
                <span style="font-weight:400;color:#3B82F6;">Login/mdp non modifiables</span>
            </div>
        </div>

        {{-- ── Matricule ─────────────────────────────────── --}}
        <div class="modal-matricule-badge">
            <i class="fas fa-id-badge" style="color:#059669;font-size:18px;"></i>
            <div>
                <div style="font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.05em;">
                    Matricule attribué automatiquement
                </div>
                <div style="font-size:16px;font-weight:800;color:#065F46;font-family:monospace;">
                    {{ $prochainMatricule }}
                </div>
            </div>
        </div>

        {{-- ── Formulaire ────────────────────────────────── --}}
        <form method="POST" action="{{ route('rh.agents.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" :value="compte.id">

            {{-- Tabs --}}
            <div class="modal-tabs">
                <button type="button" class="modal-tab-btn"
                        :class="{ active: currentTab === 'identite' }"
                        @click="currentTab = 'identite'">
                    <i class="fas fa-id-card"></i> Identité
                </button>
                <button type="button" class="modal-tab-btn"
                        :class="{ active: currentTab === 'coordonnees' }"
                        @click="currentTab = 'coordonnees'">
                    <i class="fas fa-lock"></i> Coordonnées
                    <span class="tab-lock-badge"><i class="fas fa-shield-halved"></i></span>
                </button>
                <button type="button" class="modal-tab-btn"
                        :class="{ active: currentTab === 'pro' }"
                        @click="currentTab = 'pro'">
                    <i class="fas fa-briefcase"></i> Professionnel
                </button>
                <button type="button" class="modal-tab-btn"
                        :class="{ active: currentTab === 'famille' }"
                        @click="currentTab = 'famille'">
                    <i class="fas fa-users"></i> Famille
                </button>
            </div>

            <div class="modal-body">

                {{-- Erreurs de validation globales ──────────── --}}
                @if($errors->any())
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:10px 14px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:700;color:#DC2626;margin-bottom:6px;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ $errors->count() }} erreur(s) à corriger :
                    </div>
                    <ul style="margin:0;padding-left:16px;font-size:12px;color:#991B1B;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- ══ TAB IDENTITÉ ══════════════════════════ --}}
                <div x-show="currentTab === 'identite'" x-cloak>
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="fas fa-user" style="color:#0A4D8C;"></i> Informations personnelles
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-sm">Nom de famille <span class="required">*</span></label>
                                <input type="text" name="nom"
                                       class="form-input @error('nom') is-invalid @enderror"
                                       value="{{ old('nom') }}"
                                       placeholder="DIALLO"
                                       style="text-transform:uppercase;" required>
                                @error('nom')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Prénom <span class="required">*</span></label>
                                <input type="text" name="prenom"
                                       class="form-input @error('prenom') is-invalid @enderror"
                                       value="{{ old('prenom') }}"
                                       placeholder="Amadou" required>
                                @error('prenom')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Date de naissance <span class="required">*</span></label>
                                <input type="date" name="date_naissance"
                                       class="form-input @error('date_naissance') is-invalid @enderror"
                                       value="{{ old('date_naissance') }}" required>
                                @error('date_naissance')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Lieu de naissance <span class="required">*</span></label>
                                <input type="text" name="lieu_naissance"
                                       class="form-input @error('lieu_naissance') is-invalid @enderror"
                                       value="{{ old('lieu_naissance') }}"
                                       placeholder="Dakar" required>
                                @error('lieu_naissance')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Nationalité</label>
                                <input type="text" name="nationalite" class="form-input"
                                       value="{{ old('nationalite', 'Sénégalaise') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Sexe <span class="required">*</span></label>
                                <select name="sexe" class="form-input @error('sexe') is-invalid @enderror" required>
                                    <option value="">— Choisir —</option>
                                    <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                                @error('sexe')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Situation familiale</label>
                                <select name="situation_familiale" class="form-input">
                                    <option value="">— Choisir —</option>
                                    <option value="Célibataire" {{ old('situation_familiale') === 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                    <option value="Marié"       {{ old('situation_familiale') === 'Marié'       ? 'selected' : '' }}>Marié(e)</option>
                                    <option value="Divorcé"     {{ old('situation_familiale') === 'Divorcé'     ? 'selected' : '' }}>Divorcé(e)</option>
                                    <option value="Veuf"        {{ old('situation_familiale') === 'Veuf'        ? 'selected' : '' }}>Veuf/Veuve</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Statut</label>
                                <select name="statut" class="form-input">
                                    <option value="actif" selected>Actif</option>
                                    <option value="en_conge">En congé</option>
                                    <option value="suspendu">Suspendu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Photo</label>
                                <input type="file" name="photo" class="form-input" accept="image/jpeg,image/png">
                                <div class="form-hint">JPEG ou PNG · max 2 Mo</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ TAB COORDONNÉES ═══════════════════════ --}}
                <div x-show="currentTab === 'coordonnees'" x-cloak>
                    <div class="sensitive-notice">
                        <i class="fas fa-shield-halved fa-lg"></i>
                        <span>Ces données sont stockées <strong>chiffrées AES-256</strong> — accessibles uniquement aux personnes habilitées (Pilier Confidentialité – Triade CID).</span>
                    </div>
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="fas fa-phone" style="color:#D97706;"></i> Contact & Adresse
                            <span style="margin-left:auto;font-size:10px;font-weight:600;color:#D97706;background:#FFF7ED;padding:2px 8px;border-radius:10px;border:1px solid #FED7AA;">
                                <i class="fas fa-lock"></i> Données sensibles chiffrées
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-sm">
                                    Téléphone <i class="fas fa-lock" style="font-size:9px;color:#D97706;"></i>
                                </label>
                                <input type="tel" name="telephone" class="form-input"
                                       value="{{ old('telephone') }}" placeholder="+221 77 000 00 00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Email professionnel</label>
                                <input type="email" name="email" class="form-input"
                                       value="{{ old('email') }}" placeholder="a.diallo@chnp.sn">
                            </div>
                            <div class="col-12">
                                <label class="form-label-sm">
                                    Adresse <i class="fas fa-lock" style="font-size:9px;color:#D97706;"></i>
                                </label>
                                <textarea name="adresse" class="form-input" rows="2"
                                          placeholder="Quartier, Commune, Ville…">{{ old('adresse') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">
                                    N° Assurance maladie <i class="fas fa-lock" style="font-size:9px;color:#D97706;"></i>
                                </label>
                                <input type="text" name="numero_assurance" class="form-input"
                                       value="{{ old('numero_assurance') }}" placeholder="IPRES-XXXXXXXXX">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ TAB PROFESSIONNEL ═════════════════════ --}}
                <div x-show="currentTab === 'pro'" x-cloak>
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="fas fa-briefcase" style="color:#0A4D8C;"></i> Informations professionnelles
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-sm">Date de recrutement <span class="required">*</span></label>
                                <input type="date" name="date_recrutement"
                                       class="form-input @error('date_recrutement') is-invalid @enderror"
                                       value="{{ old('date_recrutement', now()->format('Y-m-d')) }}" required>
                                @error('date_recrutement')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Fonction</label>
                                <input type="text" name="fonction" class="form-input"
                                       value="{{ old('fonction') }}" placeholder="Infirmier chef">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Grade</label>
                                <input type="text" name="grade" class="form-input"
                                       value="{{ old('grade') }}" placeholder="IES2">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Catégorie socio-professionnelle</label>
                                <select name="categorie_cp" class="form-input">
                                    <option value="">— Choisir —</option>
                                    <option value="Cadre_Superieur"      {{ old('categorie_cp') === 'Cadre_Superieur'      ? 'selected' : '' }}>Cadre Supérieur</option>
                                    <option value="Cadre_Moyen"          {{ old('categorie_cp') === 'Cadre_Moyen'          ? 'selected' : '' }}>Cadre Moyen</option>
                                    <option value="Technicien_Superieur" {{ old('categorie_cp') === 'Technicien_Superieur' ? 'selected' : '' }}>Technicien Supérieur</option>
                                    <option value="Technicien"           {{ old('categorie_cp') === 'Technicien'           ? 'selected' : '' }}>Technicien</option>
                                    <option value="Agent_Administratif"  {{ old('categorie_cp') === 'Agent_Administratif'  ? 'selected' : '' }}>Agent Administratif</option>
                                    <option value="Agent_de_Service"     {{ old('categorie_cp') === 'Agent_de_Service'     ? 'selected' : '' }}>Agent de Service</option>
                                    <option value="Commis_Administration" {{ old('categorie_cp') === 'Commis_Administration' ? 'selected' : '' }}>Commis Administration</option>
                                    <option value="Ouvrier"              {{ old('categorie_cp') === 'Ouvrier'              ? 'selected' : '' }}>Ouvrier</option>
                                    <option value="Sans_Diplome"         {{ old('categorie_cp') === 'Sans_Diplome'         ? 'selected' : '' }}>Sans Diplôme</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-sm">Service</label>
                                <select name="id_service" class="form-input">
                                    <option value="">— Aucun —</option>
                                    @foreach($services as $s)
                                    <option value="{{ $s->id_service }}" {{ old('id_service') == $s->id_service ? 'selected' : '' }}>
                                        {{ $s->nom_service }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-sm">Division</label>
                                <select name="id_division" class="form-input">
                                    <option value="">— Aucune —</option>
                                    @foreach($divisions as $d)
                                    <option value="{{ $d->id_division }}" {{ old('id_division') == $d->id_division ? 'selected' : '' }}>
                                        {{ $d->nom_division }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ TAB FAMILLE ══════════════════════════ --}}
                <div x-show="currentTab === 'famille'" x-cloak>

                    {{-- Conjoint(e) --}}
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="fas fa-heart" style="color:#EC4899;"></i> Conjoint(e)
                            <button type="button" class="btn-add-item ms-auto"
                                    @click="if(conjoints.length < 1) conjoints.push({nom:'',prenom:'',date:'',lien:'Épouse'})">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <template x-for="(c, i) in conjoints" :key="i">
                            <div class="famille-item">
                                <button type="button" class="btn-remove" @click="conjoints.splice(i,1)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label-sm">Nom</label>
                                        <input type="text" :name="`conjoints[${i}][nom_conj]`" class="form-input" x-model="c.nom">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm">Prénom</label>
                                        <input type="text" :name="`conjoints[${i}][prenom_conj]`" class="form-input" x-model="c.prenom">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm">Date de naissance</label>
                                        <input type="date" :name="`conjoints[${i}][date_naissance_conj]`" class="form-input" x-model="c.date">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm">Lien</label>
                                        <select :name="`conjoints[${i}][type_lien]`" class="form-input" x-model="c.lien">
                                            <option value="Époux">Époux</option>
                                            <option value="Épouse">Épouse</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="conjoints.length === 0" class="empty-famille">
                            <i class="fas fa-heart-crack" style="font-size:22px;display:block;margin-bottom:6px;color:#E5E7EB;"></i>
                            Aucun conjoint — cliquez sur Ajouter
                        </div>
                    </div>

                    {{-- Enfants --}}
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="fas fa-child" style="color:#059669;"></i> Enfants
                            <button type="button" class="btn-add-item ms-auto"
                                    @click="enfants.push({prenom:'',date:'',lien:'Fils'})">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        <template x-for="(e, i) in enfants" :key="i">
                            <div class="famille-item">
                                <button type="button" class="btn-remove" @click="enfants.splice(i,1)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label-sm">Prénom complet</label>
                                        <input type="text" :name="`enfants[${i}][prenom_complet]`" class="form-input" x-model="e.prenom">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Date de naissance</label>
                                        <input type="date" :name="`enfants[${i}][date_naissance_enfant]`" class="form-input" x-model="e.date">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-sm">Lien</label>
                                        <select :name="`enfants[${i}][lien_filiation]`" class="form-input" x-model="e.lien">
                                            <option value="Fils">Fils</option>
                                            <option value="Fille">Fille</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="enfants.length === 0" class="empty-famille">
                            <i class="fas fa-baby" style="font-size:22px;display:block;margin-bottom:6px;color:#E5E7EB;"></i>
                            Aucun enfant — cliquez sur Ajouter
                        </div>
                    </div>

                </div>
                {{-- FIN TABS --}}

            </div>
            {{-- FIN modal-body --}}

            {{-- ── Footer ─────────────────────────────────── --}}
            <div class="modal-footer-sirh">
                <button type="button" class="btn-modal-cancel" @click="closeModal()">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <div style="font-size:12px;color:var(--theme-text-muted);text-align:center;flex:1;">
                    <i class="fas fa-link me-1" style="color:#0A4D8C;"></i>
                    Dossier rattaché au compte
                    <strong x-text="compte.login" style="color:#0A4D8C;"></strong>
                </div>
                <button type="submit" class="btn-modal-save">
                    <i class="fas fa-check-circle"></i> Valider le dossier
                </button>
            </div>

        </form>
    </div>
    {{-- FIN modal-sirh --}}
</div>
{{-- FIN backdrop --}}

</div>
{{-- FIN x-data --}}

@endsection

@push('scripts')
<script>
function filterComptes(val) {
    const term  = val.toLowerCase().trim();
    const rows  = document.querySelectorAll('#comptesList .compte-row');
    let visible = 0;
    rows.forEach(row => {
        const match = !term || row.dataset.search.includes(term);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const noResult = document.getElementById('noResult');
    if (noResult) noResult.style.display = (visible === 0 && term) ? '' : 'none';
}

/* Re-ouverture du modal gérée via x-init Alpine.js */
</script>
@endpush
