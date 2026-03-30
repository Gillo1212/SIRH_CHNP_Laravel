@extends('layouts.master')
@section('title', 'Historique des connexions')
@section('page-title', 'Connexions')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li><a href="{{ route('admin.audit.index') }}" style="color:#1565C0;">Journal d'audit</a></li>
    <li>Connexions</li>
@endsection

@push('styles')
<style>
.log-row:hover { background:#F9FAFB; }
.ev-badge { display:inline-flex;align-items:center;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700; }
.ev-login   { background:#D1FAE5;color:#065F46; }
.ev-logout  { background:#FEE2E2;color:#991B1B; }
.ev-default { background:#F3F4F6;color:#374151; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-sign-in-alt me-2" style="color:#0A4D8C;"></i>Historique des connexions
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Traçabilité des authentifications — <strong>Intégrité CID</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Journal complet
            </a>
            <a href="{{ route('admin.audit.echecs') }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-exclamation-triangle me-1"></i>Échecs
            </a>
        </div>
    </div>

    {{-- ── KPIs ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Aujourd'hui</div>
                <div style="font-size:26px;font-weight:700;color:#059669;margin-top:4px;">{{ $stats['today'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Cette semaine</div>
                <div style="font-size:26px;font-weight:700;color:#0A4D8C;margin-top:4px;">{{ $stats['cette_semaine'] }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Information</div>
                <div style="font-size:12px;color:#7C3AED;margin-top:4px;">
                    <i class="fas fa-info-circle me-1"></i>
                    Chaque connexion/déconnexion est automatiquement enregistrée par le système d'audit (Intégrité CID).
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ──────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('admin.audit.connexions') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="causer_id" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('causer_id') == $u->id)>
                            {{ $u->login }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="form-control" style="width:auto;min-width:145px;" title="Du">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="form-control" style="width:auto;min-width:145px;" title="Au">
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['causer_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.audit.connexions') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Tableau ──────────────────────────────────────── --}}
    <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;overflow:hidden;">
        <table class="table mb-0" style="font-size:12px;">
            <thead>
                <tr style="background:var(--theme-bg-secondary);">
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date / Heure</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Utilisateur</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Action</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Événement</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Adresse IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $desc = strtolower($log->description ?? '');
                    $evCls = str_contains($desc, 'déconnex') || str_contains($desc, 'logout')
                        ? 'ev-logout'
                        : (str_contains($desc, 'connexion') || str_contains($desc, 'login') ? 'ev-login' : 'ev-default');
                    $props = is_array($log->properties) ? $log->properties : ($log->properties?->toArray() ?? []);
                @endphp
                <tr class="log-row" style="border-bottom:1px solid var(--theme-border);">
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;color:var(--theme-text-muted);font-family:monospace;font-size:11px;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;">
                        @if($log->causer)
                        <div style="font-weight:600;color:var(--theme-text);">
                            {{ $log->causer->login ?? $log->causer->name ?? '—' }}
                        </div>
                        @if($log->causer->roles->first())
                        <div style="font-size:10px;color:#9CA3AF;">{{ $log->causer->roles->first()->name }}</div>
                        @endif
                        @else
                        <span style="color:var(--theme-text-muted);">Système</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 border-0" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--theme-text);">
                        {{ $log->description }}
                    </td>
                    <td class="py-2 px-3 border-0">
                        <span class="ev-badge {{ $evCls }}">
                            {{ $log->event ? ucfirst($log->event) : '—' }}
                        </span>
                    </td>
                    <td class="py-2 px-3 border-0">
                        <code style="font-size:10px;color:var(--theme-text-muted);">
                            {{ $props['ip'] ?? '—' }}
                        </code>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 border-0 text-muted">
                        <i class="fas fa-sign-in-alt mb-2 d-block" style="font-size:24px;opacity:.3;"></i>
                        Aucun enregistrement de connexion trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="px-4 py-3 border-top" style="border-color:var(--theme-border)!important;">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
