@extends('layouts.master')
@section('title', 'Constructeur d\'Organigramme')
@section('page-title', 'Organigramme')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">DRH</a></li>
    <li>Organigramme</li>
@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   LAYOUT GÉNÉRAL
   ══════════════════════════════════════════════════════════════ */
#org-builder { display:flex;flex-direction:column;height:calc(100vh - 140px);overflow:hidden; }

/* ══════════════════════════════════════════════════════════════
   TOOLBAR
   ══════════════════════════════════════════════════════════════ */
#org-toolbar {
    display:flex;align-items:center;gap:10px;padding:10px 18px;
    background:var(--theme-panel-bg);border-bottom:1px solid var(--theme-border);
    flex-shrink:0;flex-wrap:wrap;
}
#inputTitre {
    flex:1;min-width:200px;max-width:360px;padding:7px 12px;
    border-radius:8px;border:1.5px solid var(--theme-border);
    background:var(--theme-bg-secondary);color:var(--theme-text);
    font-size:14px;font-weight:600;
}
#inputTitre:focus { outline:none;border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.1); }
.tb-btn {
    display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
    border-radius:8px;font-size:12.5px;font-weight:500;border:1.5px solid;
    cursor:pointer;transition:all 150ms;white-space:nowrap;
}
.tb-btn:hover { transform:translateY(-1px);box-shadow:0 4px 10px rgba(0,0,0,.1); }
.tb-btn.save   { background:#0A4D8C;color:#fff;border-color:#0A4D8C; }
.tb-btn.save:hover { background:#1565C0;border-color:#1565C0; }
.tb-btn.save.unsaved { background:#D97706;border-color:#D97706; }
.tb-btn.png    { background:#ECFDF5;color:#065F46;border-color:#A7F3D0; }
.tb-btn.png:hover { background:#D1FAE5; }
.tb-btn.pdf    { background:#FEF2F2;color:#991B1B;border-color:#FECACA; }
.tb-btn.pdf:hover { background:#FEE2E2; }
.tb-btn.reset  { background:var(--theme-bg-secondary);color:var(--theme-text-muted);border-color:var(--theme-border); }
.tb-btn.reset:hover { color:#DC2626;border-color:#FECACA; }
#saveStatus { font-size:12px;color:var(--theme-text-muted); }

/* ══════════════════════════════════════════════════════════════
   CORPS (palette + canvas)
   ══════════════════════════════════════════════════════════════ */
#org-body { display:flex;flex:1;overflow:hidden; }

/* ── PALETTE ──────────────────────────────────────────────────── */
#org-palette {
    width:240px;flex-shrink:0;border-right:1px solid var(--theme-border);
    background:var(--theme-panel-bg);overflow-y:auto;padding:14px;
    display:flex;flex-direction:column;gap:14px;
}
.pal-section-title {
    font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;
    color:var(--theme-text-muted);margin-bottom:6px;
}
.pal-node-btn {
    display:flex;align-items:center;gap:8px;padding:8px 10px;
    border-radius:8px;border:1.5px solid var(--theme-border);
    background:var(--theme-bg-secondary);cursor:pointer;
    font-size:12px;font-weight:500;color:var(--theme-text);
    transition:all 150ms;width:100%;text-align:left;
}
.pal-node-btn:hover { border-color:#0A4D8C;color:#0A4D8C;background:#EFF6FF; }
.pal-node-btn .pal-icon { width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0; }
.pal-service-item {
    display:flex;align-items:center;gap:6px;padding:6px 8px;
    border-radius:6px;font-size:11.5px;color:var(--theme-text);
    cursor:pointer;transition:background 120ms;
}
.pal-service-item:hover { background:#EFF6FF;color:#0A4D8C; }
.pal-service-item i { color:#059669;font-size:10px; }

/* ── CANVAS ───────────────────────────────────────────────────── */
#org-canvas {
    flex:1;overflow:auto;position:relative;
    background:var(--theme-bg-secondary);
    background-image: radial-gradient(var(--theme-border) 1px, transparent 1px);
    background-size: 24px 24px;
}
#org-canvas-inner {
    padding:60px 80px;min-width:max-content;min-height:100%;
    display:flex;justify-content:center;
}

/* ══════════════════════════════════════════════════════════════
   ORG CHART — ARBRE CSS
   ══════════════════════════════════════════════════════════════ */
.org-root { text-align:center; }

.org-ul {
    display:flex;align-items:flex-start;justify-content:center;
    padding-top:0;gap:0;
    list-style:none;padding-left:0;margin:0;
    position:relative;
}

/* Barre horizontale reliant les frères */
.org-ul::before {
    position:absolute;top:0;left:0;right:0;height:0;
    border-top:2px solid #CBD5E1;content:'';
}
/* Supprime la barre si enfant unique */
.org-ul:has(> li:only-child)::before { display:none; }

.org-li {
    list-style:none;padding:20px 10px 0;
    position:relative;text-align:center;
}

/* Ligne verticale montant vers la barre horizontale */
.org-li::before {
    content:'';position:absolute;top:0;left:50%;
    border-left:2px solid #CBD5E1;height:20px;width:0;
}

/* Raccourcis : barre horizontale commence / finit au milieu du premier/dernier enfant */
.org-ul > li:first-child { padding-left:0; }
.org-ul > li:last-child  { padding-right:0; }
.org-ul > li:first-child::before { left:50%; }
.org-ul > li:last-child::before  { left:50%; }

/* Connecteur vertical depuis le parent jusqu'à la barre */
.org-kids-wrap {
    padding-top:0;
    display:flex;flex-direction:column;align-items:center;
}
.org-connector-v {
    width:2px;height:24px;background:#CBD5E1;margin:0 auto;
}

/* ══════════════════════════════════════════════════════════════
   NODE CARD
   ══════════════════════════════════════════════════════════════ */
.org-card {
    display:inline-block;border-radius:12px;border:2px solid;
    padding:10px 14px;min-width:150px;max-width:210px;
    position:relative;cursor:default;
    transition:box-shadow 200ms,transform 200ms;
    text-align:center;
}
.org-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.12);transform:translateY(-2px); }
.org-card:hover .card-actions { opacity:1; }

.card-type-icon {
    width:32px;height:32px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    font-size:14px;margin:0 auto 6px;
}
.card-label {
    font-size:12.5px;font-weight:700;line-height:1.3;
    word-break:break-word;color:#111827;
}
.card-subtitle {
    font-size:10.5px;margin-top:3px;color:#6B7280;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;
}
.card-actions {
    display:flex;justify-content:center;gap:4px;margin-top:8px;
    opacity:0;transition:opacity 150ms;
}
.card-actions .ca-btn {
    width:24px;height:24px;border-radius:6px;display:flex;align-items:center;
    justify-content:center;font-size:10px;border:1px solid;cursor:pointer;
    transition:all 120ms;background:none;
}
.ca-btn.add  { border-color:#A7F3D0;color:#059669; }
.ca-btn.add:hover  { background:#ECFDF5; }
.ca-btn.edit { border-color:#BFDBFE;color:#1D4ED8; }
.ca-btn.edit:hover { background:#EFF6FF; }
.ca-btn.move { border-color:#DDD6FE;color:#7C3AED; }
.ca-btn.move:hover { background:#F5F3FF; }
.ca-btn.del  { border-color:#FECACA;color:#DC2626; }
.ca-btn.del:hover  { background:#FEF2F2; }

/* Node type colors */
.nt-institution { border-color:#0A4D8C!important;background:#EFF6FF!important; }
.nt-institution .card-label { color:#0A4D8C; }
.nt-institution .card-type-icon { background:#DBEAFE;color:#1D4ED8; }

.nt-direction { border-color:#7C3AED!important;background:#F5F3FF!important; }
.nt-direction .card-label { color:#5B21B6; }
.nt-direction .card-type-icon { background:#EDE9FE;color:#7C3AED; }

.nt-service { border-color:#059669!important;background:#ECFDF5!important; }
.nt-service .card-label { color:#065F46; }
.nt-service .card-type-icon { background:#D1FAE5;color:#059669; }

.nt-division { border-color:#D97706!important;background:#FFFBEB!important; }
.nt-division .card-label { color:#92400E; }
.nt-division .card-type-icon { background:#FEF3C7;color:#D97706; }

.nt-poste { border-color:#0891B2!important;background:#F0F9FF!important; }
.nt-poste .card-label { color:#0E7490; }
.nt-poste .card-type-icon { background:#E0F2FE;color:#0891B2; }

.nt-custom { border-color:#9CA3AF!important;background:#F9FAFB!important; }
.nt-custom .card-label { color:#374151; }
.nt-custom .card-type-icon { background:#F3F4F6;color:#6B7280; }

/* ══════════════════════════════════════════════════════════════
   MODAUX
   ══════════════════════════════════════════════════════════════ */
.modal-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);margin-bottom:5px; }
.modal-input { border-radius:8px;font-size:13px;border:1.5px solid var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);padding:8px 12px;width:100%;transition:border-color 150ms; }
.modal-input:focus { outline:none;border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.1); }

/* Node type selector */
.type-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:8px; }
.type-opt {
    border:2px solid var(--theme-border);border-radius:8px;padding:10px 6px;
    text-align:center;cursor:pointer;font-size:11px;font-weight:500;
    transition:all 150ms;background:var(--theme-bg-secondary);
}
.type-opt:hover,.type-opt.active { border-color:#0A4D8C;background:#EFF6FF;color:#0A4D8C; }
.type-opt .type-opt-icon { font-size:18px;display:block;margin-bottom:4px; }

/* Legende */
#org-legend {
    display:flex;align-items:center;flex-wrap:wrap;gap:12px;
    padding:10px 18px;border-top:1px solid var(--theme-border);
    background:var(--theme-panel-bg);font-size:11.5px;
    color:var(--theme-text-muted);flex-shrink:0;
}
.leg-dot { width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:4px; }

/* Zoom controls */
#zoom-controls {
    position:absolute;bottom:20px;right:20px;
    display:flex;flex-direction:column;gap:4px;z-index:10;
}
.zoom-btn {
    width:32px;height:32px;border-radius:8px;
    background:var(--theme-panel-bg);border:1px solid var(--theme-border);
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;font-size:14px;color:var(--theme-text);
    transition:all 150ms;
}
.zoom-btn:hover { background:#EFF6FF;color:#0A4D8C;border-color:#BFDBFE; }
#zoom-level { font-size:10px;font-weight:700;text-align:center;color:var(--theme-text-muted); }

/* Toast */
@keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
</style>
@endpush

@section('content')
<div id="org-builder">

    {{-- ── TOOLBAR ──────────────────────────────────────────────────── --}}
    <div id="org-toolbar">
        <i class="fas fa-project-diagram" style="color:#0A4D8C;font-size:18px;"></i>
        <input type="text" id="inputTitre" value="{{ $org->titre ?? 'Organigramme du CHNP' }}"
               placeholder="Titre de l'organigramme…">
        <div style="height:24px;width:1px;background:var(--theme-border);"></div>
        <button class="tb-btn save" id="btnSave" onclick="saveOrgChart()">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <button class="tb-btn png" onclick="downloadPNG()">
            <i class="fas fa-image"></i> PNG
        </button>
        <button class="tb-btn pdf" onclick="downloadPDF()">
            <i class="fas fa-file-pdf"></i> PDF
        </button>
        <div style="flex:1;"></div>
        <span id="saveStatus" style="font-size:12px;color:var(--theme-text-muted);">
            @if($org->updated_at ?? false)
                ✓ Enregistré le {{ $org->updated_at->format('d/m/Y à H:i') }}
            @else
                Non enregistré
            @endif
        </span>
        <a href="{{ route('drh.organigramme.reinitialiser') }}" class="tb-btn reset"
           onclick="return confirm('Réinitialiser l\'organigramme à partir de la structure actuelle du SIRH ?')">
            <i class="fas fa-redo"></i> Réinitialiser
        </a>
    </div>

    {{-- ── CORPS ─────────────────────────────────────────────────────── --}}
    <div id="org-body">

        {{-- PALETTE --}}
        <div id="org-palette">

            <div>
                <div class="pal-section-title">Ajouter un nœud</div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    @php
                    $paletteTypes = [
                        ['institution', 'fa-building',    '#1D4ED8', '#DBEAFE', 'Institution'],
                        ['direction',   'fa-sitemap',     '#7C3AED', '#EDE9FE', 'Direction'],
                        ['service',     'fa-hospital-alt','#059669', '#D1FAE5', 'Service'],
                        ['division',    'fa-layer-group', '#D97706', '#FEF3C7', 'Division'],
                        ['poste',       'fa-id-badge',    '#0891B2', '#E0F2FE', 'Poste'],
                        ['custom',      'fa-cube',        '#6B7280', '#F3F4F6', 'Texte libre'],
                    ];
                    @endphp
                    @foreach($paletteTypes as [$type, $icon, $color, $bg, $label])
                    <button class="pal-node-btn" onclick="openAddFromPalette('{{ $type }}', '{{ $label }}')">
                        <span class="pal-icon" style="background:{{ $bg }};color:{{ $color }};">
                            <i class="fas {{ $icon }}"></i>
                        </span>
                        <span>{{ $label }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            @if($services->count() > 0)
            <div>
                <div class="pal-section-title">Services du SIRH</div>
                <div style="display:flex;flex-direction:column;gap:2px;max-height:220px;overflow-y:auto;">
                    @foreach($services as $service)
                    <div class="pal-service-item"
                         onclick="addServiceNode({{ $service->id_service }}, @json($service->nom_service), @json($service->type_service))">
                        <i class="fas fa-hospital-alt"></i>
                        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $service->nom_service }}
                        </span>
                        @if($service->divisions->count() > 0)
                        <span style="font-size:10px;background:#ECFDF5;color:#065F46;border-radius:10px;padding:1px 6px;">
                            {{ $service->divisions->count() }}
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="padding:10px;background:var(--theme-bg-secondary);border-radius:8px;font-size:11.5px;color:var(--theme-text-muted);">
                <i class="fas fa-lightbulb me-1" style="color:#D97706;"></i>
                <strong>Astuce :</strong> Survolez un nœud pour voir les actions (modifier, déplacer, supprimer, ajouter un enfant).
            </div>

        </div>

        {{-- CANVAS --}}
        <div id="org-canvas">
            <div id="org-canvas-inner" style="transform-origin:top center;" id="zoomable">
                {{-- L'organigramme est rendu ici par JS --}}
                <div id="orgChartContent" class="org-root"></div>
            </div>

            {{-- Zoom controls --}}
            <div id="zoom-controls">
                <button class="zoom-btn" onclick="zoomIn()" title="Zoom +"><i class="fas fa-plus"></i></button>
                <div id="zoom-level">100%</div>
                <button class="zoom-btn" onclick="zoomOut()" title="Zoom -"><i class="fas fa-minus"></i></button>
                <button class="zoom-btn" onclick="zoomReset()" title="Réinitialiser zoom"><i class="fas fa-compress-arrows-alt"></i></button>
            </div>
        </div>
    </div>

    {{-- LÉGENDE --}}
    <div id="org-legend">
        <span style="font-weight:600;margin-right:4px;">Légende :</span>
        <span><span class="leg-dot" style="background:#1D4ED8;"></span>Institution</span>
        <span><span class="leg-dot" style="background:#7C3AED;"></span>Direction</span>
        <span><span class="leg-dot" style="background:#059669;"></span>Service</span>
        <span><span class="leg-dot" style="background:#D97706;"></span>Division</span>
        <span><span class="leg-dot" style="background:#0891B2;"></span>Poste</span>
        <span><span class="leg-dot" style="background:#9CA3AF;"></span>Personnalisé</span>
        <span style="margin-left:auto;font-size:11px;">
            {{ $totalAgents }} agents actifs ·
            Généré le {{ now()->format('d/m/Y') }}
        </span>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — AJOUTER / MODIFIER UN NŒUD
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="nodeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border-radius:14px;background:var(--theme-panel-bg);">
            <div style="padding:22px 24px 0;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0" id="modalNodeTitle" style="color:var(--theme-text);">Nouveau nœud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <div class="mb-3">
                    <label class="modal-label">Type de nœud <span class="text-danger">*</span></label>
                    <div class="type-grid" id="typeGrid">
                        @php
                        $typeOptions = [
                            ['institution', 'fa-building',    'Institution'],
                            ['direction',   'fa-sitemap',     'Direction'],
                            ['service',     'fa-hospital-alt','Service'],
                            ['division',    'fa-layer-group', 'Division'],
                            ['poste',       'fa-id-badge',    'Poste'],
                            ['custom',      'fa-cube',        'Libre'],
                        ];
                        @endphp
                        @foreach($typeOptions as [$val, $ico, $lbl])
                        <div class="type-opt" data-value="{{ $val }}" onclick="selectType('{{ $val }}')">
                            <span class="type-opt-icon"><i class="fas {{ $ico }}"></i></span>
                            {{ $lbl }}
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="inputType" value="custom">
                </div>
                <div class="mb-3">
                    <label class="modal-label">Nom / Libellé <span class="text-danger">*</span></label>
                    <input type="text" id="inputLabel" class="modal-input" placeholder="ex: Service de Chirurgie">
                    <div id="labelError" style="color:#DC2626;font-size:12px;margin-top:4px;display:none;">
                        Le nom est obligatoire.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="modal-label">Sous-titre / Description</label>
                    <input type="text" id="inputSousTitre" class="modal-input" placeholder="ex: Clinique · 12 agents">
                </div>
            </div>
            <div style="padding:0 24px 22px;display:flex;gap:10px;justify-content:flex-end;border-top:1px solid var(--theme-border);padding-top:16px;margin-top:4px;">
                <button type="button" class="tb-btn reset" data-bs-dismiss="modal" style="padding:8px 14px;">Annuler</button>
                <button type="button" class="tb-btn save" onclick="saveNodeModal()" style="padding:8px 16px;">
                    <i class="fas fa-check"></i> Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — DÉPLACER UN NŒUD
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="moveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;background:var(--theme-panel-bg);">
            <div style="padding:20px 22px 0;">
                <h5 class="fw-bold mb-1" style="color:var(--theme-text);">
                    <i class="fas fa-arrows-alt me-2" style="color:#7C3AED;"></i>Déplacer le nœud
                </h5>
                <p style="font-size:12.5px;color:var(--theme-text-muted);">
                    Nœud : <strong id="moveNodeName"></strong>
                </p>
            </div>
            <div style="padding:0 22px 10px;">
                <label class="modal-label">Nouveau parent</label>
                <select id="moveParentSelect" class="modal-input"></select>
            </div>
            <div style="padding:12px 22px 20px;display:flex;gap:10px;justify-content:flex-end;border-top:1px solid var(--theme-border);">
                <button type="button" class="tb-btn reset" data-bs-dismiss="modal" style="padding:7px 14px;">Annuler</button>
                <button type="button" class="tb-btn save" onclick="saveMoveModal()" style="padding:7px 14px;background:#7C3AED;border-color:#7C3AED;">
                    <i class="fas fa-check"></i> Déplacer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
/* ══════════════════════════════════════════════════════════════
   CONFIGURATION
   ══════════════════════════════════════════════════════════════ */
const CSRF = '{{ csrf_token() }}';
const ROUTE_SAVE = '{{ route("drh.organigramme.sauvegarder") }}';

const NODE_TYPES = {
    institution: { icon:'fa-building',    label:'Institution', class:'nt-institution' },
    direction:   { icon:'fa-sitemap',     label:'Direction',   class:'nt-direction'   },
    service:     { icon:'fa-hospital-alt',label:'Service',     class:'nt-service'     },
    division:    { icon:'fa-layer-group', label:'Division',    class:'nt-division'    },
    poste:       { icon:'fa-id-badge',    label:'Poste',       class:'nt-poste'       },
    custom:      { icon:'fa-cube',        label:'Personnalisé',class:'nt-custom'      },
};

/* ══════════════════════════════════════════════════════════════
   ÉTAT
   ══════════════════════════════════════════════════════════════ */
let treeData = @json($org->donnees_json ?? $org->donnees_json);
let currentEditId   = null;
let currentAddParentId = null;
let zoomScale = 1;

/* ══════════════════════════════════════════════════════════════
   UTILITAIRES ARBRE
   ══════════════════════════════════════════════════════════════ */
function genId() {
    return 'n_' + Date.now() + '_' + Math.random().toString(36).substr(2,6);
}

function findNode(node, id) {
    if (!node) return null;
    if (node.id === id) return node;
    if (node.children) {
        for (const c of node.children) {
            const f = findNode(c, id);
            if (f) return f;
        }
    }
    return null;
}

function findParent(node, id) {
    if (!node || !node.children) return null;
    for (const c of node.children) {
        if (c.id === id) return node;
        const f = findParent(c, id);
        if (f) return f;
    }
    return null;
}

// Récupère tous les nœuds (sauf exclusion)
function getAllNodes(node, exclude = null, result = []) {
    if (!node) return result;
    if (node.id !== exclude) {
        result.push({ id: node.id, label: node.label, depth: result.length });
        if (node.children) {
            node.children.forEach(c => getAllNodes(c, exclude, result));
        }
    }
    return result;
}

/* ══════════════════════════════════════════════════════════════
   RENDU DE L'ARBRE
   ══════════════════════════════════════════════════════════════ */
function renderTree() {
    const container = document.getElementById('orgChartContent');
    container.innerHTML = '';
    container.appendChild(buildNodeEl(treeData, true));
}

function buildNodeEl(node, isRoot) {
    const cfg = NODE_TYPES[node.type] || NODE_TYPES.custom;

    // Wrapper nœud
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'display:flex;flex-direction:column;align-items:center;';

    // Card
    const card = document.createElement('div');
    card.className = 'org-card ' + cfg.class;
    card.setAttribute('data-id', node.id);
    card.innerHTML = `
        <div class="card-type-icon"><i class="fas ${cfg.icon}"></i></div>
        <div class="card-label">${escHtml(node.label)}</div>
        ${node.sous_titre ? `<div class="card-subtitle" title="${escHtml(node.sous_titre)}">${escHtml(node.sous_titre)}</div>` : ''}
        <div class="card-actions">
            <button class="ca-btn add"  onclick="openAddModal('${node.id}')"  title="Ajouter un enfant"><i class="fas fa-plus"></i></button>
            <button class="ca-btn edit" onclick="openEditModal('${node.id}')" title="Modifier"><i class="fas fa-pen"></i></button>
            ${!isRoot ? `
            <button class="ca-btn move" onclick="openMoveModal('${node.id}')" title="Déplacer"><i class="fas fa-arrows-alt"></i></button>
            <button class="ca-btn del"  onclick="deleteNode('${node.id}')"    title="Supprimer"><i class="fas fa-trash"></i></button>
            ` : ''}
        </div>
    `;
    wrapper.appendChild(card);

    // Enfants
    if (node.children && node.children.length > 0) {
        const connV = document.createElement('div');
        connV.className = 'org-connector-v';
        wrapper.appendChild(connV);

        const kidsWrap = document.createElement('div');
        kidsWrap.style.cssText = 'display:flex;align-items:flex-start;justify-content:center;gap:0;position:relative;';

        // Barre horizontale
        if (node.children.length > 1) {
            const hBar = document.createElement('div');
            hBar.className = 'org-hbar';
            hBar.style.cssText = 'position:absolute;top:0;left:0;right:0;height:2px;background:#CBD5E1;';
            kidsWrap.appendChild(hBar);
        }

        node.children.forEach(child => {
            const childWrapper = document.createElement('div');
            childWrapper.style.cssText = 'display:flex;flex-direction:column;align-items:center;padding:0 10px;position:relative;';

            // Ligne verticale
            const vLine = document.createElement('div');
            vLine.style.cssText = 'width:2px;height:24px;background:#CBD5E1;flex-shrink:0;';
            childWrapper.appendChild(vLine);
            childWrapper.appendChild(buildNodeEl(child, false));
            kidsWrap.appendChild(childWrapper);
        });

        wrapper.appendChild(kidsWrap);
    }

    return wrapper;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

/* ══════════════════════════════════════════════════════════════
   OPÉRATIONS CRUD
   ══════════════════════════════════════════════════════════════ */

// ── AJOUTER ──────────────────────────────────────────────────────
function openAddModal(parentId) {
    currentEditId      = null;
    currentAddParentId = parentId;
    document.getElementById('modalNodeTitle').textContent = 'Ajouter un nœud enfant';
    document.getElementById('inputLabel').value     = '';
    document.getElementById('inputSousTitre').value = '';
    document.getElementById('labelError').style.display = 'none';
    selectType('custom');
    new bootstrap.Modal(document.getElementById('nodeModal')).show();
}

function openAddFromPalette(type, label) {
    currentEditId      = null;
    currentAddParentId = treeData.id; // ajoute à la racine par défaut
    document.getElementById('modalNodeTitle').textContent = 'Ajouter un nœud';
    document.getElementById('inputLabel').value     = '';
    document.getElementById('inputSousTitre').value = label;
    document.getElementById('labelError').style.display = 'none';
    selectType(type);
    new bootstrap.Modal(document.getElementById('nodeModal')).show();
}

function addServiceNode(serviceId, nomService, typeService) {
    // Vérifie si déjà présent
    if (findNode(treeData, 'service_' + serviceId)) {
        showToast('Ce service est déjà dans l\'organigramme.', 'warning');
        return;
    }
    const node = {
        id:         'service_' + serviceId,
        label:      nomService,
        sous_titre: typeService,
        type:       'service',
        children:   [],
    };
    if (!treeData.children) treeData.children = [];
    treeData.children.push(node);
    renderTree();
    markUnsaved();
    showToast('Service "' + nomService + '" ajouté.', 'success');
}

// ── MODIFIER ─────────────────────────────────────────────────────
function openEditModal(nodeId) {
    currentEditId      = nodeId;
    currentAddParentId = null;
    const node = findNode(treeData, nodeId);
    if (!node) return;
    document.getElementById('modalNodeTitle').textContent = 'Modifier le nœud';
    document.getElementById('inputLabel').value     = node.label;
    document.getElementById('inputSousTitre').value = node.sous_titre || '';
    document.getElementById('labelError').style.display = 'none';
    selectType(node.type || 'custom');
    new bootstrap.Modal(document.getElementById('nodeModal')).show();
}

function saveNodeModal() {
    const label     = document.getElementById('inputLabel').value.trim();
    const sousTitre = document.getElementById('inputSousTitre').value.trim();
    const type      = document.getElementById('inputType').value;

    if (!label) {
        document.getElementById('labelError').style.display = 'block';
        document.getElementById('inputLabel').focus();
        return;
    }
    document.getElementById('labelError').style.display = 'none';

    if (currentEditId) {
        const node = findNode(treeData, currentEditId);
        if (node) { node.label = label; node.sous_titre = sousTitre; node.type = type; }
    } else if (currentAddParentId) {
        const parent = findNode(treeData, currentAddParentId);
        if (parent) {
            if (!parent.children) parent.children = [];
            parent.children.push({ id: genId(), label, sous_titre: sousTitre, type, children: [] });
        }
    }

    bootstrap.Modal.getInstance(document.getElementById('nodeModal')).hide();
    renderTree();
    markUnsaved();
}

// ── SUPPRIMER ────────────────────────────────────────────────────
function deleteNode(nodeId) {
    const node = findNode(treeData, nodeId);
    if (!node) return;
    const nbEnfants = countDescendants(node);
    const msg = nbEnfants > 0
        ? `Supprimer "${node.label}" et ses ${nbEnfants} sous-nœud(s) ?`
        : `Supprimer "${node.label}" ?`;
    if (!confirm(msg)) return;

    const parent = findParent(treeData, nodeId);
    if (parent && parent.children) {
        parent.children = parent.children.filter(c => c.id !== nodeId);
    }
    renderTree();
    markUnsaved();
}

function countDescendants(node) {
    if (!node.children || node.children.length === 0) return 0;
    return node.children.reduce((sum, c) => sum + 1 + countDescendants(c), 0);
}

// ── DÉPLACER ─────────────────────────────────────────────────────
function openMoveModal(nodeId) {
    const node = findNode(treeData, nodeId);
    if (!node) return;
    currentEditId = nodeId;
    document.getElementById('moveNodeName').textContent = node.label;

    const sel = document.getElementById('moveParentSelect');
    sel.innerHTML = '';
    // Tous les nœuds sauf le nœud lui-même et ses descendants
    const excluded = getSubtreeIds(node);
    populateMoveOptions(treeData, sel, excluded, '');
    new bootstrap.Modal(document.getElementById('moveModal')).show();
}

function getSubtreeIds(node) {
    const ids = [node.id];
    if (node.children) node.children.forEach(c => ids.push(...getSubtreeIds(c)));
    return ids;
}

function populateMoveOptions(node, sel, excluded, prefix) {
    if (excluded.includes(node.id)) return;
    const opt = document.createElement('option');
    opt.value       = node.id;
    opt.textContent = prefix + node.label;
    sel.appendChild(opt);
    if (node.children) {
        node.children.forEach(c => populateMoveOptions(c, sel, excluded, prefix + '  ↳ '));
    }
}

function saveMoveModal() {
    const newParentId = document.getElementById('moveParentSelect').value;
    const nodeId = currentEditId;
    if (!newParentId || !nodeId) return;

    const nodeClone = JSON.parse(JSON.stringify(findNode(treeData, nodeId)));
    const oldParent = findParent(treeData, nodeId);
    if (oldParent && oldParent.children) {
        oldParent.children = oldParent.children.filter(c => c.id !== nodeId);
    }
    const newParent = findNode(treeData, newParentId);
    if (newParent) {
        if (!newParent.children) newParent.children = [];
        newParent.children.push(nodeClone);
    }
    bootstrap.Modal.getInstance(document.getElementById('moveModal')).hide();
    renderTree();
    markUnsaved();
    showToast('Nœud déplacé.', 'success');
}

/* ══════════════════════════════════════════════════════════════
   SÉLECTEUR DE TYPE
   ══════════════════════════════════════════════════════════════ */
function selectType(value) {
    document.getElementById('inputType').value = value;
    document.querySelectorAll('.type-opt').forEach(el => {
        el.classList.toggle('active', el.dataset.value === value);
    });
}

/* ══════════════════════════════════════════════════════════════
   ZOOM
   ══════════════════════════════════════════════════════════════ */
function applyZoom() {
    document.getElementById('org-canvas-inner').style.transform = `scale(${zoomScale})`;
    document.getElementById('org-canvas-inner').style.transformOrigin = 'top center';
    document.getElementById('zoom-level').textContent = Math.round(zoomScale * 100) + '%';
}
function zoomIn()    { zoomScale = Math.min(zoomScale + 0.15, 2.5); applyZoom(); }
function zoomOut()   { zoomScale = Math.max(zoomScale - 0.15, 0.3); applyZoom(); }
function zoomReset() { zoomScale = 1; applyZoom(); }

// Zoom molette
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('org-canvas').addEventListener('wheel', (e) => {
        if (!e.ctrlKey) return;
        e.preventDefault();
        e.deltaY < 0 ? zoomIn() : zoomOut();
    }, { passive: false });
});

/* ══════════════════════════════════════════════════════════════
   SAUVEGARDE
   ══════════════════════════════════════════════════════════════ */
function markUnsaved() {
    document.getElementById('btnSave').classList.add('unsaved');
    document.getElementById('saveStatus').textContent = '● Non enregistré';
}

async function saveOrgChart() {
    const titre = document.getElementById('inputTitre').value.trim() || 'Organigramme du CHNP';
    const btn   = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement…';

    try {
        const resp = await fetch(ROUTE_SAVE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ titre, donnees_json: treeData }),
        });
        const data = await resp.json();
        if (resp.ok) {
            btn.classList.remove('unsaved');
            document.getElementById('saveStatus').textContent = '✓ Enregistré à ' + new Date().toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'});
            showToast('Organigramme enregistré.', 'success');
        } else {
            showToast('Erreur lors de l\'enregistrement.', 'error');
        }
    } catch(e) {
        showToast('Erreur réseau.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
    }
}

/* ══════════════════════════════════════════════════════════════
   EXPORT
   ══════════════════════════════════════════════════════════════ */
async function downloadPNG() {
    showToast('Génération PNG…', 'info');
    const target = document.getElementById('orgChartContent');
    const prevZoom = zoomScale;
    zoomScale = 1; applyZoom();

    await new Promise(r => setTimeout(r, 200)); // laisser le temps au reflow

    const canvas = await html2canvas(target, {
        backgroundColor: '#ffffff',
        scale: 2,
        logging: false,
        useCORS: true,
    });

    zoomScale = prevZoom; applyZoom();

    const link = document.createElement('a');
    const titre = document.getElementById('inputTitre').value.trim().replace(/[^a-z0-9]/gi, '-') || 'organigramme-chnp';
    link.download = titre + '-' + new Date().toISOString().slice(0,10) + '.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
    showToast('PNG téléchargé.', 'success');
}

async function downloadPDF() {
    showToast('Génération PDF…', 'info');
    const target = document.getElementById('orgChartContent');
    const prevZoom = zoomScale;
    zoomScale = 1; applyZoom();

    await new Promise(r => setTimeout(r, 200));

    const canvas = await html2canvas(target, {
        backgroundColor: '#ffffff',
        scale: 2,
        logging: false,
        useCORS: true,
    });

    zoomScale = prevZoom; applyZoom();

    const { jsPDF } = window.jspdf;
    const w = canvas.width / 2;
    const h = canvas.height / 2;
    const orientation = w > h ? 'landscape' : 'portrait';
    const pdf = new jsPDF({ orientation, unit: 'px', format: [w, h] });
    pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, w, h);
    const titre = document.getElementById('inputTitre').value.trim().replace(/[^a-z0-9]/gi, '-') || 'organigramme-chnp';
    pdf.save(titre + '-' + new Date().toISOString().slice(0,10) + '.pdf');
    showToast('PDF téléchargé.', 'success');
}

/* ══════════════════════════════════════════════════════════════
   TOAST
   ══════════════════════════════════════════════════════════════ */
function showToast(msg, type) {
    const colors = { success:'#10B981', error:'#EF4444', info:'#3B82F6', warning:'#D97706' };
    const icons  = { success:'fa-check-circle', error:'fa-exclamation-circle', info:'fa-info-circle', warning:'fa-exclamation-triangle' };
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;
            background:${colors[type]||colors.info};color:#fff;border-radius:12px;
            padding:12px 18px;display:flex;align-items:center;gap:10px;
            box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:13px;font-weight:500;
            max-width:380px;animation:toastIn .3s ease;">
            <i class="fas ${icons[type]||icons.info}" style="font-size:16px;flex-shrink:0;"></i>
            <span>${msg}</span>
            <button onclick="document.getElementById('${id}').remove()"
                    style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;margin-left:auto;">×</button>
        </div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 3500);
}

/* ══════════════════════════════════════════════════════════════
   INIT
   ══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    if (!treeData || !treeData.id) {
        treeData = {
            id: 'node_root',
            label: 'Centre Hospitalier National de Pikine',
            sous_titre: 'Établissement Public de Santé de Niveau 3',
            type: 'institution',
            children: [],
        };
    }
    renderTree();

    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
});
</script>
@endpush
