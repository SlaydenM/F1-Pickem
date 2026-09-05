@props([
    'selectedSessionKey' => 0,
    'sessionList' => [],
    'year' => 2024,
])

<div id="choose-week-box" class="plate-1">
    <h1>View Previous Races</h1>

    <select onchange='refreshSessionKey(1)' id='choose-year-list'>
        <option value="1" {{ $year === 2024 ? 'selected' : '' }}>2024</option>
        <option value="2" {{ $year === 2025 ? 'selected' : '' }}>2025</option>
        <option value="3" {{ $year === 2026 ? 'selected' : '' }}>2026</option>
    </select>

    <div id="choose-week-list">
        @foreach ($sessionList as $sessionKey)
            <button id="session-button-{{ $sessionKey }}" class="choose-week-button" onclick="refreshSessionKey({{ $sessionKey }})">{{ $sessionKey % 1000 }}</button>
        @endforeach
    </div>

    {{ $slot }}
</div>