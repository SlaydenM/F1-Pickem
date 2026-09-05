<x-layouts.app>
    {{-- @push('styles')
        <link rel="stylesheet" href="{{ asset('css/common.css') }}">
        <link rel="stylesheet" href="{{ asset('css/viewPicksStyle.css') }}">
    @endpush --}}
    <div class="min-h-screen bg-slate-950 px-4 py-6 text-slate-100 sm:px-6 lg:px-8">
        <div id="page-name" class="mb-6 text-3xl font-semibold uppercase tracking-[0.3em] text-slate-100">F1 Pick'em</div>

        @if ($message === 'picked')
            <div id="status-message" class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-5 text-center text-emerald-200">Submitted Your Picks!</div>
        @elseif ($status === 'locked')
            <div id="status-message" class="mb-6 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-5 text-center text-amber-200">Picks are locked after<br>Grand Prix</div>
        @else
            <div id="filler" class="mb-6 h-4"></div>
        @endif

        <div class="space-y-6">
            <x-picks.welcome-box :name="auth()->user()->name" :score="auth()->user()->getTotal($year)" />

            <x-picks.standings-box :players="$players" :lastPlace="$lastPlace" />

            <x-picks.choose-session :selectedSessionKey="$selectedSessionKey" :sessionList="$sessionList" :year="$year">
                <h1 class="text-lg font-semibold text-slate-100 sm:text-xl">
                    Round #{{ $selectedSessionKey % 1000 }} - {{ optional($race)->date_start?->format('F j, Y g:ia') ?? 'TBD' }}<br>
                    <i class="text-slate-300">Formula 1 <u>{{ optional($race)->name ?? 'Race Info Pending' }}</u></i>
                </h1>
            </x-picks.choose-session>

            <x-picks.results-box
                :status="$status"
                :sessionKey="$sessionKey"
                :selectedSessionKey="$selectedSessionKey"
                :picks="$picks"
                :correctBets="$correctBets"
                :lastPlace="$lastPlace"
                :winners="$winners"
                :year="$year"
            />
        </div>
    </div>

    @push('scripts')
        <script>
            window.REFRESH_ROUTE = '{{ route('picks.view') }}';

            function refreshSessionKey(sessionKey) {
                const yearList = document.getElementById('choose-year-list');
                const yearValue = Number(yearList.value);
                const base = yearValue * 1000;
                const round = sessionKey % 1000;
                window.location.href = `${window.REFRESH_ROUTE}?sessionKey=${base + round}`;
            }

            window.onload = () => {
                const urlParams = new URLSearchParams(window.location.search);
                const sessionKey = Number(urlParams.get('sessionKey'));
                if (sessionKey) {
                    document.getElementById('choose-year-list').value = Math.floor(sessionKey / 1000);
                }
                updateStandingsElements();
                togglePanels();
            };

            window.onresize = () => updateStandingsElements();

            function toggleBox(event) {
                event.stopPropagation();
                document.querySelectorAll('.hover-box').forEach(box => {
                    if (box !== event.currentTarget.querySelector('.hover-box')) {
                        box.style.display = 'none';
                    }
                });

                const box = event.currentTarget.querySelector('.hover-box');
                box.style.display = box.style.display === 'block' ? 'none' : 'block';
            }

            const backer = document.getElementById('standings-backer');
            const header = document.getElementById('standings-head');
            const list = document.getElementById('standings-list');
            const plate = document.getElementById('standings-box');
            const info = document.getElementById('info-wrapper');

            function updateStandingsElements() {
                if (!backer || !header || !list || !plate) return;
                const isPhone = window.matchMedia('(max-width: 767px)').matches;
                backer.style.top = `${header.offsetTop + header.offsetHeight}px`;
                backer.style.left = `${header.offsetLeft + (isPhone ? 30 : 10)}px`;
                backer.style.height = `${list.offsetHeight - 2 * header.offsetHeight}px`;
                backer.style.display = 'block';
                plate.style.height = `${list.offsetHeight + 2 * header.offsetHeight}px`;
            }

            const rightPanel = document.getElementById('info-wrapper');
            const leftPanel = document.getElementById('standings-list');
            const toggleBtn = document.getElementById('toggleBtn');
            let isHidden = false;

            function togglePanels() {
                if (!rightPanel || !leftPanel || !toggleBtn) return;
                if (isHidden) {
                    leftPanel.classList.remove('panel-expanded');
                    toggleBtn.classList.remove('btn-moved');
                    updateStandingsElements();
                    setTimeout(() => {
                        rightPanel.classList.remove('panel-hidden');
                        rightPanel.style.position = 'initial';
                        toggleBtn.textContent = '→';
                    }, 100);
                } else {
                    rightPanel.classList.add('panel-hidden');
                    setTimeout(() => {
                        leftPanel.classList.add('panel-expanded');
                        toggleBtn.classList.add('btn-moved');
                        toggleBtn.textContent = '←';
                        rightPanel.style.position = 'absolute';
                        updateStandingsElements();
                    }, 200);
                }
                isHidden = !isHidden;
            }

            toggleBtn?.addEventListener('click', togglePanels);
        </script>
    @endpush
</x-layouts.app>
