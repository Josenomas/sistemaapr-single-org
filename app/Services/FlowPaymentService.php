<?php

namespace App\Services;

use App\Models\TransaccionFlow;
use App\Models\Socio;
use App\Models\Boleta;
use Exception;

class FlowPaymentService
{
    private $apiKey;
    private $secretKey;
    private $apiUrl;
    private $paymentUrl;

    public function __construct()
    {
        $mode = config('flow.mode', 'sandbox');
        $this->apiKey = config('flow.api_key');
        $this->secretKey = config('flow.secret_key');
        $this->apiUrl = config('flow.api_url')[$mode];
        $this->paymentUrl = config('flow.payment_url')[$mode];
    }

    /**
     * Crear un pago en Flow
     */
    public function crearPago($socioId, $boletaId, $monto, $email, $subject)
    {
        try {
            // Validar datos
            $socio = Socio::findOrFail($socioId);
            $boleta = Boleta::findOrFail($boletaId);

            // Generar número de orden único
            $flowOrder = $this->generarNumeroOrden();

            // URLs de callback
            $urlConfirmacion = config('flow.url_confirmacion');
            $urlRetorno = config('flow.url_retorno');

            // Preparar parámetros
            $params = [
                'commerceOrder' => $flowOrder,
                'subject' => $subject,
                'currency' => 'CLP',
                'amount' => (int) $monto,
                'email' => $email,
                'urlConfirmation' => $urlConfirmacion,
                'urlReturn' => $urlRetorno,
            ];

            // Agregar firma
            $params['s'] = $this->firmarParametros($params);

            // Realizar petición HTTP a Flow
            $response = $this->realizarPeticion('/payment/create', $params);

            if (isset($response['url']) && isset($response['token'])) {
                // Guardar transacción en base de datos
                $transaccion = TransaccionFlow::create([
                    'flow_order' => $flowOrder,
                    'token' => $response['token'],
                    'id_socio' => $socioId,
                    'id_boleta' => $boletaId,
                    'monto' => $monto,
                    'email' => $email,
                    'subject' => $subject,
                    'url_confirmacion' => $urlConfirmacion,
                    'url_retorno' => $urlRetorno,
                    'estado' => 'pendiente',
                ]);

                return [
                    'success' => true,
                    'url' => $response['url'] . '?token=' . $response['token'],
                    'token' => $response['token'],
                    'transaccion' => $transaccion,
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear el pago en Flow',
                'response' => $response,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Confirmar pago desde callback de Flow
     */
    public function confirmarPago($token)
    {
        try {
            // Obtener datos del pago
            $params = [
                'token' => $token,
            ];

            $params['s'] = $this->firmarParametros($params);

            $response = $this->realizarPeticion('/payment/getStatus', $params);

            if (isset($response['status'])) {
                // Buscar transacción
                $transaccion = TransaccionFlow::where('token', $token)->first();

                if ($transaccion) {
                    // Actualizar estado según respuesta de Flow
                    $estado = $this->mapearEstadoFlow($response['status']);

                    $transaccion->update([
                        'estado' => $estado,
                        'flow_status' => $response['status'],
                        'payment_data' => json_encode($response),
                        'fecha_pago' => $estado === 'pagado' ? now() : null,
                    ]);

                    return [
                        'success' => true,
                        'transaccion' => $transaccion,
                        'response' => $response,
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Transacción no encontrada',
                ];
            }

            return [
                'success' => false,
                'message' => 'Respuesta inválida de Flow',
                'response' => $response,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Firmar parámetros con la clave secreta
     */
    private function firmarParametros($params)
    {
        // Ordenar parámetros alfabéticamente
        ksort($params);

        // Concatenar parámetros
        $string = '';
        foreach ($params as $key => $value) {
            $string .= $key . $value;
        }

        // Agregar secret key
        $string .= $this->secretKey;

        // Generar firma HMAC SHA256
        return hash_hmac('sha256', $string, $this->secretKey);
    }

    /**
     * Realizar petición HTTP a la API de Flow
     */
    private function realizarPeticion($endpoint, $params)
    {
        $url = $this->apiUrl . $endpoint;

        // Agregar API Key
        $params['apiKey'] = $this->apiKey;

        // Inicializar cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, config('flow.timeout', 30));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('Error en petición cURL: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception('Error HTTP ' . $httpCode . ': ' . $response);
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar respuesta JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Mapear estado de Flow a estado interno
     */
    private function mapearEstadoFlow($flowStatus)
    {
        // Estados de Flow:
        // 1 = Pago completado
        // 2 = Pago rechazado
        // 3 = Pago pendiente
        // 4 = Pago anulado

        $mapeo = [
            1 => 'pagado',
            2 => 'rechazado',
            3 => 'pendiente',
            4 => 'anulado',
        ];

        return $mapeo[$flowStatus] ?? 'pendiente';
    }

    /**
     * Generar número de orden único
     */
    private function generarNumeroOrden()
    {
        $ultimaTransaccion = TransaccionFlow::orderBy('id', 'desc')->first();
        return ($ultimaTransaccion ? $ultimaTransaccion->flow_order + 1 : 1000);
    }

    /**
     * Validar firma de callback
     */
    public function validarFirma($params, $firma)
    {
        $firmaCalculada = $this->firmarParametros($params);
        return hash_equals($firmaCalculada, $firma);
    }
}
