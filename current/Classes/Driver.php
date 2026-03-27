<?php

class Driver {
    private $name;
    private $team;
    private $number;
    private $position;

    public function __construct($name=null, $team=null, $number=0, $position=0) {
        $this->setName($name);
        $this->setTeam($team);
        $this->setNumber($number);
        $this->setPosition($position);
    }

    public function getNumWins($conn, $DB_NAME, $sessionKey) {
        /*
        Returns an array (size 3) of the number of times the driver has won each [1st, 10th, 20th]
        Counts all drivers across the season (giver sessionKey) where position is [1st, 10th, 20th] and winner id is self->id
        */
        $numWins = [];
        try {
            foreach ([1, 10, 20] as $position) {
                $query = "SELECT COUNT(*) FROM $DB_NAME.drivers WHERE sessionKey > :yearKey AND position = :position AND number = :number"; // Calculate across the season
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'yearKey' => floor($sessionKey / 1000) * 1000, // Within this season
                    'position' => $position,
                    'number' => $this->getNumber()
                ]); 
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $numWins[] = $row['COUNT(*)']; // Add total to number of wins
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }

        return $numWins;
    }

    public function getPercentWins($numWins, $sessionKey) {
        $percentWins = [];
        $totalRaces = $sessionKey % 1000; // Only calculate up to the specified session 

        for ($posIndex = 0; $posIndex < 3; $posIndex++)
            $percentWins[] = $numWins[$posIndex] / $totalRaces * 100; // Divide wins by total
        
        return $percentWins;
    }

    public function toString() {
        return "Position $this->position: $this->name ($this->team)";
    }

    public function getName() { return $this->name; }

    public function setName($name) { $this->name = $name; }

    public function getTeam() { return $this->team; }

    public function setTeam($team) { $this->team = $team; }

    public function getNumber() { return $this->number; }

    public function setNumber($number) { $this->number = $number; }

    public function getPosition() { return $this->position; }

    public function setPosition($position) { $this->position = $position; }
}

?>
