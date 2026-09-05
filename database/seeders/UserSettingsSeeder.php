<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserSettings;

class UserSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $this->command->info('Starting seeder ...');

        User::all()->each(function ($user) {
            UserSettings::create([
                'user_id' => $user->id,
                'notify_picks' => 4, // Default to all
                'notify_races' => 4, // Default to all
                'notify_results' => true, // Default to enabled
                'notify_others' => true, // Default to enabled
                'phone' => '+10000000000', // Default phone number
            ]);
        });
    }
}
