<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CancelExpiredReservations;
use App\Console\Commands\ExpireReservations;
use App\Console\Commands\VerifyPendingPayments;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CancelExpiredReservations::class,
        ExpireReservations::class,
        VerifyPendingPayments::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reservations:cancel-expired')->everyMinute();
        $schedule->command('reservations:expire')->everyMinute();
        
        // Verify pending payments every 5 minutes
        $schedule->command('chapa:verify-pending')->everyFiveMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
