@extends('layouts.master')

@section('title', 'Détail Demande de Congé')
@section('page-title', 'Détail Demande de Congé')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.conges.index') }}" style="color:#1565C0;">Mes congés</a></li>
    <li>Demande #{{ $demande->id_demande }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            @php
                $conge  = $demande->conge;
                $statut = $demande->statut_demande;
                $config = match($statut) {
                    'En_attente' => ['bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'fa-clock','label'=>'En attente de validation'],
                    'Validé'     => ['bg'=>'#DBEAFE','color'=>'#1E40AF','icon'=>'fa-user-check','label'=>'Validé par le Manager — En attente RH'],
                    'Approuvé'   => ['bg'=>'#D1FAE5','color'=>'#065F46','icon'=>'fa-check-double','label'=>'Approuvé'],
                    'Rejeté'     => ['bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'fa-times-circle','label'=>'Rejeté'],
                    default      => ['bg'=>'#F3F4F6','color'=>'#374151','icon'=>'fa-question','label'=>$statut],
                };
            @endphp

            {{-- Statut --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;border-left:5px solid {{ $config['color'] }} !important;background:var(--theme-panel-bg);">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:{{ $config['bg'] }};">
                        <i class="fas {{ $config['icon'] }}" style="color:{{ $config['color'] }};font-size:20px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:{{ $config['color'] }};font-size:15px;">{{ $config['label'] }}</div>
                        <div class="text-muted small">Demande #{{ $demande->id_demande }} — soumise le {{ $demande->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Détails --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-4 pb-2" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0" style="color:var(--theme-text);">
                        <i class="fas fa-calendar-alt me-2" style="color:#0A4D8C;"></i>Détails du congé
                    </h6>
                </div>
                <div class="card-body px-4 pb-4" style="background:var(--theme-panel-bg);">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small fw-600 text-uppercase" style="font-size:10px;letter-spacing:.05em;">Type de congé</div>
                            <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ $conge->typeConge->libelle ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small fw-600 text-uppercase" style="font-size:10px;letter-spacing:.05em;">Durée</div>
                            <div class="fw-bold mt-1" style="color:#0A4D8C;font-size:18px;">{{ $conge->nbres_jours ?? '—' }} <span style="font-size:13px;font-weight:500;color:var(--theme-text-muted);">jour(s)</span></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small fw-600 text-uppercase" style="font-size:10px;letter-spacing:.05em;">Date de début</div>
                            <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ $conge->date_debut?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small fw-600 text-uppercase" style="font-size:10px;letter-spacing:.05em;">Date de fin</div>
                            <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ $conge->date_fin?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        @if($conge->date_approbation)
                            <div class="col-sm-6">
                                <div class="text-muted small fw-600 text-uppercase" style="font-size:10px;letter-spacing:.05em;">Date d'approbation</div>
                                <div class="fw-600 mt-1" style="color:#10B981;">{{ $conge->date_approbation->format('d/m/Y') }}</div>
                            </div>
                        @endif
                        @if($demande->date_traitement)
                            <div class="col-sm-6">
                                <div class="text-muted small fw-600 text-uppercase" style="font-size:10px;letter-spacing:.05em;">Date de traitement</div>
                                <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ \Carbon\Carbon::parse($demande->date_traitement)->format('d/m/Y à H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Motif de rejet --}}
            @if($statut === 'Rejeté' && $demande->motif_refus)
                <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;border-left:4px solid #EF4444;">
                    <div class="card-body px-4 py-3" style="background:#FFF5F5;">
                        <div class="fw-bold mb-1" style="color:#991B1B;font-size:13px;">
                            <i class="fas fa-times-circle me-2"></i>Motif du rejet
                        </div>
                        <p class="mb-0 text-muted small">{{ $demande->motif_refus }}</p>
                    </div>
                </div>
            @endif

            {{-- Timeline workflow --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-3 pb-2" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">
                        <i class="fas fa-stream me-2" style="color:#0A4D8C;"></i>Progression du workflow
                    </h6>
                </div>
                <div class="card-body px-4 pb-4" style="background:var(--theme-panel-bg);">
                    @php
                        $etapes = [
                            ['label'=>'Demande soumise','done'=>true,'icon'=>'fa-paper-plane'],
                            ['label'=>'Validation Manager','done'=>in_array($statut,['Validé','Approuvé','Rejeté']),'icon'=>'fa-user-check'],
                            ['label'=>'Approbation RH','done'=>$statut==='Approuvé','icon'=>'fa-check-double'],
                        ];
                    @endphp
                    <div class="d-flex align-items-center gap-0">
                        @foreach($etapes as $i => $etape)
                            <div class="text-center" style="flex:1;">
                                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                    style="width:36px;height:36px;background:{{ $etape['done'] ? '#0A4D8C' : 'var(--theme-bg-secondary)' }};border:2px solid {{ $etape['done'] ? '#0A4D8C' : 'var(--theme-border)' }};">
                                    <i class="fas {{ $etape['icon'] }}" style="font-size:13px;color:{{ $etape['done'] ? '#fff' : 'var(--theme-text-muted)' }};"></i>
                                </div>
                                <div class="mt-1" style="font-size:10px;color:{{ $etape['done'] ? '#0A4D8C' : 'var(--theme-text-muted)' }};font-weight:{{ $etape['done'] ? '600' : '400' }};">
                                    {{ $etape['label'] }}
                                </div>
                            </div>
                            @if($i < count($etapes) - 1)
                                <div style="flex:1;height:2px;background:{{ $etape['done'] && isset($etapes[$i+1]) && $etapes[$i+1]['done'] ? '#0A4D8C' : 'var(--theme-border)' }};margin-bottom:18px;"></div>
                            @endif
                        @endforeach
                    </div>
                    @if($statut === 'Rejeté')
                        <div class="text-center mt-2">
                            <span class="badge" style="background:#FEE2E2;color:#991B1B;font-size:11px;">
                                <i class="fas fa-times me-1"></i>Demande rejetée
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('agent.conges.index') }}" class="btn d-inline-flex align-items-center gap-2" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;padding:9px 18px;color:var(--theme-text);font-size:13px;">
                <i class="fas fa-arrow-left"></i> Retour à mes congés
            </a>

        </div>
    </div>
</div>
@endsection
