@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-3xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <div class="w-1 h-10 bg-[#E10600]"></div>
            <h1 class="font-['Barlow_Condensed'] font-black italic text-4xl text-white tracking-tight uppercase">
                Rules &amp; Scoring
            </h1>
        </div>

        <div class="space-y-4">

            @foreach([
                [
                    'title'   => 'Overview',
                    'content' => [
                        "F1 Pick'em is a season-long prediction game played alongside the Formula 1 calendar.",
                        "Each race weekend, participants submit exactly three picks before the race start.",
                        "Points accumulate over the season to determine the overall champion.",
                    ],
                ],
                [
                    'title'   => 'How to Submit Picks',
                    'content' => [
                        'Navigate to the Next Race page before qualifying ends.',
                        'Drag driver cards from the grid into the three prediction slots.',
                        'Slot 1: Your predicted race winner (1st Place).',
                        'Slot 2: Your predicted driver to finish 10th (Points Edge).',
                        'Slot 3: Your predicted last-place finisher (Backmarker).',
                        'Click Lock In Picks — predictions cannot be changed after submission.',
                        'The deadline is the official race start time (formation lap begins).',
                    ],
                ],
                [
                    'title'   => 'Scoring System',
                    'content' => [
                        'Correct 1st Place prediction: +7 points',
                        'Correct 10th Place prediction: +5 points',
                        'Correct Last Place prediction: +3 points',
                        'Early Bird bonus (submit before FP1): ×1.5 multiplier on earned points',
                        'Early bonus (submit before FP2): ×1.25 multiplier',
                        'Bonus (submit before FP3): ×1.10 multiplier',
                        'Late Penalty (submit after race start): ×0.5 multiplier',
                        'No points are awarded for partial matches (e.g. picking 2nd instead of 1st).',
                        'DNS / DNQ drivers are excluded from scoring; picks against them score zero.',
                    ],
                ],
                [
                    'title'   => 'Tiebreakers',
                    'content' => [
                        'In the event of equal season points, the player with more 1st-place correct picks wins.',
                        'Secondary tiebreaker: most correct 10th-place picks.',
                        'Tertiary tiebreaker: most correct last-place picks.',
                        'If still tied, earliest submission timestamp wins.',
                    ],
                ],
                [
                    'title'   => 'Fairness & Conduct',
                    'content' => [
                        "All picks must be submitted independently — sharing answers before lockout is unsportsmanlike.",
                        'Race results are taken from the official FIA classification after stewards\' decisions.',
                        'Appeals about scoring must be raised within 48 hours of results publication.',
                        "The commissioner's ruling on disputed picks is final.",
                    ],
                ],
            ] as $section)
                <div class="relative bg-[#1c1c1c] border border-white/[0.07] overflow-hidden">
                    <div class="absolute left-0 inset-y-0 w-[3px] bg-[#E10600]"></div>
                    <div class="px-6 py-5">
                        <h2 class="font-['Barlow_Condensed'] font-black italic text-[#E10600] text-xl uppercase tracking-wide mb-3">
                            {{ $section['title'] }}
                        </h2>
                        <ul class="space-y-2">
                            @foreach($section['content'] as $line)
                                <li class="flex items-start gap-3">
                                    <span class="font-['JetBrains_Mono'] text-[#E10600] text-xs mt-1 flex-shrink-0">›</span>
                                    <span class="font-['Inter'] text-[#BBBBBB] text-sm leading-relaxed">{{ $line }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach

            {{-- Quick Reference --}}
            <div class="relative bg-[#100000] border border-[#E10600]/25 overflow-hidden">
                <div class="px-6 py-5">
                    <h2 class="font-['Barlow_Condensed'] font-black italic text-white text-xl uppercase tracking-wide mb-4">
                        Quick Reference
                    </h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['label' => '1st Place correct',  'pts' => '+7'],
                            ['label' => '10th Place correct', 'pts' => '+5'],
                            ['label' => 'Last Place correct', 'pts' => '+3'],
                            ['label' => 'Early Bird bonus',   'pts' => '×1.5'],
                        ] as $row)
                            <div class="flex items-center justify-between bg-[#1c1c1c] px-4 py-3"
                                 style="clip-path:polygon(6px 0%,100% 0%,calc(100% - 6px) 100%,0% 100%)">
                                <span class="font-['Inter'] text-[#BBBBBB] text-sm">{{ $row['label'] }}</span>
                                <span class="font-['Barlow_Condensed'] font-black italic text-[#E10600] text-lg">
                                    {{ $row['pts'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
