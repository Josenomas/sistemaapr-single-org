<?php

namespace App\Helpers;

class RutHelper
{
    /**
     * Validar RUT chileno
     *
     * @param string $rut RUT en formato XX.XXX.XXX-X o XXXXXXXX-X
     * @return bool
     */
    public static function validar($rut)
    {
        if (empty($rut)) {
            return false;
        }

        // Limpiar el RUT (eliminar puntos y guiones)
        $rut = preg_replace('/[^0-9kK]/', '', $rut);

        if (strlen($rut) < 2) {
            return false;
        }

        // Separar número y dígito verificador
        $dv = strtoupper(substr($rut, -1));
        $numero = substr($rut, 0, -1);

        // Calcular dígito verificador
        $dvCalculado = self::calcularDV($numero);

        return $dv === $dvCalculado;
    }

    /**
     * Calcular dígito verificador del RUT
     *
     * @param string|int $numero Número del RUT sin DV
     * @return string Dígito verificador calculado
     */
    public static function calcularDV($numero)
    {
        $suma = 0;
        $multiplicador = 2;

        // Recorrer el número de derecha a izquierda
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += $numero[$i] * $multiplicador;
            $multiplicador = $multiplicador == 7 ? 2 : $multiplicador + 1;
        }

        $resto = $suma % 11;
        $dv = 11 - $resto;

        if ($dv == 11) {
            return '0';
        } elseif ($dv == 10) {
            return 'K';
        } else {
            return (string)$dv;
        }
    }

    /**
     * Formatear RUT con puntos y guión
     *
     * @param string $rut RUT sin formato
     * @return string RUT formateado (XX.XXX.XXX-X)
     */
    public static function formatear($rut)
    {
        if (empty($rut)) {
            return '';
        }

        // Limpiar el RUT
        $rut = preg_replace('/[^0-9kK]/', '', $rut);

        if (strlen($rut) < 2) {
            return $rut;
        }

        // Separar número y dígito verificador
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, -1);

        // Formatear con puntos
        $numeroFormateado = number_format($numero, 0, '', '.');

        return $numeroFormateado . '-' . $dv;
    }

    /**
     * Limpiar RUT (eliminar puntos y guiones)
     *
     * @param string $rut RUT con o sin formato
     * @return string RUT limpio (XXXXXXXX-X)
     */
    public static function limpiar($rut)
    {
        if (empty($rut)) {
            return '';
        }

        // Eliminar todo excepto números, K y guión
        $rut = preg_replace('/[^0-9kK-]/', '', $rut);

        // Si no tiene guión, agregarlo
        if (strpos($rut, '-') === false && strlen($rut) >= 2) {
            $rut = substr($rut, 0, -1) . '-' . substr($rut, -1);
        }

        return strtoupper($rut);
    }
}
