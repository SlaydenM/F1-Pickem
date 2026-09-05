<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSettings;

// NOT IN USE
class SettingsController extends Controller
{
    public function index()
    {
        $settings = UserSettings::where('user_id', Auth::id())->first();
        $settings->phone = $this->formatPhoneNumber($settings->phone);
        return view('settings', compact('settings'));
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-digit characters
        $digits = preg_replace('/\D/', '', $phoneNumber);
        // $digits = $phoneNumber; // Assuming the phone number is already in the correct format

        // Format the phone number as +X (XXX) XXX-XXXX
        // if (strlen($digits) === 10) {  
        return sprintf('+%s (%s) %s-%s',
            substr($digits, 0, 1),
            substr($digits, 1, 3),
            substr($digits, 4, 3),
            substr($digits, 7));
        // }

        // Return the original phone number if it doesn't have 10 digits
        // return $phoneNumber;
    }
}
