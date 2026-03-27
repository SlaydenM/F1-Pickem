<?php
class Weekend {
    public $races = [];
    public $isSprint = False;

    public function __construct($races=null) {
        $this->races = $races;
        
        // Check if sprint weekend
        $isSprint = False;
        foreach ($races as $race) {
            if ($race->type == "S") { // One of the races is a sprint
                $isSprint = True;
                break;
            }
        }

        $this->isSprint = $isSprint;
    }
}