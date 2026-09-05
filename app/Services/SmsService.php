<?php

namespace App\Services;

use App\Jobs\SmsJob;
use App\Models\Race;
use App\Models\User;
use App\Models\UserSettings;
use App\Models\Picks;
use Carbon\Carbon;

class SmsService
{
    /**
     * Get all race sessions scheduled for today.
     */
    public function getRacesToday()
    {
        return Race::whereDate('date_start', today())->get();
    }

    /**
     * Run daily to evaluate schedule & user pick status to queue SMS notifications.
     */
    public function notifySchedule(): void
    {
        $races = $this->getRacesToday();
        if ($races->isEmpty()) {
            return;
        }

        // Fetch users along with their settings
        $users = User::with('settings')->get();

        $bonuses = [
            ['FP3' => '+50%'],
            ['FP2' => '+25%'],
            ['FP3' => '+10%'],
            ['Q' => '+0%'],
        ];

        foreach ($users as $user) {
            $settings = $user->settings;
            if (!$settings) continue;

            foreach ($races as $race) {
                // Check if user has submitted picks for this session
                $hasSubmittedPicks = Picks::where('user_id', $user->id)
                    ->where('session_key', $race->session_key)
                    ->exists();

                $raceTime = Carbon::parse($race->date_start);

                if (!$hasSubmittedPicks) {
                    // Logic: Remind Picks (if user setting enables it)
                    if ($settings->notify_picks > 0) {
                        $pickTime = $raceTime->copy()->subHours(1);
                        if ($pickTime->isFuture()) {
                            SmsJob::dispatch(
                                $user->phone,
                                "F1 Pick'em:\n" .
                                "{$race->name} is about to begin at {$raceTime->format('g:i A')}. \n" .
                                "You can still get a {$bonuses[$race->type]}% bonus!" .
                                "Submit picks:\n" .
                                "https://f1pickem.net/next-race"
                                // "Reminder: Submit your picks for {$race->name} before it starts!"
                            )->delay($pickTime);
                        }
                    }
                } else {
                    // Logic: Picks submitted -> Remind Race (if user setting enables it)
                    if ($settings->notify_races > 0) {
                        $raceNotifyTime = $raceTime->copy()->subMinutes(30);
                        if ($raceNotifyTime->isFuture()) {
                            SmsJob::dispatch(
                                $user->phone,
                                "Get ready! \n" .
                                "{$race->name} is starting soon at {$raceTime->format('g:i A')}."
                            )->delay($raceNotifyTime);
                        }
                    }
                }
            }
        }
    }

    /**
     * Triggered immediately when a player submits picks, to notify other players.
     */
    public function notifyOthers(User $submittingUser, float $bonus, int $sessionKey): void
    {
        // Find users who opted into notify_others (excluding the submitting user)
        $others = UserSettings::where('notify_others', true)
            ->where('user_id', '!=', $submittingUser->id)
            // ->where('user_id', 3)
            ->get();

        $bonusPerc = ($bonus) ? ((int) (($bonus - 1.0) * 100)) : 0;
        foreach ($others as $user) {
            SmsJob::dispatch(
                $user->phone,
                "F1 Pick'em:\n" .
                "{$submittingUser->name} has just submitted their picks" .
                " with a {$bonusPerc}% bonus.\n" .
                "\n" .
                "https://f1pickem.net/next-race"
            );
        }
    }

    /**
     * Called on confirmation that results are available after GP.
     */
    public function notifyResults(): void
    {
        // Look for today's Grand Prix (GP) session
        $gpRace = Race::whereDate('date_start', today())
            ->where('type', 'GP')
            ->first();

        if (!$gpRace) return;

        // Schedule message for after the race (e.g., 2 hours after date_start)
        // $endTime = Carbon::parse($gpRace->date_start)->addHours(4);
        // Send immediately

        $users = User::whereHas('settings', fn($q) => $q->where('notify_results', true))->get();

        foreach ($users as $user) {
            SmsJob::dispatch(
                $user->phone,
                "{$user->name}, results for {$gpRace->name} are now available!\n" .
                "You have won {$user->getPicks($gpRace->session_key)->points} points.\n" .
                "\n" .
                "Check your standings:\n" .
                "https://f1pickem.net/"
            );
        }
    }

    public function optIn() {
        // return "F1 Pick'em Text Messaging Service: 
        //     By opting in, you agree to receive text messages from 
        //     F1 Pick'em regarding upcoming races, reminders to submit your picks, 
        //     and results after the Grand Prix. 

        //     Reply START to opt in.";
        SmsJob::dispatch(
            $user->phone,
            "You have successfully opted in to receive text messages from F1 Pick'em." .
            "\n".
            "You can manage your notification preferences in the" .
            "settings section of your account:" .
            "https://f1pickem.net/settings" .
            "\n".
            "Reply STOP to opt out at any time."
        );
    }

    public function optOut() {
        SmsJob::dispatch(
            $user->phone,
            "You have successfully been unsubscribed." .
            "You will not receive any more messages from this number. " .
            "Reply START to resubscribe."
        );
    }
}