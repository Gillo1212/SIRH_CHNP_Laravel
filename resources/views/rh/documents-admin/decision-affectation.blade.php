@extends('layouts.master')
@section('title', 'Décision d\'affectation — ' . $mouvement->agent->nom_complet)
@section('page-title', 'Décision d\'affectation')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.mouvements.index') }}" style="color:#1565C0;">Mouvements</a></li>
    <li>Décision</li>
@endsection

@push('styles')
<style>
.doc-preview{background:#fff;border:1px solid #E5E7EB;border-radius:12px;max-width:700px;margin:0 auto;padding:48px 60px;font-family:'Times New Roman',serif;}
.doc-header{text-align:center;margin-bottom:32px;border-bottom:2px solid #7C3AED;padding-bottom:20px;}
.doc-title{font-size:18px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:#7C3AED;margin-top:12px;}
@media print{body>*{display:none;}.doc-preview{display:block!important;border:none;}}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Décision d'affectation</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">{{ $mouvement->agent->nom_complet }} — {{ $mouvement->type_mouvement }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print me-1"></i>Imprimer</button>
            <a href="{{ route('rh.mouvements.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
    </div>

    <div class="doc-preview">
        <div class="doc-header">
            <div style="font-weight:700;font-size:16px;text-transform:uppercase;color:#7C3AED;">Centre Hospitalier National de Pikine</div>
            <div style="font-size:13px;color:#6B7280;margin-top:4px;">Direction des Ressources Humaines</div>
            <div class="doc-title">Décision {{ $mouvement->decision_generee ?? 'N° DEC-' . date('Y') . '-' . str_pad($mouvement->id_mouvement, 4, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size:13px;margin-top:6px;">Portant {{ strtolower($mouvement->type_mouvement) }} de personnel</div>
        </div>
        <div style="font-size:14px;line-height:1.8;color:#111;">
            <p>Le Directeur du Centre Hospitalier National de Pikine,</p>
            <p>Vu le cadre organique du CHNP ;<br>Vu la demande formulée ;<br>Sur proposition du Directeur des Ressources Humaines,</p>
            <p style="text-align:center;font-weight:700;margin:20px 0;">DÉCIDE :</p>
            <p>
                <strong>Article 1 :</strong>
                M./Mme <strong>{{ strtoupper($mouvement->agent->nom) }} {{ $mouvement->agent->prenom }}</strong>,
                matricule <strong>{{ $mouvement->agent->matricule }}</strong>,
                {{ $mouvement->type_mouvement === 'Mutation' ? 'est muté(e)' : ($mouvement->type_mouvement === 'Départ' ? 'est mis(e) fin à ses fonctions' : 'est affecté(e)') }}
                @if($mouvement->serviceDestination)
                au service de <strong>{{ $mouvement->serviceDestination->nom_service }}</strong>
                @endif
                à compter du <strong>{{ $mouvement->date_mouvement?->isoFormat('D MMMM YYYY') }}</strong>.
            </p>
            @if($mouvement->motif)
            <p><strong>Article 2 :</strong> Motif : {{ $mouvement->motif }}</p>
            @endif
            <p><strong>Article {{ $mouvement->motif ? 3 : 2 }} :</strong> La présente décision sera communiquée à toutes les parties concernées.</p>

            <div style="margin-top:48px;">
                <div class="row">
                    <div class="col-6">
                        <p style="font-size:13px;color:#6B7280;">
                            Fait à Pikine, le {{ ($mouvement->date_signature ?? now())->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <p style="font-size:13px;font-weight:600;">Le Directeur des Ressources Humaines</p>
                        @if($mouvement->date_signature)
                        <div style="height:60px;border-bottom:2px solid #0A4D8C;width:200px;margin-left:auto;margin-top:8px;position:relative;">
                            <div style="position:absolute;bottom:4px;left:0;right:0;text-align:center;font-size:11px;color:#0A4D8C;font-style:italic;">
                                Signé électroniquement
                            </div>
                        </div>
                        <p style="font-size:12px;color:#374151;font-weight:600;margin-top:4px;">
                            {{ $mouvement->signataire?->name ?? '—' }}
                        </p>
                        <p style="font-size:11px;color:#9CA3AF;margin-top:0;">
                            {{ $mouvement->date_signature->format('d/m/Y à H:i') }}
                        </p>
                        @else
                        <div style="height:60px;border-bottom:1px solid #374151;width:200px;margin-left:auto;margin-top:8px;"></div>
                        <p style="font-size:12px;color:#9CA3AF;margin-top:4px;">Signature et cachet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
