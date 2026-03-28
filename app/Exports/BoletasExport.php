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

class BoletasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $boletas;

    public function __construct($boletas)
    {
        $this->boletas = $boletas;
    }

    /**
     * Datos a exportar
     */
    public function collection()
    {
        return $this->boletas;
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'N° Boleta',
            'N° Socio',
            'Nombre Socio',
            'Período',
            'Fecha Emisión',
            'Fecha Vencimiento',
            'Consumo (m³)',
            'Subtotal',
            'Descuentos',
            'Recargos',
            'Total',
            'Estado',
        ];
    }

    /**
     * Mapear los datos
     */
    public function map($boleta): array
    {
        return [
            $boleta->numero_boleta,
            $boleta->socio->numero_socio ?? '-',
            $boleta->socio->nombre_completo ?? '-',
            $boleta->periodo ? date('m/Y', strtotime($boleta->periodo)) : '-',
            date('d/m/Y', strtotime($boleta->fecha_emision)),
            date('d/m/Y', strtotime($boleta->fecha_vencimiento)),
            $boleta->consumo ?? 0,
            number_format($boleta->subtotal, 0, ',', '.'),
            number_format($boleta->descuentos, 0, ',', '.'),
            number_format($boleta->recargos, 0, ',', '.'),
            number_format($boleta->total, 0, ',', '.'),
            ucfirst($boleta->estado),
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
        return 'Boletas';
    }
}
