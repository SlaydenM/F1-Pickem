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

class LegacyDataTransferSeeder2 extends Seeder
{
    public function run()
    {
        $this->command->info('Starting legacy data transfer...');

        // 1. TRANSFER USERS & CREATE ID MAPPING
        $oldUsers = DB::connection('mysql_legacy')->table('users')->get();
        $userMap = []; // Maps Old ID -> New ID

        foreach ($oldUsers as $oldUser) {
            $newUser = User::create([
                'name' => $oldUser->username ?? 'Unknown',
                'email' => $oldUser->email ?? Str::random(5) . '@example.com', // Fallback email if missing
                'password' => Hash::make($oldUser->password), 
                'email_verified_at' => now()
            ]);
            $userMap[$oldUser->userID] = $newUser->id;
        }
        $this->command->info('Users transferred.');

        // 2. POPULATE THE NEW DRIVERS TABLE
        // Since the old DB only used raw numbers, we must populate the new drivers table 
        // to generate the actual foreign key IDs.
        $contestants = [
            // 2026 Season
            ['name' => 'Lando Norris',       'team' => 'MCLAREN',       'number' => 1,   'year' => 2026],
            ['name' => 'Oscar Piastri',      'team' => 'MCLAREN',       'number' => 81,  'year' => 2026],
            ['name' => 'George Russell',     'team' => 'MERCEDES',      'number' => 63,  'year' => 2026],
            ['name' => 'Andrea Antonelli',   'team' => 'MERCEDES',      'number' => 12,  'year' => 2026],
            ['name' => 'Max Verstappen',     'team' => 'RED BULL',      'number' => 3,   'year' => 2026],
            ['name' => 'Isack Hadjar',       'team' => 'RED BULL',      'number' => 6,   'year' => 2026],
            ['name' => 'Lewis Hamilton',     'team' => 'FARRARI',       'number' => 44,  'year' => 2026],
            ['name' => 'Charles Leclerc',    'team' => 'FERRARI',       'number' => 16,  'year' => 2026],
            ['name' => 'Alexander Albon',    'team' => 'WILLIAMS',      'number' => 23,  'year' => 2026],
            ['name' => 'Carlos Sainz',       'team' => 'WILLIAMS',      'number' => 55,  'year' => 2026],
            ['name' => 'Oliver Bearman',     'team' => 'HAAS',          'number' => 87,  'year' => 2026],
            ['name' => 'Esteban Ocon',       'team' => 'HAAS',          'number' => 31,  'year' => 2026],
            ['name' => 'Lance Stroll',       'team' => 'ASTON MARTIN',  'number' => 18,  'year' => 2026],
            ['name' => 'Fernando Alonso',    'team' => 'ASTON MARTIN',  'number' => 14,  'year' => 2026],
            ['name' => 'Pierre Gasly',       'team' => 'ALPINE',        'number' => 10,  'year' => 2026],
            ['name' => 'Franco Colapinto',   'team' => 'ALPINE',        'number' => 43,  'year' => 2026],
            ['name' => 'Liam Lawson',        'team' => 'RACING BULLS',  'number' => 30,  'year' => 2026],
            ['name' => 'Arvid Lindblad',     'team' => 'RACING BULLS',  'number' => 41,  'year' => 2026],
            ['name' => 'Nico Hulkenberg',    'team' => 'AUDI',          'number' => 27,  'year' => 2026],
            ['name' => 'Gabriel Bortoletto', 'team' => 'AUDI',          'number' => 5,   'year' => 2026],
            ['name' => 'Valtteri Bottas',    'team' => 'CADILLAC',      'number' => 77,  'year' => 2026],
            ['name' => 'Sergio Perez',       'team' => 'CADILLAC',      'number' => 11,  'year' => 2026],
            ['name' => 'Extra',              'team' => 'Driver',        'number' => 100, 'year' => 2026],

            // 2025 Season
            ['name' => 'Lando Norris',       'team' => 'MCLAREN',       'number' => 4,   'year' => 2025],
            ['name' => 'Oscar Piastri',      'team' => 'MCLAREN',       'number' => 81,  'year' => 2025],
            ['name' => 'Max Verstappen',     'team' => 'RED BULL',      'number' => 1,   'year' => 2025],
            ['name' => 'Yuki Tsunoda',       'team' => 'RED BULL',      'number' => 122, 'year' => 2025], // Switched with Liam 4/1/25 weekend
            ['name' => 'Carlos Sainz',       'team' => 'WILLIAMS',      'number' => 55,  'year' => 2025],
            ['name' => 'Alexander Albon',    'team' => 'WILLIAMS',      'number' => 23,  'year' => 2025],
            ['name' => 'Isack Hadjar',       'team' => 'RACING BULLS',  'number' => 6,   'year' => 2025],
            ['name' => 'Liam Lawson',        'team' => 'RACING BULLS',  'number' => 130, 'year' => 2025], // Switched with Yuki 4/1/25 weekend
            ['name' => 'Pierre Gasly',       'team' => 'ALPINE',        'number' => 10,  'year' => 2025],
            ['name' => 'Franco Colapinto',   'team' => 'ALPINE',        'number' => 143, 'year' => 2025], // Replaced Jack 5/16/25 weekend
            ['name' => 'Fernando Alonso',    'team' => 'ASTON MARTIN',  'number' => 14,  'year' => 2025],
            ['name' => 'Lance Stroll',       'team' => 'ASTON MARTIN',  'number' => 18,  'year' => 2025],
            ['name' => 'Charles Leclerc',    'team' => 'FERRARI',       'number' => 16,  'year' => 2025],
            ['name' => 'Lewis Hamilton',     'team' => 'FARRARI',       'number' => 44,  'year' => 2025],
            ['name' => 'Oliver Bearman',     'team' => 'HAAS',          'number' => 87,  'year' => 2025],
            ['name' => 'Esteban Ocon',       'team' => 'HAAS',          'number' => 31,  'year' => 2025],
            ['name' => 'Nico Hulkenberg',    'team' => 'KICK SAUBER',   'number' => 27,  'year' => 2025],
            ['name' => 'Gabriel Bortoleto',  'team' => 'KICK SAUBER',   'number' => 5,   'year' => 2025],
            ['name' => 'Andrea Antonelli',   'team' => 'MERCEDES',      'number' => 12,  'year' => 2025],
            ['name' => 'George Russell',     'team' => 'MERCEDES',      'number' => 63,  'year' => 2025],
            
            // Outdated drivers 2025
            ['name' => 'Liam Lawson',        'team' => 'RED BULL',      'number' => 30,  'year' => 2025],
            ['name' => 'Yuki Tsunoda',       'team' => 'RACING BULLS',  'number' => 22,  'year' => 2025],
            ['name' => 'Yuki Tsunoda',       'team' => 'RACING BULLS',  'number' => 22,  'year' => 2025],
            ['name' => 'Jack Doohan',        'team' => 'ALPINE',        'number' => 7,   'year' => 2025], // Replaced with Franco 5/16/25 weekend
            ['name' => 'Extra',              'team' => 'Driver',        'number' => 100, 'year' => 2025],

            // 2024 Season
            ['name' => 'Max Verstappen',     'team' => 'RED BULL',      'number' => 1,   'year' => 2024],
            ['name' => 'Sergio Perez',       'team' => 'RED BULL',      'number' => 11,  'year' => 2024],
            ['name' => 'Franco Colapinto',   'team' => 'WILLIAMS',      'number' => 43,  'year' => 2024],
            ['name' => 'Alexander Albon',    'team' => 'WILLIAMS',      'number' => 23,  'year' => 2024],
            ['name' => 'Liam Lawson',        'team' => 'RACING BULLS',  'number' => 30,  'year' => 2024],
            ['name' => 'Yuki Tsunoda',       'team' => 'RACING BULLS',  'number' => 22,  'year' => 2024],
            ['name' => 'Lando Norris',       'team' => 'MCLAREN',       'number' => 4,   'year' => 2024],
            ['name' => 'Oscar Piastri',      'team' => 'MCLAREN',       'number' => 81,  'year' => 2024],
            ['name' => 'Pierre Gasly',       'team' => 'ALPINE',        'number' => 10,  'year' => 2024],
            ['name' => 'Esteban Ocon',       'team' => 'ALPINE',        'number' => 31,  'year' => 2024],
            ['name' => 'Fernando Alonso',    'team' => 'ASTON MARTIN',  'number' => 14,  'year' => 2024],
            ['name' => 'Lance Stroll',       'team' => 'ASTON MARTIN',  'number' => 18,  'year' => 2024],
            ['name' => 'Charles Leclerc',    'team' => 'FERRARI',       'number' => 16,  'year' => 2024],
            ['name' => 'Carlos Sainz',       'team' => 'FARRARI',       'number' => 55,  'year' => 2024],
            ['name' => 'Kevin Magnussen',    'team' => 'HAAS',          'number' => 20,  'year' => 2024],
            ['name' => 'Nico Hulkenberg',    'team' => 'HAAS',          'number' => 27,  'year' => 2024],
            ['name' => 'Zhou Guanyu',        'team' => 'KICK SAUBER',   'number' => 24,  'year' => 2024],
            ['name' => 'Valtteri Bottas',    'team' => 'KICK SAUBER',   'number' => 77,  'year' => 2024],
            ['name' => 'Lewis Hamilton',     'team' => 'MERCEDES',      'number' => 44,  'year' => 2024],
            ['name' => 'George Russell',     'team' => 'MERCEDES',      'number' => 63,  'year' => 2024],
            
            // Outdated drivers 2024
            ['name' => 'Oliver Bearman',     'team' => 'HAAS',          'number' => 38,  'year' => 2024],
            ['name' => 'Daniel Ricciardo',   'team' => 'RACING BULLS',  'number' => 3,   'year' => 2024],
            ['name' => 'Logan Sargeant',     'team' => 'WILLIAMS',      'number' => 2,   'year' => 2024],
            ['name' => 'Extra',              'team' => 'Driver',        'number' => 100, 'year' => 2024],
        ];

        $driverMap = []; // Maps [year][F1 Number] -> New Driver ID

        foreach ($contestants as $driverData) {
            $newDriver = Driver::create($driverData);
            // Ensure the year array exists
            if (!isset($driverMap[$newDriver->year])) {
                $driverMap[$newDriver->year] = [];
            }
            // Map by the driver's F1 number so lookups by number work correctly
            $driverMap[$newDriver->year][(int)$newDriver->number] = $newDriver->id;
        }
        $this->command->info('Drivers seeded and mapped.');

        // 3. TRANSFER PICKS (Formerly 'bettors')
        // We split the "12,1,82" string and map F1 numbers to the new driver_ids
        $oldBettors = DB::connection('mysql_legacy')->table('bettors')->get();

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
        $oldResults = DB::connection('mysql_legacy')->table('drivers')->get();

        foreach ($oldResults as $resultRow) {
            $year = floor($resultRow->sessionKey / 1000) + 2023;
            Winner::create([
                'driver_id' => $driverMap[$year][(int)$resultRow->number] ?? null,
                'position' => $resultRow->position, // Array index 0 = 1st place, etc.
                'session_key' => $resultRow->sessionKey ?? 1,
            ]);
        }

        // Races
        $oldRaces = DB::connection('mysql_legacy')->table('races')->get();

        foreach ($oldRaces as $oldRace) {
            Race::create([
                'session_key' => $oldRace->sessionKey,
                'date_start'  => $oldRace->dateStart,
                'name'        => $oldRace->name,
                'type'        => $oldRace->type
            ]);
        }

        $this->command->info('Winners transferred successfully. ETL Complete!');
    }
}