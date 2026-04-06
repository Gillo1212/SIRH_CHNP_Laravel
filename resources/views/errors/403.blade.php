<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Accès refusé | SIRH CHNP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F0F4F8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 3rem 2rem;
            max-width: 560px;
        }
        .shield {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #0A4D8C, #1565C0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 8px 32px rgba(10,77,140,0.25);
        }
        .shield svg { width: 48px; height: 48px; fill: white; }
        .code {
            font-size: 5rem;
            font-weight: 700;
            color: #0A4D8C;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 1rem;
        }
        .message {
            color: #64748B;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            background: white;
            border: 1px solid #E2E8F0;
            /* border-left: 4px solid #EF4444; */
            border-radius: 8px;
            padding: 1rem 1.25rem;
            text-align: left;
        }
        .message strong { color: #EF4444; }
        .info {
            font-size: 0.85rem;
            color: #94A3B8;
            margin-bottom: 2rem;
        }
        .info span {
            background: #E2E8F0;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.8rem;
            color: #475569;
        }
        .actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #0A4D8C;
            color: white;
        }
        .btn-primary:hover { background: #1565C0; transform: translateY(-1px); }
        .btn-secondary {
            background: white;
            color: #475569;
            border: 1px solid #E2E8F0;
        }
        .btn-secondary:hover { background: #F8FAFC; }
        .logo {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            color: #0A4D8C;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .logo-dot {
            width: 10px; height: 10px;
            background: #0A4D8C;
            border-radius: 50%;
        }
        .user-info {
            font-size: 0.8rem;
            color: #94A3B8;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <!-- <div class="logo-dot"></div> -->
            SIRH du Centre Hospitalier National de Pikine
        </div>

        <div class="shield">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
            </svg>
        </div>

        <div class="code">403</div>
        <div class="title">Accès refusé</div>

        <div class="message">
            <strong>Permission insuffisante.</strong><br>
            {{ $message ?? 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource.' }}
        </div>

        @auth
        <div class="info">
            Connecté en tant que :
            <span>{{ auth()->user()->login }}</span>
           ( Role : <span>{{ auth()->user()->getRoleNames()->first() ?? 'Sans rôle' }}</span>)
        </div>
        @endauth

        <div class="actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn btn-secondary">
                ← Retour
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                Mon tableau de bord
            </a>
        </div>

        <div class="user-info">
            SIRH CHNP - Système d'Information des Ressources Humaines
        </div>
    </div>
</body>
</html>
