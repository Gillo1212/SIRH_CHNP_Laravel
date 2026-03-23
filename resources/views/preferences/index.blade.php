@extends('layouts.master')

@section('title', __('messages.preferences'))
@section('page-title', __('messages.preferences'))

@section('breadcrumb')
    <li><span style="color:#6B7280;">{{ __('messages.preferences') }}</span></li>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row justify-content-center">
<div class="col-lg-8">

<form action="{{ route('preferences.update') }}" method="POST">
@csrf
@method('PUT')

{{-- ── THÈME ────────────────────────────────────────────────── --}}
<div class="card mb-4" style="border-radius:12px;border:1px solid #E5E7EB;">
    <div class="card-header d-flex align-items-center gap-2" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;border-radius:12px 12px 0 0;padding:14px 20px;">
        <i class="fas fa-palette" style="color:#0A4D8C;"></i>
        <span style="font-weight:600;font-size:14px;color:#111827;">{{ __('messages.theme') }}</span>
    </div>
    <div class="card-body" style="padding:20px;">
        <div class="d-flex gap-3 flex-wrap">
            <label class="pref-radio-card theme-card {{ $preference->theme === 'light' ? 'selected' : '' }}">
                <input type="radio" name="theme" value="light" {{ $preference->theme === 'light' ? 'checked' : '' }}>
                <div class="theme-preview light-preview">
                    <div class="preview-bar"></div>
                    <div class="preview-content"><div class="preview-line"></div><div class="preview-line short"></div></div>
                </div>
                <span class="lang-name"><i class="fas fa-sun text-warning me-1"></i>{{ __('messages.light_mode') }}</span>
                <i class="fas fa-check check-icon"></i>
            </label>
            <label class="pref-radio-card theme-card {{ $preference->theme === 'dark' ? 'selected' : '' }}">
                <input type="radio" name="theme" value="dark" {{ $preference->theme === 'dark' ? 'checked' : '' }}>
                <div class="theme-preview dark-preview">
                    <div class="preview-bar"></div>
                    <div class="preview-content"><div class="preview-line"></div><div class="preview-line short"></div></div>
                </div>
                <span class="lang-name"><i class="fas fa-moon text-secondary me-1"></i>{{ __('messages.dark_mode') }}</span>
                <i class="fas fa-check check-icon"></i>
            </label>
        </div>
    </div>
</div>

{{-- ── NOTIFICATIONS ────────────────────────────────────────── --}}
<div class="card mb-4" style="border-radius:12px;border:1px solid #E5E7EB;">
    <div class="card-header d-flex align-items-center gap-2" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;border-radius:12px 12px 0 0;padding:14px 20px;">
        <i class="fas fa-bell" style="color:#0A4D8C;"></i>
        <span style="font-weight:600;font-size:14px;color:#111827;">Notifications</span>
    </div>
    <div class="card-body" style="padding:20px;">
        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #F3F4F6;">
            <div>
                <div style="font-size:13.5px;font-weight:500;color:#111827;">Notifications par email</div>
                <div style="font-size:12px;color:#9CA3AF;">Recevoir les alertes importantes par email</div>
            </div>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="notifications_email" value="0">
                <input type="checkbox" name="notifications_email" value="1" id="notif_email"
                    class="form-check-input" style="width:42px;height:22px;cursor:pointer;"
                    {{ $preference->notifications_email ? 'checked' : '' }}>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between py-2 mt-2">
            <div>
                <div style="font-size:13.5px;font-weight:500;color:#111827;">Notifications système</div>
                <div style="font-size:12px;color:#9CA3AF;">Afficher les alertes dans l'interface</div>
            </div>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="notifications_systeme" value="0">
                <input type="checkbox" name="notifications_systeme" value="1" id="notif_systeme"
                    class="form-check-input" style="width:42px;height:22px;cursor:pointer;"
                    {{ $preference->notifications_systeme ? 'checked' : '' }}>
            </div>
        </div>
    </div>
</div>

{{-- ── AFFICHAGE ────────────────────────────────────────────── --}}
<div class="card mb-4" style="border-radius:12px;border:1px solid #E5E7EB;">
    <div class="card-header d-flex align-items-center gap-2" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;border-radius:12px 12px 0 0;padding:14px 20px;">
        <i class="fas fa-sliders-h" style="color:#0A4D8C;"></i>
        <span style="font-weight:600;font-size:14px;color:#111827;">Affichage</span>
    </div>
    <div class="card-body" style="padding:20px;">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" style="font-size:13px;font-weight:500;">Éléments par page</label>
                <select name="items_par_page" class="form-select form-select-sm">
                    @foreach([10, 15, 25, 50] as $nb)
                        <option value="{{ $nb }}" {{ $preference->items_par_page == $nb ? 'selected' : '' }}>
                            {{ $nb }} éléments
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:13px;font-weight:500;">Format de date</label>
                <select name="format_date" class="form-select form-select-sm">
                    <option value="d/m/Y" {{ $preference->format_date == 'd/m/Y' ? 'selected' : '' }}>31/03/2026 (JJ/MM/AAAA)</option>
                    <option value="Y-m-d" {{ $preference->format_date == 'Y-m-d' ? 'selected' : '' }}>2026-03-31 (ISO)</option>
                    <option value="m/d/Y" {{ $preference->format_date == 'm/d/Y' ? 'selected' : '' }}>03/31/2026 (US)</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- ── BOUTONS ──────────────────────────────────────────────── --}}
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary" style="background:#0A4D8C;border-color:#0A4D8C;padding:9px 20px;font-size:13.5px;">
        <i class="fas fa-save me-1"></i>{{ __('messages.save') }}
    </button>
    <a href="{{ url()->previous() }}" class="btn btn-light" style="border:1px solid #E5E7EB;padding:9px 20px;font-size:13.5px;">
        {{ __('messages.cancel') }}
    </a>
</div>

</form>
</div>
</div>

@endsection

@push('styles')
<style>
.pref-radio-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 10px;
    cursor: pointer;
    transition: all 150ms;
    min-width: 140px;
    background: #fff;
}
.pref-radio-card input[type=radio] { display: none; }
.pref-radio-card .flag { font-size: 20px; }
.pref-radio-card .lang-name { font-size: 13.5px; font-weight: 500; color: #374151; flex: 1; }
.pref-radio-card .check-icon { font-size: 11px; color: #10B981; opacity: 0; transition: opacity 150ms; }
.pref-radio-card:hover { border-color: #93C5FD; background: #F0F9FF; }
.pref-radio-card.selected { border-color: #0A4D8C; background: #EFF6FF; }
.pref-radio-card.selected .check-icon { opacity: 1; }
.pref-radio-card.selected .lang-name { color: #0A4D8C; font-weight: 600; }

/* Thème preview */
.theme-card { flex-direction: column; align-items: flex-start; min-width: 160px; }
.theme-preview { width: 100%; height: 60px; border-radius: 6px; overflow: hidden; margin-bottom: 8px; border: 1px solid #E5E7EB; }
.light-preview { background: #F9FAFB; }
.dark-preview { background: #1F2937; }
.preview-bar { height: 12px; background: #0A4D8C; margin-bottom: 4px; }
.dark-preview .preview-bar { background: #3B82F6; }
.preview-content { padding: 4px 6px; }
.preview-line { height: 5px; background: #D1D5DB; border-radius: 2px; margin-bottom: 3px; }
.dark-preview .preview-line { background: #4B5563; }
.preview-line.short { width: 60%; }
</style>
@endpush

@push('scripts')
<script>
// Mise à jour visuelle des radio cards
document.querySelectorAll('.pref-radio-card input[type=radio]').forEach(radio => {
    radio.addEventListener('change', function() {
        const group = this.name;
        document.querySelectorAll(`.pref-radio-card input[name="${group}"]`).forEach(r => {
            r.closest('.pref-radio-card').classList.toggle('selected', r.checked);
        });
    });
});
</script>
@endpush
