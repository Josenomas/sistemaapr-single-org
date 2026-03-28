<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PagosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $pagos;

    public function __construct($pagos)
    {
        $this->pagos = $pagos;
    }

    /**
     * Datos a exportar
     */
    public function collection()
    {
        return $this->pagos;
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'N° Comprobante',
            'N° Boleta',
            'N° Socio',
            'Nombre Socio',
            'Fecha Pago',
            'Método de Pago',
            'Monto Pagado',
            'Descuento',
            'Total Boleta',
            'Estado',
            'Observaciones',
        ];
    }

    /**
     * Mapear los datos
     */
    public function map($pago): array
    {
        return [
            $pago->numero_comprobante ?? '-',
            $pago->boleta->numero_boleta ?? '-',
            $pago->boleta->socio->numero_socio ?? '-',
            $pago->boleta->socio->nombre_completo ?? '-',
            date('d/m/Y', strtotime($pago->fecha_pago)),
            ucfirst($pago->metodo_pago),
            number_format($pago->monto_pagado, 0, ',', '.'),
            number_format($pago->descuento ?? 0, 0, ',', '.'),
            number_format($pago->boleta->total ?? 0, 0, ',', '.'),
            ucfirst($pago->estado ?? 'completado'),
            $pago->observaciones ?? '-',
        ];
    }

    /**
     * Estilos
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667eea'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Pagos';
    }
}
