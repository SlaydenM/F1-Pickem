@props(['pick', 'pickIndex', 'correctBets', 'lastPlace', 'year'])

@php
    $player = $pick->user;
    $score = round($pick->score * $pick->bonus, 2);
    $rawScore = $pick->bonus ? round($pick->score, 2) : $score;
    $bonusAmount = ($pick->bonus - 1) * 100;
    $bonusSign = $bonusAmount >= 0 ? '+' : '-';
@endphp

<div class="w-full max-w-[260px] rounded-3xl border border-slate-700/70 bg-slate-900/80 p-4 shadow-lg shadow-slate-950/40">
    @if ($pick->user->name)
        <h2 class="text-lg font-semibold text-white">{{ $pick->user->name }}</h2>
    @else
        <h2 class="text-lg font-semibold text-white">Unknown User</h2>
    @endif

    <table class="mt-4 w-full border-separate border-spacing-y-2 text-left">
        <tbody>
            @foreach ($pick->getPicks() as $betIndex => $driver)
                <tr>
                    <td>
                        @if ($driver)
                            <x-picks.driver-data-image
                                :driver="$driver"
                                :year="$year"
                                :alt="$driver->name"
                            />
                        @else
                            <span class="text-sm text-slate-400">No Driver</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 cursor-pointer rounded-2xl bg-slate-800/80 px-4 py-3 text-center text-sm font-semibold text-slate-100" onclick="toggleBox(event)">
        {{ $score }}<span class="ml-1 text-sky-400">PTS</span> ▼
        <div class="hover-box mt-3 hidden rounded-xl border border-slate-700/70 bg-slate-900/90 p-3 text-left text-sm text-slate-300">
            Raw: {{ $bonusSign }}{{ $rawScore }}<span class="ml-1 text-sky-400">PTS</span><br>
            Bonus: {{ $bonusSign }}{{ abs($bonusAmount) }}%<br>
            Total: {{ $score }}<span class="ml-1 text-sky-400">PTS</span>
        </div>
    </div>
</div>
