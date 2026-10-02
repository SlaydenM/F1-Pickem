<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PickemService;

class UpdateBonuses extends Command
{
    protected $signature = 'app:update-bonuses 
                        {sessionKey? : The session key for which to update bonuses}';
    /**
     * Execute the console command.
     */
    public function handle(PickemService $service)
    {
        $sessionKey = $this->argument('sessionKey');
        if (!$sessionKey) {
            $sessionKey = $service->getSessionKey();
            $this->info("No session key provided. Updating bonuses for session: {$sessionKey}");
        } else {
            $this->info("Updating bonuses specifically for session: {$sessionKey}");
        }
        $service->updateBonuses($sessionKey);

        $this->info("Bonuses updated for session key: {$sessionKey}");
    }
}
