<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    
    <title>Set Picks</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> <!-- Include Normalize.css via CDN -->
    <link rel="stylesheet" href="../Styles/common.css?v=<?php echo $VERSION; ?>">
    <link rel="stylesheet" href="../Styles/setPicksStyle.css?v=<?php echo $VERSION; ?>">
    <link rel="icon" type="image/x-icon" href="../Images/F1Pickem_logo.ico">
    
    <?php
        require_once "../Methods/beginPageSession.php"; 
    ?>
</head>

<body>
    <!-- Header -->
    <div id="page-name">F1 Pick'em</div>
    
    <!-- Welcome -->
    <div id="welcome-box" class="plate-1">
        <h1>
            Welcome <?= $username ?>!<br>
            Your Score Is <?= (new Bettor($userID))->findTotalScore($conn, $DB_NAME, $yearKey) ?> PTS
        </h1>
    </div>
    
    <!-- Race info -->
    <div class="plate-1" id="race-info">
        <h1>Round #<?= $race->sessionKey % 1000 ?> - <?= $race->getDate() ?></h1>
        <h1><i>Formula 1 <u><?= $race->name ?></u> Grand Prix</i></h1>
    </div>
    
    <!-- Main plates -->
    <div id="container"><!-- for draggable objects -->
        <!-- Submission and details -->
        <div id="bet-plate" class="plate-1">
            <h1 class="plate-head">Place Your Picks Here!</h1>
            
            <!-- Boxes to drag each driver -->
            <div id="bet-wrapper">
                <svg class="bettor-box-poly" viewBox="0 0 100 180" preserveAspectRatio="none">
                    <polygon points="0,0 100,0 40,180 0,180"/>
                </svg>
                <div class="snap-box bet-box widget widget-shadow" id="bet1">Drag Here For 1st</div>
                <div class="snap-box bet-box widget widget-shadow" id="bet2">Drag Here For 10th</div>
                <div class="snap-box bet-box widget widget-shadow" id="bet3">Drag Here For <?= $last_place ?></div>
            </div>
            
            <!-- Submit button -->
            <div id="button-info-wrapper">
                <!-- Button action="../Methods/pick.php" action="viewPicks.php" method="get"-->
                <iframe name="votar" style="display:none;"></iframe> <!-- Dummy variable to redirect the form -->
                <form id="button-wrapper" target="votar">
                    <input type="submit" id="submit-button" class="widget widget-shadow" value="Submit Picks!" onclick="submitBets('<?= $race->sessionKey ?>')" />
                    <?php // Check for errors
                        $error = $_GET["error"] ?? "";
                        if ($error === "failedSubmit")
                            echo "<p>Oops! Try submitting again!</p>";
                    ?>
                </form>
                
                <!-- Information -->
                <div id="info-wrapper">
                    <p class='info-head'>Welcome to F1 Pick'em!</p>
                    <p>Simply drag & drop each driver widget to the box you want to bet on. Once you have all three picks, submit!</p>
                    <p>After submitting, you'll be able to view other's picks and race stats.</p>
                    <p class='info-head'>Early/Late Bonus:</p>
                    <ul>
                        <?php
                            // Get weekend data
                            $weekend = new Weekend(getAllRaceData($conn, $DB_NAME, $sessionKey));
                            $raceTypes = $weekend->isSprint ? ["FP1", "Qually", "Sprint Qually", "Sprint"] : ["FP1", "FP2", "FP3", "Qually"]; // Type strings
                            $bonusAmounts = ["+50%", "+25%", "+10%", "no penalty", "-50%"]; // Bonus strings
                            $bonuses = [1.50, 1.25, 1.10, 1.00, 0.50]; // Bonus values
                            
                            // Print list info
                            echo 
                            "<li>Before {$raceTypes[0]}: {$bonusAmounts[0]}</li>
                            <li>Before {$raceTypes[1]}: {$bonusAmounts[1]}</li>
                            <li>Before {$raceTypes[2]}: {$bonusAmounts[2]}</li>
                            <li>After {$raceTypes[3]}: {$bonusAmounts[4]}</li>";
                            
                            // Get the current time
                            $timezone = new DateTimeZone("US/Central");
                            $timeNow = new DateTime('now', $timezone);
                            
                            // Print how long is left for bonus
                            $bonus = 1; // Default 1
                            for ($r = 0; $r < count($weekend->races); $r++) { // Search the races
                                $race = $weekend->races[$r];
                                
                                if ($timeNow < $race->dateStart) { // When the time is before the next race
                                    $timeDiff = $race->dateStart->diff($timeNow); // Take the time difference of the next race
                                    $numDays = $timeDiff->days;
                                    $days = "$numDays day". ($numDays == 1 ? '' : 's');
                                    $hours = "$timeDiff->h hour". ($timeDiff->h == 1 ? '' : 's');
                                    
                                    // Format time until next bonus
                                    //$timeDiff = $timeDiff->format("%d:%H:%I:%S"); // Format for precise time
                                    if ($numDays > 2) // Too big of a scope to display hours
                                        $timeDisplay = $days;
                                    else if ($numDays <= 0) // No point in displaying 0 days
                                        $timeDisplay = $hours;
                                    else // Display both
                                        $timeDisplay = "$days, $hours";
                                    
                                    // Print the time remaining
                                    echo "<li class='info-head' style='text-align:left'>" . (($race->type == "G") ? // The time is between qualifying and Grand Prix
                                        "You have a {$bonusAmounts[4]} penalty</li>" : // True
                                        "You have $timeDisplay left with {$bonusAmounts[$r]}</li>"); // False
                                    
                                    $bonus = $bonuses[$r];
                                    break;
                                }
                            }
                        ?>
                    </ul>
                    <p>Submissions will close before the Grand Prix.</p>
                </div>
                
                <!-- Link to f1.com -->
                <div id='link'>
                    <ul>
                        <h3>F1.com:</h3>
                        <li><a href='https://www.formula1.com/en/racing/<?= $year ?>' target='_blank'>Schedule</a></li>
                        <li><a href='https://www.formula1.com/en/results/<?= $year ?>/races' target='_blank'>Results</a></li>
                        <li><a href='https://www.formula1.com/en/drivers' target='_blank'>Drivers</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Selection for each driver -->
        <div id="selection-plate" class="plate-1">
            <h1 class="plate-head">Driver Selection</h1>
            
            <div id="selection-list">
                <?php
                    $betLabels = ["1st", "10th", $last_place]; // Positions
                    $contestants = getContestants();
                    $mostWinsNums = [0, 0, 0];
                    $mostWinsNames = ["Unknown", "Unknown", "Unknown"];
                    
                    for ($index = 0; $index < $num_drivers; $index++) {
                        $driver = $contestants[$index];
                        $numWins = $driver->getNumWins($conn, $DB_NAME, $sessionKey);
                        $percentWins = $driver->getPercentWins($numWins, $sessionKey);
                        
                        // Print each drivers information
                        echo
                        "<div class='driver-wrapper'>
                            <div class='draggable driver-data widget' id='" . $driver->getNumber() . "'>
                                <img class='logo' src='../Images/F1Pickem_driver_logos/f1_" . $driver->getNumber() . ".png?v=$VERSION'>
                                <div class='driver-tooltip-wrapper'>
                                    <img src='../Images/tooltip.png'/>
                                    <span class='widget driver-tooltip-plate'>
                                        <table class='driver-tooltip-data'>
                                            <tr>
                                                <th>Pos.</th>
                                                <th># Wins</th>
                                                <th>% Wins</th>
                                            </tr>";
                                        for ($posIndex = 0; $posIndex < 3; $posIndex++) { // Print each pos data
                                            // Print data
                                            echo
                                            "<tr class='bet-label'> 
                                                <td>" . $betLabels[$posIndex] . "</td>
                                                <td>" . $numWins[$posIndex] . "</td>
                                                <td>" . round($percentWins[$posIndex], 2) . "%</td>
                                            </tr>";
                                            
                                            if ($numWins[$posIndex] > $mostWinsNums[$posIndex]) {
                                                $mostWinsNums[$posIndex] = $numWins[$posIndex];
                                                $mostWinsNames[$posIndex] = explode(' ', $driver->getName())[1];
                                            }
                                        }
                                        echo
                                        "</table>
                                    </span>
                                </div>
                            </div>
                            <div class='snap-box driver-box widget widget-shadow' id='p" . $driver->getNumber() . "'>Pit " . $index + 1 . "</div>
                        </div>";
                    }
                    
                    // Extra driver info
                    echo
                    "<div class='driver-wrapper'>
                        <div class='draggable driver-data driver-data-extra widget' id='100'>
                            <img class='logo' src='../Images/F1Pickem_driver_logos/f1_100.png?v=$VERSION'>
                            <div class='driver-tooltip-wrapper'>
                                <img src='../Images/tooltip.png'/>
                                <span class='widget driver-tooltip-plate'>
                                    <p>Use this only when your driver is not on the list, we will update score as needed!</p>
                                </span>
                            </div>
                        </div>
                        <div class='snap-box driver-box widget widget-shadow' id='p100'>:)</div>
                    </div>";
                    
                    // Overall driver info
                    echo
                    "<div class='driver-wrapper'>
                        <div class='driver-data widget' id='driver-data-overall'>
                            <h2 style='color:white'>Overall</h2>
                            <div class='driver-tooltip-wrapper'>
                                <img src='../Images/tooltip.png'/>
                                <span class='widget driver-tooltip-plate'>
                                    <table class='driver-tooltip-data'>
                                        <tr>
                                            <th>Pos.</th>
                                            <th>Most</th>
                                            <th>Name</th>
                                        </tr>";
                                    for ($posIndex = 0; $posIndex < 3; $posIndex++) { // Print each pos data
                                        // Print data
                                        echo
                                        "<tr class='bet-label'> 
                                            <td>" . $betLabels[$posIndex] . "</td>
                                            <td>" . $mostWinsNums[$posIndex] . "</td>
                                            <td>" . $mostWinsNames[$posIndex] . "</td>
                                        </tr>";
                                    }
                                    echo
                                    "</table>
                                </span>
                            </div>
                        </div>
                        <div class='snap-box driver-box widget widget-shadow'>Pit 1</div>
                    </div>";
                ?>
            </div>
        </div>
    </div>
</body>

<script>
    window.onload = () => matchPlateHeight();
    window.onresize = () => matchPlateHeight();
    
    function matchPlateHeight() {
        $("#selection-plate, #bet-plate").height("auto"); // Reset height
        let h1 = $("#selection-plate").height(); // Get height of both plates
        let h2 = $("#bet-plate").height();
        let height = Math.max(h1, h2); // Compare which is taller
        $("#selection-plate, #bet-plate").height(height); // Set heights of each
    };
    
    function submitBets(sessionKey) {
        let bets = [1, 2, 0];
        let index = 0;
        $(".bet-box").each(function () { // For each bet
            let snapPos = $(this).offset();
            $(".draggable").each(function () { // For each driver draggable
                let dragPos = $(this).offset();
                // Find draggable and its id
                if (isSnapped(dragPos, snapPos)) {
                    bets[index] = $(this).attr('id');
                    return;
                }
            });
            index++;
        });
        
        // Validate input array
        if (bets.length != 3) {
            console.error("Bets array must have three elements.");
            return;
        }
        var error = false
        bets.forEach(function(bet) {
            if (bet == 0) {
                window.location.replace("setPicks.php?error=failedSubmit");
                error = true
            }
        })
        
        // Send to server
        if (!error) {
            // Validate with button change
            var button = document.getElementById("submit-button");
            button.style = "background-color: limegreen";
            button.onclick = "";
            
            setAttributes(sessionKey, bets);
        }
    }
    
    function setAttributes(sessionKey, bets) {
        var bettorData = {
            "bets": bets,
            "bonus": <?= $bonus ?>
        }

        // Make an AJAX call to set the session attribute
        $.ajax({
            url: '../Methods/pick.php',
            method: 'GET', // Use POST for better security
            dataType: "json",
            contentType: 'application/json',
            data: 'bettor=' + JSON.stringify(bettorData), // Convert the payload to a JSON
            success: function(response) {
                console.log("Session bet set successfully! Server response:", response);

                // Return to viewPicks
                window.location.replace("viewPicks.php?sessionKey=" + sessionKey + "&message=picked");
            },
            error: function(xhr, status, error) {
                console.error("Failed to set session bet:", error);
            }
        });
    }
    
    function isSnapped(dragPos, snapPos) {
        if (Math.abs(dragPos.top - snapPos.top) <= 50 && Math.abs(dragPos.left - snapPos.left) <= 100) // If top and left positions match
            return true;
        return false;
    }
    
    function snapTo(self, id, snapPos) {
        $("#" + id).css("top", snapPos.top + "px");
        $("#" + id).css("left", snapPos.left + "px");
        
        console.log("Textbox " + id + " snapped box with position: " + self.position().top + " " + self.position().left);
    }
    
    $(function () { // $() selects all elements with CSS notation: type, id, or class 
        let startPos;
        
        $(".draggable").draggable({// This set of {} indicates this whole block is an object
            containment: "#container",
            stack: ".draggable", // Always make the current draggable on top
            
            start: function () {
                startPos = $(this).offset(); // Keep track of current draggable's position
            },
            
            stop: function () {
                let id = $(this).attr('id'); // 'this' refers to the element that received the current event: the draggable
                let dragPos = $(this).offset(); // Returns the absolute position of the element
                let snapped = false;
                
                $(".bet-box").each(function () {
                    snapPos = $(this).offset();
                    
                    if (isSnapped(dragPos, snapPos)) { // Check if the draggable is within range to snap to the top-left corner of a bet box
                        snapTo($(this), id, snapPos);
                        snapped = true;
                    }
                });
                
                if (!snapped) { // If not already snapped
                    snapTo($(this), id, $("#p" + id).offset()); // Snap to default: original pit
                }
            }
        });
    }); 
</script>