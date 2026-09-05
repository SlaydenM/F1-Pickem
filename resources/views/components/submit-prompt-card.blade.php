@props(['submitted' => false, 'race' => null])

@php $raceName = $race?->name ?? 'the next race'; @endphp

@if($submitted)
    <div class="bg-[#0d1a0d] border border-green-900/40 p-5" style="border-radius:2px">
        <div class="flex items-center gap-2 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span class="font-['Barlow_Condensed'] font-black italic text-white text-base uppercase">
                Picks Submitted
            </span>
        </div>
        <div class="font-['Inter'] text-green-800 text-sm">
            Your predictions for {{ $raceName }} are locked in.
        </div>
    </div>
@else
    <div class="relative border border-[#E10600]/20 p-5 overflow-hidden"
         style="background:#160500;border-radius:2px">
        <div class="absolute left-0 inset-y-0 w-[3px] bg-[#E10600]"></div>
        <div class="pl-2">
            <div class="font-['Barlow_Condensed'] font-black italic text-white text-base uppercase mb-1">
                Picks Not Submitted
            </div>
            <div class="font-['Inter'] text-[#BBBBBB] text-sm mb-3">
                Lock in your {{ $raceName }} predictions before race day.
            </div>
            <a href="{{ route('next-race.submit') }}"
               class="inline-flex items-center gap-2 font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-xs text-[#E10600]">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                     fill="none" stroke="#E10600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/>
                    <polyline points="16 17 22 17 22 11"/>
                </svg>
                Head to Next Race →
            </a>
        </div>
    </div>
@endif
