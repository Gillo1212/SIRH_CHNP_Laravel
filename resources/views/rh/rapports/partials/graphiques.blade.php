{{-- ── Constructeur de graphiques ────────────────────────────────────────── --}}
<div x-data="chartBuilder()" x-init="load()" class="row g-3">

    {{-- ═══════════════════════════════════════════════════
         COLONNE GAUCHE — Panneau outils (accordéon)
    ═══════════════════════════════════════════════════ --}}
    <div class="col-12 col-xl-3">

        {{-- ── Accordéon 1 : Source de données ── --}}
        <div class="panel p-0 mb-3 overflow-hidden">
            <button class="d-flex align-items-center justify-content-between w-100 px-4 py-3"
                style="background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#0A4D8C;"
                @click="open.source = !open.source">
                <span><i class="fas fa-database me-2" style="color:#0A4D8C;"></i>Source de données</span>
                <i class="fas" :class="open.source ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size:11px;color:#9CA3AF;"></i>
            </button>
            <div x-show="open.source" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="border-top:1px solid #F3F4F6;">
                <div class="px-3 py-2">
                    @php
                    $sources = [
                        'Effectifs' => [
                            ['effectifs_par_service',   'fas fa-hospital',      'Par service'],
                            ['effectifs_par_categorie', 'fas fa-layer-group',   'Par catégorie CSP'],
                            ['effectifs_par_statut',    'fas fa-toggle-on',     'Par statut'],
                            ['effectifs_par_sexe',      'fas fa-venus-mars',    'Répartition H/F'],
                        ],
                        'Absences' => [
                            ['absences_par_type',       'fas fa-procedures',    'Par type'],
                            ['absences_par_mois',       'fas fa-calendar-times','Par mois (12m)'],
                        ],
                        'Contrats' => [
                            ['contrats_par_statut',     'fas fa-file-contract', 'Par statut'],
                            ['contrats_par_type',       'fas fa-file-alt',      'Par type'],
                        ],
                        'Mouvements' => [
                            ['mouvements_par_type',     'fas fa-exchange-alt',  'Par type'],
                            ['mouvements_par_mois',     'fas fa-history',       'Par mois (12m)'],
                        ],
                    ];
                    @endphp

                    @foreach($sources as $group => $items)
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9CA3AF;padding:8px 6px 4px;">{{ $group }}</div>
                    @foreach($items as [$val, $icon, $label])
                    <button type="button"
                        @click="source='{{ $val }}'; load()"
                        :class="source === '{{ $val }}' ? 'source-btn active' : 'source-btn'"
                        class="source-btn w-100 text-start mb-1">
                        <i class="{{ $icon }} me-2" style="width:14px;text-align:center;"></i>{{ $label }}
                    </button>
                    @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Accordéon 2 : Type de graphique ── --}}
        <div class="panel p-0 mb-3 overflow-hidden">
            <button class="d-flex align-items-center justify-content-between w-100 px-4 py-3"
                style="background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#374151;"
                @click="open.type = !open.type">
                <span><i class="fas fa-chart-bar me-2" style="color:#374151;"></i>Type de graphique</span>
                <i class="fas" :class="open.type ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size:11px;color:#9CA3AF;"></i>
            </button>
            <div x-show="open.type" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="border-top:1px solid #F3F4F6;">
                <div class="px-3 py-3">
                    <div class="row g-2">
                        @foreach([
                            ['bar',         'fas fa-chart-bar',     'Barres'],
                            ['horizontalBar','fas fa-align-left',   'H. Barres'],
                            ['line',        'fas fa-chart-line',    'Courbe'],
                            ['doughnut',    'fas fa-circle-notch',  'Donut'],
                            ['pie',         'fas fa-chart-pie',     'Camembert'],
                            ['radar',       'fas fa-bullseye',      'Radar'],
                            ['polarArea',   'fas fa-compass',       'Polaire'],
                        ] as [$v,$ico,$lbl])
                        <div class="col-6">
                            <button type="button"
                                @click="chartType='{{ $v }}'; render()"
                                :class="chartType === '{{ $v }}' ? 'chart-type-btn active' : 'chart-type-btn'"
                                class="chart-type-btn w-100">
                                <i class="{{ $ico }}" style="font-size:16px;margin-bottom:4px;display:block;"></i>
                                <span style="font-size:11px;">{{ $lbl }}</span>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Accordéon 3 : Apparence ── --}}
        <div class="panel p-0 mb-3 overflow-hidden">
            <button class="d-flex align-items-center justify-content-between w-100 px-4 py-3"
                style="background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#374151;"
                @click="open.style = !open.style">
                <span><i class="fas fa-palette me-2" style="color:#374151;"></i>Apparence</span>
                <i class="fas" :class="open.style ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size:11px;color:#9CA3AF;"></i>
            </button>
            <div x-show="open.style" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="border-top:1px solid #F3F4F6;">
                <div class="px-4 py-3">
                    <div class="mb-3">
                        <label style="font-size:11px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Palette</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach([
                                ['medical',  ['#0A4D8C','#1565C0','#10B981','#F59E0B','#EF4444','#8B5CF6']],
                                ['warm',     ['#EF4444','#F97316','#F59E0B','#EAB308','#84CC16','#22C55E']],
                                ['cool',     ['#0EA5E9','#3B82F6','#6366F1','#8B5CF6','#EC4899','#14B8A6']],
                                ['mono',     ['#111827','#374151','#6B7280','#9CA3AF','#D1D5DB','#E5E7EB']],
                            ] as [$pid,$colors])
                            <button type="button" @click="palette='{{ $pid }}'; render()"
                                :class="palette === '{{ $pid }}' ? 'palette-btn active' : 'palette-btn'"
                                class="palette-btn" title="{{ ucfirst($pid) }}" style="display:flex;gap:2px;padding:4px 8px;border-radius:6px;">
                                @foreach(array_slice($colors,0,4) as $c)
                                <span style="width:10px;height:10px;border-radius:2px;background:{{ $c }};display:inline-block;"></span>
                                @endforeach
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label style="font-size:11px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Options</label>
                        <label class="d-flex align-items-center gap-2" style="font-size:12px;color:#374151;cursor:pointer;">
                            <input type="checkbox" x-model="showLegend" @change="render()" style="accent-color:#0A4D8C;">
                            Afficher la légende
                        </label>
                        <label class="d-flex align-items-center gap-2 mt-2" style="font-size:12px;color:#374151;cursor:pointer;">
                            <input type="checkbox" x-model="showGrid" @change="render()" style="accent-color:#0A4D8C;">
                            Afficher la grille
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Accordéon 4 : Export ── --}}
        <div class="panel p-0 overflow-hidden">
            <button class="d-flex align-items-center justify-content-between w-100 px-4 py-3"
                style="background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;color:#374151;"
                @click="open.export = !open.export">
                <span><i class="fas fa-download me-2" style="color:#374151;"></i>Exporter</span>
                <i class="fas" :class="open.export ? 'fa-chevron-up' : 'fa-chevron-down'" style="font-size:11px;color:#9CA3AF;"></i>
            </button>
            <div x-show="open.export" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="border-top:1px solid #F3F4F6;">
                <div class="px-4 py-3 d-grid gap-2">
                    <button type="button" @click="dlPNG()"
                        :disabled="!inst"
                        class="btn btn-sm" style="background:#0A4D8C;color:#fff;border:none;border-radius:8px;font-size:12.5px;">
                        <i class="fas fa-image me-1"></i> Télécharger PNG
                    </button>
                    <button type="button" @click="dlSVG()"
                        :disabled="!rows.length"
                        class="btn btn-sm" style="background:#F3F4F6;color:#374151;border:none;border-radius:8px;font-size:12.5px;">
                        <i class="fas fa-file-image me-1"></i> Télécharger SVG
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /col gauche --}}

    {{-- ═══════════════════════════════════════════════════
         COLONNE DROITE — Zone de rendu
    ═══════════════════════════════════════════════════ --}}
    <div class="col-12 col-xl-9">

        {{-- En-tête du graphique --}}
        <div class="panel mb-3 py-3" style="border-radius:12px;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div x-show="label" x-text="label" style="font-size:15px;font-weight:700;color:#111827;"></div>
                    <div x-show="!label" style="font-size:15px;font-weight:700;color:#9CA3AF;">Sélectionnez une source…</div>
                    <div x-show="rows.length" style="font-size:12px;color:#9CA3AF;margin-top:2px;">
                        <span x-text="rows.length"></span> entrées ·
                        Total : <span x-text="rows.reduce((s,r)=>s+r.v,0)"></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Spinner --}}
                    <div x-show="loading" class="spinner-border spinner-border-sm text-primary" style="width:18px;height:18px;"></div>
                    {{-- Type actif --}}
                    <span x-show="!loading && label" style="font-size:11px;background:#EFF6FF;color:#1E40AF;padding:2px 10px;border-radius:20px;font-weight:600;" x-text="typeLabel"></span>
                </div>
            </div>
        </div>

        {{-- Zone canvas --}}
        <div class="panel mb-3" style="min-height:380px;display:flex;align-items:center;justify-content:center;">
            {{-- Placeholder --}}
            <div x-show="!loading && !inst" class="text-center py-5" style="color:#D1D5DB;">
                <i class="fas fa-chart-bar" style="font-size:3.5rem;opacity:.3;display:block;margin-bottom:12px;"></i>
                <p style="font-size:14px;color:#9CA3AF;margin:0;">Choisissez une source et un type de graphique</p>
            </div>
            {{-- Erreur --}}
            <div x-show="err" class="alert alert-danger py-2 px-3 w-100" style="font-size:13px;margin:0;">
                <i class="fas fa-exclamation-circle me-2"></i><span x-text="err"></span>
            </div>
            {{-- Canvas --}}
            <div x-show="!loading && inst && !err" style="width:100%;position:relative;">
                <canvas id="cbCanvas" style="max-height:380px;"></canvas>
            </div>
        </div>

        {{-- Tableau récapitulatif --}}
        <div class="panel" x-show="rows.length > 0">
            <div class="fw-600 mb-3" style="color:#111827;font-size:14px;">Tableau récapitulatif</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size:13px;">
                    <thead style="background:#F9FAFB;">
                        <tr>
                            <th class="border-0 py-2 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Libellé</th>
                            <th class="border-0 py-2 px-3 text-end" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Valeur</th>
                            <th class="border-0 py-2 px-3" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;width:40%;">Part</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="r in rows" :key="r.l">
                            <tr style="border-bottom:1px solid #F3F4F6;">
                                <td class="border-0 py-2 px-3">
                                    <span class="me-2" :style="'display:inline-block;width:10px;height:10px;border-radius:2px;background:'+r.c"></span>
                                    <span x-text="r.l"></span>
                                </td>
                                <td class="border-0 py-2 px-3 text-end fw-600" x-text="r.v"></td>
                                <td class="border-0 py-2 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="flex:1;background:#F3F4F6;border-radius:4px;height:6px;overflow:hidden;">
                                            <div :style="'height:100%;width:'+r.p+'%;background:'+r.c+';border-radius:4px;'"></div>
                                        </div>
                                        <span style="font-size:11px;color:#6B7280;width:32px;text-align:right;" x-text="r.p+'%'"></span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr style="background:#F9FAFB;">
                            <td class="border-0 py-2 px-3 fw-700">Total</td>
                            <td class="border-0 py-2 px-3 text-end fw-700" x-text="rows.reduce((s,r)=>s+r.v,0)"></td>
                            <td class="border-0 py-2 px-3 fw-700">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>{{-- /col droite --}}
</div>

@push('styles')
<style>
.source-btn{background:transparent;border:none;border-radius:7px;padding:6px 10px;font-size:12.5px;color:#374151;cursor:pointer;transition:all 140ms;}
.source-btn:hover{background:#F3F4F6;}
.source-btn.active{background:#EFF6FF;color:#0A4D8C;font-weight:600;}
.chart-type-btn{background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:8px;padding:10px 6px;font-size:12px;color:#374151;cursor:pointer;transition:all 140ms;text-align:center;}
.chart-type-btn:hover{border-color:#93C5FD;background:#EFF6FF;color:#0A4D8C;}
.chart-type-btn.active{background:#0A4D8C;border-color:#0A4D8C;color:#fff;}
.palette-btn{background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:7px;cursor:pointer;transition:border-color 140ms;}
.palette-btn:hover{border-color:#93C5FD;}
.palette-btn.active{border-color:#0A4D8C;box-shadow:0 0 0 2px rgba(10,77,140,.15);}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const PALETTES = {
    medical: ['#0A4D8C','#1565C0','#10B981','#F59E0B','#EF4444','#8B5CF6','#3B82F6','#EC4899','#14B8A6','#F97316','#6366F1','#84CC16'],
    warm:    ['#EF4444','#F97316','#F59E0B','#EAB308','#84CC16','#22C55E','#10B981','#14B8A6','#06B6D4','#0EA5E9','#3B82F6','#6366F1'],
    cool:    ['#0EA5E9','#3B82F6','#6366F1','#8B5CF6','#EC4899','#14B8A6','#06B6D4','#22C55E','#84CC16','#F59E0B','#F97316','#EF4444'],
    mono:    ['#111827','#1F2937','#374151','#4B5563','#6B7280','#9CA3AF','#D1D5DB','#E5E7EB','#F3F4F6','#F9FAFB','#374151','#6B7280'],
};
const TYPE_LABELS = {
    bar:'Barres', horizontalBar:'Barres H.', line:'Courbe',
    doughnut:'Donut', pie:'Camembert', radar:'Radar', polarArea:'Polaire'
};

function chartBuilder() {
    return {
        source: 'effectifs_par_service',
        chartType: 'bar',
        palette: 'medical',
        showLegend: true,
        showGrid: true,
        loading: false,
        err: null,
        inst: null,
        label: '',
        rows: [],
        open: { source: true, type: true, style: false, export: false },

        get typeLabel() { return TYPE_LABELS[this.chartType] || ''; },

        load() {
            this.loading = true; this.err = null;
            const url = '{{ route("rh.rapports.chart-data") }}?source=' + this.source;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(d => {
                    if (d.error) throw new Error(d.error);
                    this.label = d.label;
                    const cols = PALETTES[this.palette];
                    const total = d.data.reduce((a,b)=>a+b,0) || 1;
                    this.rows = d.labels.map((l,i) => ({
                        l, v: d.data[i], c: cols[i % cols.length],
                        p: Math.round(d.data[i] / total * 100),
                    }));
                    this.renderWith(d.labels, d.data);
                })
                .catch(e => { this.err = e.message; this.loading = false; });
        },

        render() {
            if (!this.rows.length) return;
            this.renderWith(this.rows.map(r=>r.l), this.rows.map(r=>r.v));
        },

        renderWith(labels, data) {
            if (this.inst) { this.inst.destroy(); this.inst = null; }
            const cols   = PALETTES[this.palette];
            const colors = labels.map((_,i) => cols[i % cols.length]);
            const isHBar = this.chartType === 'horizontalBar';
            const type   = isHBar ? 'bar' : this.chartType;
            const isLinear = type === 'line' || type === 'bar';
            const total  = data.reduce((a,b)=>a+b,0) || 1;

            const dataset = {
                label: this.label, data,
                backgroundColor: isLinear ? colors.map(c=>c+'CC') : colors,
                borderColor: isLinear ? colors : '#fff',
                borderWidth: isLinear ? 1 : 2,
                ...(type === 'line' ? { fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: colors[0] } : {}),
            };

            this.inst = new Chart(document.getElementById('cbCanvas'), {
                type,
                data: { labels, datasets: [dataset] },
                options: {
                    responsive: true, animation: { duration: 400 },
                    indexAxis: isHBar ? 'y' : 'x',
                    plugins: {
                        legend: { display: this.showLegend && !isLinear, position: 'bottom', labels: { font:{ size:11 }, color:'#6B7280', padding:8, boxWidth:10 } },
                        tooltip: { callbacks: { label: ctx => ' ' + ctx.formattedValue + (!isLinear ? ' (' + Math.round(ctx.parsed/total*100) + '%)' : '') } }
                    },
                    ...(isLinear ? { scales: {
                        x: { ticks:{ color:'#9CA3AF', font:{ size: isHBar?11:10 } }, grid:{ display: this.showGrid && isHBar, color:'#F3F4F6' } },
                        y: { ticks:{ color:'#9CA3AF', font:{ size: isHBar?10:11 } }, beginAtZero:true, grid:{ display: this.showGrid && !isHBar, color:'#F3F4F6' } },
                    }} : {}),
                    ...(type === 'doughnut' ? { cutout: '58%' } : {}),
                }
            });
            this.loading = false;
        },

        dlPNG() {
            if (!this.inst) return;
            const a = document.createElement('a');
            a.download = this.source + '_' + new Date().toISOString().slice(0,10) + '.png';
            a.href = this.inst.toBase64Image('image/png', 1); a.click();
        },

        dlSVG() {
            if (!this.rows.length) return;
            const W=640, barH=26, pad=6, lblW=190, barArea=W-lblW-70;
            const maxV = Math.max(...this.rows.map(r=>r.v)) || 1;
            const svgRows = this.rows.map((r,i) => {
                const y = 40 + i*(barH+pad), w = Math.round(r.v/maxV*barArea);
                return `<text x="${lblW-8}" y="${y+barH/2+4}" text-anchor="end" font-size="12" fill="#374151" font-family="Inter,sans-serif">${r.l}</text>
<rect x="${lblW}" y="${y}" width="${w}" height="${barH}" fill="${r.c}" rx="3"/>
<text x="${lblW+w+6}" y="${y+barH/2+4}" font-size="12" fill="#6B7280" font-family="Inter,sans-serif">${r.v}</text>`;
            }).join('\n');
            const H = 60 + this.rows.length*(barH+pad);
            const svg=`<svg xmlns="http://www.w3.org/2000/svg" width="${W}" height="${H}">
<text x="10" y="22" font-size="15" font-weight="700" fill="#0A4D8C" font-family="Inter,sans-serif">${this.label}</text>
${svgRows}</svg>`;
            const a = document.createElement('a');
            a.download = this.source + '_' + new Date().toISOString().slice(0,10) + '.svg';
            a.href = URL.createObjectURL(new Blob([svg],{type:'image/svg+xml'})); a.click();
        },
    };
}
</script>
@endpush
