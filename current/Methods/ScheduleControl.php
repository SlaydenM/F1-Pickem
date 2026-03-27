<?php
require '../../vendor/autoload.php';
require_once "DBControl.php";
require_once "Session.php";

use GuzzleHttp\Client;

// Scrapes the race schedule from F1 Calendar
function getRaceSchedule($yearKey, $year){
    
    // Start session key
    $sessionKey = $yearKey + 1;

    // Map the keys returned by Jolpica API to your $sessionTypeMap keys
    $apiSessionKeys = [
        'FirstPractice'    => 'FP1',
        'SecondPractice'   => 'FP2',
        'ThirdPractice'    => 'FP3',
        'Qualifying'       => 'Q',
        'SprintQualifying' => 'SQ',
        'SprintShootout'   => 'SQ', // Fallback for legacy Ergast naming
        'Sprint'           => 'S',
        'GrandPrix'       => 'G'
    ];

    $schedule = [];
    $apiUrl = 'https://api.jolpi.ca/ergast/f1/' . $year . '.json';

    // Adding a User-Agent is good practice when making API requests
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => 'User-Agent: F1-Schedule-App/1.0'
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($apiUrl, false, $context);

    if ($response !== false) {
        $data = json_decode($response, true);
        $races = $data['MRData']['RaceTable']['Races'] ?? [];
        
        foreach ($races as $race) {
            // Increment from 3000 based on the race round (Round 1 = 3001)
            $sessionKey = $yearKey + (int)$race['round'];
            $raceName = $race['raceName'];
            
            $weekendSessions = [];
            
            // 1. Extract the main Grand Prix (located at the root of the race object)
            if (isset($race['date'])) {
                $time = $race['time'] ?? '00:00:00Z'; // Fallback to midnight if time is TBD
                $weekendSessions[] = [
                    'sessionKey' => $sessionKey,
                    'dateStart'  => $race['date'] . 'T' . $time, // ISO 8601 Format
                    'name'       => $raceName,
                    'type'       => $apiSessionKeys['GrandPrix']
                ];
            }
            
            // 2. Extract Practice, Qualifying, and Sprint sessions
            foreach ($apiSessionKeys as $apiKey => $sessionType) {
                if (isset($race[$apiKey])) {
                    $sessionData = $race[$apiKey];
                    $time = $sessionData['time'] ?? '00:00:00Z';
                    $weekendSessions[] = [
                        'sessionKey' => $sessionKey,
                        'dateStart'  => $sessionData['date'] . 'T' . $time,
                        'name'       => $raceName,
                        'type'       => $sessionType
                    ];
                }
            }
            
            // 3. Sort the sessions chronologically within the weekend
            usort($weekendSessions, function ($a, $b) {
                return strcmp($a['dateStart'], $b['dateStart']);
            });
            
            // 4. Append the sorted weekend sessions to the main schedule array
            foreach ($weekendSessions as $session) {
                $schedule[] = $session;
            }
        }
    } else {
        echo "Error: Failed to fetch data from the Jolpica API.\n";
    }
    
    return $schedule;
}

// Function to update the database if race times are different
function updateDatabase($conn, $DB_NAME, $schedule, $sessionKey)
{
    foreach ($schedule as $session) {
        $stmt = $conn->prepare("SELECT * FROM $DB_NAME.races WHERE sessionKey = :sessionKey AND type = :type");
        $stmt->execute([
            'sessionKey' => $session['sessionKey'],
            'type' => $session['type']
        ]);
        $existingRace = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingRace) {
            // If the race exists, check if the times are different
            if ($existingRace['dateStart'] != $session['dateStart']) {
                // Update the time if it's different
                $updateStmt = $conn->prepare("UPDATE races SET dateStart = :dateStart WHERE sessionKey = :sessionKey AND type = :type");
                $updateStmt->execute([
                    'dateStart' => $session['dateStart'],
                    'sessionKey' => $session['sessionKey'],
                    'type' => $session['type'],
                ]);
                echo "Updated sessionKey {$session['sessionKey']} with new time.\n";
            }
        } else {
            // If the race doesn't exist, insert it
            $insertStmt = $conn->prepare("INSERT INTO races (sessionKey, dateStart, name, type) VALUES (:sessionKey, :dateStart, :name, :type)");
            $insertStmt->execute([//$session
                'sessionKey' => $session['sessionKey'],
                'dateStart' => $session['dateStart'],
                'name' => $session['name'],
                'type' => $session['type']
            ]);
            echo "Inserted new sessionKey {$session['sessionKey']}.\n";
        }
    }
}

// Session variables
$conn = getDatabaseConnection();
$sessionKey = getSessionKey($conn, $DB_NAME); // Current session key
$year = getYear($sessionKey); 
$yearKey = floor($sessionKey / 1000) * 1000;

// Update race schedule
echo "Fetching...";
echo "<pre>";
$schedule = getRaceSchedule(3000, 2026);
print_r($schedule);
echo "</pre>";
echo " Got schedule.";
updateDatabase($conn, $DB_NAME, $schedule, $sessionKey); // Update the database with the new data
echo " Finished!";
?>