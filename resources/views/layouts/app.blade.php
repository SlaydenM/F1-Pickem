<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>F1 Pick'em</title>
    <link rel="icon" type="image/png" href="{{ asset('f1pickem-logo-square.PNG') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,600;0,700;0,800;0,900;1,400;1,700;1,800;1,900&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white min-h-screen" style="font-family:'Inter',sans-serif;">

    {{-- Fixed gradient background --}}
    <div class="fixed max-h-[100dvh] w-full inset-0 overflow-hidden -z-10 pointer-events-none flex items-center justify-center">
        <img 
            src="{{ asset('slanted-gradient-3.svg') }}" 
            alt="" style="object-fit:fill;" draggable="false" 
            class="absolute xl:w-full max-w-none xl:h-auto xl:h-[100dvh] top-0 object-cover"
        >
    </div>

    {{-- Nav (authenticated pages only) --}}
    @auth
    @php
        $navTabs = [
            ['label' => 'Home',       'route' => 'home',       'match' => 'home'],
            ['label' => 'Next Race',  'route' => 'next-race',  'match' => 'next-race*'],
            ['label' => 'Past Races', 'route' => 'past-races', 'match' => 'past-races'],
            ['label' => 'Rules',      'route' => 'rules',      'match' => 'rules'],
            ['label' => 'Settings',   'route' => 'settings',   'match' => 'settings'],
        ];
    @endphp
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-gray-700"
            style="height:72px;background:rgba(8,8,8,0.95);backdrop-filter:blur(12px);">
        
        {{-- Logo (Mobile) --}}
        <div class="block md:hidden absolute w-screen flex justify-center h-full pointer-events-none">
            <img src="{{ asset('f1pickem-logo-square.PNG') }}"
                    alt="F1 Pick'em"
                    class="h-full w-auto object-contain">
        </div>
        
        <div class="max-w-7xl mx-auto gap-5 w-screen h-full flex items-center justify-between px-4 sm:px-6" style="background:#000000;">
            {{-- Mobile nav toggle mx-auto --}}
            <div class="flex md:hidden items-center">
                <button type="button" id="mobile-nav-toggle"
                        class="flex h-10 w-10 items-center justify-center rounded-sm border border-white/15 bg-[#232323] text-white"
                        aria-label="Toggle navigation" aria-controls="mobile-nav-menu" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="7" x2="20" y2="7"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="17" x2="20" y2="17"/>
                    </svg>
                </button>
            </div>


            {{-- Logo (Desktop) --}}
            <div class="hidden md:block flex h-full">
                <img src="{{ asset('f1pickem-logo-wide.PNG') }}"
                        alt="F1 Pick'em"
                        class="h-full w-auto object-contain">
            </div>
            {{-- <div class="max-md:absolute max-md:top-1/2 max-md:left-1/2 max-md:-translate-x-1/2 max-md:-translate-y-1/2 flex-1 flex justify-center md:flex-none md:justify-start md:mr-auto h-full">
                <div class="h-full flex items-center">
                    <img src="{{ asset('f1pickem-logo-square.PNG') }}"
                         alt="F1 Pick'em"
                         class="block md:hidden h-full w-auto object-contain border-b border-gray-700">
                    <img src="{{ asset('f1pickem-logo-wide.PNG') }}"
                         alt="F1 Pick'em"
                         class="hidden md:block h-full w-auto object-contain">
                </div>
            </div> --}}

            {{-- Navigation tabs --}}
            <nav class="hidden md:flex items-stretch h-full">
                @foreach($navTabs as $tab)
                    @php $isActive = request()->routeIs($tab['match']); @endphp
                    <a href="{{ route($tab['route']) }}"
                       class="relative px-6 flex items-center font-['Barlow_Condensed'] font-bold text-base uppercase tracking-widest transition-all duration-150 {{ $isActive ? 'text-white' : 'text-[#BBBBBB] hover:text-white' }}">
                        @if($isActive)
                            <div class="absolute inset-0 bg-[#E10600]/[0.07]"></div>
                            <div class="absolute inset-x-0 bottom-0 h-[3px] bg-[#E10600]"
                                 style="clip-path:polygon(6px 0%,100% 0%,calc(100% - 6px) 100%,0% 100%)"></div>
                        @endif
                        <span class="relative">
                            {{ $tab['label'] }}
                        </span>
                    </a>
                @endforeach
            </nav>

            {{-- User + logout --}}
            <div class="ml-auto hidden md:flex items-center gap-3">
                <span class="font-['Inter'] text-sm text-[#BBBBBB]">{{ Auth::user()->name }}</span>
                <div class="w-9 h-9 bg-[#2a2a2a] border border-white/10 flex items-center justify-center"
                     style="clip-path:polygon(6px 0%,100% 0%,calc(100% - 6px) 100%,0% 100%)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="#BBBBBB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ml-1">
                    @csrf
                    <button type="submit"
                            class="font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] hover:text-white tracking-widest uppercase transition-colors cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-2 md:hidden">
                <span class="hidden sm:inline max-w-[90px] truncate font-['Inter'] text-xs text-[#BBBBBB]">{{ Auth::user()->name }}</span>
                <div class="w-9 h-9 bg-[#2a2a2a] border border-white/10 flex items-center justify-center"
                     style="clip-path:polygon(6px 0%,100% 0%,calc(100% - 6px) 100%,0% 100%)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="#BBBBBB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] hover:text-white tracking-widest uppercase transition-colors cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div id="mobile-nav-menu" class="hidden border-t border-white/20 bg-black/95 md:hidden z-50">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <nav class="flex flex-col gap-1">
                    @foreach($navTabs as $tab)
                        @php $isActive = request()->routeIs($tab['match']); @endphp
                        <a href="{{ route($tab['route']) }}"
                           class="rounded-sm px-3 py-3 font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-sm transition-colors {{ $isActive ? 'bg-[#E10600]/[0.12] text-white' : 'text-[#BBBBBB] hover:bg-white/5 hover:text-white' }}">
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
    </header>
    @endauth

    {{-- Page content --}}
    <main class="{{ Auth::check() ? 'pt-[72px]' : '' }}">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.getElementById('mobile-nav-toggle');
            var menu = document.getElementById('mobile-nav-menu');
            if (!toggle || !menu) return;

            toggle.addEventListener('click', function () {
                var isHidden = menu.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
            });

            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                    menu.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
