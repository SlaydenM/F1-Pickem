@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex flex-col md:flex-row gap-6 items-start w-full">
            {{-- Season standings --}}
            <div class="w-full order-4 md:order-none">
                <x-player-standings :players="$players" :rankChanges="$rankChanges" :year="$year" :round="$round" />
            </div>

            {{-- Mobile-first action centre --}}
            <section class="flex flex-col gap-4 order-1 md:order-none">
                <x-next-race-card type="dashboard" :race="$race" />
                <x-my-season-card :myRank="$myRank" :myScore="$myScore" :round="$round" />
                <x-submit-prompt-card :submitted="(bool) $currentPick" :race="$race" />
            </section>
        </div>
    </div>
</div>
@endsection
