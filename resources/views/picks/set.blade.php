<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/common.css') }}">
        <link rel="stylesheet" href="{{ asset('css/setPicksStyle.css') }}">
    @endpush

    @php
        $currentUser = auth()->user();
        $userScore = $standings->firstWhere('name', $currentUser->name)['score'] ?? 0;
        $betLabels = ['1st', '10th', $lastPlace];
    @endphp

    <div id="page-name">F1 Pick'em</div>

    <div id="welcome-box" class="plate-1">
        <h1>
            Welcome {{ $currentUser->name }}!<br>
            Your Score Is {{ number_format($userScore, 2) }} PTS
        </h1>
    </div>

    <div class="plate-1" id="race-info">
        <h1>Round #{{ $sessionKey % 1000 }} - {{ optional($mainRace)->date_start?->format('F j, Y g:ia') ?? 'TBD' }}</h1>
        <h1><i>Formula 1 <u>{{ optional($mainRace)->name ?? 'Race Info Pending' }}</u> Grand Prix</i></h1>
    </div>

    <div id="container">
        <div id="bet-plate" class="plate-1">
            <h1 class="plate-head">Place Your Picks Here!</h1>

            <div id="bet-wrapper">
                <svg class="bettor-box-poly" viewBox="0 0 100 180" preserveAspectRatio="none">
                    <polygon points="0,0 100,0 40,180 0,180"/>
                </svg>
                <div class="snap-box bet-box widget widget-shadow" id="bet1">Drag Here For 1st</div>
                <div class="snap-box bet-box widget widget-shadow" id="bet2">Drag Here For 10th</div>
                <div class="snap-box bet-box widget widget-shadow" id="bet3">Drag Here For {{ $lastPlace }}</div>
            </div>

            <div id="button-info-wrapper">
                <form id="button-wrapper">
                    <input type="button" id="submit-button" class="widget widget-shadow" value="Submit Picks!" />
                </form>

                <div id="info-wrapper">
                    <p class="info-head">Welcome to F1 Pick'em!</p>
                    <p>Simply drag & drop each driver widget to the box you want to bet on. Once you have all three picks, submit!</p>
                    <p>After submitting, you'll be able to view others' picks and race stats.</p>
                    <p class="info-head">Early/Late Bonus:</p>
                    <ul>
                        <li>Before FP1: +50%</li>
                        <li>Before FP2: +25%</li>
                        <li>Before FP3: +10%</li>
                        <li>After Qualifying: -50%</li>
                    </ul>
                    <p>Submissions will close before the Grand Prix.</p>
                </div>

                <div id="link">
                    <ul>
                        <h3>F1.com:</h3>
                        <li><a href="https://www.formula1.com/en/racing/{{ $year }}" target="_blank">Schedule</a></li>
                        <li><a href="https://www.formula1.com/en/results/{{ $year }}/races" target="_blank">Results</a></li>
                        <li><a href="https://www.formula1.com/en/drivers" target="_blank">Drivers</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="selection-plate" class="plate-1">
            <h1 class="plate-head">Driver Selection</h1>
            <div id="selection-list">
                @foreach ($contestants as $driver)
                    <div class="driver-wrapper">
                        <div class="draggable driver-data widget" id="{{ $driver->id }}" data-number="{{ $driver->id }}">
                            <img class="logo" src="{{ asset($driver->getPath()) }}" alt="{{ $driver->name }}" />
                            <div class="driver-tooltip-wrapper">
                                <img src="{{ asset('images/tooltip.png') }}" alt="info" />
                                <span class="widget driver-tooltip-plate">
                                    <table class="driver-tooltip-data">
                                        <tr>
                                            <th>Pos.</th>
                                            <th># Wins</th>
                                            <th>% Wins</th>
                                        </tr>
                                        <tr class="bet-label">
                                            <td>1st</td>
                                            <td>0</td>
                                            <td>0%</td>
                                        </tr>
                                        <tr class="bet-label">
                                            <td>10th</td>
                                            <td>0</td>
                                            <td>0%</td>
                                        </tr>
                                        <tr class="bet-label">
                                            <td>{{ $lastPlace }}</td>
                                            <td>0</td>
                                            <td>0%</td>
                                        </tr>
                                    </table>
                                </span>
                            </div>
                        </div>
                        <div class="snap-box driver-box widget widget-shadow" id="p{{ $driver->number }}">Pit {{ $loop->iteration }}</div>
                    </div>
                @endforeach

                @if (! $contestants->contains('number', 100))
                    <div class="driver-wrapper">
                        <div class="draggable driver-data driver-data-extra widget" id="100" data-number="100">
                            <img class="logo" src="{{ route('logos', ['year' => $year, 'filename' => 'f1_100.png']) }}" alt="Extra Driver" />
                            <div class="driver-tooltip-wrapper">
                                <img src="{{ asset('images/tooltip.png') }}" alt="info" />
                                <span class="widget driver-tooltip-plate">
                                    <p>Use this only when your driver is not on the list, we will update score as needed!</p>
                                </span>
                            </div>
                        </div>
                        <div class="snap-box driver-box widget widget-shadow" id="p100">:)</div>
                    </div>
                @endif

                <div class="driver-wrapper">
                    <div class="driver-data widget" id="driver-data-overall">
                        <h2 style="color:white">Overall</h2>
                        <div class="driver-tooltip-wrapper">
                            <img src="{{ asset('images/tooltip.png') }}" alt="info" />
                            <span class="widget driver-tooltip-plate">
                                <table class="driver-tooltip-data">
                                    <tr>
                                        <th>Pos.</th>
                                        <th>Most</th>
                                        <th>Name</th>
                                    </tr>
                                    <tr class="bet-label">
                                        <td>1st</td>
                                        <td>0</td>
                                        <td>Unknown</td>
                                    </tr>
                                    <tr class="bet-label">
                                        <td>10th</td>
                                        <td>0</td>
                                        <td>Unknown</td>
                                    </tr>
                                    <tr class="bet-label">
                                        <td>{{ $lastPlace }}</td>
                                        <td>0</td>
                                        <td>Unknown</td>
                                    </tr>
                                </table>
                            </span>
                        </div>
                    </div>
                    <div class="snap-box driver-box widget widget-shadow">Pit 1</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.PICKEM_SESSION_KEY = {{ $sessionKey }};
        window.PICKEM_BONUS = {{ $bonus }};
        window.PICKEM_SUBMIT_URL = '{{ route('picks.submit') }}';
    </script>
</x-layouts.app>
