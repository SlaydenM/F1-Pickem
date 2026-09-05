@props(['players', 'lastPlace'])

@php
    $position = 1;
    $previousScore = null;
@endphp

<div id="standings-box" class="relative rounded-3xl border border-slate-700/70 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/60">
    <div id="standings-backer" class="plate pointer-events-none absolute left-0 top-0 hidden rounded-2xl border border-slate-600/60 bg-slate-800/70"></div>

    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex-1">
            <h1 class="text-2xl font-semibold text-white">Standings</h1>
            <table id="standings-list" class="mt-4 w-full border-separate border-spacing-y-2 text-left text-sm text-slate-200">
                <thead>
                    <tr class="h-[90px] border-b-2 border-black even:bg-[#8c1717] odd:bg-[#660b0b] not-first:[clip-path:polygon(0_0,100%_0,90%_100%,0_100%)] xl:h-[30px]">
                        <td class="pb-2 font-medium">Pos.</td>
                        <td class="pb-2 font-medium text-center">Name</td>
                        <td class="pb-2 font-medium text-right">Total</td>
                    </tr>
                </thead>
                    <tbody>
                    @foreach ($players as $player)
                        <tr class="h-[90px] border-b-2 border-black even:bg-[#8c1717] odd:bg-[#660b0b] not-first:[clip-path:polygon(0_0,100%_0,90%_100%,0_100%)] xl:h-[30px]">
                            <td class="rounded-l-2xl px-3 py-2">
                                {{ $previousScore !== null && $player['score'] === $previousScore ? '' : $position . '.' }}
                            </td>
                            <td class="px-3 py-2">{{ $player['name'] }}</td>
                            <td class="rounded-r-2xl px-3 py-2">
                                {{ number_format($player['score'], 2) }}<span class="ml-1 text-sky-400">PTS</span>
                            </td>
                        </tr>
                        @php
                            if ($previousScore === null || $player['score'] !== $previousScore) {
                                $position++;
                            }
                            $previousScore = $player['score'];
                        @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

        <button id="toggleBtn" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-600/70 bg-slate-800/80 text-lg text-slate-100 transition hover:bg-slate-700/80 lg:mt-2">→</button>

        <div id="info-wrapper" class="w-full max-w-xl rounded-2xl border border-slate-700/70 bg-slate-800/70 p-5 text-slate-200 shadow-lg shadow-slate-950/40 lg:ml-4">
            <div id="main-info" class="space-y-2">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-400">Welcome to F1 Pick'em!</p>
                <p>A friendly game around Formula 1.</p>
                <p>Here you can view scores, picks, and results. Check out past races to get a good idea of who to pick!</p>
                <p>This year introduces bonuses, additional driver info, and new design changes!</p>
            </div>
            <div id="scoring-info" class="mt-4 border-t border-slate-700/70 pt-4">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-400">Scoring Rules:</p>
                <p class="mt-2">1st Pick = +7<span class="ml-1 text-sky-400">PTS</span></p>
                <p>10th Pick = +5<span class="ml-1 text-sky-400">PTS</span></p>
                <p>{{ $lastPlace }} Pick = +3<span class="ml-1 text-sky-400">PTS</span></p>
            </div>
        </div>
    </div>
</div>
