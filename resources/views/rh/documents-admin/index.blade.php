@extends('layouts.master')
@section('title', 'Documents Administratifs')
@section('page-title', 'Documents Administratifs')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li>Documents administratifs</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-alt me-2" style="color:#0A4D8C;"></i>Documents Administratifs
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Génération de documents officiels pour les agents</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4 mb-4">
        @foreach([
            ['Attestation de travail', "Atteste qu'un agent est en activité au CHNP.", 'fa-certificate', '#EFF6FF', '#0A4D8C', 'attestation'],
            ['Certificat de travail', "Document officiel remis à la fin de la relation de travail.", 'fa-file-certificate', '#ECFDF5', '#059669', 'certificat'],
            ['Ordre de mission', "Autorisation de déplacement professionnel.", 'fa-route', '#FFFBEB', '#D97706', 'ordre-mission'],
            ['Décision d\'affectation', "Décision officielle d'affectation ou de mutation.", 'fa-stamp', '#F5F3FF', '#7C3AED', 'decision'],
        ] as [$title, $desc, $icon, $bg, $color, $type])
        <div class="col-12 col-md-6">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:52px;height:52px;background:{{ $bg }};border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:22px;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--theme-text);">{{ $title }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $desc }}</div>
                    </div>
                </div>
                <div class="input-group input-group-sm">
                    <select id="agent-{{ $type }}" class="form-select">
                        <option value="">Sélectionner un agent...</option>
                        @foreach(\App\Models\Agent::actif()->orderBy('nom')->get() as $agent)
                        <option value="{{ $agent->id_agent }}">{{ $agent->nom_complet }} — {{ $agent->matricule }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-sm" style="background:{{ $color }};color:#fff;"
                            onclick="genererDoc('{{ $type }}')">
                        <i class="fas fa-eye me-1"></i>Générer
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="alert alert-info" style="border-radius:10px;">
        <i class="fas fa-info-circle me-2"></i>
        <span style="font-size:13px;">
            Sélectionnez un agent puis cliquez sur "Générer" pour prévisualiser le document.
            Pour les décisions d'affectation, accédez directement depuis la page des mouvements.
        </span>
    </div>

</div>
@endsection

@push('scripts')
<script>
const routes = {
    'attestation'  : '/documents-admin/attestation/',
    'certificat'   : '/documents-admin/certificat/',
    'ordre-mission': '/documents-admin/ordre-mission/',
    'decision'     : null,
};

function genererDoc(type) {
    const sel = document.getElementById('agent-' + type);
    const agentId = sel.value;
    if (!agentId) {
        alert('Veuillez sélectionner un agent.');
        return;
    }
    if (routes[type]) {
        window.open(routes[type] + agentId, '_blank');
    }
}
</script>
@endpush
