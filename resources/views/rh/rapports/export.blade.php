@extends('layouts.master')
@section('title', 'Export données RH')
@section('page-title', 'Export données')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.rapports.index') }}" style="color:#1565C0;">Rapports</a></li>
    <li>Export</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--theme-text);">
                <i class="fas fa-file-export me-2" style="color:#D97706;"></i>Export des données RH
            </h4>
            <p class="mb-0 text-muted" style="font-size:13.5px;">Sélectionnez le type d'export et les paramètres</p>
        </div>
        <a href="{{ route('rh.rapports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Rapports
        </a>
    </div>

    <div class="row g-4">
        {{-- Export Personnel --}}
        <div class="col-12 col-md-6">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:48px;height:48px;background:#ECFDF5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-users" style="color:#059669;font-size:20px;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--theme-text);">Export Personnel</div>
                        <div style="font-size:12px;color:#9CA3AF;">Liste des agents avec filtres</div>
                    </div>
                </div>
                <form action="{{ route('rh.rapports.effectifs') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Service</label>
                        <select name="service" class="form-select form-select-sm">
                            <option value="">Tous les services</option>
                            @foreach($services as $svc)
                            <option value="{{ $svc->id_service }}">{{ $svc->nom_service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6B7280;">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="actif">Actifs</option>
                            <option value="en_conge">En congé</option>
                            <option value="suspendu">Suspendus</option>
                            <option value="retraite">Retraités</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>Voir le rapport effectifs
                    </button>
                </form>
            </div>
        </div>

        {{-- Export Absences --}}
        <div class="col-12 col-md-6">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:48px;height:48px;background:#FEF2F2;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user-times" style="color:#DC2626;font-size:20px;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--theme-text);">Export Absences</div>
                        <div style="font-size:12px;color:#9CA3AF;">Format CSV avec BOM UTF-8</div>
                    </div>
                </div>
                <p style="font-size:13px;color:#6B7280;margin-bottom:20px;">
                    Exporte toutes les absences avec les informations agent, service, date, type et statut de justification.
                </p>
                <a href="{{ route('rh.absences.export') }}" class="btn btn-danger btn-sm w-100">
                    <i class="fas fa-file-csv me-1"></i>Télécharger CSV Absences
                </a>
            </div>
        </div>

        {{-- Export Contrats --}}
        <div class="col-12 col-md-6">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:48px;height:48px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-file-contract" style="color:#0A4D8C;font-size:20px;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--theme-text);">Contrats expirants</div>
                        <div style="font-size:12px;color:#9CA3AF;">Alertes renouvellement</div>
                    </div>
                </div>
                <p style="font-size:13px;color:#6B7280;margin-bottom:20px;">
                    Liste des contrats qui arrivent à expiration dans les 60 prochains jours.
                </p>
                <a href="{{ route('rh.contrats.expiring') }}" class="btn btn-warning btn-sm w-100" style="color:#fff;">
                    <i class="fas fa-exclamation-triangle me-1"></i>Voir contrats expirants
                </a>
            </div>
        </div>

        {{-- Rapport mensuel --}}
        <div class="col-12 col-md-6">
            <div style="background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:24px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:48px;height:48px;background:#F5F3FF;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-calendar-alt" style="color:#7C3AED;font-size:20px;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--theme-text);">Rapport mensuel</div>
                        <div style="font-size:12px;color:#9CA3AF;">Vue consolidée</div>
                    </div>
                </div>
                <p style="font-size:13px;color:#6B7280;margin-bottom:20px;">
                    Rapport consolidé du mois en cours incluant absences, congés et mouvements.
                </p>
                <a href="{{ route('rh.rapports.mensuel') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-chart-bar me-1"></i>Rapport mensuel
                </a>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4" style="border-radius:10px;">
        <i class="fas fa-info-circle me-2"></i>
        <span style="font-size:13px;">
            L'export Excel avancé (avec formatage) sera disponible dans la prochaine version.
            Les exports CSV sont déjà disponibles directement depuis les listes (Absences).
        </span>
    </div>

</div>
@endsection
