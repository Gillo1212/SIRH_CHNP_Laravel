{{--
    Barre de navigation interne du module Congés RH.
    À inclure en haut de chaque page du module.
    Usage : @include('rh.conges._nav', ['active' => 'index'])
--}}
@php
    $navItems = [
        ['route' => 'rh.conges.index',    'key' => 'index',    'icon' => 'fa-list-alt',       'label' => 'Toutes les demandes'],
        ['route' => 'rh.conges.pending',  'key' => 'pending',  'icon' => 'fa-user-check',     'label' => 'À approuver'],
        ['route' => 'rh.conges.en-cours', 'key' => 'en-cours', 'icon' => 'fa-umbrella-beach', 'label' => 'En congé actuellement'],
        ['route' => 'rh.conges.soldes',   'key' => 'soldes',   'icon' => 'fa-chart-bar',      'label' => 'Soldes & Reliquats'],
        ['route' => 'rh.conge-physique',  'key' => 'physique', 'icon' => 'fa-pen-to-square',  'label' => 'Saisie physique'],
    ];
    $active = $active ?? '';
@endphp

<div class="d-flex gap-1 mb-4 flex-wrap" style="background:var(--theme-panel-bg);border:1px solid var(--theme-border);border-radius:12px;padding:6px;">
    @foreach($navItems as $item)
        @php
            $isActive = $active === $item['key'];
        @endphp
        <a href="{{ route($item['route']) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:{{ $isActive ? '600' : '500' }};text-decoration:none;transition:all 160ms;
                  background:{{ $isActive ? '#0A4D8C' : 'transparent' }};
                  color:{{ $isActive ? '#fff' : 'var(--theme-text-muted)' }};"
           onmouseover="if('{{ $isActive }}' !== 'true') { this.style.background='var(--sirh-primary-hover)'; this.style.color='#0A4D8C'; }"
           onmouseout="if('{{ $isActive }}' !== 'true') { this.style.background='transparent'; this.style.color='var(--theme-text-muted)'; }">
            <i class="fas {{ $item['icon'] }}" style="font-size:12px;"></i>
            {{ $item['label'] }}
            @if($item['key'] === 'pending' && isset($pendingCount) && $pendingCount > 0)
                <span style="background:{{ $isActive ? 'rgba(255,255,255,.25)' : '#FEF3C7' }};color:{{ $isActive ? '#fff' : '#92400E' }};font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">
                    {{ $pendingCount }}
                </span>
            @endif
            @if($item['key'] === 'en-cours' && isset($enCoursCount) && $enCoursCount > 0)
                <span style="background:{{ $isActive ? 'rgba(255,255,255,.25)' : '#FEF3C7' }};color:{{ $isActive ? '#fff' : '#92400E' }};font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">
                    {{ $enCoursCount }}
                </span>
            @endif
        </a>
    @endforeach
</div>
