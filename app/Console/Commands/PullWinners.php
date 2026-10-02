<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Command;
use App\Services\PickemService;

#[Description('Command description')]
class PullWinners extends Command
{
    protected $signature = 'app:pull-winners 
                        {sessionKey : The session key for which to pull winners} 
                        {--queue= : Which queue connection to use}';
    /**
     * Execute the console command.
     */
    public function handle(PickemService $service)
    {
        $service->pullWinners((int) $this->argument('sessionKey'));
        $this->info("Winners pulled for session key: " . $this->argument('sessionKey'));
    }
}
