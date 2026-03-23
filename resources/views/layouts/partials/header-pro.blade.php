
{{-- ══════════════════════════════════════════════════════════════
     HEADER - Thème sombre : overrides spécifiques aux dropdowns
     (chargé en body après themes.css pour avoir la priorité)
     ══════════════════════════════════════════════════════════════ --}}
<style>
/* ── HOVER ÉTATS (remplacement onmouseover) ────────────────── */
.hov-menu-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.625rem 0.75rem; border-radius: 0.5rem;
    text-decoration: none; color: #374151;
    font-size: 0.875rem; transition: background 150ms;
    border: none; cursor: pointer; width: 100%; text-align: left;
    background: transparent;
}
.hov-menu-item:hover { background: #F3F4F6 !important; color: #111827 !important; }
.hov-logout-btn {
    width: 100%; display: flex; align-items: center; gap: 0.75rem;
    padding: 0.625rem 0.75rem; border-radius: 0.5rem;
    background: transparent; border: none; cursor: pointer;
    color: #EF4444; font-size: 0.875rem; transition: background 150ms; text-align: left;
}
.hov-logout-btn:hover { background: #FEE2E2 !important; }
.hov-notif-item {
    display: flex; gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid #F3F4F6;
    text-decoration: none;
    transition: background 150ms;
    color: inherit;
}
.hov-notif-item:hover { background: #F9FAFB !important; }
.hov-notif-footer {
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    padding: 0.875rem; font-size: 0.875rem; font-weight: 500;
    color: #1565C0; text-decoration: none;
    border-top: 1px solid #E5E7EB;
    background: #F9FAFB; transition: background 150ms;
}
.hov-notif-footer:hover { background: #EFF6FF !important; }
.hov-user-btn {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.375rem 0.75rem; border-radius: 0.5rem;
    background: transparent; border: none; cursor: pointer; transition: background 150ms;
}
.hov-user-btn:hover { background: #F9FAFB !important; }
.hov-notif-read-link {
    font-size: 0.75rem; color: #1565C0; text-decoration: none;
    font-weight: 500; padding: 0.25rem 0.5rem; border-radius: 0.375rem;
    transition: background 150ms; background: transparent;
    border: none; cursor: pointer;
}
.hov-notif-read-link:hover { background: #E3F2FD !important; }

/* ── THÈME SOMBRE : HEADER ─────────────────────────────────── */
[data-theme="dark"] .header,
[data-theme="dark"] header.header {
    background: #161b22 !important;
    border-bottom-color: #30363d !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
}
[data-theme="dark"] header h1,
[data-theme="dark"] .header h1 { color: #e6edf3 !important; }
[data-theme="dark"] .header-icon-btn { color: #8d96a0 !important; }
[data-theme="dark"] .header-icon-btn:hover {
    background: #21262d !important;
    color: #e6edf3 !important;
}
[data-theme="dark"] .header-separator { background: #30363d !important; }

/* ── THÈME SOMBRE : DROPDOWNS ──────────────────────────────── */
[data-theme="dark"] .header-dropdown-panel {
    background: #161b22 !important;
    border-color: #30363d !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 4px 16px rgba(0,0,0,0.3) !important;
}
[data-theme="dark"] .header-dropdown-header {
    background: #0d1117 !important;
    border-bottom-color: #30363d !important;
}
[data-theme="dark"] .notif-scroll-area { background: #161b22 !important; }
[data-theme="dark"] .notif-empty-icon { background: #21262d !important; }

/* ── THÈME SOMBRE : HOVER ÉTATS ────────────────────────────── */
[data-theme="dark"] .hov-menu-item {
    color: #8d96a0 !important;
}
[data-theme="dark"] .hov-menu-item:hover {
    background: #21262d !important;
    color: #e6edf3 !important;
}
[data-theme="dark"] .hov-menu-item i { color: #6e7681 !important; }
[data-theme="dark"] .hov-logout-btn { color: #f85149 !important; }
[data-theme="dark"] .hov-logout-btn:hover { background: rgba(248,81,73,0.15) !important; }
[data-theme="dark"] .hov-notif-item {
    border-bottom-color: #21262d !important;
    color: #e6edf3 !important;
}
[data-theme="dark"] .hov-notif-item:hover { background: #21262d !important; }
[data-theme="dark"] .hov-notif-footer {
    background: #0d1117 !important;
    border-top-color: #30363d !important;
    color: #58a6ff !important;
}
[data-theme="dark"] .hov-notif-footer:hover { background: #161b22 !important; }
[data-theme="dark"] .hov-notif-read-link { color: #58a6ff !important; }
[data-theme="dark"] .hov-notif-read-link:hover { background: rgba(88,166,255,0.15) !important; }
[data-theme="dark"] .hov-user-btn:hover { background: #21262d !important; }
[data-theme="dark"] .online-indicator { border-color: #161b22 !important; }

/* ── THÈME SOMBRE : INPUT SEARCH ───────────────────────────── */
[data-theme="dark"] .header-search-input {
    background: #0d1117 !important;
    border-color: #30363d !important;
    color: #e6edf3 !important;
}
[data-theme="dark"] .header-search-input::placeholder { color: #6e7681 !important; }
[data-theme="dark"] .header-search-hint { color: #6e7681 !important; }
[data-theme="dark"] .header-kbd {
    background: #21262d !important;
    color: #8d96a0 !important;
    border-color: #30363d !important;
}
/* User menu header */
[data-theme="dark"] .user-menu-header {
    background: #0d1117 !important;
    border-bottom-color: #30363d !important;
}
[data-theme="dark"] .user-menu-links { background: #161b22 !important; }
[data-theme="dark"] .user-menu-footer { border-top-color: #30363d !important; }
</style>

<header class="header" style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; height: 64px; background: #ffffff; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; z-index: 1020; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: left 300ms cubic-bezier(0.4, 0, 0.2, 1);">
    
    {{-- ═══════════════════════════════════════════════════════════════════════
         PARTIE GAUCHE - Toggle Mobile + Titre
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; align-items: center; gap: 1rem;">
        {{-- Bouton Toggle Sidebar (Mobile) --}}
        <button 
            @click="sidebarOpen = !sidebarOpen"
            class="header-icon-btn"
            style="display: none;"
            aria-label="Toggle menu"
        >
            <i class="fas fa-bars"></i>
        </button>

        {{-- Fil d'Ariane / Titre de la page --}}
        <div>
            <h1 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0; line-height: 1.2;">
                @yield('page-title', 'Tableau de bord')
            </h1>
            @hasSection('breadcrumb')
                <nav aria-label="Fil d'Ariane" style="margin-top: 0.125rem;">
                    <ol style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0; font-size: 0.75rem; color: #6B7280;">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         PARTIE DROITE - Actions et Profil
         ═══════════════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        
        {{-- Recherche Rapide --}}
        <div x-data="{ searchOpen: false }" class="d-none d-md-block" style="position: relative;">
            <button 
                @click="searchOpen = !searchOpen"
                class="header-icon-btn"
                aria-label="Rechercher"
            >
                <i class="fas fa-search"></i>
            </button>

            {{-- Modal de Recherche --}}
            <div
                x-show="searchOpen"
                x-cloak
                @click.outside="searchOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="header-dropdown-panel"
                style="position: absolute; top: 100%; right: 0; margin-top: 0.5rem; width: 320px; background: white; border-radius: 0.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid #E5E7EB; padding: 1rem; z-index: 1050;"
            >
                <form action="" method="GET">
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9CA3AF;"></i>
                        <input
                            type="search"
                            name="q"
                            placeholder="Rechercher un agent, document..."
                            class="header-search-input"
                            style="width: 100%; height: 44px; padding: 0 1rem 0 2.75rem; border: 1px solid #E5E7EB; border-radius: 0.5rem; font-size: 0.875rem;"
                            autofocus
                        >
                    </div>
                </form>
                <div class="header-search-hint" style="margin-top: 0.75rem; font-size: 0.75rem; color: #6B7280;">
                    <span style="font-weight: 500;">Raccourci :</span>
                    <kbd class="header-kbd" style="background: #F3F4F6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.7rem;">Ctrl + K</kbd>
                </div>
            </div>
        </div>

        {{-- Bouton Notifications --}}
        @php
            try {
                $unreadCount = auth()->user()->unreadNotifications()->count();
                $allNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                $totalNotifications = auth()->user()->notifications()->count();
            } catch (\Exception $e) {
                $unreadCount = 0;
                $allNotifications = collect([]);
                $totalNotifications = 0;
            }
        @endphp
        <div x-data="{ open: false }" style="position: relative;">
            <button
                @click="open = !open"
                class="header-icon-btn"
                aria-label="Notifications"
                style="position: relative;"
            >
                <i class="fas fa-bell" style="transition: transform 200ms;" :style="open ? 'transform: rotate(15deg)' : ''"></i>
                @if($unreadCount > 0)
                    <span class="notification-dot" style="animation: pulse-dot 2s infinite;"></span>
                @endif
            </button>

            {{-- Dropdown Notifications --}}
            <div
                x-show="open"
                x-cloak
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                class="header-dropdown-panel"
                style="position: absolute; top: calc(100% + 0.5rem); right: 0; width: 360px; background: white; border-radius: 0.75rem; box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08); border: 1px solid #E5E7EB; z-index: 1050; overflow: hidden;"
            >
                {{-- Header Notifications --}}
                <div class="header-dropdown-header" style="padding: 1rem 1.25rem; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #F8FAFF 0%, #F0F7FF 100%);">
                    <h3 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-bell" style="color: #1565C0; font-size: 0.875rem;"></i>
                        Notifications
                        @if($unreadCount > 0)
                            <span style="background: #EF4444; color: white; font-size: 0.65rem; padding: 0.1rem 0.45rem; border-radius: 9999px; font-weight: 700;">{{ $unreadCount }}</span>
                        @endif
                    </h3>
                    @if($unreadCount > 0)
                        <button type="button" class="hov-notif-read-link">Tout lire</button>
                    @endif
                </div>

                {{-- Liste des Notifications --}}
                <div class="notif-scroll-area" style="max-height: 320px; overflow-y: auto;">
                    @forelse($allNotifications as $notification)
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                           class="hov-notif-item {{ $notification->read_at ? '' : 'notif-unread' }}"
                           style="{{ $notification->read_at ? '' : 'background: #EFF6FF;' }}"
                        >
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $notification->data['color'] ?? '#DBEAFE' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}" style="color: {{ $notification->data['iconColor'] ?? '#1565C0' }}; font-size: 0.875rem;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-size: 0.875rem; color: #111827; margin: 0; font-weight: {{ $notification->read_at ? '400' : '600' }}; line-height: 1.3;">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>
                                <p style="font-size: 0.75rem; color: #6B7280; margin: 0.2rem 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <p style="font-size: 0.7rem; color: #9CA3AF; margin: 0.2rem 0 0;">
                                    <i class="fas fa-clock" style="margin-right: 0.2rem;"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if(!$notification->read_at)
                                <div style="width: 8px; height: 8px; background: #1565C0; border-radius: 50%; margin-top: 0.375rem; flex-shrink: 0;"></div>
                            @endif
                        </a>
                    @empty
                        <div style="padding: 2.5rem; text-align: center;">
                            <div class="notif-empty-icon" style="width: 56px; height: 56px; background: #F3F4F6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                <i class="fas fa-bell-slash" style="font-size: 1.5rem; color: #D1D5DB;"></i>
                            </div>
                            <p style="font-size: 0.875rem; color: #6B7280; margin: 0; font-weight: 500;">Aucune notification</p>
                            <p style="font-size: 0.75rem; color: #9CA3AF; margin: 0.25rem 0 0;">Vous êtes à jour !</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                @if($totalNotifications > 0)
                    <a href="#" class="hov-notif-footer">
                        Voir toutes les notifications
                        <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Sélecteur de Thème (3 modes) --}}
        <div x-data="themeManager" class="dropdown">
            <button
                class="header-icon-btn btn-icon-theme"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                :title="getLabel()"
            >
                <i class="fas" :class="getIcon()"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow theme-dropdown">
                <li class="dropdown-header"><i class="fas fa-palette me-1"></i> Apparence</li>
                <li><hr class="dropdown-divider m-1"></li>
                <li>
                    <button type="button"
                            class="dropdown-item d-flex align-items-center gap-2"
                            :class="{ 'active': currentTheme === 'light' }"
                            @click="setTheme('light')">
                        <i class="fas fa-sun" style="color: #d29922; width:16px;"></i>
                        <span>Clair</span>
                        <i class="fas fa-check ms-auto" x-show="currentTheme === 'light'" style="font-size:11px; color: var(--theme-success);"></i>
                    </button>
                </li>
                <li>
                    <button type="button"
                            class="dropdown-item d-flex align-items-center gap-2"
                            :class="{ 'active': currentTheme === 'dark' }"
                            @click="setTheme('dark')">
                        <i class="fas fa-moon" style="color: #8b5cf6; width:16px;"></i>
                        <span>Sombre</span>
                        <i class="fas fa-check ms-auto" x-show="currentTheme === 'dark'" style="font-size:11px; color: var(--theme-success);"></i>
                    </button>
                </li>
                <li>
                    <button type="button"
                            class="dropdown-item d-flex align-items-center gap-2"
                            :class="{ 'active': currentTheme === 'system' }"
                            @click="setTheme('system')">
                        <i class="fas fa-desktop" style="color: #6e7781; width:16px;"></i>
                        <span>Système</span>
                        <i class="fas fa-check ms-auto" x-show="currentTheme === 'system'" style="font-size:11px; color: var(--theme-success);"></i>
                    </button>
                </li>
            </ul>
        </div>

{{-- Séparateur --}}
        <div class="header-separator" style="width: 1px; height: 24px; background: #E5E7EB; margin: 0 0.5rem;"></div>

        {{-- Menu Utilisateur --}}
        <div x-data="{ open: false }" style="position: relative;">
            <button
                @click="open = !open"
                class="hov-user-btn"
            >
                {{-- Avatar --}}
                <div style="position: relative;">
                    @if(auth()->user()->agent && auth()->user()->agent->photo)
                        <img 
                            src="{{ asset('storage/' . auth()->user()->agent->photo) }}" 
                            alt="Avatar"
                            style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #E5E7EB;"
                        >
                    @else
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #0A4D8C, #1565C0); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                            {{ strtoupper(substr(auth()->user()->login ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    {{-- Indicateur en ligne --}}
                    <span class="online-indicator" style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #3fb950; border-radius: 50%; border: 2px solid white; animation: pulse-green 2s infinite;"></span>
                </div>

                {{-- Info utilisateur --}}
                <div style="text-align: left; display: none;" class="d-md-block">
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--theme-text-primary, #111827); line-height: 1.2;">
                        {{ auth()->user()->agent->prenom ?? '' }} {{ auth()->user()->agent->nom ?? auth()->user()->login }}
                    </div>
                    <div style="font-size: 0.7rem; color: var(--theme-text-muted, #6B7280);">
                        @if(auth()->user()->hasRole('AdminSystème'))
                            Administrateur Système
                        @elseif(auth()->user()->hasRole('DRH'))
                            Directeur RH
                        @elseif(auth()->user()->hasRole('AgentRH'))
                            Agent RH
                        @elseif(auth()->user()->hasRole('Manager'))
                            Manager
                        @else
                            Agent
                        @endif
                    </div>
                </div>

                <i class="fas fa-chevron-down" style="font-size: 0.7rem; color: #9CA3AF;"></i>
            </button>

            {{-- Dropdown Menu --}}
            <div
                x-show="open"
                x-cloak
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="header-dropdown-panel"
                style="position: absolute; top: 100%; right: 0; margin-top: 0.5rem; width: 240px; background: white; border-radius: 0.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid #E5E7EB; z-index: 1050; overflow: hidden;"
            >
                {{-- En-tête profil --}}
                <div class="user-menu-header" style="padding: 1rem; border-bottom: 1px solid #E5E7EB; background: #F9FAFB;">
                    <div style="font-size: 0.875rem; font-weight: 600; color: #111827;">
                        {{ auth()->user()->agent->prenom ?? '' }} {{ auth()->user()->agent->nom ?? auth()->user()->login }}
                    </div>
                    <div style="font-size: 0.75rem; color: #6B7280; margin-top: 0.125rem;">
                        {{ auth()->user()->email ?? auth()->user()->login }}
                    </div>
                    @if(auth()->user()->agent && auth()->user()->agent->matricule)
                        <div style="font-size: 0.7rem; color: #9CA3AF; margin-top: 0.25rem;">
                            <i class="fas fa-id-badge" style="margin-right: 0.25rem;"></i>
                            {{ auth()->user()->agent->matricule }}
                        </div>
                    @endif
                </div>

                {{-- Liens du menu --}}
                <div class="user-menu-links" style="padding: 0.5rem;">
                    <a href="{{ route('profile.edit') }}" class="hov-menu-item">
                        <i class="fas fa-user" style="width: 16px; text-align: center; color: #6B7280;"></i>
                        Mon profil
                    </a>
                    <a href="{{ route('preferences.index') }}" class="hov-menu-item">
                        <i class="fas fa-sliders-h" style="width: 16px; text-align: center; color: #6B7280;"></i>
                        Préférences
                    </a>
                    <a href="{{ route('aide.index') }}" class="hov-menu-item">
                        <i class="fas fa-life-ring" style="width: 16px; text-align: center; color: #6B7280;"></i>
                        Aide
                    </a>
                </div>

                {{-- Déconnexion --}}
                <div class="user-menu-footer" style="padding: 0.5rem; border-top: 1px solid #E5E7EB;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hov-logout-btn">
                            <i class="fas fa-sign-out-alt" style="width: 16px; text-align: center;"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Styles responsive pour le header --}}
<style>
    @media (max-width: 1024px) {
        .header {
            left: 0 !important;
        }
        
        .header-icon-btn[aria-label="Toggle menu"] {
            display: flex !important;
        }
    }
    
    @media (max-width: 768px) {
        .d-md-block {
            display: none !important;
        }
    }
    
    @media (min-width: 769px) {
        .d-md-block {
            display: block !important;
        }
    }
</style>
