{{--
   Sidebar
--}}

<style>
/* ══════════════════════════════════════════════════════════════
   SIDEBAR MINIMALISTE — Charte Graphique CHNP
   ══════════════════════════════════════════════════════════════ */

.sidebar {
    background: #ffffff !important;
    border-right: 1px solid #E5E7EB !important;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.04) !important;
    overflow: visible !important;
    transition: width 250ms cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* ── BOUTON TOGGLE COLLAPSE ───────────────────────────────── */
.sb-toggle-btn {
    position: absolute;
    top: 20px;
    right: -12px;
    width: 24px;
    height: 24px;
    background: #ffffff;
    border: 1.5px solid #E5E7EB;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    color: #6B7280;
    font-size: 9px;
    transition: all 200ms;
    line-height: 1;
}

.sb-toggle-btn:hover {
    background: #0A4D8C;
    border-color: #0A4D8C;
    color: white;
    transform: scale(1.1);
}

/* ── HEADER LOGO ──────────────────────────────────────────── */
.sb-header {
    height: 64px;
    display: flex;
    align-items: center;
    padding: 0 14px;
    border-bottom: 1px solid #F3F4F6;
    gap: 10px;
    overflow: hidden;
    flex-shrink: 0;
}

.sb-logo-mark {
    width: 36px;
    height: 36px;
    min-width: 36px;
    background: linear-gradient(135deg, #0A4D8C 0%, #1565C0 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 15px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(10, 77, 140, 0.25);
    text-decoration: none;
}

.sb-logo-mark:hover { color: white; }

.sb-logo-text {
    overflow: hidden;
    transition: opacity 200ms, max-width 200ms;
    max-width: 200px;
}

.sidebar.sb-collapsed .sb-logo-text {
    opacity: 0;
    max-width: 0;
}

/* ── NAVIGATION ───────────────────────────────────────────── */
.sb-nav {
    padding: 8px 0 16px;
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    height: calc(100vh - 64px);
}

.sb-nav::-webkit-scrollbar { width: 3px; }
.sb-nav::-webkit-scrollbar-track { background: transparent; }
.sb-nav::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 2px; }
.sb-nav::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }

/* ── TITRES DE SECTION ────────────────────────────────────── */
.sb-section-title {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #9CA3AF;
    padding: 18px 0 5px 16px;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity 200ms, padding 200ms;
    user-select: none;
}

.sidebar.sb-collapsed .sb-section-title {
    opacity: 0;
    padding-top: 10px;
    padding-bottom: 3px;
    pointer-events: none;
}

.sidebar.sb-collapsed .sb-section-title::after {
    content: '';
    display: block;
    width: 30px;
    height: 1px;
    background: #E5E7EB;
    margin: 6px auto 0;
    opacity: 1;
}

/* ── ITEMS DE NAVIGATION ──────────────────────────────────── */
.sb-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    margin: 1px 6px;
    border-radius: 7px;
    color: #374151;
    font-size: 13px;
    font-weight: 400;
    text-decoration: none;
    cursor: pointer;
    transition: background 150ms, color 150ms;
    position: relative;
    white-space: nowrap;
    overflow: hidden;
    line-height: 1.4;
}

.sb-item:hover {
    background: #F3F4F6;
    color: #111827;
    text-decoration: none;
}

.sb-item.active {
    background: #EFF6FF;
    color: #1565C0;
    font-weight: 500;
}

.sb-item.active .sb-icon { color: #1565C0; }
.sb-item.active .sb-icon i { color: #1565C0; }

/* ── ICÔNE ────────────────────────────────────────────────── */
.sb-icon {
    width: 20px;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #6B7280;
    transition: color 150ms;
    flex-shrink: 0;
}

.sb-item:hover .sb-icon { color: #374151; }

/* ── LABEL ────────────────────────────────────────────────── */
.sb-label {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: opacity 150ms, max-width 150ms;
    max-width: 200px;
}

.sidebar.sb-collapsed .sb-label {
    opacity: 0;
    max-width: 0;
    overflow: hidden;
}

/* ── CHEVRON ──────────────────────────────────────────────── */
.sb-chevron {
    font-size: 10px;
    color: #D1D5DB;
    transition: transform 200ms ease, opacity 150ms, color 150ms;
    flex-shrink: 0;
}

.sb-chevron.sb-open { transform: rotate(90deg); }
.sb-item:hover .sb-chevron { color: #9CA3AF; }
.sb-item.active .sb-chevron { color: #93C5FD; }

.sidebar.sb-collapsed .sb-chevron {
    opacity: 0;
    width: 0;
    min-width: 0;
}

/* ── BADGE COMPTEUR ───────────────────────────────────────── */
.sb-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    background: #EF4444;
    color: white;
    font-size: 10px;
    font-weight: 700;
    border-radius: 9999px;
    flex-shrink: 0;
    transition: opacity 150ms;
    line-height: 1;
}

.sidebar.sb-collapsed .sb-badge {
    position: absolute;
    top: 3px;
    right: 5px;
    min-width: 14px;
    height: 14px;
    padding: 0 3px;
    font-size: 8px;
    opacity: 1 !important;
}

/* ── SOUS-MENU ────────────────────────────────────────────── */
.sb-submenu { overflow: hidden; }

.sb-subitem {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px 7px 38px;
    margin: 1px 6px;
    border-radius: 6px;
    color: #6B7280;
    font-size: 12.5px;
    text-decoration: none;
    transition: background 150ms, color 150ms;
    position: relative;
    white-space: nowrap;
}

.sb-subitem::before {
    content: '';
    position: absolute;
    left: 26px;
    top: 50%;
    transform: translateY(-50%);
    width: 5px;
    height: 5px;
    background: #D1D5DB;
    border-radius: 50%;
    transition: background 150ms, transform 150ms;
    flex-shrink: 0;
}

.sb-subitem:hover {
    background: #F9FAFB;
    color: #374151;
    text-decoration: none;
}

.sb-subitem:hover::before { background: #1565C0; transform: translateY(-50%) scale(1.2); }

.sb-subitem.active {
    color: #1565C0;
    font-weight: 500;
    background: #F0F9FF;
}

.sb-subitem.active::before { background: #1565C0; }

/* Masquer sous-menus en mode collapsed */
.sidebar.sb-collapsed .sb-submenu { display: none !important; }

/* ── TOOLTIP (mode collapsed) ─────────────────────────────── */
.sb-tooltip {
    position: absolute;
    left: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    background: #1F2937;
    color: white;
    font-size: 12px;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    visibility: hidden;
    transition: opacity 150ms, visibility 150ms;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.sb-tooltip::before {
    content: '';
    position: absolute;
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    border: 5px solid transparent;
    border-right-color: #1F2937;
}

.sidebar.sb-collapsed .sb-item:hover .sb-tooltip {
    opacity: 1;
    visibility: visible;
}

/* ── DIVISEUR ─────────────────────────────────────────────── */
.sb-divider {
    height: 1px;
    background: #F3F4F6;
    margin: 8px 14px;
}

/* ── BADGE TRIADE CID ─────────────────────────────────────── */
.sb-cid-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    margin: 0 6px 4px;
    background: linear-gradient(135deg, #EFF6FF 0%, #E0F2FE 100%);
    border: 1px solid #BFDBFE;
    border-radius: 8px;
    transition: all 200ms;
    overflow: hidden;
    cursor: default;
}

.sb-cid-badge:hover {
    background: linear-gradient(135deg, #DBEAFE 0%, #BAE6FD 100%);
    box-shadow: 0 2px 8px rgba(21, 101, 192, 0.12);
}

.sidebar.sb-collapsed .sb-cid-badge {
    justify-content: center;
    padding: 8px 4px;
}

.sidebar.sb-collapsed .sb-cid-text { display: none; }

/* ── RESPONSIVE MOBILE ────────────────────────────────────── */
@media (max-width: 1024px) {
    .sb-toggle-btn { display: none !important; }
    .sidebar { transform: translateX(-100%) !important; width: 260px !important; }
    .sidebar.show { transform: translateX(0) !important; box-shadow: 4px 0 24px rgba(0,0,0,0.15) !important; }
}

/* ══════════════════════════════════════════════════════════════
   THÈME SOMBRE — Sidebar (injecté ici pour overrider l'ordre CSS)
   ══════════════════════════════════════════════════════════════ */
[data-theme="dark"] .sidebar {
    background: #010409 !important;
    border-right-color: #30363d !important;
    box-shadow: 2px 0 8px rgba(0,0,0,0.3) !important;
}
[data-theme="dark"] .sb-toggle-btn {
    background: #21262d !important;
    border-color: #30363d !important;
    color: #8d96a0 !important;
}
[data-theme="dark"] .sb-toggle-btn:hover {
    background: #58a6ff !important;
    border-color: #58a6ff !important;
    color: #ffffff !important;
}
[data-theme="dark"] .sb-header {
    border-bottom-color: #30363d !important;
}
[data-theme="dark"] .sb-nav::-webkit-scrollbar-track { background: #010409 !important; }
[data-theme="dark"] .sb-nav::-webkit-scrollbar-thumb { background: #30363d !important; border-radius: 2px; }
[data-theme="dark"] .sb-nav::-webkit-scrollbar-thumb:hover { background: #484f58 !important; }
[data-theme="dark"] .sb-section-title {
    color: #6e7681 !important;
}
[data-theme="dark"] .sidebar.sb-collapsed .sb-section-title::after {
    background: #30363d !important;
}
[data-theme="dark"] .sb-item {
    color: #8d96a0 !important;
}
[data-theme="dark"] .sb-item:hover {
    background: #161b22 !important;
    color: #e6edf3 !important;
}
[data-theme="dark"] .sb-item.active {
    background: rgba(88, 166, 255, 0.15) !important;
    color: #58a6ff !important;
}
[data-theme="dark"] .sb-item.active .sb-icon,
[data-theme="dark"] .sb-item.active .sb-icon i { color: #58a6ff !important; }
[data-theme="dark"] .sb-icon { color: #6e7681 !important; }
[data-theme="dark"] .sb-item:hover .sb-icon { color: #8d96a0 !important; }
[data-theme="dark"] .sb-chevron { color: #484f58 !important; }
[data-theme="dark"] .sb-item.active .sb-chevron { color: #58a6ff !important; }
[data-theme="dark"] .sb-subitem {
    color: #6e7681 !important;
}
[data-theme="dark"] .sb-subitem::before { background: #30363d !important; }
[data-theme="dark"] .sb-subitem:hover {
    background: #161b22 !important;
    color: #e6edf3 !important;
}
[data-theme="dark"] .sb-subitem:hover::before { background: #58a6ff !important; }
[data-theme="dark"] .sb-subitem.active {
    background: rgba(88, 166, 255, 0.1) !important;
    color: #58a6ff !important;
}
[data-theme="dark"] .sb-subitem.active::before { background: #58a6ff !important; }
[data-theme="dark"] .sb-divider { background: #30363d !important; }
[data-theme="dark"] .sb-cid-badge {
    background: rgba(88, 166, 255, 0.15) !important;
    border-color: rgba(88, 166, 255, 0.3) !important;
    box-shadow: none !important;
}
</style>

{{-- ══════════════════════════════════════════════════════════════
     ASIDE PRINCIPAL
     ══════════════════════════════════════════════════════════════ --}}
<aside
    class="sidebar"
    :class="{ 'show': sidebarOpen, 'sb-collapsed': sidebarCollapsed }"
>
    {{-- Bouton toggle collapse (desktop) --}}
    <button
        @click="sidebarCollapsed = !sidebarCollapsed"
        class="sb-toggle-btn d-none d-lg-flex"
        :title="sidebarCollapsed ? 'Agrandir le menu' : 'Réduire le menu'"
        aria-label="Toggle sidebar"
    >
        <i
            class="fas fa-chevron-left"
            style="transition: transform 250ms;"
            :style="sidebarCollapsed ? 'transform: rotate(180deg)' : ''"
        ></i>
    </button>

    {{-- ─── LOGO ─────────────────────────────────────────────── --}}
    <div class="sb-header">
        <a href="{{ route('dashboard') }}" class="sb-logo-mark" style="background: transparent; box-shadow: none; padding: 0;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo CHNP"
                 style="width: 36px; height: 36px; object-fit: contain; border-radius: 6px;"
                 onerror="this.replaceWith(Object.assign(document.createElement('span'), {className: 'fas fa-hospital-alt', style: 'font-size:18px;color:#0A4D8C;'}))">
        </a>
        <div class="sb-logo-text">
            <div style="font-size: 14px; font-weight: 700; color: #0A4D8C; line-height: 1.2; white-space: nowrap;">SIRH CHNP</div>
            <div style="font-size: 10px; color: #9CA3AF; font-weight: 500; letter-spacing: 0.06em; white-space: nowrap;">Système RH Sécurisé</div>
        </div>
    </div>

    {{-- ─── NAVIGATION ───────────────────────────────────────── --}}
    <nav class="sb-nav">

        {{-- ══════════════════════════════════════
             MENU ADMIN SYSTÈME
             ══════════════════════════════════════ --}}
        @hasrole('AdminSystème')

            <div class="sb-section-title">Menu</div>

            <a href="{{ route('admin.dashboard') }}" class="sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="sb-label">Tableau de bord</span>
                <span class="sb-tooltip">Tableau de bord</span>
            </a>

            <div class="sb-section-title">Administration</div>

            {{-- Utilisateurs --}}
            {{-- Tous les comptes --}}
            <a href="{{ route('admin.accounts.index') }}"
               class="sb-item {{ request()->routeIs('admin.accounts.index') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-users-cog"></i></span>
                <span class="sb-label">Tous les comptes</span>
                <span class="sb-tooltip">Tous les comptes</span>
            </a>

            {{-- Agents sans compte --}}
            @php
                try {
                    $nbSansCompte = \App\Models\User::where('agent_completed', false)->count()
                                 + \App\Models\Agent::whereNull('user_id')->where('account_pending', true)->whereNull('deleted_at')->count();
                } catch (\Exception $e) { $nbSansCompte = 0; }
            @endphp
            <a href="{{ route('admin.accounts.agents-sans-compte') }}"
               class="sb-item {{ request()->routeIs('admin.accounts.agents-sans-compte') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-clock"></i></span>
                <span class="sb-label">Agents sans compte</span>
                @if($nbSansCompte > 0)
                    <span class="sb-badge">{{ $nbSansCompte }}</span>
                @endif
                <span class="sb-tooltip">Agents sans compte</span>
            </a>

            {{-- Rôles & Permissions --}}
            <div x-data="{ open: {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="sb-label">Rôles & Permissions</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Rôles & Permissions</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('admin.roles.index') }}" class="sb-subitem {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Gestion rôles</a>
                    <a href="{{ route('admin.permissions.matrix') }}" class="sb-subitem {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">Matrice permissions</a>
                </div>
            </div>

            <div class="sb-section-title">Sécurité</div>

            {{-- Logs d'Audit --}}
            <a href="{{ route('admin.audit.index') }}" class="sb-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-clipboard-list"></i></span>
                <span class="sb-label">Logs d'Audit</span>
                <span class="sb-tooltip">Logs d'Audit</span>
            </a>

            <div class="sb-section-title">Paramètres</div>

            {{-- Paramètres Système --}}
            <div x-data="{ open: {{ request()->routeIs('admin.settings*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-cogs"></i></span>
                    <span class="sb-label">Paramètres Système</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Paramètres</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('admin.settings.index') }}" class="sb-subitem {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">Configuration</a>
                    <a href="{{ route('admin.settings.notifications') }}" class="sb-subitem {{ request()->routeIs('admin.settings.notifications') ? 'active' : '' }}">Notifications</a>
                </div>
            </div>

            {{-- Sauvegardes --}}
            <div x-data="{ open: {{ request()->routeIs('admin.backups.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-database"></i></span>
                    <span class="sb-label">Sauvegardes</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Sauvegardes</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('admin.backups.index') }}" class="sb-subitem {{ request()->routeIs('admin.backups.index') ? 'active' : '' }}">Gestion des sauvegardes</a>
                </div>
            </div>

        @endhasrole

        {{-- ══════════════════════════════════════
             MENU AGENT RH
             ══════════════════════════════════════ --}}
        @hasrole('AgentRH')

            <div class="sb-section-title">Menu</div>

            <a href="{{ route('rh.dashboard') }}" class="sb-item {{ request()->routeIs('rh.dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-chart-line"></i></span>
                <span class="sb-label">Tableau de bord</span>
                <span class="sb-tooltip">Tableau de bord</span>
            </a>

            <div class="sb-section-title">Gestion du Personnel</div>

            {{-- Personnel --}}
            <div x-data="{ open: {{ request()->routeIs('rh.agents.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.agents.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-users"></i></span>
                    <span class="sb-label">Personnel</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Personnel</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.agents.index') }}" class="sb-subitem {{ request()->routeIs('rh.agents.index') ? 'active' : '' }}">Liste des agents</a>
                    @php
                        try { $nbComptesAttente = \App\Models\User::where('agent_completed', false)->count(); } catch (\Exception $e) { $nbComptesAttente = 0; }
                    @endphp
                    <a href="{{ route('rh.agents.comptes-a-completer') }}" class="sb-subitem {{ request()->routeIs('rh.agents.comptes-a-completer') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                        Comptes à compléter
                        @if($nbComptesAttente > 0)
                            <span class="sb-badge" style="margin-left:auto;position:static;">{{ $nbComptesAttente }}</span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Contrats --}}
            @php
                try { $contratsExpiring = \App\Models\Contrat::where('date_fin','<=',now()->addDays(60))->where('date_fin','>=',now())->where('statut_contrat','Actif')->count(); }
                catch (\Exception $e) { $contratsExpiring = 0; }
            @endphp
            <a href="{{ route('rh.contrats.index') }}" class="sb-item {{ request()->routeIs('rh.contrats.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-file-contract"></i></span>
                <span class="sb-label">Contrats</span>
                @if($contratsExpiring > 0)
                    <span class="sb-badge">{{ $contratsExpiring }}</span>
                @endif
                <span class="sb-tooltip">Contrats</span>
            </a>

            {{-- Mouvements --}}
            <a href="{{ route('rh.mouvements.index') }}" class="sb-item {{ request()->routeIs('rh.mouvements.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-exchange-alt"></i></span>
                <span class="sb-label">Mouvements</span>
                <span class="sb-tooltip">Mouvements</span>
            </a>

            <div class="sb-section-title">Congés & Absences</div>

            {{-- Congés RH --}}
            <div x-data="{ open: {{ request()->routeIs('rh.conges.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.conges.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-umbrella-beach"></i></span>
                    <span class="sb-label">Congés</span>
                    @php
                        try {
                            $pendingLeaves = \App\Models\Demande::where('type_demande', 'Conge')
                                ->whereIn('statut_demande', ['En_attente', 'Validé'])->count();
                        } catch (\Exception $e) { $pendingLeaves = 0; }
                    @endphp
                    @if($pendingLeaves > 0)
                        <span class="sb-badge">{{ $pendingLeaves }}</span>
                    @endif
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Congés</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.conges.pending') }}" class="sb-subitem {{ request()->routeIs('rh.conges.pending') ? 'active' : '' }}">
                        Demandes en attente
                        @if($pendingLeaves > 0)
                            <span class="sb-badge" style="margin-left: auto; position: static;">{{ $pendingLeaves }}</span>
                        @endif
                    </a>
                    <a href="{{ route('rh.conges.index') }}" class="sb-subitem {{ request()->routeIs('rh.conges.index') ? 'active' : '' }}">Historique complet</a>
                    <a href="{{ route('rh.conges.soldes') }}" class="sb-subitem {{ request()->routeIs('rh.conges.soldes') ? 'active' : '' }}">Gestion des soldes</a>
                </div>
            </div>

            {{-- Absences RH --}}
            <a href="{{ route('rh.absences.index') }}" class="sb-item {{ request()->routeIs('rh.absences.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-clock"></i></span>
                <span class="sb-label">Absences</span>
                <span class="sb-tooltip">Absences</span>
            </a>

            <div class="sb-section-title">Demandes à Traiter</div>

            {{-- Documents Administratifs --}}
            <div x-data="{ open: {{ request()->routeIs('rh.demandes-docs.*') || request()->routeIs('documents-admin.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.demandes-docs.*') || request()->routeIs('documents-admin.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="sb-label">Administratif</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Administratif</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.demandes-docs.pending') }}" class="sb-subitem {{ request()->routeIs('rh.demandes-docs.pending') ? 'active' : '' }}">Demandes en attente</a>
                    <a href="{{ route('documents-admin.index') }}" class="sb-subitem {{ request()->routeIs('documents-admin.index') ? 'active' : '' }}">Générer document</a>
                    <a href="{{ route('rh.demandes-docs.index') }}" class="sb-subitem {{ request()->routeIs('rh.demandes-docs.index') ? 'active' : '' }}">Historique</a>
                </div>
            </div>

            {{-- Prises en charge --}}
            <div x-data="{ open: {{ request()->routeIs('pec.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('pec.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-heartbeat"></i></span>
                    <span class="sb-label">Prises en charge</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Prises en charge</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('pec.index') }}" class="sb-subitem {{ request()->routeIs('pec.index') ? 'active' : '' }}">Demandes en attente</a>
                    <a href="{{ route('pec.create') }}" class="sb-subitem {{ request()->routeIs('pec.create') ? 'active' : '' }}">Nouvelle PEC</a>
                    <a href="{{ route('pec.historique') }}" class="sb-subitem {{ request()->routeIs('pec.historique') ? 'active' : '' }}">Historique</a>
                </div>
            </div>

            <div class="sb-section-title">Planification</div>

            {{-- Plannings RH --}}
            <div x-data="{ open: {{ request()->routeIs('rh.plannings.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.plannings.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="sb-label">Plannings</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Plannings</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.plannings.pending') }}" class="sb-subitem {{ request()->routeIs('rh.plannings.pending') ? 'active' : '' }}">À valider</a>
                    <a href="{{ route('rh.plannings.index') }}" class="sb-subitem {{ request()->routeIs('rh.plannings.index') ? 'active' : '' }}">Tous les plannings</a>
                </div>
            </div>

            <div class="sb-section-title">GED & Archives</div>

            {{-- GED avec sous-menu --}}
            <div x-data="{ open: {{ request()->routeIs('rh.ged.*') || request()->routeIs('rh.documents.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.ged.*') || request()->routeIs('rh.documents.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-folder-open"> </i></span>
                    <span class="sb-label">GED</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">GED Archives</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.ged.index') }}" class="sb-subitem {{ request()->routeIs('rh.ged.index') ? 'active' : '' }}">
                        <i class="ri-dashboard-line me-1"></i> Tableau de bord
                    </a>
                    <a href="{{ route('rh.ged.dossiers') }}" class="sb-subitem {{ request()->routeIs('rh.ged.dossiers') || request()->routeIs('rh.ged.dossier.*') ? 'active' : '' }}">
                        <i class="ri-folder-3-line me-1"></i> Dossiers agents
                    </a>
                    <a href="{{ route('rh.ged.etageres') }}" class="sb-subitem {{ request()->routeIs('rh.ged.etageres') ? 'active' : '' }}">
                        <i class="ri-archive-drawer-line me-1"></i> Étagères
                    </a>
                </div>
            </div>

            <div class="sb-section-title">Organisation</div>

            {{-- Services --}}
            <a href="{{ route('rh.services.index') }}" class="sb-item {{ request()->routeIs('rh.services.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-sitemap"></i></span>
                <span class="sb-label">Services</span>
                <span class="sb-tooltip">Services</span>
            </a>

            {{-- Divisions --}}
            <a href="{{ route('rh.divisions.index') }}" class="sb-item {{ request()->routeIs('rh.divisions.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-building"></i></span>
                <span class="sb-label">Divisions</span>
                <span class="sb-tooltip">Divisions</span>
            </a>

            <div class="sb-section-title">Rapports</div>

            {{-- Rapports --}}
            <a href="{{ route('rh.rapports.index') }}" class="sb-item {{ request()->routeIs('rh.rapports.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-chart-bar"></i></span>
                <span class="sb-label">Rapports</span>
                <span class="sb-tooltip">Rapports</span>
            </a>

        @endhasrole

        {{-- ══════════════════════════════════════
             MENU DRH
             ══════════════════════════════════════ --}}
        @hasrole('DRH')

            <div class="sb-section-title">Menu</div>

            <a href="{{ route('drh.dashboard') }}" class="sb-item {{ request()->routeIs('drh.dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="sb-label">Tableau de bord DRH</span>
                <span class="sb-tooltip">Tableau de bord DRH</span>
            </a>

            <div class="sb-section-title">Pilotage Stratégique</div>

            <a href="{{ route('drh.kpis') }}" class="sb-item {{ request()->routeIs('drh.kpis') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="sb-label">KPIs Globaux</span>
                <span class="sb-tooltip">KPIs Globaux</span>
            </a>

            <a href="{{ route('drh.organigramme') }}" class="sb-item {{ request()->routeIs('drh.organigramme') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-project-diagram"></i></span>
                <span class="sb-label">Organigramme</span>
                <span class="sb-tooltip">Organigramme</span>
            </a>

            {{-- Indicateurs RH --}}
            <div x-data="{ open: {{ request()->routeIs('drh.indicateurs.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('drh.indicateurs.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-chart-area"></i></span>
                    <span class="sb-label">Indicateurs RH</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Indicateurs RH</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('drh.indicateurs.effectifs') }}" class="sb-subitem {{ request()->routeIs('drh.indicateurs.effectifs') ? 'active' : '' }}">Effectifs</a>
                    <a href="{{ route('drh.indicateurs.turnover') }}" class="sb-subitem {{ request()->routeIs('drh.indicateurs.turnover') ? 'active' : '' }}">Turnover</a>
                    <a href="{{ route('drh.indicateurs.absenteisme') }}" class="sb-subitem {{ request()->routeIs('drh.indicateurs.absenteisme') ? 'active' : '' }}">Absentéisme</a>
                    <a href="{{ route('drh.indicateurs.pyramide-ages') }}" class="sb-subitem {{ request()->routeIs('drh.indicateurs.pyramide-ages') ? 'active' : '' }}">Pyramide des âges</a>
                </div>
            </div>

            <div class="sb-section-title">Validations DRH</div>

            <a href="{{ route('drh.validations.decisions') }}" class="sb-item {{ request()->routeIs('drh.validations.decisions') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-stamp"></i></span>
                <span class="sb-label">Décisions à signer</span>
                @php
                    try { $decisionsEnAttente = \App\Models\Mouvement::where('statut', 'en_attente')->count(); }
                    catch (\Exception $e) { $decisionsEnAttente = 0; }
                @endphp
                @if($decisionsEnAttente > 0)
                    <span class="sb-badge">{{ $decisionsEnAttente }}</span>
                @endif
                <span class="sb-tooltip">Décisions à signer</span>
            </a>

            <a href="{{ route('drh.validations.mouvements') }}" class="sb-item {{ request()->routeIs('drh.validations.mouvements') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-exchange-alt"></i></span>
                <span class="sb-label">Mouvements stratégiques</span>
                <span class="sb-tooltip">Mouvements stratégiques</span>
            </a>

            <a href="{{ route('drh.validations.pec') }}" class="sb-item {{ request()->routeIs('drh.validations.pec') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-hospital-user"></i></span>
                <span class="sb-label">PEC exceptionnelles</span>
                <span class="sb-tooltip">PEC exceptionnelles</span>
            </a>

            <div class="sb-section-title">Gestion du Personnel</div>

            {{-- Personnel (DRH) --}}
            <div x-data="{ open: {{ request()->routeIs('rh.agents.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.agents.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-users"></i></span>
                    <span class="sb-label">Personnel</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Personnel</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.agents.index') }}" class="sb-subitem {{ request()->routeIs('rh.agents.index') ? 'active' : '' }}">Liste des agents</a>
                    <!-- <a href="{{ route('rh.agents.index', ['view' => 'organigramme']) }}" class="sb-subitem {{ request()->is('*organigramme*') ? 'active' : '' }}">Organigramme</a> -->
                    @php
                        try { $nbComptesAttenteDrh = \App\Models\User::where('agent_completed', false)->count(); } catch (\Exception $e) { $nbComptesAttenteDrh = 0; }
                    @endphp
                    <a href="{{ route('rh.agents.comptes-a-completer') }}" class="sb-subitem {{ request()->routeIs('rh.agents.comptes-a-completer') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                        Comptes à compléter
                        @if($nbComptesAttenteDrh > 0)
                            <span class="sb-badge" style="margin-left:auto;position:static;">{{ $nbComptesAttenteDrh }}</span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Contrats (DRH) --}}
            <a href="{{ route('rh.contrats.index') }}" class="sb-item {{ request()->routeIs('rh.contrats.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-file-contract"></i></span>
                <span class="sb-label">Contrats</span>
                <span class="sb-tooltip">Contrats</span>
            </a>

            {{-- Mouvements (DRH) --}}
            <a href="{{ route('rh.mouvements.index') }}" class="sb-item {{ request()->routeIs('rh.mouvements.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-people-arrows"></i></span>
                <span class="sb-label">Mouvements</span>
                <span class="sb-tooltip">Mouvements</span>
            </a>

            <div class="sb-section-title">Congés & Absences</div>

            <div x-data="{ open: {{ request()->routeIs('rh.conges.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.conges.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-umbrella-beach"></i></span>
                    <span class="sb-label">Congés</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Congés</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.conges.pending') }}" class="sb-subitem">Demandes en attente</a>
                    <a href="{{ route('rh.conges.index') }}" class="sb-subitem">Historique</a>
                </div>
            </div>

            <a href="{{ route('rh.absences.index') }}" class="sb-item {{ request()->routeIs('rh.absences.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-clock"></i></span>
                <span class="sb-label">Absences</span>
                <span class="sb-tooltip">Absences</span>
            </a>

            <div class="sb-section-title">Demandes à Traiter</div>

            <div x-data="{ open: {{ request()->routeIs('rh.demandes-docs.*') || request()->routeIs('documents-admin.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.demandes-docs.*') || request()->routeIs('documents-admin.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="sb-label">Administratif</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Administratif</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.demandes-docs.pending') }}" class="sb-subitem">Demandes en attente</a>
                    <a href="{{ route('documents-admin.index') }}" class="sb-subitem">Générer document</a>
                    <a href="{{ route('rh.demandes-docs.index') }}" class="sb-subitem">Historique</a>
                </div>
            </div>

            <a href="{{ route('pec.index') }}" class="sb-item {{ request()->routeIs('pec.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-heartbeat"></i></span>
                <span class="sb-label">Prises en charge</span>
                <span class="sb-tooltip">Prises en charge</span>
            </a>

            <div class="sb-section-title">Planification</div>

            <div x-data="{ open: {{ request()->routeIs('rh.plannings.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.plannings.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="sb-label">Plannings</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Plannings</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.plannings.pending') }}" class="sb-subitem">À valider</a>
                    <a href="{{ route('rh.plannings.index') }}" class="sb-subitem">Tous les plannings</a>
                </div>
            </div>

            <div class="sb-section-title">GED & Archives</div>

            <div x-data="{ open: {{ request()->routeIs('rh.ged.*') || request()->routeIs('rh.documents.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('rh.ged.*') || request()->routeIs('rh.documents.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-folder-open"></i></span>
                    <span class="sb-label">GED</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">GED Archives</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('rh.ged.index') }}" class="sb-subitem {{ request()->routeIs('rh.ged.index') ? 'active' : '' }}">
                        <i class="ri-dashboard-line me-1"></i> Tableau de bord
                    </a>
                    <a href="{{ route('rh.ged.dossiers') }}" class="sb-subitem {{ request()->routeIs('rh.ged.dossiers') || request()->routeIs('rh.ged.dossier.*') ? 'active' : '' }}">
                        <i class="ri-folder-3-line me-1"></i> Dossiers agents
                    </a>
                    <a href="{{ route('rh.ged.etageres') }}" class="sb-subitem {{ request()->routeIs('rh.ged.etageres') ? 'active' : '' }}">
                        <i class="ri-archive-drawer-line me-1"></i> Étagères
                    </a>
                </div>
            </div>

            <div class="sb-section-title">Organisation</div>

            <a href="{{ route('rh.services.index') }}" class="sb-item {{ request()->routeIs('rh.services.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-sitemap"></i></span>
                <span class="sb-label">Services</span>
                <span class="sb-tooltip">Services</span>
            </a>

            <a href="{{ route('rh.divisions.index') }}" class="sb-item {{ request()->routeIs('rh.divisions.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-building"></i></span>
                <span class="sb-label">Divisions</span>
                <span class="sb-tooltip">Divisions</span>
            </a>

            <div class="sb-section-title">Rapports & Bilans</div>

            {{-- Rapports Direction --}}
            <div x-data="{ open: {{ request()->routeIs('drh.rapports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('drh.rapports.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-chart-bar"></i></span>
                    <span class="sb-label">Rapports direction</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Rapports direction</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('drh.rapports.bilan') }}" class="sb-subitem {{ request()->routeIs('drh.rapports.bilan') ? 'active' : '' }}">Bilan social</a>
                    <a href="{{ route('drh.rapports.effectifs') }}" class="sb-subitem {{ request()->routeIs('drh.rapports.effectifs') ? 'active' : '' }}">Rapport effectifs</a>
                    <a href="{{ route('drh.rapports.previsions') }}" class="sb-subitem {{ request()->routeIs('drh.rapports.previsions') ? 'active' : '' }}">Prévisions départs</a>
                </div>
            </div>

            {{-- Exports Direction --}}
            <div x-data="{ open: {{ request()->routeIs('drh.exports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('drh.exports.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-file-export"></i></span>
                    <span class="sb-label">Exports direction</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Exports direction</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('drh.rapports.export') }}" class="sb-subitem {{ request()->routeIs('drh.rapports.export') ? 'active' : '' }}">Export consolidé</a>
                    <a href="{{ route('drh.validations.decisions') }}" class="sb-subitem {{ request()->routeIs('drh.validations.decisions') ? 'active' : '' }}">Historique décisions</a>
                </div>
            </div>

        @endhasrole

        {{-- ══════════════════════════════════════
             MENU MANAGER
             ══════════════════════════════════════ --}}
        @hasrole('Manager')

            <div class="sb-section-title">Menu</div>

            <a href="{{ route('manager.dashboard') }}" class="sb-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="sb-label">Tableau de bord</span>
                <span class="sb-tooltip">Tableau de bord</span>
            </a>

            <div class="sb-section-title">Mon Équipe</div>

            <a href="{{ route('manager.equipe') }}" class="sb-item {{ request()->routeIs('manager.equipe') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-friends"></i></span>
                <span class="sb-label">Liste de l'équipe</span>
                <span class="sb-tooltip">Mon équipe</span>
            </a>

            <a href="{{ route('manager.equipe.dossiers') }}" class="sb-item {{ request()->routeIs('manager.equipe.dossiers') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-folder-open"></i></span>
                <span class="sb-label">Dossiers agents</span>
                <span class="sb-tooltip">Dossiers agents</span>
            </a>

            <div class="sb-section-title">Validations</div>

            <a href="{{ route('manager.conges.pending') }}" class="sb-item {{ request()->routeIs('manager.conges.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-clipboard-check"></i></span>
                <span class="sb-label">Congés à valider</span>
                @php
                    try {
                        $managerPendingLeaves = \App\Models\Demande::where('type_demande', 'Conge')
                            ->where('statut_demande', 'En_attente')->count();
                    } catch (\Exception $e) { $managerPendingLeaves = 0; }
                @endphp
                @if($managerPendingLeaves > 0)
                    <span class="sb-badge">{{ $managerPendingLeaves }}</span>
                @endif
                <span class="sb-tooltip">Congés à valider</span>
            </a>

            {{-- Absences Équipe --}}
            <a href="{{ route('manager.absences.index') }}" class="sb-item {{ request()->routeIs('manager.absences.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-minus"></i></span>
                <span class="sb-label">Absences équipe</span>
                <span class="sb-tooltip">Absences équipe</span>
            </a>

            <div class="sb-section-title">Planification</div>

            {{-- Plannings Manager --}}
            <div x-data="{ open: {{ request()->routeIs('manager.planning.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('manager.planning.*') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-calendar-week"></i></span>
                    <span class="sb-label">Plannings</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Plannings</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('manager.planning.index') }}" class="sb-subitem {{ request()->routeIs('manager.planning.*') ? 'active' : '' }}">Mes plannings</a>
                </div>
            </div>

        @endhasrole

        {{-- ══════════════════════════════════════
             MENU AGENT (Employé)
             ══════════════════════════════════════ --}}
        @hasrole('Agent')

            <div class="sb-section-title">Menu</div>

            <a href="{{ route('agent.dashboard') }}" class="sb-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-home"></i></span>
                <span class="sb-label">Mon espace</span>
                <span class="sb-tooltip">Mon espace</span>
            </a>

            <div class="sb-section-title">Mon Dossier</div>

            <div x-data="{ open: {{ request()->routeIs('agent.profil') || request()->routeIs('agent.famille') || request()->routeIs('agent.mon-contrat') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('agent.profil') || request()->routeIs('agent.famille') || request()->routeIs('agent.mon-contrat') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-id-card"></i></span>
                    <span class="sb-label">Mon dossier</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Mon dossier</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('agent.profil') }}" class="sb-subitem {{ request()->routeIs('agent.profil') ? 'active' : '' }}">Informations personnelles</a>
                    <a href="{{ route('agent.famille') }}" class="sb-subitem {{ request()->routeIs('agent.famille') ? 'active' : '' }}">Ma famille</a>
                    <a href="{{ route('agent.mon-contrat') }}" class="sb-subitem {{ request()->routeIs('agent.mon-contrat') ? 'active' : '' }}">Mon contrat</a>
                </div>
            </div>

            <div class="sb-section-title">Mes Demandes</div>

            {{-- Documents Administratifs --}}
            <a href="{{ route('agent.docs.index') }}" class="sb-item {{ request()->routeIs('agent.docs.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-file-alt"></i></span>
                <span class="sb-label">Administratif</span>
                <span class="sb-tooltip">Administratif</span>
            </a>

            {{-- Prises en charge --}}
            <a href="{{ route('agent.pec.index') }}" class="sb-item {{ request()->routeIs('agent.pec.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-heartbeat"></i></span>
                <span class="sb-label">Prises en charge</span>
                <span class="sb-tooltip">Prises en charge</span>
            </a>

            <div class="sb-section-title">Congés & Absences</div>

            <a href="{{ route('agent.conges.index') }}" class="sb-item {{ request()->routeIs('agent.conges.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-umbrella-beach"></i></span>
                <span class="sb-label">Mes congés</span>
                <span class="sb-tooltip">Mes congés</span>
            </a>

            <a href="{{ route('agent.absences.index') }}" class="sb-item {{ request()->routeIs('agent.absences.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-clock"></i></span>
                <span class="sb-label">Mes absences</span>
                <span class="sb-tooltip">Mes absences</span>
            </a>

            <div class="sb-section-title">Planning & Documents</div>

            <a href="{{ route('agent.planning') }}" class="sb-item {{ request()->routeIs('agent.planning') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-calendar-day"></i></span>
                <span class="sb-label">Mon planning</span>
                <span class="sb-tooltip">Mon planning</span>
            </a>

            <a href="{{ route('agent.documents.index') }}" class="sb-item {{ request()->routeIs('agent.documents.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-folder-open"> </i></span>
                <span class="sb-label">Mes documents</span>
                <span class="sb-tooltip">Mes documents</span>
            </a>

        @endhasrole

        {{-- ══════════════════════════════════════
             MON ESPACE PERSONNEL
             (Manager, AgentRH, DRH — aussi agents de l'hôpital)
             ══════════════════════════════════════ --}}
        @hasanyrole('Manager|AgentRH|DRH')

            <div class="sb-divider" style="margin-top:12px;"></div>
            <div class="sb-section-title" style="display:flex;align-items:center;gap:6px;">
                <i class="fas fa-user-circle" style="font-size:10px;color:#0A4D8C;"></i>
                Mon Espace Personnel
            </div>

            {{-- Tableau de bord personnel --}}
            <a href="{{ route('agent.dashboard') }}" class="sb-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-home"></i></span>
                <span class="sb-label">Mon tableau de bord</span>
                <span class="sb-tooltip">Mon tableau de bord</span>
            </a>

            {{-- Mon Dossier --}}
            <div x-data="{ open: {{ request()->routeIs('agent.profil') || request()->routeIs('agent.famille') || request()->routeIs('agent.mon-contrat') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sb-item w-100 border-0 text-start {{ request()->routeIs('agent.profil') || request()->routeIs('agent.famille') || request()->routeIs('agent.mon-contrat') ? 'active' : '' }}" style="background: transparent;">
                    <span class="sb-icon"><i class="fas fa-id-card"></i></span>
                    <span class="sb-label">Mon dossier</span>
                    <i class="fas fa-chevron-right sb-chevron" :class="{ 'sb-open': open }"></i>
                    <span class="sb-tooltip">Mon dossier</span>
                </button>
                <div class="sb-submenu" x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <a href="{{ route('agent.profil') }}" class="sb-subitem {{ request()->routeIs('agent.profil') ? 'active' : '' }}">Informations personnelles</a>
                    <a href="{{ route('agent.famille') }}" class="sb-subitem {{ request()->routeIs('agent.famille') ? 'active' : '' }}">Ma famille</a>
                    <a href="{{ route('agent.mon-contrat') }}" class="sb-subitem {{ request()->routeIs('agent.mon-contrat') ? 'active' : '' }}">Mon contrat</a>
                </div>
            </div>

            {{-- Mes Congés --}}
            <a href="{{ route('agent.conges.index') }}" class="sb-item {{ request()->routeIs('agent.conges.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-umbrella-beach"></i></span>
                <span class="sb-label">Mes congés</span>
                <span class="sb-tooltip">Mes congés</span>
            </a>

            {{-- Mes Absences --}}
            <a href="{{ route('agent.absences.index') }}" class="sb-item {{ request()->routeIs('agent.absences.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-user-clock"></i></span>
                <span class="sb-label">Mes absences</span>
                <span class="sb-tooltip">Mes absences</span>
            </a>

            {{-- Documents administratifs --}}
            <a href="{{ route('agent.docs.index') }}" class="sb-item {{ request()->routeIs('agent.docs.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-file-alt"></i></span>
                <span class="sb-label">Administratif.</span>
                <span class="sb-tooltip">Administratif</span>
            </a>

            {{-- Prises en charge --}}
            <a href="{{ route('agent.pec.index') }}" class="sb-item {{ request()->routeIs('agent.pec.*') ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-heartbeat"></i></span>
                <span class="sb-label">Prises en charge</span>
                <span class="sb-tooltip">Prises en charge</span>
            </a>

        @endhasanyrole

        {{-- ══════════════════════════════════════
             SECTION COMMUNE (tous les rôles)
             ══════════════════════════════════════ --}}
        <div class="sb-divider" style="margin-top: 12px;"></div>

        @php
            try { $unreadNotifications = auth()->user()->unreadNotifications()->count(); }
            catch (\Exception $e) { $unreadNotifications = 0; }
        @endphp

        <a href="{{ route('notifications.index') }}" class="sb-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-bell"></i></span>
            <span class="sb-label">Notifications</span>
            @if($unreadNotifications > 0)
                <span class="sb-badge">{{ $unreadNotifications }}</span>
            @endif
            <span class="sb-tooltip">Notifications</span>
        </a>

        <a href="{{ route('aide.index') }}" class="sb-item {{ request()->routeIs('aide.*') ? 'active' : '' }}">
            <span class="sb-icon"><i class="fas fa-life-ring"></i></span>
            <span class="sb-label">Aide</span>
            <span class="sb-tooltip">Aide</span>
        </a>

        {{-- ── Badge TRIADE CID ──────────────────────────────────── --}}
        <div style="padding: 8px 6px 4px;">
            <div class="sb-cid-badge">
                <i class="fas fa-shield-alt" style="color: #1565C0; font-size: 15px; flex-shrink: 0;"></i>
                <div class="sb-cid-text">
                    <div style="font-size: 11px; font-weight: 700; color: #0A4D8C; white-space: nowrap; line-height: 1.2;">TRIADE CID</div>
                    <div style="font-size: 10px; color: #6B7280; white-space: nowrap; letter-spacing: 0.03em;">Confidentialité · Intégrité · Disponibilité</div>
                </div>
            </div>
        </div>

    </nav>
</aside>

{{-- Overlay mobile --}}
<div
    x-show="sidebarOpen"
    x-cloak
    @click="sidebarOpen = false"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1029; backdrop-filter: blur(2px);"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
></div>
