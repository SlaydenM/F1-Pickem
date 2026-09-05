@props(['name', 'score'])

<div id="welcome-box" class="rounded-3xl border border-slate-700/70 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/60">
    <h1 class="text-2xl font-semibold leading-tight text-white sm:text-3xl">
        Welcome {{ $name }}!<br>
        Your Score Is {{ number_format($score, 2) }}<span class="ml-1 text-sky-400">PTS</span>
    </h1>
</div>
