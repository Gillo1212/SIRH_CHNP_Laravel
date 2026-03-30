@extends('layouts.master')
@section('title', 'Journal d\'audit')
@section('page-title', 'Journal d\'audit — Intégrité CID')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li>Journal d'audit</li>
@endsection

@push('styles')
<style>
.log-row:hover { background:#F9FAFB; }
.event-badge { display:inline-flex;align-items:center;padding:2px 9px;border-radius:12px;font-size:10px;font-weight:700; }
.ev-created  { background:#D1FAE5;color:#065F46; }
.ev-updated  { background:#DBEAFE;color:#1E40AF; }
.ev-deleted  { background:#FEE2E2;color:#991B1B; }
.ev-auth     { background:#EDE9FE;color:#5B21B6; }
.ev-default  { background:#F3F4F6;color:#374151; }
/* filter-bar styles handled by master layout */
.kpi-mini    { border-radius:10px;padding:14px 18px; }
.type-pill   { display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:1px solid var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);transition:all 180ms; }
.type-pill:hover { background:#EFF6FF;color:#0A4D8C;border-color:#BFDBFE; }
.type-pill.active { background:#0A4D8C;color:#fff;border-color:#0A4D8C; }
.type-pill.active-danger { background:#DC2626;color:#fff;border-color:#DC2626; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ──────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-shield-alt me-2" style="color:#0A4D8C;"></i>Journal d'audit
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Traçabilité immuable de toutes les actions — <strong>Intégrité CID</strong>
            </p>
        </div>
        <a href="{{ route('admin.audit.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-download me-1"></i>Export CSV
        </a>
    </div>

    {{-- ── Pills de filtre rapide ──────────────────────── --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.audit.index') }}"
           class="type-pill {{ $type === 'all' ? 'active' : '' }}">
            <i class="fas fa-list-ul"></i> Journal complet
        </a>
        <a href="{{ route('admin.audit.index', ['type' => 'connexions']) }}"
           class="type-pill {{ $type === 'connexions' ? 'active' : '' }}">
            <i class="fas fa-sign-in-alt"></i> Connexions
        </a>
        <a href="{{ route('admin.audit.index', ['type' => 'echecs']) }}"
           class="type-pill {{ $type === 'echecs' ? 'active-danger' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> Tentatives échouées
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── KPIs ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-mini" style="background:#EFF6FF;border:1px solid #DBEAFE;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Total entrées</div>
                <div style="font-size:26px;font-weight:700;color:#0A4D8C;margin-top:4px;">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-mini" style="background:#ECFDF5;border:1px solid #A7F3D0;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Aujourd'hui</div>
                <div style="font-size:26px;font-weight:700;color:#059669;margin-top:4px;">{{ $stats['today'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-mini" style="background:#F5F3FF;border:1px solid #DDD6FE;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Cette semaine</div>
                <div style="font-size:26px;font-weight:700;color:#7C3AED;margin-top:4px;">{{ $stats['this_week'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-mini" style="background:#FFF7ED;border:1px solid #FED7AA;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Types événements</div>
                <div style="font-size:26px;font-weight:700;color:#D97706;margin-top:4px;">{{ $stats['events']->count() }}</div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ──────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('admin.audit.index') }}">
            @if($type !== 'all')
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control border-start-0" placeholder="Rechercher dans la description...">
                    </div>
                </div>
                <select name="event" class="form-select" style="width:auto;min-width:170px;">
                    <option value="">Tous les événements</option>
                    @foreach($stats['events'] as $ev)
                        <option value="{{ $ev }}" @selected(request('event') === $ev)>{{ ucfirst($ev) }}</option>
                    @endforeach
                </select>
                <select name="log_name" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les journaux</option>
                    @foreach($stats['log_names'] as $ln)
                        <option value="{{ $ln }}" @selected(request('log_name') === $ln)>{{ ucfirst($ln) }}</option>
                    @endforeach
                </select>
                <select name="causer_id" class="form-select" style="width:auto;min-width:170px;">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('causer_id') == $u->id)>
                            {{ $u->login ?? $u->name }}
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
                @if(request()->anyFilled(['search', 'event', 'log_name', 'causer_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.audit.index', $type !== 'all' ? ['type' => $type] : []) }}" class="btn btn-outline-secondary" title="Réinitialiser">
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
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date/Heure</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Journal</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Événement</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Description</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Utilisateur</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Sujet</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $ev  = strtolower($log->event ?? '');
                    $cls = match(true) {
                        str_contains($ev, 'creat') => 'ev-created',
                        str_contains($ev, 'updat') || str_contains($ev, 'modif') => 'ev-updated',
                        str_contains($ev, 'delet') || str_contains($ev, 'suppr') => 'ev-deleted',
                        str_contains($ev, 'login') || str_contains($ev, 'auth') || str_contains($ev, 'connexion') => 'ev-auth',
                        default => 'ev-default',
                    };
                    $props = is_array($log->properties) ? $log->properties : ($log->properties?->toArray() ?? []);
                @endphp
                <tr class="log-row" style="border-bottom:1px solid var(--theme-border);">
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;color:var(--theme-text-muted);">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="py-2 px-3 border-0">
                        <code style="font-size:10px;background:rgba(10,77,140,.07);color:#0A4D8C;padding:1px 6px;border-radius:4px;">
                            {{ $log->log_name ?? '—' }}
                        </code>
                    </td>
                    <td class="py-2 px-3 border-0">
                        @if($log->event)
                            <span class="event-badge {{ $cls }}">{{ ucfirst($log->event) }}</span>
                        @else
                            <span class="event-badge ev-default">—</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 border-0" style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--theme-text);">
                        {{ $log->description }}
                    </td>
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;">
                        @if($log->causer)
                            <span style="color:var(--theme-text);font-weight:500;">
                                {{ $log->causer->login ?? $log->causer->name ?? '—' }}
                            </span>
                        @else
                            <span style="color:var(--theme-text-muted);">Système</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;color:var(--theme-text-muted);">
                        @if($log->subject_type)
                            <span>{{ class_basename($log->subject_type) }}</span>
                            <span style="color:#9CA3AF;">#{{ $log->subject_id }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;">
                        <code style="font-size:10px;color:var(--theme-text-muted);">
                            {{ $props['ip'] ?? '—' }}
                        </code>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 border-0 text-muted">
                        <i class="fas fa-search mb-2 d-block" style="font-size:24px;opacity:.3;"></i>
                        Aucune entrée trouvée
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

    {{-- ── Badge CID ────────────────────────────────────── --}}
    <div class="mt-3 p-3 rounded d-flex align-items-center gap-3 flex-wrap"
         style="background:rgba(10,77,140,.04);border:1px dashed #BFDBFE;font-size:12px;color:#1D4ED8;">
        <i class="fas fa-lock me-1"></i>
        <strong>Intégrité CID</strong> — Ce journal est géré par Spatie Activity Log.
        Les entrées sont immuables et ne peuvent pas être modifiées par les utilisateurs.
        Audit signé avec IP + User-Agent pour chaque action.
    </div>

</div>
@endsection
