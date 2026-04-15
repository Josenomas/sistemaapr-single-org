<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class ImportarInventarioController extends Controller
{
    public function index()
    {
        return view('inventario.importar');
    }

    public function descargarPlantilla()
    {
        // Obtener organización desde sesión
        $idOrganizacion = session('tenant_id') ?? session('id_organizacion') ?? auth()->user()->id_organizacion ?? null;

        if (!$idOrganizacion) {
            return redirect()->back()
                ->with('error', 'No se pudo identificar la organización. Por favor, inicie sesión nuevamente.');
        }

        // Crear nuevo Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar título
        $sheet->setCellValue('A1', 'PLANTILLA DE IMPORTACIÓN MASIVA DE INVENTARIO');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0ea5e9');
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Instrucciones
        $sheet->setCellValue('A2', 'Instrucciones:');
        $sheet->setCellValue('A3', '1. Complete los datos en las filas siguientes');
        $sheet->setCellValue('A4', '2. Los campos con fondo AZUL son REQUERIDOS');
        $sheet->setCellValue('A5', '3. Los campos con fondo GRIS son opcionales');
        $sheet->setCellValue('A6', '4. Categorías válidas: materiales, equipos, herramientas, insumos, quimicos, repuestos, otro');
        $sheet->setCellValue('A7', '5. Unidades válidas: unidad, kg, litros, metros, cajas, paquetes, etc.');
        $sheet->getStyle('A2:A7')->getFont()->setItalic(true)->setSize(9);

        // Encabezados de columnas (fila 9)
        $headers = [
            'A' => 'Nombre',
            'B' => 'Categoría',
            'C' => 'Descripción',
            'D' => 'Unidad de Medida',
            'E' => 'Cantidad Actual',
            'F' => 'Cantidad Mínima',
            'G' => 'Cantidad Máxima',
            'H' => 'Precio Unitario',
            'I' => 'Ubicación',
            'J' => 'Proveedor',
            'K' => 'Fecha Última Compra',
            'L' => 'Estado',
            'M' => 'Observaciones',
            'N' => 'Activo'
        ];

        $row = 9;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }

        // Estilo para encabezados
        $sheet->getStyle('A9:N9')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A9:N9')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1e293b');
        $sheet->getStyle('A9:N9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A9:N9')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Ajustar anchos de columnas
        $sheet->getColumnDimension('A')->setWidth(30); // Nombre
        $sheet->getColumnDimension('B')->setWidth(15); // Categoría
        $sheet->getColumnDimension('C')->setWidth(40); // Descripción
        $sheet->getColumnDimension('D')->setWidth(15); // Unidad
        $sheet->getColumnDimension('E')->setWidth(15); // Cant. Actual
        $sheet->getColumnDimension('F')->setWidth(15); // Cant. Mínima
        $sheet->getColumnDimension('G')->setWidth(15); // Cant. Máxima
        $sheet->getColumnDimension('H')->setWidth(15); // Precio
        $sheet->getColumnDimension('I')->setWidth(20); // Ubicación
        $sheet->getColumnDimension('J')->setWidth(25); // Proveedor
        $sheet->getColumnDimension('K')->setWidth(18); // Fecha
        $sheet->getColumnDimension('L')->setWidth(15); // Estado
        $sheet->getColumnDimension('M')->setWidth(30); // Observaciones
        $sheet->getColumnDimension('N')->setWidth(10); // Activo

        // Filas de ejemplo con datos de muestra (fila 10-11)
        $ejemplos = [
            ['Cloro Granulado', 'quimicos', 'Cloro para tratamiento de agua', 'kg', '50', '10', '100', '5000', 'Bodega Principal', 'Proveedor Químicos Ltda', date('Y-m-d'), 'disponible', '', '1'],
            ['Medidor Digital', 'equipos', 'Medidor de agua digital', 'unidad', '15', '5', '30', '25000', 'Bodega Equipos', 'Equipos APR S.A.', date('Y-m-d'), 'disponible', '', '1'],
        ];

        $row = 10;
        foreach ($ejemplos as $ejemplo) {
            $col = 'A';
            foreach ($ejemplo as $valor) {
                $sheet->setCellValue($col . $row, $valor);
                $col++;
            }
            $row++;
        }

        // Colorear campos requeridos (AZUL) y opcionales (GRIS)
        $camposRequeridos = ['A', 'B', 'D', 'E', 'F']; // Nombre, Categoría, Unidad, Cant. Actual, Cant. Mínima
        $camposOpcionales = ['C', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];

        for ($i = 10; $i <= 11; $i++) {
            foreach ($camposRequeridos as $col) {
                $sheet->getStyle($col . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFdbeafe'); // Azul claro
            }
            foreach ($camposOpcionales as $col) {
                $sheet->getStyle($col . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFf3f4f6'); // Gris claro
            }
        }

        // Agregar 50 filas vacías para llenar
        for ($i = 12; $i <= 62; $i++) {
            foreach ($camposRequeridos as $col) {
                $sheet->getStyle($col . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFdbeafe');
            }
            foreach ($camposOpcionales as $col) {
                $sheet->getStyle($col . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFf3f4f6');
            }
        }

        // Generar archivo Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'plantilla_inventario_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        // Obtener organización desde sesión
        $idOrganizacion = session('tenant_id') ?? session('id_organizacion') ?? auth()->user()->id_organizacion ?? null;

        if (!$idOrganizacion) {
            return redirect()->back()
                ->with('error', 'No se pudo identificar la organización. Por favor, inicie sesión nuevamente.');
        }

        $archivo = $request->file('archivo');

        try {
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Comenzar desde la fila 10 (índice 9 del array)
            // Los encabezados están en fila 9 (índice 8)
            $errores = [];
            $importados = 0;
            $omitidos = 0;

            for ($i = 9; $i < count($rows); $i++) {
                $row = $rows[$i];
                $numeroFila = $i + 1;

                // Saltar filas vacías
                if (empty(trim($row[0] ?? ''))) {
                    continue;
                }

                // Validar campos requeridos
                $nombre = trim($row[0] ?? '');
                $categoria = trim($row[1] ?? '');
                $unidadMedida = trim($row[3] ?? '');
                $cantidadActual = trim($row[4] ?? '');
                $cantidadMinima = trim($row[5] ?? '');

                if (empty($nombre)) {
                    $errores[] = "Fila $numeroFila: El nombre es requerido";
                    continue;
                }

                if (empty($categoria)) {
                    $errores[] = "Fila $numeroFila: La categoría es requerida";
                    continue;
                }

                if (empty($unidadMedida)) {
                    $errores[] = "Fila $numeroFila: La unidad de medida es requerida";
                    continue;
                }

                if ($cantidadActual === '' || $cantidadActual === null) {
                    $errores[] = "Fila $numeroFila: La cantidad actual es requerida";
                    continue;
                }

                if ($cantidadMinima === '' || $cantidadMinima === null) {
                    $errores[] = "Fila $numeroFila: La cantidad mínima es requerida";
                    continue;
                }

                // Validar categoría
                $categoriasValidas = ['materiales', 'equipos', 'herramientas', 'insumos', 'quimicos', 'repuestos', 'otro'];
                if (!in_array(strtolower($categoria), $categoriasValidas)) {
                    $errores[] = "Fila $numeroFila: Categoría inválida '$categoria'. Use: " . implode(', ', $categoriasValidas);
                    continue;
                }

                try {
                    // Generar código automático por organización
                    $ultimoProducto = Inventario::where('id_organizacion', $idOrganizacion)
                        ->orderBy('codigo_producto', 'desc')
                        ->first();

                    if ($ultimoProducto && $ultimoProducto->codigo_producto) {
                        // Extraer número del último código (PROD-000001 -> 1, PROD-000002 -> 2)
                        $partes = explode('-', $ultimoProducto->codigo_producto);
                        $numero = isset($partes[1]) ? (int)$partes[1] + 1 : 1;
                    } else {
                        $numero = 1;
                    }

                    $codigoProducto = 'PROD-' . str_pad($numero, 6, '0', STR_PAD_LEFT);

                    // Determinar estado automático
                    $cantActual = floatval($cantidadActual);
                    $cantMin = floatval($cantidadMinima);
                    $estado = $row[11] ?? '';

                    if (empty($estado)) {
                        if ($cantActual <= 0) {
                            $estado = 'agotado';
                        } elseif ($cantActual <= $cantMin) {
                            $estado = 'bajo_stock';
                        } else {
                            $estado = 'disponible';
                        }
                    }

                    // Crear producto
                    Inventario::create([
                        'id_organizacion' => $idOrganizacion,
                        'codigo_producto' => $codigoProducto,
                        'nombre' => $nombre,
                        'categoria' => strtolower($categoria),
                        'descripcion' => $row[2] ?? null,
                        'unidad_medida' => $unidadMedida,
                        'cantidad_actual' => $cantActual,
                        'cantidad_minima' => $cantMin,
                        'cantidad_maxima' => !empty($row[6]) ? floatval($row[6]) : null,
                        'precio_unitario' => !empty($row[7]) ? floatval($row[7]) : null,
                        'ubicacion' => $row[8] ?? null,
                        'proveedor' => $row[9] ?? null,
                        'fecha_ultima_compra' => !empty($row[10]) ? $row[10] : null,
                        'estado' => $estado,
                        'observaciones' => $row[12] ?? null,
                        'activo' => isset($row[13]) ? (strtolower($row[13]) === '1' || strtolower($row[13]) === 'si') : true,
                    ]);

                    $importados++;
                } catch (\Exception $e) {
                    $errores[] = "Fila $numeroFila: Error al crear producto - " . $e->getMessage();
                    Log::error("Error importando inventario fila $numeroFila: " . $e->getMessage());
                }
            }

            $mensaje = "Importación completada: $importados productos importados";
            if ($omitidos > 0) {
                $mensaje .= ", $omitidos omitidos";
            }

            if (count($errores) > 0) {
                return redirect()->route('inventario.index')
                    ->with('warning', $mensaje)
                    ->with('errores', $errores);
            }

            return redirect()->route('inventario.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error en importación de inventario: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
