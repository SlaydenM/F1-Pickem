<?php
require_once "../Classes/Driver.php";
require_once "../Classes/Race.php";

// Returns the data fields from all races of a session
function getAllRaceData(PDO $conn, $DB_NAME, int $sessionKey) {
    try {
        $querySelect = "SELECT * FROM $DB_NAME.races WHERE sessionKey = :sessionKey"; // Prepare the sql statement
        $stmt = $conn->prepare($querySelect); 
        $stmt->execute(['sessionKey' => $sessionKey]);
        
        $races = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $races[] = new Race(
                $row['sessionKey'],
                $row['dateStart'],
                $row['name'],
                $row['type']
            );
        }
        return $races;
    } catch (PDOException $e) {
        throw new PDOException("No data found for session key $sessionKey");
    }
}

// Returns the data fields from the given race type of a session
function getRaceData(PDO $conn, $DB_NAME, int $sessionKey, $type="G") {
    $querySelect = "SELECT * FROM $DB_NAME.races WHERE sessionKey = :sessionKey AND type = :type"; // Prepare the sql statement
    $stmt = $conn->prepare($querySelect); 
    $stmt->execute(['sessionKey' => $sessionKey, 'type' => $type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        return new Race(
            $row['sessionKey'],
            $row['dateStart'],
            $row['name'],
            $type
        );
    }
    throw new PDOException("No data found for session key $sessionKey and type $type");
}

// Returns contestant drivers before the weekend
function getContestants(int $year): array {  
    $contestants = [ // contains roster entries for the requested year
        '2026' => [
            new Driver("Lando Norris",      "MCLAREN",       1, 0, 2026),
            new Driver("Oscar Piastri",     "MCLAREN",      81, 0, 2026),
            new Driver("George Russell",    "MERCEDES",     63, 0, 2026),
            new Driver("Andrea Antonelli",  "MERCEDES",     12, 0, 2026),
            new Driver("Max Verstappen",    "RED BULL",      3, 0, 2026),
            new Driver("Isack Hadjar",      "RED BULL",      6, 0, 2026),
            new Driver("Lewis Hamilton",    "FARRARI",      44, 0, 2026),
            new Driver("Charles Leclerc",   "FERRARI",      16, 0, 2026),
            new Driver("Alexander Albon",   "WILLIAMS",     23, 0, 2026),
            new Driver("Carlos Sainz",      "WILLIAMS",     55, 0, 2026),
            new Driver("Oliver Bearman",    "HAAS",         87, 0, 2026),
            new Driver("Esteban Ocon",      "HAAS",         31, 0, 2026),
            new Driver("Lance Stroll",      "ASTON MARTIN", 18, 0, 2026),
            new Driver("Fernando Alonso",   "ASTON MARTIN", 14, 0, 2026),
            new Driver("Pierre Gasly",      "ALPINE",       10, 0, 2026),
            new Driver("Franco Colapinto",  "ALPINE",       43, 0, 2026),
            new Driver("Liam Lawson",       "RACING BULLS", 30, 0, 2026),
            new Driver("Arvid Lindblad",    "RACING BULLS", 41, 0, 2026),
            new Driver("Nico Hulkenberg",   "AUDI",         27, 0, 2026),
            new Driver("Gabriel Bortoletto","AUDI",         05, 0, 2026),
            new Driver("Valtteri Bottas",   "CADILLAC",     77, 0, 2026),
            new Driver("Sergio Perez",      "CADILLAC",     11, 0, 2026),
            
            new Driver("Extra",             "Driver",      100, 0, 2026),
        ],
        '2025' => [
            new Driver("Lando Norris",      "MCLAREN",       4, 0, 2025),
            new Driver("Oscar Piastri",     "MCLAREN",      81, 0, 2025),
            new Driver("Max Verstappen",    "RED BULL",      1, 0, 2025),
            new Driver("Yuki Tsunoda",      "RED BULL",    122, 0, 2025), // Switched with Liam 4/1/25 weekend
            new Driver("Carlos Sainz",      "WILLIAMS",     55, 0, 2025),
            new Driver("Alexander Albon",   "WILLIAMS",     23, 0, 2025),
            new Driver("Isack Hadjar",      "RACING BULLS",  6, 0, 2025),
            new Driver("Liam Lawson",       "RACING BULLS",130, 0, 2025), // Switched with Yuki 4/1/25 weekend
            new Driver("Pierre Gasly",      "ALPINE",       10, 0, 2025),
            new Driver("Franco Colapinto",  "ALPINE",      143, 0, 2025), // Replaced Jack 5/16/25 weekend
            new Driver("Fernando Alonso",   "ASTON MARTIN", 14, 0, 2025),
            new Driver("Lance Stroll",      "ASTON MARTIN", 18, 0, 2025),
            new Driver("Charles Leclerc",   "FERRARI",      16, 0, 2025),
            new Driver("Lewis Hamilton",    "FARRARI",      44, 0, 2025),
            new Driver("Oliver Bearman",    "HAAS",         87, 0, 2025),
            new Driver("Esteban Ocon",      "HAAS",         31, 0, 2025),
            new Driver("Nico Hulkenberg",   "KICK SAUBER",  27, 0, 2025),
            new Driver("Gabriel Bortoleto", "KICK SAUBER",   5, 0, 2025),
            new Driver("Andrea Antonelli",  "MERCEDES",     12, 0, 2025),
            new Driver("George Russell",    "MERCEDES",     63, 0, 2025),
            
            // Outdated drivers 2025
            new Driver("Liam Lawson",       "RED BULL",     30, 0, 2025),
            new Driver("Yuki Tsunoda",      "RACING BULLS", 22, 0, 2025),
            new Driver("Yuki Tsunoda",      "RACING BULLS", 22, 0, 2025),
            new Driver("Jack Doohan",       "ALPINE",        7, 0, 2025), // Replaced with Franco 5/16/25 weekend
        
            new Driver("Extra",             "Driver",      100, 0, 2025)
        ],
        '2024' => [
            new Driver("Max Verstappen",    "RED BULL",      1, 0, 2024),
            new Driver("Sergio Perez",      "RED BULL",     11, 0, 2024),
            new Driver("Franco Colapinto",  "WILLIAMS",     43, 0, 2024),
            new Driver("Alexander Albon",   "WILLIAMS",     23, 0, 2024),
            new Driver("Liam Lawson",       "RACING BULLS", 30, 0, 2024),
            new Driver("Yuki Tsunoda",      "RACING BULLS", 22, 0, 2024),
            new Driver("Lando Norris",      "MCLAREN",       4, 0, 2024),
            new Driver("Oscar Piastri",     "MCLAREN",      81, 0, 2024),
            new Driver("Pierre Gasly",      "ALPINE",       10, 0, 2024),
            new Driver("Esteban Ocon",      "ALPINE",       31, 0, 2024),
            new Driver("Fernando Alonso",   "ASTON MARTIN", 14, 0, 2024),
            new Driver("Lance Stroll",      "ASTON MARTIN", 18, 0, 2024),
            new Driver("Charles Leclerc",   "FERRARI",      16, 0, 2024),
            new Driver("Carlos Sainz",      "FARRARI",      55, 0, 2024),
            new Driver("Kevin Magnussen",   "HAAS",         20, 0, 2024),
            new Driver("Nico Hulkenberg",   "HAAS",         27, 0, 2024),
            new Driver("Zhou Guanyu",       "KICK SAUBER",  24, 0, 2024),
            new Driver("Valtteri Bottas",   "KICK SAUBER",  77, 0, 2024),
            new Driver("Lewis Hamilton",    "MERCEDES",     44, 0, 2024),
            new Driver("George Russell",    "MERCEDES",     63, 0, 2024),
            
            // Outdated drivers 2024
            new Driver("Oliver Bearman",    "HAAS",         38, 0, 2024),
            new Driver("Daniel Ricciardo",  "RACING BULLS",  3, 0, 2024),
            new Driver("Logan Sargeant",    "WILLIAMS",      2, 0, 2024),
            new Driver("Extra",             "Driver",      100, 0, 2024)
        ]
    ];
    
    return $contestants[strval($year)];
}

// Returns the latest list of winners
function getWinners($DB_NAME, int $sessionKey, $num_drivers=20, $year=2026): array {
    $winners = [];
    try {
        $conn = getDatabaseConnection();
        // Check if data exists in the database
        if (!getDatabaseDrivers($conn, $DB_NAME, $winners, $sessionKey)) {
            // Fallback to the data on the web
            $winners = getWebDrivers($sessionKey);
            
            // From DBControl
            if (!empty($winners)) { // Empty on first week of the year
                // Update driver numbers for replacements
                $contestants = array_slice(getContestants(getYear($sessionKey)), $num_drivers - 1);
                $replacements = [];
                foreach ($contestants as $c) { // Find replacements
                    $num = $c->getNumber();
                    if ($num > 100) { // Replacements are denoted by having a hundredths place
                        $replacements[] = $num; // Add to list
                    }
                }
                foreach ($winners as $w) { // Update driver numbers
                    foreach ($replacements as $r) {
                        if ($w->getYear() === $year && $w->getNumber() === $r % 100) { // If winner is a replacement currently
                            $w->setNumber($r);
                            break;
                        }
                    }
                }
                insertWinners($conn, $winners, $sessionKey);
                updateScore($conn, $winners, $sessionKey, $num_drivers);
            }
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
    return $winners;
}

// Returns the current session key
function getSessionKey($conn, $DB_NAME, $yearKey=null) {
    try {
        if ($yearKey === null) {
            $timezone = new DateTimeZone("US/Central");
            $dateNow = new DateTime('now', $timezone);
            $dateNow->add(new DateInterval("PT4H")); // Plus 4 hours after the start of Grand Prix
            
            $querySelect = "SELECT sessionKey FROM $DB_NAME.races WHERE dateStart >= :dateNow ORDER by sessionKey ASC"; // Prepare the sql statement
            $stmt = $conn->prepare($querySelect); 
            $stmt->execute(['dateNow' => date_format($dateNow,"Y-m-d H:i:s")]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && array_key_exists("sessionKey", $row)) { // Return if data exists
                return $row['sessionKey'];
            } 
            
            // Out of date range, select last race
            $querySelect = "SELECT sessionKey FROM $DB_NAME.races ORDER by sessionKey DESC"; // Prepare the sql statement
            $stmt = $conn->prepare($querySelect); 
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['sessionKey'];
        }
        else { // Returns the next race's sessionKey from f1db
            // Select from f1db
            $querySelect = "SELECT sessionKey FROM $DB_NAME.races WHERE sessionKey < :yearKey ORDER by sessionKey DESC"; // Check the previous sessionKey
            $stmtSelect = $conn->prepare($querySelect); // Prepare the statement
            $stmtSelect->execute(['yearKey' => $yearKey + 1000]); // Execute the query
            
            // Process query
            $row = $stmtSelect->fetch(PDO::FETCH_ASSOC);
            $sessionKey = $row['sessionKey'] + 1; // Get the sessionKey, Increment to create a new index
            
            return $sessionKey;
        }
    } catch (Exception $ex) {
        error_log("Error: " . $ex->getMessage());
    }
    
    return 0;
}

// Calculates the year based on session
function getYear($sessionKey) {
    return floor($sessionKey / 1000) + 2023;
}

// Returns winners from database, pass by reference to be able to return a boolean value
function getDatabaseDrivers(PDO $conn, $DB_NAME, array& $winners, int $sessionKey): bool {
    try {
        $query = "SELECT * FROM $DB_NAME.drivers WHERE sessionKey = :sessionKey";
        $stmt = $conn->prepare($query);
        $stmt->execute(['sessionKey' => $sessionKey]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Get driver data
            $name = $row['name']; // full_name
            $team = $row['team']; // team_name
            $number = $row['number']; // driver_number
            $position = $row['position'];
            
            // Store data in object
            $d = new Driver($name, $team, $number, $position, getYear($sessionKey));
            $winners[] = $d;
        }
    } catch (PDOException $e) {
        throw $e;
    }
    
    return !empty($winners);
}

/**
 * Fetches the ordered driver results for a specific F1 race.
 *
 * @param int $sessionKey The round number of the race (e.g., 13 for the 13th race).
 * @return Driver[]       Array of Driver objects.
 */
function getWebDrivers(int $sessionKey): array {
    $year = getYear($sessionKey);
    $raceKey = $sessionKey % 1000;
    
    // Utilizing the Jolpica F1 API, the direct continuation of Ergast F1
    $url = "https://api.jolpi.ca/ergast/f1/{$year}/{$raceKey}/results.json";

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Setting a User-Agent is good practice to prevent getting blocked by public APIs
    curl_setopt($ch, CURLOPT_USERAGENT, 'F1-Data-Fetcher/1.0'); 
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        throw new Exception("Failed to fetch data from API. HTTP Status Code: {$httpCode}");
    }

    $data = json_decode($response, true);
    $drivers = [];

    // Navigate the JSON response structure
    $races = $data['MRData']['RaceTable']['Races'] ?? [];
    
    // If the race hasn't happened yet or the round is invalid
    if (empty($races)) {
        return $drivers; 
    }

    $results = $races[0]['Results'] ?? [];

    foreach ($results as $result) {
            $name = $result['Driver']['givenName'] . ' ' . $result['Driver']['familyName'];
            $team = $result['Constructor']['name'];
            $number = (int) $result['number'];
            $position = (int) $result['position'];

            $drivers[] = new Driver($name, $team, $number, $position, getYear($sessionKey));
        }
    return $drivers;
}