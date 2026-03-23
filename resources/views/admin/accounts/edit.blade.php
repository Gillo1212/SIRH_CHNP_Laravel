@extends('layouts.master')

@section('title', 'Modifier le Compte — ' . $user->login)
@section('page-title', 'Modifier le Compte')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.accounts.index') }}" style="color:#1565C0;">Comptes</a></li>
    <li><a href="{{ route('admin.accounts.show', $user->id) }}" style="color:#1565C0;">{{ $user->login }}</a></li>
    <li>Modifier</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">

            <div class="alert border-0 mb-4"
                 style="background:#FFF7ED;border-left:4px solid #F59E0B !important;border-radius:8px;">
                <div class="d-flex gap-3">
                    <i class="fas fa-exclamation-triangle mt-1" style="color:#F59E0B;"></i>
                    <div style="font-size:13px;color:#92400E;">
                        <strong>Règle de séparation des responsabilités :</strong>
                        Seuls les identifiants système (login, email, rôles) sont modifiables ici.
                        Les données RH sont gérées exclusivement par le module RH.
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header border-0 bg-white" style="padding:24px 28px 0;">
                    <h5 class="fw-700 mb-0" style="color:#111827;">
                        <i class="fas fa-edit me-2" style="color:#0A4D8C;"></i>Modifier — {{ $user->login }}
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:13px;">
                        Compte créé le {{ $user->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>

                <div class="card-body" style="padding:24px 28px;">
                    <form action="{{ route('admin.accounts.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger border-0 mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $err)
                                        <li style="font-size:13px;">{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Login --}}
                        <div class="mb-4">
                            <label for="login" class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Login <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="login"
                                   name="login"
                                   class="form-control @error('login') is-invalid @enderror"
                                   value="{{ old('login', $user->login) }}"
                                   required>
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rôles --}}
                        <div class="mb-4">
                            <label class="form-label fw-600" style="font-size:13px;color:#374151;">
                                Rôle(s) <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                @foreach($roles as $role)
                                    @php
                                        $checked = $user->hasRole($role->name);
                                        $colors = [
                                            'AdminSystème' => ['bg' => '#FEF2F2', 'border' => '#FCA5A5', 'color' => '#7F1D1D', 'icon' => 'fa-shield-alt'],
                                            'DRH'          => ['bg' => '#F5F3FF', 'border' => '#A78BFA', 'color' => '#4C1D95', 'icon' => 'fa-user-tie'],
                                            'AgentRH'      => ['bg' => '#EFF6FF', 'border' => '#93C5FD', 'color' => '#1E3A8A', 'icon' => 'fa-user-cog'],
                                            'Manager'      => ['bg' => '#ECFDF5', 'border' => '#6EE7B7', 'color' => '#065F46', 'icon' => 'fa-user-check'],
                                            'Agent'        => ['bg' => '#FFFBEB', 'border' => '#FCD34D', 'color' => '#78350F', 'icon' => 'fa-user'],
                                        ];
                                        $c = $colors[$role->name] ?? ['bg' => '#F9FAFB', 'border' => '#D1D5DB', 'color' => '#374151', 'icon' => 'fa-user'];
                                    @endphp
                                    <div class="col-6 col-md-4">
                                        <label class="role-card"
                                               for="role_{{ $role->name }}"
                                               style="display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;cursor:pointer;border:2px solid {{ $checked ? $c['border'] : '#E5E7EB' }};background:{{ $checked ? $c['bg'] : '#FAFAFA' }};transition:all 150ms;">
                                            <input type="checkbox"
                                                   id="role_{{ $role->name }}"
                                                   name="roles[]"
                                                   value="{{ $role->name }}"
                                                   class="role-checkbox d-none"
                                                   {{ $checked ? 'checked' : '' }}>
                                            <i class="fas {{ $c['icon'] }}" style="color:{{ $c['color'] }};font-size:16px;flex-shrink:0;"></i>
                                            <span class="fw-600" style="font-size:13px;color:{{ $c['color'] }};">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger mt-1" style="font-size:13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary fw-600 flex-fill">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('admin.accounts.show', $user->id) }}" class="btn btn-outline-secondary fw-600">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.role-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        const label = this.closest('label');
        label.style.borderColor = this.checked ? '#93C5FD' : '#E5E7EB';
        label.style.background  = this.checked ? '#EFF6FF' : '#FAFAFA';
    });
});
document.querySelectorAll('.role-card').forEach(label => {
    label.addEventListener('click', function () {
        const cb = this.querySelector('.role-checkbox');
        cb.checked = !cb.checked;
        cb.dispatchEvent(new Event('change'));
    });
});
</script>
@endpush
