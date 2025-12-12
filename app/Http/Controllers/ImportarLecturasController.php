<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lectura;
use App\Models\Socio;
use App\Helpers\ActividadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
     * Procesar archivo CSV
     */
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $archivo = $request->file('archivo');
            $datos = [];
            $errores = [];
            $fila = 0;

            // Leer archivo CSV
            if (($handle = fopen($archivo->getPathname(), 'r')) !== false) {
                // Leer encabezados
                $encabezados = fgetcsv($handle, 1000, ',');

                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $fila++;

                    // Validar que tenga 4 columnas: numero_socio, mes, lectura_actual, fecha_lectura
                    if (count($row) < 4) {
                        $errores[] = "Fila {$fila}: Faltan columnas (se esperan 4: numero_socio, mes, lectura_actual, fecha_lectura)";
                        continue;
                    }

                    $numeroSocio = trim($row[0]);
                    $mes = trim($row[1]); // Formato: 2025-01
                    $lecturaActual = trim($row[2]);
                    $fechaLectura = trim($row[3]); // Formato: dd/mm/yyyy o yyyy-mm-dd

                    // Validar socio existe
                    $socio = Socio::where('numero_socio', $numeroSocio)->first();
                    if (!$socio) {
                        $errores[] = "Fila {$fila}: Socio '{$numeroSocio}' no existe";
                        continue;
                    }

                    // Validar formato de mes
                    if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
                        $errores[] = "Fila {$fila}: Formato de mes inválido (use YYYY-MM, ejemplo: 2025-01)";
                        continue;
                    }

                    // Convertir fecha si está en formato dd/mm/yyyy
                    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fechaLectura, $matches)) {
                        $fechaLectura = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
                    }

                    // Validar que sea un número
                    if (!is_numeric($lecturaActual)) {
                        $errores[] = "Fila {$fila}: Lectura actual debe ser un número";
                        continue;
                    }

                    $datos[] = [
                        'socio' => $socio,
                        'mes' => $mes,
                        'lectura_actual' => floatval($lecturaActual),
                        'fecha_lectura' => $fechaLectura,
                        'fila' => $fila
                    ];
                }
                fclose($handle);
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

            return redirect()->route('lecturas.index')
                ->with('success', "Importación exitosa: {$importadas} lecturas importadas" . ($omitidas > 0 ? ", {$omitidas} omitidas (ya existían)" : ''));

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('lecturas.importar.index')
                ->with('error', 'Error al guardar las lecturas: ' . $e->getMessage());
        }
    }

    /**
     * Descargar plantilla CSV
     */
    public function descargarPlantilla()
    {
        $contenido = "numero_socio,mes,lectura_actual,fecha_lectura\n";
        $contenido .= "SOC-0001,2025-01,120.50,15/01/2025\n";
        $contenido .= "SOC-0001,2025-02,135.75,15/02/2025\n";
        $contenido .= "SOC-0002,2025-01,85.00,15/01/2025\n";

        return response($contenido, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_lecturas.csv"',
        ]);
    }
}
