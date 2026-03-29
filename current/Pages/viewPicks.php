<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    
    <title>View Picks</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> <!-- Include Normalize.css via CDN -->
    <link rel="stylesheet" href="../Styles/common.css?v=<?php echo $VERSION; ?>">
    <link rel="stylesheet" href="../Styles/viewPicksStyle.css?v=<?php echo $VERSION; ?>">
    <link rel="icon" type="image/x-icon" href="../Images/F1Pickem_logo.ico">
    
    <?php
        require_once "../Methods/beginPageSession.php"; 
    ?>
</head>

<body>
    <!-- Header -->
    <div id="page-name">F1 Pick'em</div>
    
    <!-- Status message -->
    <?php if ($message === 'picked'): ?>
        <div id="status-message" style="padding: 45px 10px;">Submitted Your Picks!</div> <!-- If just picked -->
    <?php elseif ($status === 'locked'): ?>
        <div id="status-message">Picks are locked after<br>Grand Prix</div> <!-- If not picked and during qualifying -->
    <?php else: ?>
        <div id="filler"></div>
    <?php endif; ?>
    
    <!-- Welcome -->
    <div id="welcome-box" class="plate-1">
        <h1>
            Welcome <?= $username ?>!<br>
            Your Score Is <?= (new Bettor($userID))->findTotalScore($conn, $DB_NAME, $yearKey) ?><span>PTS</span>
        </h1>
    </div>
    
    <!-- Standings -->
    <div id="standings-box" class="plate-1">
        <h1>Standings</h1>
        
        <!-- List -->
        <table class="left-panel" id="standings-list">
            <div id="standings-backer"></div>
            
            <!-- Table header -->
            <tr id="standings-head" class="standings-entry">
                <td>Pos.</td>
                <td>Name</td>
                <td>Total</td>
            </tr>
            
            <!-- Table body -->
            <?php
                $index = 1;
                $bettors = getBettorsByTotal($conn, $yearKey);
                $prevBettor = (count($bettors) > 0) ? $bettors[count($bettors) - 1] : null;
                foreach ($bettors as $b) {            
                    // Print a row of pos, name, and score
                    echo 
                    "<tr class='standings-entry'>
                        <td>" . (($prevBettor !== null && $b->getScore() != $prevBettor->getScore()) ? $index++ : "") . ".</td>
                        <td>" . $b->getName() . "</td>
                        <td>" . round($b->getScore(), 2) . "<span>PTS</span></td>
                    </tr>";
                    
                    $prevBettor = $b;
                }
            ?>
        </table>
        
        <!-- Welcome and scoring information -->
        <button id="toggleBtn">→</button>
        <div class="right-panel" id="info-wrapper">
            <div id="main-info">
                <p class='info-head'>Welcome to F1 Pick'em!</p>
                <p>A friendly game around Formula 1.</p>
                <p>Here you can view scores, picks, and results. Check out past races to get a good idea of who to pick!</p>
                <p>This year introduces bonuses, additional driver info, and new design changes!</p>
            </div>
            <div id="scoring-info">
                <p class='info-head'>Scoring Rules:</p>
                <p>1st Pick = +7<span>PTS</span></p>
                <p>10th Pick = +5<span>PTS</span></p>
                <p><?= $last_place ?> Pick = +3<span>PTS</span></p>
            </div>
        </div>
    </div>
    
    <!-- Race info and selection -->
    <div id="choose-week-box" class="plate-1">
        <h1>View Previous Races</h1>
        
        <!-- Year dropdown -->
        <select onchange='refreshSessionKey(1)' id='choose-year-list'>
            <option value="1">2024</option>
            <option value="2">2025</option>
            <option value="3">2026</option>
        </select>
        
        <!-- Week buttons -->
        <div id="choose-week-list">
            <?php
                for ($sk = $yearKey + 1; $sk <= $sessionKeyPrevYear; $sk++)
                    echo "<button id='$sk' class='choose-week-button' onclick='refreshSessionKey($sk)'>" . ($sk % 1000) . "</button>";
            ?>
        </div>
        
        <!-- Race info -->
        <h1>
            Round #<?= $race->sessionKey % 1000 ?> - <?= $race->getDate() ?><br>
            <i>Formula 1 <u><?= $race->name ?></u></i>
        </h1>
    </div>
    
    <!-- Picks and Results -->
    <div id="results-box" class="plate-1">
        <?php 
            if ($status === "unpicked" && $sessionKeyInstance == $sessionKey) { ?> <!-- If the user has not yet picked  -->
            <!-- Option to place picks  -->
            <h1>Place Picks</h1>
            <form action='setPicks.php' method='get' id='button-wrapper'>
                <p>Submit your picks before viewing results</p>
                <input type='submit' id='submit-button' class='widget widget-shadow' value='Submit Picks!'/>
                <input type='hidden' name='sessionKey' value='<?= $sessionKeyInstance ?>'/>
            </form>
        <?php }
        else {
            function printBettorBox($bettor, $bettors, $correctBets, $betLabels, $userID) {
                global $conn, $DB_NAME, $sessionKeyInstance, $year, $VERSION;
                
                $bets = $bettor->getBets();
                $identity = ($bettor->getID() == $userID) ? "self" : "others"; // If the bettor is the viewer
                
                echo
                "<div class='bettor-box'>
                    <svg class='bettor-box-poly' viewBox='0 0 100 180' preserveAspectRatio='none'>
                        <polygon points='0,0 100,0 40,180 0,180'/>
                    </svg>
                    <div class='bettor-box-info'>
                        <h2 class='bettor-box-name'>{$bettor->getName()}</h2>
                        <table class='bet-list'>
                            <tbody>";
                $contestants = getContestants($year);                
                for ($betIndex = 0; $betIndex < 3; $betIndex++) { // For each bet
                    // Find driver with specified number
                    $driverIndex = 0;
                    while ($bets[$betIndex] != $contestants[$driverIndex]->getNumber() && $driverIndex + 1 < count($contestants))
                        $driverIndex++; // Find driver index
                    
                    // Print data
                    echo
                    "<tr> 
                        <td class='bet-label'>{$betLabels[$betIndex]}</td>
                        <td>
                            <img 
                                class='driver-data widget widget-shadow ". 
                                    ((count($correctBets) > 0 && 
                                    $correctBets[array_search($bettor, $bettors)][$betIndex]) ? 'imp' : '') ."' 
                                src='../Images/driver_logos/" . $year . "/f1_". $contestants[$driverIndex]->getNumber() .".png?v=$VERSION'>
                        </td>
                    </tr>";
                } 
                
                // Scoring information
                $score = round($bettor->findScore($conn, $DB_NAME, $sessionKeyInstance), 2);
                $rawScore = ($bettor->getBonus() == 0) ? $score : $score / $bettor->getBonus();
                $bonus = ($bettor->getBonus() - 1) * 100;
                $sign = ($bonus >= 0) ? "+" : "-";
                echo 
                            "</tbody>
                        </table>
                        <div class='score-container' onclick='toggleBox(event)'>
                            $score<span>PTS</span> ▼
                            <div class='hover-box'>
                                Raw: +$rawScore<span>PTS</span><br>
                                Bonus: $sign". abs($bonus) ."%<br>
                                Total: +$score<span>PTS</span>
                            </div>
                        </div>
                    </div>
                </div>";
            }
            
            $bettors = getBettorsIDFirst($conn, $sessionKeyInstance, $userID); ?>
            
            <!-- Results -->
            <div id="weekly-box">
                <h1>Weekly Picks (<?= count($bettors) ?>)</h1>
                <div id="plate-2-list">
                    <?php
                        $betLabels = ["1st", "10th", $last_place];
                        $winners = getWinners($DB_NAME, $sessionKeyInstance, $num_drivers, $year);
                        $correctBets = getCorrectBets($bettors, $winners);
                        
                        // Display each bettor
                        foreach ($bettors as $bettor) // Ensure self is first
                            printBettorBox($bettor, $bettors, $correctBets, $betLabels, $userID);
                    ?>
                </div>
            </div>
            
            <div id="race-box-wrapper">
                <h1>Results</h1>    
                <div id="race-box">
                    <table id="driver-list">
                        <?php
                            $position = 0;
                            if (empty($winners)) // If there are no winners (for current sessionKey)
                                echo "<div id='empty-drivers-message'>(No Winners Yet)</div>";
                            else {
                                foreach ($winners as $d) {
                                    $position++;
                                    if ($position > $num_drivers)
                                        break;
                                    $important = ($position == 1 || $position == 10 || $position == min($num_drivers, count($winners))) ? "imp" : "";
                                    
                                    echo
                                    "<tr>
                                        <td>
                                            <p>" . $position . ".</p>
                                        </td>
                                        <td>
                                            <img 
                                                class='driver-data widget widget-shadow $important' 
                                                src='../Images/driver_logos/" . $year . "/f1_" . $d->getNumber() . ".png?v=$VERSION'
                                            >
                                        </td>
                                    </tr>";
                                }
                            }
                        ?>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div id="footer"></div>
    
    <!-- <a href="logout.php">Logout</a> -->
</body>
</html>

<script>
    function refreshSessionKey(sessionKey) {
        var yearList = document.getElementById('choose-year-list');
        var year = yearList.value;
        sessionKey = sessionKey % 1000 + year * 1000
        
        window.location.replace("viewPicks.php?sessionKey=" + sessionKey);
    }
    
    const observeLoadingTime = () => {
        // Observe loadable elements (e.g., img, iframe)
        const loadableSelectors = ['img', 'iframe', 'script'];
        const otherSelectors = ['h1', 'p', 'div']; // Elements without `onload`
        
        // Measure time for loadable elements
        loadableSelectors.forEach((selector) => {
            document.querySelectorAll(selector).forEach((element) => {
                const startTime = performance.now();
                element.onload = () => {
                    const loadTime = performance.now() - startTime;
                    console.log(`${element.tagName}#${element.id || ''}.${element.className || ''} loaded in ${loadTime.toFixed(2)}ms`);
                };
            });
        });
        
        // Measure time for non-loadable elements
        otherSelectors.forEach((selector) => {
            document.querySelectorAll(selector).forEach((element) => {
                const renderTime = performance.now();
                console.log(`${element.tagName}#${element.id || ''}.${element.className || ''} rendered at ${renderTime.toFixed(2)}ms`);
            });
        });
    };
    
    window.onload = (event) => {
        // Keep the year list on the current value 
        const urlParams = new URLSearchParams(window.location.search);
        const sessionKey = urlParams.get("sessionKey");
        document.getElementById('choose-year-list').value = (sessionKey - sessionKey % 1000) / 1000;
        
        updateStandingsElements();
        togglePanels();
    }
    
    window.onresize = (event) => {
        updateStandingsElements();
    }
    
    function toggleBox() {
        event.stopPropagation();
        
        // Close any open boxes
        document.querySelectorAll('.hover-box').forEach(box => {
            if (box !== event.currentTarget.querySelector('.hover-box')) {
                box.style.display = 'none';
            }
        });
        
        // Toggle the current box
        var box = event.currentTarget.querySelector('.hover-box');
        box.style.display = box.style.display === 'block' ? 'none' : 'block';
    }
    
    const backer = $("#standings-backer");
    const header = $("#standings-head");
    const list = $("#standings-list");
    const plate = $("#standings-box");
    const info = $("#info-wrapper");
    plate.css("min-height", info.height() + 3.8 * header.height());
    // plate.css("min-height", info.height() + 2.5 * header.height());
    
    function updateStandingsElements() {        
        // Change style based on screen resolution so it stays within the standing entries
        const isPhone = window.matchMedia("(max-width: 767px)").matches;
        backer.css("top", header.offset().top + header.height() + "px");
        backer.css("left", header.offset().left + ((isPhone) ? 30 : 10) + "px");
        backer.height(list.height() - 2 * header.height());
        backer.css("display", "block");
        
        // Change height of plate behind to conform to the size of the standings list
        plate.height(list.height() + 2 * header.height());
    }
    
    const rightPanel = document.getElementById("info-wrapper");
    const leftPanel = document.getElementById("standings-list");
    const toggleBtn = document.getElementById("toggleBtn");
    let isHidden = false;
    
    function togglePanels() {        
        if (isHidden) { // When showing
            // Reduce left panel width first
            leftPanel.classList.remove("panel-expanded");
            toggleBtn.classList.remove("btn-moved");
            updateStandingsElements();
            
            setTimeout(() => {
                // Show right panel
                rightPanel.classList.remove("panel-hidden");
                rightPanel.style.position = "initial";
                toggleBtn.textContent = "→";
            }, 100); // Wait for left panel to shrink
        } else { // When hiding
            // Hide right panel first
            rightPanel.classList.add("panel-hidden");
            
            setTimeout(() => {
                // Expand left panel
                leftPanel.classList.add("panel-expanded");
                toggleBtn.classList.add("btn-moved");
                toggleBtn.textContent = "←";
                rightPanel.style.position = "absolute";
                updateStandingsElements();
            }, 200); // Shorter delay for right panel to hide
        }
        isHidden = !isHidden;
    }
    
    toggleBtn.addEventListener("click", togglePanels);
</script>
