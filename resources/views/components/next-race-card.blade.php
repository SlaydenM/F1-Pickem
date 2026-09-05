@props(['race', 'type' => 'dashboard'])

@if($race)
    @php
        $roundNum  = $race->session_key % 1000;
        $location  = $race->name;
        $dateStr   = \Carbon\Carbon::parse($race->date_start)->format('M j, Y');
        $timestamp = \Carbon\Carbon::parse($race->date_start)->timestamp;
        $counterId = 'countdown-' . $type . '-' . $roundNum;
    @endphp

    {{-- ── DASHBOARD type (home page right panel) ─────────────────────── --}}
    @if($type === 'dashboard')
        <div class="bg-[#1c1c1c] border border-white/[0.07] p-5" style="border-radius:2px">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                     fill="none" stroke="#E10600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-xs text-[#BBBBBB]">
                    Next Race
                </span>
            </div>
            <div class="font-['Barlow_Condensed'] font-black italic text-white text-lg uppercase leading-tight mb-0.5">
                {{ $race->name }}
            </div>
            <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[10px] mb-4">
                {{ $location }} · {{ $dateStr }}
            </div>
            <div class="grid grid-cols-4 gap-2" id="{{ $counterId }}">
                @foreach(['DAYS','HRS','MIN','SEC'] as $unit)
                <div class="bg-[#141414] p-2.5 text-center" style="border-radius:2px">
                    <div class="font-['Barlow_Condensed'] font-black text-2xl text-white leading-none tabular-nums"
                         data-unit="{{ strtolower($unit) }}">00</div>
                    <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[9px] tracking-widest mt-1">{{ $unit }}</div>
                </div>
                @endforeach
            </div>
        </div>

    {{-- ── COUNTDOWN type (next-race page banner) ───────────────────────── --}}
    @elseif($type === 'countdown')
        <div class="relative bg-[#1c1c1c] border border-white/[0.07] px-6 py-4 mb-6 flex items-center gap-6 overflow-hidden"
             style="clip-path:polygon(8px 0%,100% 0%,calc(100% - 8px) 100%,0% 100%)">
            <div>
                <div class="font-['JetBrains_Mono'] text-[#E10600] text-[10px] tracking-widest uppercase mb-0.5">
                    Round {{ $roundNum }}
                </div>
                <div class="font-['Barlow_Condensed'] font-black italic text-white text-2xl uppercase">
                    {{ $race->name }}
                </div>
                <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-xs">
                    {{ $location }} · {{ $dateStr }}
                </div>
            </div>
            <div class="ml-auto flex gap-4" id="{{ $counterId }}">
                @foreach(['DAYS','HRS','MIN'] as $unit)
                <div class="text-center">
                    <div class="font-['Barlow_Condensed'] font-black text-2xl text-white leading-none tabular-nums"
                         data-unit="{{ strtolower($unit) }}">00</div>
                    <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[9px] tracking-widest">{{ $unit }}</div>
                </div>
                @endforeach
            </div>
        </div>

    {{-- ── RESULTS type (past-races page banner) ───────────────────────── --}}
    @elseif($type === 'results')
        <div class="relative bg-[#1c1c1c] border border-white/[0.07] px-6 py-4 mb-6 flex items-center justify-between overflow-hidden"
             style="clip-path:polygon(8px 0%,100% 0%,calc(100% - 8px) 100%,0% 100%)">
            <div>
                <div class="font-['JetBrains_Mono'] text-[#E10600] text-[10px] tracking-widest uppercase mb-0.5">
                    Round {{ $roundNum }}
                </div>
                <div class="font-['Barlow_Condensed'] font-black italic text-white text-2xl uppercase">
                    {{ $race->name }}
                </div>
                <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-xs">{{ $dateStr }}</div>
            </div>
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                     fill="none" stroke="#E10600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <span class="font-['Barlow_Condensed'] font-bold uppercase text-sm text-[#BBBBBB] tracking-widest">
                    Official Results
                </span>
            </div>
        </div>
    @endif

    {{-- Countdown JS (injected for dashboard and countdown types) --}}
    @if(in_array($type, ['dashboard', 'countdown']))
        @push('scripts')
        <script>
        (function () {
            var target = {{ $timestamp }} * 1000;
            var id     = '{{ $counterId }}';
            function pad(n) { return String(n).padStart(2, '0'); }
            function tick() {
                var diff = target - Date.now();
                var d = 0, h = 0, m = 0, s = 0;
                if (diff > 0) {
                    d = Math.floor(diff / 86400000);
                    h = Math.floor((diff % 86400000) / 3600000);
                    m = Math.floor((diff % 3600000) / 60000);
                    s = Math.floor((diff % 60000) / 1000);
                }
                var container = document.getElementById(id);
                if (!container) return;
                var map = { days: d, hrs: h, min: m, sec: s };
                container.querySelectorAll('[data-unit]').forEach(function (el) {
                    if (map[el.dataset.unit] !== undefined) el.textContent = pad(map[el.dataset.unit]);
                });
            }
            tick();
            setInterval(tick, 1000);
        })();
        </script>
        @endpush
    @endif

@else
    {{-- No race scheduled --}}
    <div class="bg-[#1c1c1c] border border-white/[0.07] p-5 text-[#BBBBBB] text-sm font-['Inter']"
         style="border-radius:2px">
        No race currently scheduled.
    </div>
@endif
