<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovar Suscripción - {{ $organizacion->nombre_apr }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-3xl w-full">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header con icono de advertencia -->
            <div class="bg-gradient-to-r from-red-500 to-orange-500 p-8 text-white text-center">
                <div class="mb-4">
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-2">Tu {{ $organizacion->estado_suscripcion === 'prueba' ? 'período de prueba' : 'suscripción' }} ha vencido</h1>
                <p class="text-red-100 text-lg">Para continuar usando el sistema, necesitas renovar tu suscripción</p>
            </div>

            <!-- Contenido -->
            <div class="p-8">
                <!-- Información de la organización -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de tu organización</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Organización:</p>
                            <p class="font-semibold text-gray-900">{{ $organizacion->nombre_apr }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">RUT:</p>
                            <p class="font-semibold text-gray-900">{{ $organizacion->rut }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Estado:</p>
                            <p class="font-semibold text-red-600 uppercase">{{ $organizacion->estado_suscripcion }}</p>
                        </div>
                        @if($organizacion->estado_suscripcion === 'prueba')
                        <div>
                            <p class="text-gray-600">Días de prueba restantes:</p>
                            <p class="font-semibold text-red-600">{{ $organizacion->dias_prueba_restantes }} días</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Opciones de renovación -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Opciones de renovación</h2>

                    <!-- Opción 1: Pagar en línea con Flow -->
                    @if($pagoPendiente)
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-4">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-green-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pagar en línea (Recomendado)</h3>
                                <p class="text-gray-700 mb-3">
                                    Renueva tu suscripción de forma inmediata usando WebPay, tarjetas de crédito o débito a través de Flow.
                                </p>
                                <div class="bg-white border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm text-gray-600">Plan actual:</p>
                                            <p class="text-xl font-bold text-gray-900">{{ $organizacion->suscripcion->nombre }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">Monto a pagar:</p>
                                            <p class="text-2xl font-bold text-green-600">${{ number_format($pagoPendiente->monto, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">Por 1 mes</p>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('organizacion.pagos-suscripcion.pagar', $pagoPendiente->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-4 rounded-lg transition shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Pagar ahora con Flow
                                        </span>
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 mt-2">Pago seguro procesado por Flow. Tu acceso será reactivado inmediatamente.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Opción 2: Pago por transferencia bancaria -->
                    @if($pagoPendiente)
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-4">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pagar por transferencia bancaria</h3>
                                <p class="text-gray-700 mb-3">
                                    Realiza una transferencia bancaria y sube el comprobante para validación manual por el administrador.
                                </p>
                                <div class="bg-white border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm text-gray-600">Monto a transferir:</p>
                                            <p class="text-2xl font-bold text-blue-600">${{ number_format($pagoPendiente->monto, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('organizacion.solicitud-pago-manual.create', $pagoPendiente->id) }}"
                                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Solicitar pago manual
                                    </span>
                                </a>
                                <p class="text-xs text-gray-500 mt-2">El administrador revisará tu comprobante en un plazo de 24-48 horas.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Opción 3: Cambiar de plan -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6">
                        <div class="flex items-start mb-4">
                            <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Renovar o cambiar de plan</h3>
                                <p class="text-gray-700 mb-4">Selecciona el plan que mejor se ajuste a tus necesidades. Puedes renovar tu plan actual o hacer upgrade/downgrade.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($planes as $plan)
                            <div class="bg-white border-2 {{ $plan->id == $organizacion->id_suscripcion ? 'border-green-500' : 'border-gray-200' }} rounded-lg p-5 relative">
                                @if($plan->id == $organizacion->id_suscripcion)
                                <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                                    Plan Actual
                                </div>
                                @endif

                                <h4 class="font-bold text-gray-900 mb-1 mt-2">{{ $plan->nombre }}</h4>
                                <p class="text-3xl font-bold text-blue-600 mb-3">${{ number_format($plan->precio_mensual, 0, ',', '.') }}<span class="text-sm text-gray-500">/mes</span></p>

                                <ul class="text-sm text-gray-600 space-y-2 mb-4">
                                    @if($plan->socios_ilimitados)
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Socios ilimitados
                                    </li>
                                    @else
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Hasta {{ $plan->max_socios }} socios
                                    </li>
                                    @endif

                                    @if($plan->usuarios_ilimitados)
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Usuarios ilimitados
                                    </li>
                                    @else
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Hasta {{ $plan->max_usuarios }} usuarios
                                    </li>
                                    @endif

                                    @if($plan->permite_dominio_personalizado)
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Dominio personalizado
                                    </li>
                                    @endif
                                </ul>

                                @if($plan->id == $organizacion->id_suscripcion)
                                <form action="{{ route('organizacion.pagos-suscripcion.pagar', $pagoPendiente->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                                        Renovar este plan
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('organizacion.confirmar-cambio-plan', $plan->id) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition text-center">
                                    @if($plan->precio_mensual > $organizacion->suscripcion->precio_mensual)
                                    Hacer Upgrade
                                    @else
                                    Cambiar a este plan
                                    @endif
                                </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-700 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-yellow-800 mb-1">Importante</h3>
                            <p class="text-sm text-yellow-700">
                                Mientras tu suscripción esté vencida, no podrás acceder a las funcionalidades del sistema.
                                Los datos de tu organización están seguros y estarán disponibles una vez renueves.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botón de logout -->
                <div class="text-center">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>¿Necesitas ayuda? Contacta a <a href="mailto:soportesistemaapr@gmail.com" class="text-purple-600 hover:text-purple-700 font-medium">soportesistemaapr@gmail.com</a></p>
        </div>
    </div>
</body>
</html>
