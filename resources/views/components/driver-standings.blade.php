@props(['winners'])

<div>
    <div class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-sm text-[#BBBBBB] mb-3 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
             fill="none" stroke="#E10600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="8 18 12 22 16 18"/>
            <polyline points="8 6 12 2 16 6"/>
            <line x1="12" y1="2" x2="12" y2="22"/>
        </svg>
        Official Classification
    </div>

    @if($winners->isEmpty())
        <div class="font-['Inter'] text-[#BBBBBB] text-sm py-4">Results not yet available.</div>
    @else
        @php $total = $winners->count(); @endphp
        <div class="space-y-[2px]">
            @foreach($winners as $winner)
                @php
                    $pos    = $winner->position;
                    $driver = $winner->driver;
                    $isKey  = in_array($pos, [1, 10, $total]);
                @endphp
                <div class="flex items-center gap-3 px-3 py-2 {{ $isKey ? 'bg-[#252525]' : 'bg-[#181818]' }}"
                     style="clip-path:polygon(6px 0%,100% 0%,calc(100% - 6px) 100%,0% 100%)">
                    <span class="font-['Barlow_Condensed'] font-black italic text-lg w-7 text-right leading-none flex-shrink-0 text-[#BBBBBB]">
                        {{ $pos }}
                    </span>
                    @if($driver)
                        <div class="w-0.5 h-5 flex-shrink-0" style="background:{{ $driver->primary_color }}"></div>
                        <span class="font-['Barlow_Condensed'] font-bold uppercase text-sm flex-1 text-[#BBBBBB]">
                            {{ $driver->name }}
                        </span>
                        <span class="font-['Inter'] text-[#BBBBBB] text-xs">{{ $driver->team }}</span>
                    @else
                        <span class="font-['Barlow_Condensed'] font-bold uppercase text-sm flex-1 text-[#BBBBBB]">—</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
