<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Flow API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con Flow (Pasarela de Pagos)
    | https://www.flow.cl/
    |
    */

    // Modo de operación (sandbox o production)
    'mode' => env('FLOW_MODE', 'sandbox'),

    // API Key de Flow
    'api_key' => env('FLOW_API_KEY', ''),

    // Secret Key de Flow
    'secret_key' => env('FLOW_SECRET_KEY', ''),

    // URLs de la API de Flow
    'api_url' => [
        'sandbox' => 'https://sandbox.flow.cl/api',
        'production' => 'https://www.flow.cl/api',
    ],

    // URL de pago
    'payment_url' => [
        'sandbox' => 'https://sandbox.flow.cl/app/web/pay.php',
        'production' => 'https://www.flow.cl/app/web/pay.php',
    ],

    // Endpoints específicos
    'endpoints' => [
        'payment_create' => '/payment/create',
        'payment_getStatus' => '/payment/getStatus',
    ],

    // URLs de callback (se configuran dinámicamente en el servicio)
    'url_confirmacion' => env('APP_URL') . '/flow/confirmar',
    'url_retorno' => env('APP_URL') . '/flow/retorno',

    // Timeout para peticiones HTTP (en segundos)
    'timeout' => 30,
];
