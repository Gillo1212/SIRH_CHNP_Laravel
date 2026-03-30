{{--

  SIRH SÉCURISÉ - LAYOUT Principale                                             
                   
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SIRH Sécurisé - Système d'Information des Ressources Humaines du CHNP">
    <meta name="author" content="CHNP - Centre Hospitalier National de Pikine">
    
    <title>@yield('title', 'Tableau de bord') | SIRH CHNP</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon.png') }}">

    {{-- ═══════════════════════════════════════════════════════════════════════
         FONTS - Police Inter (Charte Graphique)
         ═══════════════════════════════════════════════════════════════════════ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- ═══════════════════════════════════════════════════════════════════════
         CSS - Bootstrap 5.3 + Animate.css + Font Awesome
         ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- Bootstrap 5.3 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    {{-- Font Awesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Remix Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">

    {{-- ═══════════════════════════════════════════════════════════════════════
         STYLES PERSONNALISÉS - Charte Graphique SIRH CHNP
         ═══════════════════════════════════════════════════════════════════════ --}}
    <style>
        :root {
            /* Couleurs Primaires - Charte Graphique v2.0 */
            --sirh-primary: #0A4D8C;
            --sirh-primary-light: #1565C0;
            --sirh-primary-dark: #0D47A1;
            --sirh-primary-hover: #E3F2FD;
            
            /* Couleurs d'État */
            --sirh-success: #10B981;
            --sirh-warning: #F59E0B;
            --sirh-danger: #EF4444;
            --sirh-info: #3B82F6;
            
            /* Gris */
            --sirh-gray-50: #F9FAFB;
            --sirh-gray-200: #E5E7EB;
            --sirh-gray-500: #6B7280;
            --sirh-gray-700: #374151;
            --sirh-gray-900: #111827;
            
            /* Layout */
            --sidebar-width: 260px;
            --header-height: 64px;
        }

        /* ═════════════════════════════════════════════════════════════════════
           TYPOGRAPHIE - Inter uniquement
           ═════════════════════════════════════════════════════════════════════ */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
            background-color: var(--sirh-gray-50);
            color: var(--sirh-gray-900);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ═════════════════════════════════════════════════════════════════════
           SIDEBAR - Bleu Médical Principal
           ═════════════════════════════════════════════════════════════════════ */
        .sidebar {
            background: linear-gradient(180deg, var(--sirh-primary) 0%, var(--sirh-primary-dark) 100%) !important;
            width: var(--sidebar-width);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            margin: 0.125rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 200ms ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-left: 3px solid #ffffff;
            margin-left: calc(0.75rem - 3px);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-section-title {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1rem 1.25rem 0.5rem;
        }

        /* Badge notification sidebar */
        .sidebar .badge-notification {
            background-color: var(--sirh-danger);
            color: white;
            font-size: 0.65rem;
            padding: 0.15rem 0.45rem;
            border-radius: 9999px;
            margin-left: auto;
            font-weight: 600;
        }

        /* ═════════════════════════════════════════════════════════════════════
           HEADER
           ═════════════════════════════════════════════════════════════════════ */
        .header {
            height: var(--header-height);
            background-color: #ffffff;
            border-bottom: 1px solid var(--sirh-gray-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            color: var(--sirh-gray-500);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 150ms ease-in-out;
        }

        .header-icon-btn:hover {
            background-color: var(--sirh-gray-50);
            color: var(--sirh-gray-700);
        }

        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background-color: var(--sirh-danger);
            border-radius: 50%;
            border: 2px solid white;
        }

        /* ═════════════════════════════════════════════════════════════════════
           CARTES (Cards) - Charte Graphique
           ═════════════════════════════════════════════════════════════════════ */
        .card {
            background-color: #ffffff;
            border: 1px solid var(--sirh-gray-200);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 200ms ease-in-out;
        }

        .card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--sirh-gray-200);
            background-color: transparent;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--sirh-gray-900);
            margin: 0;
        }

        /* ═════════════════════════════════════════════════════════════════════
           KPI CARDS
           ═════════════════════════════════════════════════════════════════════ */
        .kpi-card {
            position: relative;
            overflow: hidden;
        }

        .kpi-card .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .kpi-card .kpi-icon.primary { 
            background-color: var(--sirh-primary-hover); 
            color: var(--sirh-primary); 
        }
        .kpi-card .kpi-icon.success { 
            background-color: #D1FAE5; 
            color: var(--sirh-success); 
        }
        .kpi-card .kpi-icon.warning { 
            background-color: #FEF3C7; 
            color: var(--sirh-warning); 
        }
        .kpi-card .kpi-icon.danger { 
            background-color: #FEE2E2; 
            color: var(--sirh-danger); 
        }
        .kpi-card .kpi-icon.info { 
            background-color: #DBEAFE; 
            color: var(--sirh-info); 
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--sirh-gray-900);
            line-height: 1;
        }

        .kpi-label {
            font-size: 0.875rem;
            color: var(--sirh-gray-500);
            margin-top: 0.25rem;
        }

        .kpi-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .kpi-trend.positive { color: var(--sirh-success); }
        .kpi-trend.negative { color: var(--sirh-danger); }

        /* ═════════════════════════════════════════════════════════════════════
           BOUTONS - Charte Graphique
           ═════════════════════════════════════════════════════════════════════ */
        .btn-primary {
            background-color: var(--sirh-primary-light) !important;
            border-color: var(--sirh-primary-light) !important;
            color: white !important;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 200ms ease-in-out;
        }

        .btn-primary:hover {
            background-color: #1976D2 !important;
            transform: scale(1.02);
        }

        .btn-primary:active {
            background-color: var(--sirh-primary-dark) !important;
            transform: scale(0.98);
        }

        .btn-secondary {
            background-color: transparent !important;
            border: 2px solid var(--sirh-primary-light) !important;
            color: var(--sirh-primary-light) !important;
            font-weight: 500;
            border-radius: 0.5rem;
        }

        .btn-secondary:hover {
            background-color: var(--sirh-primary-hover) !important;
        }

        .btn-success {
            background-color: var(--sirh-success) !important;
            border-color: var(--sirh-success) !important;
        }

        .btn-danger {
            background-color: var(--sirh-danger) !important;
            border-color: var(--sirh-danger) !important;
        }

        /* ═════════════════════════════════════════════════════════════════════
           BADGES - Charte Graphique
           ═════════════════════════════════════════════════════════════════════ */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }

        .badge-success { background-color: var(--sirh-success); color: white; }
        .badge-warning { background-color: var(--sirh-warning); color: white; }
        .badge-danger { background-color: var(--sirh-danger); color: white; }
        .badge-info { background-color: var(--sirh-info); color: white; }
        .badge-primary { background-color: var(--sirh-primary-light); color: white; }
        .badge-secondary { background-color: var(--sirh-gray-500); color: white; }

        /* ═════════════════════════════════════════════════════════════════════
           TABLEAUX - Charte Graphique
           ═════════════════════════════════════════════════════════════════════ */
        .table {
            font-size: 0.875rem;
        }

        .table thead th {
            background-color: var(--sirh-gray-50);
            font-weight: 600;
            color: var(--sirh-gray-700);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--sirh-gray-200);
        }

        .table tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--sirh-gray-200);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: var(--sirh-primary-hover);
        }

        /* ═════════════════════════════════════════════════════════════════════
           FORMULAIRES - Charte Graphique
           ═════════════════════════════════════════════════════════════════════ */
        .form-control, .form-select {
            height: 44px;
            padding: 0 1rem;
            font-size: 1rem;
            border: 1px solid var(--sirh-gray-200);
            border-radius: 0.5rem;
            transition: all 150ms ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--sirh-primary-light);
            box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--sirh-danger);
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--sirh-gray-700);
            margin-bottom: 0.5rem;
        }

        /* ═════════════════════════════════════════════════════════════════════
           ALERTES - Charte Graphique
           ═════════════════════════════════════════════════════════════════════ */
        .alert {
            border-radius: 0.75rem;
            border-left: 4px solid;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background-color: #D1FAE5;
            border-color: var(--sirh-success);
            color: #059669;
        }

        .alert-warning {
            background-color: #FEF3C7;
            border-color: var(--sirh-warning);
            color: #D97706;
        }

        .alert-danger {
            background-color: #FEE2E2;
            border-color: var(--sirh-danger);
            color: #DC2626;
        }

        .alert-info {
            background-color: #DBEAFE;
            border-color: var(--sirh-info);
            color: #2563EB;
        }

        /* ═════════════════════════════════════════════════════════════════════
           MAIN CONTENT
           ═════════════════════════════════════════════════════════════════════ */
        .main-container {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
            background-color: var(--sirh-gray-50);
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sirh-gray-900);
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--sirh-gray-500);
            margin-top: 0.25rem;
        }

        /* ═════════════════════════════════════════════════════════════════════
           ACCESSIBILITY - WCAG 2.1
           ═════════════════════════════════════════════════════════════════════ */
        :focus-visible {
            outline: 3px solid var(--sirh-primary-light);
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

        /* ═════════════════════════════════════════════════════════════════════
           RESPONSIVE
           ═════════════════════════════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 300ms ease-in-out;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-container {
                margin-left: 0;
            }
        }

        /* ═════════════════════════════════════════════════════════════════════
           BARRE DE CHARGEMENT (Page Progress)
           ═════════════════════════════════════════════════════════════════════ */
        #page-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--sirh-primary-light), #10B981, var(--sirh-primary-light));
            background-size: 200% 100%;
            z-index: 9999;
            transition: width 300ms ease, opacity 400ms ease;
            animation: progress-shimmer 1.5s infinite linear;
        }

        @keyframes progress-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ═════════════════════════════════════════════════════════════════════
           SCROLL TO TOP
           ═════════════════════════════════════════════════════════════════════ */
        #scroll-to-top {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 40px;
            height: 40px;
            background: var(--sirh-primary-light);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);
            transition: all 250ms ease;
            opacity: 0;
            transform: translateY(10px);
            z-index: 1000;
        }

        #scroll-to-top.visible {
            opacity: 1;
            transform: translateY(0);
        }

        #scroll-to-top:hover {
            background: var(--sirh-primary);
            box-shadow: 0 6px 16px rgba(21, 101, 192, 0.4);
            transform: translateY(-2px);
        }

        /* ═════════════════════════════════════════════════════════════════════
           ANIMATIONS DE PAGE
           ═════════════════════════════════════════════════════════════════════ */
        .content-wrapper {
            animation: page-enter 250ms ease-out;
        }

        @keyframes page-enter {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation entrée des cartes KPI */
        .kpi-card {
            animation: card-enter 350ms ease-out both;
        }

        .kpi-card:nth-child(1) { animation-delay: 50ms; }
        .kpi-card:nth-child(2) { animation-delay: 100ms; }
        .kpi-card:nth-child(3) { animation-delay: 150ms; }
        .kpi-card:nth-child(4) { animation-delay: 200ms; }
        .kpi-card:nth-child(5) { animation-delay: 250ms; }

        @keyframes card-enter {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ═════════════════════════════════════════════════════════════════════
           ALERTES FLASH - Animation slide-in
           ═════════════════════════════════════════════════════════════════════ */
        .alert {
            animation: alert-slide-in 300ms ease-out;
        }

        @keyframes alert-slide-in {
            from {
                opacity: 0;
                transform: translateX(-12px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ═════════════════════════════════════════════════════════════════════
           NOTIFICATION DOT - Animation pulse
           ═════════════════════════════════════════════════════════════════════ */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.3); }
        }

        /* ═════════════════════════════════════════════════════════════════════
           RIPPLE EFFECT SUR BOUTONS
           ═════════════════════════════════════════════════════════════════════ */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 150ms;
        }

        .btn:active::after {
            background: rgba(255,255,255,0.15);
        }

        /* ═════════════════════════════════════════════════════════════════════
           SIDEBAR - Transitions améliorées
           ═════════════════════════════════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1030;
            overflow-y: auto;
            transition: transform 300ms cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 300ms ease;
        }

        .sidebar.show {
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }

        /* Active nav-link avec indicateur animé */
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 70%;
            background: white;
            border-radius: 0 3px 3px 0;
        }

        .sidebar .nav-link {
            position: relative;
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ═════════════════════════════════════════════════════════════════════
           TABLE - Row hover amélioré
           ═════════════════════════════════════════════════════════════════════ */
        .table tbody tr {
            transition: background-color 150ms ease;
        }

        /* ═════════════════════════════════════════════════════════════════════
           SKELETON LOADING
           ═════════════════════════════════════════════════════════════════════ */
        .skeleton {
            background: linear-gradient(90deg, #F3F4F6 25%, #E9ECEF 50%, #F3F4F6 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s infinite;
            border-radius: 0.375rem;
        }

        @keyframes skeleton-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        /* ═════════════════════════════════════════════════════════════════════
           FILTER BAR — Standard harmonisé tous modules
           ═════════════════════════════════════════════════════════════════════ */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: #f8f9fa;
            border-radius: 0.625rem;
            margin-bottom: 1rem;
            border: 1px solid var(--sirh-gray-200);
        }
        .filter-bar .form-control,
        .filter-bar .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            height: 42px;
            font-size: 13px;
            color: var(--sirh-gray-700);
        }
        .filter-bar .form-control:focus,
        .filter-bar .form-select:focus {
            border-color: var(--sirh-primary);
            box-shadow: 0 0 0 3px rgba(10,77,140,.12);
        }
        .filter-bar .input-group {
            flex: 1;
            min-width: 200px;
        }
        .filter-bar .input-group .input-group-text {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-right: none;
            border-radius: 0.375rem 0 0 0.375rem;
            color: var(--sirh-gray-500);
            height: 42px;
        }
        .filter-bar .input-group .form-control {
            border-left: none;
            border-radius: 0 0.375rem 0.375rem 0;
        }
        .filter-bar .filter-select {
            min-width: 160px;
            max-width: 220px;
        }
        .filter-bar .btn-filter {
            background: var(--sirh-primary);
            border: none;
            color: #fff;
            padding: 0.5rem 1.25rem;
            height: 42px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.375rem;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: background 200ms;
        }
        .filter-bar .btn-filter:hover {
            background: var(--sirh-primary-dark);
            color: #fff;
        }
        .filter-bar .btn-reset {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: var(--sirh-gray-500);
            width: 42px;
            height: 42px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            transition: all 200ms;
        }
        .filter-bar .btn-reset:hover {
            background: #e2e8f0;
            color: var(--sirh-danger);
        }
        @media (max-width: 768px) {
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar .input-group { min-width: 100%; }
            .filter-bar .filter-select { min-width: 100%; max-width: 100%; }
            .filter-bar .filter-actions { display: flex; gap: 0.5rem; }
            .filter-bar .filter-actions .btn-filter { flex: 1; justify-content: center; }
        }
    </style>

    {{-- Styles additionnels des pages --}}
    @stack('styles')
</head>

{{-- Barre de chargement --}}
<div id="page-progress" style="width: 0;"></div>

<body
    x-data="{
        sidebarOpen: true,
        sidebarCollapsed: false,
        darkMode: false,
        notificationOpen: false,
        userMenuOpen: false
    }"
    :class="{ 'dark': darkMode }"
    :style="sidebarCollapsed ? '--sidebar-width: 70px' : '--sidebar-width: 260px'"
>
    {{-- ═══════════════════════════════════════════════════════════════════════
         SIDEBAR
         ═══════════════════════════════════════════════════════════════════════ --}}
    @include('layouts.partials.sidebar-pro')

    {{-- ═══════════════════════════════════════════════════════════════════════
         MAIN WRAPPER
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="main-container">
        {{-- Header --}}
        @include('layouts.partials.header-pro')

        {{-- Contenu Principal --}}
        <main class="content-wrapper">
            {{-- Messages Flash --}}
            @if(session('success'))
                <div class="alert alert-success mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            {{-- Erreurs de Validation --}}
            @if($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-exclamation-circle mt-1"></i>
                        <div>
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Contenu de la page --}}
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.partials.footer-pro')
    </div>

    {{-- Bouton Scroll to top --}}
    <button id="scroll-to-top" aria-label="Retour en haut" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SCRIPTS
         ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- Bootstrap 5.3 Bundle (Popper inclus) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    {{-- ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Script d'initialisation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Barre de chargement ──────────────────────────────────────────
            const progress = document.getElementById('page-progress');
            if (progress) {
                progress.style.width = '100%';
                setTimeout(() => { progress.style.opacity = '0'; }, 300);
                setTimeout(() => { progress.style.display = 'none'; }, 700);
            }

            // Relancer la barre sur chaque navigation
            document.querySelectorAll('a[href]').forEach(function(link) {
                if (!link.href.startsWith('#') && link.target !== '_blank') {
                    link.addEventListener('click', function() {
                        if (progress) {
                            progress.style.display = 'block';
                            progress.style.opacity = '1';
                            progress.style.width = '0';
                            setTimeout(() => { progress.style.width = '70%'; }, 10);
                        }
                    });
                }
            });

            // ── Configuration ApexCharts ─────────────────────────────────────
            if (typeof ApexCharts !== 'undefined') {
                Apex.colors = ['#0A4D8C', '#1565C0', '#10B981', '#F59E0B', '#EF4444', '#3B82F6'];
                Apex.chart = { fontFamily: 'Inter, system-ui, sans-serif' };
            }

            // ── Auto-fermeture des alertes (5s) ──────────────────────────────
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    alert.style.transition = 'opacity 400ms ease, transform 400ms ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(12px)';
                    setTimeout(() => alert.remove(), 400);
                });
            }, 5000);

            // ── Scroll to top ────────────────────────────────────────────────
            const scrollBtn = document.getElementById('scroll-to-top');
            if (scrollBtn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        scrollBtn.classList.add('visible');
                    } else {
                        scrollBtn.classList.remove('visible');
                    }
                }, { passive: true });
            }

            // ── Raccourci Ctrl+K (recherche) ─────────────────────────────────
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchBtn = document.querySelector('[aria-label="Rechercher"]');
                    if (searchBtn) searchBtn.click();
                }
            });

            // ── Compteur animé pour les KPI ──────────────────────────────────
            function animateCounter(el) {
                const target = parseFloat(el.textContent.replace(/\s/g, ''));
                if (isNaN(target) || target === 0) return;
                const duration = 800;
                const start = performance.now();
                const isFloat = el.textContent.includes('.');

                function update(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
                    const current = target * eased;
                    el.textContent = isFloat ? current.toFixed(1) : Math.round(current).toLocaleString('fr-FR');
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }

            // Observer pour déclencher le compteur quand visible
            const kpiObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.querySelectorAll('.kpi-value').forEach(animateCounter);
                        kpiObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            document.querySelectorAll('.kpi-card').forEach(function(card) {
                kpiObserver.observe(card);
            });
        });

        // ── Confirmation suppression ─────────────────────────────────────────
        function confirmDelete(form, message = 'Cette action est irréversible.') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    borderRadius: '0.75rem'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            } else {
                if (confirm('Confirmer la suppression ? ' + message)) form.submit();
            }
            return false;
        }
    </script>

    {{-- Scripts additionnels des pages --}}
    @stack('scripts')
</body>
</html>
