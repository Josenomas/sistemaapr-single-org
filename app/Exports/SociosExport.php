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

class SociosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $socios;

    public function __construct($socios)
    {
        $this->socios = $socios;
    }

    /**
     * Datos a exportar
     */
    public function collection()
    {
        return $this->socios;
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'N° Socio',
            'RUT',
            'Nombre Completo',
            'Dirección',
            'Sector',
            'Teléfono',
            'Email',
            'Tipo Cliente',
            'N° Medidor',
            'Estado',
            'Fecha Ingreso',
        ];
    }

    /**
     * Mapear los datos
     */
    public function map($socio): array
    {
        return [
            $socio->numero_socio,
            $socio->rut,
            $socio->nombre_completo,
            $socio->direccion,
            $socio->sector ?? '-',
            $socio->telefono ?? '-',
            $socio->email ?? '-',
            ucfirst($socio->tipo_cliente),
            $socio->numero_medidor ?? '-',
            ucfirst($socio->estado),
            $socio->fecha_ingreso ? date('d/m/Y', strtotime($socio->fecha_ingreso)) : '-',
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
        return 'Socios';
    }
}
