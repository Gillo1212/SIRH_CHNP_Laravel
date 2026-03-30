@extends('layouts.master')

@section('title', 'Agents sans compte')
@section('page-title', 'Agents sans compte')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Administration</a></li>
    <li><a href="{{ route('admin.accounts.index') }}" style="color:#1565C0;">Comptes</a></li>
    <li>Agents sans compte</li>
@endsection

@push('styles')
<style>
.panel { background:#fff; border-radius:12px; padding:24px; border:1px solid #E5E7EB; }
.section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.section-title-text { font-size:15px; font-weight:700; color:#111827; display:flex; align-items:center; gap:10px; }
.table th { font-size:11px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:.05em; border-bottom:2px solid #E5E7EB; }
.table td { font-size:13px; vertical-align:middle; }
.action-btn { width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; border:1px solid; font-size:12px; transition:all 150ms; cursor:pointer; background:transparent; }
.badge-role { font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; background:#EFF6FF; color:#1E40AF; }
.modal-sirh .modal-content { border:none; border-radius:14px; box-shadow:0 20px 50px rgba(0,0,0,.18); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-700 mb-0" style="color:#111827;">
                <i class="fas fa-user-clock me-2" style="color:#D97706;"></i>Agents sans compte
            </h4>
            <div style="font-size:13px;color:#6B7280;margin-top:2px;">
                Vue consolidée : agents sans accès + comptes en attente de dossier
            </div>
        </div>
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary fw-600">
            <i class="fas fa-arrow-left me-2"></i>Tous les comptes
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SECTION 1 — Comptes créés par Admin en attente RH
         ═══════════════════════════════════════════════════════ --}}
    <div class="panel mb-4">
        <div class="section-header">
            <div class="section-title-text">
                <div style="width:36px;height:36px;border-radius:9px;background:#FFFBEB;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-clock" style="color:#D97706;font-size:16px;"></i>
                </div>
                <div>
                    <div>Comptes en attente de dossier RH</div>
                    <div style="font-size:12px;font-weight:400;color:#6B7280;">Créés par l'Admin · La RH doit compléter le dossier agent</div>
                </div>
            </div>
            <span class="badge" style="background:#FEF3C7;color:#92400E;font-size:13px;font-weight:700;padding:5px 12px;border-radius:20px;">
                {{ $comptesSansDossier->count() }} compte(s)
            </span>
        </div>

        @if($comptesSansDossier->isEmpty())
            <div class="text-center py-4" style="color:#9CA3AF;">
                <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#10B981;"></i>
                <span style="font-size:13px;">Tous les comptes Admin ont leur dossier RH complété.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Rôle(s)</th>
                            <th>Créé le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comptesSansDossier as $user)
                        <tr>
                            <td class="fw-600" style="color:#0A4D8C;">{{ $user->login }}</td>
                            <td style="color:#6B7280;">{{ $user->email ?? '—' }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge-role me-1">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td style="color:#9CA3AF;font-size:12px;">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Renvoyer notif RH --}}
                                    <button type="button"
                                            class="action-btn text-warning border-warning-subtle bg-warning-subtle"
                                            title="Notifier la RH pour compléter le dossier"
                                            onclick="confirmResendNotif({{ $user->id }}, '{{ $user->login }}')">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                    {{-- Voir compte --}}
                                    <a href="{{ route('admin.accounts.show', $user->id) }}"
                                       class="action-btn text-info border-info-subtle bg-info-subtle" title="Voir le compte">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <form id="form-resend-{{ $user->id }}"
                                      action="{{ route('admin.accounts.resend-rh', $user->id) }}"
                                      method="POST" class="d-none">@csrf</form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SECTION 2 — Agents sans compte utilisateur
         ═══════════════════════════════════════════════════════ --}}
    <div class="panel">
        <div class="section-header">
            <div class="section-title-text">
                <div style="width:36px;height:36px;border-radius:9px;background:#FEF2F2;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-user-slash" style="color:#DC2626;font-size:16px;"></i>
                </div>
                <div>
                    <div>Agents sans compte utilisateur</div>
                    <div style="font-size:12px;font-weight:400;color:#6B7280;">Créés par la RH · L'Admin doit créer leur compte d'accès</div>
                </div>
            </div>
            <span class="badge" style="background:#FEE2E2;color:#991B1B;font-size:13px;font-weight:700;padding:5px 12px;border-radius:20px;">
                {{ $agentsSansUser->count() }} agent(s)
            </span>
        </div>

        @if($agentsSansUser->isEmpty())
            <div class="text-center py-4" style="color:#9CA3AF;">
                <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#10B981;"></i>
                <span style="font-size:13px;">Tous les agents ont un compte utilisateur.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Agent</th>
                            <th>Fonction</th>
                            <th>Service</th>
                            <th>Date recrutement</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agentsSansUser as $agent)
                        <tr>
                            <td>
                                <span class="fw-700" style="font-size:12px;color:#6B7280;font-family:monospace;">{{ $agent->matricule }}</span>
                            </td>
                            <td>
                                <div class="fw-600" style="color:#111827;">{{ $agent->prenom }} {{ $agent->nom }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ ucfirst(strtolower($agent->sexe === 'M' ? 'Homme' : 'Femme')) }}</div>
                            </td>
                            <td style="color:#374151;font-size:12px;">{{ str_replace('_',' ',$agent->famille_d_emploi ?? '—') ?? '—' }}</td>
                            <td style="color:#6B7280;font-size:12px;">{{ $agent->service->nom_service ?? '—' }}</td>
                            <td style="color:#9CA3AF;font-size:12px;">{{ str_replace('_', ' ', $agent->statut_agent ?? '—') }}</td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-primary fw-600"
                                        onclick="ouvrirModalCompte({{ $agent->id_agent }}, '{{ $agent->prenom }} {{ $agent->nom }}', '{{ $agent->matricule }}')"
                                        style="font-size:12px;">
                                    <i class="fas fa-user-plus me-1"></i>Créer compte
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL — Créer compte pour agent existant
     ═══════════════════════════════════════════════════════════ --}}
<div class="modal fade modal-sirh" id="modalCompteAgent" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-700 mb-0" style="color:#111827;">
                        <i class="fas fa-user-plus me-2" style="color:#0A4D8C;"></i>Créer un compte pour
                    </h5>
                    <p class="mb-0 fw-600 mt-1" id="modal_agent_name" style="color:#0A4D8C;font-size:14px;"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCompteAgent" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:13px;">Login <span class="text-danger">*</span></label>
                        <input type="text" name="login" id="modal_login" class="form-control" required
                               placeholder="ex: prenom.nom">
                        <div class="form-text">Minuscules, chiffres, points, tirets. Min. 3 car.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:13px;">Email professionnel</label>
                        <input type="email" name="email" id="modal_email" class="form-control"
                               placeholder="prenom.nom@chnp.sn">
                        <div class="form-text">Optionnel — les identifiants seront envoyés à cet email.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600" style="font-size:13px;">Rôle <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">— Choisir un rôle —</option>
                            <option value="Agent">Agent</option>
                            <option value="Manager">Manager</option>
                            <option value="AgentRH">Agent RH</option>
                            <option value="DRH">DRH</option>
                            <option value="AdminSystème">Admin Système</option>
                        </select>
                    </div>
                    <div class="alert border-0" style="background:#EFF6FF;border-radius:8px;font-size:12px;color:#1E40AF;">
                        <i class="fas fa-info-circle me-2"></i>
                        Un mot de passe temporaire sera généré automatiquement. Le dossier agent étant déjà créé, le compte sera immédiatement actif.
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

@endsection

@push('scripts')
<script>
function ouvrirModalCompte(agentId, nom, matricule) {
    document.getElementById('modal_agent_name').textContent = nom + ' (' + matricule + ')';
    document.getElementById('formCompteAgent').action = '{{ url('admin/users/store-for-agent') }}/' + agentId;
    // Suggestion de login : prenom.nom en lowercase sans accents
    const parts = nom.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').split(' ');
    const login = (parts[0] + '.' + (parts[1] || '')).replace(/[^a-z0-9._-]/g,'');
    document.getElementById('modal_login').value = login;
    // Suggestion email
    document.getElementById('modal_email').value = login + '@chnp.sn';
    new bootstrap.Modal(document.getElementById('modalCompteAgent')).show();
}

function confirmResendNotif(id, login) {
    Swal.fire({
        title: 'Notifier la RH ?',
        html: `Un email sera envoyé à tous les AgentRH et DRH pour compléter le dossier de <strong>${login}</strong>.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0A4D8C',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Notifier',
    }).then(r => { if(r.isConfirmed) document.getElementById('form-resend-'+id).submit(); });
}
</script>
@endpush
