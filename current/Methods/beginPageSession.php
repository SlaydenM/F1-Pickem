<?php
require_once "Login.php";
require_once "DBControl.php";
require_once "Session.php";
require_once "../Classes/Bettor.php";
require_once "../Classes/Race.php";
require_once "../Classes/Weekend.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

try {
    // Set database connection
    $conn = getDatabaseConnection();

    // Get session info
    $username = $_SESSION['username'] ?? '';
    $userID = $_SESSION['userID'] ?? null;
    $status = $_SESSION['status'] ?? ''; 
    $message = $_GET['message'] ?? '';
    
    $sessionKey = getSessionKey($conn, $DB_NAME); // Current session key
    // if (!$sessionKey) $sessionKey = 
    $sessionKeyInstance = $_GET['sessionKey'] ?? $sessionKey; // Not current, a specific session set in the url
    
    // Check session key to be in the correct year
    $sessionKeyPrevYear = $sessionKey; // The number of races within the selected year
    $yearIndex = floor($sessionKeyInstance / 1000); // Thousandths place of the selected year
    if ($yearIndex < floor($sessionKey / 1000)) // If previous year btd battles bananza fire
        $sessionKeyPrevYear = getSessionKey($conn, $DB_NAME, $yearIndex * 1000) - 1; // Subtract 1 because it is no longer predicting the next session
    
    $race = getRaceData($conn, $DB_NAME, $sessionKeyInstance);
    $year = getYear($sessionKeyPrevYear);
    $yearKey = floor($sessionKeyPrevYear / 1000) * 1000;
    
    $num_drivers = ($year < 2026) ? 20 : 22;
    $last_place = ($num_drivers == 20) ? "20th" : "22nd";
    
    $VERSION = 20; //random_int(31, 2097151);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} 


