@props(['status', 'sessionKey', 'selectedSessionKey', 'picks', 'correctBets', 'lastPlace', 'winners', 'year'])

<div id="results-box" class="rounded-3xl border border-slate-700/70 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/60">
    @if ($status === 'unpicked' && $selectedSessionKey === $sessionKey)
        <h1 class="text-2xl font-semibold text-white">Place Picks</h1>
        <form action="{{ route('picks.index') }}" method="get" class="mt-4 rounded-2xl border border-slate-700/70 bg-slate-800/70 p-5">
            <p class="text-slate-300">Submit your picks before viewing results</p>
            <input type="submit" class="mt-4 cursor-pointer rounded-full bg-sky-500 px-5 py-2 font-semibold text-white transition hover:bg-sky-400" value="Submit Picks!" />
            <input type="hidden" name="sessionKey" value="{{ $selectedSessionKey }}" />
        </form>
    @else
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div id="weekly-box" class="rounded-2xl border border-slate-700/70 bg-slate-800/70 p-5">
                <h1 class="text-2xl font-semibold text-white">Weekly Picks ({{ $picks->count() }})</h1>
                <div class="mt-6 flex flex-wrap gap-4">
                    @foreach ($picks as $pickIndex => $pick)
                        <x-picks.bettor-box
                            :pick="$pick"
                            :pickIndex="$pickIndex"
                            :correctBets="$correctBets"
                            :lastPlace="$lastPlace"
                            :year="$year"
                        />
                    @endforeach
                </div>
            </div>

            <x-picks.race-box :winners="$winners" :year="$year" />
        </div>
    @endif
</div>
