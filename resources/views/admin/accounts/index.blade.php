@extends('layouts.master')

@section('title', 'Comptes Utilisateurs')
@section('page-title', 'Comptes Utilisateurs')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li>Comptes Utilisateurs</li>
@endsection

@push('styles')
<style>
.kpi-card { border-radius:12px; padding:20px 24px; transition:box-shadow 200ms,transform 200ms; position:relative; overflow:hidden; }
.kpi-card:hover { box-shadow:0 6px 20px rgba(10,77,140,.10); transform:translateY(-2px); }
.kpi-card .kpi-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.kpi-card .kpi-value { font-size:28px; font-weight:700; line-height:1.1; margin-top:12px; }
.kpi-card .kpi-label { font-size:13px; margin-top:2px; font-weight:500; }
.kpi-card::before { content:''; position:absolute; top:0; right:0; width:80px; height:80px; border-radius:0 12px 0 80px; opacity:.07; }
.kpi-card.blue::before  { background:#0A4D8C; }
.kpi-card.green::before { background:#059669; }
.kpi-card.amber::before { background:#D97706; }
.kpi-card.red::before   { background:#DC2626; }
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }
.badge-role { font-size:11px; font-weight:600; padding:2px 10px; border-radius:20px; background:#EFF6FF; color:#1E40AF; }
.table th { font-size:12px; font-weight:600; color:#6B7280; text-transform:uppercase; letter-spacing:.04em; border-bottom:2px solid #E5E7EB; }
.table td { font-size:13.5px; vertical-align:middle; }
.action-btn { width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid; font-size:13px; transition:all 150ms; cursor:pointer; background:transparent; }
/* Modal */
.modal-sirh .modal-content { border:none; border-radius:14px; box-shadow:0 20px 50px rgba(0,0,0,.18); }
.modal-sirh .modal-header { padding:20px 24px; border-bottom:1px solid #E5E7EB; }
.modal-sirh .modal-body   { padding:24px; }
.modal-sirh .modal-footer { padding:16px 24px; border-top:1px solid #E5E7EB; }
.role-card { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; cursor:pointer; border:2px solid #E5E7EB; background:#FAFAFA; transition:all 150ms; user-select:none; }
.role-card.selected { border-color:#93C5FD; background:#EFF6FF; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-700 mb-0" style="color:#111827;">
                <i class="fas fa-users-cog me-2" style="color:#0A4D8C;"></i>Comptes Utilisateurs
            </h4>
            <div style="font-size:13px;color:#6B7280;margin-top:2px;">Gestion des accès au SIRH</div>
        </div>
        <button type="button" class="btn btn-primary fw-600" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fas fa-plus me-2"></i>Nouveau compte
        </button>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="kpi-card blue" style="background:#EFF6FF;">
                <div class="kpi-icon" style="background:#DBEAFE;"><i class="fas fa-users" style="color:#1D4ED8;"></i></div>
                <div class="kpi-value" style="color:#1E3A8A;">{{ $stats['total'] }}</div>
                <div class="kpi-label" style="color:#1E40AF;">Comptes totaux</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card green" style="background:#ECFDF5;">
                <div class="kpi-icon" style="background:#D1FAE5;"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
                <div class="kpi-value" style="color:#065F46;">{{ $stats['completes'] }}</div>
                <div class="kpi-label" style="color:#065F46;">Dossiers complétés</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card amber" style="background:#FFFBEB;">
                <div class="kpi-icon" style="background:#FEF3C7;"><i class="fas fa-clock" style="color:#D97706;"></i></div>
                <div class="kpi-value" style="color:#92400E;">{{ $stats['attente'] }}</div>
                <div class="kpi-label" style="color:#92400E;">En attente RH</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="kpi-card red" style="background:#FEF2F2;">
                <div class="kpi-icon" style="background:#FEE2E2;"><i class="fas fa-lock" style="color:#DC2626;"></i></div>
                <div class="kpi-value" style="color:#7F1D1D;">{{ $stats['verrouilles'] }}</div>
                <div class="kpi-label" style="color:#991B1B;">Verrouillés</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <form action="{{ route('admin.accounts.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="flex-grow-1" style="min-width:250px;max-width:400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted" style="font-size:12px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Login, email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <select name="role" class="form-select" style="width:auto;min-width:160px;">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <select name="dossier_status" class="form-select" style="width:auto;min-width:150px;">
                    <option value="">Dossier RH</option>
                    <option value="completed" {{ request('dossier_status') == 'completed' ? 'selected' : '' }}>Complété</option>
                    <option value="pending"   {{ request('dossier_status') == 'pending'   ? 'selected' : '' }}>En attente</option>
                </select>
                <select name="statut" class="form-select" style="width:auto;min-width:150px;">
                    <option value="">Statut compte</option>
                    <option value="Actif"    {{ request('statut') == 'Actif'    ? 'selected' : '' }}>Actif</option>
                    <option value="Suspendu" {{ request('statut') == 'Suspendu' ? 'selected' : '' }}>Suspendu</option>
                    <option value="Inactif"  {{ request('statut') == 'Inactif'  ? 'selected' : '' }}>Inactif</option>
                </select>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" style="white-space:nowrap;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @if(request()->anyFilled(['search', 'role', 'dossier_status', 'statut']))
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="panel">
        <div class="d-flex justify-content-between mb-3">
            <span style="font-size:13px;color:#6B7280;">{{ $users->total() }} compte(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Rôle(s)</th>
                        <th>Dossier RH</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <a href="{{ route('admin.accounts.show', $user->id) }}"
                               class="fw-600 text-decoration-none" style="color:#0A4D8C;">{{ $user->login }}</a>
                            @if($user->verouille)
                                <i class="fas fa-lock ms-1 text-danger" style="font-size:10px;" title="Verrouillé"></i>
                            @endif
                        </td>
                        <td style="color:#6B7280;font-size:13px;">{{ $user->email ?? '—' }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge-role me-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->agent_completed)
                                <span class="badge bg-success-subtle text-success fw-600" style="font-size:11px;"><i class="fas fa-check me-1"></i>Complété</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning fw-600" style="font-size:11px;"><i class="fas fa-clock me-1"></i>En attente</span>
                            @endif
                        </td>
                        <td>
                            @if($user->verouille)
                                <span class="badge bg-danger-subtle text-danger fw-600" style="font-size:11px;">Verrouillé</span>
                            @elseif($user->statut_compte === 'Actif')
                                <span class="badge bg-success-subtle text-success fw-600" style="font-size:11px;">Actif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary fw-600" style="font-size:11px;">{{ $user->statut_compte }}</span>
                            @endif
                        </td>
                        <td style="color:#9CA3AF;font-size:12px;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Voir --}}
                                <a href="{{ route('admin.accounts.show', $user->id) }}"
                                   class="action-btn text-info border-info-subtle bg-info-subtle" title="Voir détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- Éditer (ouvre modal) --}}
                                <button type="button"
                                        class="action-btn text-primary border-primary-subtle bg-primary-subtle"
                                        title="Modifier"
                                        onclick="ouvrirModalEdit({{ $user->id }}, '{{ route('admin.accounts.data', $user->id) }}', '{{ route('admin.accounts.update', $user->id) }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                {{-- Reset MDP --}}
                                <button type="button"
                                        class="action-btn text-secondary border-secondary-subtle bg-light"
                                        title="Réinitialiser mot de passe"
                                        onclick="confirmResetPassword({{ $user->id }}, '{{ $user->login }}')">
                                    <i class="fas fa-key"></i>
                                </button>
                                {{-- Notif RH ou lien dossier --}}
                                @if(!$user->agent_completed)
                                    <button type="button"
                                            class="action-btn text-warning border-warning-subtle bg-warning-subtle"
                                            title="Renvoyer notification RH"
                                            onclick="confirmResendNotif({{ $user->id }}, '{{ $user->login }}')">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                @elseif($user->agent)
                                    <a href="{{ route('rh.agents.show', $user->agent->id_agent) }}"
                                       class="action-btn text-success border-success-subtle bg-success-subtle" title="Voir dossier agent">
                                        <i class="fas fa-id-badge"></i>
                                    </a>
                                @endif
                            </div>
                            {{-- Forms POST cachés --}}
                            <form id="form-reset-{{ $user->id }}" action="{{ route('admin.accounts.reset-password', $user->id) }}" method="POST" class="d-none">@csrf</form>
                            <form id="form-resend-{{ $user->id }}" action="{{ route('admin.accounts.resend-rh', $user->id) }}" method="POST" class="d-none">@csrf</form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:#9CA3AF;">
                            <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#E5E7EB;"></i>
                            Aucun compte trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="mt-4">{{ $users->links() }}</div>
        @endif
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL — CRÉER UN COMPTE
     ═══════════════════════════════════════════════════════════ --}}
<div class="modal fade modal-sirh" id="modalCreate" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-700 mb-0" style="color:#111827;">
                        <i class="fas fa-user-plus me-2" style="color:#0A4D8C;"></i>Nouveau compte utilisateur
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:12px;">Identifiants système uniquement. La RH complètera le dossier.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounts.store') }}" method="POST" id="formCreate">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Login --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Login <span class="text-danger">*</span></label>
                            <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                                   value="{{ old('login') }}" placeholder="ex: jean.dupont" required>
                            @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Minuscules, chiffres, points, tirets. Min. 4 car.</div>
                        </div>
                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="ex: jean@chnp.sn" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- Mot de passe --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="c_password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min. 8 caractères" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleVis('c_password','c_eye')"><i class="fas fa-eye" id="c_eye"></i></button>
                                <button type="button" class="btn btn-outline-primary" onclick="genPwd()"><i class="fas fa-random"></i></button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        {{-- Confirmation --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px;">Confirmer <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="c_confirm" name="password_confirmation"
                                       class="form-control" placeholder="Répétez" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleVis('c_confirm','c_eye2')"><i class="fas fa-eye" id="c_eye2"></i></button>
                            </div>
                        </div>
                        {{-- MDP généré --}}
                        <div class="col-12" id="c_gen_box" style="display:none;">
                            <div style="background:#F0FDF4;border:1px solid #86EFAC;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:12px;">
                                <i class="fas fa-key" style="color:#16A34A;"></i>
                                <span style="font-size:13px;color:#15803D;">Mot de passe généré : </span>
                                <code id="c_gen_display" style="font-size:14px;font-weight:700;color:#166534;"></code>
                                <button type="button" class="btn btn-sm ms-auto" onclick="copyPwd()"
                                        style="background:#DCFCE7;border:none;color:#166534;">
                                    <i class="fas fa-copy me-1"></i>Copier
                                </button>
                            </div>
                        </div>
                        {{-- Rôles --}}
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:13px;">Rôle(s) <span class="text-danger">*</span></label>
                            @error('roles')<div class="text-danger mb-2" style="font-size:13px;">{{ $message }}</div>@enderror
                            <div class="row g-2">
                                @foreach($roles as $role)
                                @php
                                    $checked = in_array($role->name, old('roles', []));
                                    $ic = ['AdminSystème'=>'fa-shield-alt','DRH'=>'fa-user-tie','AgentRH'=>'fa-user-cog','Manager'=>'fa-user-check','Agent'=>'fa-user'];
                                @endphp
                                <div class="col-6 col-md-4">
                                    <label class="role-card {{ $checked ? 'selected' : '' }}" onclick="toggleRole(this)">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="d-none" {{ $checked ? 'checked' : '' }}>
                                        <i class="fas {{ $ic[$role->name] ?? 'fa-user' }}" style="color:#0A4D8C;"></i>
                                        <span class="fw-600" style="font-size:13px;">{{ $role->name }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        {{-- Notifier RH --}}
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="c_notify" name="notify_rh" value="1" checked>
                                <label class="form-check-label fw-600" for="c_notify" style="font-size:13px;">
                                    <i class="fas fa-bell me-1" style="color:#F59E0B;"></i>Notifier la RH immédiatement
                                </label>
                                <div class="form-text ms-4">Email envoyé à tous les AgentRH et DRH avec un lien pour compléter le dossier.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-600">
                        <i class="fas fa-save me-2"></i>Créer le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL — MODIFIER UN COMPTE
     ═══════════════════════════════════════════════════════════ --}}
<div class="modal fade modal-sirh" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-700 mb-0" style="color:#111827;">
                    <i class="fas fa-edit me-2" style="color:#0A4D8C;"></i>Modifier le compte — <span id="edit_login_title"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="edit_loading" class="text-center py-4">
                        <div class="spinner-border text-primary" style="width:2rem;height:2rem;"></div>
                        <div class="mt-2 text-muted" style="font-size:13px;">Chargement…</div>
                    </div>
                    <div id="edit_fields" style="display:none;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:13px;">Login <span class="text-danger">*</span></label>
                                <input type="text" id="edit_login" name="login" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600" style="font-size:13px;">Email <span class="text-danger">*</span></label>
                                <input type="email" id="edit_email" name="email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600" style="font-size:13px;">Rôle(s) <span class="text-danger">*</span></label>
                                <div class="row g-2" id="edit_roles_container">
                                    @foreach($roles as $role)
                                    @php $ic = ['AdminSystème'=>'fa-shield-alt','DRH'=>'fa-user-tie','AgentRH'=>'fa-user-cog','Manager'=>'fa-user-check','Agent'=>'fa-user']; @endphp
                                    <div class="col-6 col-md-4">
                                        <label class="role-card" onclick="toggleRole(this)" data-role="{{ $role->name }}">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="d-none">
                                            <i class="fas {{ $ic[$role->name] ?? 'fa-user' }}" style="color:#0A4D8C;"></i>
                                            <span class="fw-600" style="font-size:13px;">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-600" id="edit_submit">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Générateur mot de passe ──
function genPwd() {
    const u='ABCDEFGHJKLMNPQRSTUVWXYZ', l='abcdefghjkmnpqrstuvwxyz', d='23456789', s='!@#$%&*';
    const all=u+l+d+s;
    let p=u[~~(Math.random()*u.length)]+l[~~(Math.random()*l.length)]+d[~~(Math.random()*d.length)]+s[~~(Math.random()*s.length)];
    for(let i=4;i<12;i++) p+=all[~~(Math.random()*all.length)];
    p=p.split('').sort(()=>Math.random()-.5).join('');
    document.getElementById('c_password').value=p;
    document.getElementById('c_confirm').value=p;
    document.getElementById('c_gen_display').textContent=p;
    document.getElementById('c_gen_box').style.display='block';
}
function copyPwd(){
    navigator.clipboard.writeText(document.getElementById('c_gen_display').textContent).then(()=>{
        Swal.fire({toast:true,position:'top-end',icon:'success',title:'Copié !',showConfirmButton:false,timer:1500});
    });
}
function toggleVis(id,iconId){
    const el=document.getElementById(id), ic=document.getElementById(iconId);
    if(el.type==='password'){ el.type='text'; ic.classList.replace('fa-eye','fa-eye-slash'); }
    else { el.type='password'; ic.classList.replace('fa-eye-slash','fa-eye'); }
}

// ── Role cards toggle ──
function toggleRole(label){
    const cb=label.querySelector('input[type=checkbox]');
    cb.checked=!cb.checked;
    label.classList.toggle('selected', cb.checked);
}

// ── Reset MDP (confirmation Swal) ──
function confirmResetPassword(id, login){
    Swal.fire({
        title: 'Réinitialiser le mot de passe ?',
        html: `Le nouveau mot de passe de <strong>${login}</strong> sera affiché ici.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0A4D8C',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Réinitialiser',
    }).then(r => { if(r.isConfirmed) document.getElementById('form-reset-'+id).submit(); });
}

// ── Resend notif RH ──
function confirmResendNotif(id, login){
    Swal.fire({
        title: 'Renvoyer la notification ?',
        html: `Tous les AgentRH et DRH recevront un email pour compléter le dossier de <strong>${login}</strong>.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0A4D8C',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Envoyer',
    }).then(r => { if(r.isConfirmed) document.getElementById('form-resend-'+id).submit(); });
}

// ── Modal édition ──
function ouvrirModalEdit(userId, dataUrl, updateUrl){
    const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    document.getElementById('edit_loading').style.display = 'block';
    document.getElementById('edit_fields').style.display  = 'none';
    document.getElementById('formEdit').action = updateUrl;
    document.getElementById('edit_login_title').textContent = '…';
    modal.show();

    fetch(dataUrl, { headers:{ 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit_login').value = data.login;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_login_title').textContent = data.login;

            // Cocher les rôles actuels
            document.querySelectorAll('#edit_roles_container .role-card').forEach(label => {
                const cb = label.querySelector('input');
                const active = data.roles.includes(label.dataset.role);
                cb.checked = active;
                label.classList.toggle('selected', active);
            });

            document.getElementById('edit_loading').style.display = 'none';
            document.getElementById('edit_fields').style.display  = 'block';
        })
        .catch(() => {
            modal.hide();
            Swal.fire({ icon:'error', title:'Erreur', text:'Impossible de charger les données du compte.' });
        });
}

// ── Auto-ouvrir modal create si erreurs de validation ──
@if($errors->any() && old('login'))
document.addEventListener('DOMContentLoaded', ()=>new bootstrap.Modal(document.getElementById('modalCreate')).show());
@endif
</script>
@endpush
