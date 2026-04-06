@extends('layouts.master')

@section('title', $libelleType . ' - ' . $agent->nom_complet)
@section('page-title', 'Générer : ' . $libelleType)

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('documents-admin.index') }}" style="color:#1565C0;">Documents</a></li>
    <li><a href="{{ route('documents-admin.select-type', $agent->id_agent) }}" style="color:#1565C0;">Sélection</a></li>
    <li>{{ $libelleType }}</li>
@endsection

@push('styles')
<style>
.form-card { background: white; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; }
.form-card-header {
    background: linear-gradient(135deg, #0A4D8C 0%, #1E3A5F 100%);
    color: white; padding: 20px 24px;
}
.form-card-header h5 { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
.form-card-header p { font-size: 13px; opacity: 0.85; margin: 0; }
.form-card-body { padding: 24px; }

.agent-summary {
    background: #F8FAFC; border: 1px solid #E2E8F0;
    border-radius: 8px; padding: 16px; margin-bottom: 24px;
}
.agent-summary-row { display: flex; flex-wrap: wrap; gap: 24px; }
.agent-summary-item { flex: 1; min-width: 150px; }
.agent-summary-label { font-size: 11px; text-transform: uppercase; color: #64748B; margin-bottom: 2px; }
.agent-summary-value { font-size: 14px; font-weight: 500; color: #1E293B; }

.form-section { margin-bottom: 24px; }
.form-section-title {
    font-size: 14px; font-weight: 600; color: #374151;
    margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #E5E7EB;
}
.form-section-title i { color: #0A4D8C; margin-right: 8px; }

.form-group { margin-bottom: 16px; }
.form-label { font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
.form-label .required { color: #DC2626; }
.form-control, .form-select { border-radius: 8px; border: 1px solid #D1D5DB; padding: 10px 14px; font-size: 14px; }
.form-control:focus, .form-select:focus { border-color: #0A4D8C; box-shadow: 0 0 0 3px rgba(10, 77, 140, 0.1); }
.form-text { font-size: 12px; color: #6B7280; margin-top: 4px; }

.btn-preview {
    background: linear-gradient(135deg, #0A4D8C 0%, #1E3A5F 100%);
    border: none; color: white; padding: 12px 24px; font-weight: 500;
}
.btn-preview:hover { background: linear-gradient(135deg, #083D6F 0%, #162D4A 100%); color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="form-card">
                <div class="form-card-header">
                    @if(isset($sourceId))
                        <h5><i class="fas fa-edit me-2"></i>Modifier : {{ $libelleType }}</h5>
                        <p>Modifiez les informations puis cliquez sur Regénérer pour mettre à jour le document.</p>
                    @else
                        <h5><i class="fas fa-file-alt me-2"></i>{{ $libelleType }}</h5>
                        <p>Renseignez les informations nécessaires pour générer le document.</p>
                    @endif
                </div>

                <div class="form-card-body">
                    <div class="agent-summary">
                        <div class="agent-summary-row">
                            <div class="agent-summary-item">
                                <div class="agent-summary-label">Agent</div>
                                <div class="agent-summary-value">{{ $agent->nom_complet }}</div>
                            </div>
                            <div class="agent-summary-item">
                                <div class="agent-summary-label">Matricule</div>
                                <div class="agent-summary-value">{{ $agent->matricule }}</div>
                            </div>
                            <div class="agent-summary-item">
                                <div class="agent-summary-label">Fonction</div>
                                <div class="agent-summary-value">{{ $agent->fonction ?? '-' }}</div>
                            </div>
                            <div class="agent-summary-item">
                                <div class="agent-summary-label">Service</div>
                                <div class="agent-summary-value">{{ $agent->service?->nom_service ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    @if(isset($sourceId))
                        @php
                            $formAction = route('documents-admin.update', $sourceId);
                            $formMethod = 'POST';
                        @endphp
                    @else
                        @php
                            $formAction = route('documents-admin.preview', ['agentId' => $agent->id_agent, 'type' => $type]);
                            $formMethod = 'POST';
                        @endphp
                    @endif

                    <form action="{{ $formAction }}" method="{{ $formMethod }}">
                        @csrf
                        @if(isset($sourceId))
                            @method('PUT')
                        @endif

                        @if(count($champs) > 0)
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-edit"></i>Informations spécifiques</div>

                            @foreach($champs as $nom => $config)
                                <div class="form-group">
                                    <label class="form-label" for="{{ $nom }}">
                                        {{ $config['label'] }}
                                        @if(!isset($config['optional']) || !$config['optional'])<span class="required">*</span>@endif
                                    </label>

                                    @switch($config['type'])
                                        @case('text')
                                            <input type="text" class="form-control" id="{{ $nom }}" name="{{ $nom }}" 
                                                   value="{{ old($nom, $config['default'] ?? '') }}">
                                            @break
                                        @case('textarea')
                                            <textarea class="form-control" id="{{ $nom }}" name="{{ $nom }}" rows="3">{{ old($nom, $config['default'] ?? '') }}</textarea>
                                            @break
                                        @case('number')
                                            <input type="number" class="form-control" id="{{ $nom }}" name="{{ $nom }}" 
                                                   value="{{ old($nom, $config['default'] ?? '') }}">
                                            @break
                                        @case('date')
                                            <input type="date" class="form-control" id="{{ $nom }}" name="{{ $nom }}" 
                                                   value="{{ old($nom, $config['default'] ?? now()->format('Y-m-d')) }}">
                                            @break
                                        @case('select')
                                            <select class="form-select" id="{{ $nom }}" name="{{ $nom }}">
                                                <option value="">Sélectionner...</option>
                                                @foreach($config['options'] as $option)
                                                    <option value="{{ $option }}" {{ old($nom, $config['default'] ?? '') == $option ? 'selected' : '' }}>
                                                        {{ ucfirst($option) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @break
                                        @case('select_service')
                                            <select class="form-select" id="{{ $nom }}" name="{{ $nom }}">
                                                <option value="">Sélectionner un service...</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id_service }}">{{ $service->nom_service }}</option>
                                                @endforeach
                                            </select>
                                            @break
                                        @case('select_agent')
                                            <select class="form-select" id="{{ $nom }}" name="{{ $nom }}">
                                                <option value="">Sélectionner un agent...</option>
                                                @foreach($agents as $ag)
                                                    @if($ag->id_agent !== $agent->id_agent)
                                                    <option value="{{ $ag->id_agent }}">{{ $ag->nom_complet }} ({{ $ag->matricule }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @break
                                    @endswitch
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Ce document ne nécessite pas d'informations supplémentaires.
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            @if(isset($sourceId))
                                <a href="{{ route('documents-admin.show-generated', $sourceId) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-preview">
                                    <i class="fas fa-sync-alt me-1"></i>Regénérer le document
                                </button>
                            @else
                                <a href="{{ route('documents-admin.select-type', $agent->id_agent) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Retour
                                </a>
                                <button type="submit" class="btn btn-preview">
                                    <i class="fas fa-eye me-1"></i>Prévisualiser
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
