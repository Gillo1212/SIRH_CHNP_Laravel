{{-- ── Ligne 1 : Absences ──────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;font-size:14px;">Évolution des absences</div>
                    <div style="font-size:12px;color:#9CA3AF;">12 derniers mois</div>
                </div>
                <button onclick="dlChart('chartAbsences','absences_evolution')"
                    class="btn btn-sm" style="background:#F3F4F6;color:#374151;border:none;border-radius:8px;font-size:12px;"
                    title="Télécharger PNG">
                    <i class="fas fa-download me-1"></i>PNG
                </button>
            </div>
            <canvas id="chartAbsences" style="max-height:260px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="panel h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600" style="color:#111827;font-size:14px;">Types d'absences</div>
                    <div style="font-size:12px;color:#9CA3AF;">{{ now()->year }}</div>
                </div>
                <button onclick="dlChart('chartAbsTypes','absences_types')"
                    class="btn btn-sm" style="background:#F3F4F6;color:#374151;border:none;border-radius:8px;font-size:12px;"
                    title="Télécharger PNG">
                    <i class="fas fa-download me-1"></i>PNG
                </button>
            </div>
            <canvas id="chartAbsTypes" style="max-height:260px;"></canvas>
        </div>
    </div>
</div>

{{-- ── Ligne 2 : Contrats + Effectifs par service ──────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-4">
        <div class="panel h-100">
            <div class="fw-600 mb-3" style="color:#111827;font-size:14px;">Contrats</div>
            @foreach([
                ['actifs',   'Actifs',           '#059669','#ECFDF5','#D1FAE5'],
                ['expiring', 'Expirant < 60 j',  '#D97706','#FFFBEB','#FEF3C7'],
                ['expires',  'Expirés',           '#DC2626','#FEF2F2','#FEE2E2'],
                ['clotured', 'Clôturés',          '#6B7280','#F9FAFB','#F3F4F6'],
            ] as [$k,$l,$c,$bg,$badge])
            <div class="data-row">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $c }};display:inline-block;"></span>
                    <span style="font-size:13px;color:#374151;">{{ $l }}</span>
                </div>
                <span style="font-weight:700;color:{{ $c }};font-size:17px;">{{ $statsContrats[$k] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="panel h-100">
            <div class="fw-600 mb-3" style="color:#111827;font-size:14px;">Effectifs par service <span style="font-size:11px;color:#9CA3AF;font-weight:400;">(top 10 actifs)</span></div>
            @forelse($effParService as $svc)
            @php $max = $effParService->max('actifs') ?: 1; $pct = round($svc->actifs / $max * 100); @endphp
            <div class="d-flex align-items-center gap-3 mb-2">
                <div style="font-size:12px;color:#374151;width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex-shrink:0;">{{ $svc->nom_service }}</div>
                <div class="flex-grow-1" style="background:#F3F4F6;border-radius:4px;height:8px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#0A4D8C,#1565C0);border-radius:4px;transition:width .6s ease;"></div>
                </div>
                <span style="font-weight:700;color:#0A4D8C;width:28px;text-align:right;font-size:13px;">{{ $svc->actifs }}</span>
            </div>
            @empty
            <p class="text-muted mb-0" style="font-size:13px;">Aucun service avec des agents actifs.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Ligne 3 : Mouvements ────────────────────────────────── --}}
<div class="panel">
    <div class="fw-600 mb-3" style="color:#111827;font-size:14px;">
        Mouvements ce mois
        <span style="font-size:11px;color:#9CA3AF;font-weight:400;margin-left:6px;">{{ now()->isoFormat('MMMM YYYY') }}</span>
    </div>
    @if($mouvMois->isNotEmpty())
    <div class="d-flex flex-wrap gap-3">
        @foreach($mouvMois as $type => $count)
        <div style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:14px 22px;text-align:center;min-width:100px;">
            <div style="font-size:24px;font-weight:700;color:#0A4D8C;">{{ $count }}</div>
            <div style="font-size:12px;color:#6B7280;margin-top:2px;">{{ $type }}</div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-muted mb-0" style="font-size:13px;"><i class="fas fa-check-circle me-1" style="color:#D1D5DB;"></i>Aucun mouvement enregistré ce mois.</p>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const _c = {};
    _c['chartAbsences'] = new Chart(document.getElementById('chartAbsences'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Absences', data: @json($absParMois),
                borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,.1)',
                borderWidth: 2, fill: true, tension: 0.35, pointRadius: 3, pointBackgroundColor: '#EF4444',
            }]
        },
        options: {
            responsive: true, animation: { duration: 600 },
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#9CA3AF', font: { size: 10 } }, grid: { display: false } },
                y: { ticks: { color: '#9CA3AF', font: { size: 11 } }, beginAtZero: true, grid: { color: '#F3F4F6' } }
            }
        }
    });

    _c['chartAbsTypes'] = new Chart(document.getElementById('chartAbsTypes'), {
        type: 'doughnut',
        data: {
            labels: @json($absByType->keys()->values()),
            datasets: [{ data: @json($absByType->values()), backgroundColor: ['#EF4444','#F59E0B','#3B82F6','#8B5CF6','#10B981'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, cutout: '58%', animation: { duration: 600 },
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#6B7280', padding: 8, boxWidth: 10 } } }
        }
    });

    window.dlChart = function(id, name) {
        const c = _c[id]; if (!c) return;
        const a = document.createElement('a');
        a.download = name + '_' + new Date().toISOString().slice(0,10) + '.png';
        a.href = c.toBase64Image('image/png', 1); a.click();
    };
})();
</script>
@endpush
