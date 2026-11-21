@extends('layouts.master')
@section('title', 'Sauvegardes-Disponibilité CID')
@section('page-title', 'Gestion des sauvegardes')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li>Sauvegardes</li>
@endsection

@push('styles')
<style>
/* ── KPI cards ── */
.kpi-card {
    border-radius: 14px;
    padding: 20px 22px;
    position: relative;
    overflow: hidden;
    transition: box-shadow 200ms, transform 200ms;
}
.kpi-card:hover {
    box-shadow: 0 8px 24px rgba(10,77,140,.12);
    transform: translateY(-3px);
}
.kpi-card .kpi-icon {
    width: 46px; height: 46px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; flex-shrink: 0;
}
.kpi-card .kpi-value  { font-size: 28px; font-weight: 700; line-height: 1.1; margin-top: 14px; }
.kpi-card .kpi-label  { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6B7280; }
.kpi-card::before {
    content: ''; position: absolute; top: 0; right: 0;
    width: 80px; height: 80px;
    border-radius: 0 14px 0 80px; opacity: .06;
}
.kpi-card.blue::before  { background: #0A4D8C; }
.kpi-card.green::before { background: #059669; }
.kpi-card.amber::before { background: #D97706; }

/* ── Table ── */
.backup-row { transition: background 140ms; }
.backup-row:hover { background: var(--theme-bg-secondary); }

/* ── Cron badge ── */
.cron-code {
    font-family: monospace; font-size: 11.5px;
    background: rgba(10,77,140,.08); color: #0A4D8C;
    padding: 2px 8px; border-radius: 5px; font-weight: 700;
    letter-spacing: .03em;
}

/* ── Pills ── */
.pill-ok  { background:#D1FAE5; color:#065F46; padding:3px 11px; border-radius:20px; font-size:10px; font-weight:700; display:inline-flex; align-items:center; gap:4px; }
.pill-dim { background:#F3F4F6; color:#6B7280; padding:3px 11px; border-radius:20px; font-size:10px; font-weight:700; }

/* ── Panel header ── */
.panel-hd {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--theme-border);
    font-weight: 700; font-size: 13.5px; color: var(--theme-text);
}

/* ── Strat card items ── */
.strat-item {
    background: rgba(255,255,255,.72);
    border-radius: 9px; padding: 12px 14px;
}
.strat-item .strat-title { font-weight: 700; font-size: 11px; color: #0A4D8C; margin-bottom: 3px; }
.strat-item .strat-desc  { font-size: 12px; color: #374151; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête ─────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-database me-2" style="color:#0A4D8C;"></i>Gestion des sauvegardes
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Disponibilité des données : Sauvegardes automatiques - Rétention 30 jours - Purge automatique
            </p>
        </div>
        @can('backups.create')
        <form action="{{ route('admin.backups.create') }}" method="POST"
              onsubmit="return confirm('Lancer une nouvelle sauvegarde maintenant ?')">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="border-radius:8px;">
                <i class="fas fa-plus"></i> Nouvelle sauvegarde
            </button>
        </form>
        @endcan
    </div>

    {{-- ── Flash messages ───────────────────────────────────────── --}}
    @foreach(['success','info','error','warning'] as $msg)
    @if(session($msg))
    <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-{{ $msg === 'success' ? 'check-circle' : ($msg === 'error' ? 'times-circle' : ($msg === 'warning' ? 'exclamation-triangle' : 'info-circle')) }} me-2"></i>
        {{ session($msg) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @endforeach

    {{-- ── KPIs (3 cartes) ──────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Nombre de sauvegardes --}}
        <div class="col-md-4">
            <div class="kpi-card blue" style="background:#EFF6FF;border:1px solid #BFDBFE;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#DBEAFE;">
                        <i class="fas fa-hdd" style="color:#0A4D8C;"></i>
                    </div>
                    <div class="kpi-label">Sauvegardes</div>
                </div>
                <div class="kpi-value" style="color:#0A4D8C;">{{ $stats['count'] }}</div>
            </div>
        </div>

        {{-- Taille totale --}}
        <div class="col-md-4">
            <div class="kpi-card green" style="background:#ECFDF5;border:1px solid #A7F3D0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#D1FAE5;">
                        <i class="fas fa-weight-hanging" style="color:#059669;"></i>
                    </div>
                    <div class="kpi-label">Taille totale</div>
                </div>
                <div class="kpi-value" style="color:#059669;">{{ $stats['total_size'] }}</div>
            </div>
        </div>

        {{-- Dernière sauvegarde --}}
        <div class="col-md-4">
            <div class="kpi-card amber" style="background:#FFFBEB;border:1px solid #FDE68A;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#FEF3C7;">
                        <i class="fas fa-clock" style="color:#D97706;"></i>
                    </div>
                    <div class="kpi-label">Dernière sauvegarde</div>
                </div>
                <div style="font-size:15px;font-weight:700;color:#D97706;margin-top:14px;line-height:1.2;">
                    {{ $stats['derniere'] }}
                </div>
            </div>
        </div>

        {{-- Espace libre — commenté : non pertinent en démo
        <div class="col-md-3">
            <div class="kpi-card grape" style="background:#F5F3FF;border:1px solid #DDD6FE;">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:#EDE9FE;">
                        <i class="fas fa-server" style="color:#7C3AED;"></i>
                    </div>
                    <div class="kpi-label">Espace libre</div>
                </div>
                <div class="kpi-value" style="color:#7C3AED;">{{ $stats['disk_free'] }}</div>
            </div>
        </div>
        --}}

    </div>

    {{-- ── Stratégie & planification ─────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Stratégie CID --}}
        <div class="col-lg-7">
            <div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:14px;padding:22px;height:100%;">
                <div style="font-weight:700;color:#0A4D8C;margin-bottom:16px;font-size:13.5px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-shield-alt"></i> Stratégie de sauvegarde
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="strat-item">
                            <div class="strat-title"><i class="fas fa-robot me-1"></i>Automatique (cron)</div>
                            <div class="strat-desc">Sauvegarde quotidienne à <strong>02h00</strong></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="strat-item">
                            <div class="strat-title"><i class="fas fa-lock me-1"></i>Chiffrée AES-256</div>
                            <div class="strat-desc">Confidentialité des données sauvegardées</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="strat-item">
                            <div class="strat-title"><i class="fas fa-history me-1"></i>Rétention 30 jours</div>
                            <div class="strat-desc">Purge automatique des fichiers anciens</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-2" style="font-size:12px;color:#1E40AF;border-top:1px solid rgba(10,77,140,.12);">
                    <i class="fas fa-info-circle me-1"></i>
                    Base technique du <strong>Plan de Reprise d'Activité (PRA)</strong>
                     norme <strong>ISO 22301:2019</strong>.
                </div>
            </div>
        </div>

        {{-- Tâches planifiées --}}
        <div class="col-lg-5">
            <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:14px;padding:22px;height:100%;">
                <div style="font-weight:700;color:var(--theme-text);margin-bottom:16px;font-size:13.5px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-calendar-alt" style="color:#0A4D8C;"></i> Tâches planifiées (Scheduler)
                </div>
                <div class="d-flex flex-column gap-3">

                    {{-- Backup --}}
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--theme-bg-secondary);border-radius:10px;border-left:3px solid #059669;">
                        <div style="width:36px;height:36px;background:#D1FAE5;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-database" style="font-size:14px;color:#059669;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:12.5px;color:var(--theme-text);">Sauvegarde automatique</div>
                            <div style="font-size:11px;color:var(--theme-text-muted);margin-top:2px;">
                                <span class="cron-code">0 2 * * *</span> &nbsp;chaque nuit à 02h00
                            </div>
                        </div>
                        <span class="pill-ok flex-shrink-0">
                            <i class="fas fa-check"></i> Actif
                        </span>
                    </div>

                    {{-- Contrats --}}
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--theme-bg-secondary);border-radius:10px;border-left:3px solid #D97706;">
                        <div style="width:36px;height:36px;background:#FEF3C7;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-file-contract" style="font-size:14px;color:#D97706;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:12.5px;color:var(--theme-text);">Vérification contrats</div>
                            <div style="font-size:11px;color:var(--theme-text-muted);margin-top:2px;">
                                <span class="cron-code">0 6 * * *</span> &nbsp;chaque matin à 06h00
                            </div>
                        </div>
                        <span class="pill-ok flex-shrink-0">
                            <i class="fas fa-check"></i> Actif
                        </span>
                    </div>

                </div>
                <div class="mt-3 pt-2" style="font-size:11px;color:var(--theme-text-muted);border-top:1px solid var(--theme-border);">
                    <i class="fas fa-terminal me-1"></i>
                    Déclenchées par <code>php artisan schedule:run</code> (cron système)
                </div>
            </div>
        </div>
    </div>

    {{-- ── Liste des sauvegardes ─────────────────────────────────── --}}
    <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;overflow:hidden;">

        {{-- En-tête tableau --}}
        <div class="d-flex align-items-center justify-content-between px-4 py-3"
             style="border-bottom:1px solid var(--theme-border);">
            <div style="font-weight:700;font-size:13.5px;color:var(--theme-text);">
                <i class="fas fa-list me-2" style="color:#0A4D8C;"></i>
                Fichiers de sauvegarde
                @if($stats['count'] > 0)
                    <span style="font-weight:400;font-size:12px;color:var(--theme-text-muted);">({{ $stats['count'] }} fichier{{ $stats['count'] > 1 ? 's' : '' }})</span>
                @endif
            </div>
            <div style="font-size:12px;color:var(--theme-text-muted);">
                <i class="fas fa-folder-open me-1"></i><code>storage/app/backups/</code>
            </div>
        </div>

        @if(count($backups) > 0)
        <div class="table-responsive">
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
                    @foreach($backups as $i => $backup)
                    <tr class="backup-row" style="border-bottom:1px solid var(--theme-border);">

                        {{-- Fichier --}}
                        <td class="py-3 px-4 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:38px;height:38px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-file-archive" style="color:#0A4D8C;font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--theme-text);font-family:monospace;font-size:11.5px;">
                                        {{ $backup['name'] }}
                                    </div>
                                    <div style="font-size:11px;color:#9CA3AF;">
                                        Dump SQL - mysqldump
                                        @if($i === 0)
                                            <span style="background:#FEF3C7;color:#92400E;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;">Dernière</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Taille --}}
                        <td class="py-3 px-4 border-0" style="vertical-align:middle;color:var(--theme-text-muted);">
                            {{ $backup['size'] }}
                        </td>

                        {{-- Date --}}
                        <td class="py-3 px-4 border-0" style="vertical-align:middle;color:var(--theme-text-muted);">
                            <i class="fas fa-calendar-day me-1" style="font-size:11px;"></i>
                            {{ $backup['date'] }}
                        </td>

                        {{-- Statut --}}
                        <td class="py-3 px-4 border-0" style="vertical-align:middle;">
                            <span class="pill-ok">
                                <i class="fas fa-check me-1"></i>Intègre
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="py-3 px-4 border-0 text-end" style="vertical-align:middle;">
                            <div class="d-flex justify-content-end gap-2">
                                @can('backups.download')
                                <a href="{{ route('admin.backups.download', $backup['name']) }}"
                                   class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                                   style="font-size:12px;" title="Télécharger">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                                @endcan
                                @can('backups.delete')
                                <form method="POST"
                                      action="{{ route('admin.backups.delete', $backup['name']) }}"
                                      onsubmit="return confirm('Supprimer définitivement la sauvegarde ?\n\n{{ $backup['name'] }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
                                            style="font-size:12px;" title="Supprimer">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        {{-- État vide --}}
        <div class="text-center py-5 px-4">
            <div style="width:64px;height:64px;background:#F3F4F6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fas fa-database" style="font-size:26px;color:#D1D5DB;"></i>
            </div>
            <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">Aucune sauvegarde disponible</div>
            <p class="text-muted mb-4" style="font-size:13px;max-width:380px;margin:0 auto 20px;">
                Créez votre première sauvegarde pour garantir la <strong>Disponibilité</strong> des données
                et constituer la base du Plan de Reprise d'Activité.
            </p>
            @can('backups.create')
            <form action="{{ route('admin.backups.create') }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Lancer une sauvegarde maintenant ?')">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Créer une sauvegarde
                </button>
            </form>
            @endcan
        </div>
        @endif
    </div>

    {{-- ── Note audit ───────────────────────────────────────────── --}}
    <div class="mt-3 p-3 rounded d-flex align-items-center gap-3 flex-wrap"
         style="background:rgba(10,77,140,.04);border:1px dashed #BFDBFE;font-size:12px;color:#1D4ED8;">
        <i class="fas fa-shield-alt"></i>
        <span>
            Toute opération de sauvegarde (création, téléchargement, suppression) est enregistrée dans le
            <a href="{{ route('admin.audit.index') }}" style="color:#0A4D8C;font-weight:600;">journal d'audit</a>
        </span>
    </div>

</div>
@endsection
