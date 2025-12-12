<?php

if (!function_exists('format_lectura')) {
    /**
     * Formatea lecturas respetando los decimales originales del CSV
     * Mantiene hasta 3 decimales sin agregar ceros innecesarios
     */
    function format_lectura($value, $decimals = 3)
    {
        // Convertir a string con precisión
        $formatted = number_format($value, $decimals, '.', ',');

        // Remover ceros finales solo después del punto decimal
        $formatted = rtrim($formatted, '0');

        // Si termina en punto, también quitarlo
        $formatted = rtrim($formatted, '.');

        return $formatted;
    }
}
