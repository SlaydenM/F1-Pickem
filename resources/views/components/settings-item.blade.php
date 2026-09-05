@props(['label', 'model', 'options'])

@php
    // Detect if this should be a toggle (has exactly two options: 1 and 0)
    $isToggle = count($options) === 2 && isset($options[1]) && isset($options[0]);
@endphp

<li class="flex items-center justify-between py-3 border-b border-white/[0.05] last:border-0" x-data="{ val: @entangle($model).live }">
    <div class="flex items-start gap-3">
        <span class="font-['JetBrains_Mono'] text-[#E10600] text-xs mt-1 flex-shrink-0">›</span>
        <span class="font-['Inter'] text-[#BBBBBB] text-sm leading-relaxed">{{ $label }}</span>
    </div>
    
    <div class="ml-4 flex-shrink-0">
        @if($isToggle)
            <!-- Custom Toggle Switch -->
            <button 
                type="button"
                x-on:click="val = val ? 0 : 1"
                x-bind:class="val ? 'bg-[#E10600] border-transparent' : 'bg-[#100000] border-[#E10600]/25'"
                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border transition-colors duration-200 ease-in-out focus:outline-none"
            >
                <span 
                    x-bind:class="val ? 'translate-x-4' : 'translate-x-0'"
                    class="pointer-events-none inline-block h-[1.15rem] w-[1.15rem] transform rounded-full bg-white transition duration-200 ease-in-out"
                ></span>
            </button>
        @else
            <!-- Dropdown Select -->
            <select 
                x-model="val" 
                x-bind:class="val > 0 ? 'text-black bg-[#E10600]' : 'bg-[#100000]'"
                class="border-[#E10600]/25 rounded-sm px-3 py-1.5 font-['Inter'] text-sm focus:outline-none focus:border-[#E10600] transition-colors cursor-pointer border"
            >
                @foreach($options as $value => $text)
                    <option value="{{ $value }}">{{ $text }}</option>
                @endforeach
            </select>
        @endif
    </div>
</li>