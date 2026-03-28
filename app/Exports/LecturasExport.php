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

class LecturasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $lecturas;

    public function __construct($lecturas)
    {
        $this->lecturas = $lecturas;
    }

    /**
     * Datos a exportar
     */
    public function collection()
    {
        return $this->lecturas;
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'N° Socio',
            'Nombre Socio',
            'N° Medidor',
            'Fecha Lectura',
            'Período',
            'Lectura Anterior',
            'Lectura Actual',
            'Consumo (m³)',
            'Observaciones',
            'Estado',
        ];
    }

    /**
     * Mapear los datos
     */
    public function map($lectura): array
    {
        return [
            $lectura->socio->numero_socio ?? '-',
            $lectura->socio->nombre_completo ?? '-',
            $lectura->socio->numero_medidor ?? '-',
            date('d/m/Y', strtotime($lectura->fecha_lectura)),
            $lectura->periodo ? date('m/Y', strtotime($lectura->periodo)) : '-',
            $lectura->lectura_anterior ?? 0,
            $lectura->lectura_actual ?? 0,
            $lectura->consumo ?? 0,
            $lectura->observaciones ?? '-',
            ucfirst($lectura->estado ?? 'registrada'),
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
        return 'Lecturas';
    }
}
