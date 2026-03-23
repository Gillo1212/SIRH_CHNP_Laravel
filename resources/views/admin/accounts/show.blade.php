@extends('layouts.master')

@section('title', 'Compte — ' . $user->login)
@section('page-title', 'Détail du Compte')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.accounts.index') }}" style="color:#1565C0;">Comptes</a></li>
    <li>{{ $user->login }}</li>
@endsection

@push('styles')
<style>
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }
.info-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #F3F4F6; }
.info-row:last-child { border-bottom:none; }
.info-label { font-size:12px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.05em; }
.info-value { font-size:14px; font-weight:500; color:#111827; }
.badge-role { font-size:12px; font-weight:600; padding:3px 12px; border-radius:20px; background:#EFF6FF; color:#1E40AF; }
.audit-row { padding:10px 0; border-bottom:1px solid #F3F4F6; }
.audit-row:last-child { border-bottom:none; }
.modal-sirh .modal-content { border:none; border-radius:14px; box-shadow:0 20px 50px rgba(0,0,0,.18); }
.role-card { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; cursor:pointer; border:2px solid #E5E7EB; background:#FAFAFA; transition:all 150ms; user-select:none; }
.role-card.selected { border-color:#93C5FD; background:#EFF6FF; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div style="width:56px;height:56px;border-radius:14px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-user-circle" style="font-size:28px;color:#1D4ED8;"></i>
            </div>
            <div>
                <h4 class="fw-700 mb-0" style="color:#111827;">{{ $user->login }}</h4>
                <div style="font-size:13px;color:#6B7280;">
                    {{ $user->email ?? 'Pas d\'email renseigné' }}
                    &nbsp;·&nbsp; Créé le {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary fw-600"
                    onclick="ouvrirModalEdit()">
                <i class="fas fa-edit me-2"></i>Modifier
            </button>
            <button type="button" class="btn btn-outline-secondary fw-600" onclick="confirmResetPassword()">
                <i class="fas fa-key me-2"></i>Réinitialiser MDP
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Colonne gauche : infos compte --}}
        <div class="col-lg-5">

            {{-- Informations compte --}}
            <div class="panel mb-4">
                <div class="fw-700 mb-3" style="color:#111827;font-size:15px;">
                    <i class="fas fa-id-card me-2" style="color:#0A4D8C;"></i>Informations du compte
                </div>

                <div class="info-row">
                    <span class="info-label">Login</span>
                    <span class="info-value fw-700">{{ $user->login }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $user->email ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rôle(s)</span>
                    <div>
                        @foreach($user->roles as $role)
                            <span class="badge-role me-1">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut compte</span>
                    <span>
                        @if($user->verouille)
                            <span class="badge bg-danger-subtle text-danger fw-600">Verrouillé</span>
                        @elseif($user->statut_compte === 'Actif')
                            <span class="badge bg-success-subtle text-success fw-600">Actif</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary fw-600">{{ $user->statut_compte }}</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tentatives échouées</span>
                    <span class="info-value">
                        {{ $user->tentatives_connexion }}
                        @if($user->tentatives_connexion > 0)
                            <span class="text-danger ms-1" style="font-size:12px;">(max 5)</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dernière connexion</span>
                    <span class="info-value" style="font-size:13px;">
                        {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i') : 'Jamais connecté' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Créé le</span>
                    <span class="info-value" style="font-size:13px;">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            {{-- Statut dossier agent --}}
            <div class="panel">
                <div class="fw-700 mb-3" style="color:#111827;font-size:15px;">
                    <i class="fas fa-folder-open me-2" style="color:#0A4D8C;"></i>Dossier Agent RH
                </div>

                @if($user->agent_completed && $user->agent)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3"
                         style="background:#ECFDF5;border:1px solid #A7F3D0;">
                        <i class="fas fa-check-circle fa-2x" style="color:#059669;"></i>
                        <div>
                            <div class="fw-600" style="color:#065F46;">Dossier complété</div>
                            <div style="font-size:12px;color:#6B7280;">
                                {{ $user->agent->prenom }} {{ $user->agent->nom }} —
                                {{ $user->agent->matricule }}
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('rh.agents.show', $user->agent->id_agent) }}"
                       class="btn btn-success fw-600 w-100">
                        <i class="fas fa-id-badge me-2"></i>Voir le dossier agent
                    </a>
                @else
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3"
                         style="background:#FFFBEB;border:1px solid #FCD34D;">
                        <i class="fas fa-clock fa-2x" style="color:#D97706;"></i>
                        <div>
                            <div class="fw-600" style="color:#92400E;">En attente de la RH</div>
                            <div style="font-size:12px;color:#6B7280;">Le dossier agent n'a pas encore été complété.</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.accounts.resend-rh', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning fw-600 w-100">
                            <i class="fas fa-bell me-2"></i>Renvoyer notification à la RH
                        </button>
                    </form>
                @endif
            </div>

            {{-- Actions dangereuses --}}
            <div class="panel mt-4" style="border:1px solid #FEE2E2;">
                <div class="fw-700 mb-3" style="color:#DC2626;font-size:14px;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Actions administratives
                </div>
                <div class="d-grid gap-2">
                    <button onclick="confirmResetPassword()" class="btn btn-outline-secondary fw-600 text-start">
                        <i class="fas fa-key me-2 text-secondary"></i>Réinitialiser le mot de passe
                    </button>
                    <form action="{{ route('admin.accounts.toggle-verrouillage', $user->id) }}" method="POST" id="form-toggle">
                        @csrf
                        <button type="button" onclick="confirmToggle()"
                                class="btn fw-600 text-start w-100 {{ $user->verouille ? 'btn-outline-success' : 'btn-outline-danger' }}">
                            @if($user->verouille)
                                <i class="fas fa-unlock me-2"></i>Déverrouiller le compte
                            @else
                                <i class="fas fa-lock me-2"></i>Verrouiller le compte
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Colonne droite : audit --}}
        <div class="col-lg-7">
            <div class="panel">
                <div class="fw-700 mb-3" style="color:#111827;font-size:15px;">
                    <i class="fas fa-history me-2" style="color:#0A4D8C;"></i>Journal d'audit — 20 dernières actions
                </div>

                @forelse($auditLogs as $log)
                    <div class="audit-row">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div style="flex:1;">
                                <div class="fw-600" style="font-size:13px;color:#111827;">{{ $log->description }}</div>
                                @if($log->causer)
                                    <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">
                                        Par : {{ $log->causer->login ?? 'système' }}
                                    </div>
                                @endif
                                @if($log->properties && count($log->properties) > 0)
                                    <div style="font-size:11px;color:#6B7280;margin-top:3px;font-family:monospace;">
                                        {{ json_encode($log->properties->except(['old', 'attributes'])->toArray()) }}
                                    </div>
                                @endif
                            </div>
                            <div style="font-size:11px;color:#9CA3AF;white-space:nowrap;flex-shrink:0;">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color:#9CA3AF;">
                        <i class="fas fa-clipboard fa-2x mb-2 d-block"></i>
                        <span style="font-size:13px;">Aucune action enregistrée</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Forms cachés --}}
    <form id="form-reset-pwd" action="{{ route('admin.accounts.reset-password', $user->id) }}" method="POST" class="d-none">@csrf</form>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL — MODIFIER CE COMPTE
     ═══════════════════════════════════════════════════════════ --}}
<div class="modal fade modal-sirh" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" style="color:#111827;">
                    <i class="fas fa-edit me-2" style="color:#0A4D8C;"></i>Modifier — {{ $user->login }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounts.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Login <span class="text-danger">*</span></label>
                            <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                                   value="{{ old('login', $user->login) }}" required>
                            @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:13px;">Rôle(s) <span class="text-danger">*</span></label>
                            @error('roles')<div class="text-danger mb-2" style="font-size:13px;">{{ $message }}</div>@enderror
                            <div class="row g-2">
                                @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                                @php
                                    $checked = $user->hasRole($role->name);
                                    $ic = ['AdminSystème'=>'fa-shield-alt','DRH'=>'fa-user-tie','AgentRH'=>'fa-user-cog','Manager'=>'fa-user-check','Agent'=>'fa-user'];
                                @endphp
                                <div class="col-6 col-md-4">
                                    <label class="role-card {{ $checked ? 'selected' : '' }}" onclick="toggleRole(this)">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="d-none" {{ $checked ? 'checked' : '' }}>
                                        <i class="fas {{ $ic[$role->name] ?? 'fa-user' }}" style="color:#0A4D8C;"></i>
                                        <span class="fw-600" style="font-size:13px;">{{ $role->name }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-600">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function ouvrirModalEdit() {
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function toggleRole(label) {
    const cb = label.querySelector('input[type=checkbox]');
    cb.checked = !cb.checked;
    label.classList.toggle('selected', cb.checked);
}

function confirmResetPassword() {
    Swal.fire({
        title: 'Réinitialiser le mot de passe ?',
        html: `Le nouveau mot de passe de <strong>{{ $user->login }}</strong> sera affiché dans une notification.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0A4D8C',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Réinitialiser',
    }).then(r => { if (r.isConfirmed) document.getElementById('form-reset-pwd').submit(); });
}

function confirmToggle() {
    const action = '{{ $user->verouille ? "déverrouiller" : "verrouiller" }}';
    const icon   = '{{ $user->verouille ? "success" : "warning" }}';
    Swal.fire({
        title: `Voulez-vous ${action} ce compte ?`,
        html: `Compte : <strong>{{ $user->login }}</strong>`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#0A4D8C',
        cancelButtonText: 'Annuler',
        confirmButtonText: action.charAt(0).toUpperCase() + action.slice(1),
    }).then(r => { if (r.isConfirmed) document.getElementById('form-toggle').submit(); });
}

{{-- Auto-ouvrir le modal si erreurs de validation --}}
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => ouvrirModalEdit());
@endif
</script>
@endpush
