<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Enviar notificaciones de boletas vencidas diariamente a las 9:00 AM
        $schedule->command('notificaciones:boletas-vencidas')
                 ->dailyAt('09:00')
                 ->emailOutputOnFailure('admin@aprpitrelahue.cl');

        // Alternativa: Enviar cada 3 días a las 9:00 AM
        // $schedule->command('notificaciones:boletas-vencidas')
        //          ->days([1, 4, 7, 10, 13, 16, 19, 22, 25, 28])
        //          ->at('09:00')
        //          ->emailOutputOnFailure('admin@aprpitrelahue.cl');

        // Alternativa: Enviar los lunes a las 9:00 AM
        // $schedule->command('notificaciones:boletas-vencidas')
        //          ->weekly()
        //          ->mondays()
        //          ->at('09:00')
        //          ->emailOutputOnFailure('admin@aprpitrelahue.cl');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
