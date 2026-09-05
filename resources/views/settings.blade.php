<div class="min-h-screen">
    <div class="max-w-3xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <div class="w-1 h-10 bg-[#E10600]"></div>
            <h1 class="font-['Barlow_Condensed'] font-black italic text-4xl text-white tracking-tight uppercase">
                User Settings
            </h1>
        </div>

        <div class="space-y-4">
            
            {{-- Notifications Card --}}
            <div class="relative bg-[#1c1c1c] border border-white/[0.07] overflow-hidden">
                <div class="absolute left-0 inset-y-0 w-[3px] bg-[#E10600]"></div>
                
                <div class="px-6 py-5">
                    <h2 class="font-['Barlow_Condensed'] font-black italic text-[#E10600] text-xl uppercase tracking-wide mb-3">
                        Notifications
                    </h2>
                    
                    <ul class="space-y-1 mb-5">
                        <x-settings-item 
                            label="All notifications" 
                            model="all_notifications" 
                            :options="[1 => 'On', 0 => 'Off']" 
                        />
                        
                        @if($all_notifications)
                            <x-settings-item 
                                label="Remind me to submit picks" 
                                model="notify_picks" 
                                :options="[
                                    'Off',
                                    'Before Qualifying',
                                    'Before Grand Prix',
                                    'Before every race'
                                ]" 
                            />
                            
                            <x-settings-item 
                                label="Remind me when races start" 
                                model="notify_races" 
                                :options="[
                                    'Off',
                                    'Before Qualifying',
                                    'Before Grand Prix',
                                    'Before every race' 
                                ]" 
                            />
                            
                            <x-settings-item 
                                label="Notify me when results are out" 
                                model="notify_results" 
                                :options="[1 => 'On', 0 => 'Off']" 
                            />
                            
                            <x-settings-item 
                                label="Notify when others submit picks" 
                                model="notify_others" 
                                :options="[1 => 'On', 0 => 'Off']" 
                            />
                        @else
                            <li class="text-[#BBBBBB]/50 text-sm">
                                By enabling notifications, you consent to receive 
                                SMS notifications from F1 Pick'em regarding race 
                                schedules, game results, and other relavent information. 
                                Reply STOP to opt out.
                            </li>
                        @endif
                    </ul>

                    {{-- Footer Label --}}
                    <div class="pt-4 border-t border-white/[0.07]">
                        @if ($settings->phone && $settings->phone != '+1 (000) 000-0000')
                            <p class="font-['Inter'] text-[#BBBBBB]/50 text-xs">
                                Notifications sent to {{ $settings->phone }}. 
                            </p>
                            <p class="font-['Inter'] text-[#BBBBBB]/50 text-xs mt-2">
                                Reply STOP to any text message to unsubscribe. 
                            
                            </p>
                        @else
                            <p class="font-['Inter'] text-[#BBBBBB]/50 text-xs">
                                You haven't set a phone number yet. Contact the administrator to add your phone number to receive notifications.
                            </p>
                        @endif
                        
                        <p class="font-['Inter'] text-[#BBBBBB]/50 text-xs mt-2">
                            For more information, see our 
                            <a href="https://f1pickem.net/pages/privacy-policy.html" class="underline text-[#E10600]">Privacy Policy</a> and
                            <a href="https://f1pickem.net/pages/terms-conditions.html" class="underline text-[#E10600]">Terms and Conditions</a>.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>