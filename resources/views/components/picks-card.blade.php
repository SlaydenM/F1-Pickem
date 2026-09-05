@props(['pick', 'correct' => [0, 0, 0], 'type' => 'countdown'])

@php
    $slots = [
        ['driver' => $pick->d1, 'label' => '1ST',  'pts' => 7, 'color' => '#FFD700', 'hit' => $correct[0] ?? 0],
        ['driver' => $pick->d2, 'label' => '10TH', 'pts' => 5, 'color' => '#E10600', 'hit' => $correct[1] ?? 0],
        ['driver' => $pick->d3, 'label' => 'LAST', 'pts' => 3, 'color' => '#555555', 'hit' => $correct[2] ?? 0],
    ];

    $bonusFloat = (float) ($pick->bonus ?? 1.0);
    if ($bonusFloat >= 1.50)     { $bLabel = 'EARLIEST';    $bColor = '#22c55e'; $bDisp = '+50%'; }
    elseif ($bonusFloat >= 1.25) { $bLabel = 'EARLY';       $bColor = '#86efac'; $bDisp = '+25%'; }
    elseif ($bonusFloat >= 1.10) { $bLabel = 'BONUS';       $bColor = '#fbbf24'; $bDisp = '+10%'; }
    elseif ($bonusFloat >= 1.00) { $bLabel = 'NO BONUS';    $bColor = '#888888'; $bDisp = '+0%';  }
    else                         { $bLabel = 'LATE';        $bColor = '#E10600'; $bDisp = '-50%'; }


    $totalScore  = round((float) ($pick->score ?? 0), 3);
@endphp

<div class="relative bg-[#1c1c1c] border border-white/[0.07] overflow-hidden"
     style="clip-path:polygon(8px 0%,100% 0%,calc(100% - 8px) 100%,0% 100%)">
    <div class="flex gap-4 flex-wrap px-5 py-4">
        {{-- Driver cards --}}
        <div class="flex gap-3 flex-col">
            <div class="font-['Barlow_Condensed'] font-bold uppercase text-[#BBBBBB] text-sm tracking-wider mb-3">
                {{ $pick->user->name }}
            </div>
            <div class="flex gap-3 flex-row">
                @foreach($slots as $slot)
                    <div class="flex flex-col gap-1.5">
                        <span class="font-['JetBrains_Mono'] text-[10px] tracking-wider"
                            style="color:{{ $slot['color'] }}">{{ $slot['label'] }}</span>

                        @if($slot['driver'])
                            <x-driver-card
                                :driver="$slot['driver']"
                                size="sm"
                                :correct="$type === 'results' && $slot['hit'] == 1"
                            />
                        @else
                            <div class="bg-[#2a2a2a] flex items-center justify-center font-['Inter'] text-[10px] text-[#BBBBBB]"
                                style="width:120px;height:80px;border-radius:2px">—</div>
                        @endif

                        {{-- Points Breakdown --}}
                        @if($type === 'results')
                            @php
                                $earned  = $slot['hit'] == 1 ? $slot['pts'] : 0;
                                $boosted = $earned > 0 ? round($earned * $bonusFloat, 3) : 0;
                            @endphp
                            @if($earned > 0)
                                <div class="flex flex-col items-center gap-0.5 mt-0.5">
                                    <div class="flex items-baseline gap-1 font-['JetBrains_Mono'] tabular-nums">
                                        <span class="text-[10px] text-green-400">+{{ $earned }}</span>
                                        <span class="text-[7px] text-[#BBBBBB]">PTS</span>
                                        <span class="text-[9px] font-bold" style="color:{{ $bColor }}">{{ $bDisp }}</span>
                                    </div>
                                    <div class="flex items-baseline font-['JetBrains_Mono'] tabular-nums">
                                        <span class="text-[11px] font-bold text-green-300">+{{ $boosted }}</span>
                                        <span class="text-[7px] text-[#BBBBBB] pl-1">PTS</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-baseline justify-center font-['JetBrains_Mono'] tabular-nums mt-0.5">
                                    <span class="text-[10px] text-[#BBBBBB]">0</span>
                                    <span class="text-[7px] text-[#BBBBBB] pl-1">PTS</span>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submission info + round total --}}
        <div class="flex flex-col justify-between flex-1 pl-3 border-l border-white/[0.06]">
            <div class="flex flex-col gap-1">
                <span class="font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] tracking-wider uppercase">
                    Submitted on:
                </span>
                <span class="font-['JetBrains_Mono'] text-[11px] text-white tracking-wide">
                    {{ $pick->created_at?->format('M j, Y') ?? '—' }}
                </span>
                <span class="font-['JetBrains_Mono'] text-[11px] text-white tracking-wide">
                    {{ $pick->created_at?->format('g:i A') ?? '' }}
                </span>
                <span class="font-['Barlow_Condensed'] font-black italic text-sm uppercase tracking-widest"
                        style="color:{{ $bColor }}">{{ $bLabel }}</span>
            </div>

            @if($type === 'results')
                <div class="flex flex-col gap-1 mt-2">
                    <span class="font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] tracking-wider">
                        ROUND TOTAL:
                    </span>
                    <div>
                        <span class="font-['Barlow_Condensed'] font-black italic text-base leading-none"
                                style="color:{{ $totalScore > 0 ? '#E10600' : '#BBBBBB' }}">
                            {{ $totalScore > 0 ? '+' . $totalScore : '0' }}
                        </span>
                        <span class="text-[7px] text-[#BBBBBB] font-['JetBrains_Mono'] tracking-wider pl-1">PTS</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
