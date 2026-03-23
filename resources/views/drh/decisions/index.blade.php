@extends('layouts.master')

@section('title', 'Décisions RH — DRH')
@section('page-title', 'Décisions RH')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>Décisions RH</li>
@endsection

@push('styles')
<style>
.decision-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 12px;
    transition: box-shadow 180ms, border-color 180ms;
}
.decision-card:hover {
    box-shadow: 0 4px 16px rgba(10,77,140,0.08);
    border-color: #BFDBFE;
}
.badge-decision {
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-pending   { background: #FEF3C7; color: #92400E; }
.badge-signed    { background: #D1FAE5; color: #065F46; }
.badge-urgent    { background: #FEE2E2; color: #991B1B; }
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9CA3AF;
}
.empty-state i { font-size: 48px; color: #D1D5DB; margin-bottom: 16px; display: block; }
</style>
@endpush

@section('content')

{{-- En-tête --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">Décisions RH</h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            Validation et signature des décisions de la Direction des Ressources Humaines
        </p>
    </div>
    <a href="{{ route('drh.dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour
    </a>
</div>

{{-- Filtres --}}
<div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text" style="background:#F9FAFB;border-color:#E5E7EB;">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control" placeholder="Rechercher une décision..."
                       style="border-color:#E5E7EB;">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <select class="form-select form-select-sm" style="border-color:#E5E7EB;">
                <option value="">Tous les types</option>
                <option>Affectation</option>
                <option>Mutation</option>
                <option>Promotion</option>
                <option>Renouvellement contrat</option>
                <option>Sanction</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select class="form-select form-select-sm" style="border-color:#E5E7EB;">
                <option value="">Tous les statuts</option>
                <option>En attente de signature</option>
                <option>Signée</option>
                <option>Urgente</option>
            </select>
        </div>
    </div>
</div>

{{-- État vide (aucune décision pour l'instant) --}}
<div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;">
    <div style="padding:16px 24px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-weight:600;color:#111827;font-size:15px;">
            Décisions en attente de signature
        </div>
        <span style="background:#EFF6FF;color:#1565C0;font-size:11px;font-weight:600;padding:3px 12px;border-radius:20px;">
            0 décision(s)
        </span>
    </div>

    {{-- Liste vide --}}
    <div class="empty-state">
        <i class="fas fa-check-double"></i>
        <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">
            Aucune décision en attente
        </div>
        <div style="font-size:13px;">
            Toutes les décisions RH ont été traitées.<br>
            Les nouvelles décisions soumises par le service RH apparaîtront ici.
        </div>
    </div>
</div>

{{-- Historique récent --}}
<div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;margin-top:20px;">
    <div style="padding:16px 24px;border-bottom:1px solid #F3F4F6;">
        <div style="font-weight:600;color:#111827;font-size:15px;">Historique des décisions</div>
    </div>
    <div class="empty-state" style="padding:40px 20px;">
        <i class="fas fa-history" style="font-size:36px;"></i>
        <div style="font-size:13px;">Aucune décision dans l'historique pour l'instant.</div>
    </div>
</div>

{{-- Note informative --}}
<div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;margin-top:20px;">
    <div style="font-weight:600;color:#0A4D8C;margin-bottom:8px;">
        <i class="fas fa-info-circle me-2"></i>À propos des décisions RH
    </div>
    <div style="font-size:13px;color:#1E40AF;line-height:1.6;">
        Le module de gestion des décisions permet au DRH de valider et signer électroniquement
        toutes les décisions importantes : affectations, mutations, promotions, sanctions disciplinaires
        et renouvellements de contrats. Chaque décision signée génère un document PDF officiel
        enregistré dans la GED du système.
    </div>
</div>

@endsection
