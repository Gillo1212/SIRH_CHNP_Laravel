@extends('layouts.master')

@section('title', 'Politique de confidentialité')
@section('page-title', 'Politique de confidentialité')

@section('breadcrumb')
    <li><a href="{{ route('aide.index') }}" style="color:#1565C0;">Aide</a></li>
    <li><span style="color:#6B7280;">Politique de confidentialité</span></li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="card" style="border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;">
    <div class="card-header d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#0A4D8C,#1565C0);padding:20px 24px;">
        <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-shield-alt" style="color:white;font-size:18px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="color:white;font-weight:700;">Politique de Confidentialité</h5>
            <div style="font-size:12px;color:rgba(255,255,255,0.75);">SIRH CHNP — Centre Hospitalier National de Pikine</div>
        </div>
    </div>
    <div class="card-body" style="padding:28px 32px;">

        <div class="alert alert-info d-flex gap-2 mb-4" style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;">
            <i class="fas fa-info-circle mt-1" style="color:#1565C0;flex-shrink:0;"></i>
            <div style="font-size:13px;color:#1E40AF;">
                Dernière mise à jour : <strong>{{ now()->format('d/m/Y') }}</strong> —
                Ce document décrit comment le CHNP collecte, utilise et protège vos données personnelles.
            </div>
        </div>

        {{-- TRIADE CID --}}
        <div class="row g-3 mb-4">
            @foreach([
                ['icon' => 'lock', 'color' => '#0A4D8C', 'bg' => '#EFF6FF', 'label' => 'Confidentialité', 'desc' => 'Chiffrement AES-256 des données sensibles. Accès contrôlé par rôle (RBAC).'],
                ['icon' => 'check-double', 'color' => '#059669', 'bg' => '#ECFDF5', 'label' => 'Intégrité', 'desc' => 'Journal d\'audit immuable. Validation stricte de toutes les entrées.'],
                ['icon' => 'server', 'color' => '#D97706', 'bg' => '#FFFBEB', 'label' => 'Disponibilité', 'desc' => 'Sauvegardes automatiques quotidiennes. Haute disponibilité du système.'],
            ] as $pilier)
            <div class="col-md-4">
                <div style="padding:16px;border-radius:10px;background:{{ $pilier['bg'] }};height:100%;">
                    <div style="font-weight:700;color:{{ $pilier['color'] }};font-size:13.5px;margin-bottom:6px;">
                        <i class="fas fa-{{ $pilier['icon'] }} me-2"></i>{{ $pilier['label'] }}
                    </div>
                    <div style="font-size:12.5px;color:#6B7280;line-height:1.5;">{{ $pilier['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <h6 style="color:#0A4D8C;font-weight:700;margin-top:28px;padding-bottom:8px;border-bottom:2px solid #EFF6FF;">
            1. Données collectées
        </h6>
        <p style="font-size:13.5px;color:#374151;line-height:1.7;">Dans le cadre de la gestion RH du CHNP, nous collectons les données suivantes :</p>
        <ul style="font-size:13.5px;color:#374151;line-height:1.9;">
            <li><strong>Identification</strong> : nom, prénom, date et lieu de naissance, matricule, photo</li>
            <li><strong>Coordonnées</strong> <span class="badge" style="background:#FEF3C7;color:#92400E;font-size:10px;">Chiffrées AES-256</span> : adresse, téléphone, email</li>
            <li><strong>Professionnelles</strong> : fonction, service, grade, catégorie, ancienneté, contrats</li>
            <li><strong>Sociales</strong> : situation familiale, enfants, conjoints (pour prises en charge médicales)</li>
            <li><strong>N° d'assurance</strong> <span class="badge" style="background:#FEF3C7;color:#92400E;font-size:10px;">Chiffré AES-256</span></li>
            <li><strong>Présence</strong> : congés, absences, plannings, heures supplémentaires</li>
        </ul>

        <h6 style="color:#0A4D8C;font-weight:700;margin-top:24px;padding-bottom:8px;border-bottom:2px solid #EFF6FF;">
            2. Contrôle d'accès — RBAC à 5 niveaux
        </h6>
        <div class="table-responsive">
            <table class="table table-sm" style="font-size:13px;">
                <thead style="background:#F9FAFB;">
                    <tr>
                        <th style="font-weight:600;color:#6B7280;">Rôle</th>
                        <th style="font-weight:600;color:#6B7280;">Accès aux données</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><span class="badge bg-secondary">Agent</span></td><td>Ses propres données uniquement</td></tr>
                    <tr><td><span class="badge bg-info">Manager</span></td><td>Données de son équipe (lecture seule)</td></tr>
                    <tr><td><span class="badge bg-primary">Agent RH</span></td><td>Données nécessaires à la gestion RH</td></tr>
                    <tr><td><span class="badge bg-warning text-dark">DRH</span></td><td>Accès complet pour pilotage stratégique</td></tr>
                    <tr><td><span class="badge bg-danger">Admin Système</span></td><td>Gestion technique et sécurité du système</td></tr>
                </tbody>
            </table>
        </div>

        <h6 style="color:#0A4D8C;font-weight:700;margin-top:24px;padding-bottom:8px;border-bottom:2px solid #EFF6FF;">
            3. Vos droits
        </h6>
        <ul style="font-size:13.5px;color:#374151;line-height:1.9;">
            <li>Droit d'<strong>accès</strong> à vos données personnelles</li>
            <li>Droit de <strong>rectification</strong> des données inexactes</li>
            <li>Droit à l'<strong>effacement</strong> dans les limites légales et réglementaires</li>
            <li>Droit à la <strong>portabilité</strong> de vos données</li>
            <li>Droit d'<strong>opposition</strong> au traitement dans les cas prévus par la loi</li>
        </ul>

        <h6 style="color:#0A4D8C;font-weight:700;margin-top:24px;padding-bottom:8px;border-bottom:2px solid #EFF6FF;">
            4. Conservation & Sécurité
        </h6>
        <p style="font-size:13.5px;color:#374151;line-height:1.7;">
            Les données sont conservées pendant la durée de votre contrat et archivées conformément aux obligations légales
            (5 ans minimum après le départ). Des sauvegardes chiffrées sont effectuées quotidiennement.
            Chaque accès aux données est tracé dans un journal d'audit immuable.
        </p>

        <h6 style="color:#0A4D8C;font-weight:700;margin-top:24px;padding-bottom:8px;border-bottom:2px solid #EFF6FF;">
            5. Contact
        </h6>
        <div style="padding:16px;background:#F9FAFB;border-radius:8px;font-size:13.5px;">
            <p class="mb-1">Pour exercer vos droits ou toute question relative à vos données :</p>
            <p class="mb-1"><i class="fas fa-envelope me-2 text-primary"></i><a href="mailto:rh@chnp.sn">rh@chnp.sn</a></p>
            <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Service RH — Centre Hospitalier National de Pikine, Sénégal</p>
        </div>

    </div>
</div>

</div>
</div>
@endsection
