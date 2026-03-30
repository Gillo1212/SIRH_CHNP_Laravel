@extends('layouts.master')
@section('title', 'Certificat de travail — ' . $agent->nom_complet)
@section('page-title', 'Certificat de travail')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('documents-admin.index') }}" style="color:#1565C0;">Documents</a></li>
    <li>Certificat</li>
@endsection

@push('styles')
<style>
.doc-preview{background:#fff;border:1px solid #E5E7EB;border-radius:12px;max-width:700px;margin:0 auto;padding:48px 60px;font-family:'Times New Roman',serif;}
.doc-header{text-align:center;margin-bottom:32px;border-bottom:2px solid #059669;padding-bottom:20px;}
.doc-title{font-size:18px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:#059669;margin-top:12px;}
.doc-body{font-size:14px;line-height:1.8;color:#111;}
.doc-footer{margin-top:48px;}
@media print{body>*{display:none;}.doc-preview{display:block!important;border:none;}}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Certificat de travail</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Pour : {{ $agent->nom_complet }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print me-1"></i>Imprimer</button>
            <a href="{{ route('documents-admin.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
    </div>

    <div class="doc-preview">
        <div class="doc-header">
            <div style="font-weight:700;font-size:16px;text-transform:uppercase;color:#059669;">Centre Hospitalier National de Pikine</div>
            <div style="font-size:13px;color:#6B7280;margin-top:4px;">Direction des Ressources Humaines</div>
            <div class="doc-title">Certificat de travail</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:6px;">Réf. : CER-{{ date('Y') }}-{{ str_pad($agent->id_agent, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="doc-body">
            <p>Je soussigné(e), le Directeur des Ressources Humaines du Centre Hospitalier National de Pikine, certifie que :</p>
            <p style="margin:24px 0;padding:16px;background:#F0FDF4;border-left:4px solid #059669;">
                <strong>M./Mme {{ strtoupper($agent->nom) }} {{ $agent->prenom }}</strong>,
                matricule <strong>{{ $agent->matricule }}</strong>,
                a été employé(e) au CHNP dans la famille d'emploi <strong>{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</strong>,
                catégorie <strong>{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</strong>
                @if($agent->statut_agent === 'Retraité') au {{ now()->isoFormat('D MMMM YYYY') }}, date de fin de service @else à ce jour @endif.
            </p>
            <p>Ce certificat est établi pour servir et valoir ce que de droit et ne préjuge en rien du motif du départ.</p>
        </div>
        <div class="doc-footer">
            <div class="row">
                <div class="col-6"><p style="font-size:13px;color:#6B7280;">Fait à Pikine, le {{ now()->isoFormat('D MMMM YYYY') }}</p></div>
                <div class="col-6 text-end">
                    <p style="font-size:13px;font-weight:600;">Le Directeur RH</p>
                    <div style="height:60px;border-bottom:1px solid #374151;width:200px;margin-left:auto;margin-top:8px;"></div>
                    <p style="font-size:12px;color:#9CA3AF;margin-top:4px;">Signature et cachet</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
