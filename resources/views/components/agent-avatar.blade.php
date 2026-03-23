@props(['agent', 'size' => 40])

@php
    $hasPhoto = $agent->photo && Storage::disk('public')->exists($agent->photo);
    $initials = strtoupper(
        substr($agent->prenom ?? 'A', 0, 1) . substr($agent->nom ?? 'G', 0, 1)
    );
    $colors = ['#0A4D8C', '#1565C0', '#10B981', '#F59E0B', '#7C3AED', '#DB2777', '#0891B2'];
    $bgColor = $colors[ord($initials[0]) % count($colors)];
    $fontSize = round($size * 0.35);
@endphp

@if($hasPhoto)
    <img src="{{ Storage::url($agent->photo) }}"
         alt="{{ $agent->prenom }} {{ $agent->nom }}"
         class="rounded-circle"
         style="width:{{ $size }}px;height:{{ $size }}px;object-fit:cover;flex-shrink:0;">
@else
    <div class="rounded-circle d-flex align-items-center justify-content-center"
         style="width:{{ $size }}px;height:{{ $size }}px;background:{{ $bgColor }};color:#fff;font-size:{{ $fontSize }}px;font-weight:700;flex-shrink:0;user-select:none;">
        {{ $initials }}
    </div>
@endif
