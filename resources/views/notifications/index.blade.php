@extends('layouts.master')
@section('title', 'Mes Notifications')
@section('page-title', 'Mes Notifications')

@section('breadcrumb')
    <li>Notifications</li>
@endsection

@push('styles')
<style>
.notif-row { border-radius:10px;transition:background 150ms;border-left:3px solid transparent; }
.notif-row.unread { border-left-color:#1565C0;background:#EFF6FF; }
.notif-row:hover { background:#F8FAFF; }
.notif-icon { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.action-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;border:none;cursor:pointer;transition:all 160ms; }
.action-btn-outline { background:transparent;color:#374151;border:1px solid #E5E7EB; }
.action-btn-outline:hover { background:#F9FAFB;color:#111827; }
.action-btn-danger { background:#FEE2E2;color:#DC2626;border:1px solid #FECACA; }
.action-btn-danger:hover { background:#FECACA; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--theme-text);">Mes notifications</h4>
            <p class="text-muted small mb-0">{{ $notifications->total() }} notification(s) au total</p>
        </div>
        @if($notifications->total() > 0)
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('notifications.destroy-all') }}">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn action-btn-danger"
                    onclick="return confirm('Supprimer toutes les notifications ?')">
                    <i class="fas fa-trash"></i> Tout supprimer
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div class="notif-row p-3 {{ $isUnread ? 'unread' : '' }}" style="border-bottom:1px solid var(--theme-border);">
                    <div class="d-flex align-items-start gap-3">
                        {{-- Icône --}}
                        <div class="notif-icon" style="background:{{ $data['color'] ?? '#DBEAFE' }};">
                            <i class="fas {{ $data['icon'] ?? 'fa-bell' }}" style="color:{{ $data['iconColor'] ?? '#1565C0' }};font-size:15px;"></i>
                        </div>

                        {{-- Contenu --}}
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span style="font-size:14px;font-weight:{{ $isUnread ? '600' : '500' }};color:var(--theme-text);">
                                    {{ $data['title'] ?? 'Notification' }}
                                </span>
                                @if($isUnread)
                                    <span style="width:8px;height:8px;background:#1565C0;border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                                @endif
                            </div>
                            <p class="mb-1 text-muted" style="font-size:13px;">{{ $data['message'] ?? '' }}</p>
                            <span style="font-size:11px;color:#9CA3AF;">
                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 flex-shrink-0">
                            @if(isset($data['url']))
                                <a href="{{ route('notifications.read', $notification->id) }}"
                                   class="action-btn action-btn-outline" style="font-size:12px;padding:6px 12px;">
                                    <i class="fas fa-external-link-alt"></i> Voir
                                </a>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn action-btn-danger" style="font-size:12px;padding:6px 12px;"
                                    title="Supprimer">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;background:#F3F4F6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="fas fa-bell-slash" style="font-size:1.5rem;color:#D1D5DB;"></i>
                    </div>
                    <p class="text-muted mb-0 fw-500">Aucune notification</p>
                    <p class="text-muted small">Vous êtes à jour !</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif

</div>
@endsection
