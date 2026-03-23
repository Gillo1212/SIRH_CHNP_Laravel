@extends('layouts.master')

@section('title', 'Bilan Social — DRH')
@section('page-title', 'Bilan Social')

@section('breadcrumb')
    <li><a href="{{ route('drh.dashboard') }}" style="color:#1565C0;">Tableau de bord DRH</a></li>
    <li>Bilan Social</li>
@endsection

@push('styles')
<style>
.bilan-section {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
}
.bilan-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #0A4D8C;
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid #EFF6FF;
    display: flex;
    align-items: center;
    gap: 8px;
}
.bilan-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #F9FAFB;
    font-size: 13.5px;
}
.bilan-row:last-child { border-bottom: none; }
.bilan-value {
    font-weight: 700;
    color: #111827;
    font-size: 15px;
}
.print-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: all 180ms;
    text-decoration: none;
    border: none;
}
@media print {
    .no-print { display: none !important; }
    .bilan-section { break-inside: avoid; }
}
</style>
@endpush

@section('content')

@php
    $annee = now()->year;
    try { $effectifTotal   = \App\Models\Agent::where('statut','actif')->count(); }     catch(\Exception $e) { $effectifTotal = 0; }
    try { $effectifFemmes  = \App\Models\Agent::where('statut','actif')->where('sexe','F')->count(); } catch(\Exception $e) { $effectifFemmes = 0; }
    try { $effectifHommes  = \App\Models\Agent::where('statut','actif')->where('sexe','M')->count(); } catch(\Exception $e) { $effectifHommes = 0; }
    try { $effectifEnConge = \App\Models\Agent::where('statut','en_conge')->count(); }   catch(\Exception $e) { $effectifEnConge = 0; }
    try { $effectifSuspendu= \App\Models\Agent::where('statut','suspendu')->count(); }  catch(\Exception $e) { $effectifSuspendu = 0; }
    try { $nServices       = \App\Models\Service::count(); }                             catch(\Exception $e) { $nServices = 0; }
    try { $nDivisions      = \App\Models\Division::count(); }                            catch(\Exception $e) { $nDivisions = 0; }
    try {
        $absencesAn = \App\Models\Absence::whereYear('date_absence', $annee)->count();
    } catch(\Exception $e) { $absencesAn = 0; }
    try {
        $contratsActifs  = \App\Models\Contrat::where('statut_contrat','Actif')->count();
        $contratsExpires = \App\Models\Contrat::where('statut_contrat','Expiré')->count();
    } catch(\Exception $e) { $contratsActifs = 0; $contratsExpires = 0; }
    try {
        $mouvementsAn = \App\Models\Mouvement::whereYear('created_at', $annee)->count();
    } catch(\Exception $e) { $mouvementsAn = 0; }
    $tauxFeminisation = $effectifTotal > 0 ? round(($effectifFemmes/$effectifTotal)*100,1) : 0;
    $tauxAbsenteisme  = $effectifTotal > 0 ? round(($absencesAn/max($effectifTotal,1))*100,1) : 0;
@endphp

{{-- En-tête --}}
<div class="d-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">Bilan Social {{ $annee }}</h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            Centre Hospitalier National de Pikine — Direction des Ressources Humaines
        </p>
    </div>
    <div class="d-flex gap-2">
        <button class="print-btn" style="background:#F3F4F6;color:#374151;border:1px solid #E5E7EB;" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="{{ route('drh.dashboard') }}" class="print-btn" style="background:#F3F4F6;color:#374151;border:1px solid #E5E7EB;">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

{{-- Titre officiel (visible à l'impression) --}}
<div style="text-align:center;margin-bottom:28px;display:none;" class="d-print-block">
    <div style="font-size:18px;font-weight:700;color:#0A4D8C;">CENTRE HOSPITALIER NATIONAL DE PIKINE</div>
    <div style="font-size:14px;font-weight:600;color:#374151;">Direction des Ressources Humaines</div>
    <div style="font-size:20px;font-weight:700;color:#111827;margin-top:10px;">BILAN SOCIAL {{ $annee }}</div>
    <div style="font-size:12px;color:#6B7280;margin-top:4px;">Généré le {{ now()->isoFormat('D MMMM YYYY') }}</div>
</div>

{{-- 1. Effectifs --}}
<div class="bilan-section">
    <div class="bilan-section-title">
        <i class="fas fa-users"></i> 1. Données sur les Effectifs
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Effectif total actif</span>
        <span class="bilan-value">{{ $effectifTotal }} agents</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Effectifs féminins</span>
        <span class="bilan-value">{{ $effectifFemmes }} ({{ $tauxFeminisation }}%)</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Effectifs masculins</span>
        <span class="bilan-value">{{ $effectifHommes }} ({{ 100 - $tauxFeminisation }}%)</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Agents en congé</span>
        <span class="bilan-value">{{ $effectifEnConge }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Agents suspendus</span>
        <span class="bilan-value">{{ $effectifSuspendu }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Taux de féminisation</span>
        <span class="bilan-value" style="color:#BE185D;">{{ $tauxFeminisation }}%</span>
    </div>
</div>

{{-- 2. Structure organisationnelle --}}
<div class="bilan-section">
    <div class="bilan-section-title">
        <i class="fas fa-sitemap"></i> 2. Structure Organisationnelle
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Nombre de services</span>
        <span class="bilan-value">{{ $nServices }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Nombre de divisions</span>
        <span class="bilan-value">{{ $nDivisions }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Ratio agents / service</span>
        <span class="bilan-value">{{ $nServices > 0 ? round($effectifTotal/$nServices,1) : '—' }}</span>
    </div>
</div>

{{-- 3. Contrats --}}
<div class="bilan-section">
    <div class="bilan-section-title">
        <i class="fas fa-file-contract"></i> 3. Gestion des Contrats
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Contrats actifs</span>
        <span class="bilan-value" style="color:#059669;">{{ $contratsActifs }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Contrats expirés</span>
        <span class="bilan-value" style="color:#DC2626;">{{ $contratsExpires }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Total contrats référencés</span>
        <span class="bilan-value">{{ $contratsActifs + $contratsExpires }}</span>
    </div>
</div>

{{-- 4. Absentéisme --}}
<div class="bilan-section">
    <div class="bilan-section-title">
        <i class="fas fa-user-clock"></i> 4. Absentéisme
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Total absences enregistrées ({{ $annee }})</span>
        <span class="bilan-value">{{ $absencesAn }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Taux d'absentéisme annuel</span>
        <span class="bilan-value" style="color:#D97706;">{{ $tauxAbsenteisme }}%</span>
    </div>
</div>

{{-- 5. Mouvements --}}
<div class="bilan-section">
    <div class="bilan-section-title">
        <i class="fas fa-exchange-alt"></i> 5. Mouvements du Personnel
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Total mouvements enregistrés ({{ $annee }})</span>
        <span class="bilan-value">{{ $mouvementsAn }}</span>
    </div>
    <div class="bilan-row">
        <span style="color:#374151;">Masse salariale annuelle</span>
        <span class="bilan-value" style="color:#9CA3AF;">— XOF (module paie requis)</span>
    </div>
</div>

{{-- Signature DRH --}}
<div class="bilan-section" style="background:#F9FAFB;">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div style="font-size:13px;color:#6B7280;">Document généré le</div>
            <div style="font-weight:700;color:#111827;">{{ now()->isoFormat('D MMMM YYYY à HH:mm') }}</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:12px;color:#6B7280;margin-bottom:8px;">Visa du Directeur des Ressources Humaines</div>
            <div style="width:180px;height:60px;border-bottom:2px solid #9CA3AF;"></div>
            <div style="font-size:12px;color:#374151;margin-top:6px;">{{ Auth::user()->agent->prenom ?? '' }} {{ Auth::user()->agent->nom ?? 'DRH' }}</div>
        </div>
    </div>
</div>

@endsection
