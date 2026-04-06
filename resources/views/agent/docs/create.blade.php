@extends('layouts.master')
@section('title', 'Nouvelle demande de document')
@section('page-title', 'Demande de document administratif')

@section('breadcrumb')
    <li><a href="{{ route('agent.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('agent.docs.index') }}" style="color:#1565C0;">Mes documents</a></li>
    <li>Nouvelle demande</li>
@endsection

@push('styles')
<style>
.type-card {
    border: 2px solid #E5E7EB; border-radius: 12px; padding: 16px;
    cursor: pointer; transition: all 150ms; background: #fff;
    display: flex; flex-direction: column; gap: 8px; height: 100%;
}
.type-card:hover { border-color: #93C5FD; background: #F8FAFF; }
.type-card.selected { border-color: #1D4ED8; background: #EFF6FF; }
.type-card .tc-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.type-card .tc-title { font-size: 13px; font-weight: 600; color: #1E293B; }
.type-card .tc-desc  { font-size: 11px; color: #9CA3AF; line-height: 1.4; }
.type-card .tc-check {
    margin-left: auto; width: 20px; height: 20px; border-radius: 50%;
    background: #1D4ED8; display: none; align-items: center; justify-content: center;
}
.type-card.selected .tc-check { display: flex; }

.cat-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #6B7280; margin-bottom: 12px;
    display: flex; align-items: center; gap: 8px;
}
.cat-label::after { content:''; flex:1; height:1px; background:#E5E7EB; }

.form-panel {
    background: #fff; border: 1px solid #E5E7EB; border-radius: 14px;
    overflow: hidden; margin-top: 24px;
}
.form-panel-header {
    background: linear-gradient(135deg, #0A4D8C 0%, #1E3A5F 100%);
    color: white; padding: 18px 24px;
}
.form-panel-body { padding: 24px; }

.field-group { margin-bottom: 18px; }
.field-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.field-required { color: #DC2626; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4" x-data="docForm()" x-cloak>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-plus me-2" style="color:#0A4D8C;"></i>Nouvelle demande de document
            </h4>
            <p class="mb-0 text-muted" style="font-size:13px;">Sélectionnez le type de document souhaité</p>
        </div>
        <a href="{{ route('agent.docs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="border-radius:10px;">
        <ul class="mb-0" style="font-size:13px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Info --}}
    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px 18px;margin-bottom:24px;font-size:13px;color:#1E40AF;">
        <i class="fas fa-info-circle me-2"></i>
        Votre demande sera traitée par le service RH dans les meilleurs délais. Vous serez notifié lorsque votre document sera prêt.
    </div>

    {{-- Catalogue par catégorie --}}
    @php
        $catIcons = [
            'attestations' => ['bg'=>'#EFF6FF','color'=>'#1D4ED8','fas'=>'fa-file-alt'],
            'conges'       => ['bg'=>'#FDF4FF','color'=>'#9333EA','fas'=>'fa-calendar-check'],
            'mouvements'   => ['bg'=>'#F0F9FF','color'=>'#0284C7','fas'=>'fa-exchange-alt'],
            'missions'     => ['bg'=>'#FFF7ED','color'=>'#EA580C','fas'=>'fa-plane'],
            'autres'       => ['bg'=>'#F0FDF4','color'=>'#059669','fas'=>'fa-file-signature'],
        ];
        $typeIcons = [
            'attestation_travail'             => ['bg'=>'#EFF6FF','color'=>'#1D4ED8','fas'=>'fa-file-contract', 'desc'=>'Atteste de votre emploi au CHNP'],
            'certificat_travail'              => ['bg'=>'#F0FDF4','color'=>'#059669','fas'=>'fa-certificate',   'desc'=>'Récapitulatif de votre parcours au CHNP'],
            'decision_conge_administratif'    => ['bg'=>'#FDF4FF','color'=>'#9333EA','fas'=>'fa-calendar-check','desc'=>'Décision officielle pour votre congé administratif'],
            'attestation_jouissance_conge'    => ['bg'=>'#FDF4FF','color'=>'#9333EA','fas'=>'fa-calendar',      'desc'=>'Autorise la jouissance partielle du congé'],
            'attestation_cessation_maternite' => ['bg'=>'#FFF1F2','color'=>'#E11D48','fas'=>'fa-heart',         'desc'=>'Cessation de service pour congé maternité'],
            'note_affectation'                => ['bg'=>'#F0F9FF','color'=>'#0284C7','fas'=>'fa-exchange-alt',  'desc'=>"Note officielle d'affectation à un service"],
            'note_interim'                    => ['bg'=>'#F0F9FF','color'=>'#0284C7','fas'=>'fa-user-clock',    'desc'=>'Note de service pour assurer un intérim'],
            'ordre_mission'                   => ['bg'=>'#FEF3C7','color'=>'#D97706','fas'=>'fa-route',         'desc'=>'Autorisation de déplacement en mission'],
            'autorisation_sortie_territoire'  => ['bg'=>'#FFF7ED','color'=>'#EA580C','fas'=>'fa-plane',         'desc'=>'Autorisation de sortie du territoire national'],
            'attestation_prime_motivation'    => ['bg'=>'#F0FDF4','color'=>'#059669','fas'=>'fa-award',         'desc'=>"Atteste du bénéfice d'une prime de motivation"],
            'attestation_prise_service'       => ['bg'=>'#F8FAFF','color'=>'#4F46E5','fas'=>'fa-user-check',    'desc'=>'Confirme la prise de service effective'],
            'attestation_stage'               => ['bg'=>'#FFFBEB','color'=>'#B45309','fas'=>'fa-graduation-cap','desc'=>'Atteste d\'un stage effectué au CHNP'],
        ];
    @endphp

    @foreach($categories as $catKey => $cat)
    <div class="mb-4">
        <div class="cat-label">
            <i class="fas {{ $catIcons[$catKey]['fas'] ?? 'fa-folder' }}" style="color:{{ $catIcons[$catKey]['color'] ?? '#6B7280' }};"></i>
            {{ $cat['label'] }}
        </div>
        <div class="row g-3">
            @foreach($cat['types'] as $typeKey)
            @php $ti = $typeIcons[$typeKey] ?? ['bg'=>'#F3F4F6','color'=>'#6B7280','fas'=>'fa-file','desc'=>'']; @endphp
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="type-card" :class="{ 'selected': selectedType === '{{ $typeKey }}' }"
                     @click="selectType('{{ $typeKey }}', '{{ $types[$typeKey] }}')"
                     tabindex="0" @keydown.enter="selectType('{{ $typeKey }}', '{{ $types[$typeKey] }}')">
                    <div class="d-flex align-items-start gap-2">
                        <div class="tc-icon" style="background:{{ $ti['bg'] }};">
                            <i class="fas {{ $ti['fas'] }}" style="color:{{ $ti['color'] }};font-size:18px;"></i>
                        </div>
                        <div class="tc-check">
                            <i class="fas fa-check" style="font-size:10px;color:#fff;"></i>
                        </div>
                    </div>
                    <div class="tc-title">{{ $types[$typeKey] }}</div>
                    <div class="tc-desc">{{ $ti['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Panneau formulaire (s'affiche quand un type est sélectionné) --}}
    <div class="form-panel" x-show="selectedType !== ''" x-transition>
        <div class="form-panel-header">
            <div style="font-size:16px;font-weight:600;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-edit"></i>
                <span x-text="selectedLabel"></span>
            </div>
            <p style="font-size:12px;opacity:.8;margin:4px 0 0;">Renseignez les informations ci-dessous pour votre demande</p>
        </div>
        <div class="form-panel-body">
            <form action="{{ route('agent.docs.store') }}" method="POST" id="doc-form">
                @csrf
                <input type="hidden" name="type_document" :value="selectedType">

                {{-- Champs dynamiques par type --}}
                @foreach($champsParType as $typeKey => $champs)
                @if(count($champs) > 0)
                <div x-show="selectedType === '{{ $typeKey }}'">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #F3F4F6;">
                        Informations spécifiques
                    </div>
                    <div class="row g-3 mb-4">
                        @foreach($champs as $champKey => $champConfig)
                        <div class="{{ in_array($champConfig['type'], ['textarea']) ? 'col-12' : 'col-sm-6' }}">
                            <div class="field-group">
                                <label class="field-label" for="{{ $typeKey }}_{{ $champKey }}">
                                    {{ $champConfig['label'] }}
                                    @if($champConfig['required'] ?? false)<span class="field-required">*</span>@endif
                                </label>
                                @if($champConfig['type'] === 'textarea')
                                    <textarea class="form-control" id="{{ $typeKey }}_{{ $champKey }}"
                                              name="{{ $champKey }}" rows="3"
                                              placeholder="{{ $champConfig['label'] }}…">{{ old($champKey, $champConfig['default'] ?? '') }}</textarea>
                                @elseif($champConfig['type'] === 'select')
                                    <select class="form-select" id="{{ $typeKey }}_{{ $champKey }}" name="{{ $champKey }}">
                                        <option value="">Sélectionner…</option>
                                        @foreach($champConfig['options'] as $optVal => $optLabel)
                                            <option value="{{ $optVal }}" {{ old($champKey) === $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                @elseif($champConfig['type'] === 'number')
                                    <input type="number" class="form-control" id="{{ $typeKey }}_{{ $champKey }}"
                                           name="{{ $champKey }}"
                                           value="{{ old($champKey, $champConfig['default'] ?? '') }}"
                                           min="1">
                                @elseif($champConfig['type'] === 'date')
                                    <input type="date" class="form-control" id="{{ $typeKey }}_{{ $champKey }}"
                                           name="{{ $champKey }}"
                                           value="{{ old($champKey, $champConfig['default'] ?? '') }}"
                                           min="{{ now()->format('Y-m-d') }}">
                                @else
                                    <input type="text" class="form-control" id="{{ $typeKey }}_{{ $champKey }}"
                                           name="{{ $champKey }}"
                                           value="{{ old($champKey, $champConfig['default'] ?? '') }}"
                                           placeholder="{{ $champConfig['label'] }}…">
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach

                {{-- Motif (toujours visible) --}}
                <div class="field-group">
                    <label class="field-label" for="motif">Motif de la demande <span style="color:#9CA3AF;font-weight:400;">(optionnel)</span></label>
                    <textarea class="form-control" name="motif" id="motif" rows="3"
                              placeholder="Précisez la raison de votre demande…">{{ old('motif') }}</textarea>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Soumettre la demande
                    </button>
                    <button type="button" class="btn btn-outline-secondary" @click="selectedType = ''; selectedLabel = ''">
                        Changer de type
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Aucun type sélectionné --}}
    <div x-show="selectedType === ''" class="text-center py-4 text-muted" style="font-size:13px;">
        <i class="fas fa-hand-pointer me-1"></i> Cliquez sur un type de document ci-dessus pour continuer
    </div>

</div>
@endsection

@push('scripts')
<script>
function docForm() {
    return {
        selectedType: '{{ old('type_document', '') }}',
        selectedLabel: '',
        selectType(type, label) {
            this.selectedType = type;
            this.selectedLabel = label;
            this.$nextTick(() => {
                document.getElementById('doc-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    }
}
</script>
@endpush
