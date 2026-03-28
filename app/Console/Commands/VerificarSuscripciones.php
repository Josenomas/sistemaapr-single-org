<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organizacion;
use App\Models\PagoSuscripcion;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuscripcionPorVencer;
use App\Mail\SuscripcionVencida;

class VerificarSuscripciones extends Command
{
    protected $signature = 'suscripciones:verificar';
    protected $description = 'Verifica suscripciones vencidas, por vencer y genera pagos mensuales';

    public function handle()
    {
        $this->info('🔍 Verificando suscripciones...');

        // 1. Generar pagos mensuales para organizaciones activas
        $this->generarPagosMensuales();

        // 2. Notificar suscripciones por vencer (7 días antes)
        $this->notificarPorVencer();

        // 3. Notificar suscripciones por vencer (3 días antes)
        $this->notificarPorVencer(3);

        // 4. Notificar suscripciones por vencer (1 día antes)
        $this->notificarPorVencer(1);

        // 5. Suspender organizaciones con pagos vencidos
        $this->suspenderOrganizacionesVencidas();

        $this->info('✅ Verificación completada');
        return Command::SUCCESS;
    }

    /**
     * Generar pagos mensuales para organizaciones activas
     */
    private function generarPagosMensuales()
    {
        $this->info('💳 Generando pagos mensuales...');

        $organizaciones = Organizacion::where('activo', true)
            ->where('estado_suscripcion', 'activa')
            ->get();

        $generados = 0;

        foreach ($organizaciones as $org) {
            // Verificar si ya tiene un pago para el próximo período
            $finPeriodoActual = $org->fecha_fin_periodo ?? now()->endOfMonth();
            $inicioPeriodoNuevo = $finPeriodoActual->copy()->addDay();
            $finPeriodoNuevo = $inicioPeriodoNuevo->copy()->endOfMonth();

            $pagoExiste = PagoSuscripcion::where('id_organizacion', $org->id)
                ->where('periodo_inicio', $inicioPeriodoNuevo->format('Y-m-d'))
                ->exists();

            if (!$pagoExiste) {
                PagoSuscripcion::create([
                    'id_organizacion' => $org->id,
                    'id_suscripcion' => $org->id_suscripcion,
                    'monto' => $org->suscripcion->precio_mensual,
                    'metodo_pago' => 'flow',
                    'estado' => 'pendiente',
                    'periodo_inicio' => $inicioPeriodoNuevo,
                    'periodo_fin' => $finPeriodoNuevo,
                    'fecha_vencimiento' => $inicioPeriodoNuevo->copy()->addDays(5), // 5 días para pagar
                ]);

                $generados++;
            }
        }

        $this->info("  → {$generados} pagos generados");
    }

    /**
     * Notificar suscripciones por vencer
     */
    private function notificarPorVencer($dias = 7)
    {
        $pagosPorVencer = PagoSuscripcion::with(['organizacion', 'suscripcion'])
            ->pendientes()
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addDays($dias))
            ->whereDate('fecha_vencimiento', now()->addDays($dias)->format('Y-m-d'))
            ->get();

        foreach ($pagosPorVencer as $pago) {
            try {
                Mail::to($pago->organizacion->email_contacto)->send(
                    new SuscripcionPorVencer($pago, $dias)
                );

                // Crear notificación en el sistema
                NotificacionSistema::notificarPagoPendiente($pago->organizacion, $pago, $dias);

                $this->info("  → Notificación enviada a {$pago->organizacion->nombre_apr} ({$dias} días)");
            } catch (\Exception $e) {
                $this->error("  ✗ Error enviando email a {$pago->organizacion->nombre_apr}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Suspender organizaciones con pagos vencidos
     */
    private function suspenderOrganizacionesVencidas()
    {
        $this->info('⚠️  Suspendiendo organizaciones con pagos vencidos...');

        $pagosVencidos = PagoSuscripcion::with('organizacion')
            ->vencidos()
            ->get();

        $suspendidas = 0;

        foreach ($pagosVencidos as $pago) {
            $org = $pago->organizacion;

            // Verificar que la organización no esté ya suspendida
            if ($org->activo && $org->estado_suscripcion === 'activa') {
                $org->update([
                    'activo' => false,
                    'estado_suscripcion' => 'suspendida',
                ]);

                // Enviar email de notificación
                try {
                    Mail::to($org->email_contacto)->send(
                        new SuscripcionVencida($pago)
                    );
                } catch (\Exception $e) {
                    $this->error("  ✗ Error enviando email: {$e->getMessage()}");
                }

                // Crear notificación en el sistema
                NotificacionSistema::notificarCuentaSuspendida($org, $pago);

                $this->warn("  → {$org->nombre_apr} suspendida por falta de pago");
                $suspendidas++;
            }
        }

        $this->info("  → {$suspendidas} organizaciones suspendidas");
    }
}
