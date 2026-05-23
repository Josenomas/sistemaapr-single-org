<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\ConfiguracionDTE;
use App\Services\LibreDTEService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            $boleta = Boleta::with('socio')->findOrFail($idBoleta);

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

            // Enviar email automático con PDF timbrado si el socio tiene email
            if ($boleta->socio && $boleta->socio->email && $boleta->pdf_url) {
                \App\Jobs\EnviarBoletaDTEEmail::dispatch($boleta->fresh());
                return redirect()->back()->with('success', "Boleta electrónica emitida exitosamente. Folio: {$response['folio']}. Email enviado a {$boleta->socio->email}");
            }

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
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'Debe ingresar un motivo de anulación',
            'motivo.min' => 'El motivo debe tener al menos 10 caracteres',
            'motivo.max' => 'El motivo no puede exceder 500 caracteres',
        ]);

        try {
            $boleta = Boleta::findOrFail($idBoleta);

            // Verificar multi-tenancy
            if ($boleta->id_organizacion != auth()->user()->id_organizacion) {
                return redirect()->back()->with('error', 'Acceso denegado');
            }

            // Verificar que tenga DTE emitido
            if (!$boleta->dteEmitido()) {
                return redirect()->back()->with('error', 'La boleta no tiene DTE emitido para anular');
            }

            // Verificar que no esté ya anulada
            if ($boleta->estado_dte === 'anulada') {
                return redirect()->back()->with('error', 'El DTE ya está anulado');
            }

            // Verificar que el estado sea válido para anular (emitida o aceptada)
            if (!in_array($boleta->estado_dte, ['emitida', 'aceptada'])) {
                return redirect()->back()->with('error', 'Solo se pueden anular DTEs emitidos o aceptados por el SII');
            }

            // Emitir Nota de Crédito
            $this->libredteService->setOrganizacion($boleta->id_organizacion);
            $response = $this->libredteService->anularDocumento($boleta, $request->motivo);

            return redirect()->back()->with('success', "DTE anulado exitosamente. Nota de Crédito Folio: {$response['folio']}");

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

        // Prioridad 1: Descargar desde almacenamiento local si existe
        if ($boleta->pdf_local_path && Storage::exists($boleta->pdf_local_path)) {
            $nombreArchivo = "DTE_{$boleta->tipo_dte}_F{$boleta->folio_sii}_B{$boleta->numero_boleta}.pdf";
            return Storage::download($boleta->pdf_local_path, $nombreArchivo);
        }

        // Prioridad 2: Redirigir a LibreDTE si no hay copia local
        if ($boleta->pdf_url) {
            return redirect($boleta->pdf_url);
        }

        return redirect()->back()->with('error', 'No hay PDF disponible para esta boleta');
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
