<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        // Verificar que las credenciales de Twilio estén configuradas
        $sid = config('services.twilio.account_sid');
        $token = config('services.twilio.auth_token');
        $this->from = config('services.twilio.whatsapp_from');

        if (!$sid || !$token || !$this->from) {
            throw new \Exception('Credenciales de Twilio WhatsApp no configuradas en .env');
        }

        $this->client = new Client($sid, $token);
    }

    /**
     * Enviar mensaje de WhatsApp
     *
     * @param string $telefono Número de teléfono del destinatario
     * @param string $mensaje Contenido del mensaje
     * @return array ['success' => bool, 'sid' => string|null, 'error' => string|null]
     */
    public function enviarMensaje($telefono, $mensaje)
    {
        try {
            // Formatear número de teléfono a formato internacional
            $telefonoFormateado = $this->formatearTelefono($telefono);

            // Enviar mensaje usando Twilio
            $message = $this->client->messages->create(
                "whatsapp:{$telefonoFormateado}", // To
                [
                    'from' => $this->from,
                    'body' => $mensaje
                ]
            );

            return [
                'success' => true,
                'sid' => $message->sid,
                'error' => null
            ];

        } catch (\Twilio\Exceptions\RestException $e) {
            \Log::error("Error de Twilio al enviar WhatsApp: " . $e->getMessage());
            return [
                'success' => false,
                'sid' => null,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Log::error("Error general al enviar WhatsApp: " . $e->getMessage());
            return [
                'success' => false,
                'sid' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Formatear teléfono a formato internacional
     *
     * @param string $telefono
     * @return string Teléfono en formato +56XXXXXXXXX
     */
    protected function formatearTelefono($telefono)
    {
        // Remover espacios, guiones y paréntesis
        $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);

        // Si ya tiene +56 al inicio, retornar
        if (strpos($telefono, '+56') === 0) {
            return $telefono;
        }

        // Si empieza con 56, agregar +
        if (strpos($telefono, '56') === 0) {
            return '+' . $telefono;
        }

        // Si empieza con 9 (celulares chilenos), agregar +56
        if (strpos($telefono, '9') === 0 && strlen($telefono) === 9) {
            return '+56' . $telefono;
        }

        // Si no tiene código de país, asumir Chile (+56)
        return '+56' . $telefono;
    }

    /**
     * Verificar si un número está registrado en WhatsApp (opcional)
     *
     * @param string $telefono
     * @return bool
     */
    public function verificarNumero($telefono)
    {
        // Esta funcionalidad requiere configuración adicional en Twilio
        // Por ahora retornamos true
        return true;
    }
}
