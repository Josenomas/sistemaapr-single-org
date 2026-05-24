<?php

namespace App\Jobs;

use App\Models\Boleta;
use App\Services\LibreDTEService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DTERechazoNotificacion;

class VerificarEstadoDTEs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $organizacionId;

    /**
     * Create a new job instance.
     */
    public function __construct($organizacionId = null)
    {
        $this->organizacionId = $organizacionId;
    }

    /**
     * Execute the job.
     */
    public function handle(LibreDTEService $libredteService)
    {
        Log::info('Iniciando verificación automática de estados DTE', [
            'organizacion_id' => $this->organizacionId,
        ]);

        // Obtener DTEs pendientes de verificación (estado 'emitida')
        $query = Boleta::whereNotNull('folio_sii')
            ->where('estado_dte', 'emitida')
            ->where('fecha_emision_dte', '>=', now()->subDays(7)); // Solo últimos 7 días

        if ($this->organizacionId) {
            $query->where('id_organizacion', $this->organizacionId);
        }

        $dtesPendientes = $query->get();

        Log::info("DTEs pendientes de verificación: {$dtesPendientes->count()}");

        $verificados = 0;
        $aceptados = 0;
        $rechazados = 0;
        $errores = 0;

        foreach ($dtesPendientes as $dte) {
            try {
                // Configurar organización
                $libredteService->setOrganizacion($dte->id_organizacion);

                // Verificar estado en SII
                $resultado = $libredteService->verificarEstadoSII($dte->folio_sii, $dte->tipo_dte ?? 39);

                if ($resultado['success']) {
                    $estadoSII = strtoupper($resultado['estado']);

                    // Mapear estado SII a estado local
                    if (in_array($estadoSII, ['ACEPTADO', 'ACEPTADA', 'APROBADO', 'APROBADA'])) {
                        $dte->estado_dte = 'aceptada';
                        $dte->observaciones_dte = $resultado['glosa'] ?? 'Aceptada por SII';
                        $dte->save();
                        $aceptados++;

                        Log::info("DTE aceptada por SII", [
                            'folio' => $dte->folio_sii,
                            'tipo_dte' => $dte->tipo_dte,
                            'boleta_id' => $dte->id,
                        ]);

                    } elseif (in_array($estadoSII, ['RECHAZADO', 'RECHAZADA'])) {
                        $dte->estado_dte = 'rechazada';
                        $dte->observaciones_dte = $resultado['glosa'] ?? 'Rechazada por SII';
                        $dte->save();
                        $rechazados++;

                        Log::warning("DTE rechazada por SII", [
                            'folio' => $dte->folio_sii,
                            'tipo_dte' => $dte->tipo_dte,
                            'boleta_id' => $dte->id,
                            'glosa' => $resultado['glosa'],
                        ]);

                        // Enviar notificación por email
                        $this->enviarNotificacionRechazo($dte, $resultado['glosa']);
                    }

                    $verificados++;
                } else {
                    $errores++;
                    Log::error("Error al verificar DTE", [
                        'folio' => $dte->folio_sii,
                        'error' => $resultado['error'] ?? 'Error desconocido',
                    ]);
                }

                // Delay entre consultas para no sobrecargar la API
                sleep(2);

            } catch (\Exception $e) {
                $errores++;
                Log::error("Excepción al verificar estado DTE", [
                    'folio' => $dte->folio_sii,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Verificación automática completada', [
            'total_procesados' => $verificados,
            'aceptados' => $aceptados,
            'rechazados' => $rechazados,
            'errores' => $errores,
        ]);
    }

    /**
     * Enviar notificación de rechazo por email
     */
    protected function enviarNotificacionRechazo(Boleta $dte, $glosa)
    {
        try {
            $config = $dte->organizacion->configuracionLibreDTE;
            if ($config && $config->notificar_rechazos && $config->email_notificaciones) {
                Mail::to($config->email_notificaciones)->send(new DTERechazoNotificacion($dte, $glosa));

                Log::info("Notificación de rechazo enviada", [
                    'folio' => $dte->folio_sii,
                    'email' => $config->email_notificaciones,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de rechazo", [
                'folio' => $dte->folio_sii,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
