@extends('layouts.master')

@section('title', 'Gestion des Permissions')
@section('page-title', 'Permissions')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.roles.index') }}" style="color:#1565C0;">Rôles</a></li>
    <li>Permissions</li>
@endsection

@push('styles')
<style>
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }
.kpi-card { border-radius:12px; padding:20px 24px; position:relative; overflow:hidden; }
.kpi-card::before { content:''; position:absolute; top:0; right:0; width:80px; height:80px; border-radius:0 12px 0 80px; opacity:.08; }
.kpi-card.blue::before  { background:#0A4D8C; }
.kpi-card.green::before { background:#059669; }
.perm-table th { font-size:12px;font-weight:600;color:#6B7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #E5E7EB; }
.perm-table td { font-size:13px;vertical-align:middle; }
.badge-role { font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:#EFF6FF;color:#1E40AF;margin:1px; }
.module-section { margin-bottom:28px; }
.module-title {
    display:flex;align-items:center;gap:10px;
    padding:8px 16px;background:#F8FAFC;border-radius:8px;
    border-left:3px solid #0A4D8C;margin-bottom:12px;
}
.module-title .title-text { font-weight:700;font-size:13px;color:#1E293B;flex:1; }
.module-title .badge-count { font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:#DBEAFE;color:#1E40AF; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-700 mb-0" style="color:#111827;">
                <i class="fas fa-key me-2" style="color:#0A4D8C;"></i>Gestion des Permissions
            </h4>
            <div style="font-size:13px;color:#6B7280;margin-top:2px;">
                {{ $stats['total'] }} permissions &bull; {{ $stats['modules'] }} modules
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.matrix') }}" class="btn btn-outline-primary fw-600">
                <i class="fas fa-table me-2"></i>Matrice interactive
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary fw-600">
                <i class="fas fa-arrow-left me-2"></i>Rôles
            </a>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary fw-600">
                <i class="fas fa-plus me-2"></i>Nouvelle permission
            </a>
        </div>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div style="width:40px;height:40px;border-radius:10px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;font-size:18px;">
                    <i class="fas fa-key" style="color:#1D4ED8;"></i>
                </div>
                <div style="font-size:28px;font-weight:700;color:#1E3A8A;margin-top:10px;">{{ $stats['total'] }}</div>
                <div style="font-size:13px;color:#1E40AF;font-weight:500;">Permissions totales</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green" style="background:#F0FDF4;">
                <div style="width:40px;height:40px;border-radius:10px;background:#D1FAE5;display:flex;align-items:center;justify-content:center;font-size:18px;">
                    <i class="fas fa-folder" style="color:#059669;"></i>
                </div>
                <div style="font-size:28px;font-weight:700;color:#065F46;margin-top:10px;">{{ $stats['modules'] }}</div>
                <div style="font-size:13px;color:#065F46;font-weight:500;">Modules</div>
            </div>
        </div>
    </div>

    {{-- Permissions par module --}}
    <div class="panel">
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
            <div class="module-section">
                <div class="module-title">
                    <i class="fas {{ $groupIcons[$groupName] ?? 'fa-folder' }}" style="color:#0A4D8C;width:16px;"></i>
                    <span class="title-text">{{ $groupName }}</span>
                    <span class="badge-count">{{ count($permissions) }} permission(s)</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover perm-table mb-0">
                        <thead>
                            <tr>
                                <th>Nom technique</th>
                                <th>Rôles assignés</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>
                                        <code style="background:#F3F4F6;padding:2px 8px;border-radius:6px;font-size:12.5px;color:#0A4D8C;">
                                            {{ $permission->name }}
                                        </code>
                                    </td>
                                    <td>
                                        @forelse($permission->roles as $role)
                                            <span class="badge-role">{{ $role->name }}</span>
                                        @empty
                                            <span style="color:#9CA3AF;font-size:12px;">Aucun rôle</span>
                                        @endforelse
                                    </td>
                                    <td class="text-end">
                                        @if($permission->roles->count() === 0)
                                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Supprimer la permission « {{ $permission->name }} » ?');"
                                                  class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        style="padding:3px 10px;font-size:12px;">
                                                    <i class="fas fa-trash me-1"></i>Supprimer
                                                </button>
                                            </form>
                                        @else
                                            <span style="font-size:12px;color:#9CA3AF;">
                                                <i class="fas fa-lock me-1"></i>En cours d'utilisation
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
