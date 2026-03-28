<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PdfExportService
{
    /**
     * Generar PDF desde una vista
     */
    public function generarPDF($vista, $datos, $orientacion = 'portrait', $tamano = 'letter')
    {
        $pdf = Pdf::loadView($vista, $datos);

        // Configurar orientación y tamaño
        $pdf->setPaper($tamano, $orientacion);

        // Configurar opciones
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'enable_php' => false,
        ]);

        return $pdf;
    }

    /**
     * Generar PDF de reporte de socios
     */
    public function reporteSocios($socios, $organizacion, $filtros = [])
    {
        $datos = [
            'socios' => $socios,
            'organizacion' => $organizacion,
            'filtros' => $filtros,
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        return $this->generarPDF('pdf.reporte-socios', $datos, 'landscape');
    }

    /**
     * Generar PDF de reporte financiero
     */
    public function reporteFinanciero($datos, $organizacion, $periodo)
    {
        $datosVista = [
            'datos' => $datos,
            'organizacion' => $organizacion,
            'periodo' => $periodo,
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        return $this->generarPDF('pdf.reporte-financiero', $datosVista, 'portrait');
    }

    /**
     * Generar PDF de boleta
     */
    public function boleta($boleta, $organizacion)
    {
        $datos = [
            'boleta' => $boleta,
            'socio' => $boleta->socio,
            'organizacion' => $organizacion,
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        return $this->generarPDF('pdf.boleta', $datos, 'portrait', 'letter');
    }

    /**
     * Generar PDF de listado de boletas
     */
    public function listadoBoletas($boletas, $organizacion, $filtros = [])
    {
        $datos = [
            'boletas' => $boletas,
            'organizacion' => $organizacion,
            'filtros' => $filtros,
            'total' => $boletas->sum('total'),
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        return $this->generarPDF('pdf.listado-boletas', $datos, 'landscape');
    }

    /**
     * Generar PDF de reporte de consumo
     */
    public function reporteConsumo($lecturas, $organizacion, $periodo)
    {
        $datos = [
            'lecturas' => $lecturas,
            'organizacion' => $organizacion,
            'periodo' => $periodo,
            'consumo_total' => $lecturas->sum('consumo'),
            'consumo_promedio' => $lecturas->avg('consumo'),
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        return $this->generarPDF('pdf.reporte-consumo', $datos, 'landscape');
    }

    /**
     * Generar PDF de comprobante de pago
     */
    public function comprobantePago($pago, $organizacion)
    {
        $datos = [
            'pago' => $pago,
            'socio' => $pago->socio,
            'boleta' => $pago->boleta,
            'organizacion' => $organizacion,
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        return $this->generarPDF('pdf.comprobante-pago', $datos, 'portrait', 'letter');
    }
}
