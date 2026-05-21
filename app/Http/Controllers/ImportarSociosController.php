<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Organizacion;
use App\Models\Auditoria;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportarSociosController extends Controller
{
    /**
     * Mostrar formulario de importación
     */
    public function mostrarFormulario($idOrganizacion)
    {
        $organizacion = Organizacion::findOrFail($idOrganizacion);

        return view('superadmin.importar-socios', compact('organizacion'));
    }

    /**
     * Procesar archivo Excel e importar socios
     */
    public function importar(Request $request, $idOrganizacion)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $organizacion = Organizacion::findOrFail($idOrganizacion);

        try {
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Validar encabezados
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            $requiredHeaders = ['rut', 'nombre', 'apellido_paterno'];

            foreach ($requiredHeaders as $required) {
                if (!in_array($required, $headers)) {
                    return redirect()->back()
                        ->with('error', "Falta la columna requerida: {$required}");
                }
            }

            $sociosCreados = 0;
            $errores = [];
            $filaActual = 1;

            DB::beginTransaction();

            // Procesar cada fila (saltar encabezados)
            foreach (array_slice($rows, 1) as $index => $row) {
                $filaActual = $index + 2; // +2 porque array_slice quita el [0] y empezamos en fila 2 del Excel

                try {
                    // Crear array asociativo con los headers
                    $data = array_combine($headers, $row);

                    // Saltar filas vacías
                    if (empty($data['rut'])) {
                        continue;
                    }

                    // Validar que el socio no exista
                    $existente = Socio::where('id_organizacion', $idOrganizacion)
                        ->where('rut', $data['rut'])
                        ->first();

                    if ($existente) {
                        $errores[] = "Fila {$filaActual}: El RUT {$data['rut']} ya existe";
                        continue;
                    }

                    // Generar número de socio automáticamente
                    $ultimoSocio = Socio::where('id_organizacion', $idOrganizacion)
                        ->orderBy('id', 'desc')
                        ->first();
                    $numeroSocio = 'SOC-' . str_pad(($ultimoSocio ? $ultimoSocio->id + 1 : 1), 4, '0', STR_PAD_LEFT);

                    // Crear socio
                    Socio::create([
                        'id_organizacion' => $idOrganizacion,
                        'numero_socio' => $numeroSocio,
                        'rut' => $data['rut'] ?? null,
                        'nombre' => $data['nombre'] ?? null,
                        'apellido_paterno' => $data['apellido_paterno'] ?? $data['apellido'] ?? null,
                        'apellido_materno' => $data['apellido_materno'] ?? null,
                        'email' => $data['email'] ?? null,
                        'telefono' => $data['telefono'] ?? $data['teléfono'] ?? null,
                        'direccion' => $data['direccion'] ?? $data['dirección'] ?? null,
                        'comuna' => $data['comuna'] ?? null,
                        'region' => $data['region'] ?? $data['región'] ?? null,
                        'numero_medidor' => $data['numero_medidor'] ?? $data['medidor'] ?? $data['n_medidor'] ?? null,
                        'sector' => $data['sector'] ?? null,
                        'rol_avaluo' => $data['rol_avaluo'] ?? $data['rol'] ?? null,
                        'estado' => $data['estado'] ?? 'activo',
                        'exento_iva' => isset($data['exento_iva']) ? (strtolower($data['exento_iva']) === 'si' || $data['exento_iva'] == '1') : false,
                        'fecha_ingreso' => now(),
                        'fecha_creacion' => now(),
                    ]);

                    $sociosCreados++;

                } catch (\Exception $e) {
                    $errores[] = "Fila {$filaActual}: " . $e->getMessage();
                }
            }

            // Registrar en auditoría
            Auditoria::registrar(
                auth()->id(),
                'importar_socios',
                "Importó {$sociosCreados} socios a la organización {$organizacion->nombre_apr}",
                'socios',
                null
            );

            DB::commit();

            $mensaje = "Se importaron exitosamente {$sociosCreados} socios.";
            if (count($errores) > 0) {
                $mensaje .= " Se encontraron " . count($errores) . " errores.";
            }

            return redirect()->route('superadmin.organizacion.ver', $idOrganizacion)
                ->with('success', $mensaje)
                ->with('errores', $errores);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Descargar plantilla Excel de ejemplo
     */
    public function descargarPlantilla()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
            'rut',
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'email',
            'telefono',
            'direccion',
            'comuna',
            'region',
            'numero_medidor',
            'sector',
            'rol_avaluo',
            'estado',
            'exento_iva'
        ];

        // Escribir encabezados
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Ejemplos de datos
        $ejemplos = [
            ['12345678-9', 'Juan', 'Pérez', 'González', 'juan.perez@email.com', '+56912345678', 'Av. Principal 123', 'Santiago', 'Región Metropolitana', 'MED-001', 'Sector A', '12345-001', 'activo', 'no'],
            ['98765432-1', 'María', 'González', 'López', 'maria.gonzalez@email.com', '+56987654321', 'Calle Secundaria 456', 'Providencia', 'Región Metropolitana', 'MED-002', 'Sector B', '12345-002', 'activo', 'si'],
        ];

        $row = 2;
        foreach ($ejemplos as $ejemplo) {
            $col = 'A';
            foreach ($ejemplo as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Crear archivo
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'plantilla_importacion_socios.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Eliminar todos los socios de una organización
     */
    public function eliminarTodosSocios($idOrganizacion)
    {
        $organizacion = Organizacion::findOrFail($idOrganizacion);

        try {
            // Contar cuántos socios se van a eliminar
            $cantidadSocios = Socio::where('id_organizacion', $idOrganizacion)->count();

            // Eliminar SOLO los socios de esta organización específica
            $eliminados = Socio::where('id_organizacion', $idOrganizacion)->delete();

            // Registrar en auditoría
            Auditoria::create([
                'accion' => 'Eliminación masiva de socios',
                'modelo' => 'Socio',
                'id_modelo' => null,
                'usuario' => auth()->user()->nombre . ' ' . auth()->user()->apellido,
                'detalles' => "Se eliminaron {$eliminados} socios de la organización {$organizacion->nombre_apr} (ID: {$idOrganizacion})"
            ]);

            return redirect()->route('superadmin.organizacion.importar-socios', $idOrganizacion)
                ->with('success', "Se eliminaron exitosamente {$eliminados} socios de {$organizacion->nombre_apr}. Ahora puedes importar nuevamente.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar los socios: ' . $e->getMessage());
        }
    }
}
