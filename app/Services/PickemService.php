<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Pick;
use App\Models\Race;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Services\SmsService;
use Exception;
use Illuminate\Support\Facades\Log;

class PickemService
{
    // ── Year helpers ─────────────────────────────────────────────────────────

    public function getYear(int $sessionKey): int
    {
        return floor($sessionKey / 1000) + 2023;
    }

    public function getSessionKey(): int
    {
        $targetDate = Carbon::now('America/Chicago')->addHours(4);

        $nextRace = Race::where('date_start', '>=', $targetDate)->min('session_key');

        if ($nextRace) {
            return $nextRace;
        }

        return Race::max('session_key') ?? 1000;
    }

    public function getLatestSessionKey(int $sessionKey): int
    {
        return Winner::where('session_key', '<=', $sessionKey)
            ->max('session_key') ?? $sessionKey;
    }

    // ── Data fetchers ────────────────────────────────────────────────────────

    public function getDrivers(int $year): Collection
    {
        return Driver::where('year', $year)->get();
    }

    public function getPlayer(int $userId): ?User
    {
        return User::with('picks')->find($userId);
    }

    public function getPlayers(int $year): Collection
    {
        $startKey = ($year - 2023) * 1000;
        $endKey   = $startKey + 999;

        $players = User::whereHas('picks', function ($query) use ($startKey, $endKey) {
            $query->whereBetween('session_key', [$startKey, $endKey]);
        })->with(['picks' => function ($query) use ($startKey, $endKey) {
            $query->whereBetween('session_key', [$startKey, $endKey]);
        }])->get();

        return $players->map(function ($player) use ($year) {
            $player->total_score = $player->getTotal($year);
            return $player;
        })->sortByDesc('total_score')->values();
    }

    public function getPicks(int $sessionKey): Collection
    {
        return Pick::with('user', 'd1', 'd2', 'd3')
            ->where('session_key', $sessionKey)
            ->get();
    }

    public function getRankChanges(int $sessionKey, Collection $players): array
    {
        if ($sessionKey % 1000 == 0) {
            return [];
        }

        // 1. Current year standings
        // Using ->values() resets the keys to 0, 1, 2... establishing the actual rank
        $totalStandings = $players
            ->sortByDesc('total_score')
            ->values()
            ->map(function ($player) {
                return [
                    'id'    => $player->id,
                    'score' => $player->total_score,
                ];
            });

        // Fetch the picks for the week you are subtracting
        $latestPicks = $this->getPicks($sessionKey - 1); 

        // 2. Previous standings
        // Map over $totalStandings to ensure ALL players are included, even if they missed a pick
        $prevStandings = $totalStandings
            ->map(function ($player) use ($latestPicks) {
                $pick = $latestPicks->firstWhere('user_id', $player['id']);
                $sessionScore = $pick ? $pick->score : 0;
                
                return [
                    'id'    => $player['id'],
                    'score' => max(0, $player['score'] - $sessionScore), // Prevent negative scores
                ];
            })
            ->sortByDesc('score')
            ->values(); // Reset keys again to establish the previous ranks

        // 3. Subtract current and previous standings to get the diff
        $diff = $totalStandings->mapWithKeys(function ($player, $key) use ($prevStandings) {
            // Because of ->values() earlier, $key + 1 represents the true current rank
            $currRank = $key + 1; 
            
            $prevRankIndex = $prevStandings->search(fn ($p) => $p['id'] === $player['id']);
            
            // Safely check if false before doing math
            $prevRank = $prevRankIndex !== false ? $prevRankIndex + 1 : $currRank; 
            
            return [
                $player['id'] => $prevRank - $currRank
            ];
        });

        return $diff->toArray();
    }

    /**
     * Get the winners either from db or from the web
     */
    public function getWinners(int $sessionKey): Collection
    {
        $winners = Winner::with('driver')
            ->where('session_key', $sessionKey)
            ->orderBy('position', 'asc')
            ->get();
        if ($winners->isEmpty()) {
            $this->pullWinners($sessionKey);
            
            // Re-fetch from DB after inserting
            $winners = Winner::with('driver')
                ->where('session_key', $sessionKey)
                ->orderBy('position', 'asc')
                ->get();

            // Send notifications to users who opted in for results
            $this->getSmsService()->notifyResults();
        }
        return $winners;
    }

    /** All Race rows for a weekend (ordered chronologically — used for bonus tiers) */
    public function getSessionRaces(int $sessionKey): Collection
    {
        return Race::where('session_key', $sessionKey)
            ->orderBy('date_start')
            ->get();
    }

    /** Alias matching the old controller call */
    public function getMainRace(int $sessionKey): ?object
    {
        return $this->getRace($sessionKey);
    }

    public function getRace(int $sessionKey): ?object
    {
        return Race::where('session_key', $sessionKey)->where('type', 'G')->first();
    }

    // ── Scoring ──────────────────────────────────────────────────────────────

    public function updateScores(int $sessionKey): void
    {
        $year = $this->getYear($sessionKey);
        $winners = $this->getWinners($sessionKey);
        $players = $this->getPlayers($year);

        if ($winners->isEmpty() || $players->isEmpty()) return;

        $this->evaluatePicks($winners, $players, $sessionKey);
    }

    public function evaluatePicks(Collection $winners, Collection $players, int $sessionKey): void
    {
        $firstPlaceId = $winners->where('position', 1)->first()?->driver_id;
        $tenthPlaceId = $winners->where('position', 10)->first()?->driver_id;
        $lastPlaceId  = $winners->sortByDesc('position')->first()?->driver_id;

        foreach ($players as $player) {
            $pick = $player->picks->where('session_key', $sessionKey)->first();
            if (! $pick) continue;

            $score = 0;
            if ($pick->d1_id === $firstPlaceId) $score += 7;
            if ($pick->d2_id === $tenthPlaceId) $score += 5;
            if ($pick->d3_id === $lastPlaceId)  $score += 3;

            $player->updateScore($sessionKey, $score * $pick->bonus);
        }
    }

    public function getCorrectBets(Collection $winners, Collection $picks): array
    {
        if ($winners->isEmpty()) return [];

        $first = $winners->where('position', 1)->first()?->driver_id;
        $tenth = $winners->where('position', 10)->first()?->driver_id;
        $last  = $winners->sortByDesc('position')->first()?->driver_id;

        $correctBets = [];
        foreach ($picks as $pick) {
            $correctBets[] = [
                $pick->d1_id == $first ? 1 : 0,
                $pick->d2_id == $tenth ? 1 : 0,
                $pick->d3_id == $last  ? 1 : 0,
            ];
        }
        return $correctBets;
    }

    // ── API sync ─────────────────────────────────────────────────────────────
    public function insertWinners(int $sessionKey): void
    {
        if (Winner::where('session_key', $sessionKey)->exists()) return;

        $year    = $this->getYear($sessionKey);
        $raceKey = $sessionKey % 1000;

        $response = Http::withUserAgent('F1-Data-Fetcher/2.0')
            ->get("https://api.jolpi.ca/ergast/f1/{$year}/{$raceKey}/results.json");

        if (! $response->successful()) return;

        $races = $response->json('MRData.RaceTable.Races');
        if (empty($races)) return;

        $results  = $races[0]['Results'] ?? [];
        $dbDrivers = Driver::where('year', $year)->get()->keyBy('number');

        foreach ($results as $result) {
            $f1Number = (int) $result['number'];
            $position = (int) $result['position'];
            $driver   = $dbDrivers->get($f1Number);

            if ($driver) {
                Winner::create([
                    'driver_id'   => $driver->id,
                    'position'    => $position,
                    'session_key' => $sessionKey,
                ]);
            }
        }
    }

    public function updateSchedule(int $year): void
    {   
        // Fetch the schedule from the Ergast API for the given year
        // For each race, check if it exists in the database; if not, insert it
        // For existing sessions, update the date/time if it has changed

        $response = Http::withUserAgent('F1-Schedule-App/2.0')
            ->get("https://api.jolpi.ca/ergast/f1/{$year}.json");

        if (! $response->successful()) return;

        $races = $response->json('MRData.RaceTable.Races', []);

        $apiSessionKeys = [
            'FirstPractice'    => 'FP1', 'SecondPractice' => 'FP2',
            'ThirdPractice'    => 'FP3', 'Qualifying'     => 'Q',
            'SprintQualifying' => 'SQ',  'SprintShootout' => 'SQ',
            'Sprint'           => 'S',   'GrandPrix'      => 'G',
        ];

        foreach ($races as $race) {
            $sessionKey = ($year - 2023) * 1000 + (int) $race['round'];
            $raceName   = $race['raceName'];

            if (isset($race['date'])) {
                $time = $race['time'] ?? '00:00:00Z';
                $this->upsertRace($sessionKey, 'G', $raceName, $race['date'] . 'T' . $time);
            }

            foreach ($apiSessionKeys as $apiKey => $type) {
                if (isset($race[$apiKey])) {
                    $time = $race[$apiKey]['time'] ?? '00:00:00Z';
                    $this->upsertRace($sessionKey, $type, $raceName, $race[$apiKey]['date'] . 'T' . $time);
                }
            }
        }
    }

    private function upsertRace(int $sessionKey, string $type, string $name, string $dateStart): void
    {
        Race::updateOrCreate(
            ['session_key' => $sessionKey, 'type' => $type],
            ['name' => $name, 'date_start' => Carbon::parse($dateStart)]
        );
    }

    // ── Notifications ────────────────────────────────────────────────────────
    public function getSmsService(): SmsService
    {
        return new SmsService();
    }

    // ── API Helpers ──────────────────────────────────────────────────────────────
    /**
     * 1. Pull winners from the Jolpica F1 API and insert them into the database.
     * 2. Insert new winners into the database.
     * 3. Update scores for all players based on the new winners.
     *
     * @param int $sessionKey The session key for which to pull winners.
     * @throws Exception If the API request fails or returns an unexpected response.
     */
    function pullWinners(int $sessionKey): void
    {
        $newWinners = $this->getNewWinners($sessionKey);

        // Insert new winners into the database
        foreach ($newWinners as $row) {
            $driver = Driver::where('year', $this->getYear($sessionKey))
                ->where('number', $row['number'])
                ->first();
            if (! $driver) continue;
            Winner::create([
                'driver_id'   => $driver->id,
                'position'    => $row['position'],
                'session_key' => $sessionKey,
            ]);
        }

        $this->updateScores($sessionKey);
    }

    function getNewWinners(int $sessionKey): array
    {
        // Fetch the winners from the Jolpica F1 API for the given session key
        // Return list of driver numbers in order of their finishing positions

        $year = $this->getYear($sessionKey);
        $raceKey = $sessionKey % 1000;

        // Utilizing the Jolpica F1 API, the direct continuation of Ergast F1
        $url = "https://api.jolpi.ca/ergast/f1/{$year}/{$raceKey}/results.json";

        // Send HTTP GET request with custom User-Agent
        $response = Http::withUserAgent('F1-Data-Fetcher/1.0')->get($url);

        // Verify response status
        if ($response->failed() || $response->status() !== 200) {
            throw new Exception("Failed to fetch data from API. HTTP Status Code: {$response->status()}");
        }

        $data = $response->json();
        $winners = new Collection();

        // Navigate the JSON response structure
        $races = $data['MRData']['RaceTable']['Races'] ?? [];

        // If the race hasn't happened yet or the round is invalid
        if (empty($races)) {
            return $winners->all(); // Return an empty collection
        }

        $results = $races[0]['Results'] ?? [];

        foreach ($results as $result) {
            // $name = $result['Driver']['givenName'] . ' ' . $result['Driver']['familyName'];
            // $team = $result['Constructor']['name'];
            $number = (int) $result['number'];
            $position = (int) $result['position'];

            $winners->push([
                'number'   => $number,
                'position' => $position,
            ]);

            // $winners[] = new Driver($name, $team, $number, $position, getYear($sessionKey));
        }

        return $winners->sortBy('position')->values()->all();
    }

    function pingResultsApi(): bool 
    {
        $sessionKey = $this->getSessionKey() + 1; // Check the next session key for results
        $year = $this->getYear($sessionKey);
        $raceKey = $sessionKey % 1000;

        // Utilizing the Jolpica F1 API, the direct continuation of Ergast F1
        $url = "https://api.jolpi.ca/ergast/f1/{$year}/{$raceKey}/results.json";
        
        // Send HTTP GET request with custom User-Agent
        $response = Http::withUserAgent('F1-Data-Fetcher/1.0')->get($url);

        // Verify response status
        if ($response->failed() || $response->status() !== 200) {
            // throw new Exception("Failed to fetch data from API. HTTP Status Code: {$response->status()}");
            return false;
        }

        // If successful, attempt to fetch winners to update the database
        try {
            $this->getWinners($sessionKey);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error("Error fetching winners for session {$sessionKey}: " . $e->getMessage());
            return false;
        }
        return true;
    }
}
