<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSettings;

class Settings extends Component
{
    public $user;
    public $settings;
    
    // Define public properties that match your wire:model names
    public $all_notifications;
    public $notify_picks;
    public $notify_races;
    public $notify_results;
    public $notify_others;

    public function mount()
    {
        $this->user = Auth::user();
        $this->settings = UserSettings::where('user_id', Auth::id())->first();
        $this->settings->phone = $this->formatPhoneNumber($this->settings->phone);

        // Hydrate the properties with the user's current settings
        $this->notify_picks = $this->settings->notify_picks ?? 3;
        $this->notify_races = $this->settings->notify_races ?? 3;
        $this->notify_results = $this->settings->notify_results ?? 1;
        $this->notify_others = $this->settings->notify_others ?? 1;
        $this->all_notifications = $this->settings->notify_picks || $this->settings->notify_races || $this->settings->notify_results || $this->settings->notify_others ? 1 : 0; 
    }

    /**
     * The $propertyName parameter automatically receives the name of the 
     * property that was just updated from the frontend.
     */
    public function updated($propertyName)
    {
        if ($propertyName === 'all_notifications') {
            if ($this->all_notifications) {
                // If "All Notifications" is turned off, disable all other notifications
                $this->notify_picks = 3;
                $this->notify_races = 3;
                $this->notify_results = 1;
                $this->notify_others = 1;
            } else {
                // If "All Notifications" is turned on, enable all other notifications  
                $this->notify_picks = 0;
                $this->notify_races = 0;
                $this->notify_results = 0;
                $this->notify_others = 0;
            }

            // Save all cascaded changes to the database immediately
            $this->settings->update([
                'notify_picks' => $this->notify_picks,
                'notify_races' => $this->notify_races,
                'notify_results' => $this->notify_results,
                'notify_others' => $this->notify_others,
            ]);
            
            return; // Stop execution here for this property
        }

        // Define which properties are allowed to be updated this way
        // to prevent mass assignment vulnerabilities
        $allowedProperties = [
            'notify_picks',
            'notify_races',
            'notify_results',
            'notify_others',
        ];

        if (in_array($propertyName, $allowedProperties)) {
            $this->settings->update([
                $propertyName => $this->$propertyName
            ]);

            if ($this->$propertyName > 0) {
                $this->all_notifications = 1; // Turn on "All Notifications" if any individual notification is enabled
            }
        }
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-digit characters
        $digits = preg_replace('/\D/', '', $phoneNumber);

        // Format the phone number as +X (XXX) XXX-XXXX
        return sprintf('+%s (%s) %s-%s',
            substr($digits, 0, 1),
            substr($digits, 1, 3),
            substr($digits, 4, 3),
            substr($digits, 7));
    }

    public function render()
    {
        // Ensure your blade file from the previous step is named appropriately 
        // (e.g., resources/views/settings.blade.php)
        return view('settings')
            ->extends('layouts.app')
            ->section('content');
    }
}