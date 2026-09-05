@extends('layouts.app')

@section('content')
<div class="min-h-screen" id="drag-container">
    <div class="max-w-7xl mx-auto px-2 py-4">

        {{-- Race banner --}}
        <x-next-race-card type="countdown" :race="$race" />

        {{-- Section title --}}
        <div class="flex flex-row items-start gap-3 mb-5 sm:items-center sm:gap-4">
            <div class="w-1 h-10 bg-[#E10600]"></div>
            <div>
                <h1 class="font-['Barlow_Condensed'] font-black italic text-4xl text-white tracking-tight uppercase leading-none">
                    Place Picks
                </h1>
                <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[10px] tracking-wider mt-0.5">
                    @if ($numPicks > 1)
                        {{ $numPicks }} players have picked · You should too
                    @elseif ($numPicks == 1)
                        1 player has picked · Make it a pair
                    @else
                        No players have picked · Be the first
                    @endif
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 border border-[#E10600]/30 px-4 py-3 font-['Inter'] text-sm text-[#E10600]"
                 style="background:#160500;border-radius:2px">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Picks form --}}
        <div class="sticky top-20 z-40 transition-all duration-900">
            <form id="picks-form" method="POST" action="{{ route('submit-picks') }}">
                @csrf
                <input type="hidden" name="bettor" id="bettor-input">

                {{-- Bonus + drop zones --}}
                <div class="relative mb-5 border border-white/[0.08] overflow-hidden"
                    style="border-radius:2px;background:rgba(15,15,15,0.85)">

                    {{-- Bonus strip --}}
                    <div class="flex items-center justify-between px-5 py-2.5 border-b border-white/[0.07]"
                        style="background:rgba(0,0,0,0.3)">
                        <span class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[10px] tracking-widest uppercase">
                            Submission Bonus
                        </span>
                        <div class="flex items-center gap-2">
                            <div class="font-['Barlow_Condensed'] font-black italic text-base px-3 py-0.5"
                                style="color:{{ $bonusColor }};background:{{ $bonusColor }}18;
                                        border:1px solid {{ $bonusColor }}44;border-radius:2px">
                                {{ $bonusDisplay }}
                            </div>
                            <span class="font-['JetBrains_Mono'] text-[10px] tracking-widest uppercase"
                                style="color:{{ $bonusColor }}">{{ $bonusLabel }}</span>
                        </div>
                    </div>

                    {{-- Drop zones --}}
                    <div class="flex flex-row gap-2 py-6 px-4 justify-center">
                        @foreach([
                            ['slot' => 'first', 'label' => '1ST PLACE'],
                            ['slot' => 'tenth', 'label' => '10TH PLACE'],
                            ['slot' => 'last',  'label' => 'LAST PLACE'],
                        ] as $zone)
                            <div class="flex flex-col items-center gap-2 w-full">
                                <div class="font-['Barlow_Condensed'] font-black italic text-white text-sm uppercase tracking-widest">
                                    {{ $zone['label'] }}
                                </div>
                                <div class="drop-zone w-[150px] h-[100px] max-sm:w-[120px] max-sm:h-[80px]"
                                    data-slot="{{ $zone['slot'] }}">
                                    <div class="drop-placeholder w-full h-full flex items-center justify-center
                                                font-['JetBrains_Mono'] text-[10px] tracking-widest
                                                border-2 border-dashed border-white/40 text-[#BBBBBB]"
                                        style="border-radius:2px">· · ·</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between">
                    {{-- Fill button --}}
                    <div class="flex justify-center mb-6 sm:justify-end">
                        <button type="button" id="fill-btn" 
                                class="font-['Barlow_Condensed'] font-black italic uppercase text-lg px-5 py-3
                                    transition-all duration-150 bg-[#E10600] text-white cursor-pointer"
                                style="clip-path:polygon(12px 0%,100% 0%,calc(100% - 12px) 100%,0% 100%)">
                            ⚡ Fill Last Picks
                        </button>
                    </div>

                    {{-- Submit button --}}
                    <div class="flex justify-center mb-6 sm:justify-end">
                        <button type="submit" id="submit-btn" disabled
                                class="font-['Barlow_Condensed'] font-black italic uppercase text-lg px-5 py-3
                                    transition-all duration-150 bg-[#232323] text-[#BBBBBB] cursor-not-allowed"
                                style="clip-path:polygon(12px 0%,100% 0%,calc(100% - 12px) 100%,0% 100%)">
                            🔒 Lock In Picks
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Driver grid --}}
        <div class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-sm text-[#BBBBBB] mb-4">
            {{ $year }} Driver Grid · drag to pick
        </div>
        <x-driver-grid :drivers="$drivers" />
    </div>
</div>
@endsection

@push('scripts')
{{-- jQuery & jQuery UI Dependencies --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    html, body { 
        /* width: 100%; max-width: 100vw;
        /* overflow-x: hidden; overscroll-behavior: none;  */
        /* position: relative; */ */
    }
    .driver-grid-item > div, .slot-item {
        /* 1. Allows vertical page scrolling, but lets JS intercept the long-press */
        touch-action: pan-y !important;
        
        /* 2. Prevents the mobile "Save Image" or text-selection menus from interrupting the drag */
        user-select: none !important;
        -webkit-user-select: none !important;
        -webkit-touch-callout: none !important;
    }
    /* ADDED: Strict lock applied to the body only during an active drag */
    body.is-dragging {
        overflow: hidden !important;
        touch-action: none !important;
    }
</style>

<script>
$(document).ready(function() {
    // ── PATCH: Stop Touch Punch from breaking mobile scrolling ─────────────────
    var _origTouchStart = $.ui.mouse.prototype._touchStart;
    var _origTouchMove = $.ui.mouse.prototype._touchMove;

    $.ui.mouse.prototype._touchStart = function(event) {
        // Record starting coordinates to detect swiping later
        this._dragTouchStartY = event.originalEvent.touches ? event.originalEvent.touches[0].clientY : 0;
        this._dragTouchStartX = event.originalEvent.touches ? event.originalEvent.touches[0].clientX : 0;

        var origJqPrevent = event.preventDefault;
        var origNativePrevent = event.originalEvent ? event.originalEvent.preventDefault : $.noop;
        
        event.preventDefault = function() {};
        if (event.originalEvent) event.originalEvent.preventDefault = function() {};
        
        _origTouchStart.apply(this, arguments);
        
        event.preventDefault = origJqPrevent;
        if (event.originalEvent) event.originalEvent.preventDefault = origNativePrevent;
    };

    $.ui.mouse.prototype._touchMove = function(event) {
        if (!this._mouseStarted) {
            // SCROLL FIX: Check if the user's finger has moved more than 15px
            if (this._dragTouchStartY !== undefined && event.originalEvent.touches) {
                var currentY = event.originalEvent.touches[0].clientY;
                var currentX = event.originalEvent.touches[0].clientX;
                
                if (Math.abs(currentY - this._dragTouchStartY) > 15 || Math.abs(currentX - this._dragTouchStartX) > 15) {
                    // They are scrolling. Kill the 200ms drag timer so the element doesn't get picked up.
                    if (this._mouseDelayTimer) {
                        clearTimeout(this._mouseDelayTimer);
                        delete this._mouseDelayTimer;
                    }
                }
            }

            var origJqPrevent = event.preventDefault;
            var origNativePrevent = event.originalEvent ? event.originalEvent.preventDefault : $.noop;
            
            event.preventDefault = function() {};
            if (event.originalEvent) event.originalEvent.preventDefault = function() {};
            
            _origTouchMove.apply(this, arguments);
            
            event.preventDefault = origJqPrevent;
            if (event.originalEvent) event.originalEvent.preventDefault = origNativePrevent;
        } else {
            // Drag has started, allow Touch Punch to control the movement
            _origTouchMove.apply(this, arguments);
        }
    };
    // ───────────────────────────────────────────────────────────────────────────

    var picks = { first: null, tenth: null, last: null };
    var bonus = {{ $bonus }}; 

    $('[draggable]').removeAttr('draggable');

    // ── ADDED: The ultimate scroll-killer for mobile ───────────────────────────
    var isDragging = false;
    document.addEventListener('touchmove', function(e) {
        if (isDragging) {
            e.preventDefault(); // Physically blocks the browser's scroll event
        }
    }, { passive: false });

    // ── 1. Unified Draggable Configuration ─────────────────────────────────────
    function bindDraggable($el, isSlot) {
        // Accurately detect if the user is on a touch-enabled device
        var isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

        $el.draggable({
            delay: isTouchDevice ? 100 : 0, // hold delay for mobile, instant for desktop
            helper: 'clone',      
            revert: 'invalid',    
            appendTo: 'body',     
            zIndex: 100,
            scroll: false,
            containment: '#drag-container',
            start: function(event, ui) {
                isDragging = true;
                $('body').addClass('is-dragging');

                var $this = $(this);
                // Grab ID based on whether it's coming from a slot or the grid wrapper
                var driverId = isSlot ? $this.data('driver-id') : $this.closest('.driver-grid-item').data('driver-id');
                
                ui.helper
                    // .removeClass('transition-all duration-50 hover:scale-150 hover:z-10')
                    .css({ 
                        transition: 'transform 0.15s ease-out', 
                        transform: 'scale(1.25)' 
                    });
                
                ui.helper.data('driver-id', driverId);
                if (isSlot) ui.helper.data('slot', $this.data('slot'));

                // Visual feedback during drag
                if (isSlot) {
                    $this.css('opacity', '0.01'); 
                } else {
                    dimGridItem(driverId, true);
                }
            },
            stop: function(event, ui) {
                isDragging = false;
                $('body').removeClass('is-dragging');

                var $this = $(this);
                var driverId = isSlot ? $this.data('driver-id') : $this.closest('.driver-grid-item').data('driver-id');
                
                if (isSlot) {
                    $this.css('opacity', '1'); 
                } else {
                    // Check if it was successfully placed in a slot. If not, un-dim.
                    if (!Object.values(picks).includes(driverId)) {
                        dimGridItem(driverId, false);
                    }
                }
            }
        });
    }

    // Initialize all grid items
    bindDraggable($('.driver-grid-item > div'), false);

    // ── 2. Slot Drop Zone Behaviour ────────────────────────────────────────────
    $('.drop-zone').droppable({
        accept: '.driver-grid-item > div, .slot-item',
        hoverClass: 'dz-over',
        greedy: true,
        drop: function(event, ui) {
            var driverId = ui.helper.data('driver-id');
            var sourceSlot = ui.helper.data('slot'); // Will exist if dragged from another slot
            var targetSlot = $(this).data('slot');

            if (!driverId) return;

            // Clean up old slot if moving an item between slots
            if (sourceSlot && sourceSlot !== targetSlot) {
                picks[sourceSlot] = null;
                renderEmptySlot(sourceSlot);
            }

            // Un-dim previous driver in the grid if overwriting an existing pick
            if (picks[targetSlot] && picks[targetSlot] != driverId) {
                dimGridItem(picks[targetSlot], false);
            }

            picks[targetSlot] = driverId;
            renderFilledSlot(targetSlot, driverId);
            dimGridItem(driverId, true);
            updateBtn();
        }
    });

    // ── 3. Document Body Droppable (Remove pick by dragging it out) ────────────
    $('body').droppable({
        accept: '.slot-item',
        drop: function(event, ui) {
            var slot = ui.helper.data('slot');
            if (slot) {
                clearSlot(slot);
                updateBtn();
            }
        }
    });

    // ── 4. UI Helper Functions ─────────────────────────────────────────────────
    function renderFilledSlot(slot, driverId) {
        // Clone the clean inner div directly from the desktop grid layout 
        // This eliminates the need for the hidden #driver-store
        const isMobile = window.matchMedia("(max-width: 767.98px)").matches;
        var $pristineCard = $('#driver-grid-' + ((isMobile) ? 'sm' : 'md') + ' .driver-grid-item[data-driver-id="' + driverId + '"] > div').clone();
        
        $pristineCard
            .addClass('slot-item')
            .data('slot', slot)
            .data('driver-id', driverId)
            .css('cursor', 'grab');

        $('.drop-zone[data-slot="' + slot + '"]').empty().append($pristineCard);
        
        // Re-bind draggable logic to the newly placed element
        bindDraggable($pristineCard, true);
    }

    function clearSlot(slot) {
        var prev = picks[slot];
        picks[slot] = null;
        if (prev) dimGridItem(prev, false);
        renderEmptySlot(slot);
    }

    function renderEmptySlot(slot) {
        $('.drop-zone[data-slot="' + slot + '"]').html(
            '<div class="drop-placeholder w-full h-full flex items-center justify-center font-[\'JetBrains_Mono\'] text-[10px] tracking-widest border-2 border-dashed border-white/40 text-[#BBBBBB]" style="border-radius:2px">· · ·</div>'
        );
    }

    function dimGridItem(driverId, dim) {
        // Uses a class selector because the ID #driver-grid is duplicated for desktop/mobile views
        $('.driver-grid-item[data-driver-id="' + driverId + '"]').css({
            'opacity': dim ? '0.30' : '',
            'filter': dim ? 'saturate(0.30)' : ''
        });
    }

    function updateBtn() {
        var ready = picks.first && picks.tenth && picks.last;
        var $btn = $('#submit-btn');
        
        $btn.prop('disabled', !ready);
        
        if (ready) {
            $btn.removeClass('bg-[#232323] text-[#BBBBBB] cursor-not-allowed').addClass('bg-[#E10600] text-white cursor-pointer');
        } else {
            $btn.removeClass('bg-[#E10600] text-white cursor-pointer').addClass('bg-[#232323] text-[#BBBBBB] cursor-not-allowed');
        }
    }

    // ── 6. Auto-Fill Functionality ─────────────────────────────────────────────
    function autoFillPicks(selections) {
        // 1. Wipe the board clean to prevent driver duplication glitches
        ['first', 'tenth', 'last'].forEach(function(slot) {
            if (picks[slot]) {
                clearSlot(slot);
            }
        });

        // 2. Loop through the new selections and place them
        Object.keys(selections).forEach(function(slot) {
            var driverId = selections[slot];
            
            if (driverId) {
                picks[slot] = driverId;
                renderFilledSlot(slot, driverId);
                dimGridItem(driverId, true);
            }
        });

        // 3. Run the validation check to light up the Submit button
        updateBtn();
    }

    // ── 5. Form Submission ─────────────────────────────────────────────────────
    $('#picks-form').on('submit', function (e) {
        if (!picks.first || !picks.tenth || !picks.last) { e.preventDefault(); return; }
        $('#bettor-input').val(JSON.stringify({
            bets:  [parseInt(picks.first), parseInt(picks.tenth), parseInt(picks.last)],
            bonus: bonus
        }));
    });
    $('#fill-btn').on('click', function() {
        autoFillPicks(@json($lastPicks));
        // window.alert(JSON.stringify(@json($lastPicks)))
    });
    // $('#picks-form').on('fill', function (e) {
    //     if (!picks.first || !picks.tenth || !picks.last) { e.preventDefault(); return; }
    //     $('#bettor-input').val(JSON.stringify({
    //         bets:  [parseInt(picks.first), parseInt(picks.tenth), parseInt(picks.last)],
    //         bonus: bonus
    //     }));
    // });
    
    // Inject dynamic CSS for the drop-zone hover
    $('<style>')
        .prop('type', 'text/css')
        .html('.drop-zone.dz-over .drop-placeholder{border-color:#E10600!important;color:#E10600!important;}')
        .appendTo('head');
});
</script>
@endpush