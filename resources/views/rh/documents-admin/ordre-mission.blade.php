@extends('layouts.master')
@section('title', 'Ordre de mission — ' . $agent->nom_complet)
@section('page-title', 'Ordre de mission')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('documents-admin.index') }}" style="color:#1565C0;">Documents</a></li>
    <li>Ordre de mission</li>
@endsection

@push('styles')
<style>
.doc-preview{background:#fff;border:1px solid #E5E7EB;border-radius:12px;max-width:700px;margin:0 auto;padding:48px 60px;font-family:'Times New Roman',serif;}
.doc-header{text-align:center;margin-bottom:32px;border-bottom:2px solid #D97706;padding-bottom:20px;}
.doc-title{font-size:18px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:#D97706;margin-top:12px;}
.doc-body{font-size:14px;line-height:1.8;color:#111;}
.field-row{display:flex;gap:16px;margin-bottom:12px;}
.field-label{font-weight:700;min-width:160px;flex-shrink:0;}
.field-value{flex:1;border-bottom:1px solid #374151;}
@media print{body>*{display:none;}.doc-preview{display:block!important;border:none;}}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Ordre de mission</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Pour : {{ $agent->nom_complet }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print me-1"></i>Imprimer</button>
            <a href="{{ route('documents-admin.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
    </div>

    <div class="doc-preview">
        <div class="doc-header">
            <div style="font-weight:700;font-size:16px;text-transform:uppercase;color:#D97706;">Centre Hospitalier National de Pikine</div>
            <div style="font-size:13px;color:#6B7280;margin-top:4px;">Direction des Ressources Humaines</div>
            <div class="doc-title">Ordre de mission N° OM-{{ date('Y') }}-{{ str_pad($agent->id_agent, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="doc-body">
            <p>Le Directeur des Ressources Humaines du Centre Hospitalier National de Pikine donne mission à :</p>
            <div style="margin:24px 0;">
                <div class="field-row"><span class="field-label">Nom et prénom :</span><span class="field-value">{{ strtoupper($agent->nom) }} {{ $agent->prenom }}</span></div>
                <div class="field-row"><span class="field-label">Matricule :</span><span class="field-value">{{ $agent->matricule }}</span></div>
                <div class="field-row"><span class="field-label">Famille d'emploi :</span><span class="field-value">{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</span></div>
                <div class="field-row"><span class="field-label">Service :</span><span class="field-value">{{ $agent->service?->nom_service ?? 'CHNP' }}</span></div>
                <div class="field-row"><span class="field-label">Destination :</span><span class="field-value">&nbsp;</span></div>
                <div class="field-row"><span class="field-label">Objet de la mission :</span><span class="field-value">&nbsp;</span></div>
                <div class="field-row"><span class="field-label">Date de départ :</span><span class="field-value">&nbsp;</span></div>
                <div class="field-row"><span class="field-label">Date de retour :</span><span class="field-value">&nbsp;</span></div>
            </div>
            <p>Le présent ordre de mission est valable pour la durée indiquée ci-dessus.</p>
        </div>
        <div style="margin-top:48px;">
            <div class="row">
                <div class="col-6 text-center">
                    <p style="font-size:13px;font-weight:600;">L'intéressé(e)</p>
                    <div style="height:60px;border-bottom:1px solid #374151;width:180px;margin:8px auto;"></div>
                    <p style="font-size:12px;color:#9CA3AF;">Signature</p>
                </div>
                <div class="col-6 text-center">
                    <p style="font-size:13px;font-weight:600;">Le Directeur RH</p>
                    <div style="height:60px;border-bottom:1px solid #374151;width:180px;margin:8px auto;"></div>
                    <p style="font-size:12px;color:#9CA3AF;">Signature et cachet</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
