<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Connexion au SIRH Sécurisé - Centre Hospitalier National de Pikine">

    <title>Connexion | SIRH CHNP</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    {{-- Fonts - Inter (Charte Graphique) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ═══════════════════════════════════════════════════════════════════════
           VARIABLES CSS - Charte Graphique v2.0
           ═══════════════════════════════════════════════════════════════════════ */
        :root {
            --primary: #0A4D8C;
            --primary-light: #1565C0;
            --primary-dark: #0D47A1;
            --primary-hover: #E3F2FD;
            --success: #10B981;
            --danger: #EF4444;
            --gray-50: #F9FAFB;
            --gray-200: #E5E7EB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        /* ═══════════════════════════════════════════════════════════════════════
           RESET & BASE
           ═══════════════════════════════════════════════════════════════════════ */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--gray-900);
            background-color: var(--gray-50);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ═══════════════════════════════════════════════════════════════════════
           LAYOUT SPLIT-SCREEN
           ═══════════════════════════════════════════════════════════════════════ */
        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* Partie gauche - Illustration/Branding */
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

        @media (min-width: 1024px) {
            .login-branding {
                display: flex;
            }
        }

        /* Éléments décoratifs */
        .login-branding::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, 30px) rotate(10deg); }
        }

        .login-branding-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            max-width: 480px;
        }

        .login-branding-logo {
            width: 120px;
            height: 120px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
        }

        .login-branding h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 1rem;
            line-height: 1.2;
        }

        .login-branding p {
            font-size: 1.125rem;
            opacity: 0.9;
            margin: 0;
            line-height: 1.6;
        }

        /* Badges de sécurité */
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2.5rem;
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.15);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Partie droite - Formulaire */
        .login-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: white;
        }

        @media (min-width: 1024px) {
            .login-form-section {
                max-width: 50%;
            }
        }

        .login-form-container {
            width: 100%;
            max-width: 400px;
        }

        /* ═══════════════════════════════════════════════════════════════════════
           FORMULAIRE
           ═══════════════════════════════════════════════════════════════════════ */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo-mobile {
            width: 64px;
            height: 64px;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 1024px) {
            .login-logo-mobile {
                display: none;
            }
        }

        .login-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0 0 0.5rem;
        }

        .login-header p {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin: 0;
        }

        /* Champs de formulaire */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1rem;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 1rem 0 2.75rem;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: var(--gray-900);
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 0.5rem;
            transition: all 150ms ease-in-out;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1);
        }

        .form-input.is-invalid {
            border-color: var(--danger);
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-error {
            font-size: 0.75rem;
            color: var(--danger);
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Toggle password */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: var(--gray-700);
        }

        /* Remember me & Forgot password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .form-checkbox input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-light);
            cursor: pointer;
        }

        .form-checkbox span {
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 500;
            transition: color 150ms;
        }

        .forgot-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* Bouton Submit */
        .btn-login {
            width: 100%;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 200ms ease-in-out;
            box-shadow: 0 4px 6px rgba(10, 77, 140, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(10, 77, 140, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Alerte d'erreur */
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: #FEE2E2;
            border: 1px solid #FECACA;
            border-left: 4px solid var(--danger);
            border-radius: 0.5rem;
        }

        .alert-error-icon {
            color: var(--danger);
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .alert-error-content {
            flex: 1;
        }

        .alert-error-title {
            font-weight: 600;
            color: #991B1B;
            margin: 0 0 0.25rem;
        }

        .alert-error-message {
            font-size: 0.875rem;
            color: #B91C1C;
            margin: 0;
        }

        /* Compte bloqué */
        .alert-warning {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: #FEF3C7;
            border: 1px solid #FDE68A;
            border-left: 4px solid #F59E0B;
            border-radius: 0.5rem;
        }

        .alert-warning-icon {
            color: #F59E0B;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .alert-warning-content {
            flex: 1;
        }

        .alert-warning-title {
            font-weight: 600;
            color: #92400E;
            margin: 0 0 0.25rem;
        }

        .alert-warning-message {
            font-size: 0.875rem;
            color: #B45309;
            margin: 0;
        }

        /* ═══════════════════════════════════════════════════════════════════════
           ICÔNE DÉCORATIVE
           ═══════════════════════════════════════════════════════════════════════ */
        .page-icon {
            width: 64px; height: 64px; border-radius: 16px;
            background: var(--primary-hover);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .page-icon i { font-size: 1.75rem; color: var(--primary); }

        /* ═══════════════════════════════════════════════════════════════════════
           FOOTER
           ═══════════════════════════════════════════════════════════════════════ */
        .login-footer {
            margin-top: 2rem;
            text-align: center;
        }

        .login-footer p {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin: 0;
        }

        .login-footer-badges {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .footer-badge {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            background: var(--gray-50);
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--gray-500);
        }

        .footer-badge i {
            font-size: 0.7rem;
        }

        .footer-badge.secure i {
            color: var(--success);
        }

        /* ═══════════════════════════════════════════════════════════════════════
           MICRO-INTERACTIONS LOGIN
           ═══════════════════════════════════════════════════════════════════════ */

        .login-branding::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
            border-radius: 50%;
            animation: float-reverse 25s ease-in-out infinite;
        }

        @keyframes float-reverse {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -30px) scale(1.1); }
        }

        .login-form-container {
            animation: form-enter 400ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes form-enter {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-branding-content {
            animation: branding-enter 600ms cubic-bezier(0.34, 1.56, 0.64, 1) 100ms both;
        }

        @keyframes branding-enter {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.12),
                        0 1px 4px rgba(21, 101, 192, 0.08);
        }

        .form-input.is-invalid {
            animation: input-shake 400ms ease;
        }

        @keyframes input-shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }

        .btn-login .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .security-badge:nth-child(1) { animation: badge-enter 400ms ease 300ms both; }
        .security-badge:nth-child(2) { animation: badge-enter 400ms ease 450ms both; }
        .security-badge:nth-child(3) { animation: badge-enter 400ms ease 600ms both; }

        @keyframes badge-enter {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-input-wrapper:focus-within .form-input-icon {
            color: var(--primary-light);
            transition: color 150ms;
        }

        /* ═══════════════════════════════════════════════════════════════════════
           ACCESSIBILITÉ
           ═══════════════════════════════════════════════════════════════════════ */
        :focus-visible {
            outline: 3px solid var(--primary-light);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>

<body>
    <div class="login-container">
        {{-- ═══════════════════════════════════════════════════════════════════
             PARTIE GAUCHE - Branding
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="login-branding">
            <div class="login-branding-content">
                {{-- Logo --}}
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Logo CHNP"
                    class="login-branding-logo"
                    onerror="this.style.display='none'"
                >

                <h1>SIRH Sécurisé</h1>
                <p>
                    Système d'Information des Ressources Humaines<br>
                    du Centre Hospitalier National de Pikine
                </p>

                {{-- Badges sécurité --}}
                <div class="security-badges">
                    <div class="security-badge">
                        <i class="fas fa-lock"></i>
                        <span>Chiffrement AES-256</span>
                    </div>
                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>TRIADE CID</span>
                    </div>
                    <div class="security-badge">
                        <i class="fas fa-user-shield"></i>
                        <span>RBAC</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             PARTIE DROITE - Formulaire
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="login-form-section">
            <div class="login-form-container">
                {{-- Header --}}
                <div class="login-header">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="Logo CHNP"
                        class="login-logo-mobile"
                        onerror="this.style.display='none'"
                    >
                    <div class="page-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h2>Connexion</h2>
                    <p>Accédez à votre espace personnel sécurisé</p>
                </div>

                {{-- Alertes --}}
                @if(session('error'))
                    <div class="alert-error" role="alert">
                        <i class="fas fa-exclamation-circle alert-error-icon"></i>
                        <div class="alert-error-content">
                            <p class="alert-error-title">Erreur de connexion</p>
                            <p class="alert-error-message">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('account_locked'))
                    <div class="alert-warning" role="alert">
                        <i class="fas fa-lock alert-warning-icon"></i>
                        <div class="alert-warning-content">
                            <p class="alert-warning-title">Compte temporairement verrouillé</p>
                            <p class="alert-warning-message">
                                Trop de tentatives échouées. Veuillez réessayer dans quelques minutes ou contacter l'administrateur.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    {{-- Identifiant --}}
                    <div class="form-group">
                        <label for="login" class="form-label">Identifiant</label>
                        <div class="form-input-wrapper">
                            <i class="fas fa-user form-input-icon"></i>
                            <input
                                type="text"
                                id="login"
                                name="login"
                                class="form-input @error('login') is-invalid @enderror"
                                value="{{ old('login') }}"
                                placeholder="Votre identifiant ou email"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        @error('login')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="form-input-wrapper">
                            <i class="fas fa-lock form-input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-invalid @enderror"
                                placeholder="Votre mot de passe"
                                required
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword()"
                                aria-label="Afficher/masquer le mot de passe"
                            >
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Options --}}
                    <div class="form-options">
                        <label class="form-checkbox">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Se souvenir de moi</span>
                        </label>

                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>

                    {{-- Bouton Submit --}}
                    <button type="submit" class="btn-login" id="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Se connecter</span>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="login-footer">
                    <p>© {{ date('Y') }} SIRH CHNP — Centre Hospitalier National de Pikine</p>
                    <div class="login-footer-badges">
                        <div class="footer-badge secure">
                            <i class="fas fa-lock"></i>
                            <span>SSL/TLS</span>
                        </div>
                        <div class="footer-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>RGPD</span>
                        </div>
                        <div class="footer-badge">
                            <i class="fas fa-key"></i>
                            <span>AES-256</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // ── Toggle password visibility ─────────────────────────────────────
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // ── Form submission - loading state amélioré ───────────────────────
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('login-btn');
            const loginVal = document.getElementById('login').value.trim();
            const passVal  = document.getElementById('password').value.trim();

            if (!loginVal || !passVal) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> <span>Connexion en cours...</span>';
            btn.style.opacity = '0.9';
        });

        // ── Supprimer la classe is-invalid sur correction ─────────────────
        document.querySelectorAll('.form-input.is-invalid').forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>
