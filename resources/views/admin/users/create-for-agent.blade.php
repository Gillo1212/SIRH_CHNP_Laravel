@extends('layouts.master')

@section('title', 'Créer un compte — ' . $agent->prenom . ' ' . $agent->nom)
@section('page-title', 'Création compte utilisateur')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Admin</a></li>
    <li><a href="{{ route('admin.users.agents-sans-compte') }}" style="color:#1565C0;">Comptes en attente</a></li>
    <li>Créer compte</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Résumé agent --}}
        <div class="panel mb-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#0A4D8C,#1565C0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($agent->prenom,0,1).substr($agent->nom,0,1)) }}
                </div>
                <div>
                    <div style="font-size:16px;font-weight:700;">{{ $agent->prenom }} {{ $agent->nom }}</div>
                    <div style="font-size:13px;" class="text-muted">
                        <code>{{ $agent->matricule }}</code>
                        &nbsp;·&nbsp;{{ $agent->fonction ?? '—' }}
                        &nbsp;·&nbsp;{{ $agent->service?->nom_service ?? '—' }}
                    </div>
                </div>
                <div class="ms-auto">
                    <span class="badge" style="background:#FEF3C7;color:#92400E;font-size:12px;">
                        <i class="fas fa-clock me-1"></i>Compte en attente
                    </span>
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="panel">
            <h6 class="fw-bold mb-4" style="font-size:14px;">
                <i class="fas fa-user-plus me-2" style="color:#0A4D8C;"></i>
                Créer le compte utilisateur
            </h6>

            @if($errors->any())
            <div class="alert alert-danger" style="font-size:13px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.users.store-for-agent', $agent->id_agent) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">
                        Identifiant de connexion (login) <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                           value="{{ old('login', Str::ascii(strtolower($agent->prenom)) . '.' . Str::ascii(strtolower($agent->nom))) }}"
                           placeholder="ex: prenom.nom"
                           pattern="[a-z0-9._-]+"
                           required>
                    <div class="form-text" style="font-size:11px;">
                        Lettres minuscules, chiffres, points et tirets uniquement. Un mot de passe temporaire sera généré automatiquement.
                    </div>
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-size:12px;font-weight:600;">
                        Rôle initial <span class="text-danger">*</span>
                    </label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="Agent" selected>Agent</option>
                        <option value="Manager">Manager</option>
                        <option value="AgentRH">Agent RH</option>
                        <option value="DRH">DRH</option>
                        <option value="AdminSystème">Admin Système</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($agent->email)
                <div class="alert alert-info" style="font-size:12px;">
                    <i class="fas fa-envelope me-2"></i>
                    Les identifiants seront envoyés par email à : <strong>{{ $agent->email }}</strong>
                </div>
                @else
                <div class="alert alert-warning" style="font-size:12px;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Aucune adresse email renseignée. Notez le mot de passe temporaire affiché après création pour le communiquer manuellement.
                </div>
                @endif

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.users.agents-sans-compte') }}" class="btn btn-outline-secondary" style="font-size:13px;">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary" style="font-size:13px;">
                        <i class="fas fa-user-plus me-1"></i>Créer le compte
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
