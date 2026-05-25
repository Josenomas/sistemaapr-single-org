<?php

namespace App\Services;

use App\Models\AlertaDTE;
use App\Models\ConfiguracionDTE;
use App\Models\Boleta;
use Carbon\Carbon;

class AlertaDTEService
{
    /**
     * Verificar y generar alertas para una organización
     */
    public function verificarAlertas($idOrganizacion)
    {
        $config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)
            ->where('activo', true)
            ->first();

        if (!$config) {
            return; // No hay configuración DTE
        }

        // Verificar alertas
        $this->verificarConexionLibreDTE($idOrganizacion, $config);
        $this->verificarAmbienteCertificacion($idOrganizacion, $config);
        $this->verificarLibroVentasPendiente($idOrganizacion);
        $this->verificarHitosDTEs($idOrganizacion);
    }

    /**
     * Verificar conexión con LibreDTE
     */
    protected function verificarConexionLibreDTE($idOrganizacion, $config)
    {
        try {
            $service = new LibreDTEService();
            $service->setOrganizacion($idOrganizacion);
            $conectado = $service->verificarConexion();

            if (!$conectado) {
                // Verificar si ya existe una alerta activa de este tipo
                $alertaExistente = AlertaDTE::where('id_organizacion', $idOrganizacion)
                    ->where('tipo', 'conexion_fallida')
                    ->activas()
                    ->first();

                if (!$alertaExistente) {
                    AlertaDTE::crearConexionFallida($idOrganizacion, 'No se pudo establecer conexión');
                }
            } else {
                // Si hay conexión, resolver alertas de conexión fallida
                AlertaDTE::where('id_organizacion', $idOrganizacion)
                    ->where('tipo', 'conexion_fallida')
                    ->activas()
                    ->each(fn($alerta) => $alerta->marcarComoResuelta());
            }
        } catch (\Exception $e) {
            // Si hay error, crear alerta
            $alertaExistente = AlertaDTE::where('id_organizacion', $idOrganizacion)
                ->where('tipo', 'conexion_fallida')
                ->activas()
                ->first();

            if (!$alertaExistente) {
                AlertaDTE::crearConexionFallida($idOrganizacion, $e->getMessage());
            }
        }
    }

    /**
     * Verificar si está en ambiente de certificación por mucho tiempo
     */
    protected function verificarAmbienteCertificacion($idOrganizacion, $config)
    {
        if ($config->ambiente === 'certificacion') {
            // Verificar si lleva más de 30 días en certificación
            $diasEnCertificacion = Carbon::parse($config->updated_at)->diffInDays(now());

            if ($diasEnCertificacion > 30) {
                // Verificar si ya existe alerta
                $alertaExistente = AlertaDTE::where('id_organizacion', $idOrganizacion)
                    ->where('tipo', 'ambiente_certificacion')
                    ->activas()
                    ->first();

                if (!$alertaExistente) {
                    AlertaDTE::crearAmbienteCertificacion($idOrganizacion);
                }
            }
        } else {
            // Si está en producción, resolver alertas de certificación
            AlertaDTE::where('id_organizacion', $idOrganizacion)
                ->where('tipo', 'ambiente_certificacion')
                ->activas()
                ->each(fn($alerta) => $alerta->marcarComoResuelta());
        }
    }

    /**
     * Verificar libro de ventas pendiente
     */
    protected function verificarLibroVentasPendiente($idOrganizacion)
    {
        // Verificar si hay DTEs del mes anterior sin libro generado
        $mesAnterior = now()->subMonth();
        $inicioMes = $mesAnterior->copy()->startOfMonth();
        $finMes = $mesAnterior->copy()->endOfMonth();

        $dtesMesAnterior = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->whereBetween('fecha_emision_dte', [$inicioMes, $finMes])
            ->count();

        if ($dtesMesAnterior > 0 && now()->day >= 10) {
            // Si es después del día 10 del mes y hay DTEs del mes anterior, crear alerta
            $alertaExistente = AlertaDTE::where('id_organizacion', $idOrganizacion)
                ->where('tipo', 'libro_ventas_pendiente')
                ->where('created_at', '>=', now()->startOfMonth())
                ->first();

            if (!$alertaExistente) {
                AlertaDTE::create([
                    'id_organizacion' => $idOrganizacion,
                    'tipo' => 'libro_ventas_pendiente',
                    'nivel' => 'advertencia',
                    'titulo' => 'Libro de Ventas pendiente',
                    'mensaje' => "Recuerda enviar el Libro de Ventas de {$mesAnterior->format('F Y')} al SII. Tienes {$dtesMesAnterior} DTEs emitidos.",
                    'datos_adicionales' => [
                        'mes' => $mesAnterior->format('Y-m'),
                        'total_dtes' => $dtesMesAnterior
                    ],
                ]);
            }
        }
    }

    /**
     * Verificar hitos de DTEs emitidos
     */
    protected function verificarHitosDTEs($idOrganizacion)
    {
        $totalDTEs = Boleta::where('id_organizacion', $idOrganizacion)
            ->whereNotNull('folio_sii')
            ->count();

        $hitos = [100, 500, 1000, 5000, 10000];

        foreach ($hitos as $hito) {
            if ($totalDTEs >= $hito) {
                // Verificar si ya se creó alerta de este hito
                $alertaExistente = AlertaDTE::where('id_organizacion', $idOrganizacion)
                    ->where('tipo', 'hito_dtes')
                    ->where('datos_adicionales->hito', $hito)
                    ->first();

                if (!$alertaExistente) {
                    AlertaDTE::create([
                        'id_organizacion' => $idOrganizacion,
                        'tipo' => 'hito_dtes',
                        'nivel' => 'info',
                        'titulo' => "¡{$hito} DTEs emitidos!",
                        'mensaje' => "Felicitaciones, has alcanzado {$hito} documentos tributarios electrónicos emitidos.",
                        'datos_adicionales' => ['hito' => $hito, 'total' => $totalDTEs],
                    ]);
                }
            }
        }
    }

    /**
     * Obtener alertas activas de una organización
     */
    public function obtenerAlertasActivas($idOrganizacion)
    {
        return AlertaDTE::where('id_organizacion', $idOrganizacion)
            ->activas()
            ->orderBy('nivel', 'asc') // Críticas primero
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener conteo de alertas por nivel
     */
    public function contarAlertas($idOrganizacion)
    {
        return [
            'criticas' => AlertaDTE::where('id_organizacion', $idOrganizacion)->activas()->criticas()->count(),
            'advertencias' => AlertaDTE::where('id_organizacion', $idOrganizacion)->activas()->advertencias()->count(),
            'informativas' => AlertaDTE::where('id_organizacion', $idOrganizacion)->activas()->informativas()->count(),
            'total' => AlertaDTE::where('id_organizacion', $idOrganizacion)->activas()->count(),
        ];
    }
}
