<?php
// require_once "Login.php";
require_once "DBControl.php";
require_once "Session.php";
require_once "../Classes/Bettor.php";
require_once "../Classes/Weekend.php";

session_start(); // Start or resume the session

// Get attributes
if (!isset($_SESSION['userID']))
    exit();
$userID = $_SESSION['userID'];
$bettorData = json_decode($_GET['bettor'], true);
$bets = $bettorData['bets'];
// $bonus = $bettorData['bonus'];

// Abort if any bets are not filled
foreach ($bets as $bet)
    if ($bet === 0)
        exit;

try {
    // Get weekend data
    $conn = getDatabaseConnection();
    $sessionKey = getSessionKey($conn, $DB_NAME); // Current session key
    $weekend = new Weekend(getAllRaceData($conn, $DB_NAME, $sessionKey));
    
    // Get the current time
    $timezone = new DateTimeZone("US/Central");
    $timeNow = new DateTime('now', $timezone);
    
    // Print how long is left for bonus
    $bonuses = [1.50, 1.25, 1.10, 1.00, 0.50]; // Bonus values
    $bonus = 1; // Default 1
    // CHANGE. FIRST WEEK OF 2026 GETS NO BONUS BC IM SILLY
    // for ($r = 0; $r < count($weekend->races); $r++) { // Search the race        
    //     if ($timeNow < $weekend->races[$r]->dateStart) { // When the time is before the next race
    //         $bonus = $bonuses[$r];
    //         break;
    //     }
    // }
    
    // Create an object
    $bettor = new Bettor($userID, $bets, bonus: $bonus);
    
    // Update and log data
    $conn = getDatabaseConnection(); // Open connection to the database
    $sessionKey = getSessionKey($conn, $DB_NAME); // Get session key
    $bettor->databaseInsert($conn, $DB_NAME, $sessionKey); // Insert bettor data into the database
    $_SESSION['status'] = "picked";
    // echo "Success";
    
    // $log = fopen("log.txt", 'a');
    // date_default_timezone_set('US/Central');
    // $timestamp = date('Y/m/d H:i:s');
    // fwrite($log, "\n$timestamp - ". $bettor->getName() ." (". $bettor->getId() .") picked. Bonus: ". ($bettor->getBonus() - 1) * 100 ."%");
    // fclose($log);
    
    // Return status for AJAX to read
    header('Content-Type: application/json');
    echo json_encode(["status" => "Successfully added new entry"]);
    
    exit;
} catch (PDOException $ex) {
    error_log("Database error: " . $ex->getMessage());
    echo "Database error: " . $ex->getMessage();
    exit;
}
