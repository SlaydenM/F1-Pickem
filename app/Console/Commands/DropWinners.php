<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Winner;

#[Description('Command description')]
class DropWinners extends Command
{
    protected $signature = 'app:drop-winners 
                        {sessionKey : The session key for which to drop winners} 
                        {--queue= : Which queue connection to use}';
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Winner::where('session_key', (int) $this->argument('sessionKey'))->delete();
    }
}
