@extends('layouts.master')

@section('title', 'Tableau de bord — Administration')
@section('page-title', 'Administration Système')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" style="color:#1565C0;">Tableau de bord</a></li>
@endsection

@push('styles')
<style>
.kpi-card {
    border-radius: 12px;
    padding: 20px 24px;
    transition: box-shadow 200ms, transform 200ms;
    position: relative; overflow: hidden;
}
.kpi-card:hover { box-shadow: 0 6px 20px rgba(10,77,140,0.10); transform: translateY(-2px); }
.kpi-card .kpi-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.kpi-card .kpi-value  { font-size:28px; font-weight:700; line-height:1.1; margin-top:12px; }
.kpi-card .kpi-label  { font-size:13px; margin-top:2px; font-weight:500; }
.kpi-card .kpi-trend  { font-size:12px; font-weight:600; margin-top:6px; }
.kpi-card .kpi-trend.up   { color: #10B981; }
.kpi-card .kpi-trend.down { color: #EF4444; }
.kpi-card::before { content:''; position:absolute; top:0; right:0; width:80px; height:80px; border-radius:0 12px 0 80px; opacity:0.07; }
.kpi-card.blue::before   { background: #0A4D8C; }
.kpi-card.green::before  { background: #059669; }
.kpi-card.amber::before  { background: #D97706; }
.kpi-card.red::before    { background: #DC2626; }
.kpi-card.purple::before { background: #7C3AED; }

.action-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:8px; font-size:13.5px; font-weight:500; text-decoration:none; border:none; cursor:pointer; transition:all 180ms; }
.action-btn-primary { background: #0A4D8C; color: white; }
.action-btn-primary:hover { background: #1565C0; color: white; box-shadow: 0 4px 12px rgba(10,77,140,0.3); transform: translateY(-1px); }

.section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:12px; padding-bottom:6px; }
.panel { border-radius: 12px; padding: 20px; }
.alert-item { display:flex; align-items:flex-start; gap:12px; padding:12px 0; }
.alert-item:last-child { border-bottom: none !important; }
.alert-dot  { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:5px; }
</style>
@endpush

@section('content')

{{-- ─── EN-TÊTE ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold" style="color:#111827;">
            Bonjour, {{ Auth::user()->agent->prenom ?? 'Administrateur' }} 👋
        </h4>
        <p class="mb-0 text-muted" style="font-size:13.5px;">
            {{ now()->isoFormat('dddd D MMMM YYYY') }} — Administration Système
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="#" class="action-btn action-btn-outline">
            <i class="fas fa-clipboard-list"></i> Logs d'audit
        </a>
        <a href="#" class="action-btn action-btn-primary">
            <i class="fas fa-user-plus"></i> Nouvel utilisateur
        </a>
    </div>
</div>

{{-- ─── KPIs ─────────────────────────────────────────────────────── --}}
<div class="section-title">Indicateurs système</div>
<div class="row g-3 mb-4">
    @php
        try { $totalUsers   = \App\Models\User::count(); }                         catch(\Exception $e) { $totalUsers   = 0; }
        try { $totalAgents  = \App\Models\Agent::where('statut','actif')->count();} catch(\Exception $e) { $totalAgents  = 0; }
        try { $logsAujourdhui = \App\Models\LogAudit::whereDate('date_evenement', today())->count(); } catch(\Exception $e) { $logsAujourdhui = 0; }
        try { $tentativesEchouees = \App\Models\User::where('tentatives_connexion','>', 0)->count(); } catch(\Exception $e) { $tentativesEchouees = 0; }
    @endphp

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card blue">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#EFF6FF;"><i class="fas fa-users" style="color:#0A4D8C;"></i></div>
                <span style="background:#EFF6FF;color:#1E40AF;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Actifs</span>
            </div>
            <div class="kpi-value">{{ $totalUsers }}</div>
            <div class="kpi-label">Utilisateurs enregistrés</div>
            <div class="kpi-trend up"><i class="fas fa-arrow-up me-1"></i>Comptes système</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#ECFDF5;"><i class="fas fa-id-badge" style="color:#059669;"></i></div>
                <span style="background:#ECFDF5;color:#065F46;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Actifs</span>
            </div>
            <div class="kpi-value">{{ $totalAgents }}</div>
            <div class="kpi-label">Agents en poste</div>
            <div class="kpi-trend up"><i class="fas fa-user-check me-1"></i>Personnel actif</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card amber">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FFFBEB;"><i class="fas fa-clipboard-list" style="color:#D97706;"></i></div>
                <span style="background:#FEF3C7;color:#92400E;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Aujourd'hui</span>
            </div>
            <div class="kpi-value">{{ $logsAujourdhui }}</div>
            <div class="kpi-label">Événements d'audit</div>
            <div class="kpi-trend neutral"><i class="fas fa-clock me-1"></i>Depuis minuit</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card red">
            <div class="d-flex align-items-start justify-content-between">
                <div class="kpi-icon" style="background:#FEF2F2;"><i class="fas fa-shield-alt" style="color:#DC2626;"></i></div>
                <span style="background:#FEE2E2;color:#991B1B;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;">Alerte</span>
            </div>
            <div class="kpi-value">{{ $tentativesEchouees }}</div>
            <div class="kpi-label">Comptes avec tentatives</div>
            <div class="kpi-trend down"><i class="fas fa-exclamation-triangle me-1"></i>À surveiller</div>
        </div>
    </div>
</div>

{{-- ─── GRAPHIQUES ───────────────────────────────────────────────── --}}
<div class="section-title">Analyse système</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-5">
        <div class="panel h-100">
            <div class="fw-600 mb-1" style="color:#111827;">Distribution des rôles</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:14px;">Répartition actuelle</div>
            <canvas id="chartRoles" style="max-height:220px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="panel h-100">
            <div class="fw-600 mb-1" style="color:#111827;">Activité système — 7 derniers jours</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:14px;">Connexions et actions enregistrées</div>
            <canvas id="chartActivite" style="max-height:220px;"></canvas>
        </div>
    </div>
</div>

{{-- ─── TABLEAUX ─────────────────────────────────────────────────── --}}
<div class="section-title">Activité récente</div>
<div class="row g-3 mb-4">
    {{-- Logs d'audit --}}
    <div class="col-12 col-lg-6">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Derniers événements d'audit</div>
                <a href="#" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @php
                try {
                    $logs = \App\Models\LogAudit::latest('date_evenement')->limit(5)->get();
                } catch(\Exception $e) { $logs = collect(); }
            @endphp
            @forelse($logs as $log)
                <div class="alert-item">
                    <div class="alert-dot" style="background:#0A4D8C;"></div>
                    <div class="flex-1">
                        <div style="font-size:13px;font-weight:500;color:#111827;">{{ $log->action ?? 'Événement système' }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">
                            {{ $log->utilisateur ?? 'Système' }} — {{ optional($log->date_evenement)->diffForHumans() ?? '' }}
                        </div>
                    </div>
                </div>
            @empty
                {{-- Démo --}}
                @foreach([['Connexion réussie','amadou.diop','#10B981','Il y a 5 min'],['Modification agent','fatou.sarr','#0A4D8C','Il y a 12 min'],['Export données','fatou.sarr','#D97706','Il y a 28 min'],['Création utilisateur','amadou.diop','#7C3AED','Il y a 1h'],['Déconnexion','moussa.ndiaye','#6B7280','Il y a 2h']] as $item)
                <div class="alert-item">
                    <div class="alert-dot" style="background:{{ $item[2] }};"></div>
                    <div>
                        <div style="font-size:13px;font-weight:500;color:#111827;">{{ $item[0] }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">{{ $item[1] }} — {{ $item[3] }}</div>
                    </div>
                </div>
                @endforeach
            @endforelse
        </div>
    </div>

    {{-- Utilisateurs récents --}}
    <div class="col-12 col-lg-6">
        <div class="panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-600" style="color:#111827;">Utilisateurs actifs récemment</div>
                <a href="#" style="font-size:12px;color:#1565C0;text-decoration:none;font-weight:500;">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @php
                try {
                    $recentUsers = \App\Models\User::whereNotNull('derniere_connexion')->orderByDesc('derniere_connexion')->limit(5)->get();
                } catch(\Exception $e) { $recentUsers = collect(); }
                $roleColors = ['AdminSystème'=>'#DC2626','DRH'=>'#7C3AED','AgentRH'=>'#059669','Manager'=>'#D97706','Agent'=>'#0A4D8C'];
            @endphp
            @forelse($recentUsers as $user)
                @php $role = $user->getRoleNames()->first() ?? 'Agent'; @endphp
                <div class="alert-item">
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $roleColors[$role] ?? '#0A4D8C' }};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0;">
                        {{ strtoupper(substr($user->login,0,1)) }}
                    </div>
                    <div class="flex-1">
                        <div style="font-size:13px;font-weight:500;color:#111827;">{{ $user->login }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">
                            <span style="background:{{ $roleColors[$role] ?? '#0A4D8C' }}22;color:{{ $roleColors[$role] ?? '#0A4D8C' }};padding:1px 8px;border-radius:20px;font-weight:600;">{{ $role }}</span>
                            — {{ $user->derniere_connexion?->diffForHumans() ?? 'Jamais' }}
                        </div>
                    </div>
                </div>
            @empty
                @foreach([['amadou.diop','AdminSystème'],['ibrahima.diallo','DRH'],['fatou.sarr','AgentRH'],['moussa.ndiaye','Manager'],['aissatou.fall','Agent']] as [$login, $role])
                <div class="alert-item">
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $roleColors[$role] }};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0;">
                        {{ strtoupper(substr($login,0,1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:500;color:#111827;">{{ $login }}</div>
                        <div style="font-size:12px;color:#9CA3AF;">
                            <span style="background:{{ $roleColors[$role] }}22;color:{{ $roleColors[$role] }};padding:1px 8px;border-radius:20px;font-weight:600;">{{ $role }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforelse
        </div>
    </div>
</div>

{{-- ─── ACTIONS RAPIDES ──────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#EFF6FF 0%,#E0F2FE 100%);border:1px solid #BFDBFE;border-radius:12px;padding:20px;margin-top:4px;">
    <div class="fw-600 mb-3" style="color:#0A4D8C;">Actions rapides</div>
    <div class="d-flex flex-wrap gap-2">
        <a href="#" class="action-btn action-btn-primary"><i class="fas fa-user-plus"></i> Créer un utilisateur</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-user-shield"></i> Gérer les rôles</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-clipboard-list"></i> Audit trail</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-cogs"></i> Paramètres système</a>
        <a href="#" class="action-btn action-btn-outline"><i class="fas fa-database"></i> Sauvegarde</a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = { primary:'#0A4D8C', secondary:'#1565C0', green:'#059669', amber:'#D97706', red:'#DC2626', purple:'#7C3AED', grid:'#F3F4F6', text:'#9CA3AF' };

    // Rôles (donut)
    new Chart(document.getElementById('chartRoles'), {
        type: 'doughnut',
        data: {
            labels: ['AdminSystème','DRH','AgentRH','Manager','Agent'],
            datasets: [{ data: [1,1,1,1,{{ $totalUsers - 4 }}], backgroundColor: [colors.red,colors.purple,colors.green,colors.amber,colors.primary], borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, color: colors.text, padding: 10, boxWidth: 10 } } }
        }
    });

    // Activité 7 jours (bar)
    new Chart(document.getElementById('chartActivite'), {
        type: 'bar',
        data: {
            labels: ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'],
            datasets: [
                { label: 'Connexions', data: [28,35,42,38,45,12,8], backgroundColor: 'rgba(10,77,140,0.15)', borderColor: colors.primary, borderWidth: 1.5, borderRadius: 4 },
                { label: 'Actions',    data: [85,102,95,88,110,30,20], backgroundColor: 'rgba(5,150,105,0.12)', borderColor: colors.green, borderWidth: 1.5, borderRadius: 4 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top', labels: { font: { size: 11 }, color: colors.text, boxWidth: 10 } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11 } } },
                y: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11 } }, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
