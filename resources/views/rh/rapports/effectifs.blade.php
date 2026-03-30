@extends('layouts.master')
@section('title', 'Rapport effectifs')
@section('page-title', 'Rapport Effectifs')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.rapports.index') }}" style="color:#1565C0;">Rapports</a></li>
    <li>Effectifs</li>
@endsection

@push('styles')
<style>
.filter-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;margin-bottom:4px;}
.kpi-mini{text-align:center;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:12px 16px;}
.kpi-mini .val{font-size:22px;font-weight:700;}
.kpi-mini .lbl{font-size:12px;color:#9CA3AF;margin-top:2px;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-users me-2" style="color:#059669;"></i>Rapport Effectifs
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Liste filtrée du personnel</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach([['total','Total','#374151'],['actifs','Actifs','#059669'],['en_conge','En congé','#1D4ED8'],['retraites','Retraités','#6B7280'],['suspendus','Suspendus','#DC2626']] as [$k,$l,$c])
        <div class="col-6 col-xl-2dot4">
            <div class="kpi-mini"><div class="val" style="color:{{ $c }};">{{ $stats[$k] }}</div><div class="lbl">{{ $l }}</div></div>
        </div>
        @endforeach
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form method="GET">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select name="service" class="form-select" style="width:auto;min-width:180px;">
                    <option value="">Tous les services</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id_service }}" {{ $serviceId == $svc->id_service ? 'selected' : '' }}>{{ $svc->nom_service }}</option>
                    @endforeach
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:150px;">
                    <option value="">Tous les statuts</option>
                    @foreach(['actif','en_conge','suspendu','retraite'] as $s)
                        <option value="{{ $s }}" {{ $statut == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['service', 'statut']))
                    <a href="{{ route('rh.rapports.effectifs') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <a href="{{ route('rh.absences.export') }}" class="btn btn-outline-success btn-sm ms-auto d-flex align-items-center gap-1" style="white-space:nowrap;">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>
        </form>
    </div>

    {{-- Répartition par catégorie --}}
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;margin-bottom:12px;">Répartition par catégorie</div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($parCategorie as $cat => $count)
            <span style="background:#EFF6FF;color:#0A4D8C;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                {{ str_replace('_', ' ', $cat) }} : {{ $count }}
            </span>
            @endforeach
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Agent</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Service</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Catégorie</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Contrat</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Statut</th>
                            <th class="border-0 py-3 px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;">Recrutement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                        <tr style="border-bottom:1px solid #F3F4F6;">
                            <td class="py-3 px-4 border-0">
                                <div style="font-weight:600;color:var(--theme-text);">{{ $agent->prenom }} {{ $agent->nom }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $agent->matricule }}</div>
                            </td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $agent->service?->nom_service ?? '—' }}</td>
                            <td class="py-3 px-4 border-0" style="font-size:12px;">{{ str_replace('_', ' ', $agent->categorie_cp) }}</td>
                            <td class="py-3 px-4 border-0">
                                @if($agent->contratActif)
                                <span style="font-size:11px;background:#EFF6FF;color:#1E40AF;padding:2px 8px;border-radius:20px;font-weight:600;">{{ $agent->contratActif->type_contrat }}</span>
                                @else
                                <span class="text-muted" style="font-size:12px;">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-0">
                                @php $sBg = ['actif'=>'#D1FAE5','en_conge'=>'#DBEAFE','suspendu'=>'#FEE2E2','retraite'=>'#F3F4F6']; @endphp
                                <span style="background:{{ $sBg[$agent->statut_agent] ?? '#F3F4F6' }};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;">
                                    {{ ucfirst(str_replace('_', ' ', $agent->statut_agent)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0 text-muted">{{ $agent->date_prise_service?->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted border-0">Aucun agent correspondant aux filtres.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($agents->hasPages())
        <div class="card-footer bg-transparent px-4 py-3">{{ $agents->links() }}</div>
        @endif
    </div>

</div>
@endsection
