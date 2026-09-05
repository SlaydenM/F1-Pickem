@props(['players', 'rankChanges', 'year', 'round'])

<section>
    {{-- Section header --}}
    <div class="flex items-center gap-4 mb-5">
            <div class="w-1 h-10 bg-[#E10600]"></div>
        <div>
            <h1 class="font-['Barlow_Condensed'] font-black italic text-4xl text-white tracking-tight uppercase leading-none">
                Season Standings
            </h1>
            <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[10px] tracking-wider mt-0.5">
                {{ $year }} · Round {{ max(0, $round - 1) }} of 24 complete
            </div>
        </div>
    </div>

    {{-- Column headers --}}
    <div class="flex items-center gap-5 px-4 pb-2">
        <div class="w-8 font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] uppercase tracking-widest text-center">#</div>
        <div class="flex-1 font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] uppercase tracking-widest">Player</div>
        <div class="w-8"></div>
        <div class="font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] uppercase tracking-widest w-24 text-right">Points</div>
    </div>

    {{-- Rows --}}
    <div class="space-y-[3px]">
        @foreach($players as $index => $player)
            @php
                $rank   = $index + 1;
                $isMe   = $player->id === auth()->id();
                $bg     = $isMe ? '#000000' : ($index % 2 === 0 ? '#232323' : '#1c1c1c');
                $border = $isMe ? 'rgba(225,6,0,0.6)' : 'rgba(255,255,255,0.06)';
                $trend  = $rankChanges[$player->id] ?? 0;
                // dd(count($rankChanges), $rankChanges, $player->id, $trend);

                $rankColor = match($rank) {
                    1       => '#FFD700',
                    2       => '#C0C0C0',
                    3       => '#CD7F32',
                    default => '#BBBBBB',
                };
            @endphp
            <div class="flex items-center gap-5 px-4 py-3 justify-between"
                 style="clip-path:polygon(16px 0%,100% 0%,calc(100% - 16px) 100%,0% 100%);
                        background:{{ $bg }};
                        border-bottom:2px solid {{ $border }}">

                {{-- Rank number --}}
                <div class="font-['Barlow_Condensed'] font-black italic text-3xl w-8 text-center leading-none flex-shrink-0"
                     style="color:{{ $rankColor }}">{{ $rank }}</div>

                {{-- Player name --}}
                <div class="flex min-w-0 text-right items-center">
                    <span class="font-['Barlow_Condensed'] font-bold uppercase tracking-wide text-base {{ $isMe ? 'text-white' : 'text-[#BBBBBB]' }}">
                        {{ $player->name }}
                    </span>
                    @if($isMe)
                        <span class="ml-2 font-['JetBrains_Mono'] text-[10px] text-[#E10600] tracking-widest">YOU</span>
                    @endif
                </div>

                {{-- Trend indicator --}}
                <div class="w-8 text-center flex-shrink-0">
                    @if($trend > 0)
                        <span class="font-['JetBrains_Mono'] text-xs text-green-400">▲{{ $trend }}</span>
                    @elseif($trend < 0)
                        <span class="font-['JetBrains_Mono'] text-xs text-red-400">▼{{ abs($trend) }}</span>
                    @endif
                </div>

                {{-- Points --}}
                <div class="font-['JetBrains_Mono'] font-bold text-sm flex-shrink-0 text-right flex items-baseline w-12 mr-5">
                    <span style="color:{{ $isMe ? '#E10600' : '#BBBBBB' }}">
                        {{ number_format($player->total_score, 1) }}
                    </span>
                    <span class="text-[9px] text-[#BBBBBB] tracking-wider pl-1">PTS</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
