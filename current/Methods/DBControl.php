<?php

$excludedUsers = [

];

// Database credentials
/*
$DB_HOST = "localhost";
$DB_USER = "SlaydenMartin";
$DB_PASS = "Timmy18?";
$DB_NAME = "f1db";
$DB_PORT = 3006;
/*/
$DB_HOST = "localhost";
$DB_USER = "martinpl_Slayden";
$DB_PASS = "Timmy18?*?";
$DB_NAME = "martinpl_f1db";
$DB_PORT = 3306;
//*/

// Establish database connection
function getDatabaseConnection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT;

    try {
        $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;port=$DB_PORT;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $DB_USER, $DB_PASS, $options);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Get bettor data for viewing
function getBettors($conn, $sessionKey) {
    global $DB_NAME;

    $querySelect1 = "SELECT * FROM $DB_NAME.bettors WHERE sessionKey = :sessionKey";
    $querySelect2 = "SELECT * FROM $DB_NAME.users WHERE userID = :userID";
    
    try {
        $stmtSelect1 = $conn->prepare($querySelect1);
        $stmtSelect2 = $conn->prepare($querySelect2);
        
        $stmtSelect1->execute(['sessionKey' => $sessionKey]);
        
        $bettors = [];
        while ($row1 = $stmtSelect1->fetch(PDO::FETCH_ASSOC)) {
            $ID = $row1['userID'];
            $bonus = $row1['bonus'];
            $bets = explode(',', $row1['bets']);
            
            // Check duplicates
            $duplicate = false;
            foreach ($bettors as $b) {
                if ($b->getID() === $ID) {
                    $duplicate = true;
                    break;
                }
            }
            if ($duplicate) continue;
            
            // Get username
            $stmtSelect2->execute(['userID' => $ID]);
            $row2 = $stmtSelect2->fetch(PDO::FETCH_ASSOC);
            $name = $row2['username'];
            
            // Create object and update score
            $bettor = new Bettor($ID, $bets, $name, $bonus);
            $bettor->setScore($row1['score'] ?? 0);
            
            $bettors[] = $bettor;
        }
        
        return $bettors;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Returns a list of all users by total score
function getBettorsByTotal($conn, $yearKey) {
    global $DB_NAME, $excludedUsers;
    
    try {
        // Get a list of users
        $queryUsers = "SELECT * FROM $DB_NAME.users";
        $stmtUsers = $conn->prepare($queryUsers);
        $stmtUsers->execute();
        
        // Process query and store as Bettor
        $users = [];
        while ($rowUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
            $ID = $rowUsers['userID'];
            
            $queryBettors = "SELECT * FROM $DB_NAME.bettors WHERE userID = :userID AND sessionKey > :yearKey AND sessionKey < :nextYearKey LIMIT 1";
            $stmtBettors = $conn->prepare($queryBettors);
            $stmtBettors->execute(['userID' => $ID, 'yearKey' => $yearKey, 'nextYearKey' => $yearKey + 1000]); // Select user within year
            
            if ($stmtBettors->fetch(PDO::FETCH_ASSOC)) // If user appears this year
                $users[] = new Bettor($ID, null, $rowUsers['username']); // Add new object
        }
        
        // Set the total score for each bettor
        foreach ($users as $b)
            $b->setScore($b->findTotalScore($conn, $DB_NAME, $yearKey));
        
        // Sort the users by score in descending order
        usort($users, function($a, $b) {
            return $b->getScore() <=> $a->getScore();// Compare scores in reverse order
        });
        
        return $users;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }

    return [];
}

// Returns a list of bettors starting with the bettor containing the specified userID
function getBettorsIDFirst($conn, $sessionKey, $userID, $bettors=null) {
    global $DB_NAME;

    // Get the newest bettor list
    if (is_null($bettors))
        $bettors = getBettors($conn, $sessionKey); // Assuming getBettors() is defined and returns an array of Bettor objects
    $bettorsSorted = [];
    
    // Sort
    foreach ($bettors as $bettor) {
        if ($bettor->getID() == $userID)
            array_unshift($bettorsSorted, $bettor); // Add the matching user to the beginning of the array
        else
            $bettorsSorted[] = $bettor; // Add other users to the end of the array
    }
    
    return $bettorsSorted;
}

// Inserts winners into the database
function insertWinners($conn, $winners, $sessionKey) {
    global $DB_NAME;
    
    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $queryCheck = "SELECT 1 FROM $DB_NAME.drivers WHERE sessionKey = :sessionKey";
        $queryInsert = "INSERT INTO `" . $DB_NAME . "`.drivers 
            (number, name, team, `position`, sessionKey) 
            VALUES (:number, :name, :team, :position, :sessionKey)";
        
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute(['sessionKey' => $sessionKey]);
        
        if ($stmtCheck->fetch() === false) { // If there are no results under this sessionKey            
            $stmtInsert = $conn->prepare($queryInsert);
            foreach ($winners as $driver) {
                $stmtInsert->execute([
                    'number' => $driver->getNumber(),
                    'name' => $driver->getName(),
                    'team' => $driver->getTeam(),
                    'position' => $driver->getPosition(),
                    'sessionKey' => $sessionKey
                ]);
            }
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// Updates the scores
function updateScore($conn, $winners, $sessionKey, $num_drivers) {
    global $DB_NAME;

    try {
        $queryUpdate = "UPDATE $DB_NAME.bettors SET score = :score WHERE sessionKey = :sessionKey AND userID = :userID";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $bettors = getBettors($conn, $sessionKey);
        evaluateBets($bettors, $winners, $num_drivers);
        foreach ($bettors as $b) {
            $score = $b->getScore() * $b->getBonus(); // Multiply score by bonus
            $stmtUpdate->execute([
                'score' => $score,
                'sessionKey' => $sessionKey, 
                'userID' => $b->getID()
            ]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// Evaluates bets and awards points
function evaluateBets($bettors, $winners, $num_drivers) {
    foreach ($bettors as $b) {
        if ($b->getBets()[0] % 100 == $winners[0]->getNumber() % 100) $b->incrementScore(7);
        if ($b->getBets()[1] % 100 == $winners[9]->getNumber() % 100) $b->incrementScore(5);
        if ($b->getBets()[2] % 100 == $winners[min($num_drivers, count($winners) - 1)]->getNumber() % 100) $b->incrementScore(3);
    }
}

// Get correct bets
function getCorrectBets($bettors, $winners) {
    if (empty($winners)) // Do not evaluate if winners are not yet out
        return [];
    
    $correctBets = [];
    foreach ($bettors as $b) {
        $currBets = [0, 0, 0];
        if ($b->getBets()[0] == $winners[0]->getNumber()) $currBets[0] = 1;
        if ($b->getBets()[1] == $winners[9]->getNumber()) $currBets[1] = 1;
        if ($b->getBets()[2] == $winners[count($winners) - 1]->getNumber()) $currBets[2] = 1;
        $correctBets[] = $currBets;
    }
    return $correctBets;
}

?>