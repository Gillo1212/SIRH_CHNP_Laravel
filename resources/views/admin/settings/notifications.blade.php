@extends('layouts.master')
@section('title', 'Configuration des notifications')
@section('page-title', 'Notifications')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li><a href="{{ route('admin.settings.index') }}" style="color:#1565C0;">Paramètres</a></li>
    <li>Notifications</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-bell me-2" style="color:#7C3AED;"></i>Configuration des notifications
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Activez ou désactivez les notifications par événement</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour aux paramètres
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:24px;max-width:720px;">

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="notifications">

            @php
            $notifItems = [
                'conge_demande'      => ['icon' => 'fa-umbrella-beach', 'color' => '#0A4D8C',
                                         'label' => 'Nouvelle demande de congé',
                                         'desc'  => 'Le Manager reçoit une notification quand un agent soumet une demande'],
                'conge_valide'       => ['icon' => 'fa-check-circle',   'color' => '#059669',
                                         'label' => 'Congé approuvé',
                                         'desc'  => 'L\'agent est notifié lorsque son congé est approuvé par le RH'],
                'conge_rejete'       => ['icon' => 'fa-times-circle',   'color' => '#DC2626',
                                         'label' => 'Congé rejeté',
                                         'desc'  => 'L\'agent est notifié lorsque son congé est refusé'],
                'contrat_expiration' => ['icon' => 'fa-file-contract',  'color' => '#D97706',
                                         'label' => 'Alerte expiration contrat',
                                         'desc'  => 'L\'Agent RH est alerté 60 jours avant l\'expiration d\'un contrat'],
                'document_pret'      => ['icon' => 'fa-file-alt',       'color' => '#7C3AED',
                                         'label' => 'Document administratif prêt',
                                         'desc'  => 'L\'agent est notifié quand son document est disponible au téléchargement'],
                'pec_traitement'     => ['icon' => 'fa-heartbeat',      'color' => '#EC4899',
                                         'label' => 'Prise en charge traitée',
                                         'desc'  => 'L\'agent est notifié du traitement de sa demande de PEC'],
                'mouvement_valide'   => ['icon' => 'fa-people-arrows',  'color' => '#0891B2',
                                         'label' => 'Mouvement RH validé',
                                         'desc'  => 'L\'agent et le RH sont notifiés après validation DRH'],
            ];
            @endphp

            @foreach($notifItems as $key => $item)
            <div class="d-flex align-items-center justify-content-between py-3"
                 style="border-bottom:1px solid var(--theme-border);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:{{ $item['color'] }}18;border-radius:10px;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas {{ $item['icon'] }}" style="color:{{ $item['color'] }};font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--theme-text);">{{ $item['label'] }}</div>
                        <div style="font-size:11px;color:#9CA3AF;margin-top:1px;">{{ $item['desc'] }}</div>
                    </div>
                </div>
                <div style="flex-shrink:0;margin-left:16px;">
                    <input type="hidden" name="{{ $key }}" value="0">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                               @checked(\App\Models\Setting::get('notifications.' . $key, true))
                               style="width:2.4em;height:1.2em;cursor:pointer;">
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mt-4 pt-2 d-flex justify-content-between align-items-center">
                <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:10px 14px;font-size:12px;color:#1E40AF;">
                    <i class="fas fa-info-circle me-1"></i>
                    Les notifications sont envoyées via le système Laravel Notifications (base de données).
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-4 ms-3">
                    <i class="fas fa-save me-1"></i>Enregistrer
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
