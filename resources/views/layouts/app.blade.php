<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIRH CHNP &mdash; @yield('title', 'Dashboard')</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    /* ================================================
       VARIABLES
    ================================================ */
    :root {
        --sidebar-width: 260px;
        --sidebar-bg: #1a3a2a;
        --sidebar-hover: rgba(255,255,255,0.08);
        --sidebar-active: #198754;
        --topbar-height: 60px;
        --chnp-green: #198754;
        --body-bg: #f0f2f5;
    }

    /* ================================================
       BASE
    ================================================ */
    body {
        background: var(--body-bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        overflow-x: hidden;
    }

    /* ================================================
       SIDEBAR
    ================================================ */
    #sidebar {
        position: fixed;
        top: 0; left: 0;
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--sidebar-bg);
        z-index: 1050;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
    }
    #sidebar::-webkit-scrollbar { width: 3px; }
    #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

    /* Brand */
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 1rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        text-decoration: none;
        flex-shrink: 0;
    }
    .sidebar-brand-logo {
        width: 38px; height: 38px; border-radius: 8px;
        background: var(--chnp-green);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; font-weight: 900; color: white;
        flex-shrink: 0;
    }
    .sidebar-brand-name { font-weight: 700; font-size: 1rem; color: white; line-height: 1.2; }
    .sidebar-brand-sub  { font-size: 0.68rem; color: rgba(255,255,255,0.4); font-weight: 400; }

    /* User card */
    .sidebar-user {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sidebar-avatar {
        width: 42px; height: 42px; border-radius: 50%;
        background: var(--chnp-green);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; font-weight: 700; color: white;
        border: 2px solid rgba(255,255,255,0.25);
        flex-shrink: 0;
        object-fit: cover;
    }
    .sidebar-user-name { color: white; font-weight: 600; font-size: 0.85rem; line-height: 1.3; }
    .sidebar-user-badge { font-size: 0.65rem; padding: 2px 7px; border-radius: 10px; font-weight: 600; }

    /* Nav */
    .sidebar-nav { flex: 1; padding: 0.5rem 0; }
    .sidebar-section {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255,255,255,0.3);
        padding: 0.75rem 1rem 0.2rem;
        font-weight: 600;
    }
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 0.55rem 1rem;
        color: rgba(255,255,255,0.65);
        text-decoration: none;
        font-size: 0.855rem;
        transition: all 0.15s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    .sidebar-link:hover { color: white; background: var(--sidebar-hover); }
    .sidebar-link.active {
        color: white;
        background: var(--sidebar-active);
        border-left: 3px solid rgba(255,255,255,0.7);
        padding-left: calc(1rem - 3px);
    }
    .sidebar-link .nav-icon { width: 17px; text-align: center; font-size: 0.82rem; flex-shrink: 0; }
    .sidebar-link .ms-auto { margin-left: auto !important; }
    .sidebar-link.coming-soon { opacity: 0.4; cursor: default; pointer-events: none; }
    .coming-soon { opacity: 0.45 !important; cursor: default !important; pointer-events: none !important; }
    .sidebar-arrow { margin-left: auto; font-size: 0.6rem; transition: transform 0.2s; }
    .sidebar-link[aria-expanded="true"] .sidebar-arrow { transform: rotate(90deg); }

    /* Sub-menu */
    .sidebar-submenu { background: rgba(0,0,0,0.18); }
    .sidebar-submenu .sidebar-link { padding-left: 2.4rem; font-size: 0.82rem; }

    /* Footer */
    .sidebar-footer {
        padding: 0.6rem 0.75rem;
        border-top: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }

    /* ================================================
       MAIN WRAPPER
    ================================================ */
    #mainWrapper {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: margin-left 0.3s ease;
    }

    /* ================================================
       TOPBAR
    ================================================ */
    #topbar {
        position: sticky; top: 0;
        height: var(--topbar-height);
        background: white;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        padding: 0 1.25rem;
        gap: 0.75rem;
        z-index: 1040;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        flex-shrink: 0;
    }
    #sidebarToggle {
        background: none; border: none;
        color: #6c757d; font-size: 1.15rem;
        padding: 0.3rem 0.5rem;
        cursor: pointer;
        border-radius: 0.375rem;
        line-height: 1;
        display: none;
    }
    #sidebarToggle:hover { background: #f3f4f6; }
    .topbar-title { font-weight: 600; font-size: 1rem; color: #1f2937; }
    .topbar-spacer { flex: 1; }

    /* ================================================
       CONTENT & FOOTER
    ================================================ */
    .page-content { flex: 1; padding: 1.5rem; }
    #appFooter {
        background: white;
        border-top: 1px solid #e9ecef;
        padding: 0.6rem 1.5rem;
        font-size: 0.78rem;
        color: #9ca3af;
        flex-shrink: 0;
    }

    /* ================================================
       BACKDROP
    ================================================ */
    #sidebarBackdrop {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1049;
    }
    #sidebarBackdrop.show { display: block; }

    /* ================================================
       RESPONSIVE
    ================================================ */
    @media (max-width: 991.98px) {
        #sidebar       { transform: translateX(-100%); }
        #sidebar.show  { transform: translateX(0); }
        #mainWrapper   { margin-left: 0; }
        #sidebarToggle { display: block; }
        .page-content  { padding: 1rem; }
    }

    /* ================================================
       CARDS & UTILITIES
    ================================================ */
    .stat-card {
        border: none !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 1px 6px rgba(0,0,0,0.07) !important;
        transition: box-shadow 0.2s;
    }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important; }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 0.6rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .quick-action-btn {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 8px; padding: 1.25rem 0.75rem;
        border-radius: 0.75rem;
        font-size: 0.85rem; font-weight: 500;
        text-decoration: none;
        transition: all 0.15s;
        border: 1.5px solid transparent;
        color: inherit;
    }
    .quick-action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .quick-action-btn i { font-size: 1.5rem; }
    .section-card {
        border: none !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 1px 6px rgba(0,0,0,0.07) !important;
    }
    .section-card .card-header {
        background: white !important;
        border-bottom: 1px solid #f3f4f6;
        border-radius: 0.75rem 0.75rem 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .welcome-banner {
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #198754 0%, #0f5132 100%);
        color: white;
        padding: 1.5rem;
    }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Backdrop mobile --}}
    <div id="sidebarBackdrop" onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main --}}
    <div id="mainWrapper">
        @include('layouts.partials.header')

        <main class="page-content">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarBackdrop').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('sidebarBackdrop').classList.remove('show');
    }
    </script>

    @stack('scripts')
</body>
</html>
