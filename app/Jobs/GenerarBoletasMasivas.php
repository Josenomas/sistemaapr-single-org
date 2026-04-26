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
            DB::connection('mysql')->statement('CALL sistema_apr.sp_generar_boletas_mes(?)', [$this->mes, $this->idOrganizacion]);

            // Obtener boletas recién generadas
            $boletasGeneradas = Boleta::activos()
                ->where('mes', $this->mes)
                ->whereNull('folio_sii')
                ->get();

            $totalGeneradas = $boletasGeneradas->count();
            $foliosAsignados = 0;

            // Asignar folios SII en chunks de 100
            $boletasGeneradas->chunk(100)->each(function ($chunk) use (&$foliosAsignados) {
                foreach ($chunk as $boleta) {
                    $folioAsignado = $boleta->asignarFolioSII('boleta');
                    if ($folioAsignado) {
                        $boleta->save();
                        $foliosAsignados++;
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
                'tiempo_segundos' => $tiempoTranscurrido
            ]);

            // Enviar notificación en sistema
            $usuario = User::find($this->userId);
            if ($usuario) {
                try {
                    NotificacionSistema::create([
                        'titulo' => '✅ Generación de Boletas Completada',
                        'mensaje' => "Se generaron {$totalGeneradas} boletas para " . \Carbon\Carbon::createFromFormat('Y-m', $this->mes)->locale('es')->isoFormat('MMMM YYYY') . 
                                    ($foliosAsignados > 0 ? ". Se asignaron {$foliosAsignados} folios SII" : '') .
                                    ". Tiempo: {$tiempoTranscurrido}s",
                        'tipo' => 'otro',
                        'icono' => 'check-circle',
                        'url' => route('boletas.index'),
                        'id_usuario' => $this->userId,
                        'id_organizacion' => $this->idOrganizacion,
                        'leida' => 0
                    ]);
                } catch (\Exception $e) {
                    Log::warning('No se pudo crear notificación del sistema', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error en generación masiva de boletas', [
                'mes' => $this->mes,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Crear notificación de error
            try {
                NotificacionSistema::create([
                    'titulo' => '❌ Error en Generación de Boletas',
                    'mensaje' => "No se pudieron generar las boletas de {$this->mes}. Error: " . $e->getMessage(),
                    'tipo' => 'otro',
                    'icono' => 'exclamation-circle',
                    'url' => route('boletas.generar'),
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
                'titulo' => '🔴 Generación de Boletas Falló',
                'mensaje' => "La generación de boletas para {$this->mes} falló después de 3 intentos. Por favor, contacta al soporte técnico.",
                'tipo' => 'otro',
                'icono' => 'times-circle',
                'url' => route('boletas.generar'),
                'id_usuario' => $this->userId,
                'id_organizacion' => $this->idOrganizacion,
                'leida' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('No se pudo crear notificación de fallo', ['error' => $e->getMessage()]);
        }
    }
}
