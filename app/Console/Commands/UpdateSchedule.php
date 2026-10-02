<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PickemService;

class UpdateSchedule extends Command
{
    protected $signature = 'app:update-schedule 
                        {year=2026 : The year for which to update the schedule}';
    /**
     * Execute the console command.
     */
    public function handle(PickemService $service)
    {
        $service->updateSchedule((int) $this->argument('year'));
        $this->info("Schedule updated for year: " . $this->argument('year'));
    }
}
