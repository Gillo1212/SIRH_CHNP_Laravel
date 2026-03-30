@extends('layouts.master')

@section('title', 'Détail absence — Major')
@section('page-title', 'Détail de l\'absence')

@section('breadcrumb')
    <li><a href="{{ route('major.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
    <li><a href="{{ route('major.absences.index') }}" style="color:#1565C0;">Absences</a></li>
    <li>Détail</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
<div class="row justify-content-center">
<div class="col-lg-6">

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-user-minus me-2 text-danger"></i>Absence</h6>
        <a href="{{ route('major.absences.index') }}" class="btn btn-sm btn-outline-secondary">Retour</a>
    </div>
    <div class="card-body px-4 pb-4">
        <table class="table table-borderless" style="font-size:14px;">
            <tr>
                <th class="text-muted" style="width:40%;font-weight:600;">Agent</th>
                <td>{{ $agent->nom_complet }}</td>
            </tr>
            <tr>
                <th class="text-muted" style="font-weight:600;">Matricule</th>
                <td><code>{{ $agent->matricule }}</code></td>
            </tr>
            <tr>
                <th class="text-muted" style="font-weight:600;">Date</th>
                <td>{{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th class="text-muted" style="font-weight:600;">Type</th>
                <td><span class="badge bg-danger">{{ $absence->type_absence }}</span></td>
            </tr>
            <tr>
                <th class="text-muted" style="font-weight:600;">Justifiée</th>
                <td>
                    @if($absence->justifie)
                        <span class="badge bg-success">Oui</span>
                    @else
                        <span class="badge bg-secondary">Non</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="text-muted" style="font-weight:600;">Enregistrée le</th>
                <td>{{ $absence->created_at->format('d/m/Y à H:i') }}</td>
            </tr>
        </table>
    </div>
</div>

</div>
</div>
</div>
@endsection
