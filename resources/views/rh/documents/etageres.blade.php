@extends('layouts.master')
@section('title', 'GED — Étagères')
@section('page-title', 'Gestion des Étagères')

@section('breadcrumb')
    <li><a href="{{ route('rh.dashboard') }}" style="color:#1565C0;">RH</a></li>
    <li><a href="{{ route('rh.ged.index') }}" style="color:#1565C0;">GED</a></li>
    <li>Étagères</li>
@endsection

@push('styles')
<style>
/* ── BOUTONS ─────────────────────────────────────────────────── */
.ged-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all 180ms;text-decoration:none;border:none; }
.ged-btn-primary { background:#0A4D8C;color:#fff; }
.ged-btn-primary:hover { background:#1565C0;color:#fff;box-shadow:0 4px 12px rgba(10,77,140,.30); }
.ged-btn-outline { background:#fff;color:#374151;border:1px solid #E5E7EB; }
.ged-btn-outline:hover { background:#F9FAFB; }

/* ── CLASSEUR (cabinet par service) ─────────────────────────── */
.cabinet {
    background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%);
    border: 2px solid #CBD5E1;
    border-radius: 14px;
    padding: 0;
    margin-bottom: 28px;
    box-shadow: 0 4px 16px rgba(0,0,0,.06), inset 0 1px 0 rgba(255,255,255,.8);
    overflow: hidden;
}

.cabinet-header {
    background: linear-gradient(135deg, #1E293B, #334155);
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff;
}
.cabinet-header-icon {
    width: 34px; height: 34px;
    background: rgba(255,255,255,.12);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.cabinet-header-name { font-size: 13px; font-weight: 700; flex: 1; }
.cabinet-header-badge { font-size: 11px; background: rgba(255,255,255,.15); padding: 3px 10px; border-radius: 20px; }

.cabinet-body { padding: 10px; }

/* ── TIROIR ──────────────────────────────────────────────────── */
.tiroir {
    background: #fff;
    border: 1.5px solid #E2E8F0;
    border-radius: 10px;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    transition: box-shadow 180ms;
}
.tiroir:last-child { margin-bottom: 0; }
.tiroir:hover { box-shadow: 0 4px 14px rgba(10,77,140,.09); }

/* Handle du tiroir (poignée) */
.tiroir-handle {
    padding: 13px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    user-select: none;
    transition: background 150ms;
    position: relative;
}
.tiroir-handle:hover { background: #F8FAFC; }

/* Ligne de "métal" simulant la poignée physique */
.tiroir-handle::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #0A4D8C, #1565C0);
    border-radius: 10px 0 0 10px;
    opacity: 0;
    transition: opacity 180ms;
}
.tiroir.is-open > .tiroir-handle::before { opacity: 1; }

.tiroir-icon {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    color: #1D4ED8;
    flex-shrink: 0;
}
.tiroir.is-open .tiroir-icon {
    background: linear-gradient(135deg, #0A4D8C, #1565C0);
    color: #fff;
}

.tiroir-info { flex: 1; min-width: 0; }
.tiroir-name { font-size: 13.5px; font-weight: 600; color: #1E293B; }
.tiroir-meta { font-size: 11.5px; color: #64748B; margin-top: 2px; }

.tiroir-badge-count {
    background: #EFF6FF;
    color: #1D4ED8;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    flex-shrink: 0;
}
.tiroir.is-open .tiroir-badge-count { background: #0A4D8C; color: #fff; }

/* Chevron animé */
.tiroir-chevron {
    color: #94A3B8;
    transition: transform 200ms, color 200ms;
    flex-shrink: 0;
}
.tiroir.is-open .tiroir-chevron { transform: rotate(90deg); color: #0A4D8C; }

/* ── CONTENU DU TIROIR ───────────────────────────────────────── */
.tiroir-content {
    display: none;
    border-top: 1.5px solid #E2E8F0;
    background: #FAFBFD;
    padding: 14px;
}
.tiroir.is-open .tiroir-content { display: block; }

/* Barre de remplissage du tiroir */
.tiroir-fill { height: 3px; background: #E2E8F0; border-radius: 2px; margin: 4px 0 8px; overflow: hidden; }
.tiroir-fill-bar { height: 100%; background: linear-gradient(90deg, #0A4D8C, #1565C0); border-radius: 2px; transition: width .4s; }

/* ── MINI-CARTES AGENT dans le tiroir ────────────────────────── */
.agent-mini-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }

.agent-mini {
    background: #fff;
    border: 1.5px solid #E5E7EB;
    border-radius: 10px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: inherit;
    transition: all 180ms;
}
.agent-mini:hover {
    border-color: #BFDBFE;
    box-shadow: 0 4px 14px rgba(10,77,140,.12);
    transform: translateY(-1px);
    color: inherit;
    text-decoration: none;
}
.agent-mini-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    color: #1D4ED8;
    font-size: 13px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    border: 2px solid #DBEAFE;
}
.agent-mini-name { font-size: 12.5px; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.agent-mini-mat  { font-size: 11px; color: #9CA3AF; }

.agent-mini-docs {
    margin-left: auto;
    background: #F3F4F6;
    color: #6B7280;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 20px;
    flex-shrink: 0;
}

/* ── FORMULAIRE ─────────────────────────────────────────────── */
.form-label-sm { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6B7280;margin-bottom:6px;display:block; }
.form-ctrl { border:1px solid #E5E7EB;border-radius:8px;padding:10px 14px;font-size:13.5px;transition:border-color 180ms;width:100%;outline:none; }
.form-ctrl:focus { border-color:#0A4D8C;box-shadow:0 0 0 3px rgba(10,77,140,.1); }

/* ── ÉTAT VIDE ───────────────────────────────────────────────── */
.empty-tiroir { text-align:center;padding:20px;color:#94A3B8;font-size:12.5px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 mb-3" style="border-radius:10px;background:#ECFDF5;color:#065F46;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TOOLBAR --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h5 class="mb-0 fw-700" style="color:#111827;">Étagères GED</h5>
            <p class="mb-0 mt-1" style="font-size:13px;color:#6B7280;">
                Chaque étagère regroupe les dossiers des agents d'un service. Cliquez sur un tiroir pour l'ouvrir.
            </p>
        </div>
        <button type="button" class="ged-btn ged-btn-primary" data-bs-toggle="modal" data-bs-target="#newEtagereModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle étagère
        </button>
    </div>

    {{-- CLASSEURS (un par service) --}}
    @forelse($etageres as $serviceName => $shelves)
    <div class="cabinet">

        {{-- En-tête classeur --}}
        <div class="cabinet-header">
            <div class="cabinet-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <span class="cabinet-header-name">{{ $serviceName }}</span>
            <span class="cabinet-header-badge">{{ $shelves->count() }} étagère(s)</span>
            <span class="cabinet-header-badge ms-2">{{ $shelves->sum('dossiers_count') }} dossier(s)</span>
        </div>

        {{-- Tiroirs --}}
        <div class="cabinet-body">
            @forelse($shelves as $et)
            @php
                $nbDossiers = $et->dossiers_count;
                $maxDossiers = 20;
                $pct = $maxDossiers > 0 ? min(100, ($nbDossiers / $maxDossiers) * 100) : 0;
            @endphp
            <div class="tiroir" id="tiroir-{{ $et->id_etagere }}"
                 onclick="toggleTiroir({{ $et->id_etagere }})">

                {{-- Poignée --}}
                <div class="tiroir-handle">
                    <div class="tiroir-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    </div>
                    <div class="tiroir-info">
                        <div class="tiroir-name">
                            {{ $et->nom_etagere }}
                            @if($et->numero) <span style="color:#94A3B8;font-weight:400;font-size:12px;"> · N°{{ $et->numero }}</span> @endif
                            @if(!$et->actif) <span class="badge bg-secondary ms-2" style="font-size:10px;">Inactif</span> @endif
                        </div>
                        @if($et->description)
                        <div class="tiroir-meta">{{ $et->description }}</div>
                        @endif
                        <div class="tiroir-fill">
                            <div class="tiroir-fill-bar" style="width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                    <span class="tiroir-badge-count">
                        {{ $nbDossiers }} dossier{{ $nbDossiers !== 1 ? 's' : '' }}
                    </span>
                    <svg class="tiroir-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                {{-- Contenu du tiroir : cartes agents --}}
                <div class="tiroir-content" onclick="event.stopPropagation()">
                    @if($et->dossiers->isEmpty())
                        <div class="empty-tiroir">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="display:block;margin:0 auto 8px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            Aucun dossier dans cette étagère
                        </div>
                    @else
                        <div class="agent-mini-grid">
                            @foreach($et->dossiers as $dos)
                            @php $ag = $dos->agent; @endphp
                            <a href="{{ route('rh.ged.dossier.show', $dos->id_dossier) }}" class="agent-mini">
                                <div class="agent-mini-avatar">
                                    @if($ag?->photo)
                                        <img src="{{ asset('storage/'.$ag->photo) }}" alt=""
                                             style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                    @else
                                        {{ strtoupper(substr($ag?->prenom??'?',0,1)) }}{{ strtoupper(substr($ag?->nom??'',0,1)) }}
                                    @endif
                                </div>
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="agent-mini-name">{{ $ag?->nom_complet ?? 'Agent' }}</div>
                                    <div class="agent-mini-mat">{{ $ag?->matricule ?? $dos->reference }}</div>
                                </div>
                                <span class="agent-mini-docs">
                                    {{ $dos->documents_count ?? '—' }}
                                </span>
                            </a>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-3 pt-2" style="border-top:1px solid #F1F5F9;">
                            <a href="{{ route('rh.ged.dossiers', ['etagere' => $et->id_etagere]) }}"
                               class="ged-btn ged-btn-outline" style="font-size:12px;padding:6px 14px;"
                               onclick="event.stopPropagation()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                Voir tous les dossiers de cette étagère
                            </a>
                        </div>
                    @endif
                </div>

            </div>
            @empty
            <div class="empty-tiroir" style="background:rgba(255,255,255,.5);border-radius:10px;">
                Aucune étagère pour ce service
            </div>
            @endforelse
        </div>

    </div>
    @empty
    <div class="text-center py-5 text-muted" style="background:#fff;border:1px dashed #E5E7EB;border-radius:14px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" stroke-width="1.5" style="display:block;margin:0 auto 14px;"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
        <p class="fw-500 mb-1">Aucune étagère configurée</p>
        <p style="font-size:13px;">Créez des étagères pour organiser les dossiers des agents par service</p>
        <button class="ged-btn ged-btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newEtagereModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Créer la première étagère
        </button>
    </div>
    @endforelse
</div>

{{-- ═══ MODAL NOUVELLE ÉTAGÈRE ═══════════════════════════════════ --}}
<div class="modal fade" id="newEtagereModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px;">
            <form action="{{ route('rh.ged.etageres.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0" style="padding:20px 24px 0;">
                    <h5 class="modal-title fw-700" style="color:#111827;">Nouvelle étagère</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <div class="mb-3">
                        <label class="form-label-sm">Service <span class="text-danger">*</span></label>
                        <select name="id_service" class="form-ctrl" required>
                            <option value="">-- Sélectionner un service --</option>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id_service }}">{{ $svc->nom_service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-sm">Nom de l'étagère <span class="text-danger">*</span></label>
                        <input type="text" name="nom_etagere" class="form-ctrl"
                               placeholder="Ex: Étagère Médecins Internes" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label-sm">Numéro</label>
                            <input type="text" name="numero" class="form-ctrl" placeholder="01">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label-sm">Description</label>
                        <textarea name="description" class="form-ctrl" rows="2" placeholder="Description optionnelle…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:0 24px 20px;">
                    <button type="button" class="ged-btn ged-btn-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="ged-btn ged-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Créer l'étagère
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleTiroir(id) {
    const el = document.getElementById('tiroir-' + id);
    el.classList.toggle('is-open');
}
</script>
@endpush
