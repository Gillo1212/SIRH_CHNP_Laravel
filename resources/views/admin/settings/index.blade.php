@extends('layouts.master')
@section('title', 'Paramètres système')
@section('page-title', 'Paramètres système')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li>Paramètres</li>
@endsection

@push('styles')
<style>
.settings-tab { cursor:pointer;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;color:#6B7280;transition:all .2s;border:none;background:none;text-align:left;width:100%; }
.settings-tab.active, .settings-tab:hover { background:#EFF6FF;color:#0A4D8C; }
.settings-tab.active { border-left:3px solid #0A4D8C;padding-left:15px; }
.settings-panel { display:none; }
.settings-panel.active { display:block; }
.form-group-label { font-size:12px;font-weight:600;color:#374151;margin-bottom:4px; }
.form-hint { font-size:11px;color:#9CA3AF;margin-top:3px; }
.toggle-switch { position:relative;display:inline-flex;align-items:center;gap:8px;cursor:pointer; }
.toggle-switch input[type=checkbox] { width:38px;height:20px;appearance:none;background:#D1D5DB;border-radius:20px;cursor:pointer;transition:.2s;flex-shrink:0; }
.toggle-switch input[type=checkbox]:checked { background:#0A4D8C; }
.toggle-switch input[type=checkbox]::after { content:'';position:absolute;width:16px;height:16px;background:#fff;border-radius:50%;top:2px;left:2px;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2); }
.toggle-switch input[type=checkbox]:checked::after { left:20px; }
.cid-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700; }
.cid-c { background:#D1FAE5;color:#065F46; }
.cid-i { background:#DBEAFE;color:#1E40AF; }
.cid-d { background:#FEF3C7;color:#92400E; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-cog me-2" style="color:#0A4D8C;"></i>Paramètres système
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Configuration globale du SIRH CHNP</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-exclamation-circle me-2"></i>
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- ── Colonne gauche : navigation + infos ─────── --}}
        <div class="col-12 col-lg-3">

            {{-- Navigation onglets --}}
            <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:12px;margin-bottom:16px;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;padding:4px 8px 8px;">Configuration</div>
                <button class="settings-tab active" onclick="showTab('app')">
                    <i class="fas fa-building me-2"></i>Application
                </button>
                <button class="settings-tab" onclick="showTab('security')">
                    <i class="fas fa-shield-alt me-2"></i>Sécurité
                    <span class="cid-badge cid-c ms-1">C</span>
                </button>
                <button class="settings-tab" onclick="showTab('notifications')">
                    <i class="fas fa-bell me-2"></i>Notifications
                </button>
                <button class="settings-tab" onclick="showTab('backup')">
                    <i class="fas fa-database me-2"></i>Sauvegardes
                    <span class="cid-badge cid-d ms-1">D</span>
                </button>
            </div>

            {{-- Infos système --}}
            <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:16px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:12px;">
                    <i class="fas fa-server me-1"></i> Système
                </div>
                @foreach([
                    'PHP'        => $info['php_version'],
                    'Laravel'    => $info['laravel_version'],
                    'Base de données' => $info['db_driver'],
                    'Env'        => $info['env'],
                    'Timezone'   => $info['timezone'],
                    'Locale'     => $info['locale'],
                ] as $label => $value)
                <div class="d-flex justify-content-between py-1" style="border-bottom:1px solid #F3F4F6;">
                    <span style="font-size:11px;color:#6B7280;">{{ $label }}</span>
                    <span style="font-size:11px;font-weight:600;color:var(--theme-text);">{{ $value }}</span>
                </div>
                @endforeach
            </div>

            {{-- Stats données --}}
            <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:12px;">
                    <i class="fas fa-chart-bar me-1"></i> Données
                </div>
                @foreach([
                    'Agents'         => $info['agents_total'],
                    'Utilisateurs'   => $info['users_total'],
                    'Logs'           => $info['storage_logs'],
                    'Uploads'        => $info['storage_uploads'],
                    'Sauvegardes'    => $info['storage_backups'],
                ] as $label => $value)
                <div class="d-flex justify-content-between py-1" style="border-bottom:1px solid #F3F4F6;">
                    <span style="font-size:11px;color:#6B7280;">{{ $label }}</span>
                    <span style="font-size:11px;font-weight:600;color:var(--theme-text);">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Colonne droite : formulaires ─────────────── --}}
        <div class="col-12 col-lg-9">

            {{-- ── ONGLET APP ──────────────────────────── --}}
            <div id="tab-app" class="settings-panel active">
                <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:24px;">
                    <div style="font-weight:700;font-size:15px;color:var(--theme-text);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--theme-border);">
                        <i class="fas fa-building me-2" style="color:#0A4D8C;"></i>Paramètres de l'application
                    </div>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <input type="hidden" name="group" value="app">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-group-label">Nom du système</label>
                                <input type="text" name="nom" class="form-control form-control-sm"
                                       value="{{ \App\Models\Setting::get('app.nom', 'SIRH CHNP') }}">
                                <div class="form-hint">Affiché dans les en-têtes et documents</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-group-label">Nom de l'hôpital</label>
                                <input type="text" name="nom_hopital" class="form-control form-control-sm"
                                       value="{{ \App\Models\Setting::get('app.nom_hopital', 'Centre Hospitalier National de Pikine') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-group-label">Email de contact</label>
                                <input type="email" name="email_contact" class="form-control form-control-sm"
                                       value="{{ \App\Models\Setting::get('app.email_contact', '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-group-label">Fuseau horaire</label>
                                <input type="text" name="timezone" class="form-control form-control-sm"
                                       value="{{ \App\Models\Setting::get('app.timezone', 'Africa/Dakar') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-group-label">Langue</label>
                                @php $locale = \App\Models\Setting::get('app.locale', 'fr'); @endphp
                                <select name="locale" class="form-select form-select-sm">
                                    <option value="fr" @selected($locale === 'fr')>Français</option>
                                    <option value="en" @selected($locale === 'en')>English</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end" style="border-color:var(--theme-border)!important;">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── ONGLET SÉCURITÉ ──────────────────────── --}}
            <div id="tab-security" class="settings-panel">
                <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:24px;">
                    <div style="font-weight:700;font-size:15px;color:var(--theme-text);margin-bottom:6px;padding-bottom:12px;border-bottom:1px solid var(--theme-border);">
                        <i class="fas fa-shield-alt me-2" style="color:#059669;"></i>Paramètres de sécurité
                        <span class="cid-badge cid-c ms-2">Confidentialité CID</span>
                    </div>
                    <p class="text-muted mb-4" style="font-size:12px;">
                        Ces paramètres implémentent le pilier <strong>Confidentialité</strong> de la Triade CID.
                    </p>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <input type="hidden" name="group" value="security">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-group-label">Durée session (minutes)</label>
                                <input type="number" name="session_lifetime" class="form-control form-control-sm"
                                       min="5" max="1440"
                                       value="{{ \App\Models\Setting::get('security.session_lifetime', 120) }}">
                                <div class="form-hint">Durée avant déconnexion automatique (5–1440)</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-group-label">Tentatives max connexion</label>
                                <input type="number" name="max_login_attempts" class="form-control form-control-sm"
                                       min="1" max="20"
                                       value="{{ \App\Models\Setting::get('security.max_login_attempts', 5) }}">
                                <div class="form-hint">Avant verrouillage compte (1–20)</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-group-label">Durée verrouillage (min)</label>
                                <input type="number" name="lockout_duration" class="form-control form-control-sm"
                                       min="5" max="1440"
                                       value="{{ \App\Models\Setting::get('security.lockout_duration', 30) }}">
                                <div class="form-hint">Durée verrouillage après échecs (5–1440)</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-group-label">Longueur min. mot de passe</label>
                                <input type="number" name="password_min_length" class="form-control form-control-sm"
                                       min="6" max="32"
                                       value="{{ \App\Models\Setting::get('security.password_min_length', 8) }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div>
                                    <label class="form-group-label d-block mb-2">Majuscule obligatoire</label>
                                    <input type="hidden" name="password_requires_uppercase" value="0">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="password_requires_uppercase" value="1"
                                               @checked(\App\Models\Setting::get('security.password_requires_uppercase', true))>
                                        <span style="font-size:12px;color:var(--theme-text);">Activé</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div>
                                    <label class="form-group-label d-block mb-2">Chiffre obligatoire</label>
                                    <input type="hidden" name="password_requires_number" value="0">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="password_requires_number" value="1"
                                               @checked(\App\Models\Setting::get('security.password_requires_number', true))>
                                        <span style="font-size:12px;color:var(--theme-text);">Activé</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div>
                                    <label class="form-group-label d-block mb-2">Double authentification (2FA)</label>
                                    <input type="hidden" name="two_factor_enabled" value="0">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="two_factor_enabled" value="1"
                                               @checked(\App\Models\Setting::get('security.two_factor_enabled', false))>
                                        <span style="font-size:12px;color:var(--theme-text);">Activé</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end" style="border-color:var(--theme-border)!important;">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── ONGLET NOTIFICATIONS ─────────────────── --}}
            <div id="tab-notifications" class="settings-panel">
                <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:24px;">
                    <div style="font-weight:700;font-size:15px;color:var(--theme-text);margin-bottom:6px;padding-bottom:12px;border-bottom:1px solid var(--theme-border);">
                        <i class="fas fa-bell me-2" style="color:#7C3AED;"></i>Paramètres de notifications
                    </div>
                    <p class="text-muted mb-4" style="font-size:12px;">
                        Activer ou désactiver les notifications automatiques par événement.
                    </p>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <input type="hidden" name="group" value="notifications">
                        @php
                        $notifLabels = [
                            'conge_demande'      => ['label' => 'Nouvelle demande de congé',     'icon' => 'fas fa-umbrella-beach', 'color' => '#0A4D8C'],
                            'conge_valide'       => ['label' => 'Congé approuvé (agent)',         'icon' => 'fas fa-check-circle',  'color' => '#059669'],
                            'conge_rejete'       => ['label' => 'Congé rejeté (agent)',           'icon' => 'fas fa-times-circle',  'color' => '#DC2626'],
                            'contrat_expiration' => ['label' => 'Alerte expiration contrat',      'icon' => 'fas fa-file-contract', 'color' => '#D97706'],
                            'document_pret'      => ['label' => 'Document administratif prêt',   'icon' => 'fas fa-file-alt',      'color' => '#7C3AED'],
                            'pec_traitement'     => ['label' => 'Prise en charge traitée',        'icon' => 'fas fa-heartbeat',     'color' => '#EC4899'],
                            'mouvement_valide'   => ['label' => 'Mouvement RH validé',            'icon' => 'fas fa-people-arrows', 'color' => '#0891B2'],
                        ];
                        @endphp
                        <div class="row g-3">
                            @foreach($notifLabels as $key => $meta)
                            <div class="col-md-6">
                                <div style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:32px;height:32px;border-radius:8px;background:{{ $meta['color'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="{{ $meta['icon'] }}" style="color:{{ $meta['color'] }};font-size:12px;"></i>
                                        </div>
                                        <span style="font-size:12px;font-weight:500;color:var(--theme-text);">{{ $meta['label'] }}</span>
                                    </div>
                                    <div>
                                        <input type="hidden" name="{{ $key }}" value="0">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="{{ $key }}" value="1"
                                                   @checked(\App\Models\Setting::get('notifications.' . $key, true))>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end" style="border-color:var(--theme-border)!important;">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── ONGLET BACKUP ────────────────────────── --}}
            <div id="tab-backup" class="settings-panel">
                <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:24px;">
                    <div style="font-weight:700;font-size:15px;color:var(--theme-text);margin-bottom:6px;padding-bottom:12px;border-bottom:1px solid var(--theme-border);">
                        <i class="fas fa-database me-2" style="color:#D97706;"></i>Paramètres de sauvegardes
                        <span class="cid-badge cid-d ms-2">Disponibilité CID</span>
                    </div>
                    <p class="text-muted mb-4" style="font-size:12px;">
                        Ces paramètres implémentent le pilier <strong>Disponibilité</strong> de la Triade CID.
                        Un backup quotidien garantit la continuité du service.
                    </p>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <input type="hidden" name="group" value="backup">
                        <div class="row g-4">
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div>
                                    <label class="form-group-label d-block mb-2">Sauvegarde automatique</label>
                                    <input type="hidden" name="auto_enabled" value="0">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="auto_enabled" value="1"
                                               @checked(\App\Models\Setting::get('backup.auto_enabled', true))>
                                        <span style="font-size:12px;color:var(--theme-text);">Activée</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-group-label">Fréquence</label>
                                @php $freq = \App\Models\Setting::get('backup.frequency', 'daily'); @endphp
                                <select name="frequency" class="form-select form-select-sm">
                                    <option value="daily"   @selected($freq === 'daily')>Quotidienne</option>
                                    <option value="weekly"  @selected($freq === 'weekly')>Hebdomadaire</option>
                                    <option value="monthly" @selected($freq === 'monthly')>Mensuelle</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-group-label">Heure d'exécution</label>
                                <input type="time" name="time" class="form-control form-control-sm"
                                       value="{{ \App\Models\Setting::get('backup.time', '02:00') }}">
                                <div class="form-hint">Heure de déclenchement automatique</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-group-label">Rétention (jours)</label>
                                <input type="number" name="retention_days" class="form-control form-control-sm"
                                       min="1" max="365"
                                       value="{{ \App\Models\Setting::get('backup.retention_days', 30) }}">
                                <div class="form-hint">Nombre de jours de conservation (1–365)</div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between" style="border-color:var(--theme-border)!important;">
                            <a href="{{ route('admin.backups.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-database me-1"></i>Gérer les sauvegardes
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Triade CID ──────────────────────────── --}}
            <div class="mt-4" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:20px;">
                <div style="font-weight:700;font-size:13px;color:var(--theme-text);margin-bottom:16px;">
                    <i class="fas fa-shield-alt me-2" style="color:#059669;"></i>État de la Triade CID
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:10px;padding:14px;">
                            <div style="font-weight:700;color:#065F46;margin-bottom:8px;font-size:12px;">
                                <i class="fas fa-lock me-1"></i> C — Confidentialité
                            </div>
                            <ul style="font-size:11px;color:#065F46;margin:0;padding-left:14px;line-height:1.8;">
                                <li>Chiffrement AES-256 actif</li>
                                <li>RBAC 5 rôles configuré</li>
                                <li>Policies Laravel actives</li>
                                <li>Sessions sécurisées</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px;">
                            <div style="font-weight:700;color:#1E40AF;margin-bottom:8px;font-size:12px;">
                                <i class="fas fa-check-double me-1"></i> I — Intégrité
                            </div>
                            <ul style="font-size:11px;color:#1E40AF;margin:0;padding-left:14px;line-height:1.8;">
                                <li>Audit log immuable (Spatie)</li>
                                <li>Transactions DB actives</li>
                                <li>Validation stricte (FormRequest)</li>
                                <li>Contraintes FK en base</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px;">
                            <div style="font-weight:700;color:#92400E;margin-bottom:8px;font-size:12px;">
                                <i class="fas fa-cloud me-1"></i> D — Disponibilité
                            </div>
                            <ul style="font-size:11px;color:#92400E;margin:0;padding-left:14px;line-height:1.8;">
                                <li>Sauvegardes automatiques</li>
                                <li>Cache & Eager loading</li>
                                <li>Index DB optimisés</li>
                                <li>Tests robustes (Feature + Unit)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function showTab(name) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    event.currentTarget.classList.add('active');
}

// Activer l'onglet selon le fragment d'URL ou après soumission
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('tab-' + hash)) {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + hash).classList.add('active');
        document.querySelectorAll('.settings-tab').forEach(t => {
            if (t.getAttribute('onclick').includes("'" + hash + "'")) t.classList.add('active');
        });
    }
});
</script>
@endpush
