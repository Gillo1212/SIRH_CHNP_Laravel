@extends('layouts.master')
@section('title', 'GED — Gestion Électronique de Documents')
@section('page-title', 'GED — Gestion Électronique de Documents')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>GED</li>
@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════
   GED — DASHBOARD
   ══════════════════════════════════════════════════════════ */

/* KPI */
.ged-kpi { border-radius:14px;padding:22px 26px;border:1px solid #F3F4F6;transition:all 200ms;position:relative;overflow:hidden; }
.ged-kpi:hover { box-shadow:0 8px 24px rgba(10,77,140,.10);transform:translateY(-2px); }
.ged-kpi .kpi-icon { width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0; }
.ged-kpi .kpi-val { font-size:32px;font-weight:700;line-height:1;margin-top:14px; }
.ged-kpi .kpi-lbl { font-size:13px;color:#6B7280;font-weight:500;margin-top:3px; }
.ged-kpi::after { content:'';position:absolute;top:-30px;right:-30px;width:100px;height:100px;border-radius:50%;opacity:.06; }
.ged-kpi.blue  .kpi-icon { background:#EFF6FF;color:#1D4ED8; }
.ged-kpi.blue::after  { background:#1D4ED8; }
.ged-kpi.green .kpi-icon { background:#ECFDF5;color:#059669; }
.ged-kpi.green::after { background:#059669; }
.ged-kpi.amber .kpi-icon { background:#FFFBEB;color:#D97706; }
.ged-kpi.amber::after { background:#D97706; }
.ged-kpi.red   .kpi-icon { background:#FEF2F2;color:#DC2626; }
.ged-kpi.red::after { background:#DC2626; }
.ged-kpi.purple .kpi-icon { background:#F5F3FF;color:#7C3AED; }
.ged-kpi.purple::after { background:#7C3AED; }
.ged-kpi.teal  .kpi-icon { background:#F0FDFA;color:#0F766E; }
.ged-kpi.teal::after  { background:#0F766E; }

/* ACTIONS BAR */
.ged-toolbar { background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
.ged-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.ged-btn-primary  { background:#0A4D8C;color:#fff; }
.ged-btn-primary:hover  { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.ged-btn-outline  { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.ged-btn-outline:hover  { background:#F9FAFB;border-color:#D1D5DB; }

/* ÉTAGÈRE CARDS */
.etagere-card { background:#fff;border:1px solid #E5E7EB;border-radius:14px;overflow:hidden;transition:all 200ms;cursor:pointer; }
.etagere-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10);transform:translateY(-2px); }
.etagere-card .ec-header { padding:20px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;gap:14px; }
.etagere-card .ec-icon { width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);display:flex;align-items:center;justify-content:center;font-size:20px;color:#1D4ED8;flex-shrink:0; }
.etagere-card .ec-body { padding:16px 20px; }
.etagere-card .ec-stat { display:flex;justify-content:space-between;font-size:13px;color:#6B7280;margin-bottom:8px; }
.etagere-card .ec-stat strong { color:#111827;font-weight:600; }

/* RECENT DOCS TABLE */
.doc-row { transition:background 150ms; }
.doc-row:hover { background:#F9FAFB !important; }
.doc-icon-cell { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0; }
.badge-confidentialite { font-size:11px;padding:3px 8px;border-radius:20px;font-weight:600; }
.badge-conf-public       { background:#ECFDF5;color:#059669; }
.badge-conf-interne      { background:#EFF6FF;color:#1D4ED8; }
.badge-conf-confidentiel { background:#FFFBEB;color:#D97706; }
.badge-conf-secret       { background:#FEF2F2;color:#DC2626; }

/* CHARTS */
.chart-card { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:22px; }
.section-hd  { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6B7280;margin-bottom:14px; }

/* SEARCH BAR */
.ged-search { flex:1;min-width:220px;height:38px;border:1px solid #E5E7EB;border-radius:8px;padding:0 14px 0 38px;font-size:14px;background:#F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 12px center;outline:none; }
.ged-search:focus { border-color:#0A4D8C;background-color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- ── TOOLBAR ────────────────────────────────────────────── --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-between">
            <form action="{{ route('rh.ged.search') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="Rechercher un document, titre, référence…">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>
            <a href="{{ route('rh.ged.documents.create') }}" class="ged-btn ged-btn-primary ms-3">
                <i class="ri-upload-cloud-2-line"></i> Déposer un document
            </a>
        </div>
    </div>

    {{-- ── KPI ────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="ged-kpi blue">
                <div class="kpi-icon"><i class="ri-file-text-line"></i></div>
                <div class="kpi-val">{{ number_format($stats['total_documents']) }}</div>
                <div class="kpi-lbl">Documents actifs</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="ged-kpi green">
                <div class="kpi-icon"><i class="ri-folder-open-line"></i></div>
                <div class="kpi-val">{{ number_format($stats['total_dossiers']) }}</div>
                <div class="kpi-lbl">Dossiers agents</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="ged-kpi teal">
                <div class="kpi-icon"><i class="ri-archive-drawer-line"></i></div>
                <div class="kpi-val">{{ number_format($stats['total_etageres']) }}</div>
                <div class="kpi-lbl">Étagères</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="ged-kpi amber">
                <div class="kpi-icon"><i class="ri-archive-line"></i></div>
                <div class="kpi-val">{{ number_format($stats['docs_archives']) }}</div>
                <div class="kpi-lbl">Archivés</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="ged-kpi red">
                <div class="kpi-icon"><i class="ri-spy-line"></i></div>
                <div class="kpi-val">{{ number_format($stats['docs_confidentiels']) }}</div>
                <div class="kpi-lbl">Confidentiels</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="ged-kpi purple">
                <div class="kpi-icon"><i class="ri-calendar-line"></i></div>
                <div class="kpi-val">{{ number_format($stats['docs_recents']) }}</div>
                <div class="kpi-lbl">Ce mois-ci</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── COL GAUCHE : Charts + Étagères ────────────────── --}}
        <div class="col-xl-4">

            {{-- Répartition par type --}}
            <div class="chart-card mb-4">
                <div class="section-hd"><i class="ri-bar-chart-2-line me-1"></i> Répartition par type</div>
                <div id="chartType" style="min-height:240px;"></div>
            </div>

            {{-- Répartition par confidentialité --}}
            <div class="chart-card mb-4">
                <div class="section-hd"><i class="ri-lock-line me-1"></i> Niveau de confidentialité</div>
                <div id="chartConf" style="min-height:200px;"></div>
            </div>

            {{-- Étagères --}}
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-hd mb-0"><i class="ri-archive-drawer-line me-1"></i> Étagères</div>
                    <a href="{{ route('rh.ged.etageres') }}" class="ged-btn ged-btn-outline" style="padding:5px 12px;font-size:12px;">Gérer</a>
                </div>
                @forelse($etageres as $et)
                    <a href="{{ route('rh.ged.etageres') }}" class="text-decoration-none">
                        <div class="etagere-card mb-2">
                            <div class="ec-header">
                                <div class="ec-icon"><i class="ri-archive-drawer-line"></i></div>
                                <div>
                                    <div class="fw-600" style="font-size:14px;color:#111827;">{{ $et->nom_etagere }}</div>
                                    <div style="font-size:12px;color:#6B7280;">{{ $et->service?->nom_service ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="ec-body">
                                <div class="ec-stat">
                                    <span>Dossiers</span>
                                    <strong>{{ $et->dossiers_count }}</strong>
                                </div>
                                <div class="progress" style="height:4px;border-radius:2px;">
                                    <div class="progress-bar bg-primary" style="width:{{ min(100, $et->dossiers_count * 5) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted py-3" style="font-size:13px;">
                        <i class="ri-archive-drawer-line d-block mb-1" style="font-size:24px;"></i>
                        Aucune étagère configurée
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ── COL DROITE : Documents récents ─────────────────── --}}
        <div class="col-xl-8">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-hd mb-0"><i class="ri-time-line me-1"></i> Documents récents</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('rh.ged.dossiers') }}" class="ged-btn ged-btn-outline" style="padding:5px 12px;font-size:12px;">
                            <i class="ri-folder-3-line"></i> Tous les dossiers
                        </a>
                        <a href="{{ route('rh.ged.documents.create') }}" class="ged-btn ged-btn-primary" style="padding:5px 12px;font-size:12px;">
                            <i class="ri-upload-2-line"></i> Déposer
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size:13px;">
                        <thead>
                            <tr style="background:#F9FAFB;">
                                <th class="border-0 py-2 ps-3" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Document</th>
                                <th class="border-0 py-2" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Agent</th>
                                <th class="border-0 py-2" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Type</th>
                                <th class="border-0 py-2" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Confidentialité</th>
                                <th class="border-0 py-2" style="color:#6B7280;font-weight:600;font-size:11px;text-transform:uppercase;">Date</th>
                                <th class="border-0 py-2 pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($docsRecents as $doc)
                            <tr class="doc-row border-top">
                                <td class="py-2 ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <x-ged-file-icon :format="$doc->format_fichier ?? ''" :size="34"/>
                                        <div>
                                            <div class="fw-500" style="color:#111827;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $doc->titre }}</div>
                                            <div style="font-size:11px;color:#9CA3AF;">{{ $doc->reference }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2" style="color:#374151;">
                                    {{ $doc->dossier?->agent?->nom_complet ?? '—' }}
                                </td>
                                <td class="py-2">
                                    <span class="badge rounded-pill" style="background:{{ $doc->type_info['color'] }}18;color:{{ $doc->type_info['color'] }};font-size:11px;">
                                        {{ $doc->type_info['label'] }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    @php $ci = $doc->confidentialite_info; @endphp
                                    <span class="badge-confidentialite badge-conf-{{ strtolower($doc->niveau_confidentialite) }}">
                                        {{ $ci['label'] }}
                                    </span>
                                </td>
                                <td class="py-2" style="color:#6B7280;white-space:nowrap;">
                                    {{ $doc->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-2 pe-3">
                                    {{-- Voir : SVG inline bleu --}}
                                    <a href="{{ route('rh.ged.documents.show', $doc->id_document) }}"
                                       class="btn btn-sm btn-light" style="color:#1D4ED8;" title="Voir le document">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ri-file-text-line d-block mb-2" style="font-size:32px;opacity:.3;"></i>
                                    Aucun document archivé pour le moment
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($docsRecents->isNotEmpty())
                <div class="mt-3 text-center">
                    <a href="{{ route('rh.ged.search') }}" class="ged-btn ged-btn-outline" style="font-size:13px;">
                        Voir tous les documents <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3/dist/apexcharts.min.js"></script>
<script>
// Chart répartition par type
const typeData = @json($parType);
const typeLabels = Object.keys(typeData);
const typeVals   = Object.values(typeData);

const typeColors = {
    'Contrat':'#1D4ED8','Attestation':'#059669','Décision':'#DC2626',
    'Ordre_mission':'#D97706','Nomination':'#7C3AED','PV':'#0891B2',
    'Domiciliation':'#6B7280','Diplome':'#B45309','Certificat_medical':'#BE123C',
    'Fiche_evaluation':'#0E7490','Piece_identite':'#374151','Autre':'#9CA3AF'
};

new ApexCharts(document.getElementById('chartType'), {
    chart: { type: 'bar', height: 240, toolbar: { show: false }, sparkline: { enabled: false } },
    series: [{ name: 'Documents', data: typeVals }],
    xaxis: { categories: typeLabels.map(t => t.replace('_',' ')), labels: { style: { fontSize: '10px' } } },
    colors: typeLabels.map(t => typeColors[t] || '#6B7280'),
    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%', distributed: true } },
    legend: { show: false },
    dataLabels: { enabled: false },
    grid: { borderColor: '#F3F4F6' },
    tooltip: { y: { formatter: v => v + ' doc(s)' } }
}).render();

// Chart confidentialité
const confData = @json($parConfidentialite);
const confLabels = Object.keys(confData);
const confVals   = Object.values(confData);
const confColors = { 'Public':'#059669','Interne':'#1D4ED8','Confidentiel':'#D97706','Secret':'#DC2626' };

new ApexCharts(document.getElementById('chartConf'), {
    chart: { type: 'donut', height: 200, toolbar: { show: false } },
    series: confVals,
    labels: confLabels,
    colors: confLabels.map(l => confColors[l] || '#6B7280'),
    legend: { position: 'bottom', fontSize: '12px' },
    dataLabels: { enabled: true, formatter: (v) => Math.round(v) + '%' },
    plotOptions: { pie: { donut: { size: '60%' } } },
    tooltip: { y: { formatter: v => v + ' doc(s)' } }
}).render();
</script>
@endpush
