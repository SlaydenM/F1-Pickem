
<?php
    echo "Pinging...\n";
    require_once "./beginPageSession.php"; 
    $winners = getWinners($DB_NAME, $sessionKeyInstance, $num_drivers, $year);
    echo "Got winners\n";
    echo json_encode($winners);
    
    // require_once "../Methods/Session.php";
    // $winners = getWebDrivers(2011);
?>