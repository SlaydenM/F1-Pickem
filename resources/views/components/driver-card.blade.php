@props([
    'driver',
    'size'      => 'md',
    'picked'    => false,
    'correct'   => false,
    'draggable' => false,
])

@php
$sizes = ['sm' => 120, 'md' => 150, 'lg' => 180];
$w = $sizes[$size] ?? 150;
$h = round($w * 2 / 3);
@endphp

<div
    class="flex-shrink-0 transition-all duration-50
        {{ $draggable ? 'cursor-grab active:cursor-grabbing hover:scale-105 hover:z-10' : '' }}
        {{ $picked    ? 'opacity-35 saturate-0' : '' }}"
    style="width:{{ $w }}px"
    @if($draggable) draggable="true" data-driver-id="{{ $driver->id }}" @endif
>
    <div class="relative overflow-hidden"
         style="width:{{ $w }}px;height:{{ $h }}px;border-radius:2px;
                background:#1e1e1e;border:1px solid rgba(255,255,255,0.08)">

        {{-- Driver image --}}
        <img src="{{ asset($driver->getPath()) }}"
             alt="{{ $driver->name }}"
             class="w-full h-full object-cover object-top driver-img"
             draggable="false"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">

        {{-- Text fallback shown when image fails --}}
        <div class="w-full h-full justify-between p-2"
             style="display:none;border-left:3px solid {{ $driver->primary_color }}">
            {{-- <span class="font-['JetBrains_Mono'] font-bold text-xs"
                  style="color:{{ $driver->primary_color }}">#{{ $driver->number }}</span> --}}
            <div>
                <div class="font-['Barlow_Condensed'] font-black italic text-white text-xl leading-tight">
                    {{ $driver->name }}
                </div>
                <div class="font-['Inter'] text-sm text-white/50 uppercase tracking-wider">
                    {{ $driver->team }}
                </div>
            </div>
        </div>

        {{-- Team colour top-accent bar --}}
        <div class="absolute inset-x-0 top-0 h-[3px]"
             style="background:{{ $driver->primary_color }}"></div>
    </div>

    {{-- Green "correct" bar below card --}}
    <div class="transition-all duration-300"
         style="height:4px;border-radius:0 0 2px 2px;margin-top:2px;
                background:{{ $correct ? '#22c55e' : 'transparent' }};
                box-shadow:{{ $correct ? '0 0 8px rgba(34,197,94,0.6)' : 'none' }}">
    </div>
</div>
