@extends('layouts.master')

@section('title', 'Compléter le dossier RH')
@section('page-title', 'Compléter le dossier RH')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.agents.index') }}" style="color:#1565C0;">Personnel</a></li>
    <li><a href="{{ route('rh.agents.comptes-a-completer') }}" style="color:#1565C0;">Comptes à compléter</a></li>
    <li>Compléter dossier</li>
@endsection

@section('content')
{{--
    Cette page est conservée comme fallback.
    Le formulaire de complétion est désormais un modal dans comptes-a-completer.
    Cette vue ne devrait plus être rendue directement — le contrôleur redirige.
--}}
<div style="max-width:520px;margin:80px auto;text-align:center;">
    <div style="width:72px;height:72px;border-radius:50%;background:#EFF6FF;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px;">
        <i class="fas fa-folder-open" style="color:#1565C0;"></i>
    </div>
    <h5 style="font-weight:700;margin-bottom:8px;">Formulaire déplacé</h5>
    <p style="font-size:13px;color:var(--theme-text-muted);margin-bottom:24px;">
        Le formulaire de complétion de dossier RH est maintenant accessible
        directement depuis la liste des comptes à compléter.
    </p>
    <a href="{{ route('rh.agents.comptes-a-completer') }}"
       class="btn btn-primary btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>
@endsection
