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
        // Actualizar días de prueba restantes diariamente a las 00:00 (medianoche)
        $schedule->command('suscripciones:actualizar-dias-prueba')
                 ->daily()
                 ->emailOutputOnFailure('admin@aprpitrelahue.cl');

        // Verificar suscripciones y notificar pagos pendientes diariamente a las 7:00 AM
        $schedule->command('suscripciones:verificar')
                 ->dailyAt('07:00')
                 ->emailOutputOnFailure('admin@aprpitrelahue.cl');

        // Procesar renovaciones de suscripciones diariamente a las 8:00 AM
        $schedule->command('renovaciones:procesar')
                 ->dailyAt('08:00')
                 ->emailOutputOnFailure('admin@aprpitrelahue.cl');

        // Enviar notificaciones de boletas vencidas diariamente a las 9:00 AM
        $schedule->command('notificaciones:boletas-vencidas')
                 ->dailyAt('09:00')
                 ->emailOutputOnFailure('admin@aprpitrelahue.cl');

        // Enviar resumen mensual el primer día de cada mes a las 10:00 AM
        $schedule->command('resumen:mensual')
                 ->monthlyOn(1, '10:00')
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
