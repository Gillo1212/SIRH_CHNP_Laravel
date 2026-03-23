@extends('layouts.master')

@section('title', 'Détails du Rôle')
@section('page-title', 'Rôle – {{ $displayName }}')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.roles.index') }}" style="color:#1565C0;">Rôles</a></li>
    <li>{{ $displayName }}</li>
@endsection

@push('styles')
<style>
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }
.kpi-mini { border-radius:10px; padding:16px 18px; }
.badge-perm {
    display:inline-block; font-size:11.5px; font-weight:500;
    padding:3px 10px; border-radius:20px; background:#F3F4F6; color:#374151;
    border:1px solid #E5E7EB; margin:2px; font-family:monospace;
}
.user-row { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #F3F4F6; }
.user-row:last-child { border-bottom:none; }
.user-avatar {
    width:36px; height:36px; border-radius:50%;
    background:#DBEAFE; color:#1E40AF;
    display:flex; align-items:center; justify-content:center;
    font-weight:600; font-size:14px; flex-shrink:0;
}
.audit-row { padding:8px 0; border-bottom:1px solid #F9FAFB; font-size:13px; }
.audit-row:last-child { border-bottom:none; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-start justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div style="width:56px;height:56px;background:{{ $color }}18;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;">
                <i class="fas {{ $icon }}" style="color:{{ $color }};"></i>
            </div>
            <div>
                <h4 class="fw-700 mb-0" style="color:#111827;">{{ $displayName }}</h4>
                <div style="font-size:13px;color:#6B7280;">
                    <code style="background:#F3F4F6;padding:1px 6px;border-radius:4px;font-size:11px;">{{ $role->name }}</code>
                    @if($isSystem)
                        <span class="ms-2" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">Système</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($role->name !== 'AdminSystème')
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary fw-600">
                    <i class="fas fa-edit me-2"></i>Modifier les permissions
                </a>
            @endif
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary fw-600">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#EFF6FF;">
                <div style="font-size:28px;font-weight:700;color:#1E3A8A;">{{ $users->count() }}</div>
                <div style="font-size:12px;color:#1E40AF;font-weight:500;margin-top:2px;">
                    <i class="fas fa-users me-1"></i>Utilisateurs
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#F0FDF4;">
                <div style="font-size:28px;font-weight:700;color:#065F46;">{{ $role->permissions->count() }}</div>
                <div style="font-size:12px;color:#065F46;font-weight:500;margin-top:2px;">
                    <i class="fas fa-key me-1"></i>Permissions
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#FFFBEB;">
                <div style="font-size:28px;font-weight:700;color:#92400E;">{{ $groupedPermissions->count() }}</div>
                <div style="font-size:12px;color:#92400E;font-weight:500;margin-top:2px;">
                    <i class="fas fa-folder me-1"></i>Modules
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-mini" style="background:#F5F3FF;">
                <div style="font-size:28px;font-weight:700;color:#4C1D95;">{{ $auditLogs->count() }}</div>
                <div style="font-size:12px;color:#4C1D95;font-weight:500;margin-top:2px;">
                    <i class="fas fa-history me-1"></i>Événements audit
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Permissions --}}
        <div class="col-lg-8">
            <div class="panel">
                <h6 class="fw-700 mb-3" style="color:#374151;">
                    <i class="fas fa-key me-2 text-primary"></i>Permissions accordées
                </h6>

                @if($groupedPermissions->isEmpty())
                    <div class="text-center py-4" style="color:#9CA3AF;">
                        <i class="fas fa-ban fa-2x mb-2 d-block"></i>
                        Aucune permission assignée à ce rôle
                    </div>
                @else
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
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas {{ $groupIcons[$groupName] ?? 'fa-folder' }}"
                                   style="color:#0A4D8C;font-size:13px;width:16px;"></i>
                                <span style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.05em;">
                                    {{ $groupName }}
                                </span>
                                <span style="font-size:11px;color:#9CA3AF;">({{ count($permissions) }})</span>
                            </div>
                            <div>
                                @foreach($permissions as $permission)
                                    <span class="badge-perm">{{ $permission->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Utilisateurs + Audit --}}
        <div class="col-lg-4">

            {{-- Utilisateurs --}}
            <div class="panel mb-4">
                <h6 class="fw-700 mb-3" style="color:#374151;">
                    <i class="fas fa-users me-2 text-primary"></i>Utilisateurs ({{ $users->count() }})
                </h6>

                @if($users->isEmpty())
                    <div class="text-center py-3" style="color:#9CA3AF;font-size:13px;">
                        <i class="fas fa-user-slash mb-2 d-block fa-lg"></i>
                        Aucun utilisateur avec ce rôle
                    </div>
                @else
                    <div style="max-height:250px;overflow-y:auto;">
                        @foreach($users->take(15) as $user)
                            <div class="user-row">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->agent?->prenom ?? $user->login, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div style="font-size:13px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $user->agent?->prenom ? $user->agent->prenom . ' ' . $user->agent->nom : $user->login }}
                                    </div>
                                    <div style="font-size:11px;color:#9CA3AF;">{{ $user->login }}</div>
                                </div>
                            </div>
                        @endforeach
                        @if($users->count() > 15)
                            <div class="text-center pt-2" style="font-size:12px;color:#6B7280;">
                                +{{ $users->count() - 15 }} autre(s)
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Audit --}}
            <div class="panel">
                <h6 class="fw-700 mb-3" style="color:#374151;">
                    <i class="fas fa-history me-2 text-primary"></i>Historique modifications
                </h6>

                @if($auditLogs->isEmpty())
                    <div class="text-center py-3" style="color:#9CA3AF;font-size:13px;">
                        <i class="fas fa-clock mb-2 d-block fa-lg"></i>
                        Aucune modification enregistrée
                    </div>
                @else
                    @foreach($auditLogs as $log)
                        <div class="audit-row">
                            <div style="font-weight:600;color:#374151;">{{ $log->description }}</div>
                            <div style="color:#6B7280;font-size:11.5px;margin-top:2px;">
                                <i class="fas fa-user me-1"></i>{{ $log->causer?->login ?? 'Système' }}
                                &bull;
                                <i class="fas fa-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
