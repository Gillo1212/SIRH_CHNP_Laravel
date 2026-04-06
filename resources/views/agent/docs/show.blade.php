@extends('layouts.master')
@section('title', $demande->libelleType)
@section('page-title', 'Détail de la demande')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.docs.index') }}" style="color:#1565C0;">Mes documents</a></li>
    <li>{{ $demande->libelleType }}</li>
@endsection

@push('styles')
<style>
.timeline { position: relative; padding-left: 24px; }
.timeline::before { content:''; position:absolute; left:8px; top:0; bottom:0; width:2px; background:#E5E7EB; }
.tl-step { position: relative; margin-bottom: 20px; }
.tl-dot {
    position: absolute; left: -20px; top: 2px;
    width: 16px; height: 16px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 8px; color: #fff;
}
.tl-dot.active   { background: #1D4ED8; }
.tl-dot.done     { background: #059669; }
.tl-dot.pending  { background: #E5E7EB; }
.tl-dot.rejected { background: #DC2626; }
.tl-title    { font-size: 13px; font-weight: 600; color: #1E293B; }
.tl-subtitle { font-size: 11px; color: #9CA3AF; margin-top: 2px; }

.info-block { background:#F9FAFB; border:1px solid #E5E7EB; border-radius:10px; padding:16px 18px; margin-bottom:12px; }
.info-block-label { font-size:10px; font-weight:700; text-transform:uppercase; color:#9CA3AF; margin-bottom:4px; }
.info-block-val { font-size:13px; color:#1E293B; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">{{ $demande->libelleType }}</h4>
            <p class="mb-0 text-muted" style="font-size:13px;">
                Demande #{{ $demande->id }} · Soumise le {{ $demande->created_at?->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($demande->statut === 'pret')
            <a href="{{ route('agent.docs.download', $demande->id) }}" class="btn btn-success btn-sm" style="border-radius:8px;">
                <i class="fas fa-file-alt me-1"></i>Accéder au document
            </a>
            @endif
            @if($demande->statut === 'en_attente')
            <form action="{{ route('agent.docs.cancel', $demande->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius:8px;">
                    <i class="fas fa-times me-1"></i>Annuler la demande
                </button>
            </form>
            @endif
            <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('info'))<div class="alert alert-info alert-dismissible fade show" style="border-radius:10px;"><i class="fas fa-info-circle me-2"></i>{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row g-4">

        {{-- Colonne gauche : infos + données soumises --}}
        <div class="col-12 col-lg-7">

            {{-- Données soumises --}}
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    Informations de la demande
                </div>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="info-block">
                            <div class="info-block-label">Type de document</div>
                            <div class="info-block-val fw-semibold">{{ $demande->libelleType }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="info-block">
                            <div class="info-block-label">Date de soumission</div>
                            <div class="info-block-val">{{ $demande->created_at?->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                    @if($demande->numero_reference)
                    <div class="col-12">
                        <div class="info-block" style="background:#EFF6FF;border-color:#BFDBFE;">
                            <div class="info-block-label" style="color:#1D4ED8;">Numéro de référence</div>
                            <div class="info-block-val fw-bold" style="color:#1D4ED8;font-size:14px;">{{ $demande->numero_reference }}</div>
                        </div>
                    </div>
                    @endif
                    @if($demande->motif)
                    <div class="col-12">
                        <div class="info-block">
                            <div class="info-block-label">Motif</div>
                            <div class="info-block-val">{{ $demande->motif }}</div>
                        </div>
                    </div>
                    @endif

                    {{-- Données spécifiques soumises --}}
                    @if($demande->donnees_specifiques && count($demande->donnees_specifiques) > 0)
                    @foreach($champsAgent as $champKey => $champConfig)
                        @if(isset($demande->donnees_specifiques[$champKey]))
                        <div class="col-sm-6">
                            <div class="info-block">
                                <div class="info-block-label">{{ $champConfig['label'] }}</div>
                                <div class="info-block-val">
                                    @php $val = $demande->donnees_specifiques[$champKey]; @endphp
                                    @if($champConfig['type'] === 'date')
                                        {{ \Carbon\Carbon::parse($val)->isoFormat('D MMMM YYYY') }}
                                    @elseif($champConfig['type'] === 'select' && isset($champConfig['options'][$val]))
                                        {{ $champConfig['options'][$val] }}
                                    @else
                                        {{ $val }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    @endif

                    @if($demande->motif_rejet)
                    <div class="col-12">
                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;">
                            <div style="font-size:11px;font-weight:700;color:#991B1B;margin-bottom:4px;text-transform:uppercase;">Motif</div>
                            <div style="font-size:13px;color:#991B1B;">{{ $demande->motif_rejet }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- CTA si prêt --}}
            @if($demande->statut === 'pret')
            <div style="background:linear-gradient(135deg,#059669,#047857);border-radius:12px;padding:20px 24px;color:white;display:flex;align-items:center;gap:16px;">
                <i class="fas fa-check-circle fa-2x"></i>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:15px;">Document prêt !</div>
                    <div style="font-size:13px;opacity:.9;margin-top:2px;">Votre document est disponible. Cliquez pour le consulter et l'imprimer.</div>
                </div>
                <a href="{{ route('agent.docs.download', $demande->id) }}"
                   style="background:white;color:#059669;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;flex-shrink:0;">
                    <i class="fas fa-file-alt me-1"></i>Voir le document
                </a>
            </div>
            @endif
        </div>

        {{-- Colonne droite : timeline de statut --}}
        <div class="col-12 col-lg-5">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9CA3AF;margin-bottom:20px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                    Suivi de la demande
                </div>

                @php
                    $steps = [
                        'submitted'  => ['label' => 'Demande soumise',           'sub' => $demande->created_at?->format('d/m/Y H:i'), 'done' => true],
                        'en_attente' => ['label' => 'En attente de traitement',  'sub' => 'Le service RH prend en charge votre demande', 'done' => in_array($demande->statut, ['en_attente','en_cours','pret'])],
                        'en_cours'   => ['label' => 'En cours de traitement',    'sub' => 'Votre document est en cours de préparation',  'done' => in_array($demande->statut, ['en_cours','pret'])],
                        'pret'       => ['label' => 'Document prêt',             'sub' => $demande->date_traitement ? 'Traité le '.$demande->date_traitement->format('d/m/Y') : 'En attente', 'done' => $demande->statut === 'pret'],
                    ];
                @endphp

                @if($demande->statut === 'rejete')
                    <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:16px;text-align:center;">
                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                        <div style="font-weight:600;color:#991B1B;">Demande annulée / rejetée</div>
                        @if($demande->motif_rejet)
                        <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">{{ $demande->motif_rejet }}</div>
                        @endif
                        <a href="{{ route('agent.docs.create') }}" class="btn btn-outline-danger btn-sm mt-3">
                            <i class="fas fa-redo me-1"></i>Faire une nouvelle demande
                        </a>
                    </div>
                @else
                    <div class="timeline">
                        @foreach($steps as $stepKey => $step)
                        <div class="tl-step">
                            @php
                                $dotClass = $step['done'] ? 'done' : 'pending';
                                if ($stepKey === $demande->statut && $demande->statut !== 'pret') {
                                    $dotClass = 'active';
                                }
                            @endphp
                            <div class="tl-dot {{ $dotClass }}">
                                @if($step['done'])<i class="fas fa-check"></i>
                                @elseif($dotClass === 'active')<i class="fas fa-circle" style="font-size:6px;"></i>
                                @else<i class="fas fa-circle" style="font-size:6px;color:#D1D5DB;"></i>
                                @endif
                            </div>
                            <div class="tl-title" style="{{ !$step['done'] ? 'color:#9CA3AF;' : '' }}">{{ $step['label'] }}</div>
                            <div class="tl-subtitle">{{ $step['sub'] }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Délai indicatif --}}
                @if(in_array($demande->statut, ['en_attente', 'en_cours']))
                <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:12px 14px;margin-top:16px;font-size:12px;color:#92400E;">
                    <i class="fas fa-clock me-1"></i>
                    <strong>Délai estimé :</strong> 2 à 5 jours ouvrés selon la charge du service RH.
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
