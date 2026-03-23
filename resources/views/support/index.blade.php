@extends('layouts.master')

@section('title', 'Support technique')
@section('page-title', 'Support technique')

@section('breadcrumb')
    <li><span style="color:#6B7280;">Support</span></li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Cartes de contact --}}
    <div class="col-12">
        <div class="row g-3">
            @foreach([
                ['icon' => 'envelope', 'color' => '#1565C0', 'bg' => '#EFF6FF', 'title' => 'Email support', 'value' => 'support@chnp.sn', 'sub' => 'Réponse sous 24-48h ouvrées'],
                ['icon' => 'clock', 'color' => '#059669', 'bg' => '#ECFDF5', 'title' => 'Horaires', 'value' => 'Lun–Ven : 8h–17h', 'sub' => 'Hors heures : laisser un ticket'],
                ['icon' => 'map-marker-alt', 'color' => '#D97706', 'bg' => '#FFFBEB', 'title' => 'Localisation', 'value' => 'Service Informatique', 'sub' => 'Bâtiment administratif — CHNP'],
            ] as $card)
            <div class="col-md-4">
                <div style="padding:16px 20px;border-radius:10px;background:{{ $card['bg'] }};border:1px solid {{ $card['color'] }}22;display:flex;align-items:center;gap:14px;">
                    <div style="width:42px;height:42px;background:white;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                        <i class="fas fa-{{ $card['icon'] }}" style="color:{{ $card['color'] }};font-size:16px;"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;">{{ $card['title'] }}</div>
                        <div style="font-size:14px;font-weight:600;color:#111827;margin:2px 0;">{{ $card['value'] }}</div>
                        <div style="font-size:11.5px;color:#6B7280;">{{ $card['sub'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bouton nouveau ticket + liste --}}
    <div class="col-12">
        <div class="card" style="border-radius:12px;border:1px solid #E5E7EB;">
            <div class="card-header d-flex align-items-center justify-content-between" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;padding:14px 20px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-ticket-alt" style="color:#0A4D8C;"></i>
                    <span style="font-weight:600;font-size:14px;color:#111827;">Mes tickets de support</span>
                    <span class="badge" style="background:#EFF6FF;color:#0A4D8C;font-size:11px;">{{ $tickets->total() }}</span>
                </div>
                <a href="{{ route('support.create') }}" class="btn btn-primary btn-sm" style="font-size:13px;padding:7px 14px;">
                    <i class="fas fa-plus me-1"></i>Nouveau ticket
                </a>
            </div>

            @if($tickets->isEmpty())
            <div class="card-body" style="padding:48px;text-align:center;">
                <div style="width:64px;height:64px;background:#F3F4F6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-headset" style="font-size:24px;color:#D1D5DB;"></i>
                </div>
                <p style="font-size:14px;font-weight:500;color:#6B7280;margin-bottom:4px;">Aucun ticket ouvert</p>
                <p style="font-size:13px;color:#9CA3AF;margin-bottom:20px;">Vous n'avez pas encore soumis de ticket de support.</p>
                <a href="{{ route('support.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Créer mon premier ticket
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13.5px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sujet</th>
                            <th>Catégorie</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td style="color:#9CA3AF;font-size:12px;">#{{ $ticket->id }}</td>
                            <td style="font-weight:500;color:#111827;">{{ Str::limit($ticket->sujet, 45) }}</td>
                            <td>
                                @php
                                    $cats = ['bug' => ['Bug', '#FEE2E2', '#DC2626'], 'question' => ['Question', '#EFF6FF', '#1565C0'], 'amelioration' => ['Amélioration', '#ECFDF5', '#059669'], 'autre' => ['Autre', '#F3F4F6', '#6B7280']];
                                    $cat = $cats[$ticket->categorie] ?? ['?', '#F3F4F6', '#6B7280'];
                                @endphp
                                <span style="background:{{ $cat[1] }};color:{{ $cat[2] }};font-size:11px;font-weight:600;padding:2px 8px;border-radius:6px;">{{ $cat[0] }}</span>
                            </td>
                            <td>
                                @php
                                    $prios = ['basse' => ['Basse', '#ECFDF5', '#059669'], 'normale' => ['Normale', '#EFF6FF', '#1565C0'], 'haute' => ['Haute', '#FEF3C7', '#D97706'], 'urgente' => ['Urgente', '#FEE2E2', '#DC2626']];
                                    $prio = $prios[$ticket->priorite] ?? ['?', '#F3F4F6', '#6B7280'];
                                @endphp
                                <span style="background:{{ $prio[1] }};color:{{ $prio[2] }};font-size:11px;font-weight:600;padding:2px 8px;border-radius:6px;">{{ $prio[0] }}</span>
                            </td>
                            <td>
                                @php
                                    $statuts = ['ouvert' => ['Ouvert', '#EFF6FF', '#1565C0'], 'en_cours' => ['En cours', '#FEF3C7', '#D97706'], 'resolu' => ['Résolu', '#ECFDF5', '#059669'], 'ferme' => ['Fermé', '#F3F4F6', '#6B7280']];
                                    $stat = $statuts[$ticket->statut] ?? ['?', '#F3F4F6', '#6B7280'];
                                @endphp
                                <span style="background:{{ $stat[1] }};color:{{ $stat[2] }};font-size:11px;font-weight:600;padding:2px 8px;border-radius:6px;">{{ $stat[0] }}</span>
                            </td>
                            <td style="color:#6B7280;font-size:12px;">{{ $ticket->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('support.show', $ticket) }}" style="color:#1565C0;text-decoration:none;font-size:12px;">
                                    Voir <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
            <div class="card-footer" style="background:#F9FAFB;border-top:1px solid #E5E7EB;padding:12px 20px;">
                {{ $tickets->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

</div>
@endsection
