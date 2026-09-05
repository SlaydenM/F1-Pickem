<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Race;

class LegacyDataTransferSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting legacy data transfer...');

        // 1. EXTRACT: Pull all records from the old database
        $oldUsers = DB::connection('f1db')->table('users')->get();

        // 2. TRANSFORM & LOAD: Loop through and map to the new structure
        foreach ($oldUsers as $oldUser) {
            User::create([
                'name'     => $oldUser->name, // Mapping old column to new column
                'email'    => "",
                'password' => ""
            ]);
        }
        
        $this->command->info('Users transferred successfully!');

        // Repeat the process for Races, Drivers, Picks, etc.
        // Make sure to transfer parent tables (Users/Races) before child tables (Picks) 
        // so foreign keys match up!


        // 1. EXTRACT: Pull all records from the old database
        $oldRaces = DB::connection('f1db')->table('races')->get();

        // 2. TRANSFORM & LOAD: Loop through and map to the new structure
        foreach ($oldRaces as $oldRace) {
            Race::create([
                'session_key' => $oldRace->sessionKey, // Mapping old column to new column
                'date_start'  => $oldRace->dateStart,
                'name'        => $oldRace->name,
                'type'        => $oldRace->type
            ]);
        }
        
        $this->command->info('Races transferred successfully!');
        
        // 1. EXTRACT: Pull all records from the old database
        $oldDrivers = DB::connection('f1db')->table('drivers')->get();

        // 2. TRANSFORM & LOAD: Loop through and map to the new structure
        foreach ($oldDrivers as $oldDriver) {
            Driver::create([
                'name'   => $oldDriver->name, // Mapping old column to new column
                'team'   => $oldDriver->team, // Mapping old column to new column
                'number' => $oldDriver->number, // Mapping old column to new column
                'year'   => $oldDriver->year, // Mapping old column to new column
            ]);
        }
        
        $this->command->info('Drivers transferred successfully!');
    }
}
