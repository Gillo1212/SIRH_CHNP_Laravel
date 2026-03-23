@extends('layouts.master')

@section('title', 'Raccourcis clavier')
@section('page-title', 'Raccourcis clavier')

@section('breadcrumb')
    <li><a href="{{ route('aide.index') }}" style="color:#1565C0;">Aide</a></li>
    <li><span style="color:#6B7280;">Raccourcis clavier</span></li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card" style="border-radius:12px;border:1px solid #E5E7EB;overflow:hidden;">
    <div class="card-header d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#D97706,#F59E0B);padding:20px 24px;">
        <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-keyboard" style="color:white;font-size:18px;"></i>
        </div>
        <div>
            <h5 class="mb-0" style="color:white;font-weight:700;">Raccourcis clavier</h5>
            <div style="font-size:12px;color:rgba(255,255,255,0.85);">Naviguez plus vite dans le SIRH</div>
        </div>
    </div>
    <div class="card-body" style="padding:28px 32px;">

        <div class="alert alert-info d-flex gap-2 mb-4" style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;">
            <i class="fas fa-info-circle mt-1" style="color:#1565C0;flex-shrink:0;"></i>
            <div style="font-size:13px;color:#1E40AF;">
                Les raccourcis fonctionnent depuis n'importe quelle page de l'application.
                Sur Mac, remplacez <kbd>Ctrl</kbd> par <kbd>⌘ Cmd</kbd>.
            </div>
        </div>

        {{-- Navigation --}}
        <h6 style="color:#0A4D8C;font-weight:700;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #EFF6FF;">
            <i class="fas fa-compass me-2"></i>Navigation générale
        </h6>
        <table class="table table-sm mb-4" style="font-size:13.5px;">
            <tbody>
                @foreach([
                    ['keys' => ['Ctrl', 'K'], 'action' => 'Ouvrir la recherche rapide'],
                    ['keys' => ['Alt', '←'], 'action' => 'Page précédente (historique navigateur)'],
                    ['keys' => ['Alt', '→'], 'action' => 'Page suivante (historique navigateur)'],
                    ['keys' => ['F5'], 'action' => 'Recharger la page'],
                ] as $sc)
                <tr>
                    <td style="width:200px;padding:10px 0;">
                        @foreach($sc['keys'] as $k)
                            <kbd style="background:#F3F4F6;border:1px solid #D1D5DB;border-radius:4px;padding:2px 8px;font-size:12px;color:#374151;margin-right:4px;">{{ $k }}</kbd>
                        @endforeach
                    </td>
                    <td style="color:#374151;padding:10px 0;">{{ $sc['action'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Formulaires --}}
        <h6 style="color:#059669;font-weight:700;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #ECFDF5;">
            <i class="fas fa-edit me-2"></i>Formulaires
        </h6>
        <table class="table table-sm mb-4" style="font-size:13.5px;">
            <tbody>
                @foreach([
                    ['keys' => ['Tab'], 'action' => 'Passer au champ suivant'],
                    ['keys' => ['Shift', 'Tab'], 'action' => 'Revenir au champ précédent'],
                    ['keys' => ['Enter'], 'action' => 'Soumettre le formulaire (si dans un champ)'],
                    ['keys' => ['Esc'], 'action' => 'Fermer une modal ou un menu déroulant'],
                ] as $sc)
                <tr>
                    <td style="width:200px;padding:10px 0;">
                        @foreach($sc['keys'] as $k)
                            <kbd style="background:#F3F4F6;border:1px solid #D1D5DB;border-radius:4px;padding:2px 8px;font-size:12px;color:#374151;margin-right:4px;">{{ $k }}</kbd>
                        @endforeach
                    </td>
                    <td style="color:#374151;padding:10px 0;">{{ $sc['action'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tableaux --}}
        <h6 style="color:#D97706;font-weight:700;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #FFFBEB;">
            <i class="fas fa-table me-2"></i>Tableaux et listes
        </h6>
        <table class="table table-sm mb-0" style="font-size:13.5px;">
            <tbody>
                @foreach([
                    ['keys' => ['↑', '↓'], 'action' => 'Naviguer entre les lignes (si activé)'],
                    ['keys' => ['Ctrl', 'A'], 'action' => 'Sélectionner tout le texte dans un champ'],
                    ['keys' => ['Ctrl', 'C'], 'action' => 'Copier la sélection'],
                    ['keys' => ['Ctrl', 'P'], 'action' => 'Imprimer la page'],
                ] as $sc)
                <tr>
                    <td style="width:200px;padding:10px 0;">
                        @foreach($sc['keys'] as $k)
                            <kbd style="background:#F3F4F6;border:1px solid #D1D5DB;border-radius:4px;padding:2px 8px;font-size:12px;color:#374151;margin-right:4px;">{{ $k }}</kbd>
                        @endforeach
                    </td>
                    <td style="color:#374151;padding:10px 0;">{{ $sc['action'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

</div>
</div>
@endsection
