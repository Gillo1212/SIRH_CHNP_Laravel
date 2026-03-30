{{--
    Composant <x-ged-file-icon format="pdf" size="36" />
    Retourne une icône SVG inline représentant le format de fichier.
    @prop format  : extension (pdf, docx, xlsx, jpg…)
    @prop size    : taille en px (défaut 36)
--}}
@props(['format' => '', 'size' => 36])
@php $ext = strtolower(trim($format ?? '', '.')); @endphp

@if($ext === 'pdf')
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="36" height="36" rx="8" fill="#FEF2F2"/>
    <path d="M10 8C10 6.9 10.9 6 12 6H21L28 13V29C28 30.1 27.1 31 26 31H12C10.9 31 10 30.1 10 29V8Z" fill="#DC2626" fill-opacity=".12" stroke="#DC2626" stroke-width="1.5"/>
    <path d="M21 6V13H28" stroke="#DC2626" stroke-width="1.5" stroke-linejoin="round"/>
    <text x="18" y="25" text-anchor="middle" fill="#DC2626" font-size="6.5" font-weight="700" font-family="Arial,sans-serif">PDF</text>
</svg>

@elseif(in_array($ext, ['doc', 'docx']))
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="36" height="36" rx="8" fill="#EFF6FF"/>
    <path d="M10 8C10 6.9 10.9 6 12 6H21L28 13V29C28 30.1 27.1 31 26 31H12C10.9 31 10 30.1 10 29V8Z" fill="#1D4ED8" fill-opacity=".1" stroke="#1D4ED8" stroke-width="1.5"/>
    <path d="M21 6V13H28" stroke="#1D4ED8" stroke-width="1.5" stroke-linejoin="round"/>
    <text x="18.5" y="26" text-anchor="middle" fill="#1D4ED8" font-size="11" font-weight="800" font-family="Arial,sans-serif">W</text>
</svg>

@elseif(in_array($ext, ['xls', 'xlsx']))
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="36" height="36" rx="8" fill="#ECFDF5"/>
    <path d="M10 8C10 6.9 10.9 6 12 6H21L28 13V29C28 30.1 27.1 31 26 31H12C10.9 31 10 30.1 10 29V8Z" fill="#059669" fill-opacity=".1" stroke="#059669" stroke-width="1.5"/>
    <path d="M21 6V13H28" stroke="#059669" stroke-width="1.5" stroke-linejoin="round"/>
    <text x="18.5" y="26" text-anchor="middle" fill="#059669" font-size="11" font-weight="800" font-family="Arial,sans-serif">X</text>
</svg>

@elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']))
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="36" height="36" rx="8" fill="#F5F3FF"/>
    <path d="M10 8C10 6.9 10.9 6 12 6H21L28 13V29C28 30.1 27.1 31 26 31H12C10.9 31 10 30.1 10 29V8Z" fill="#7C3AED" fill-opacity=".1" stroke="#7C3AED" stroke-width="1.5"/>
    <path d="M21 6V13H28" stroke="#7C3AED" stroke-width="1.5" stroke-linejoin="round"/>
    <path d="M13 27L17 21L20 24.5L22.5 22L27 27" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="22" cy="18" r="1.5" fill="#7C3AED"/>
</svg>

@elseif(in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz']))
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="36" height="36" rx="8" fill="#F3F4F6"/>
    <path d="M10 8C10 6.9 10.9 6 12 6H21L28 13V29C28 30.1 27.1 31 26 31H12C10.9 31 10 30.1 10 29V8Z" fill="#6B7280" fill-opacity=".1" stroke="#6B7280" stroke-width="1.5"/>
    <path d="M21 6V13H28" stroke="#6B7280" stroke-width="1.5" stroke-linejoin="round"/>
    <rect x="15.5" y="15" width="5" height="3.5" rx="1" fill="#6B7280"/>
    <line x1="16.5" y1="18.5" x2="16.5" y2="28" stroke="#6B7280" stroke-width="1.5"/>
    <line x1="19.5" y1="18.5" x2="19.5" y2="28" stroke="#6B7280" stroke-width="1.5"/>
    <line x1="16.5" y1="21.5" x2="19.5" y2="21.5" stroke="#6B7280" stroke-width="1"/>
    <line x1="16.5" y1="24.5" x2="19.5" y2="24.5" stroke="#6B7280" stroke-width="1"/>
</svg>

@else
{{-- Document générique --}}
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="36" height="36" rx="8" fill="#F9FAFB"/>
    <path d="M10 8C10 6.9 10.9 6 12 6H21L28 13V29C28 30.1 27.1 31 26 31H12C10.9 31 10 30.1 10 29V8Z" fill="#9CA3AF" fill-opacity=".15" stroke="#9CA3AF" stroke-width="1.5"/>
    <path d="M21 6V13H28" stroke="#9CA3AF" stroke-width="1.5" stroke-linejoin="round"/>
    <line x1="13" y1="19" x2="23" y2="19" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
    <line x1="13" y1="22.5" x2="23" y2="22.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
    <line x1="13" y1="26" x2="19" y2="26" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
</svg>
@endif
