<?php

namespace App\Services;

use App\Exports\SociosExport;
use App\Exports\BoletasExport;
use App\Exports\PagosExport;
use App\Exports\LecturasExport;
use App\Exports\ReporteFinancieroExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportService
{
    /**
     * Exportar socios a Excel
     */
    public function exportarSocios($socios, $nombreArchivo = null)
    {
        $nombreArchivo = $nombreArchivo ?? 'socios_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new SociosExport($socios), $nombreArchivo);
    }

    /**
     * Exportar boletas a Excel
     */
    public function exportarBoletas($boletas, $nombreArchivo = null)
    {
        $nombreArchivo = $nombreArchivo ?? 'boletas_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new BoletasExport($boletas), $nombreArchivo);
    }

    /**
     * Exportar pagos a Excel
     */
    public function exportarPagos($pagos, $nombreArchivo = null)
    {
        $nombreArchivo = $nombreArchivo ?? 'pagos_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new PagosExport($pagos), $nombreArchivo);
    }

    /**
     * Exportar lecturas a Excel
     */
    public function exportarLecturas($lecturas, $nombreArchivo = null)
    {
        $nombreArchivo = $nombreArchivo ?? 'lecturas_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new LecturasExport($lecturas), $nombreArchivo);
    }

    /**
     * Exportar reporte financiero a Excel
     */
    public function exportarReporteFinanciero($datos, $nombreArchivo = null)
    {
        $nombreArchivo = $nombreArchivo ?? 'reporte_financiero_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new ReporteFinancieroExport($datos), $nombreArchivo);
    }
}
