<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Driver;
use App\Models\Pick;
use App\Models\Winner;
use App\Models\Race;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LegacyDataTransferSeeder3 extends Seeder
{
    public function run()
    {
        $this->command->info('Starting legacy 3 data transfer...');

        // 1. TRANSFER USERS & CREATE ID MAPPING
        $oldUsers = DB::connection('mysql_legacy')->table('users')->get();
        $userMap = []; // Maps Old ID -> New ID

        // $newUsers = User::all(); // Fetch all new users to map old IDs to new IDs

        foreach ($oldUsers as $oldUser) {

            $userMap[$oldUser->userID] = $oldUser->userID;
        }

        $driverMap = []; // Maps [year][F1 Number] -> New Driver ID
        $drivers = Driver::all(); // Fetch all new drivers to map old F1 numbers to new IDs

        foreach ($drivers as $d) {
            // Ensure the year array exists
            if (!isset($driverMap[$d->year])) {
                $driverMap[$d->year] = [];
            }
            // Map by the driver's F1 number so lookups by number work correctly
            $driverMap[$d->year][(int)$d->number] = $d->id;
        }
        // $this->command->info('Drivers seeded and mapped.');

        // 3. TRANSFER PICKS (Formerly 'bettors')
        // We split the "12,1,82" string and map F1 numbers to the new driver_ids
        $oldBettors = DB::connection('mysql_legacy')->table('bettors')->where('sessionKey', '>=', 3010)->get();

        foreach ($oldBettors as $bettor) {
            // Split the string "12,1,82" into an array [12, 1, 82]
            $betNumbers = explode(',', $bettor->bets);

            if (count($betNumbers) >= 3) {
                $year = floor($bettor->sessionKey / 1000) + 2023;
                Pick::create([
                    // Look up the new User ID using the old primitive ID
                    'user_id' => $userMap[$bettor->userID] ?? null, 
                    
                    // Look up the new Driver IDs using the primitive F1 numbers
                    'd1_id' => $driverMap[$year][(int)$betNumbers[0]] ?? null,
                    'd2_id' => $driverMap[$year][(int)$betNumbers[1]] ?? null,
                    'd3_id' => $driverMap[$year][(int)$betNumbers[2]] ?? null,
                    
                    // Defaults for columns not mentioned in the old DB
                    'score' => $bettor->score ?? 0, 
                    'bonus' => $bettor->bonus ?? 0,
                    'session_key' => $bettor->sessionKey ?? 1, 
                ]);
            }
        }
        $this->command->info('Picks transferred successfully.');

        // 4. TRANSFER WINNERS (Formerly ordered results in 'drivers')
        // $oldResults = DB::connection('mysql_legacy')->table('drivers')->where('sessionKey', '>=', 3010)->get();

        // foreach ($oldResults as $resultRow) {
        //     $year = floor($resultRow->sessionKey / 1000) + 2023;
        //     Winner::create([
        //         'driver_id' => $driverMap[$year][(int)$resultRow->number] ?? null,
        //         'position' => $resultRow->position, // Array index 0 = 1st place, etc.
        //         'session_key' => $resultRow->sessionKey ?? 1,
        //     ]);
        // }
        // $this->command->info('Winners transferred successfully.');

        // Races
        // $oldRaces = DB::connection('mysql_legacy')->table('races')->get();

        // foreach ($oldRaces as $oldRace) {
        //     Race::create([
        //         'session_key' => $oldRace->sessionKey,
        //         'date_start'  => $oldRace->dateStart,
        //         'name'        => $oldRace->name,
        //         'type'        => $oldRace->type
        //     ]);
        // }

        // $this->command->info('Winners transferred successfully. ETL Complete!');
    }
}