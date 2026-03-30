@extends('layouts.master')
@section('title', 'Dossiers de l\'équipe')
@section('page-title', 'Dossiers agents')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.equipe') }}" style="color:#1565C0;">Mon équipe</a></li>
    <li>Dossiers</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-folder-open me-2" style="color:#0A4D8C;"></i>Dossiers agents
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Consultation lecture seule — Service : <strong>{{ $service->nom_service }}</strong>
            </p>
        </div>
        <a href="{{ route('manager.equipe') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour équipe
        </a>
    </div>

    <div class="row g-3">
        @forelse($agents as $agent)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;transition:box-shadow 180ms,transform 180ms;"
                 onmouseenter="this.style.boxShadow='0 6px 20px rgba(10,77,140,.12)';this.style.transform='translateY(-2px)'"
                 onmouseleave="this.style.boxShadow='';this.style.transform=''">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($agent->photo)
                        <img src="{{ Storage::url($agent->photo) }}" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        @else
                        <div style="width:48px;height:48px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;color:#0A4D8C;font-size:16px;">
                            {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->nom, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <div style="font-weight:700;font-size:14px;color:var(--theme-text);">{{ $agent->prenom }} {{ $agent->nom }}</div>
                            <div style="font-size:12px;color:#9CA3AF;">{{ $agent->matricule }}</div>
                        </div>
                    </div>
                    <div style="font-size:13px;color:#374151;margin-bottom:8px;">
                        <i class="fas fa-briefcase me-1" style="color:#9CA3AF;width:14px;"></i>{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') }}
                    </div>
                    <div style="font-size:13px;color:#374151;margin-bottom:8px;">
                        <i class="fas fa-file-contract me-1" style="color:#9CA3AF;width:14px;"></i>
                        {{ $agent->contratActif?->type_contrat ?? 'Aucun contrat actif' }}
                    </div>
                    <div style="font-size:13px;color:#374151;margin-bottom:12px;">
                        <i class="fas fa-calendar-check me-1" style="color:#9CA3AF;width:14px;"></i>
                        {{ $agent->demandes->count() }} demande(s)
                    </div>
                    <a href="{{ route('manager.equipe.show', $agent->id_agent) }}"
                       class="btn btn-outline-primary btn-sm w-100" style="border-radius:8px;">
                        <i class="fas fa-eye me-1"></i>Consulter le dossier
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-folder-open" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
                <p class="mb-0 text-muted">Aucun agent dans votre service.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection
