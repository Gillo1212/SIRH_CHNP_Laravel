{{-- Sélecteur mois/année --}}
<div class="bg-white rounded shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('rh.rapports.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
        <input type="hidden" name="view" value="mensuel">
        <select name="mois" class="form-select" style="width:auto;min-width:140px;">
            @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ $m == $mois ? 'selected' : '' }}>
                {{ \Carbon\Carbon::createFromDate($annee, $m, 1)->isoFormat('MMMM') }}
            </option>
            @endfor
        </select>
        <select name="annee" class="form-select" style="width:auto;min-width:100px;">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
            <option value="{{ $y }}" {{ $y == $annee ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
            <i class="fas fa-filter"></i> Afficher
        </button>
    </form>
</div>

{{-- KPIs --}}
<div class="section-card mb-4">
    <div class="section-title">Vue d'ensemble — {{ $moisLabel }}</div>
    <div class="row g-0">
        <div class="col-4 border-end"><div class="kpi-stat"><div class="val" style="color:#EF4444;">{{ $statsMois['total_absences'] }}</div><div class="lbl">Absences</div></div></div>
        <div class="col-4 border-end"><div class="kpi-stat"><div class="val" style="color:#3B82F6;">{{ $statsMois['total_conges'] }}</div><div class="lbl">Congés</div></div></div>
        <div class="col-4"><div class="kpi-stat"><div class="val" style="color:#7C3AED;">{{ $statsMois['total_mouvements'] }}</div><div class="lbl">Mouvements</div></div></div>
    </div>
</div>

{{-- Absences --}}
<div class="section-card">
    <div class="section-title">
        Absences ({{ $statsMois['total_absences'] }})
        <span class="ms-2" style="color:#059669;font-size:10px;">{{ $statsMois['absences_justif'] }} justifiées</span>
        <span class="ms-2" style="color:#EF4444;font-size:10px;">{{ $statsMois['absences_injustif'] }} non justifiées</span>
    </div>
    @if($absences->isNotEmpty())
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
        @foreach($absByType as $type => $count)
        <span style="background:#F3F4F6;color:#374151;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
            {{ $type }}: {{ $count }}
        </span>
        @endforeach
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size:13px;">
            <thead><tr style="background:#F9FAFB;">
                <th class="border-0 py-2 px-3">Agent</th>
                <th class="border-0 py-2 px-3">Service</th>
                <th class="border-0 py-2 px-3">Date</th>
                <th class="border-0 py-2 px-3">Type</th>
                <th class="border-0 py-2 px-3">Justifiée</th>
            </tr></thead>
            <tbody>
                @foreach($absences->take(20) as $abs)
                @php $agent = $abs->demande?->agent; @endphp
                <tr>
                    <td class="py-2 px-3 border-0 fw-600">{{ $agent?->nom_complet ?? '—' }}</td>
                    <td class="py-2 px-3 border-0 text-muted">{{ $agent?->service?->nom_service ?? '—' }}</td>
                    <td class="py-2 px-3 border-0">{{ $abs->date_absence?->format('d/m/Y') }}</td>
                    <td class="py-2 px-3 border-0">{{ $abs->type_absence }}</td>
                    <td class="py-2 px-3 border-0">
                        @if($abs->justifie)
                        <span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">Oui</span>
                        @else
                        <span style="background:#FEE2E2;color:#991B1B;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">Non</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-muted" style="font-size:13px;">Aucune absence enregistrée pour cette période.</p>
    @endif
</div>

{{-- Congés --}}
<div class="section-card">
    <div class="section-title">Congés ({{ $statsMois['total_conges'] }})</div>
    @if($conges->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size:13px;">
            <thead><tr style="background:#F9FAFB;">
                <th class="border-0 py-2 px-3">Agent</th>
                <th class="border-0 py-2 px-3">Service</th>
                <th class="border-0 py-2 px-3">Demande</th>
                <th class="border-0 py-2 px-3">Statut</th>
            </tr></thead>
            <tbody>
                @foreach($conges->take(20) as $d)
                <tr>
                    <td class="py-2 px-3 border-0 fw-600">{{ $d->agent?->nom_complet ?? '—' }}</td>
                    <td class="py-2 px-3 border-0 text-muted">{{ $d->agent?->service?->nom_service ?? '—' }}</td>
                    <td class="py-2 px-3 border-0">{{ $d->created_at?->format('d/m/Y') }}</td>
                    <td class="py-2 px-3 border-0">
                        <span style="font-size:11px;padding:2px 8px;border-radius:20px;font-weight:600;background:#F3F4F6;color:#374151;">
                            {{ str_replace('_', ' ', $d->statut_demande) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-muted" style="font-size:13px;">Aucun congé pour cette période.</p>
    @endif
</div>

{{-- Mouvements --}}
<div class="section-card">
    <div class="section-title">Mouvements ({{ $statsMois['total_mouvements'] }})</div>
    @if($mouvements->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size:13px;">
            <thead><tr style="background:#F9FAFB;">
                <th class="border-0 py-2 px-3">Agent</th>
                <th class="border-0 py-2 px-3">Type</th>
                <th class="border-0 py-2 px-3">Service</th>
                <th class="border-0 py-2 px-3">Statut</th>
            </tr></thead>
            <tbody>
                @foreach($mouvements as $mouv)
                <tr>
                    <td class="py-2 px-3 border-0 fw-600">{{ $mouv->agent?->nom_complet ?? '—' }}</td>
                    <td class="py-2 px-3 border-0">{{ $mouv->type_mouvement }}</td>
                    <td class="py-2 px-3 border-0 text-muted">{{ $mouv->agent?->service?->nom_service ?? '—' }}</td>
                    <td class="py-2 px-3 border-0">
                        <span style="font-size:11px;padding:2px 8px;border-radius:20px;font-weight:600;background:#F3F4F6;color:#374151;">
                            {{ $mouv->statut }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-muted" style="font-size:13px;">Aucun mouvement pour cette période.</p>
    @endif
</div>
