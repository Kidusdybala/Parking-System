<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CancelExpiredReservations;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CancelExpiredReservations::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reservations:cancel-expired')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
