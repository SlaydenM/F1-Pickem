@props(['pick', 'correct' => [0, 0, 0], 'type' => 'countdown'])

@php
    $slots = [
        ['driver' => $pick->d1, 'label' => '1ST',  'pts' => 7, 'color' => '#FFD700', 'hit' => $correct[0] ?? 0],
        ['driver' => $pick->d2, 'label' => '10TH', 'pts' => 5, 'color' => '#E10600', 'hit' => $correct[1] ?? 0],
        ['driver' => $pick->d3, 'label' => 'LAST', 'pts' => 3, 'color' => '#555555', 'hit' => $correct[2] ?? 0],
    ];

    $bonusFloat = (float) ($pick->bonus ?? 1.0);
    if ($bonusFloat >= 1.50)     { $bLabel = 'EARLIEST'; $bColor = '#4ade80'; $bDisp = '+50%'; }
    elseif ($bonusFloat >= 1.25) { $bLabel = 'EARLY';    $bColor = '#86efac'; $bDisp = '+25%'; }
    elseif ($bonusFloat >= 1.10) { $bLabel = 'BONUS';    $bColor = '#4ade80'; $bDisp = '+10%'; }
    elseif ($bonusFloat >= 1.00) { $bLabel = 'NO BONUS'; $bColor = '#888888'; $bDisp = '+0%';  }
    else                         { $bLabel = 'LATE';     $bColor = '#E10600'; $bDisp = '-50%'; }

    $totalScore  = round((float) ($pick->score ?? 0), 3);
    $date = $pick->created_at ? $pick->created_at->format('M j, g:i A') : '—';
@endphp

<div class="grid w-full mb-4 px-2">
    
    {{-- Sibling 1: Polygon Background --}}
    {{-- Occupies the exact same grid space as the content, allowing natural overlap --}}
    <div class="col-start-1 row-start-1 bg-[#1c1c1c]"
         style="clip-path:polygon(12px 0%, 100% 0%, calc(100% - 12px) 100%, 0% 100%);">
    </div>

    {{-- Sibling 2: Content Container --}}
    <div class="col-start-1 row-start-1 flex flex-col py-2 px-5 z-10">
        
        {{-- Header Row --}}
        <div class="flex justify-between items-end mb-2">
            <span class="font-['Barlow_Condensed'] font-bold uppercase text-[#BBBBBB] text-xl tracking-wide">
                {{ $pick->user->name }}
            </span>
            <span class="font-['JetBrains_Mono'] text-[#BBBBBB] text-lg mt-1">
                {{ $date }}
            </span>
        </div>

        {{-- Grid for Labels, Cards, and Points --}}
        <div class="flex flex-col">
            {{-- Labels --}}
            <div class="grid grid-cols-3 gap-1 mb-1">
                @foreach($slots as $slot)
                    <div class="text-center font-['JetBrains_Mono'] text-sm tracking-wider uppercase" 
                         style="color:{{ $slot['color'] }}">
                        {{ $slot['label'] }}
                    </div>
                @endforeach
            </div>

            {{-- Driver Cards (Overflow Row) --}}
            {{-- Negative margins pull the cards outside the container's padding to break the polygon bounds --}}
            <div class="grid grid-cols-3 gap-1 -mx-7">
                @foreach($slots as $slot)
                    <div class="flex flex-col">
                        <div class="flex justify-center">
                            @if($slot['driver'])
                                <x-driver-card
                                    :driver="$slot['driver']"
                                    size="sm"
                                    :correct="$type === 'results' && $slot['hit'] == 1"
                                />
                            @else
                                <div class="bg-[#2a2a2a] w-full flex items-center justify-center font-['Inter'] text-[10px] text-[#BBBBBB]"
                                    style="height: 120px; border-radius: 2px;">—</div>
                            @endif
                        </div>

                        {{-- Points Breakdown --}}
                        @if($type === 'results')
                            @php
                                $earned  = $slot['hit'] == 1 ? $slot['pts'] : 0;
                                $boosted = $earned > 0 ? round($earned * $bonusFloat, 3) : 0;
                            @endphp
                            @if($earned > 0)
                                <div class="flex flex-col items-center gap-0.5 mt-0.5">
                                    @if ($bLabel != "NO BONUS")
                                        <div class="flex items-baseline gap-1 font-['JetBrains_Mono'] tabular-nums">
                                            <span class="text-xl text-green-400">+{{ $earned }}</span>
                                            <span class="text-[10px] text-[#BBBBBB]">PTS</span>
                                            <span class="text-[20px] font-bold" style="color:{{ $bColor }}">{{ $bDisp }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-baseline font-['JetBrains_Mono'] tabular-nums">
                                        <span class="text-xl font-bold text-green-300">+{{ $boosted }}</span>
                                        <span class="text-[10px] text-[#BBBBBB] pl-1">PTS</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-baseline justify-center font-['JetBrains_Mono'] tabular-nums mt-0.5">
                                    <span class="text-xl text-[#BBBBBB]">0</span>
                                    <span class="text-[10px] text-[#BBBBBB] pl-1">PTS</span>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-between items-center mt-auto border-t border-black/10 pt-2">
            <span class="text-md font-['JetBrains_Mono'] tracking-wide" style="color:{{ $bColor }}">
                {{ $bLabel }} @if ($bLabel != "NO BONUS") ({{ $bDisp }}) @endif
            </span>
            <span class="text-[#d1d1d1] text-md font-['JetBrains_Mono']">
                TOTAL: 
                <span class="text-lg font-['Barlow_Condensed'] font-black italic text-base leading-none"
                        style="color:{{ $totalScore > 0 ? '#E10600' : '#BBBBBB' }}">
                    {{ $totalScore > 0 ? '+' . $totalScore : '0' }}
                </span>
                <span class="text-[10px] text-[#BBBBBB] font-['JetBrains_Mono'] tracking-wider pl-1">PTS</span>
            </span>
        </div>
        
    </div>
</div>