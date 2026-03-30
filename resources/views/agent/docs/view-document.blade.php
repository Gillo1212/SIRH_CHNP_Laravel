@extends('layouts.master')
@section('title', $demande->libelleType . ' — ' . $agent->nom_complet)
@section('page-title', $demande->libelleType)

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Mon espace</a></li>
    <li><a href="{{ route('agent.docs.index') }}" style="color:#1565C0;">Mes documents</a></li>
    <li>{{ $demande->libelleType }}</li>
@endsection

@push('styles')
<style>
.doc-preview {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    max-width: 700px;
    margin: 0 auto;
    padding: 48px 60px;
    font-family: 'Times New Roman', serif;
}
.doc-header { text-align: center; margin-bottom: 32px; padding-bottom: 20px; }
.doc-title   { font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-top: 12px; }
.doc-body    { font-size: 14px; line-height: 1.8; color: #111; }
.doc-footer  { margin-top: 48px; }
.field-row   { display:flex; gap:16px; margin-bottom:12px; }
.field-label { font-weight:700; min-width:160px; flex-shrink:0; }
.field-value { flex:1; border-bottom:1px solid #374151; }

@media print {
    /* Masquer toute l'UI, n'imprimer que le document */
    body > *             { display: none !important; }
    .print-wrapper       { display: block !important; }
    .doc-preview         { border: none; padding: 20px; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── En-tête actions ──────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">{{ $demande->libelleType }}</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">
                Demande du {{ $demande->created_at?->format('d/m/Y') }}
                · Traité le {{ $demande->date_traitement?->format('d/m/Y') ?? '—' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm" style="border-radius:8px;">
                <i class="fas fa-print me-1"></i>Imprimer / Enregistrer PDF
            </button>
            <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    {{-- ── Corps du document (selon le type) ───────────── --}}
    <div class="print-wrapper">

        {{-- ══════════ ATTESTATION DE TRAVAIL ══════════ --}}
        @if($demande->type_document === 'attestation_travail')
        <div class="doc-preview">
            <div class="doc-header" style="border-bottom:2px solid #0A4D8C;">
                <div style="font-weight:700;font-size:16px;text-transform:uppercase;color:#0A4D8C;">Centre Hospitalier National de Pikine</div>
                <div style="font-size:13px;color:#6B7280;margin-top:4px;">Direction des Ressources Humaines</div>
                <div class="doc-title" style="color:#0A4D8C;">Attestation de travail</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:6px;">
                    Réf. : ATT-{{ date('Y') }}-{{ str_pad($agent->id_agent, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="doc-body">
                <p>
                    Je soussigné(e), le Directeur des Ressources Humaines du Centre Hospitalier National de Pikine (CHNP),
                    atteste par la présente que :
                </p>
                <p style="margin:24px 0;padding:16px;background:#F9FAFB;border-left:4px solid #0A4D8C;">
                    <strong>M./Mme {{ strtoupper($agent->nom) }} {{ $agent->prenom }}</strong>,
                    matricule <strong>{{ $agent->matricule }}</strong>,
                    est bien employé(e) au sein du Centre Hospitalier National de Pikine,
                    dans la famille d'emploi <strong>{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</strong>,
                    au service de <strong>{{ $agent->service?->nom_service ?? 'CHNP' }}</strong>.
                </p>
                <p>
                    L'intéressé(e) occupe actuellement la catégorie
                    <strong>{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</strong>.
                </p>
                <p>
                    La présente attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.
                </p>
            </div>

            <div class="doc-footer">
                <div class="row">
                    <div class="col-6">
                        <p style="font-size:13px;color:#6B7280;">Fait à Pikine, le {{ now()->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <p style="font-size:13px;font-weight:600;">Le Directeur RH</p>
                        <div style="height:60px;border-bottom:1px solid #374151;width:200px;margin-left:auto;margin-top:8px;"></div>
                        <p style="font-size:12px;color:#9CA3AF;margin-top:4px;">Signature et cachet</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════ CERTIFICAT DE TRAVAIL ══════════ --}}
        @elseif($demande->type_document === 'certificat_travail')
        <div class="doc-preview">
            <div class="doc-header" style="border-bottom:2px solid #059669;">
                <div style="font-weight:700;font-size:16px;text-transform:uppercase;color:#059669;">Centre Hospitalier National de Pikine</div>
                <div style="font-size:13px;color:#6B7280;margin-top:4px;">Direction des Ressources Humaines</div>
                <div class="doc-title" style="color:#059669;">Certificat de travail</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:6px;">
                    Réf. : CER-{{ date('Y') }}-{{ str_pad($agent->id_agent, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="doc-body">
                <p>Je soussigné(e), le Directeur des Ressources Humaines du Centre Hospitalier National de Pikine,
                   certifie que :</p>
                <p style="margin:24px 0;padding:16px;background:#F0FDF4;border-left:4px solid #059669;">
                    <strong>M./Mme {{ strtoupper($agent->nom) }} {{ $agent->prenom }}</strong>,
                    matricule <strong>{{ $agent->matricule }}</strong>,
                    a été employé(e) au CHNP dans la famille d'emploi
                    <strong>{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</strong>,
                    catégorie <strong>{{ str_replace('_', ' ', $agent->categorie_cp ?? '—') }}</strong>
                    @if($agent->statut_agent === 'Retraité')
                        au {{ now()->isoFormat('D MMMM YYYY') }}, date de fin de service.
                    @else
                        à ce jour.
                    @endif
                </p>
                @if($agent->date_prise_service)
                <p>
                    La prise de service remonte au <strong>{{ $agent->date_prise_service->format('d/m/Y') }}</strong>.
                </p>
                @endif
                <p>Le présent certificat est délivré à l'intéressé(e) à sa demande, pour servir et valoir ce que de droit.</p>
            </div>

            <div class="doc-footer">
                <div class="row">
                    <div class="col-6">
                        <p style="font-size:13px;color:#6B7280;">Fait à Pikine, le {{ now()->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <p style="font-size:13px;font-weight:600;">Le Directeur RH</p>
                        <div style="height:60px;border-bottom:1px solid #374151;width:200px;margin-left:auto;margin-top:8px;"></div>
                        <p style="font-size:12px;color:#9CA3AF;margin-top:4px;">Signature et cachet</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════ ORDRE DE MISSION ══════════ --}}
        @elseif($demande->type_document === 'ordre_mission')
        <div class="doc-preview">
            <div class="doc-header" style="border-bottom:2px solid #D97706;">
                <div style="font-weight:700;font-size:16px;text-transform:uppercase;color:#D97706;">Centre Hospitalier National de Pikine</div>
                <div style="font-size:13px;color:#6B7280;margin-top:4px;">Direction des Ressources Humaines</div>
                <div class="doc-title" style="color:#D97706;">
                    Ordre de mission N° OM-{{ date('Y') }}-{{ str_pad($agent->id_agent, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="doc-body">
                <p>Le Directeur des Ressources Humaines du Centre Hospitalier National de Pikine donne mission à :</p>
                <div style="margin:24px 0;">
                    <div class="field-row"><span class="field-label">Nom et prénom :</span><span class="field-value">{{ strtoupper($agent->nom) }} {{ $agent->prenom }}</span></div>
                    <div class="field-row"><span class="field-label">Matricule :</span><span class="field-value">{{ $agent->matricule }}</span></div>
                    <div class="field-row"><span class="field-label">Famille d'emploi :</span><span class="field-value">{{ str_replace('_', ' ', $agent->famille_d_emploi ?? '—') }}</span></div>
                    <div class="field-row"><span class="field-label">Service :</span><span class="field-value">{{ $agent->service?->nom_service ?? 'CHNP' }}</span></div>
                    <div class="field-row"><span class="field-label">Objet :</span><span class="field-value">{{ $demande->motif ?? '&nbsp;' }}</span></div>
                    <div class="field-row"><span class="field-label">Destination :</span><span class="field-value">&nbsp;</span></div>
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

        @else
            <div class="alert alert-warning">Type de document non reconnu.</div>
        @endif

    </div>{{-- /print-wrapper --}}

</div>
@endsection
