<?php

namespace App\Services;

class DomainVerificationService
{
    /**
     * Verifica si un dominio personalizado está correctamente configurado
     *
     * @param string $dominio El dominio a verificar (ej: www.aprnombre.cl)
     * @return array [
     *   'valido' => bool,
     *   'mensaje' => string,
     *   'detalles' => array
     * ]
     */
    public function verificarDNS(string $dominio): array
    {
        if (empty($dominio)) {
            return [
                'valido' => false,
                'mensaje' => 'Dominio vacío',
                'detalles' => []
            ];
        }

        // Limpiar dominio (remover http://, https://, espacios, etc.)
        $dominio = $this->limpiarDominio($dominio);

        try {
            // Verificar que el dominio resuelva
            $ip = gethostbyname($dominio);

            if ($ip === $dominio) {
                // gethostbyname devuelve el mismo string si no puede resolver
                return [
                    'valido' => false,
                    'mensaje' => 'El dominio no resuelve a ninguna IP. Verifica que el dominio existe y está registrado.',
                    'detalles' => [
                        'dominio' => $dominio,
                        'resuelve' => false,
                        'ip' => null
                    ]
                ];
            }

            // Verificar registro CNAME
            $cnameResult = $this->verificarCNAME($dominio);

            if (!$cnameResult['tiene_cname']) {
                return [
                    'valido' => false,
                    'mensaje' => 'El dominio no tiene un registro CNAME apuntando a sistemaapr.cl. Por favor configura tu DNS correctamente.',
                    'detalles' => [
                        'dominio' => $dominio,
                        'resuelve' => true,
                        'ip' => $ip,
                        'tiene_cname' => false,
                        'cname_destino' => null,
                        'instruccion' => 'Debes crear un registro CNAME en tu proveedor DNS apuntando a sistemaapr.cl'
                    ]
                ];
            }

            if (!$cnameResult['apunta_correcto']) {
                return [
                    'valido' => false,
                    'mensaje' => "El CNAME apunta a '{$cnameResult['cname_destino']}' pero debe apuntar a 'sistemaapr.cl'",
                    'detalles' => [
                        'dominio' => $dominio,
                        'resuelve' => true,
                        'ip' => $ip,
                        'tiene_cname' => true,
                        'cname_destino' => $cnameResult['cname_destino'],
                        'cname_esperado' => 'sistemaapr.cl'
                    ]
                ];
            }

            // Todo correcto
            return [
                'valido' => true,
                'mensaje' => 'DNS configurado correctamente. El dominio está listo para usar.',
                'detalles' => [
                    'dominio' => $dominio,
                    'resuelve' => true,
                    'ip' => $ip,
                    'tiene_cname' => true,
                    'cname_destino' => $cnameResult['cname_destino'],
                    'verificado_en' => now()->toDateTimeString()
                ]
            ];

        } catch (\Exception $e) {
            \Log::error('Error al verificar DNS de dominio', [
                'dominio' => $dominio,
                'error' => $e->getMessage()
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error al verificar DNS: ' . $e->getMessage(),
                'detalles' => [
                    'dominio' => $dominio,
                    'error' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Verifica si existe registro CNAME y hacia dónde apunta
     *
     * @param string $dominio
     * @return array
     */
    private function verificarCNAME(string $dominio): array
    {
        // Intentar obtener registros DNS del tipo CNAME
        $dnsRecords = @dns_get_record($dominio, DNS_CNAME);

        if ($dnsRecords === false || empty($dnsRecords)) {
            // No tiene CNAME, puede ser un registro A directo
            // Intentar obtener registros A
            $dnsRecordsA = @dns_get_record($dominio, DNS_A);

            if ($dnsRecordsA !== false && !empty($dnsRecordsA)) {
                // Tiene registro A directo, verificar si apunta a la IP correcta
                // Para simplificar, asumimos que debe tener CNAME
                return [
                    'tiene_cname' => false,
                    'apunta_correcto' => false,
                    'cname_destino' => null,
                    'nota' => 'Tiene registro A directo, se recomienda usar CNAME'
                ];
            }

            return [
                'tiene_cname' => false,
                'apunta_correcto' => false,
                'cname_destino' => null
            ];
        }

        // Obtener el destino del CNAME
        $cnameDestino = $dnsRecords[0]['target'] ?? null;

        if (!$cnameDestino) {
            return [
                'tiene_cname' => false,
                'apunta_correcto' => false,
                'cname_destino' => null
            ];
        }

        // Limpiar destino (remover punto final si existe)
        $cnameDestino = rtrim($cnameDestino, '.');

        // Verificar si apunta a sistemaapr.cl o sus variantes
        $destinosValidos = [
            'sistemaapr.cl',
            'www.sistemaapr.cl',
        ];

        $apuntaCorrecto = in_array(strtolower($cnameDestino), $destinosValidos);

        return [
            'tiene_cname' => true,
            'apunta_correcto' => $apuntaCorrecto,
            'cname_destino' => $cnameDestino
        ];
    }

    /**
     * Limpia el dominio removiendo protocolos, www duplicados, espacios, etc.
     *
     * @param string $dominio
     * @return string
     */
    private function limpiarDominio(string $dominio): string
    {
        // Remover espacios
        $dominio = trim($dominio);

        // Remover http:// y https://
        $dominio = preg_replace('#^https?://#i', '', $dominio);

        // Remover trailing slash
        $dominio = rtrim($dominio, '/');

        // Convertir a minúsculas
        $dominio = strtolower($dominio);

        return $dominio;
    }

    /**
     * Genera un token de verificación único para un dominio
     *
     * @param int $organizacionId
     * @return string
     */
    public function generarTokenVerificacion(int $organizacionId): string
    {
        return 'sistemaapr-verify-' . md5($organizacionId . time() . config('app.key'));
    }

    /**
     * Verifica registro TXT con token de verificación (para implementación futura)
     *
     * @param string $dominio
     * @param string $token
     * @return bool
     */
    public function verificarTokenTXT(string $dominio, string $token): bool
    {
        $dominio = $this->limpiarDominio($dominio);

        // Obtener registros TXT
        $dnsRecords = @dns_get_record('_sistemaapr-verify.' . $dominio, DNS_TXT);

        if ($dnsRecords === false || empty($dnsRecords)) {
            return false;
        }

        foreach ($dnsRecords as $record) {
            if (isset($record['txt']) && $record['txt'] === $token) {
                return true;
            }
        }

        return false;
    }
}
