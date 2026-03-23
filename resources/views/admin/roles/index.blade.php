@extends('layouts.master')

@section('title', 'Gestion des Rôles')
@section('page-title', 'Rôles & Permissions')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li>Rôles & Permissions</li>
@endsection

@push('styles')
<style>
.role-card {
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    background: #fff;
    padding: 24px;
    transition: box-shadow 200ms, transform 200ms;
    height: 100%;
}
.role-card:hover {
    box-shadow: 0 6px 20px rgba(10,77,140,.10);
    transform: translateY(-2px);
}
.role-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.stat-block { text-align: center; padding: 10px 0; }
.stat-block .stat-value { font-size: 26px; font-weight: 700; line-height: 1; }
.stat-block .stat-label { font-size: 12px; color: #6B7280; margin-top: 2px; font-weight: 500; }
.badge-system {
    font-size: 10px; font-weight: 600; padding: 2px 8px;
    border-radius: 20px; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;
}
.kpi-summary {
    border-radius: 12px; padding: 16px 20px;
    background: linear-gradient(135deg, #0A4D8C 0%, #1565C0 100%);
    color: #fff;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-700 mb-0" style="color:#111827;">
                <i class="fas fa-user-shield me-2" style="color:#0A4D8C;"></i>Gestion des Rôles
            </h4>
            <div style="font-size:13px;color:#6B7280;margin-top:2px;">
                {{ count($rolesData) }} rôles &bull; {{ $totalPermissions }} permissions au total
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.permissions.matrix') }}" class="btn btn-outline-primary fw-600">
                <i class="fas fa-table me-2"></i>Matrice permissions
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary fw-600">
                <i class="fas fa-key me-2"></i>Permissions
            </a>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary fw-600">
                <i class="fas fa-plus me-2"></i>Nouveau rôle
            </a>
        </div>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Hiérarchie --}}
    <div class="panel mb-4" style="background:#F8FAFF;border:1px solid #DBEAFE;">
        <div class="d-flex align-items-center gap-2 mb-0" style="font-size:13px;color:#1E40AF;font-weight:600;">
            <i class="fas fa-sitemap"></i>
            <span>Hiérarchie des rôles :</span>
            <span class="text-muted fw-400">Agent</span>
            <i class="fas fa-angle-right text-muted"></i>
            <span class="text-muted fw-400">Manager</span>
            <i class="fas fa-angle-right text-muted"></i>
            <span class="text-muted fw-400">AgentRH</span>
            <i class="fas fa-angle-right text-muted"></i>
            <span class="text-muted fw-400">DRH</span>
            <i class="fas fa-angle-right text-muted"></i>
            <span class="text-muted fw-400">AdminSystème</span>
        </div>
    </div>

    {{-- Grille rôles --}}
    <div class="row g-3">
        @foreach($rolesData as $role)
            <div class="col-md-6 col-xl-4">
                <div class="role-card">
                    {{-- Header --}}
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="role-icon" style="background:{{ $role['color'] }}18;">
                            <i class="fas {{ $role['icon'] }}" style="color:{{ $role['color'] }};"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="fw-700 mb-0" style="color:#111827;">{{ $role['display_name'] }}</h6>
                                @if($role['is_system'])
                                    <span class="badge-system">Système</span>
                                @endif
                            </div>
                            <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">
                                <code style="background:#F3F4F6;padding:1px 6px;border-radius:4px;font-size:11px;">{{ $role['name'] }}</code>
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="row g-0 mb-3" style="border:1px solid #F3F4F6;border-radius:8px;overflow:hidden;">
                        <div class="col-6 stat-block" style="border-right:1px solid #F3F4F6;padding:12px;">
                            <div class="stat-value" style="color:{{ $role['color'] }};">{{ $role['users_count'] }}</div>
                            <div class="stat-label">Utilisateurs</div>
                        </div>
                        <div class="col-6 stat-block" style="padding:12px;">
                            <div class="stat-value" style="color:#111827;">{{ $role['permissions_count'] }}</div>
                            <div class="stat-label">Permissions</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.roles.show', $role['id']) }}"
                           class="btn btn-sm fw-600"
                           style="background:#F3F4F6;color:#374151;border:none;">
                            <i class="fas fa-eye me-1"></i>Voir les détails
                        </a>

                        @if(!$role['is_admin'])
                            <a href="{{ route('admin.roles.edit', $role['id']) }}"
                               class="btn btn-sm btn-outline-primary fw-600">
                                <i class="fas fa-edit me-1"></i>Modifier les permissions
                            </a>
                        @else
                            <div class="d-flex align-items-center gap-2 p-2 rounded"
                                 style="background:#FEF3C7;border:1px solid #FDE68A;">
                                <i class="fas fa-lock" style="color:#D97706;font-size:12px;"></i>
                                <span style="font-size:12px;color:#92400E;">Rôle protégé – permissions fixes</span>
                            </div>
                        @endif

                        @if(!$role['is_system'])
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger fw-600"
                                    onclick="confirmDelete({{ $role['id'] }}, '{{ $role['name'] }}')">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                            <form id="delete-form-{{ $role['id'] }}"
                                  action="{{ route('admin.roles.destroy', $role['id']) }}"
                                  method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Note --}}
    <div class="alert alert-info mt-4 mb-0">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Rôles système :</strong> Agent, Manager, AgentRH, DRH et AdminSystème sont des rôles système et ne peuvent pas être supprimés.
        Le rôle <strong>AdminSystème</strong> conserve toujours toutes les permissions.
    </div>

</div>

{{-- Modal confirmation suppression --}}
<div class="modal fade modal-sirh" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Vous êtes sur le point de supprimer le rôle <strong id="deleteRoleName"></strong>.</p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible. Assurez-vous qu'aucun utilisateur n'a ce rôle.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger fw-600" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteRoleId = null;

function confirmDelete(roleId, roleName) {
    deleteRoleId = roleId;
    document.getElementById('deleteRoleName').textContent = roleName;
    new bootstrap.Modal(document.getElementById('modalDelete')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (deleteRoleId) {
        document.getElementById('delete-form-' + deleteRoleId).submit();
    }
});
</script>
@endpush
