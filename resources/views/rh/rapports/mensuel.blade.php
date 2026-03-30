@extends('layouts.master')
@section('title', 'Rapport mensuel — ' . $moisLabel)
@section('page-title', 'Rapport mensuel')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.rapports.index') }}" style="color:#1565C0;">Rapports</a></li>
    <li>Mensuel</li>
@endsection

@push('styles')
<style>
.section-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:16px;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;}
.kpi-stat{text-align:center;padding:16px;}
.kpi-stat .val{font-size:26px;font-weight:700;}
.kpi-stat .lbl{font-size:12px;color:#9CA3AF;margin-top:3px;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header avec sélecteur mois --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                    <i class="fas fa-calendar-alt me-2" style="color:#0A4D8C;"></i>Rapport mensuel
                </h4>
                <p class="mb-0 text-muted" style="font-size:13.5px;">{{ $moisLabel }}</p>
            </div>
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
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
    </div>

    {{-- KPIs --}}
    <div class="section-card mb-4">
        <div class="section-title">Vue d'ensemble — {{ $moisLabel }}</div>
        <div class="row g-0">
            <div class="col-4 border-end"><div class="kpi-stat"><div class="val" style="color:#EF4444;">{{ $stats['total_absences'] }}</div><div class="lbl">Absences</div></div></div>
            <div class="col-4 border-end"><div class="kpi-stat"><div class="val" style="color:#3B82F6;">{{ $stats['total_conges'] }}</div><div class="lbl">Congés</div></div></div>
            <div class="col-4"><div class="kpi-stat"><div class="val" style="color:#7C3AED;">{{ $stats['total_mouvements'] }}</div><div class="lbl">Mouvements</div></div></div>
        </div>
    </div>

    {{-- Absences --}}
    <div class="section-card">
        <div class="section-title">
            Absences ({{ $stats['total_absences'] }})
            <span class="ms-2" style="color:#059669;font-size:10px;">{{ $stats['absences_justif'] }} justifiées</span>
            <span class="ms-2" style="color:#EF4444;font-size:10px;">{{ $stats['absences_injustif'] }} non justifiées</span>
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
        <div class="section-title">Congés ({{ $stats['total_conges'] }})</div>
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
        <div class="section-title">Mouvements ({{ $stats['total_mouvements'] }})</div>
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
                    @foreach($mouvements as $m)
                    <tr>
                        <td class="py-2 px-3 border-0 fw-600">{{ $m->agent?->nom_complet ?? '—' }}</td>
                        <td class="py-2 px-3 border-0">{{ $m->type_mouvement }}</td>
                        <td class="py-2 px-3 border-0 text-muted">{{ $m->agent?->service?->nom_service ?? '—' }}</td>
                        <td class="py-2 px-3 border-0">
                            <span style="font-size:11px;padding:2px 8px;border-radius:20px;font-weight:600;background:#F3F4F6;color:#374151;">
                                {{ $m->statut }}
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

</div>
@endsection
