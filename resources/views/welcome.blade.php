<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIRH CHNP - Système de Gestion RH</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0A4D8C 0%, #1565C0 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 64px;
            height: 64px;
            background: #E3F2FD;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #1565C0;
            font-size: 1.75rem;
        }
        
        .stats-section {
            background: #F9FAFB;
            padding: 60px 0;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #0A4D8C;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6B7280;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="SIRH CHNP" height="40">
                <span class="ms-2 fw-bold text-primary">SIRH CHNP</span>
            </a>
            
            <div class="d-flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Connexion
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">
                        Système de Gestion des Ressources Humaines
                    </h1>
                    <p class="hero-subtitle">
                        Solution complète et sécurisée pour la gestion du personnel du Centre Hospitalier National de Pikine
                    </p>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-lock me-2"></i>
                            Accès Sécurisé
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold text-primary mb-3">Fonctionnalités Principales</h2>
                    <p class="text-muted">Une suite complète d'outils pour gérer efficacement vos ressources humaines</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="text-primary mb-3">Gestion du Personnel</h4>
                        <p class="text-muted">Dossiers complets, contrats, mouvements et suivi des agents en temps réel.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4 class="text-primary mb-3">Gestion des Congés</h4>
                        <p class="text-muted">Workflow automatisé de validation avec gestion des soldes et historique.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <h4 class="text-primary mb-3">Plannings de Service</h4>
                        <p class="text-muted">Création et validation des plannings par service avec gestion des horaires.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h4 class="text-primary mb-3">GED Intégrée</h4>
                        <p class="text-muted">Gestion électronique des documents avec archivage sécurisé et recherche.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="text-primary mb-3">Reporting Avancé</h4>
                        <p class="text-muted">Tableaux de bord et rapports personnalisés pour le pilotage RH.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="text-primary mb-3">Sécurité Renforcée</h4>
                        <p class="text-muted">Authentification sécurisée, RBAC, audit trail et protection des données.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ \App\Models\Agent::actif()->count() }}</div>
                        <div class="stat-label">Agents Actifs</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ \App\Models\Service::count() }}</div>
                        <div class="stat-label">Services</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ \App\Models\Contrat::where('statut_contrat', 'Actif')->count() }}</div>
                        <div class="stat-label">Contrats Actifs</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Sécurisé</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Centre Hospitalier National de Pikine - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>