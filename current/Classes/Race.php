<?php

final class Race {
    public $sessionKey;
    public $dateStart; // DateTime
    public $name;
    public $type;

    public function __construct($sessionKey, $dateStart, $name, $type) {
        $this->sessionKey = $sessionKey;
        $this->setDateStart($dateStart);
        $this->name = $name;
        $this->type = $type;
    }

    private function formatDate($SQLDate): DateTime {
        // Convert from MySQL's syntax: YYYY-MM-DD HH:MI:SS
        $timezone = new DateTimeZone("US/Central");
        return DateTime::createFromFormat('Y-m-d H:i:s', $SQLDate)->setTimezone($timezone);
    }

    public function getDate() {
    	// Format month
        $monthCapital = strtoupper($this->dateStart->format('F'));
        $month = ucfirst(strtolower($monthCapital));

        // Format day
        $day = $this->dateStart->format('j');
        $daySuffix = 'th';
        if ($day % 10 == 1 && $day != 11) {
            $daySuffix = 'st';
        } elseif ($day % 10 == 2 && $day != 12) {
            $daySuffix = 'nd';
        } elseif ($day % 10 == 3 && $day != 13) {
            $daySuffix = 'rd';
        }

        $date = $month . " " . $day . $daySuffix . ", " . $this->dateStart->format('Y');
        return $date;
    }

    public function setDateStart($SQLDate) {
        $this->dateStart = $this->formatDate($SQLDate);
    }
}
