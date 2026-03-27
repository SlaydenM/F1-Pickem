
<?php
    echo "Pinging...\n";
    require_once "./beginPageSession.php"; 
    $winners = getWinners($DB_NAME, $sessionKeyInstance, $num_drivers);
    echo "Got winners\n";
    echo json_encode($winners);

    // require_once "../Methods/Session.php";
    // $winners = getWebDrivers(2011);
?>