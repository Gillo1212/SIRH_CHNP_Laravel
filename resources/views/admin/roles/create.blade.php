@extends('layouts.master')

@section('title', 'Créer un Rôle')
@section('page-title', 'Nouveau Rôle')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.roles.index') }}" style="color:#1565C0;">Rôles</a></li>
    <li>Nouveau rôle</li>
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

.group-icons {
    'Personnel':'fa-users','Contrats':'fa-file-contract','Congés':'fa-umbrella-beach',
    'Absences':'fa-calendar-times','Planning':'fa-calendar-alt','GED':'fa-folder',
    'Reporting':'fa-chart-bar','DRH':'fa-user-tie','Administration':'fa-cog','Autres':'fa-ellipsis-h'
}
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
                        <i class="fas fa-plus-circle me-2" style="color:#0A4D8C;"></i>Créer un nouveau rôle
                    </h4>
                    <div style="font-size:13px;color:#6B7280;margin-top:2px;">Définissez le nom et les permissions du rôle</div>
                </div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary fw-600">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                {{-- Nom du rôle --}}
                <div class="panel mb-4">
                    <h6 class="fw-700 mb-3" style="color:#374151;">
                        <i class="fas fa-tag me-2 text-primary"></i>Identification du rôle
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-600" style="font-size:13px;">
                                Nom du rôle <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="ex: chef_service"
                                   autocomplete="off">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div style="font-size:12px;color:#6B7280;margin-top:4px;">
                                Lettres et underscores uniquement. Ce nom sera utilisé dans le code.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Aperçu</label>
                            <div id="previewName" class="form-control" style="background:#F8FAFC;color:#6B7280;font-size:13px;">
                                Le nom apparaîtra ici...
                            </div>
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
                                <span class="badge-count" id="badge-{{ Str::slug($groupName) }}">0/{{ count($permissions) }}</span>
                                <i class="fas fa-chevron-down toggle-icon" id="icon-{{ Str::slug($groupName) }}" style="color:#6B7280;font-size:11px;transition:transform 200ms;"></i>
                            </div>
                            <div class="perm-grid" id="group-{{ Str::slug($groupName) }}">
                                @foreach($permissions as $permission)
                                    <div class="perm-item {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}"
                                         id="item-{{ $permission->id }}">
                                        <input type="checkbox"
                                               class="form-check-input permission-checkbox"
                                               id="perm_{{ $permission->id }}"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               data-module="{{ Str::slug($groupName) }}"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label for="perm_{{ $permission->id }}" class="flex-grow-1">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @error('permissions')
                        <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Boutons --}}
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary fw-600 px-4">
                        <i class="fas fa-save me-2"></i>Créer le rôle
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary fw-600">
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
// Aperçu du nom
document.getElementById('name').addEventListener('input', function () {
    const preview = document.getElementById('previewName');
    preview.textContent = this.value || 'Le nom apparaîtra ici...';
    preview.style.color = this.value ? '#111827' : '#6B7280';
});

// Toggle groupe (masquer/afficher)
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

// Cocher/décocher tout un module
document.querySelectorAll('.module-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function (e) {
        e.stopPropagation();
        const module = this.dataset.module;
        document.querySelectorAll(`[data-module="${module}"].permission-checkbox`).forEach(cb => {
            cb.checked = this.checked;
            document.getElementById('item-' + cb.closest('.perm-item')?.id?.replace('item-', '')).classList.toggle('checked', this.checked);
            updateItemStyle(cb);
        });
        updateBadge(module);
        updateCount();
    });
});

// Style des items
document.querySelectorAll('.permission-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        updateItemStyle(this);
        updateBadge(this.dataset.module);
        syncModuleCheckbox(this.dataset.module);
        updateCount();
    });
});

function updateItemStyle(cb) {
    const item = cb.closest('.perm-item');
    item.classList.toggle('checked', cb.checked);
}

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

// Sélectionner/désélectionner tout
document.getElementById('selectAll').addEventListener('click', function () {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = true;
        updateItemStyle(cb);
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
        updateItemStyle(cb);
    });
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('[id^="badge-"]').forEach(badge => {
        const module = badge.id.replace('badge-', '');
        const all = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
        badge.textContent = `0/${all.length}`;
    });
    updateCount();
});

// Init badges au chargement
document.querySelectorAll('[id^="badge-"]').forEach(badge => {
    const module = badge.id.replace('badge-', '');
    updateBadge(module);
    syncModuleCheckbox(module);
});
updateCount();
</script>
@endpush
