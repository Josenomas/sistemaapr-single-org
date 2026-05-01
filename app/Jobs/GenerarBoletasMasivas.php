<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Boleta;
use App\Models\User;
use App\Models\NotificacionSistema;
use App\Helpers\ActividadHelper;

class GenerarBoletasMasivas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mes;
    public $idOrganizacion;
    public $userId;
    public $timeout = 600; // 10 minutos de timeout
    public $tries = 3; // 3 intentos

    /**
     * Create a new job instance.
     */
    public function __construct(string $mes, int $idOrganizacion, int $userId)
    {
        $this->mes = $mes;
        $this->idOrganizacion = $idOrganizacion;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $tiempoInicio = microtime(true);
        
        Log::info('Iniciando generación masiva de boletas', [
            'mes' => $this->mes,
            'id_organizacion' => $this->idOrganizacion,
            'user_id' => $this->userId
        ]);

        DB::beginTransaction();
        try {
            // Ejecutar procedimiento almacenado
            DB::connection('mysql')->statement('CALL sp_generar_boletas_mes(?, ?)', [$this->mes, $this->idOrganizacion]);

            // Obtener boletas recién generadas
            $boletasGeneradas = Boleta::activos()
                ->where('mes', $this->mes)
                ->whereNull('folio_sii')
                ->get();

            $totalGeneradas = $boletasGeneradas->count();
            $foliosAsignados = 0;

            // Asignar folios SII en chunks de 100
            $erroresFolios = 0;
            $boletasGeneradas->chunk(100)->each(function ($chunk) use (&$foliosAsignados, &$erroresFolios) {
                foreach ($chunk as $boleta) {
                    try {
                        $folioAsignado = $boleta->asignarFolioSII('boleta');
                        if ($folioAsignado) {
                            $boleta->save();
                            $foliosAsignados++;
                        } else {
                            $erroresFolios++;
                        }
                    } catch (\Exception $e) {
                        $erroresFolios++;
                        Log::warning("Error asignando folio SII a boleta {$boleta->id}", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

            // Registrar actividad
            ActividadHelper::registrar(
                'Boletas',
                "Generación masiva de boletas para {$this->mes}: {$totalGeneradas} boletas creadas" .
                ($foliosAsignados > 0 ? " | {$foliosAsignados} folios SII asignados" : ""),
                $this->userId
            );

            DB::commit();

            $tiempoFin = microtime(true);
            $tiempoTranscurrido = round($tiempoFin - $tiempoInicio, 2);

            Log::info('Generación masiva de boletas completada', [
                'mes' => $this->mes,
                'total_generadas' => $totalGeneradas,
                'folios_asignados' => $foliosAsignados,
                'errores_folios' => $erroresFolios,
                'tiempo_segundos' => $tiempoTranscurrido
            ]);

            // Enviar notificación en sistema
            try {
                // Determinar color y prioridad según errores de folios
                $color = 'success';
                $prioridad = 'alta';
                $titulo = 'Generación de Boletas Completada';

                $mensaje = "Se generaron {$totalGeneradas} boletas para " . \Carbon\Carbon::createFromFormat('Y-m', $this->mes)->locale('es')->isoFormat('MMMM YYYY');

                if ($erroresFolios > 0) {
                    $color = 'warning';
                    $prioridad = 'alta';
                    $titulo = 'Generación de Boletas - Con Advertencias';
                    $mensaje .= ". Se asignaron {$foliosAsignados} folios SII correctamente";
                    $mensaje .= ", pero {$erroresFolios} boleta(s) no pudieron obtener folio (verifica folios disponibles en SII)";
                } elseif ($foliosAsignados > 0) {
                    $mensaje .= ". Se asignaron {$foliosAsignados} folios SII exitosamente";
                }

                NotificacionSistema::create([
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'tipo' => 'otro',
                    'prioridad' => $prioridad,
                    'icono' => $erroresFolios > 0 ? 'fa-exclamation-triangle' : 'fa-file-invoice',
                    'color' => $color,
                    'url' => '/boletas',
                    'texto_accion' => 'Ver Boletas',
                    'id_usuario' => $this->userId,
                    'id_organizacion' => $this->idOrganizacion,
                    'leida' => 0
                ]);
                Log::info('Notificación de generación de boletas creada', [
                    'user_id' => $this->userId,
                    'total_boletas' => $totalGeneradas,
                    'folios_asignados' => $foliosAsignados,
                    'errores_folios' => $erroresFolios
                ]);
            } catch (\Exception $e) {
                Log::error('No se pudo crear notificación del sistema', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            // Determinar tipo de error
            $tipoError = 'desconocido';
            $mensajeDetallado = $e->getMessage();

            if (str_contains($mensajeDetallado, 'sp_generar_boletas_mes')) {
                $tipoError = 'procedimiento_almacenado';
            } elseif (str_contains($mensajeDetallado, 'Connection') || str_contains($mensajeDetallado, 'timeout')) {
                $tipoError = 'conexion_base_datos';
            } elseif (str_contains($mensajeDetallado, 'folio') || str_contains($mensajeDetallado, 'SII')) {
                $tipoError = 'asignacion_folios_sii';
            }

            Log::error('Error en generación masiva de boletas', [
                'mes' => $this->mes,
                'tipo_error' => $tipoError,
                'error' => $mensajeDetallado,
                'trace' => $e->getTraceAsString()
            ]);

            // Crear notificación de error detallada
            try {
                $mensajeUsuario = match($tipoError) {
                    'procedimiento_almacenado' => "Error al generar boletas de " . \Carbon\Carbon::createFromFormat('Y-m', $this->mes)->locale('es')->isoFormat('MMMM YYYY') . ". Problema en el proceso de generación.",
                    'conexion_base_datos' => "Error de conexión con la base de datos al generar boletas de " . \Carbon\Carbon::createFromFormat('Y-m', $this->mes)->locale('es')->isoFormat('MMMM YYYY') . ". Intenta nuevamente.",
                    'asignacion_folios_sii' => "Las boletas se generaron pero hubo un error al asignar folios SII. Verifica los folios disponibles.",
                    default => "Error inesperado al generar boletas de " . \Carbon\Carbon::createFromFormat('Y-m', $this->mes)->locale('es')->isoFormat('MMMM YYYY') . "."
                };

                NotificacionSistema::create([
                    'titulo' => 'Error en Generación de Boletas',
                    'mensaje' => $mensajeUsuario,
                    'tipo' => 'otro',
                    'prioridad' => 'urgente',
                    'icono' => 'fa-exclamation-triangle',
                    'color' => 'danger',
                    'url' => '/boletas/generar',
                    'texto_accion' => 'Reintentar',
                    'id_usuario' => $this->userId,
                    'id_organizacion' => $this->idOrganizacion,
                    'leida' => 0
                ]);
            } catch (\Exception $notifError) {
                Log::error('No se pudo crear notificación de error', ['error' => $notifError->getMessage()]);
            }

            // Re-lanzar excepción para que Laravel maneje el retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        Log::error('Job GenerarBoletasMasivas falló después de todos los intentos', [
            'mes' => $this->mes,
            'id_organizacion' => $this->idOrganizacion,
            'error' => $exception->getMessage()
        ]);

        // Notificación final de fallo
        try {
            NotificacionSistema::create([
                'titulo' => 'Generación de Boletas Falló',
                'mensaje' => "La generación de boletas para {$this->mes} falló después de 3 intentos. Contacta al soporte técnico.",
                'tipo' => 'otro',
                'prioridad' => 'urgente',
                'icono' => 'fa-times-circle',
                'color' => 'danger',
                'url' => '/boletas/generar',
                'texto_accion' => 'Ver Detalles',
                'id_usuario' => $this->userId,
                'id_organizacion' => $this->idOrganizacion,
                'leida' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('No se pudo crear notificación de fallo', ['error' => $e->getMessage()]);
        }
    }
}
