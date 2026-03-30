@extends('layouts.master')

@section('title', 'Comptes utilisateurs en attente')
@section('page-title', 'Comptes en attente de création')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li>Comptes en attente</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-600 mb-0" style="font-size:15px;">
        Agents sans compte utilisateur
        @if($agents->total() > 0)
            <span class="badge bg-warning text-dark ms-2" style="font-size:12px;">{{ $agents->total() }}</span>
        @endif
    </h5>
</div>

<div class="panel p-0" style="overflow:hidden;">
    @if($agents->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px;">
            <thead>
                <tr style="font-size:11px;text-transform:uppercase;letter-spacing:0.05em;">
                    <th class="ps-3" style="width:130px;">Matricule</th>
                    <th>Agent</th>
                    <th class="d-none d-md-table-cell">Fonction</th>
                    <th class="d-none d-lg-table-cell">Service</th>
                    <th class="d-none d-lg-table-cell">Créé le</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $agent)
                <tr>
                    <td class="ps-3">
                        <code style="font-size:12px;">{{ $agent->matricule }}</code>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $agent->prenom }} {{ $agent->nom }}</div>
                                @if($agent->email)
                                    <div style="font-size:11px;" class="text-muted">{{ $agent->email }}</div>
                                @else
                                    <div style="font-size:11px;color:#DC2626;"><i class="fas fa-exclamation-circle me-1"></i>Pas d'email</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') ?? '—' }}</td>
                    <td class="d-none d-lg-table-cell">{{ $agent->service?->nom_service ?? '—' }}</td>
                    <td class="d-none d-lg-table-cell text-muted">{{ $agent->created_at->format('d/m/Y') }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.users.create-for-agent', $agent->id_agent) }}"
                           class="btn btn-sm btn-primary" style="font-size:12px;padding:4px 10px;">
                            <i class="fas fa-user-plus me-1"></i>Créer le compte
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top" style="font-size:12px;">
        <span class="text-muted">{{ $agents->total() }} agent(s) en attente</span>
        {{ $agents->links('pagination::bootstrap-5') }}
    </div>

    @else
    <div class="text-center py-5">
        <i class="fas fa-check-circle" style="font-size:48px;color:#D1D5DB;"></i>
        <p class="mt-3 text-muted">Tous les agents ont un compte utilisateur.</p>
    </div>
    @endif
</div>

@if(session('success'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastSuccess" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" style="font-size:13px;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var t = new bootstrap.Toast(document.getElementById('toastSuccess'), { delay: 5000 });
    t.show();
});
</script>
@endpush
@endif

@endsection
