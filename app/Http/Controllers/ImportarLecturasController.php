<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lectura;
use App\Models\Socio;
use App\Models\Auditoria;
use App\Models\NotificacionSistema;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ImportarLecturasController extends Controller
{
    /**
     * Mostrar formulario de importación
     */
    public function index()
    {
        return view('lecturas.importar');
    }

    /**
     * Procesar archivo Excel/CSV
     */
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'mes' => 'required|date_format:Y-m',
            'fecha_lectura' => 'required|date',
        ]);

        try {
            // Obtener organización desde sesión o usuario autenticado
            $idOrganizacion = session('tenant_id') ?? session('id_organizacion') ?? auth()->user()->id_organizacion ?? null;

            if (!$idOrganizacion) {
                return redirect()->back()
                    ->with('error', 'No se pudo identificar la organización.');
            }

            $mes = $request->mes;
            $fechaLectura = $request->fecha_lectura;

            $archivo = $request->file('archivo');
            $datos = [];
            $errores = [];

            // Detectar si es Excel o CSV
            $extension = $archivo->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                // Procesar Excel
                $spreadsheet = IOFactory::load($archivo->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                // Validar encabezados
                $headers = array_map('strtolower', array_map('trim', $rows[0]));
                $requiredHeaders = ['numero_socio', 'lectura_actual'];

                foreach ($requiredHeaders as $required) {
                    if (!in_array($required, $headers)) {
                        return redirect()->back()
                            ->with('error', "Falta la columna requerida: {$required}");
                    }
                }

                // Procesar filas
                foreach (array_slice($rows, 1) as $index => $row) {
                    $fila = $index + 2;
                    $data = array_combine($headers, $row);

                    // Saltar filas vacías
                    if (empty($data['numero_socio'])) continue;

                    $numeroSocio = trim($data['numero_socio']);
                    $lecturaActual = floatval($data['lectura_actual']);

                    // Buscar socio
                    $socio = Socio::where('id_organizacion', $idOrganizacion)
                        ->where('numero_socio', $numeroSocio)
                        ->first();

                    if (!$socio) {
                        $errores[] = "Fila {$fila}: Socio {$numeroSocio} no encontrado";
                        continue;
                    }

                    $datos[] = [
                        'socio' => $socio,
                        'mes' => $mes,
                        'lectura_actual' => $lecturaActual,
                        'fecha_lectura' => $fechaLectura,
                        'observaciones' => $data['observaciones'] ?? null,
                        'fila' => $fila
                    ];
                }
            } else {
                // Procesar CSV (código original)
                if (($handle = fopen($archivo->getPathname(), 'r')) !== false) {
                    $encabezados = fgetcsv($handle, 1000, ',');
                    $fila = 0;

                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $fila++;

                        if (count($row) < 2) continue;

                        $numeroSocio = trim($row[0]);
                        $lecturaActual = trim($row[1]);

                        $socio = Socio::where('id_organizacion', $idOrganizacion)
                            ->where('numero_socio', $numeroSocio)
                            ->first();

                        if (!$socio) {
                            $errores[] = "Fila {$fila}: Socio '{$numeroSocio}' no existe";
                            continue;
                        }

                        if (!is_numeric($lecturaActual)) {
                            $errores[] = "Fila {$fila}: Lectura actual debe ser un número";
                            continue;
                        }

                        $datos[] = [
                            'socio' => $socio,
                            'mes' => $mes,
                            'lectura_actual' => floatval($lecturaActual),
                            'fecha_lectura' => $fechaLectura,
                            'observaciones' => null,
                            'fila' => $fila
                        ];
                    }
                    fclose($handle);
                }
            }

            if (!empty($errores)) {
                return redirect()->back()
                    ->with('error', 'Se encontraron errores en el archivo:')
                    ->with('errores', $errores)
                    ->withInput();
            }

            if (empty($datos)) {
                return redirect()->back()
                    ->with('error', 'El archivo está vacío o no contiene datos válidos')
                    ->withInput();
            }

            // Guardar en sesión para confirmación
            session(['lecturas_importar' => $datos]);

            return view('lecturas.confirmar-importacion', compact('datos'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Confirmar y guardar lecturas
     */
    public function confirmar(Request $request)
    {
        $datos = session('lecturas_importar');

        if (!$datos) {
            return redirect()->route('lecturas.importar.index')
                ->with('error', 'No hay datos para importar');
        }

        DB::beginTransaction();

        try {
            $importadas = 0;
            $omitidas = 0;

            foreach ($datos as $dato) {
                // Verificar si ya existe lectura para ese socio y mes
                $existe = Lectura::where('id_socio', $dato['socio']->id)
                    ->where('mes', $dato['mes'])
                    ->first();

                if ($existe) {
                    $omitidas++;
                    continue;
                }

                // Obtener lectura anterior
                $lecturaAnterior = Lectura::where('id_socio', $dato['socio']->id)
                    ->where('mes', '<', $dato['mes'])
                    ->orderBy('mes', 'desc')
                    ->first();

                $lectura = Lectura::create([
                    'id_socio' => $dato['socio']->id,
                    'mes' => $dato['mes'],
                    'lectura_anterior' => $lecturaAnterior ? $lecturaAnterior->lectura_actual : 0,
                    'lectura_actual' => $dato['lectura_actual'],
                    'fecha_lectura' => $dato['fecha_lectura'],
                    'id_usuario_registro' => auth()->id(),
                ]);

                $importadas++;
            }

            DB::commit();

            // Limpiar sesión
            session()->forget('lecturas_importar');

            ActividadHelper::registrar(
                'Lecturas',
                "Importación masiva completada: {$importadas} lecturas importadas, {$omitidas} omitidas (ya existían)",
                auth()->id()
            );

            // Enviar notificación SSE
            try {
                $color = $omitidas > 0 ? 'warning' : 'success';
                $titulo = $omitidas > 0 ? 'Importación de Lecturas - Con Advertencias' : 'Importación de Lecturas Completada';
                $mensaje = "Se importaron {$importadas} lecturas exitosamente" . ($omitidas > 0 ? ". {$omitidas} lectura(s) omitida(s) (ya existían)" : "");

                NotificacionSistema::create([
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'tipo' => 'otro',
                    'prioridad' => 'alta',
                    'icono' => $omitidas > 0 ? 'fa-exclamation-triangle' : 'fa-clipboard-check',
                    'color' => $color,
                    'url' => '/lecturas',
                    'texto_accion' => 'Ver Lecturas',
                    'id_usuario' => auth()->id(),
                    'id_organizacion' => session('tenant_id') ?? auth()->user()->id_organizacion,
                    'leida' => 0
                ]);
            } catch (\Exception $e) {
                Log::error('No se pudo crear notificación', ['error' => $e->getMessage()]);
            }

            return redirect()->route('lecturas.index')
                ->with('success', "Importación exitosa: {$importadas} lecturas importadas" . ($omitidas > 0 ? ", {$omitidas} omitidas (ya existían)" : ''));

        } catch (\Exception $e) {
            DB::rollback();

            // Notificar error
            try {
                NotificacionSistema::create([
                    'titulo' => 'Error en Importación de Lecturas',
                    'mensaje' => 'No se pudieron guardar las lecturas. ' . substr($e->getMessage(), 0, 100),
                    'tipo' => 'otro',
                    'prioridad' => 'urgente',
                    'icono' => 'fa-times-circle',
                    'color' => 'danger',
                    'url' => '/lecturas/importar',
                    'texto_accion' => 'Reintentar',
                    'id_usuario' => auth()->id(),
                    'id_organizacion' => session('tenant_id') ?? auth()->user()->id_organizacion,
                    'leida' => 0
                ]);
            } catch (\Exception $notifError) {
                Log::error('No se pudo crear notificación de error', ['error' => $notifError->getMessage()]);
            }

            return redirect()->route('lecturas.importar.index')
                ->with('error', 'Error al guardar las lecturas: ' . $e->getMessage());
        }
    }

    /**
     * Descargar plantilla Excel con socios y lecturas anteriores
     */
    public function descargarPlantilla()
    {
        // Obtener organización desde sesión o usuario autenticado
        $idOrganizacion = session('tenant_id') ?? session('id_organizacion') ?? auth()->user()->id_organizacion ?? null;

        // Validar que existe organización en sesión
        if (!$idOrganizacion) {
            return redirect()->back()
                ->with('error', 'No se pudo identificar la organización. Por favor, inicie sesión nuevamente.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
            'numero_socio',
            'nombre_completo',
            'numero_medidor',
            'lectura_anterior',
            'lectura_actual',
            'observaciones'
        ];

        // Escribir encabezados con estilo
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('7C3AED');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Obtener socios activos con su última lectura
        $socios = Socio::where('id_organizacion', $idOrganizacion)
            ->where('estado', 'activo')
            ->orderBy('numero_socio')
            ->get();

        // Log para debug
        \Log::info("Plantilla lecturas - Org: {$idOrganizacion}, Socios encontrados: " . $socios->count());

        $row = 2;
        foreach ($socios as $socio) {
            $ultimaLectura = Lectura::where('id_socio', $socio->id)
                ->orderBy('fecha_lectura', 'desc')
                ->first();

            $lecturaAnterior = $ultimaLectura ? $ultimaLectura->lectura_actual : 0;

            $sheet->setCellValue('A' . $row, $socio->numero_socio);
            $sheet->setCellValue('B' . $row, $socio->nombre_completo);
            $sheet->setCellValue('C' . $row, $socio->numero_medidor ?? '-');
            $sheet->setCellValue('D' . $row, $lecturaAnterior);
            $sheet->setCellValue('E' . $row, ''); // Lectura actual - para llenar
            $sheet->setCellValue('F' . $row, ''); // Observaciones

            // Estilo para lectura anterior (solo lectura)
            $sheet->getStyle('D' . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F3F4F6');

            // Estilo para lectura actual (para llenar) - color azul claro
            $sheet->getStyle('E' . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('DBEAFE');

            $row++;
        }

        // Escribir a archivo temporal
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFile = tempnam(sys_get_temp_dir(), 'plantilla_lecturas_');
        $writer->save($tempFile);

        $nombreArchivo = 'plantilla_lecturas_' . date('Y-m') . '.xlsx';

        return response()->download($tempFile, $nombreArchivo)
            ->deleteFileAfterSend(true);
    }
}
