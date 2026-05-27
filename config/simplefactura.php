<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SimpleFactura API URL
    |--------------------------------------------------------------------------
    |
    | URL base de la API de SimpleFactura (ChileSystems)
    |
    */

    'url' => env('SIMPLEFACTURA_URL', 'https://api.simplefactura.cl'),

    /*
    |--------------------------------------------------------------------------
    | SimpleFactura Credenciales
    |--------------------------------------------------------------------------
    |
    | Usuario y contraseña para autenticación JWT
    | Por defecto usa credenciales demo públicas
    |
    */

    'username' => env('SIMPLEFACTURA_USERNAME', 'demo@chilesystems.com'),
    'password' => env('SIMPLEFACTURA_PASSWORD', 'Rv8Il4eV'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Tiempo máximo de espera para requests HTTP (en segundos)
    |
    */

    'timeout' => env('SIMPLEFACTURA_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Activar logs de requests/responses para debugging
    |
    */

    'log_requests' => env('SIMPLEFACTURA_LOG_REQUESTS', false),

];
