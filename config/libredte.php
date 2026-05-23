<?php

return [

    /*
    |--------------------------------------------------------------------------
    | URL de la API de LibreDTE
    |--------------------------------------------------------------------------
    |
    | URL base para conectar con los servicios web de LibreDTE.
    | Por defecto usa la instancia oficial de LibreDTE.
    |
    */

    'url' => env('LIBREDTE_URL', 'https://libredte.cl'),

    /*
    |--------------------------------------------------------------------------
    | Hash de Autenticación
    |--------------------------------------------------------------------------
    |
    | Token de autenticación para la API de LibreDTE.
    | Este hash se obtiene desde el panel de LibreDTE en:
    | Configuración > Usuarios > API Token
    |
    */

    'hash' => env('LIBREDTE_HASH', ''),

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

    'ambiente' => env('LIBREDTE_AMBIENTE', 'certificacion'),

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

    'timeout' => env('LIBREDTE_TIMEOUT', 30),

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
    | Logging
    |--------------------------------------------------------------------------
    |
    | Habilitar registro de todas las peticiones a LibreDTE.
    |
    */

    'log_requests' => env('LIBREDTE_LOG_REQUESTS', false),

];
