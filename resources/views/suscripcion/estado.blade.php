<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción {{ ucfirst($organizacion->estado_suscripcion) }} - {{ $organizacion->nombre_apr }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-600 to-gray-800 p-8 text-white text-center">
                <div class="mb-4">
                    @if($organizacion->estado_suscripcion === 'suspendida')
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    @else
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </div>
                <h1 class="text-3xl font-bold mb-2">Suscripción {{ ucfirst($organizacion->estado_suscripcion) }}</h1>
                <p class="text-gray-200 text-lg">Tu cuenta ha sido {{ $organizacion->estado_suscripcion }}</p>
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
                            <p class="font-semibold text-gray-600 uppercase">{{ $organizacion->estado_suscripcion }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email de contacto:</p>
                            <p class="font-semibold text-gray-900">{{ $organizacion->email_contacto }}</p>
                        </div>
                    </div>
                </div>

                <!-- Mensaje según estado -->
                @if($organizacion->estado_suscripcion === 'suspendida')
                <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-orange-700 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-orange-800 mb-1">Cuenta Suspendida</h3>
                            <p class="text-sm text-orange-700">
                                Tu cuenta ha sido suspendida temporalmente. Por favor contacta al administrador del sistema para más información sobre la reactivación.
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-700 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-red-800 mb-1">Cuenta Cancelada</h3>
                            <p class="text-sm text-red-700">
                                Tu cuenta ha sido cancelada. Si crees que esto es un error o deseas reactivarla, contacta al administrador del sistema.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Opciones de contacto -->
                <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <svg class="w-8 h-8 text-purple-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Contacta al administrador</h3>
                            <p class="text-gray-700 mb-4">
                                Para resolver esta situación y reactivar tu cuenta, envía un correo al administrador del sistema.
                            </p>
                            <a href="mailto:admin@sistemaapr.cl?subject=Reactivación de cuenta - {{ $organizacion->nombre_apr }}&body=Hola, mi cuenta de {{ $organizacion->nombre_apr }} (RUT: {{ $organizacion->rut }}) está {{ $organizacion->estado_suscripcion }}. Necesito información sobre cómo reactivarla."
                               class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                Enviar correo de soporte
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-700 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-800 mb-1">Tus datos están seguros</h3>
                            <p class="text-sm text-blue-700">
                                Toda la información de tu organización, socios y pagos está almacenada de forma segura y estará disponible cuando tu cuenta sea reactivada.
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
            <p>¿Necesitas ayuda? Contacta a <a href="mailto:soporte@sistemaapr.cl" class="text-purple-600 hover:text-purple-700 font-medium">soporte@sistemaapr.cl</a></p>
        </div>
    </div>
</body>
</html>
