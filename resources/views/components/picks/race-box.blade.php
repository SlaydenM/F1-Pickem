@props(['winners', 'year'])

<div id="race-box-wrapper" class="rounded-2xl border border-slate-700/70 bg-slate-800/70 p-5">
    <h1 class="text-2xl font-semibold text-white">Results</h1>
    <div id="race-box" class="mt-4">
        <table id="driver-list" class="w-full">
            @if ($winners->isEmpty())
                <tr>
                    <td>
                        <div class="rounded-2xl border border-dashed border-slate-600/70 p-4 text-center text-sm text-slate-400">(No Winners Yet)</div>
                    </td>
                </tr>
            @else
                @foreach ($winners as $winner)
                    @php
                        $position = $loop->iteration;
                        $important = $position === 1 || $position === 10 || $loop->last;
                        $importantClass = $important ? 'ring-2 ring-amber-400' : 'ring-1 ring-slate-700';
                    @endphp
                    <tr class="border-b border-slate-700/70 last:border-b-0">
                        <td class="py-3 pr-3 text-sm font-medium text-slate-300">{{ $position }}.</td>
                        <td class="py-3">
                            <x-picks.driver-data-image
                                :driver="$winner->driver"
                                :year="$year"
                                :alt="$winner->driver->name"
                                class="h-16 w-16 rounded-xl border border-white/10 bg-slate-950/60 p-1 shadow-md shadow-black/30 {{ $importantClass }}"
                            />
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
</div>
