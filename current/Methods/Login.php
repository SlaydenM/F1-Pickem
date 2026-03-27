<?php
require_once "Session.php";
require_once "DBControl.php";

// Check login
function loginCheck($username, $password) {
    global $DB_NAME;
    
    if (empty($username) || empty($password))
        return;
    
    $log = fopen("log.txt", 'a');
    date_default_timezone_set('US/Central');
    $timestamp = date('Y/m/d H:i:s');
    fwrite($log, "\n$timestamp - $username logged in");
    
    // Query to check if user exists
    $conn = getDatabaseConnection();
    $queryCheckUsers = "SELECT * FROM users WHERE username = :username AND password = :password";
    $stmt = $conn->prepare($queryCheckUsers);
    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch();
    
    if ($user) { // If user is found in the database
        fwrite($log, " successfully!");
        
        $userID = $user['userID'];
        $sessionKey = getSessionKey($conn, $DB_NAME); // Get key from web
        
        // Set session attributes
        session_start();
        $_SESSION['username'] = ucfirst($username);
        $_SESSION['userID'] = $userID;
        
        // Query to check if user has bet
        $queryCheckBettors = "SELECT * FROM bettors WHERE userID = :userID AND sessionKey = :sessionKey";
        $stmt = $conn->prepare($queryCheckBettors);
        $stmt->execute(['userID' => $userID, 'sessionKey' => $sessionKey]);
        $bettor = $stmt->fetch();
        
        // Get current time
        $timezone = new DateTimeZone("US/Central");
        $timeNow = new DateTime('now', $timezone);
        $timeRace = getRaceData($conn, $DB_NAME, $sessionKey, "G")->dateStart; // Minus an hour before the Grand Prix->sub(new DateInterval("PT1H"))
        
        // Update status
        $status = $bettor ? 'picked' : ($timeNow > $timeRace ? 'locked' : 'unpicked');
        
        // Redirect to viewPicks.php
        $_SESSION['status'] = $status;
        header("Location: ../Pages/viewPicks.php?sessionKey=$sessionKey");
    } else { // User does not exist
        fwrite($log, " unsuccessfully. :(");
        header("Location: ../Pages/login.php?error=loginMismatch"); // Redirect back to login with error
    }
    
    fclose($log);
    
    exit;
}

// Handle POST request on load
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['usernameLogin'];
    $password = $_POST['passwordLogin'];
    loginCheck($username, $password);
}

?>