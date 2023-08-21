<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:initialize-expected-schedule')->daily();
        $schedule->command('app:check-subscription-expiry')->daily();
        $schedule->command('app:generate-payroll')->dailyAt('00:15');
        $schedule->command('app:initialize-holiday')->yearly();
        $schedule->command('app:update-pending-employees-status')->dailyAt('00:05');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
