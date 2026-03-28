<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organizacion;
use App\Models\RenovacionSuscripcion;
use App\Mail\RenovacionProximaMail;
use App\Mail\SuscripcionSuspendidaMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcesarRenovaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renovaciones:procesar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesar renovaciones de suscripciones pendientes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Procesando renovaciones de suscripciones...');

        // 1. Generar renovaciones para organizaciones activas que no tienen renovación pendiente
        $this->generarRenovaciones();

        // 2. Enviar notificaciones de vencimiento próximo
        $this->enviarNotificaciones();

        // 3. Suspender organizaciones con pagos vencidos
        $this->suspenderVencidas();

        $this->info('✅ Proceso completado.');
        return Command::SUCCESS;
    }

    /**
     * Generar renovaciones para el siguiente mes
     */
    private function generarRenovaciones()
    {
        $organizaciones = Organizacion::where('estado_suscripcion', 'activa')
            ->whereNotNull('fecha_fin_suscripcion')
            ->get();

        $generadas = 0;

        foreach ($organizaciones as $org) {
            // Verificar si ya tiene una renovación pendiente
            $tienePendiente = RenovacionSuscripcion::where('id_organizacion', $org->id)
                ->whereIn('estado', ['pendiente', 'procesando'])
                ->exists();

            if (!$tienePendiente) {
                $fechaVencimiento = Carbon::parse($org->fecha_fin_suscripcion);

                // Solo generar si el vencimiento está dentro de los próximos 30 días
                if ($fechaVencimiento->diffInDays(now(), false) <= 30 && $fechaVencimiento->isFuture()) {
                    RenovacionSuscripcion::create([
                        'id_organizacion' => $org->id,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'monto' => $org->suscripcion->precio_mensual,
                        'estado' => 'pendiente',
                    ]);
                    $generadas++;
                }
            }
        }

        $this->info("   📋 Generadas {$generadas} nuevas renovaciones");
    }

    /**
     * Enviar notificaciones de vencimiento
     */
    private function enviarNotificaciones()
    {
        $renovaciones = RenovacionSuscripcion::with('organizacion')
            ->where('estado', 'pendiente')
            ->get();

        $notificaciones7dias = 0;
        $notificaciones3dias = 0;
        $notificaciones1dia = 0;

        foreach ($renovaciones as $renovacion) {
            if ($renovacion->necesitaNotificacion7Dias()) {
                $this->enviarNotificacion($renovacion, '7dias');
                $notificaciones7dias++;
            } elseif ($renovacion->necesitaNotificacion3Dias()) {
                $this->enviarNotificacion($renovacion, '3dias');
                $notificaciones3dias++;
            } elseif ($renovacion->necesitaNotificacion1Dia()) {
                $this->enviarNotificacion($renovacion, '1dia');
                $notificaciones1dia++;
            }
        }

        if ($notificaciones7dias > 0) {
            $this->info("   📧 Enviadas {$notificaciones7dias} notificaciones (7 días)");
        }
        if ($notificaciones3dias > 0) {
            $this->info("   📧 Enviadas {$notificaciones3dias} notificaciones (3 días)");
        }
        if ($notificaciones1dia > 0) {
            $this->info("   📧 Enviadas {$notificaciones1dia} notificaciones (1 día)");
        }
    }

    /**
     * Enviar notificación por email
     */
    private function enviarNotificacion($renovacion, $tipo)
    {
        $diasRestantes = match($tipo) {
            '7dias' => 7,
            '3dias' => 3,
            '1dia' => 1,
            default => 0,
        };

        try {
            // Enviar email
            Mail::to($renovacion->organizacion->email_contacto)
                ->send(new RenovacionProximaMail($renovacion, $diasRestantes));

            // Marcar como notificada
            $renovacion->marcarNotificacion($tipo);

            $this->line("      → Notificación enviada a {$renovacion->organizacion->nombre_apr} ({$diasRestantes} días)");
        } catch (\Exception $e) {
            $this->error("      ✗ Error al enviar email a {$renovacion->organizacion->nombre_apr}: {$e->getMessage()}");
        }
    }

    /**
     * Suspender organizaciones con pagos vencidos
     */
    private function suspenderVencidas()
    {
        $renovacionesVencidas = RenovacionSuscripcion::with('organizacion')
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now()->toDateString())
            ->get();

        $suspendidas = 0;

        foreach ($renovacionesVencidas as $renovacion) {
            $org = $renovacion->organizacion;

            // Suspender organización
            $org->update([
                'estado_suscripcion' => 'suspendida',
                'activo' => false,
            ]);

            // Marcar renovación como fallida
            $renovacion->marcarComoFallida('Suspendida automáticamente por falta de pago');

            // Enviar notificación de suspensión
            if (!$renovacion->notificado_vencido) {
                try {
                    Mail::to($org->email_contacto)
                        ->send(new SuscripcionSuspendidaMail($renovacion));
                    $renovacion->update(['notificado_vencido' => now()]);
                } catch (\Exception $e) {
                    $this->error("      ✗ Error al enviar email de suspensión: {$e->getMessage()}");
                }
            }

            $suspendidas++;
            $this->warn("   ⚠️  Suspendida: {$org->nombre_apr}");
        }

        if ($suspendidas > 0) {
            $this->warn("   🔴 Total suspendidas: {$suspendidas}");
        } else {
            $this->info("   ✅ No hay organizaciones para suspender");
        }
    }
}
