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
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // Verificar suscripciones y notificar pagos pendientes diariamente a las 7:00 AM
        $schedule->command('suscripciones:verificar')
                 ->dailyAt('07:00')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // Procesar renovaciones de suscripciones diariamente a las 8:00 AM
        $schedule->command('renovaciones:procesar')
                 ->dailyAt('08:00')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // Aplicar cambios de plan pendientes (downgrades) diariamente a las 8:30 AM
        $schedule->command('cambios-plan:aplicar')
                 ->dailyAt('08:30')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // Enviar notificaciones de boletas vencidas diariamente a las 9:00 AM
        $schedule->command('notificaciones:boletas-vencidas')
                 ->dailyAt('09:00')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // Enviar resumen mensual el primer día de cada mes a las 10:00 AM
        $schedule->command('resumen:mensual')
                 ->monthlyOn(1, '10:00')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // ========================================
        // BACKUPS AUTOMÁTICOS
        // ========================================

        // Backup completo de base de datos diariamente a las 2:00 AM
        $schedule->command('backup:run --only-db')
                 ->dailyAt('02:00')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // Limpiar backups antiguos (más de 30 días) semanalmente
        $schedule->command('backup:clean')
                 ->weekly()
                 ->sundays()
                 ->at('03:00')
                 ->emailOutputOnFailure('sistemaapr@gmail.com');

        // ========================================
        // VERIFICACIÓN AUTOMÁTICA DE DTES
        // ========================================

        // Verificar estado de DTEs en SII cada 6 horas
        $schedule->call(function () {
            \App\Jobs\VerificarEstadoDTEs::dispatch();
        })->everyFourHours()
          ->name('verificar-estado-dtes')
          ->withoutOverlapping()
          ->onFailure(function () {
              \Log::error('Falló la verificación automática de estados DTE');
          });

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
