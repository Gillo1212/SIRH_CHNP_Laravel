@extends('layouts.master')
@section('title', 'Sauvegardes')
@section('page-title', 'Gestion des sauvegardes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li>Sauvegardes</li>
@endsection

@push('styles')
<style>
.backup-row:hover { background:#F9FAFB; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-database me-2" style="color:#0A4D8C;"></i>Gestion des sauvegardes
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Disponibilité des données — <strong>Triade CID</strong>
            </p>
        </div>
        @can('backups.create')
        <form action="{{ route('admin.backups.create') }}" method="POST"
              onsubmit="return confirm('Lancer une nouvelle sauvegarde maintenant ?')">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;">
                <i class="fas fa-plus me-1"></i>Nouvelle sauvegarde
            </button>
        </form>
        @endcan
    </div>

    {{-- ── Flash messages ─────────────────────────────── --}}
    @foreach(['success','info','error'] as $msg)
    @if(session($msg))
    <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-{{ $msg === 'success' ? 'check-circle' : ($msg === 'error' ? 'times-circle' : 'info-circle') }} me-2"></i>
        {{ session($msg) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @endforeach

    {{-- ── KPIs ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Sauvegardes</div>
                <div style="font-size:26px;font-weight:700;color:#0A4D8C;margin-top:4px;">{{ $stats['count'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Taille totale</div>
                <div style="font-size:26px;font-weight:700;color:#059669;margin-top:4px;">{{ $stats['total_size'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Dernière sauvegarde</div>
                <div style="font-size:14px;font-weight:700;color:#D97706;margin-top:4px;">{{ $stats['derniere'] }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:10px;padding:14px 18px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;">Espace libre</div>
                <div style="font-size:26px;font-weight:700;color:#7C3AED;margin-top:4px;">{{ $stats['disk_free'] }}</div>
            </div>
        </div>
    </div>

    {{-- ── Bannière CID Disponibilité ───────────────────── --}}
    <div style="background:linear-gradient(135deg,#EFF6FF,#E0F2FE);border:1px solid #BFDBFE;border-radius:12px;padding:20px;margin-bottom:24px;">
        <div style="font-weight:600;color:#0A4D8C;margin-bottom:10px;font-size:14px;">
            <i class="fas fa-cloud me-2"></i>Stratégie de sauvegarde — Disponibilité (D de CID)
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div style="background:rgba(255,255,255,.7);border-radius:8px;padding:12px;">
                    <div style="font-weight:600;font-size:12px;color:#0A4D8C;margin-bottom:4px;">
                        <i class="fas fa-clock me-1"></i>Automatique (cron)
                    </div>
                    <div style="font-size:12px;color:#374151;">Sauvegarde quotidienne — 02h00 du matin</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:rgba(255,255,255,.7);border-radius:8px;padding:12px;">
                    <div style="font-weight:600;font-size:12px;color:#0A4D8C;margin-bottom:4px;">
                        <i class="fas fa-lock me-1"></i>Chiffrée AES-256
                    </div>
                    <div style="font-size:12px;color:#374151;">Confidentialité des données sauvegardées</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:rgba(255,255,255,.7);border-radius:8px;padding:12px;">
                    <div style="font-weight:600;font-size:12px;color:#0A4D8C;margin-bottom:4px;">
                        <i class="fas fa-history me-1"></i>Rétention 30 jours
                    </div>
                    <div style="font-size:12px;color:#374151;">Purge automatique des anciennes sauvegardes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Liste des sauvegardes ─────────────────────────── --}}
    <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;overflow:hidden;">
        @if(count($backups) > 0)
        <table class="table mb-0" style="font-size:13px;">
            <thead>
                <tr style="background:var(--theme-bg-secondary);">
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Fichier</th>
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Taille</th>
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Date de création</th>
                    <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Statut</th>
                    <th class="border-0 py-3 px-4 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($backups as $backup)
                <tr class="backup-row" style="border-bottom:1px solid var(--theme-border);">
                    <td class="py-3 px-4 border-0">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-file-archive" style="color:#0A4D8C;font-size:14px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--theme-text);font-family:monospace;font-size:12px;">
                                    {{ $backup['name'] }}
                                </div>
                                <div style="font-size:11px;color:#9CA3AF;">Sauvegarde SQL</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 border-0" style="color:var(--theme-text-muted);vertical-align:middle;">
                        {{ $backup['size'] }}
                    </td>
                    <td class="py-3 px-4 border-0" style="color:var(--theme-text-muted);vertical-align:middle;">
                        {{ $backup['date'] }}
                    </td>
                    <td class="py-3 px-4 border-0" style="vertical-align:middle;">
                        <span style="background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;">
                            <i class="fas fa-check me-1"></i>OK
                        </span>
                    </td>
                    <td class="py-3 px-4 border-0 text-end" style="vertical-align:middle;">
                        <div class="d-flex justify-content-end gap-2">
                            @can('backups.download')
                            <a href="{{ route('admin.backups.download', $backup['name']) }}"
                               class="btn btn-outline-primary btn-sm" style="font-size:12px;"
                               title="Télécharger">
                                <i class="fas fa-download me-1"></i>Télécharger
                            </a>
                            @endcan
                            @can('backups.delete')
                            <form method="POST"
                                  action="{{ route('admin.backups.delete', $backup['name']) }}"
                                  onsubmit="return confirm('Supprimer définitivement la sauvegarde {{ $backup['name'] }} ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" style="font-size:12px;"
                                        title="Supprimer">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-5">
            <i class="fas fa-database" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
            <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">Aucune sauvegarde disponible</div>
            <p class="text-muted mb-4" style="font-size:13px;">
                Créez votre première sauvegarde manuelle pour garantir la <strong>Disponibilité</strong> des données.
            </p>
            @can('backups.create')
            <form action="{{ route('admin.backups.create') }}" method="POST"
                  onsubmit="return confirm('Lancer une sauvegarde ?')">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Créer une sauvegarde
                </button>
            </form>
            @endcan
        </div>
        @endif
    </div>

    {{-- ── Note technique ────────────────────────────────── --}}
    <div class="mt-3 p-3 rounded d-flex align-items-center gap-3 flex-wrap"
         style="background:rgba(10,77,140,.04);border:1px dashed #BFDBFE;font-size:12px;color:#1D4ED8;">
        <i class="fas fa-info-circle me-1"></i>
        <span>Les sauvegardes sont stockées dans <code>storage/app/backups/</code> et générées via <strong>mysqldump</strong>.
        Chaque opération est enregistrée dans le <a href="{{ route('admin.audit.index') }}" style="color:#0A4D8C;">journal d'audit</a>.</span>
    </div>

</div>
@endsection
