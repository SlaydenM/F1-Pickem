@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-sm">

        {{-- Title --}}
        <div class="mb-8 text-center">
            <h1 class="font-['Barlow_Condensed'] font-black italic text-5xl text-white uppercase tracking-tight leading-none">
                F1 Pick'em
            </h1>
            <div class="font-['JetBrains_Mono'] text-[#BBBBBB] text-[10px] tracking-widest mt-2 uppercase">
                Season Predictions League
            </div>
            <div class="mt-3 mx-auto h-[2px] w-16 bg-[#E10600]"></div>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="mb-4 border border-[#E10600]/30 px-4 py-3"
                 style="background:#160500;border-radius:2px">
                <span class="font-['Inter'] text-sm text-[#E10600]">{{ $errors->first() }}</span>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-4 bg-[#0d1a0d] border border-green-900/40 px-4 py-3"
                 style="border-radius:2px">
                <span class="font-['Inter'] text-sm text-green-400">{{ session('status') }}</span>
            </div>
        @endif

        {{-- Card --}}
        <div class="bg-[#1c1c1c] border border-white/[0.07] p-8" style="border-radius:2px">
            <div class="font-['Barlow_Condensed'] font-bold uppercase tracking-widest text-xs text-[#BBBBBB] mb-6">
                Sign In
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] uppercase tracking-widest mb-2">
                        Username
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required autocomplete="username"
                           class="w-full bg-[#141414] border border-white/[0.08] px-4 py-3 text-white font-['Inter'] text-sm
                                  focus:outline-none focus:border-[#E10600]/50 transition-colors"
                           style="border-radius:2px">
                </div>

                <div>
                    <label class="block font-['JetBrains_Mono'] text-[10px] text-[#BBBBBB] uppercase tracking-widest mb-2">
                        Password
                    </label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full bg-[#141414] border border-white/[0.08] px-4 py-3 text-white font-['Inter'] text-sm
                                  focus:outline-none focus:border-[#E10600]/50 transition-colors"
                           style="border-radius:2px">
                </div>

                <button type="submit"
                        class="w-full bg-[#E10600] text-white font-['Barlow_Condensed'] font-black italic uppercase
                               text-lg py-3 hover:bg-[#ff0a00] transition-colors cursor-pointer mt-2"
                        style="clip-path:polygon(8px 0%,100% 0%,calc(100% - 8px) 100%,0% 100%)">
                    Enter
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
