<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use App\Services\LibreDTEService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DTEController extends Controller
{
    protected $libredteService;

    public function __construct(LibreDTEService $libredteService)
    {
        $this->libredteService = $libredteService;
    }

    /**
     * Mostrar configuración DTE de la organización
     */
    public function configuracion()
    {
        $idOrganizacion = auth()->user()->id_organizacion;
        $config = ConfiguracionDTE::where('id_organizacion', $idOrganizacion)->first();

        return view('dte.configuracion', compact('config'));
    }

    /**
     * Guardar configuración DTE
     */
    public function guardarConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'rut_emisor' => 'required|string|max:12',
            'razon_social' => 'required|string|max:200',
            'giro' => 'required|string|max:200',
            'direccion_casa_matriz' => 'required|string|max:255',
            'comuna' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email_contacto' => 'required|email|max:150',
            'libredte_hash' => 'required|string|max:100',
            'libredte_url' => 'nullable|url|max:255',
            'ambiente' => 'required|in:certificacion,produccion',
        ]);

        $idOrganizacion = auth()->user()->id_organizacion;

        ConfiguracionDTE::updateOrCreate(
            ['id_organizacion' => $idOrganizacion],
            array_merge($validated, [
                'libredte_url' => $validated['libredte_url'] ?? 'https://libredte.cl',
                'activo' => true,
            ])
        );

        return redirect()->route('dte.configuracion')
            ->with('success', 'Configuración DTE guardada exitosamente');
    }

    /**
     * Emitir DTE para una boleta
     */
    public function emitir($idBoleta)
    {
        try {
            $boleta = Boleta::findOrFail($idBoleta);

            // Verificar que pertenece a la organización del usuario
            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            // Verificar que no tenga DTE ya emitido
            if ($boleta->dteEmitido()) {
                return redirect()->back()->with('error', 'La boleta ya tiene DTE emitido');
            }

            // Configurar servicio
            $this->libredteService->setOrganizacion($boleta->id_organizacion);

            // Emitir boleta
            $response = $this->libredteService->emitirBoleta($boleta);

            return redirect()->back()->with('success', "Boleta electrónica emitida exitosamente. Folio: {$response['folio']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al emitir DTE: ' . $e->getMessage());
        }
    }

    /**
     * Consultar estado de DTE en el SII
     */
    public function consultarEstado($idBoleta)
    {
        try {
            $boleta = Boleta::findOrFail($idBoleta);

            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            $this->libredteService->setOrganizacion($boleta->id_organizacion);
            $estado = $this->libredteService->consultarEstado($boleta);

            return redirect()->back()->with('success', "Estado SII: {$estado['estado']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al consultar estado: ' . $e->getMessage());
        }
    }

    /**
     * Anular DTE (emitir Nota de Crédito)
     */
    public function anular(Request $request, $idBoleta)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        try {
            $boleta = Boleta::findOrFail($idBoleta);

            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            $this->libredteService->setOrganizacion($boleta->id_organizacion);
            $response = $this->libredteService->anularDocumento($boleta, $request->motivo);

            return redirect()->back()->with('success', "DTE anulado exitosamente. NC Folio: {$response['folio']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al anular DTE: ' . $e->getMessage());
        }
    }

    /**
     * Descargar PDF del DTE
     */
    public function descargarPDF($idBoleta)
    {
        $boleta = Boleta::findOrFail($idBoleta);

        if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
            abort(403);
        }

        if (!$boleta->pdf_url) {
            return redirect()->back()->with('error', 'No hay PDF disponible para esta boleta');
        }

        return redirect($boleta->pdf_url);
    }

    /**
     * Verificar conexión con LibreDTE
     */
    public function verificarConexion()
    {
        try {
            $idOrganizacion = auth()->user()->id_organizacion;
            $this->libredteService->setOrganizacion($idOrganizacion);

            $conectado = $this->libredteService->verificarConexion();

            if ($conectado) {
                return response()->json(['status' => 'success', 'message' => 'Conexión exitosa con LibreDTE']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No se pudo conectar con LibreDTE'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
