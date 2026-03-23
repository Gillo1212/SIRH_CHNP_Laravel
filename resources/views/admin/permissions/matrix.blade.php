@extends('layouts.master')

@section('title', 'Matrice Permissions')
@section('page-title', 'Matrice Rôles × Permissions')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.roles.index') }}" style="color:#1565C0;">Rôles</a></li>
    <li>Matrice Permissions</li>
@endsection

@push('styles')
<style>
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }

/* Matrice */
.matrix-wrap { overflow-x:auto; }
.matrix-table { border-collapse:separate; border-spacing:0; width:100%; }
.matrix-table thead th {
    position:sticky; top:0; z-index:10;
    background:#fff; border-bottom:2px solid #E5E7EB;
    padding:10px 8px; white-space:nowrap; text-align:center;
    min-width:90px; font-size:12px;
}
.matrix-table thead th.perm-col {
    text-align:left; min-width:240px; position:sticky; left:0;
    background:#fff; z-index:20; padding-left:16px;
}
.matrix-table tbody tr:hover td { background:#FAFBFF; }
.matrix-table td {
    padding:6px 8px; border-bottom:1px solid #F3F4F6;
    font-size:12.5px; vertical-align:middle; text-align:center;
}
.matrix-table td.perm-col {
    text-align:left; position:sticky; left:0;
    background:#fff; z-index:5; padding-left:16px;
    font-family:monospace; color:#374151; white-space:nowrap;
}
.matrix-table td.perm-col:hover { background:#F8FAFC; }

/* Ligne module */
.module-row td {
    background:#F1F5F9; font-weight:700; font-size:12px;
    color:#0A4D8C; text-transform:uppercase; letter-spacing:.05em;
    padding:7px 16px; border:none; text-align:left;
    position:sticky; left:0; z-index:8;
}

/* Toggle switch compact */
.toggle-switch {
    width: 36px; height: 20px;
    background: #E5E7EB; border-radius: 20px;
    position: relative; cursor: pointer;
    transition: background 200ms;
    border: none; outline: none; flex-shrink:0;
    display: inline-block;
}
.toggle-switch::after {
    content: '';
    position: absolute;
    width: 14px; height: 14px;
    background: #fff; border-radius: 50%;
    top: 3px; left: 3px;
    transition: left 200ms;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch.on {
    background: #10B981;
}
.toggle-switch.on::after { left: 19px; }
.toggle-switch.disabled { cursor: not-allowed; opacity: .5; }
.toggle-switch.loading { background: #93C5FD; animation: pulse 1s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }

/* Entêtes rôles */
.role-header { display:flex; flex-direction:column; align-items:center; gap:2px; }
.role-header .role-name { font-weight:700; font-size:12px; color:#1E293B; }
.role-header .role-count { font-size:10px; color:#9CA3AF; }

/* Toast */
#toast-container {
    position:fixed; bottom:24px; right:24px; z-index:9999;
    display:flex; flex-direction:column; gap:8px;
    max-width:320px;
}
.toast-msg {
    padding:10px 16px; border-radius:8px;
    font-size:13px; font-weight:500;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
    animation:slideIn .2s ease;
}
.toast-msg.success { background:#D1FAE5; color:#065F46; border-left:4px solid #10B981; }
.toast-msg.error   { background:#FEE2E2; color:#7F1D1D; border-left:4px solid #EF4444; }
@keyframes slideIn { from{transform:translateX(30px);opacity:0} to{transform:translateX(0);opacity:1} }

/* Filtre module */
.filter-btn {
    padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600;
    border:1px solid #E5E7EB; background:#fff; cursor:pointer; transition:all 150ms;
    color:#374151;
}
.filter-btn.active, .filter-btn:hover { background:#EFF6FF; border-color:#93C5FD; color:#1E40AF; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-start justify-content-between mb-4">
        <div>
            <h4 class="fw-700 mb-0" style="color:#111827;">
                <i class="fas fa-table me-2" style="color:#0A4D8C;"></i>Matrice Rôles × Permissions
            </h4>
            <div style="font-size:13px;color:#6B7280;margin-top:2px;">
                Gérez visuellement les permissions de chaque rôle. Modifications sauvegardées en temps réel.
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary fw-600">
                <i class="fas fa-key me-2"></i>Permissions
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary fw-600">
                <i class="fas fa-arrow-left me-2"></i>Rôles
            </a>
        </div>
    </div>

    {{-- Note protection admin --}}
    <div class="alert mb-4" style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;">
        <i class="fas fa-lock me-2" style="color:#D97706;"></i>
        <strong style="color:#92400E;">Rôle AdminSystème protégé :</strong>
        <span style="color:#92400E;font-size:13px;">
            Ce rôle conserve toutes les permissions et ne peut pas être modifié.
        </span>
    </div>

    {{-- Légende --}}
    <div class="d-flex align-items-center gap-4 mb-4" style="font-size:12.5px;color:#6B7280;">
        <div class="d-flex align-items-center gap-2">
            <div class="toggle-switch on" style="cursor:default;pointer-events:none;"></div>
            <span>Permission accordée</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="toggle-switch" style="cursor:default;pointer-events:none;"></div>
            <span>Permission non accordée</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-sync-alt" style="color:#3B82F6;"></i>
            <span>Sauvegarde automatique</span>
        </div>
    </div>

    {{-- Matrice --}}
    <div class="panel p-0" style="overflow:hidden;">
        <div class="matrix-wrap">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th class="perm-col">
                            <span style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;">Permission</span>
                        </th>
                        @foreach($roles as $role)
                            @php
                                $roleColors = [
                                    'Agent' => '#6B7280', 'Manager' => '#D97706',
                                    'AgentRH' => '#0891B2', 'DRH' => '#7C3AED',
                                    'AdminSystème' => '#DC2626',
                                ];
                                $roleColor = $roleColors[$role->name] ?? '#0A4D8C';
                            @endphp
                            <th>
                                <div class="role-header">
                                    <span class="role-name" style="color:{{ $roleColor }};">{{ $role->name }}</span>
                                    <span class="role-count">{{ $role->permissions->count() }} perm.</span>
                                    @if($role->name === 'AdminSystème')
                                        <i class="fas fa-crown" style="color:#DC2626;font-size:10px;"></i>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupIcons = [
                            'Personnel' => 'fa-users', 'Contrats' => 'fa-file-contract',
                            'Congés' => 'fa-umbrella-beach', 'Absences' => 'fa-calendar-times',
                            'Planning' => 'fa-calendar-alt', 'GED' => 'fa-folder',
                            'Reporting' => 'fa-chart-bar', 'DRH' => 'fa-user-tie',
                            'Administration' => 'fa-cog', 'Autres' => 'fa-ellipsis-h',
                        ];
                    @endphp

                    @foreach($groupedPermissions as $groupName => $permissions)
                        {{-- Ligne module --}}
                        <tr>
                            <td colspan="{{ count($roles) + 1 }}" class="module-row">
                                <i class="fas {{ $groupIcons[$groupName] ?? 'fa-folder' }} me-2"></i>{{ $groupName }}
                                <span style="font-weight:400;color:#64748B;margin-left:6px;">({{ count($permissions) }})</span>
                            </td>
                        </tr>

                        {{-- Permissions --}}
                        @foreach($permissions as $permission)
                            <tr>
                                <td class="perm-col">
                                    <i class="fas fa-key me-2" style="color:#CBD5E1;font-size:11px;"></i>
                                    {{ $permission->name }}
                                </td>
                                @foreach($roles as $role)
                                    @php $hasPermission = $role->hasPermissionTo($permission->name); @endphp
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button"
                                                    class="toggle-switch {{ $hasPermission ? 'on' : '' }} {{ $role->name === 'AdminSystème' ? 'disabled' : '' }}"
                                                    data-role-id="{{ $role->id }}"
                                                    data-permission-id="{{ $permission->id }}"
                                                    data-permission-name="{{ $permission->name }}"
                                                    data-role-name="{{ $role->name }}"
                                                    title="{{ $role->name }} : {{ $permission->name }}"
                                                    {{ $role->name === 'AdminSystème' ? 'disabled' : '' }}>
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 text-end" style="font-size:12px;color:#9CA3AF;">
        <i class="fas fa-clock me-1"></i>Dernière mise à jour : <span id="lastUpdate">–</span>
    </div>

</div>

{{-- Toast container --}}
<div id="toast-container"></div>
@endsection

@push('scripts')
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const MATRIX_URL = '{{ route("admin.permissions.matrix.update") }}';

document.querySelectorAll('.toggle-switch:not(.disabled)').forEach(btn => {
    btn.addEventListener('click', async function () {
        const isOn       = this.classList.contains('on');
        const roleId     = this.dataset.roleId;
        const permId     = this.dataset.permissionId;
        const permName   = this.dataset.permissionName;
        const roleName   = this.dataset.roleName;
        const action     = isOn ? 'remove' : 'assign';

        // Optimistic update
        this.classList.toggle('on');
        this.classList.add('loading');
        this.disabled = true;

        try {
            const resp = await fetch(MATRIX_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    role_id: roleId,
                    permission_id: permId,
                    action: action,
                }),
            });

            const data = await resp.json();

            if (!data.success) {
                // Revenir en arrière
                this.classList.toggle('on');
                showToast('error', data.message || 'Erreur lors de la mise à jour');
            } else {
                showToast('success', data.message);
                updateLastUpdate();
                // Mettre à jour le compteur dans l'en-tête
                updateRoleCount(roleId, isOn ? -1 : +1);
            }

        } catch (err) {
            this.classList.toggle('on');
            showToast('error', 'Erreur réseau. Veuillez réessayer.');
            console.error(err);
        } finally {
            this.classList.remove('loading');
            this.disabled = false;
        }
    });
});

function updateRoleCount(roleId, delta) {
    // Mettre à jour le compteur affiché dans l'en-tête (approximatif)
    document.querySelectorAll('thead th').forEach(th => {
        const btn = th.querySelector('[data-role-id="' + roleId + '"]');
        if (!btn) return;
        const countEl = th.querySelector('.role-count');
        if (countEl) {
            const match = countEl.textContent.match(/\d+/);
            if (match) {
                const newCount = parseInt(match[0]) + delta;
                countEl.textContent = Math.max(0, newCount) + ' perm.';
            }
        }
    });

    // Mettre à jour via les headers
    document.querySelectorAll('th .role-header').forEach(header => {
        // Simple recherche parmi les toggles de cette colonne
    });
}

function updateLastUpdate() {
    const now = new Date();
    document.getElementById('lastUpdate').textContent =
        now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function showToast(type, message) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast-msg ${type}`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>
@endpush
