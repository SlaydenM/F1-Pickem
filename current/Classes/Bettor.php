<?php
class Bettor {
    private $id;
    private $name;
    private $score;
    private $bets;
    private $bonus;

    public function __construct($id, $bets = [0, 0, 0], $name='', $bonus=1.00) {
        $this->id = $id;
        $this->name = $name;
        $this->score = 0;
        $this->bets = $bets;
        $this->bonus = $bonus;
    }
    
    // Find score for a specific session
    public function findScore($conn, $DB_NAME, $sessionKey) {
        $query = "SELECT score FROM $DB_NAME.bettors WHERE userID = :userID AND sessionKey = :sessionKey";
        $stmt = $conn->prepare($query);
        $stmt->execute(['userID' => $this->id, 'sessionKey' => $sessionKey]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->score = $row['score'];
            return $this->score;
        }
        return 0; // Default if no score is found.
    }

    // Find total score across all sessions
    public function findTotalScore($conn, $DB_NAME, $yearKey) {
        $query = "SELECT SUM(score) AS total FROM $DB_NAME.bettors WHERE userID = :userID AND sessionKey BETWEEN :yearKey AND :nextYearKey";
        $stmt = $conn->prepare($query);
        $stmt->execute(['userID' => $this->id, 'yearKey' => $yearKey, 'nextYearKey' => $yearKey + 1000]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'] ?? 0;
    }

    // Insert new entry into the database
    public function databaseInsert($conn, $DB_NAME, $sessionKey) {
        // Check if a record already exists for this user and session
        $queryCheck = "SELECT 1 FROM $DB_NAME.bettors WHERE userID = :userID AND sessionKey = :sessionKey";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute(['userID' => $this->id, 'sessionKey' => $sessionKey]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$row) { // If no record exists
            // Insert new record
            $queryInsert = "INSERT INTO bettors (userID, score, bets, bonus, sessionKey) 
                            VALUES (:userID, :score, :bets, :bonus, :sessionKey)";
            $stmtInsert = $conn->prepare($queryInsert);
            $stmtInsert->execute([
                'userID' => $this->id,
                'score' => 0, // Default score for a new session
                'bets' => join(',', $this->bets),
                'bonus' => $this->bonus,
                'sessionKey' => $sessionKey
            ]);
        }
    }

    // Getter and setter methods
    public function getBets() {
        return $this->bets;
    }
    
    public function setScore($newScore) {
        $this->score = $newScore;
    }

    public function getScore() {
        return $this->score;
    }
    
    public function incrementScore($score) {
        $this->score += $score;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getBonus() {
        return $this->bonus;
    }

    public function setBonus($bonus) { 
        $this->bonus = $bonus;
    }
}
?>
