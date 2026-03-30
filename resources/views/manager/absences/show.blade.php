@extends('layouts.master')
@section('title', 'Détail Absence')
@section('page-title', 'Détail de l\'Absence')

@section('breadcrumb')
    <li><a href="{{ route('manager.dashboard') }}" style="color:#1565C0;">Manager</a></li>
    <li><a href="{{ route('manager.absences.index') }}" style="color:#1565C0;">Absences équipe</a></li>
    <li>Détail</li>
@endsection

@push('styles')
<style>
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB; }
.info-row { display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid #F3F4F6; }
.info-row:last-child { border-bottom:none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    @php $agent = $absence->demande->agent ?? null; @endphp

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Absence du {{ $absence->date_absence->format('d/m/Y') }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">{{ $agent?->nom_complet }} &mdash; {{ $service->nom_service }}</p>
        </div>
        <a href="{{ route('manager.absences.index') }}" class="action-btn action-btn-outline">
            <i class="fas fa-arrow-left"></i>Retour à la liste
        </a>
    </div>

    <div class="row g-4">

        {{-- Colonne gauche --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-body p-4 text-center">
                    <div style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);color:white;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;margin:0 auto 14px;box-shadow:0 4px 15px rgba(10,77,140,.25);">
                        {{ strtoupper(substr($agent?->prenom ?? 'A',0,1).substr($agent?->nom ?? '',0,1)) }}
                    </div>
                    <h6 class="fw-bold mb-1" style="color:var(--theme-text);">{{ $agent?->nom_complet ?? '—' }}</h6>
                    <div class="text-muted small mb-1">{{ $agent?->fonction ?? '—' }}</div>
                    <div style="font-size:11px;color:#9CA3AF;margin-bottom:12px;">{{ $agent?->matricule }}</div>
                    <span style="background:#EFF6FF;color:#1E40AF;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                        <i class="fas fa-building me-1"></i>{{ $service->nom_service }}
                    </span>
                </div>
            </div>

            {{-- Stats --}}
            @php
                $statsAgent = \App\Models\Absence::forAgent($agent->id_agent)->whereYear('date_absence', now()->year)->get();
            @endphp
            <div class="card border-0 shadow-sm" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0 small" style="color:var(--theme-text);">Bilan {{ now()->year }}</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div style="font-size:22px;font-weight:700;color:#DC2626;">{{ $statsAgent->count() }}</div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">Total</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:22px;font-weight:700;color:#059669;">{{ $statsAgent->where('justifie',true)->count() }}</div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">Justifiées</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:22px;font-weight:700;color:#D97706;">{{ $statsAgent->where('justifie',false)->count() }}</div>
                            <div class="text-muted" style="font-size:10px;margin-top:2px;">Non just.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;background:var(--theme-panel-bg);">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-3 d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:10px;background:#FEE2E2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-user-minus" style="color:#DC2626;font-size:18px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--theme-text);">Détail de l'absence</h5>
                        @php
                            $typeColors=['Maladie'=>'#FEF3C7;color:#92400E','Personnelle'=>'#DBEAFE;color:#1E40AF','Professionnelle'=>'#EDE9FE;color:#5B21B6','Injustifiée'=>'#FEE2E2;color:#991B1B'];
                            $ts = $typeColors[$absence->type_absence] ?? '#F3F4F6;color:#374151';
                        @endphp
                        <span style="font-size:12px;background:{{ $ts }};padding:2px 10px;border-radius:20px;font-weight:600;">{{ $absence->type_absence }}</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="info-row">
                        <span class="text-muted small">Date</span>
                        <span style="font-weight:600;color:var(--theme-text);">{{ $absence->date_absence->isoFormat('dddd D MMMM YYYY') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-muted small">Type</span>
                        <span style="font-weight:600;color:var(--theme-text);">{{ $absence->type_absence }}</span>
                    </div>
                    <div class="info-row">
                        <span class="text-muted small">Justification</span>
                        @if($absence->justifie)
                            <span style="font-size:12px;background:#D1FAE5;color:#065F46;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-check me-1"></i>Justifiée</span>
                        @else
                            <span style="font-size:12px;background:#FEE2E2;color:#991B1B;padding:3px 10px;border-radius:20px;font-weight:600;"><i class="fas fa-times me-1"></i>Non justifiée</span>
                        @endif
                    </div>
                    <div class="info-row">
                        <span class="text-muted small">Enregistrée le</span>
                        <span style="font-weight:500;color:var(--theme-text);">{{ $absence->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if($absence->commentaire)
                    <div class="mt-3 p-3" style="background:#F8FAFC;border-radius:8px;border-left:3px solid #3B82F6;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;margin-bottom:4px;">Observations</div>
                        <p style="font-size:13px;color:var(--theme-text);margin:0;">{{ $absence->commentaire }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Bannière lecture seule manager --}}
            <div class="d-flex align-items-start gap-3 p-4 rounded-3" style="background:#EFF6FF;border-left:4px solid #1565C0;">
                <i class="fas fa-info-circle mt-1" style="color:#1565C0;font-size:18px;flex-shrink:0;"></i>
                <div>
                    <div style="font-weight:600;color:#1D4ED8;font-size:14px;margin-bottom:4px;">Accès en lecture seule</div>
                    <p style="font-size:13px;color:#374151;margin:0;">
                        En tant que Manager, vous consultez cette absence. La validation du justificatif et la modification relèvent du service RH.
                        Si nécessaire, <a href="#" style="color:#1565C0;font-weight:600;">contactez le service RH</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.fw-600{font-weight:600!important;}</style>
@endsection
