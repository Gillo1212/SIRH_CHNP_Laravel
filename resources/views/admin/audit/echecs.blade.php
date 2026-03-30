@extends('layouts.master')
@section('title', 'Tentatives échouées')
@section('page-title', 'Échecs & Actions suspectes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li><a href="{{ route('admin.audit.index') }}" style="color:#1565C0;">Journal d'audit</a></li>
    <li>Échecs</li>
@endsection

@push('styles')
<style>
.log-row:hover { background:#FFF5F5; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-exclamation-triangle me-2" style="color:#DC2626;"></i>Tentatives échouées / Actions suspectes
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Détection des menaces — <strong>Confidentialité & Intégrité CID</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.audit.connexions') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sign-in-alt me-1"></i>Connexions
            </a>
            <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Journal complet
            </a>
        </div>
    </div>

    {{-- ── KPIs ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Aujourd'hui</div>
                <div style="font-size:26px;font-weight:700;color:#DC2626;margin-top:4px;">{{ $stats['today'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Cette semaine</div>
                <div style="font-size:26px;font-weight:700;color:#D97706;margin-top:4px;">{{ $stats['this_week'] }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            @if($stats['today'] > 10)
            <div style="background:#FEF2F2;border:1px solid #FCA5A5;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#DC2626;">
                    <i class="fas fa-exclamation-circle me-1"></i>Alerte — Activité suspecte détectée
                </div>
                <div style="font-size:12px;color:#991B1B;margin-top:4px;">
                    Plus de 10 échecs aujourd'hui. Vérifiez les tentatives de connexion.
                </div>
            </div>
            @else
            <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#059669;">
                    <i class="fas fa-check-circle me-1"></i>Aucune anomalie détectée
                </div>
                <div style="font-size:12px;color:#065F46;margin-top:4px;">
                    Le système surveille en continu les tentatives d'intrusion.
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Filtres ──────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET" action="{{ route('admin.audit.echecs') }}">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="form-control" style="width:auto;min-width:145px;" title="Du">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="form-control" style="width:auto;min-width:145px;" title="Au">
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['date_from', 'date_to']))
                    <a href="{{ route('admin.audit.echecs') }}" class="btn btn-outline-secondary" title="Réinitialiser">
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
                <tr style="background:#FEF2F2;">
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date / Heure</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Utilisateur</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Journal</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Description</th>
                    <th class="border-0 py-3 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Adresse IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $props = is_array($log->properties) ? $log->properties : ($log->properties?->toArray() ?? []);
                @endphp
                <tr class="log-row" style="border-bottom:1px solid var(--theme-border);">
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;color:var(--theme-text-muted);font-family:monospace;font-size:11px;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="py-2 px-3 border-0" style="white-space:nowrap;">
                        @if($log->causer)
                        <span style="font-weight:600;color:#DC2626;">
                            {{ $log->causer->login ?? $log->causer->name ?? '—' }}
                        </span>
                        @else
                        <span style="color:var(--theme-text-muted);">
                            {{ $props['email'] ?? $props['login'] ?? 'Inconnu' }}
                        </span>
                        @endif
                    </td>
                    <td class="py-2 px-3 border-0">
                        <code style="font-size:10px;background:rgba(220,38,38,.07);color:#DC2626;padding:1px 6px;border-radius:4px;">
                            {{ $log->log_name ?? '—' }}
                        </code>
                    </td>
                    <td class="py-2 px-3 border-0" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--theme-text);">
                        {{ $log->description }}
                    </td>
                    <td class="py-2 px-3 border-0">
                        <code style="font-size:10px;color:var(--theme-text-muted);">
                            {{ $props['ip'] ?? '—' }}
                        </code>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 border-0">
                        <i class="fas fa-check-circle mb-2 d-block" style="font-size:32px;color:#10B981;opacity:.6;"></i>
                        <div style="color:#065F46;font-weight:600;margin-bottom:4px;">Aucune tentative échouée enregistrée</div>
                        <div class="text-muted" style="font-size:12px;">Le système est sécurisé — aucune anomalie détectée.</div>
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

    {{-- ── Note sécurité ─────────────────────────────────── --}}
    <div class="mt-3 p-3 rounded d-flex align-items-center gap-3 flex-wrap"
         style="background:rgba(220,38,38,.04);border:1px dashed #FCA5A5;font-size:12px;color:#991B1B;">
        <i class="fas fa-shield-alt me-1"></i>
        <span>Ce journal surveille les tentatives de connexion échouées, les accès refusés (403) et les actions bloquées.
        Toute activité suspecte est automatiquement enregistrée. Les comptes sont verrouillés après
        {{ \App\Models\Setting::get('security.max_login_attempts', 5) }} tentatives infructueuses.</span>
    </div>

</div>
@endsection
