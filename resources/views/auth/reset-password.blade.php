<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Réinitialisation du mot de passe — SIRH CHNP">

    <title>Nouveau mot de passe | SIRH CHNP</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0A4D8C;
            --primary-light: #1565C0;
            --primary-dark: #0D47A1;
            --primary-hover: #E3F2FD;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --gray-50: #F9FAFB;
            --gray-200: #E5E7EB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0; padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 16px; line-height: 1.6;
            color: var(--gray-900);
            background-color: var(--gray-50);
            -webkit-font-smoothing: antialiased;
        }

        /* ── Layout ── */
        .login-container { display: flex; min-height: 100vh; }

        .login-branding {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 50%, #052c52 100%);
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .login-branding { display: flex; } }

        .login-branding::before {
            content: '';
            position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }
        .login-branding::after {
            content: '';
            position: absolute; bottom: -30%; right: -20%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
            border-radius: 50%;
            animation: float-reverse 25s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translate(0,0) rotate(0deg); }
            50% { transform: translate(30px,30px) rotate(10deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-20px,-30px) scale(1.1); }
        }

        .login-branding-content {
            position: relative; z-index: 1;
            text-align: center; color: white; max-width: 480px;
            animation: branding-enter 600ms cubic-bezier(0.34,1.56,0.64,1) 100ms both;
        }
        @keyframes branding-enter {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .login-branding-logo { width: 120px; height: 120px; margin-bottom: 2rem; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3)); }
        .login-branding h1 { font-size: 2.5rem; font-weight: 700; margin: 0 0 1rem; line-height: 1.2; }
        .login-branding p  { font-size: 1.125rem; opacity: 0.9; margin: 0; line-height: 1.6; }

        .security-badges { display: flex; justify-content: center; gap: 1rem; margin-top: 2.5rem; flex-wrap: wrap; }
        .security-badge {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.15);
            border-radius: 9999px;
            font-size: 0.75rem; font-weight: 500;
        }
        .security-badge:nth-child(1) { animation: badge-enter 400ms ease 300ms both; }
        .security-badge:nth-child(2) { animation: badge-enter 400ms ease 450ms both; }
        .security-badge:nth-child(3) { animation: badge-enter 400ms ease 600ms both; }
        @keyframes badge-enter {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Partie formulaire ── */
        .login-form-section {
            flex: 1;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 2rem; background: white;
        }
        @media (min-width: 1024px) { .login-form-section { max-width: 50%; } }

        .login-form-container {
            width: 100%; max-width: 420px;
            animation: form-enter 400ms cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes form-enter {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Header ── */
        .login-header { text-align: center; margin-bottom: 2rem; }

        .login-logo-mobile { width: 64px; height: 64px; margin-bottom: 1.5rem; }
        @media (min-width: 1024px) { .login-logo-mobile { display: none; } }

        .login-header h2 { font-size: 1.75rem; font-weight: 700; color: var(--gray-900); margin: 0 0 0.5rem; }
        .login-header p  { font-size: 0.875rem; color: var(--gray-500); margin: 0; line-height: 1.5; }

        /* ── Icône décorative ── */
        .page-icon {
            width: 64px; height: 64px; border-radius: 16px;
            background: var(--primary-hover);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .page-icon i { font-size: 1.75rem; color: var(--primary); }

        /* ── Champs ── */
        .form-group { margin-bottom: 1.25rem; }

        .form-label {
            display: block; font-size: 0.875rem; font-weight: 500;
            color: var(--gray-700); margin-bottom: 0.5rem;
        }

        .form-input-wrapper { position: relative; }

        .form-input-icon {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500); font-size: 1rem; pointer-events: none;
        }
        .form-input-wrapper:focus-within .form-input-icon { color: var(--primary-light); transition: color 150ms; }

        .form-input {
            width: 100%; height: 48px;
            padding: 0 3rem 0 2.75rem;
            font-family: 'Inter', sans-serif; font-size: 1rem;
            color: var(--gray-900); background: white;
            border: 1px solid var(--gray-200); border-radius: 0.5rem;
            transition: all 150ms ease-in-out;
        }
        .form-input:focus {
            outline: none; border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(21,101,192,0.12), 0 1px 4px rgba(21,101,192,0.08);
        }
        .form-input.is-invalid { border-color: var(--danger); animation: input-shake 400ms ease; }
        .form-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        @keyframes input-shake {
            0%,100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* Input sans icône gauche (email caché) */
        .form-input.no-icon { padding-left: 1rem; }

        /* Toggle password */
        .password-toggle {
            position: absolute; right: 1rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: var(--gray-500); cursor: pointer; padding: 0; font-size: 1rem;
        }
        .password-toggle:hover { color: var(--gray-700); }

        .form-error {
            font-size: 0.75rem; color: var(--danger);
            margin-top: 0.375rem;
            display: flex; align-items: center; gap: 0.25rem;
        }

        /* ── Indicateur force mot de passe ── */
        .password-strength { margin-top: 0.5rem; }
        .strength-bar {
            height: 4px; border-radius: 2px; background: var(--gray-200);
            overflow: hidden; margin-bottom: 0.25rem;
        }
        .strength-fill {
            height: 100%; width: 0; border-radius: 2px;
            transition: width 300ms ease, background-color 300ms ease;
        }
        .strength-label { font-size: 0.75rem; color: var(--gray-500); }

        /* ── Bouton principal ── */
        .btn-login {
            width: 100%; height: 48px; margin-top: 0.5rem;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            font-family: 'Inter', sans-serif; font-size: 1rem; font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            border: none; border-radius: 0.5rem;
            cursor: pointer;
            transition: all 200ms ease-in-out;
            box-shadow: 0 4px 6px rgba(10,77,140,0.25);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(10,77,140,0.3); }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .btn-login .spinner {
            display: inline-block; width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3); border-top-color: white;
            border-radius: 50%; animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Lien retour ── */
        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            margin-top: 1.25rem;
            font-size: 0.875rem; color: var(--gray-500);
            text-decoration: none; transition: color 150ms;
        }
        .back-link:hover { color: var(--primary-light); }

        /* ── Alertes ── */
        .alert-error {
            display: flex; align-items: flex-start; gap: 0.75rem;
            padding: 1rem; margin-bottom: 1.5rem;
            background: #FEE2E2; border: 1px solid #FECACA;
            border-left: 4px solid var(--danger); border-radius: 0.5rem;
        }
        .alert-error > i { color: var(--danger); font-size: 1.25rem; flex-shrink: 0; }
        .alert-error-content .title { font-weight: 600; color: #991B1B; font-size: 0.875rem; margin: 0 0 2px; }
        .alert-error-content .msg  { font-size: 0.8125rem; color: #B91C1C; margin: 0; }

        /* ── Footer ── */
        .login-footer { margin-top: 2rem; text-align: center; }
        .login-footer p { font-size: 0.75rem; color: var(--gray-500); margin: 0; }
        .login-footer-badges { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; }
        .footer-badge {
            display: flex; align-items: center; gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            background: var(--gray-50); border-radius: 9999px;
            font-size: 0.65rem; font-weight: 600; color: var(--gray-500);
        }
        .footer-badge.secure i { color: var(--success); }

        /* ── Accessibilité ── */
        :focus-visible { outline: 3px solid var(--primary-light); outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>
<div class="login-container">

    {{-- ── Branding gauche ───────────────────────────────────────────────── --}}
    <div class="login-branding">
        <div class="login-branding-content">
            <img src="{{ asset('images/logo.png') }}" alt="Logo CHNP"
                 class="login-branding-logo" onerror="this.style.display='none'">
            <h1>SIRH Sécurisé</h1>
            <p>Système d'Information des Ressources Humaines<br>du Centre Hospitalier National de Pikine</p>
            <div class="security-badges">
                <div class="security-badge"><i class="fas fa-lock"></i><span>Chiffrement AES-256</span></div>
                <div class="security-badge"><i class="fas fa-shield-alt"></i><span>TRIADE CID</span></div>
                <div class="security-badge"><i class="fas fa-user-shield"></i><span>RBAC</span></div>
            </div>
        </div>
    </div>

    {{-- ── Formulaire droite ─────────────────────────────────────────────── --}}
    <div class="login-form-section">
        <div class="login-form-container">

            {{-- Icône + Header --}}
            <div class="login-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CHNP"
                     class="login-logo-mobile" onerror="this.style.display='none'">
                <div class="page-icon">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h2>Nouveau mot de passe</h2>
                <p>Choisissez un mot de passe sécurisé<br>d'au moins 8 caractères.</p>
            </div>

            {{-- Erreurs globales --}}
            @if($errors->any())
                <div class="alert-error" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="alert-error-content">
                        <p class="title">Veuillez corriger les erreurs ci-dessous</p>
                        @foreach($errors->all() as $error)
                            <p class="msg">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('password.store') }}" id="reset-form">
                @csrf

                {{-- Token caché --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email (pré-rempli, lecture seule) --}}
                <div class="form-group">
                    <label for="email" class="form-label">Adresse email</label>
                    <div class="form-input-wrapper">
                        <i class="fas fa-envelope form-input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-invalid @enderror"
                            value="{{ old('email', $request->email) }}"
                            required
                            autocomplete="username"
                            readonly
                            style="background:#F9FAFB;cursor:default;"
                        >
                    </div>
                    @error('email')
                        <p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div class="form-group">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <div class="form-input-wrapper">
                        <i class="fas fa-lock form-input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="Minimum 8 caractères"
                            required
                            autofocus
                            autocomplete="new-password"
                            oninput="checkStrength(this.value)"
                        >
                        <button type="button" class="password-toggle"
                                onclick="togglePassword('password','icon-pwd')"
                                aria-label="Afficher/masquer le mot de passe">
                            <i class="fas fa-eye" id="icon-pwd"></i>
                        </button>
                    </div>
                    {{-- Indicateur de force --}}
                    <div class="password-strength">
                        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                        <span class="strength-label" id="strength-label"></span>
                    </div>
                    @error('password')
                        <p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <div class="form-input-wrapper">
                        <i class="fas fa-lock form-input-icon"></i>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="Répétez votre mot de passe"
                            required
                            autocomplete="new-password"
                            oninput="checkMatch()"
                        >
                        <button type="button" class="password-toggle"
                                onclick="togglePassword('password_confirmation','icon-confirm')"
                                aria-label="Afficher/masquer la confirmation">
                            <i class="fas fa-eye" id="icon-confirm"></i>
                        </button>
                    </div>
                    <p class="form-error" id="match-error" style="display:none;">
                        <i class="fas fa-exclamation-circle"></i> Les mots de passe ne correspondent pas.
                    </p>
                    @error('password_confirmation')
                        <p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-login" id="submit-btn">
                    <i class="fas fa-check-circle"></i>
                    <span>Réinitialiser le mot de passe</span>
                </button>
            </form>

            {{-- Retour connexion --}}
            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Retour à la connexion
            </a>

            {{-- Footer --}}
            <div class="login-footer">
                <p>© {{ date('Y') }} SIRH CHNP — Centre Hospitalier National de Pikine</p>
                <div class="login-footer-badges">
                    <div class="footer-badge secure"><i class="fas fa-lock"></i><span>SSL/TLS</span></div>
                    <div class="footer-badge"><i class="fas fa-shield-alt"></i><span>RGPD</span></div>
                    <div class="footer-badge"><i class="fas fa-key"></i><span>AES-256</span></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// ── Toggle visibilité mot de passe ─────────────────────────
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ── Indicateur de force du mot de passe ───────────────────
function checkStrength(value) {
    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');
    let score = 0;

    if (value.length >= 8)  score++;
    if (value.length >= 12) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    const levels = [
        { pct: '0%',   color: '',          text: '' },
        { pct: '25%',  color: '#EF4444',   text: 'Très faible' },
        { pct: '50%',  color: '#F59E0B',   text: 'Faible' },
        { pct: '75%',  color: '#3B82F6',   text: 'Correct' },
        { pct: '90%',  color: '#10B981',   text: 'Fort' },
        { pct: '100%', color: '#059669',   text: 'Très fort' },
    ];
    const lvl = levels[Math.min(score, 5)];
    fill.style.width = lvl.pct;
    fill.style.backgroundColor = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;

    checkMatch();
}

// ── Vérification correspondance ────────────────────────────
function checkMatch() {
    const pwd     = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const error   = document.getElementById('match-error');
    if (confirm && pwd !== confirm) {
        error.style.display = 'flex';
    } else {
        error.style.display = 'none';
    }
}

// ── État chargement à la soumission ───────────────────────
document.getElementById('reset-form').addEventListener('submit', function (e) {
    const pwd     = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    if (pwd !== confirm) { e.preventDefault(); return; }

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> <span>Réinitialisation…</span>';
});
</script>
</body>
</html>
