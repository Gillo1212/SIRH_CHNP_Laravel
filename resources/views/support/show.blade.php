@extends('layouts.master')

@section('title', 'Ticket #' . $ticket->id)
@section('page-title', 'Ticket #' . $ticket->id)

@section('breadcrumb')
    <li><a href="{{ route('support.index') }}" style="color:#1565C0;">Support</a></li>
    <li><span style="color:#6B7280;">Ticket #{{ $ticket->id }}</span></li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

@php
    $statuts = ['ouvert' => ['Ouvert', '#EFF6FF', '#1565C0', 'info-circle'], 'en_cours' => ['En cours', '#FEF3C7', '#D97706', 'spinner'], 'resolu' => ['Résolu', '#ECFDF5', '#059669', 'check-circle'], 'ferme' => ['Fermé', '#F3F4F6', '#6B7280', 'times-circle']];
    $stat = $statuts[$ticket->statut] ?? ['?', '#F3F4F6', '#6B7280', 'circle'];
    $prios = ['basse' => ['Basse', '#ECFDF5', '#059669'], 'normale' => ['Normale', '#EFF6FF', '#1565C0'], 'haute' => ['Haute', '#FEF3C7', '#D97706'], 'urgente' => ['Urgente', '#FEE2E2', '#DC2626']];
    $prio = $prios[$ticket->priorite] ?? ['?', '#F3F4F6', '#6B7280'];
    $cats = ['bug' => 'Bug', 'question' => 'Question', 'amelioration' => 'Amélioration', 'autre' => 'Autre'];
@endphp

<div class="card" style="border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;margin-bottom:16px;">
    {{-- En-tête du ticket --}}
    <div class="card-header" style="background:linear-gradient(135deg,#0A4D8C,#1565C0);padding:20px 24px;">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <div style="font-size:11px;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Ticket #{{ $ticket->id }}</div>
                <h5 class="mb-0" style="color:white;font-weight:700;font-size:16px;">{{ $ticket->sujet }}</h5>
            </div>
            <span style="background:{{ $stat[1] }};color:{{ $stat[2] }};font-size:11.5px;font-weight:600;padding:4px 12px;border-radius:20px;white-space:nowrap;flex-shrink:0;">
                <i class="fas fa-{{ $stat[3] }} me-1"></i>{{ $stat[0] }}
            </span>
        </div>
    </div>

    {{-- Métadonnées --}}
    <div style="background:#F9FAFB;padding:14px 24px;border-bottom:1px solid #E5E7EB;display:flex;gap:24px;flex-wrap:wrap;">
        <div>
            <div style="font-size:10.5px;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Catégorie</div>
            <div style="font-size:13px;font-weight:500;color:#374151;">{{ $cats[$ticket->categorie] ?? $ticket->categorie }}</div>
        </div>
        <div>
            <div style="font-size:10.5px;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Priorité</div>
            <span style="background:{{ $prio[1] }};color:{{ $prio[2] }};font-size:11.5px;font-weight:600;padding:2px 10px;border-radius:6px;">{{ $prio[0] }}</span>
        </div>
        <div>
            <div style="font-size:10.5px;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Soumis le</div>
            <div style="font-size:13px;color:#374151;">{{ $ticket->created_at->format('d/m/Y à H:i') }}</div>
        </div>
        @if($ticket->date_resolution)
        <div>
            <div style="font-size:10.5px;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Résolu le</div>
            <div style="font-size:13px;color:#374151;">{{ $ticket->date_resolution->format('d/m/Y à H:i') }}</div>
        </div>
        @endif
    </div>

    {{-- Corps --}}
    <div class="card-body" style="padding:24px;">

        {{-- Description --}}
        <div style="margin-bottom:24px;">
            <div style="font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">
                <i class="fas fa-align-left me-1"></i>Description
            </div>
            <div style="font-size:13.5px;color:#374151;line-height:1.7;white-space:pre-wrap;background:#F9FAFB;border-radius:8px;padding:16px;border:1px solid #F3F4F6;">{{ $ticket->description }}</div>
        </div>

        {{-- Capture d'écran --}}
        @if($ticket->capture_ecran)
        <div style="margin-bottom:24px;">
            <div style="font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">
                <i class="fas fa-image me-1"></i>Capture d'écran
            </div>
            <a href="{{ asset('storage/' . $ticket->capture_ecran) }}" target="_blank">
                <img src="{{ asset('storage/' . $ticket->capture_ecran) }}"
                     alt="Capture d'écran"
                     style="max-width:100%;max-height:400px;border-radius:8px;border:1px solid #E5E7EB;">
            </a>
        </div>
        @endif

        {{-- Réponse du support --}}
        @if($ticket->reponse)
        <div style="border-top:1px solid #E5E7EB;padding-top:24px;">
            <div style="font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">
                <i class="fas fa-reply me-1"></i>Réponse du support
            </div>
            <div style="font-size:13.5px;color:#374151;line-height:1.7;white-space:pre-wrap;background:#ECFDF5;border-radius:8px;padding:16px;border:1px solid #A7F3D0;">{{ $ticket->reponse }}</div>
            @if($ticket->traitePar)
            <div style="font-size:12px;color:#9CA3AF;margin-top:8px;">
                Répondu par <strong>{{ $ticket->traitePar->login }}</strong>
                @if($ticket->date_resolution)
                    le {{ $ticket->date_resolution->format('d/m/Y à H:i') }}
                @endif
            </div>
            @endif
        </div>
        @elseif($ticket->statut === 'ouvert' || $ticket->statut === 'en_cours')
        <div style="border-top:1px solid #E5E7EB;padding-top:20px;">
            <div class="d-flex gap-2 align-items-center" style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:8px;padding:12px 16px;">
                <i class="fas fa-clock" style="color:#D97706;"></i>
                <div style="font-size:13px;color:#92400E;">
                    Votre ticket est en attente de traitement. Vous serez notifié par email dès qu'une réponse est disponible.
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<a href="{{ route('support.index') }}" style="font-size:13.5px;color:#1565C0;text-decoration:none;">
    <i class="fas fa-arrow-left me-1"></i>Retour à mes tickets
</a>

</div>
</div>
@endsection
