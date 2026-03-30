@extends('layouts.master')

@section('title', 'Détail Demande de Congé')
@section('page-title', 'Détail Demande de Congé')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.conges.index') }}" style="color:#1565C0;">Congés</a></li>
    <li>Demande #{{ $demande->id_demande }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    @php
        $conge  = $demande->conge;
        $agent  = $demande->agent;
        $statut = $demande->statut_demande;
        $config = match($statut) {
            'En_attente' => ['bg'=>'#FEF3C7','color'=>'#92400E','icon'=>'fa-clock','label'=>'En attente (validation Manager)'],
            'Validé'     => ['bg'=>'#DBEAFE','color'=>'#1E40AF','icon'=>'fa-user-check','label'=>'Validé Manager — En attente approbation RH'],
            'Approuvé'   => ['bg'=>'#D1FAE5','color'=>'#065F46','icon'=>'fa-check-double','label'=>'Approuvé définitivement'],
            'Rejeté'     => ['bg'=>'#FEE2E2','color'=>'#991B1B','icon'=>'fa-times-circle','label'=>'Rejeté'],
            default      => ['bg'=>'#F3F4F6','color'=>'#374151','icon'=>'fa-question','label'=>$statut],
        };
    @endphp

    <div class="row g-4">
        {{-- Colonne principale --}}
        <div class="col-lg-8">

            {{-- Statut --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;border-left:5px solid {{ $config['color'] }} !important;background:var(--theme-panel-bg);">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:{{ $config['bg'] }};">
                            <i class="fas {{ $config['icon'] }}" style="color:{{ $config['color'] }};font-size:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="color:{{ $config['color'] }};font-size:15px;">{{ $config['label'] }}</div>
                            <div class="text-muted small">Demande #{{ $demande->id_demande }} — {{ $demande->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    {{-- Boutons d'action selon statut --}}
                    @if($statut === 'Validé')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                style="background:#10B981;color:#fff;border:none;border-radius:8px;padding:8px 16px;"
                                data-bs-toggle="modal" data-bs-target="#modalApprouverShow">
                                <i class="fas fa-check-double"></i> Approuver
                            </button>
                            <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                style="background:#FEE2E2;color:#991B1B;border:none;border-radius:8px;padding:8px 14px;"
                                data-bs-toggle="modal" data-bs-target="#rejetModal">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        </div>
                    @elseif($statut === 'En_attente')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm d-flex align-items-center gap-2"
                                style="background:#FEE2E2;color:#991B1B;border:none;border-radius:8px;padding:8px 14px;"
                                data-bs-toggle="modal" data-bs-target="#rejetModal">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info agent --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-3 pb-2" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">
                        <i class="fas fa-user me-2" style="color:#0A4D8C;"></i>Informations de l'agent
                    </h6>
                </div>
                <div class="card-body px-4 pb-4" style="background:var(--theme-panel-bg);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center fw-bold" style="width:56px;height:56px;border-radius:50%;background:#EFF6FF;color:#0A4D8C;font-size:18px;">
                                {{ strtoupper(substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? '', 0, 1)) }}
                            </div>
                        <div>
                            <div class="fw-bold" style="color:var(--theme-text);">{{ $agent->nom_complet }}</div>
                            <div class="text-muted small">{{ $agent->matricule }} — {{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</div>
                            <div class="text-muted small">{{ $agent->service->nom_service ?? '—' }}</div>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('rh.agents.show', $agent->id_agent) }}" class="btn btn-sm" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;font-size:11px;color:var(--theme-text);">
                                <i class="fas fa-external-link-alt me-1"></i>Dossier
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Détails du congé --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-header border-0 px-4 pt-3 pb-2" style="background:var(--theme-panel-bg);">
                    <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">
                        <i class="fas fa-calendar-alt me-2" style="color:#0A4D8C;"></i>Détails du congé
                    </h6>
                </div>
                <div class="card-body px-4 pb-4" style="background:var(--theme-panel-bg);">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Type</div>
                            <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ $conge->typeConge->libelle ?? '—' }}</div>
                            @if($conge->typeConge)
                                <div class="small text-muted">{{ $conge->typeConge->deductible ? 'Déductible du solde' : 'Non déductible' }}</div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Durée</div>
                            <div class="fw-bold mt-1" style="color:#0A4D8C;font-size:22px;">{{ $conge->nbres_jours ?? '—' }} <span style="font-size:13px;font-weight:500;color:var(--theme-text-muted);">jour(s)</span></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Début</div>
                            <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ $conge->date_debut?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Fin</div>
                            <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ $conge->date_fin?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        @if($demande->date_traitement)
                            <div class="col-sm-6">
                                <div class="text-muted" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Traité le</div>
                                <div class="fw-600 mt-1" style="color:var(--theme-text);">{{ \Carbon\Carbon::parse($demande->date_traitement)->format('d/m/Y à H:i') }}</div>
                            </div>
                        @endif
                        @if($conge->date_approbation)
                            <div class="col-sm-6">
                                <div class="text-muted" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Approuvé le</div>
                                <div class="fw-600 mt-1" style="color:#10B981;">{{ $conge->date_approbation->format('d/m/Y') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Motif rejet --}}
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

            {{-- Historique de l'agent --}}
            @if($historique->count() > 0)
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-header border-0 px-4 pt-3 pb-2" style="background:var(--theme-panel-bg);">
                        <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">
                            <i class="fas fa-history me-2" style="color:#0A4D8C;"></i>Congés précédents de cet agent
                        </h6>
                    </div>
                    <div class="card-body p-0" style="background:var(--theme-panel-bg);">
                        @foreach($historique as $h)
                            <div class="d-flex align-items-center px-4 py-2 border-bottom gap-3">
                                <div class="flex-grow-1 small" style="color:var(--theme-text);">
                                    {{ $h->conge->typeConge->libelle ?? '—' }}
                                    @if($h->conge)
                                        — {{ $h->conge->date_debut?->format('d/m/Y') }} → {{ $h->conge->date_fin?->format('d/m/Y') }}
                                        ({{ $h->conge->nbres_jours }}j)
                                    @endif
                                </div>
                                <span class="badge" style="font-size:10px;
                                    background:{{ match($h->statut_demande) { 'Approuvé' => '#D1FAE5', 'Rejeté' => '#FEE2E2', default => '#FEF3C7' } }};
                                    color:{{ match($h->statut_demande) { 'Approuvé' => '#065F46', 'Rejeté' => '#991B1B', default => '#92400E' } }};">
                                    {{ $h->statut_demande }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Colonne latérale — Solde --}}
        <div class="col-lg-4">
            @if($solde)
                <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                    <div class="card-header border-0 px-4 pt-3 pb-2" style="background:var(--theme-panel-bg);">
                        <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">
                            <i class="fas fa-balance-scale me-2" style="color:#0A4D8C;"></i>Solde actuel
                        </h6>
                    </div>
                    <div class="card-body px-4 pb-4" style="background:var(--theme-panel-bg);">
                        @php
                            $pct = $solde->solde_initial > 0
                                ? round(($solde->solde_restant / $solde->solde_initial) * 100)
                                : 0;
                            $color = $pct >= 50 ? '#10B981' : ($pct >= 25 ? '#F59E0B' : '#EF4444');
                        @endphp
                        <div class="text-center mb-3">
                            <div class="fw-bold" style="font-size:36px;color:{{ $color }};">{{ $solde->solde_restant }}</div>
                            <div class="text-muted small">jours disponibles sur {{ $solde->solde_initial }}</div>
                        </div>
                        <div class="progress mb-2" style="height:8px;border-radius:4px;">
                            <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};border-radius:4px;"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Pris : {{ $solde->solde_pris }}j</span>
                            <span>{{ $pct }}% restant</span>
                        </div>

                        @if($conge && $conge->typeConge && $conge->typeConge->deductible && $statut === 'Validé')
                            <div class="mt-3 p-2 rounded" style="background:#FEF3C7;font-size:11px;">
                                <i class="fas fa-exclamation-triangle me-1" style="color:#D97706;"></i>
                                <span style="color:#92400E;">
                                    Si approuvé : {{ $solde->solde_restant - $conge->nbres_jours }}j restants
                                    ({{ $conge->nbres_jours }}j déduits)
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Actions rapides --}}
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body px-4 py-3" style="background:var(--theme-panel-bg);">
                    <a href="{{ route('rh.conges.index') }}" class="btn w-100 mb-2 d-flex align-items-center gap-2 justify-content-center" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;font-size:13px;color:var(--theme-text);padding:9px;">
                        <i class="fas fa-list"></i> Historique congés
                    </a>
                    <a href="{{ route('rh.conges.pending') }}" class="btn w-100 d-flex align-items-center gap-2 justify-content-center" style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;font-size:13px;color:#1E40AF;padding:9px;">
                        <i class="fas fa-clock"></i> À approuver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Approbation --}}
<div class="modal fade" id="modalApprouverShow" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <form action="{{ route('rh.conges.approuver', $demande->id_demande) }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                        <i class="fas fa-check-double me-2" style="color:#10B981;"></i>Approuver le congé
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p style="font-size:14px;color:var(--theme-text);margin:0;">
                        Approuver le congé de <strong>{{ $agent->nom_complet }}</strong>
                        ({{ $conge->nbres_jours }} jour(s)) ?
                    </p>
                    <p class="text-muted small mt-2 mb-0">Le solde sera automatiquement déduit.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-sm" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;color:var(--theme-text);" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-2" style="background:#10B981;color:#fff;border:none;border-radius:8px;padding:8px 16px;">
                        <i class="fas fa-check-double"></i> Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Rejet --}}
<div class="modal fade" id="rejetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:1px solid var(--theme-border);background:var(--theme-panel-bg);">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h6 class="modal-title fw-bold" style="color:var(--theme-text);">
                    <i class="fas fa-times-circle me-2 text-danger"></i>Rejeter la demande
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.conges.rejeter', $demande->id_demande) }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-600 small" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--theme-text-muted);">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea name="motif_refus" rows="3" class="form-control" style="border-radius:8px;font-size:13px;border-color:var(--theme-border);background:var(--theme-panel-bg);color:var(--theme-text);"
                            placeholder="Expliquez le motif (minimum 10 caractères)…" required minlength="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-sm" style="background:var(--theme-bg-secondary);border:1px solid var(--theme-border);border-radius:8px;color:var(--theme-text);" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center gap-2" style="border-radius:8px;">
                        <i class="fas fa-times"></i> Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
