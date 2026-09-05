@props(['picks', 'type' => 'countdown', 'correctBets' => [], 'currentPick' => null])

<div>
    <div class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-sm text-[#BBBBBB] mb-3 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
             fill="none" stroke="#E10600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        {{ $type === 'results' ? 'Player Picks' : "Other Players' Picks" }}
    </div>

    @if($picks->isEmpty())
        <div class="font-['Inter'] text-[#BBBBBB] text-sm py-4">No picks submitted yet.</div>
    @else
        <div class="space-y-3">
            @foreach($picks as $i => $pick)
                {{-- In countdown mode, skip the current user's own pick (shown separately above) --}}
                @if($type === 'countdown' && $currentPick && $pick->user_id === auth()->id())
                    @continue
                @endif

                {{-- Mobile View: Shows by default, hides on medium (md) screens and larger --}}
                <div class="block md:hidden">
                    <x-picks-card-mobile 
                        :pick="$pick" 
                        :correct="$correctBets[$i] ?? [0, 0, 0]"
                        :type="$type"
                    />
                </div>

                {{-- Desktop View: Hidden by default, shows on medium (md) screens and larger --}}
                <div class="hidden md:block">
                    <x-picks-card 
                        :pick="$pick" 
                        :correct="$correctBets[$i] ?? [0, 0, 0]"
                        :type="$type"
                    />
                </div>
            @endforeach
        </div>
    @endif
</div>
