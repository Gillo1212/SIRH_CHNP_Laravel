@extends('layouts.master')
@section('title', 'Organigramme CHNP')
@section('page-title', 'Organigramme')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>Organigramme</li>
@endsection

@push('styles')
<style>
.org-division{background:#fff;border:1px solid #E5E7EB;border-radius:12px;margin-bottom:24px;overflow:hidden;}
.org-division-header{background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 100%);color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;}
.org-service{background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:16px;transition:box-shadow 180ms,transform 180ms;}
.org-service:hover{box-shadow:0 4px 12px rgba(10,77,140,.1);transform:translateY(-2px);}
.org-node-header{background:#fff;border:2px solid #0A4D8C;border-radius:12px;padding:12px 20px;display:inline-block;text-align:center;font-weight:700;color:#0A4D8C;font-size:14px;white-space:nowrap;}
@media print{
    .btn,.breadcrumb{display:none!important;}
    .org-division{break-inside:avoid;}
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-sitemap me-2" style="color:#0A4D8C;"></i>Organigramme du CHNP
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Structure organisationnelle — {{ $totalAgents }} agent(s) actif(s)</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print me-1"></i>Imprimer
            </button>
        </div>
    </div>

    {{-- Nœud racine --}}
    <div class="text-center mb-4">
        <div style="display:inline-block;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:#fff;border-radius:14px;padding:16px 32px;font-weight:700;font-size:16px;box-shadow:0 4px 16px rgba(10,77,140,.2);">
            <div style="font-size:12px;font-weight:400;opacity:.8;margin-bottom:4px;">Établissement</div>
            Centre Hospitalier National de Pikine
        </div>
        <div style="width:2px;height:30px;background:#CBD5E1;margin:0 auto;"></div>
        <div class="text-center">
            <div style="display:inline-block;background:#1565C0;color:#fff;border-radius:10px;padding:10px 24px;font-weight:600;font-size:13px;">
                <i class="fas fa-users me-2"></i>Direction des Ressources Humaines
            </div>
        </div>
        <div style="width:2px;height:20px;background:#CBD5E1;margin:0 auto;"></div>
    </div>

    {{-- Divisions --}}
    @forelse($divisions as $division)
    <div class="org-division">
        <div class="org-division-header">
            <div>
                <i class="fas fa-layer-group me-2"></i>
                <span style="font-weight:700;font-size:14px;">Division : {{ $division->nom_division }}</span>
            </div>
            <span style="background:rgba(255,255,255,.2);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                {{ $division->services->count() }} service(s)
            </span>
        </div>

        <div class="p-3">
            @if($division->services->isEmpty())
            <div class="text-center py-3" style="color:#9CA3AF;font-size:13px;">
                <i class="fas fa-info-circle me-1"></i>Aucun service rattaché à cette division.
            </div>
            @else
            <div class="row g-3">
                @foreach($division->services as $service)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="org-service">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <div style="width:40px;height:40px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-hospital-symbol" style="color:#0A4D8C;font-size:16px;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;font-size:13px;color:var(--theme-text);">{{ $service->nom_service }}</div>
                                @if($service->type_service)
                                <div style="font-size:11px;color:#9CA3AF;margin-top:2px;">{{ $service->type_service }}</div>
                                @endif
                                <div style="margin-top:10px;display:flex;gap:12px;flex-wrap:wrap;">
                                    <div style="text-align:center;">
                                        <div style="font-size:18px;font-weight:700;color:#0A4D8C;">{{ $service->agents_actifs_count ?? 0 }}</div>
                                        <div style="font-size:10px;color:#9CA3AF;">agents actifs</div>
                                    </div>
                                    @if($service->manager)
                                    <div>
                                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#9CA3AF;margin-bottom:2px;">Responsable</div>
                                        <div style="font-size:12px;color:var(--theme-text);font-weight:600;">{{ $service->manager?->nom_complet ?? '—' }}</div>
                                    </div>
                                    @endif
                                </div>
                                @if($service->tel_service)
                                <div style="font-size:11px;color:#9CA3AF;margin-top:6px;">
                                    <i class="fas fa-phone me-1"></i>{{ $service->tel_service }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5" style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;">
        <i class="fas fa-sitemap" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
        <p class="text-muted">Aucune division enregistrée. Configurez la structure dans le module Services.</p>
    </div>
    @endforelse

    {{-- Légende --}}
    <div style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:12px;padding:16px 20px;margin-top:8px;font-size:12px;color:#9CA3AF;">
        <i class="fas fa-info-circle me-1"></i>
        Organigramme généré automatiquement à partir de la structure organisationnelle du SIRH. {{ now()->format('d/m/Y à H:i') }}
    </div>

</div>
@endsection
