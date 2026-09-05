@props(['myRank' => null, 'myScore' => 0, 'round' => 0])

<div class="bg-[#1c1c1c] border border-white/[0.07] p-5" style="border-radius:2px">
    <div class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-xs text-[#BBBBBB] mb-3">
        My Season
    </div>
    <div class="grid grid-cols-3 gap-2">
        @foreach([
            ['val' => number_format($myScore, 1), 'label' => 'TOTAL PTS'],
            ['val' => $myRank ? '#' . $myRank : '—', 'label' => 'RANK'],
            ['val' => max(0, $round - 1),             'label' => 'RACES'],
        ] as $stat)
            <div class="bg-[#141414] p-3 text-center" style="border-radius:2px">
                <div class="font-['Barlow_Condensed'] font-black italic text-[#E10600] text-4xl leading-none">
                    {{ $stat['val'] }}
                </div>
                <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[9px] tracking-widest mt-1">
                    {{ $stat['label'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
