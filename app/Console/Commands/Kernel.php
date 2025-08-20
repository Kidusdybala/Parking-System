<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CancelExpiredReservations;
use App\Console\Commands\ExpireReservations;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CancelExpiredReservations::class,
        ExpireReservations::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reservations:cancel-expired')->everyMinute();
        $schedule->command('reservations:expire')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
