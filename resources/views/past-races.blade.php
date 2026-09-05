@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 sm:py-8">

        {{-- Page header + year dropdown md:--}}
        <div class="flex flex-col gap-3 mb-6 flex-row items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-1 h-10 bg-[#E10600]"></div>
                <h1 class="font-['Barlow_Condensed'] font-black italic text-3xl text-white tracking-tight uppercase sm:text-4xl">
                    Past Races
                </h1>
            </div>

            {{-- Year selector md:--}}
            <div class="relative ml-auto" id="year-dropdown">
                <button onclick="document.getElementById('year-menu').classList.toggle('hidden')"
                        class="flex items-center gap-3 bg-[#232323] px-10 py-2
                               font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-sm text-white
                               hover:border-white/20 transition-all cursor-pointer"
                        style="clip-path:polygon(10px 0%, calc(100% - 10px) 0%, 100% 50%, calc(100% - 10px) 100%, 10px 100%, 0% 50%)">
                    {{ $year }} Season
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div id="year-menu" class="hidden absolute right-0 top-full mt-1 bg-[#232323] border border-white/[0.08] z-20 min-w-full">
                    @foreach($years as $y)
                        <a href="{{ route('past-races', ['year' => $y]) }}"
                           class="block w-full text-left px-5 py-3 font-['Barlow_Condensed'] font-bold uppercase
                                  tracking-widest text-sm transition-colors
                                  {{ $y == $year ? 'text-[#E10600] bg-[#E10600]/10' : 'text-[#BBBBBB] hover:text-white hover:bg-[#2a2a2a]' }}">
                            {{ $y }} Season
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Round tabs --}}
        @if($racesForYear->isNotEmpty())
            <div class="flex gap-2 flex-wrap mb-6">
                @foreach($racesForYear as $r)
                    @php $rnd = $r->session_key % 1000; @endphp
                    <a href="{{ route('past-races', ['sessionKey' => $r->session_key]) }}"
                       class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-xs px-10 py-2 transition-all duration-150
                              {{ $r->session_key == $sessionKey
                                    ? 'bg-[#E10600] text-white'
                                    : 'bg-[#232323] text-[#BBBBBB] hover:text-white hover:bg-[#2a2a2a]' }}"
                       style="clip-path:polygon(0% 0%,calc(100% - 10px) 0%,100% 50%,calc(100% - 10px) 100%, 0% 100%, 10px 50%)">
                        R{{ $rnd }} · {{ Str::before($r->name, ' Grand Prix') ?: $r->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Race banner --}}
        @if($race)
            <x-next-race-card type="results" :race="$race" />

            {{-- Two-column layout --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- Left: Player predictions --}}
                <x-picks-list
                    type="results"
                    :picks="$picks"
                    :correctBets="$correctBets"
                />

                {{-- Right: Official classification --}}
                <x-driver-standings :winners="$winners" />
            </div>

        @elseif($sessionKey)
            <div class="font-['Inter'] text-[#BBBBBB] text-sm py-8">
                Race data not yet available for this round.
            </div>
        @else
            <div class="font-['Inter'] text-[#BBBBBB] text-sm py-8">
                No completed races found.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Close year dropdown on outside click
document.addEventListener('click', function(e) {
    var dd = document.getElementById('year-dropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('year-menu')?.classList.add('hidden');
    }
});
</script>
@endpush
