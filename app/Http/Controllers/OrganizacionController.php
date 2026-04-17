<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Socio;
use App\Models\Usuario;
use App\Models\Auditoria;
use App\Services\DomainVerificationService;

class OrganizacionController extends Controller
{
    /**
     * Muestra información de la organización y su suscripción
     */
    public function index()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión.');
            }

            if (!$user->id_organizacion) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes una organización asignada. Contacta al administrador.');
            }

            $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);

            if (!$organizacion) {
                return redirect()->route('dashboard')
                    ->with('error', 'Organización no encontrada. Contacta al administrador.');
            }

            if (!$organizacion->suscripcion) {
                return redirect()->route('dashboard')
                    ->with('error', 'La organización no tiene una suscripción asignada. Contacta al administrador.');
            }

            // Obtener estadísticas de uso
            $sociosTotales = Socio::where('id_organizacion', $organizacion->id)->count();
            $usuariosTotales = Usuario::where('id_organizacion', $organizacion->id)->count();

            $estadisticas = [
                'socios_totales' => $sociosTotales,
                'socios_limite' => $organizacion->suscripcion->max_socios,
                'socios_porcentaje' => $organizacion->suscripcion->tieneSociosIlimitados()
                    ? 0
                    : ($organizacion->suscripcion->max_socios > 0
                        ? round(($sociosTotales / $organizacion->suscripcion->max_socios) * 100, 1)
                        : 0),

                'usuarios_totales' => $usuariosTotales,
                'usuarios_limite' => $organizacion->suscripcion->max_usuarios,
                'usuarios_porcentaje' => $organizacion->suscripcion->tieneUsuariosIlimitados()
                    ? 0
                    : ($organizacion->suscripcion->max_usuarios > 0
                        ? round(($usuariosTotales / $organizacion->suscripcion->max_usuarios) * 100, 1)
                        : 0),
            ];

            return view('organizacion.index', compact('organizacion', 'estadisticas'));

        } catch (\Exception $e) {
            \Log::error('Error en OrganizacionController@index: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Ocurrió un error al cargar la información: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición de la organización
     */
    public function edit()
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);

        if (!$organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'Organización no encontrada.');
        }

        $solicitudPendiente = \App\Models\SolicitudCompraDominio::where('id_organizacion', $organizacion->id)
            ->whereIn('estado', ['solicitado', 'verificado_disponible', 'pendiente_pago', 'pagado', 'comprado'])
            ->first();

        return view('organizacion.edit', compact('organizacion', 'solicitudPendiente'));
    }

    /**
     * Actualiza la información de la organización
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::find($user->id_organizacion);

        if (!$organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'Organización no encontrada.');
        }

        // Preparar reglas de validación base
        $rules = [
            'nombre_apr' => 'required|string|max:255',
            'rut' => 'required|string|max:12',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email_contacto' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048', // Max 2MB
            'color_primario' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_secundario' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'pago_presencial_dias' => 'nullable|string|max:100',
            'pago_presencial_horario' => 'nullable|string|max:100',
            'pago_presencial_lugar' => 'nullable|string|max:100',
            'banco' => 'nullable|string|max:100',
            'tipo_cuenta' => 'nullable|in:Cuenta Corriente,Cuenta Vista,Cuenta de Ahorro',
            'numero_cuenta' => 'nullable|string|max:50',
            'titular_cuenta' => 'nullable|string|max:200',
        ];

        // Si la suscripción permite dominio personalizado, agregar validación
        if ($organizacion->suscripcion && $organizacion->suscripcion->permite_dominio_personalizado) {
            $rules['dominio_personalizado'] = [
                'nullable',
                'string',
                'max:100',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                'unique:organizaciones,dominio_personalizado,' . $organizacion->id,
            ];
        }

        $validated = $request->validate($rules, [
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.mimes' => 'El logo debe ser un archivo de tipo: jpeg, jpg, png, svg.',
            'logo.max' => 'El logo no debe pesar más de 2MB.',
            'color_primario.regex' => 'El color primario debe ser un código hexadecimal válido (ejemplo: #2563eb).',
            'color_secundario.regex' => 'El color secundario debe ser un código hexadecimal válido (ejemplo: #10b981).',
            'dominio_personalizado.regex' => 'El dominio debe tener un formato válido (ejemplo: www.aprnombre.cl).',
            'dominio_personalizado.unique' => 'Este dominio ya está en uso por otra organización.',
        ]);

        // Capturar datos antes de actualizar
        $datosAnteriores = $organizacion->toArray();

        // Detectar si se cambiaron los colores
        $coloresCambiados = false;
        if (isset($validated['color_primario']) || isset($validated['color_secundario'])) {
            $colorAnterior = $organizacion->color_primario;
            $coloresCambiados = ($validated['color_primario'] ?? $colorAnterior) !== $colorAnterior ||
                               ($validated['color_secundario'] ?? $organizacion->color_secundario) !== $organizacion->color_secundario;
        }

        // VERIFICACIÓN DNS AUTOMÁTICA DE DOMINIO PERSONALIZADO
        $dominioModificado = false;
        $resultadoVerificacion = null;

        if ($organizacion->suscripcion && $organizacion->suscripcion->permite_dominio_personalizado) {
            $dominioAnterior = $organizacion->dominio_personalizado;
            $dominioNuevo = $validated['dominio_personalizado'] ?? null;

            // Si cambió el dominio o se ingresó uno nuevo
            if ($dominioNuevo && $dominioNuevo !== $dominioAnterior) {
                $dominioModificado = true;

                // Verificar DNS automáticamente
                $domainService = new DomainVerificationService();
                $resultadoVerificacion = $domainService->verificarDNS($dominioNuevo);

                if ($resultadoVerificacion['valido']) {
                    // DNS configurado correctamente
                    $validated['estado_dominio_personalizado'] = 'verificado_dns';
                    $validated['fecha_solicitud_dominio'] = now();
                    $validated['fecha_verificacion_dns'] = now();
                    $validated['detalles_verificacion_dns'] = json_encode($resultadoVerificacion['detalles']);
                    $validated['observaciones_dominio'] = null; // Limpiar observaciones previas
                } else {
                    // DNS no configurado correctamente
                    $validated['estado_dominio_personalizado'] = 'pendiente_configuracion';
                    $validated['fecha_solicitud_dominio'] = now();
                    $validated['fecha_verificacion_dns'] = null;
                    $validated['detalles_verificacion_dns'] = json_encode($resultadoVerificacion['detalles']);
                    $validated['observaciones_dominio'] = $resultadoVerificacion['mensaje'];
                }
            } elseif (empty($dominioNuevo) && !empty($dominioAnterior)) {
                // Si se eliminó el dominio, resetear estado
                $validated['estado_dominio_personalizado'] = 'sin_configurar';
                $validated['fecha_solicitud_dominio'] = null;
                $validated['fecha_verificacion_dns'] = null;
                $validated['fecha_aprobacion_dominio'] = null;
                $validated['aprobado_por'] = null;
                $validated['observaciones_dominio'] = null;
                $validated['detalles_verificacion_dns'] = null;
            }
        }

        // Actualizar datos básicos (sin logo aún)
        $organizacion->update(collect($validated)->except('logo')->toArray());

        // Subir logo si existe
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($organizacion->logo && \Storage::disk('public')->exists($organizacion->logo)) {
                \Storage::disk('public')->delete($organizacion->logo);
            }

            // Subir nuevo logo
            $path = $request->file('logo')->store('logos', 'public');
            $organizacion->update(['logo' => $path]);
        }

        // Registrar en auditoría
        Auditoria::registrar(
            'organizacion',
            'editar',
            "Editó configuración de la organización: {$organizacion->nombre_apr}",
            'organizaciones',
            $organizacion->id,
            $datosAnteriores,
            $organizacion->fresh()->toArray()
        );

        // Construir mensaje de respuesta
        $mensaje = 'Organización actualizada correctamente.';

        if ($coloresCambiados) {
            $mensaje .= ' Los cambios de colores se aplicarán al recargar la página.';
        }

        if ($dominioModificado && $resultadoVerificacion) {
            if ($resultadoVerificacion['valido']) {
                $mensaje .= ' ✓ Dominio personalizado verificado y activado automáticamente. Pendiente de aprobación del Super Admin.';

                // TODO: Enviar notificación al Super Admin
                // NotificacionHelper::notificarSuperAdmin('Nueva solicitud de dominio personalizado', $organizacion);
            } else {
                $mensaje .= ' ⚠️ El dominio personalizado no pudo ser verificado: ' . $resultadoVerificacion['mensaje'];
            }
        }

        return redirect()->route('organizacion.index')
            ->with($resultadoVerificacion && $resultadoVerificacion['valido'] ? 'success' : 'warning', $mensaje);
    }

    /**
     * Re-verificar DNS de dominio personalizado
     */
    public function reverificarDNS()
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);

        if (!$organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'Organización no encontrada.');
        }

        // Verificar que tenga plan Enterprise
        if (!$organizacion->suscripcion || !$organizacion->suscripcion->permite_dominio_personalizado) {
            return redirect()->route('organizacion.index')
                ->with('error', 'Tu plan actual no permite dominios personalizados.');
        }

        // Verificar que tenga un dominio configurado
        if (empty($organizacion->dominio_personalizado)) {
            return redirect()->route('organizacion.index')
                ->with('error', 'No tienes un dominio personalizado configurado.');
        }

        // Verificar DNS
        $domainService = new DomainVerificationService();
        $resultado = $domainService->verificarDNS($organizacion->dominio_personalizado);

        if ($resultado['valido']) {
            // DNS configurado correctamente
            $organizacion->update([
                'estado_dominio_personalizado' => 'verificado_dns',
                'fecha_verificacion_dns' => now(),
                'detalles_verificacion_dns' => json_encode($resultado['detalles']),
                'observaciones_dominio' => null,
            ]);

            // Registrar en auditoría
            Auditoria::registrar(
                'organizacion',
                'reverificar_dns',
                "DNS reverificado correctamente para dominio: {$organizacion->dominio_personalizado}",
                'organizaciones',
                $organizacion->id
            );

            return redirect()->route('organizacion.edit')
                ->with('success', '✓ DNS verificado correctamente. Tu dominio personalizado está activo y funcionando. Pendiente de aprobación del Super Admin.');
        } else {
            // DNS no configurado correctamente
            $organizacion->update([
                'estado_dominio_personalizado' => 'pendiente_configuracion',
                'fecha_verificacion_dns' => null,
                'detalles_verificacion_dns' => json_encode($resultado['detalles']),
                'observaciones_dominio' => $resultado['mensaje'],
            ]);

            // Registrar en auditoría
            Auditoria::registrar(
                'organizacion',
                'reverificar_dns',
                "Error en reverificación DNS para dominio: {$organizacion->dominio_personalizado}. Error: {$resultado['mensaje']}",
                'organizaciones',
                $organizacion->id
            );

            return redirect()->route('organizacion.edit')
                ->with('error', '⚠️ Error en verificación DNS: ' . $resultado['mensaje']);
        }
    }

    /**
     * Muestra la página de upgrade de plan
     */
    public function upgrade()
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);
        $planes = \App\Models\Suscripcion::orderBy('precio_mensual', 'asc')->get();

        return view('organizacion.upgrade', compact('organizacion', 'planes'));
    }

    /**
     * Elimina el logo de la organización
     */
    public function deleteLogo()
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::find($user->id_organizacion);

        if (!$organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'Organización no encontrada.');
        }

        // Eliminar logo del almacenamiento si existe
        if ($organizacion->logo && \Storage::disk('public')->exists($organizacion->logo)) {
            \Storage::disk('public')->delete($organizacion->logo);
        }

        // Actualizar registro
        $organizacion->update(['logo' => null]);

        return redirect()->route('organizacion.edit')
            ->with('success', 'Logo eliminado correctamente.');
    }

    /**
     * Mostrar página de confirmación antes de cambiar de plan
     */
    public function mostrarConfirmacionCambioPlan($idSuscripcionNueva)
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);

        if (!$organizacion || !$organizacion->suscripcion) {
            return redirect()->route('dashboard')
                ->with('error', 'Organización o suscripción no encontrada.');
        }

        // Validar que la suscripción nueva sea diferente
        if ($organizacion->id_suscripcion == $idSuscripcionNueva) {
            return redirect()->back()
                ->with('error', 'Ya estás en este plan.');
        }

        $planActual = $organizacion->suscripcion;
        $planNuevo = \App\Models\Suscripcion::findOrFail($idSuscripcionNueva);

        // Determinar tipo de cambio
        $tipo = $planNuevo->precio_mensual > $planActual->precio_mensual ? 'upgrade' : 'downgrade';

        // Calcular monto a pagar
        $montoPagar = $this->calcularDiferenciaPlan(
            $organizacion,
            $planActual,
            $planNuevo,
            $tipo
        );

        // Calcular días restantes (solo si suscripción activa)
        $diasRestantes = null;
        if (!$organizacion->suscripcionVencida() && $organizacion->fecha_fin_suscripcion) {
            $diasRestantes = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($organizacion->fecha_fin_suscripcion));
        }

        return view('organizacion.confirmar-cambio-plan', compact(
            'organizacion',
            'planActual',
            'planNuevo',
            'tipo',
            'montoPagar',
            'diasRestantes'
        ));
    }

    /**
     * Iniciar proceso de cambio de plan (upgrade o downgrade)
     */
    public function iniciarCambioPlan(Request $request, $idSuscripcionNueva)
    {
        $user = auth()->user();

        if (!$user->id_organizacion) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::with('suscripcion')->find($user->id_organizacion);

        if (!$organizacion || !$organizacion->suscripcion) {
            return redirect()->route('dashboard')
                ->with('error', 'Organización o suscripción no encontrada.');
        }

        // Validar que la suscripción nueva sea diferente
        if ($organizacion->id_suscripcion == $idSuscripcionNueva) {
            return redirect()->back()
                ->with('error', 'Ya estás en este plan.');
        }

        $suscripcionNueva = \App\Models\Suscripcion::findOrFail($idSuscripcionNueva);

        // Determinar tipo de cambio
        $tipo = $suscripcionNueva->precio_mensual > $organizacion->suscripcion->precio_mensual ? 'upgrade' : 'downgrade';

        // Calcular diferencia prorrateada
        $montoDiferencia = $this->calcularDiferenciaPlan(
            $organizacion,
            $organizacion->suscripcion,
            $suscripcionNueva,
            $tipo
        );

        // Crear registro de cambio de plan
        $cambioPlan = \App\Models\CambioPlan::create([
            'id_organizacion' => $organizacion->id,
            'id_suscripcion_anterior' => $organizacion->id_suscripcion,
            'id_suscripcion_nueva' => $idSuscripcionNueva,
            'tipo' => $tipo,
            'estado' => 'pendiente',
            'monto_anterior' => $organizacion->suscripcion->precio_mensual,
            'monto_nuevo' => $suscripcionNueva->precio_mensual,
            'monto_diferencia' => $montoDiferencia,
        ]);

        // Si es upgrade, generar pago con Flow
        if ($tipo === 'upgrade' && $montoDiferencia > 0) {
            // Verificar si ya existe un cambio de plan pendiente con token
            $cambioPlanPendiente = \App\Models\CambioPlan::where('id_organizacion', $organizacion->id)
                ->where('id_suscripcion_nueva', $idSuscripcionNueva)
                ->where('estado', 'procesando')
                ->whereNotNull('token_flow')
                ->first();

            if ($cambioPlanPendiente) {
                // Verificar si la transacción existe y está pendiente
                $transaccion = \App\Models\TransaccionFlow::where('token', $cambioPlanPendiente->token_flow)
                    ->where('estado', 'pendiente')
                    ->first();

                if ($transaccion) {
                    \Log::info('Redirigiendo a cambio de plan existente', ['token' => $cambioPlanPendiente->token_flow]);
                    $urlPago = 'https://www.flow.cl/app/web/pay.php?token=' . $cambioPlanPendiente->token_flow;

                    // Eliminar el nuevo cambio de plan creado y usar el existente
                    $cambioPlan->delete();

                    return redirect($urlPago);
                }
            }

            $flowService = new \App\Services\FlowPaymentService();

            $emailOrganizacion = $organizacion->email_contacto ?? $user->email;
            $subject = "Upgrade a Plan {$suscripcionNueva->nombre_mostrar}";

            $resultado = $flowService->crearPago(
                null, // No hay socio asociado
                null, // No hay boleta asociada
                $montoDiferencia,
                $emailOrganizacion,
                $subject
            );

            if ($resultado['success']) {
                // Actualizar cambio de plan con token de Flow
                $cambioPlan->update([
                    'token_flow' => $resultado['token'],
                    'id_transaccion_flow' => $resultado['transaccion']->id,
                    'estado' => 'procesando',
                ]);

                // Actualizar transacción Flow para indicar que es un cambio de plan
                $resultado['transaccion']->update([
                    'subject' => $subject,
                    'observaciones' => "Cambio de plan de {$organizacion->suscripcion->nombre_mostrar} a {$suscripcionNueva->nombre_mostrar}",
                    'tipo_pago' => 'cambio_plan',
                    'referencia_id' => $cambioPlan->id,
                ]);

                // Redirigir al pago de Flow
                return redirect($resultado['url']);
            } else {
                $cambioPlan->update([
                    'estado' => 'rechazado',
                    'observaciones' => 'Error al generar pago: ' . ($resultado['message'] ?? 'Error desconocido'),
                ]);

                return redirect()->back()
                    ->with('error', 'Error al procesar el pago: ' . ($resultado['message'] ?? 'Error desconocido'));
            }
        }

        // Si es downgrade, aplicar al final del período
        if ($tipo === 'downgrade') {
            $cambioPlan->update([
                'observaciones' => 'El cambio se aplicará al final del período de facturación actual.',
            ]);

            return redirect()->route('organizacion.index')
                ->with('success', "Cambio a plan {$suscripcionNueva->nombre_mostrar} programado. Se aplicará al final del período actual.");
        }

        return redirect()->route('organizacion.index')
            ->with('success', 'Cambio de plan procesado correctamente.');
    }

    /**
     * Calcular diferencia prorrateada del plan
     */
    private function calcularDiferenciaPlan($organizacion, $planAnterior, $planNuevo, $tipo)
    {
        // Si no hay fecha de inicio de suscripción, cobrar diferencia completa
        if (!$organizacion->fecha_inicio_suscripcion) {
            return abs($planNuevo->precio_mensual - $planAnterior->precio_mensual);
        }

        // Si la suscripción está vencida, cobrar precio completo del nuevo plan (no prorratear)
        if ($organizacion->suscripcionVencida()) {
            return $planNuevo->precio_mensual;
        }

        // Calcular días transcurridos del mes actual
        $fechaInicio = \Carbon\Carbon::parse($organizacion->fecha_inicio_suscripcion);
        $hoy = \Carbon\Carbon::now();

        // Calcular día del mes en que se renueva
        $diaRenovacion = $fechaInicio->day;

        // Determinar inicio y fin del período actual
        $inicioperiodo = \Carbon\Carbon::create($hoy->year, $hoy->month, $diaRenovacion);
        if ($inicioperiodo->isFuture()) {
            $inicioperiodo->subMonth();
        }

        $finPeriodo = $inicioperiodo->copy()->addMonth();

        // Calcular días totales del período y días restantes
        $diasTotales = $inicioperiodo->diffInDays($finPeriodo);
        $diasRestantes = $hoy->diffInDays($finPeriodo);

        // Calcular diferencia diaria
        $diferenciaDiaria = ($planNuevo->precio_mensual - $planAnterior->precio_mensual) / $diasTotales;

        // Calcular monto prorrateado
        $montoDiferencia = $diferenciaDiaria * $diasRestantes;

        // Flow requiere mínimo 350 CLP, si es menor cobrar el monto completo de la diferencia
        if ($montoDiferencia > 0 && $montoDiferencia < 350) {
            return abs($planNuevo->precio_mensual - $planAnterior->precio_mensual);
        }

        // Si es upgrade, debe ser positivo; si es downgrade, puede ser 0 (se aplicará al siguiente período)
        return max(0, $montoDiferencia);
    }

    /**
     * Confirmar cambio de plan después de pago exitoso
     */
    public function confirmarCambioPlan($idCambioPlan)
    {
        $cambioPlan = \App\Models\CambioPlan::with(['organizacion', 'suscripcionNueva'])->findOrFail($idCambioPlan);

        // Verificar que el cambio esté en estado procesando
        if ($cambioPlan->estado !== 'procesando') {
            return redirect()->route('organizacion.index')
                ->with('error', 'El cambio de plan no está en estado válido para confirmar.');
        }

        // Aplicar el cambio
        if ($cambioPlan->aplicar()) {
            return redirect()->route('organizacion.index')
                ->with('success', "Plan actualizado correctamente a {$cambioPlan->suscripcionNueva->nombre_mostrar}.");
        }

        return redirect()->route('organizacion.index')
            ->with('error', 'Error al aplicar el cambio de plan.');
    }

    /**
     * Solicitar compra de dominio personalizado
     */
    public function solicitarCompraDominio(Request $request)
    {
        \Log::info('=== SOLICITUD DOMINIO INICIADA ===');
        \Log::info('Request data: ', $request->all());

        $user = auth()->user();
        \Log::info('Usuario ID: ' . $user->id . ', Org ID: ' . ($user->id_organizacion ?? 'NULL'));

        if (!$user->id_organizacion) {
            \Log::warning('Usuario sin organización');
            return redirect()->back()->with('error', 'No tienes una organización asignada.');
        }

        $organizacion = Organizacion::find($user->id_organizacion);
        \Log::info('Organización encontrada: ' . $organizacion->nombre_apr);

        // Verificar que tenga plan Enterprise
        if (!$organizacion->suscripcion || !$organizacion->suscripcion->permite_dominio_personalizado) {
            \Log::warning('Organización sin plan Enterprise');
            return redirect()->back()->with('error', 'Esta función solo está disponible para el plan Enterprise.');
        }

        // Validar
        \Log::info('Validando dominio...');
        $validated = $request->validate([
            'dominio_solicitado' => 'required|string|regex:/^[a-z0-9\-]+$/|max:50',
        ], [
            'dominio_solicitado.regex' => 'El dominio solo puede contener letras minúsculas, números y guiones.',
        ]);
        \Log::info('Dominio validado: ' . $validated['dominio_solicitado']);

        // Construir dominio completo
        $dominioCompleto = 'www.' . strtolower($validated['dominio_solicitado']) . '.cl';
        \Log::info('Dominio completo: ' . $dominioCompleto);

        // Verificar si ya tiene una solicitud pendiente
        \Log::info('Verificando solicitudes existentes...');
        $solicitudExistente = \App\Models\SolicitudCompraDominio::where('id_organizacion', $organizacion->id)
            ->whereIn('estado', ['solicitado', 'verificado_disponible', 'pendiente_pago', 'pagado', 'comprado'])
            ->first();

        if ($solicitudExistente) {
            \Log::warning('Ya existe una solicitud pendiente');
            return redirect()->back()->with('error', 'Ya tienes una solicitud de dominio en proceso.');
        }

        // Crear solicitud
        \Log::info('Creando solicitud en BD...');
        $solicitud = \App\Models\SolicitudCompraDominio::create([
            'id_organizacion' => $organizacion->id,
            'dominio_solicitado' => $dominioCompleto,
            'estado' => 'solicitado',
            'monto' => 20000,
        ]);
        \Log::info('Solicitud creada con ID: ' . $solicitud->id);

        // Enviar email al SuperAdmin
        try {
            \Mail::raw(
                "Nueva solicitud de compra de dominio\n\n" .
                "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                "Organización: {$organizacion->nombre_apr}\n" .
                "RUT: {$organizacion->rut}\n" .
                "Dominio solicitado: {$dominioCompleto}\n" .
                "Monto: $20.000\n" .
                "Fecha: " . now()->format('d/m/Y H:i') . "\n" .
                "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
                "ACCIONES REQUERIDAS:\n" .
                "1. Verifica disponibilidad en https://nic.cl/whois\n" .
                "2. Gestiona la solicitud en:\n" .
                "   https://sistemaapr.cl/superadmin/solicitudes-dominio\n\n" .
                "Saludos,\n" .
                "Sistema Automático",
                function($message) {
                    $message->to('soportesistemaapr@gmail.com')
                            ->subject('🌐 Nueva Solicitud de Dominio');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando email de solicitud dominio: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "✅ Solicitud enviada correctamente. Verificaremos la disponibilidad de {$dominioCompleto} y te contactaremos en las próximas 24 horas.");
    }
}
