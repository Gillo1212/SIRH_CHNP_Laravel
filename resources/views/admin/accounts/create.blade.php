@extends('layouts.master')

@section('title', 'Créer un Compte Utilisateur')
@section('page-title', 'Nouveau Compte')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.accounts.index') }}" style="color:#1565C0;">Comptes</a></li>
    <li>Nouveau compte</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">

            {{-- Info contextuelle --}}
            <div class="alert border-0 mb-4"
                 style="background:#EFF6FF;border-left:4px solid #3B82F6 !important;border-radius:8px;">
                <div class="d-flex gap-3">
                    <i class="fas fa-info-circle mt-1" style="color:#3B82F6;"></i>
                    <div>
                        <div class="fw-600 mb-1" style="color:#1E40AF;">Workflow Admin-first</div>
                        <div style="font-size:13px;color:#1E40AF;">
                            Vous créez ici les <strong>identifiants d'accès</strong> uniquement.
                            La RH recevra une notification pour compléter le dossier agent (données RH complètes).
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header border-0 bg-white" style="padding:24px 28px 0;border-radius:12px 12px 0 0;">
                    <h5 class="fw-700 mb-0" style="color:#111827;">
                        <i class="fas fa-user-plus me-2" style="color:#0A4D8C;"></i>Créer un compte utilisateur
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:13px;">Rôle système + Identifiants uniquement</p>
                </div>

                <div class="card-body" style="padding:24px 28px;">
                    <form action="{{ route('admin.accounts.store') }}" method="POST" id="formCreate">
                        @csrf

                        {{-- Erreurs globales --}}
                        @if($errors->any())
                            <div class="alert alert-danger border-0 mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $err)
                                        <li style="font-size:13px;">{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Login --}}
                        <div class="mb-4">
                            <label for="login" class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Login <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="login"
                                   name="login"
                                   class="form-control @error('login') is-invalid @enderror"
                                   value="{{ old('login') }}"
                                   placeholder="ex: jean.dupont ou jdupont"
                                   required>
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Lettres minuscules, chiffres, points, tirets, underscores. Min. 4 caractères.</div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="ex: jean.dupont@chnp.sn"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Utilisé pour les notifications système.</div>
                        </div>

                        {{-- Mot de passe --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min. 8 caractères"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleMdp('password')">
                                    <i class="fas fa-eye" id="icon-password"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="generatePassword()">
                                    <i class="fas fa-random me-1"></i>Générer
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Mot de passe généré --}}
                        <div id="generated-box" class="alert border-0 mb-3 d-none"
                             style="background:#F0FDF4;border-left:4px solid #10B981 !important;border-radius:8px;">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-key" style="color:#059669;"></i>
                                <div>
                                    <div style="font-size:11px;color:#6B7280;font-weight:600;">MOT DE PASSE GÉNÉRÉ</div>
                                    <code id="generated-display" style="font-size:15px;color:#065F46;font-weight:700;"></code>
                                </div>
                                <button type="button" class="btn btn-sm ms-auto"
                                        style="background:#D1FAE5;color:#065F46;border:none;"
                                        onclick="copyPassword()">
                                    <i class="fas fa-copy me-1"></i>Copier
                                </button>
                            </div>
                        </div>

                        {{-- Confirmation MDP --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Confirmer le mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control"
                                       placeholder="Répétez le mot de passe"
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleMdp('password_confirmation')">
                                    <i class="fas fa-eye" id="icon-password_confirmation"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Rôles --}}
                        <div class="mb-4">
                            <label class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Rôle(s) <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                @foreach($roles as $role)
                                    @php
                                        $checked = in_array($role->name, old('roles', []));
                                        $colors = [
                                            'AdminSystème' => ['bg' => '#FEF2F2', 'border' => '#FCA5A5', 'color' => '#7F1D1D', 'icon' => 'fa-shield-alt'],
                                            'DRH'          => ['bg' => '#F5F3FF', 'border' => '#A78BFA', 'color' => '#4C1D95', 'icon' => 'fa-user-tie'],
                                            'AgentRH'      => ['bg' => '#EFF6FF', 'border' => '#93C5FD', 'color' => '#1E3A8A', 'icon' => 'fa-user-cog'],
                                            'Manager'      => ['bg' => '#ECFDF5', 'border' => '#6EE7B7', 'color' => '#065F46', 'icon' => 'fa-user-check'],
                                            'Agent'        => ['bg' => '#FFFBEB', 'border' => '#FCD34D', 'color' => '#78350F', 'icon' => 'fa-user'],
                                        ];
                                        $c = $colors[$role->name] ?? ['bg' => '#F9FAFB', 'border' => '#D1D5DB', 'color' => '#374151', 'icon' => 'fa-user'];
                                    @endphp
                                    <div class="col-6 col-md-4">
                                        <label class="role-card {{ $checked ? 'selected' : '' }}"
                                               for="role_{{ $role->name }}"
                                               style="display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;cursor:pointer;border:2px solid {{ $checked ? $c['border'] : '#E5E7EB' }};background:{{ $checked ? $c['bg'] : '#FAFAFA' }};transition:all 150ms;">
                                            <input type="checkbox"
                                                   id="role_{{ $role->name }}"
                                                   name="roles[]"
                                                   value="{{ $role->name }}"
                                                   class="role-checkbox d-none"
                                                   {{ $checked ? 'checked' : '' }}>
                                            <i class="fas {{ $c['icon'] }}" style="color:{{ $c['color'] }};font-size:16px;flex-shrink:0;"></i>
                                            <span class="fw-600" style="font-size:13px;color:{{ $c['color'] }};">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notification RH --}}
                        <div class="mb-4 p-3 rounded-3" style="background:#F9FAFB;border:1px solid #E5E7EB;">
                            <div class="form-check mb-0">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="notify_rh"
                                       name="notify_rh"
                                       value="1"
                                       checked>
                                <label class="form-check-label fw-600" for="notify_rh" style="font-size:13px;color:#374151;">
                                    <i class="fas fa-bell me-1" style="color:#F59E0B;"></i>
                                    Notifier la RH immédiatement
                                </label>
                            </div>
                            <div class="mt-1 ms-4" style="font-size:12px;color:#6B7280;">
                                Tous les agents RH et DRH recevront un email avec un lien pour compléter le dossier agent.
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary fw-600 flex-fill">
                                <i class="fas fa-save me-2"></i>Créer le compte
                            </button>
                            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary fw-600">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Génération mot de passe sécurisé
function generatePassword() {
    const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const lower = 'abcdefghjkmnpqrstuvwxyz';
    const digits = '23456789';
    const special = '!@#$%&*';
    const all = upper + lower + digits + special;

    let pwd = upper[Math.floor(Math.random() * upper.length)]
            + lower[Math.floor(Math.random() * lower.length)]
            + digits[Math.floor(Math.random() * digits.length)]
            + special[Math.floor(Math.random() * special.length)];

    for (let i = 4; i < 12; i++) {
        pwd += all[Math.floor(Math.random() * all.length)];
    }

    // Mélanger
    pwd = pwd.split('').sort(() => Math.random() - 0.5).join('');

    document.getElementById('password').value = pwd;
    document.getElementById('password_confirmation').value = pwd;
    document.getElementById('generated-display').textContent = pwd;
    document.getElementById('generated-box').classList.remove('d-none');
}

function copyPassword() {
    const pwd = document.getElementById('generated-display').textContent;
    navigator.clipboard.writeText(pwd).then(() => {
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copié !';
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copier'; }, 2000);
    });
}

function toggleMdp(id) {
    const input = document.getElementById(id);
    const icon  = document.getElementById('icon-' + id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Role cards toggle
document.querySelectorAll('.role-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        const label = this.closest('label');
        if (this.checked) {
            label.style.borderColor = '#93C5FD';
            label.style.background  = '#EFF6FF';
        } else {
            label.style.borderColor = '#E5E7EB';
            label.style.background  = '#FAFAFA';
        }
    });
});

document.querySelectorAll('.role-card').forEach(label => {
    label.addEventListener('click', function () {
        const cb = this.querySelector('.role-checkbox');
        cb.checked = !cb.checked;
        cb.dispatchEvent(new Event('change'));
    });
});
</script>
@endpush
