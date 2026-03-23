{{-- Vue générique "En construction" - utilisée par les pages stub --}}
@extends('layouts.master')

@section('title', $pageTitle ?? 'En construction')
@section('page-title', $pageTitle ?? 'En construction')

@section('breadcrumb')
    <li style="color: #6B7280;">{{ $pageTitle ?? 'En construction' }}</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
    <div class="text-center" style="max-width: 500px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #EFF6FF, #DBEAFE); border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
            <i class="fas fa-tools" style="font-size: 32px; color: #0A4D8C;"></i>
        </div>
        <h2 style="font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 12px;">
            {{ $pageTitle ?? 'Page en construction' }}
        </h2>
        <p style="color: #6B7280; font-size: 14px; line-height: 1.6; margin-bottom: 28px;">
            Cette fonctionnalité est en cours de développement.<br>
            Elle sera disponible prochainement dans le cadre du <strong>MVP SIRH CHNP</strong>.
        </p>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm" style="background: #0A4D8C; border-color: #0A4D8C;">
                <i class="fas fa-home me-1"></i> Tableau de bord
            </a>
        </div>
        <div style="margin-top: 32px; padding: 12px 16px; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; display: inline-block;">
            <span style="font-size: 12px; color: #059669; font-weight: 600;">
                <i class="fas fa-shield-alt me-1"></i> SIRH CHNP — Système RH Sécurisé
            </span>
        </div>
    </div>
</div>
@endsection
