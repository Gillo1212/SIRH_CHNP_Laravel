@extends('layouts.master')

@section('title', 'Modifier le Rôle')
@section('page-title', 'Modifier les Permissions')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.roles.index') }}" style="color:#1565C0;">Rôles</a></li>
    <li>Modifier – {{ $displayName }}</li>
@endsection

@push('styles')
<style>
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }
.perm-group { border:1px solid #E5E7EB; border-radius:10px; overflow:hidden; margin-bottom:12px; }
.perm-group-header {
    background:#F8FAFC; padding:10px 16px;
    display:flex; align-items:center; gap:10px; cursor:pointer;
    border-bottom:1px solid #E5E7EB; user-select:none;
}
.perm-group-header:hover { background:#F1F5F9; }
.perm-group-header .group-name { font-weight:600; font-size:13.5px; color:#1E293B; flex:1; }
.perm-group-header .badge-count {
    font-size:11px; font-weight:600; padding:2px 8px;
    border-radius:20px; background:#DBEAFE; color:#1E40AF;
}
.perm-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:8px; padding:14px 16px; }
.perm-item {
    display:flex; align-items:center; gap:8px;
    padding:8px 10px; border-radius:8px; border:1px solid #E5E7EB;
    background:#FAFAFA; cursor:pointer; transition:all 150ms;
}
.perm-item:hover { border-color:#93C5FD; background:#EFF6FF; }
.perm-item.checked { border-color:#3B82F6; background:#EFF6FF; }
.perm-item input[type=checkbox] { flex-shrink:0; cursor:pointer; }
.perm-item label { font-size:12.5px; color:#374151; cursor:pointer; margin:0; font-family:monospace; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">

            {{-- En-tête --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-700 mb-0" style="color:#111827;">
                        <i class="fas fa-edit me-2" style="color:#0A4D8C;"></i>Modifier les permissions
                    </h4>
                    <div style="font-size:13px;color:#6B7280;margin-top:2px;">
                        Rôle : <strong>{{ $displayName }}</strong>
                        <code style="background:#F3F4F6;padding:1px 6px;border-radius:4px;font-size:11px;margin-left:4px;">{{ $role->name }}</code>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-outline-secondary fw-600">
                        <i class="fas fa-eye me-2"></i>Voir le rôle
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary fw-600">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>

            {{-- Alertes --}}
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf @method('PUT')

                {{-- Info rôle --}}
                <div class="panel mb-4" style="background:#F8FAFF;border-color:#DBEAFE;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-info-circle" style="color:#1E40AF;"></i>
                        </div>
                        <div style="font-size:13px;color:#1E40AF;">
                            Vous modifiez les permissions du rôle <strong>{{ $displayName }}</strong>.
                            Les changements s'appliqueront immédiatement à tous les utilisateurs ayant ce rôle.
                        </div>
                    </div>
                </div>

                {{-- Permissions --}}
                <div class="panel mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-700 mb-0" style="color:#374151;">
                            <i class="fas fa-key me-2 text-primary"></i>Permissions
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-600" id="selectAll">
                                <i class="fas fa-check-double me-1"></i>Tout sélectionner
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary fw-600" id="deselectAll">
                                <i class="fas fa-times me-1"></i>Tout désélectionner
                            </button>
                        </div>
                    </div>

                    <div id="selectedCount" class="mb-3" style="font-size:13px;color:#6B7280;">
                        <i class="fas fa-info-circle me-1"></i>
                        <span id="countValue">0</span> permission(s) sélectionnée(s)
                    </div>

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
                        @php
                            $groupChecked = collect($permissions)->whereIn('id', $rolePermissionIds)->count();
                        @endphp
                        <div class="perm-group">
                            <div class="perm-group-header" onclick="toggleGroup('{{ Str::slug($groupName) }}')">
                                <div class="form-check mb-0">
                                    <input type="checkbox"
                                           class="form-check-input module-checkbox"
                                           id="module_{{ Str::slug($groupName) }}"
                                           data-module="{{ Str::slug($groupName) }}"
                                           onclick="event.stopPropagation()">
                                </div>
                                <i class="fas {{ $groupIcons[$groupName] ?? 'fa-folder' }}" style="color:#0A4D8C;width:16px;"></i>
                                <span class="group-name">{{ $groupName }}</span>
                                <span class="badge-count" id="badge-{{ Str::slug($groupName) }}">{{ $groupChecked }}/{{ count($permissions) }}</span>
                                <i class="fas fa-chevron-down toggle-icon" id="icon-{{ Str::slug($groupName) }}" style="color:#6B7280;font-size:11px;transition:transform 200ms;"></i>
                            </div>
                            <div class="perm-grid" id="group-{{ Str::slug($groupName) }}">
                                @foreach($permissions as $permission)
                                    @php $isChecked = in_array($permission->id, $rolePermissionIds); @endphp
                                    <div class="perm-item {{ $isChecked ? 'checked' : '' }}">
                                        <input type="checkbox"
                                               class="form-check-input permission-checkbox"
                                               id="perm_{{ $permission->id }}"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               data-module="{{ Str::slug($groupName) }}"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <label for="perm_{{ $permission->id }}" class="flex-grow-1">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Boutons --}}
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary fw-600 px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                    <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-outline-secondary fw-600">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleGroup(slug) {
    const grid = document.getElementById('group-' + slug);
    const icon = document.getElementById('icon-' + slug);
    if (grid.style.display === 'none') {
        grid.style.display = '';
        icon.style.transform = 'rotate(0deg)';
    } else {
        grid.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}

document.querySelectorAll('.module-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function (e) {
        e.stopPropagation();
        const module = this.dataset.module;
        document.querySelectorAll(`[data-module="${module}"].permission-checkbox`).forEach(cb => {
            cb.checked = this.checked;
            cb.closest('.perm-item').classList.toggle('checked', this.checked);
        });
        updateBadge(module);
        updateCount();
    });
});

document.querySelectorAll('.permission-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        this.closest('.perm-item').classList.toggle('checked', this.checked);
        updateBadge(this.dataset.module);
        syncModuleCheckbox(this.dataset.module);
        updateCount();
    });
});

function updateBadge(module) {
    const all     = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
    const checked = document.querySelectorAll(`[data-module="${module}"].permission-checkbox:checked`);
    const badge   = document.getElementById('badge-' + module);
    if (badge) badge.textContent = `${checked.length}/${all.length}`;
}

function syncModuleCheckbox(module) {
    const all     = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
    const checked = document.querySelectorAll(`[data-module="${module}"].permission-checkbox:checked`);
    const moduleCheckbox = document.querySelector(`#module_${module}`);
    if (moduleCheckbox) {
        moduleCheckbox.checked       = all.length > 0 && all.length === checked.length;
        moduleCheckbox.indeterminate = checked.length > 0 && checked.length < all.length;
    }
}

function updateCount() {
    const total = document.querySelectorAll('.permission-checkbox:checked').length;
    document.getElementById('countValue').textContent = total;
}

document.getElementById('selectAll').addEventListener('click', function () {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = true;
        cb.closest('.perm-item').classList.add('checked');
    });
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = true);
    document.querySelectorAll('[id^="badge-"]').forEach(badge => {
        const module = badge.id.replace('badge-', '');
        const all = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
        badge.textContent = `${all.length}/${all.length}`;
    });
    updateCount();
});

document.getElementById('deselectAll').addEventListener('click', function () {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('.perm-item').classList.remove('checked');
    });
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('[id^="badge-"]').forEach(badge => {
        const module = badge.id.replace('badge-', '');
        const all = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
        badge.textContent = `0/${all.length}`;
    });
    updateCount();
});

// Init
document.querySelectorAll('[id^="badge-"]').forEach(badge => {
    const module = badge.id.replace('badge-', '');
    syncModuleCheckbox(module);
});
updateCount();
</script>
@endpush
