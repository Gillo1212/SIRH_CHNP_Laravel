@extends('layouts.master')
@section('title', 'PEC Exceptionnelles — Validation DRH')
@section('page-title', 'Prises en charge exceptionnelles')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>PEC Exceptionnelles</li>
@endsection

@push('styles')
<style>
.pec-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px 24px;margin-bottom:12px;transition:box-shadow 180ms,border-color 180ms;}
.pec-card:hover{box-shadow:0 4px 16px rgba(10,77,140,.08);border-color:#BFDBFE;}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--theme-text);">Prises en charge exceptionnelles</h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Validation DRH pour les PEC hors normes</p>
        </div>
        <div class="d-flex gap-2">
            <span style="background:{{ $stats['en_attente'] > 0 ? '#FEF3C7' : '#D1FAE5' }};color:{{ $stats['en_attente'] > 0 ? '#92400E' : '#065F46' }};padding:8px 18px;border-radius:8px;font-weight:700;font-size:14px;">
                {{ $stats['en_attente'] }} en attente
            </span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:12px;padding:16px 20px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#D97706;">{{ $stats['en_attente'] }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:3px;">En attente DRH</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:12px;padding:16px 20px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#059669;">{{ $stats['approuvees'] }}</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:3px;">Approuvées</div>
            </div>
        </div>
    </div>

    {{-- Liste des PEC exceptionnelles en attente --}}
    <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;margin-bottom:24px;">
        <div style="padding:16px 24px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-weight:600;color:var(--theme-text);font-size:15px;">
                <i class="fas fa-star me-2" style="color:#D97706;"></i>PEC exceptionnelles — en attente d'approbation DRH
            </div>
        </div>

        @forelse($pecsEnAttente as $pec)
        <div class="pec-card" style="margin-bottom:0;border-radius:0;border-left:none;border-right:none;border-top:none;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div style="font-weight:600;font-size:14px;color:var(--theme-text);">
                        {{ $pec->demande?->agent?->nom_complet ?? '—' }}
                        <span style="font-size:12px;color:#9CA3AF;font-weight:400;margin-left:8px;">{{ $pec->demande?->agent?->matricule }}</span>
                    </div>
                    <div style="font-size:13px;color:#6B7280;margin-top:3px;">
                        <span style="font-weight:600;color:#D97706;">{{ $pec->type_prise ?? '—' }}</span>
                        · Bénéficiaire : {{ $pec->ayant_droit ?? '—' }}
                        · {{ $pec->demande?->agent?->service?->nom_service ?? '—' }}
                    </div>
                    @if($pec->raison_medical)
                    <div style="font-size:12px;color:#9CA3AF;margin-top:2px;">{{ Str::limit($pec->raison_medical, 80) }}</div>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('drh.validations.valider-pec', $pec->id_priseenche) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Approuver cette prise en charge exceptionnelle ?')">
                        @csrf
                        <input type="hidden" name="action" value="approuver">
                        <button type="submit" style="display:inline-flex;align-items:center;gap:8px;padding:8px 18px;border-radius:8px;background:#059669;color:#fff;border:none;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-check"></i>Approuver
                        </button>
                    </form>
                    <form action="{{ route('drh.validations.valider-pec', $pec->id_priseenche) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Rejeter cette prise en charge ?')">
                        @csrf
                        <input type="hidden" name="action" value="rejeter">
                        <button type="submit" style="display:inline-flex;align-items:center;gap:8px;padding:8px 18px;border-radius:8px;background:#EF4444;color:#fff;border:none;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-times"></i>Rejeter
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-check-double" style="font-size:40px;color:#D1D5DB;margin-bottom:12px;display:block;"></i>
            <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">Aucune PEC exceptionnelle en attente</div>
            <div style="font-size:13px;color:#9CA3AF;">Toutes les demandes ont été traitées.</div>
        </div>
        @endforelse

        @if($pecsEnAttente->hasPages())
        <div style="padding:12px 24px;border-top:1px solid #F3F4F6;">{{ $pecsEnAttente->links() }}</div>
        @endif
    </div>

    {{-- Info --}}
    <div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;margin-top:20px;">
        <div style="font-weight:600;color:#0A4D8C;margin-bottom:8px;"><i class="fas fa-info-circle me-2"></i>À propos des PEC exceptionnelles</div>
        <div style="font-size:13px;color:#1E40AF;line-height:1.6;">
            Les prises en charge marquées comme exceptionnelles nécessitent une approbation du DRH en plus de la validation RH standard.
            Elles apparaissent ici après avoir été validées par le service RH.
        </div>
    </div>

</div>
@endsection
