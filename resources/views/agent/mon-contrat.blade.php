@extends('layouts.master')

@section('title', 'Mon Contrat')
@section('page-title', 'Mon Contrat')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.profil') }}" style="color:#1565C0;">Mon dossier</a></li>
    <li>Mon contrat</li>
@endsection

@push('styles')
<style>
.action-btn { display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 180ms; }
.action-btn-outline { background:var(--theme-panel-bg);color:var(--theme-text);border:1px solid var(--theme-border); }
.action-btn-outline:hover { background:var(--sirh-primary-hover);color:#0A4D8C; }

.info-card { border-radius:12px;padding:20px;background:var(--theme-panel-bg);border:1px solid var(--theme-border); }
.info-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);margin-bottom:4px; }
.info-value { font-size:14px;font-weight:500;color:var(--theme-text); }

.badge-actif   { background:#D1FAE5;color:#065F46; }
.badge-expire  { background:#FEE2E2;color:#991B1B; }
.badge-clot    { background:#F3F4F6;color:#374151; }
.badge-renouv  { background:#FEF3C7;color:#92400E; }
.stat-badge { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap; }

.timeline-item { position:relative;padding-left:24px;padding-bottom:20px; }
.timeline-item:not(:last-child)::before { content:'';position:absolute;left:7px;top:16px;bottom:0;width:2px;background:var(--theme-border); }
.timeline-dot { position:absolute;left:0;top:4px;width:16px;height:16px;border-radius:50%;border:2px solid var(--theme-border);background:var(--theme-panel-bg);display:flex;align-items:center;justify-content:center; }
.timeline-dot.active { border-color:#059669;background:#D1FAE5; }
.timeline-dot.expired { border-color:#DC2626;background:#FEE2E2; }
.timeline-dot.closed { border-color:#9CA3AF;background:#F3F4F6; }

.urgence-bar-wrap { height:8px;border-radius:4px;background:var(--theme-bg-secondary);overflow:hidden; }
.urgence-bar-fill { height:100%;border-radius:4px; }

@keyframes toastIn { from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);} }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4" style="max-width:960px;">

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--theme-text);">
            <i class="fas fa-file-contract me-2" style="color:#0A4D8C;"></i>Mon contrat
        </h4>
        <p class="text-muted small mb-0">Situation contractuelle de votre emploi au CHNP</p>
    </div>
    <a href="{{ route('agent.profil') }}" class="action-btn action-btn-outline">
        <i class="fas fa-arrow-left"></i> Mon dossier
    </a>
</div>

@if($contratActif)
{{-- ─── CONTRAT ACTIF (bannière) ─────────────────────────────── --}}
<div class="mb-4" style="background:linear-gradient(135deg,#0A4D8C 0%,#1565C0 100%);border-radius:16px;padding:24px;color:#fff;position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;right:0;width:180px;height:180px;background:rgba(255,255,255,.04);border-radius:50%;transform:translate(40%,-40%);"></div>
    <div style="position:absolute;bottom:0;left:0;width:120px;height:120px;background:rgba(255,255,255,.04);border-radius:50%;transform:translate(-30%,30%);"></div>
    <div class="d-flex align-items-center gap-3 mb-3">
        <div style="width:48px;height:48px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-file-contract" style="font-size:20px;color:#fff;"></i>
        </div>
        <div>
            <div class="fw-bold" style="font-size:18px;">Contrat actif</div>
            <div style="opacity:.8;font-size:13px;">{{ \App\Models\Contrat::TYPES[$contratActif->type_contrat] ?? $contratActif->type_contrat }}</div>
        </div>
        <div class="ms-auto">
            <span class="stat-badge" style="background:rgba(255,255,255,.2);color:#fff;">
                <i class="fas fa-circle" style="font-size:7px;"></i>
                Actif
            </span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div style="opacity:.7;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;">Date début</div>
            <div style="font-size:14px;font-weight:600;">{{ $contratActif->date_debut?->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div style="opacity:.7;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;">Date fin</div>
            <div style="font-size:14px;font-weight:600;">
                @if($contratActif->date_fin)
                    {{ $contratActif->date_fin->format('d/m/Y') }}
                @else
                    <span style="opacity:.7;font-style:italic;">Indéterminée</span>
                @endif
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="opacity:.7;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;">Durée</div>
            <div style="font-size:14px;font-weight:600;">{{ $contratActif->duree }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div style="opacity:.7;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;">Jours restants</div>
            <div style="font-size:14px;font-weight:600;">
                @if($contratActif->date_fin)
                    @php $jr = $contratActif->jours_restants; @endphp
                    @if($jr !== null && $jr > 0)
                        {{ $jr }}j
                    @elseif($jr !== null && $jr <= 0)
                        <span style="color:#FCA5A5;">Expiré</span>
                    @endif
                @else
                    <i class="fas fa-infinity"></i>
                @endif
            </div>
        </div>
    </div>

    {{-- Barre d'urgence si date fin proche --}}
    @if($contratActif->date_fin)
        @php
            $jr = $contratActif->jours_restants;
            // Référence : durée totale du contrat (cohérent avec expiring.blade)
            $dureeTotale = max(1, $contratActif->date_debut->diffInDays($contratActif->date_fin));
            $progress = $jr !== null ? max(0, min(100, ($jr / $dureeTotale) * 100)) : 100;
            $barColor = $jr <= 30 ? '#FCA5A5' : ($jr <= 60 ? '#FDE68A' : '#6EE7B7');
        @endphp
        @if($jr !== null && $jr <= 60)
        <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.15);">
            <div class="d-flex justify-content-between mb-1">
                <span style="font-size:12px;opacity:.8;">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Votre contrat expire bientôt
                </span>
                <span style="font-size:12px;font-weight:600;">{{ $jr }} jours restants</span>
            </div>
            <div style="height:6px;border-radius:3px;background:rgba(255,255,255,.15);">
                <div style="width:{{ $progress }}%;height:100%;border-radius:3px;background:{{ $barColor }};transition:width .3s;"></div>
            </div>
        </div>
        @endif
    @endif
</div>

{{-- ─── DÉTAILS CONTRAT ACTIF ────────────────────────────────── --}}
<div class="info-card mb-4">
    <div class="fw-600 mb-3" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);">
        <i class="fas fa-info-circle me-1" style="color:#0A4D8C;"></i>Détails du contrat actif
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="info-label">Type de contrat</div>
            <div class="info-value">{{ \App\Models\Contrat::TYPES[$contratActif->type_contrat] ?? $contratActif->type_contrat }}</div>
        </div>
        <div class="col-md-6">
            <div class="info-label">Statut</div>
            <div>
                <span class="stat-badge badge-actif">
                    <i class="fas fa-circle" style="font-size:7px;"></i>
                    Actif
                </span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-label">Date de début</div>
            <div class="info-value">{{ $contratActif->date_debut?->isoFormat('DD MMMM YYYY') ?? '—' }}</div>
        </div>
        <div class="col-md-6">
            <div class="info-label">Date de fin</div>
            <div class="info-value">
                @if($contratActif->date_fin)
                    {{ $contratActif->date_fin->isoFormat('DD MMMM YYYY') }}
                @else
                    <span class="text-muted" style="font-style:italic;">Durée indéterminée</span>
                @endif
            </div>
        </div>
        @if($contratActif->observation)
        <div class="col-12">
            <div class="info-label">Observation</div>
            <div class="info-value" style="white-space:pre-wrap;">{{ $contratActif->observation }}</div>
        </div>
        @endif
    </div>
</div>

@else
{{-- ─── PAS DE CONTRAT ACTIF ────────────────────────────────── --}}
<div class="info-card mb-4 text-center py-5">
    <div style="width:64px;height:64px;background:#FEF3C7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <i class="fas fa-file-contract fa-2x" style="color:#D97706;"></i>
    </div>
    <p class="fw-600 mb-1" style="color:var(--theme-text);">Aucun contrat actif</p>
    <p class="text-muted small">Vous n'avez pas de contrat actif en ce moment. Contactez le service RH pour plus d'informations.</p>
</div>
@endif

{{-- ─── HISTORIQUE DES CONTRATS ──────────────────────────────── --}}
@if($contrats->count() > 0)
<div class="info-card">
    <div class="fw-600 mb-4" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--theme-text-muted);">
        <i class="fas fa-history me-1" style="color:#0A4D8C;"></i>Historique contractuel
        <span style="margin-left:6px;background:#EFF6FF;color:#1E40AF;border-radius:20px;padding:1px 8px;font-size:10px;font-weight:700;">
            {{ $contrats->count() }} contrat(s)
        </span>
    </div>

    <div>
        @foreach($contrats as $c)
        @php
            $st = $c->statut_contrat;
            $dotClass = match($st) {
                'Actif'             => 'active',
                'Expiré'            => 'expired',
                'Clôturé'           => 'closed',
                'En_renouvellement' => 'expired',
                default             => 'closed',
            };
            $badgeClass = match($st) {
                'Actif'             => 'badge-actif',
                'Expiré'            => 'badge-expire',
                'Clôturé'           => 'badge-clot',
                'En_renouvellement' => 'badge-renouv',
                default             => '',
            };
        @endphp
        <div class="timeline-item">
            <div class="timeline-dot {{ $dotClass }}">
                @if($st === 'Actif')
                    <i class="fas fa-circle" style="font-size:6px;color:#059669;"></i>
                @elseif($st === 'Expiré')
                    <i class="fas fa-circle" style="font-size:6px;color:#DC2626;"></i>
                @else
                    <i class="fas fa-circle" style="font-size:6px;color:#9CA3AF;"></i>
                @endif
            </div>
            <div style="background:var(--theme-bg-secondary);border-radius:10px;padding:14px 16px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="fw-600" style="color:var(--theme-text);font-size:13.5px;">
                            {{ \App\Models\Contrat::TYPES[$c->type_contrat] ?? $c->type_contrat }}
                        </div>
                        <div class="text-muted" style="font-size:12px;margin-top:2px;">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $c->date_debut?->format('d/m/Y') }}
                            @if($c->date_fin)
                                → {{ $c->date_fin->format('d/m/Y') }}
                                <span style="margin-left:6px;">({{ $c->duree }})</span>
                            @else
                                → <em>Indéterminé</em>
                            @endif
                        </div>
                    </div>
                    <span class="stat-badge {{ $badgeClass }}">
                        {{ \App\Models\Contrat::STATUTS[$st]['label'] ?? $st }}
                    </span>
                </div>
                @if($c->observation)
                <div class="mt-2 pt-2" style="border-top:1px solid var(--theme-border);font-size:12px;color:var(--theme-text-muted);">
                    <i class="fas fa-quote-left me-1" style="font-size:9px;"></i>{{ $c->observation }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ─── INFO CONTACT RH ──────────────────────────────────────── --}}
<div class="mt-4" style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:16px 20px;">
    <div class="d-flex gap-3 align-items-start">
        <div style="width:36px;height:36px;background:#DBEAFE;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-info-circle" style="color:#1D4ED8;"></i>
        </div>
        <div>
            <div class="fw-600 mb-1" style="color:#1E40AF;font-size:13.5px;">Informations importantes</div>
            <p class="mb-1" style="font-size:12.5px;color:#374151;">
                Pour toute question concernant votre contrat ou pour demander un renouvellement, contactez le service des Ressources Humaines du CHNP.
            </p>
            <p class="mb-0" style="font-size:12px;color:#6B7280;">
                <i class="fas fa-shield-alt me-1"></i>
                Les informations contractuelles sont strictement confidentielles et accessibles uniquement par vous et les agents RH autorisés.
            </p>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function showToast(message, type = 'success') {
    const cfg = { success:{bg:'#10B981',icon:'fa-check-circle'}, error:{bg:'#EF4444',icon:'fa-exclamation-circle'} };
    const c = cfg[type] || cfg.success;
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `<div id="${id}" style="position:fixed;top:22px;right:22px;z-index:10000;background:${c.bg};color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 28px rgba(0,0,0,.18);font-size:14px;font-weight:500;max-width:420px;animation:toastIn .3s ease;"><i class="fas ${c.icon}" style="font-size:18px;flex-shrink:0;"></i><span>${message}</span><button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;margin-left:auto;padding:0 0 0 8px;line-height:1;">×</button></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 5000);
}
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
@endif
</script>
@endpush
