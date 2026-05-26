<?php

return [

    /*
    |--------------------------------------------------------------------------
    | URL de la API de SimpleAPI
    |--------------------------------------------------------------------------
    |
    | URL base para conectar con los servicios web de SimpleAPI.
    | Por defecto usa la instancia oficial de ChileSystems.
    |
    */

    'url' => env('SIMPLEAPI_URL', 'https://api.simpleapi.cl'),

    /*
    |--------------------------------------------------------------------------
    | API Key de Autenticación
    |--------------------------------------------------------------------------
    |
    | Token de autenticación para la API de SimpleAPI.
    | Este API Key se obtiene desde el panel de SimpleAPI en:
    | https://www.simpleapi.cl/
    |
    */

    'api_key' => env('SIMPLEAPI_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Ambiente de Operación
    |--------------------------------------------------------------------------
    |
    | Define el ambiente en el que se emitirán los DTEs:
    | - 'certificacion': Para pruebas (no válido en el SII real)
    | - 'produccion': Para DTEs válidos oficialmente
    |
    */

    'ambiente' => env('SIMPLEAPI_AMBIENTE', 'certificacion'),

    /*
    |--------------------------------------------------------------------------
    | Tipos de Documentos
    |--------------------------------------------------------------------------
    |
    | Códigos oficiales de los tipos de Documentos Tributarios Electrónicos
    | según el SII de Chile.
    |
    */

    'tipos_dte' => [
        33 => 'Factura Electrónica',
        34 => 'Factura No Afecta o Exenta Electrónica',
        39 => 'Boleta Electrónica',
        41 => 'Boleta Exenta Electrónica',
        43 => 'Liquidación Factura Electrónica',
        46 => 'Factura de Compra Electrónica',
        52 => 'Guía de Despacho Electrónica',
        56 => 'Nota de Débito Electrónica',
        61 => 'Nota de Crédito Electrónica',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout de Conexión
    |--------------------------------------------------------------------------
    |
    | Tiempo máximo de espera (en segundos) para las llamadas a la API.
    |
    */

    'timeout' => env('SIMPLEAPI_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Configuración de Reintentos
    |--------------------------------------------------------------------------
    |
    | Número de reintentos en caso de falla de conexión.
    |
    */

    'retry' => [
        'times' => 3,
        'sleep' => 1000, // milisegundos
    ],

    /*
    |--------------------------------------------------------------------------
    | Límite de Rate Limiting
    |--------------------------------------------------------------------------
    |
    | SimpleAPI permite hasta 3 consultas por segundo.
    |
    */

    'rate_limit' => [
        'per_second' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Habilitar registro de todas las peticiones a SimpleAPI.
    |
    */

    'log_requests' => env('SIMPLEAPI_LOG_REQUESTS', true),

];
