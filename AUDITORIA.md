# Sistema de Auditoría - Documentación

## Descripción

El sistema de auditoría registra automáticamente todas las acciones importantes realizadas en el sistema, permitiendo trazabilidad completa de quién hizo qué, cuándo y desde dónde.

## Características

- ✅ Registro automático de usuario, organización, IP y navegador
- ✅ Almacenamiento de datos antes/después para cambios
- ✅ Vista de auditoría en panel super-admin con filtros avanzados
- ✅ Paginación automática
- ✅ Exportable a JSON para análisis

## Cómo Usar

### 1. Importar el modelo

```php
use App\Models\Auditoria;
```

### 2. Registrar una acción

#### Método Simple (sin datos anteriores/nuevos)

```php
Auditoria::registrar(
    'socios',           // Módulo
    'crear',            // Acción
    'Creó socio: Juan Pérez'  // Descripción
);
```

#### Método Completo (con datos antes/después)

```php
Auditoria::registrar(
    'socios',                    // Módulo
    'editar',                    // Acción
    'Editó socio #123',          // Descripción
    'socios',                    // Tabla afectada
    $socio->id,                  // ID del registro
    $socio->getOriginal(),       // Datos anteriores
    $socio->getAttributes()      // Datos nuevos
);
```

### 3. Ejemplos por Módulo

#### Socios

```php
// Al crear un socio
Auditoria::registrar(
    'socios',
    'crear',
    "Creó socio: {$socio->nombre} {$socio->apellido_paterno}",
    'socios',
    $socio->id,
    null,
    $socio->toArray()
);

// Al editar un socio
$datosAnteriores = $socio->getOriginal();
$socio->update($request->all());

Auditoria::registrar(
    'socios',
    'editar',
    "Editó socio #{$socio->id}: {$socio->nombre} {$socio->apellido_paterno}",
    'socios',
    $socio->id,
    $datosAnteriores,
    $socio->fresh()->toArray()
);

// Al eliminar un socio
Auditoria::registrar(
    'socios',
    'eliminar',
    "Eliminó socio #{$socio->id}: {$socio->nombre} {$socio->apellido_paterno}",
    'socios',
    $socio->id,
    $socio->toArray(),
    null
);
```

#### Boletas

```php
// Al crear una boleta
Auditoria::registrar(
    'boletas',
    'crear',
    "Generó boleta #{$boleta->numero_boleta} para {$boleta->socio->nombre}",
    'boletas',
    $boleta->id
);

// Al anular una boleta
Auditoria::registrar(
    'boletas',
    'anular',
    "Anuló boleta #{$boleta->numero_boleta}",
    'boletas',
    $boleta->id,
    ['estado' => 'emitida'],
    ['estado' => 'anulada']
);
```

#### Lecturas

```php
Auditoria::registrar(
    'lecturas',
    'importar',
    "Importó {$cantidadLecturas} lecturas desde archivo CSV",
    'lecturas',
    null,
    null,
    ['cantidad' => $cantidadLecturas, 'archivo' => $archivo->getClientOriginalName()]
);
```

#### Usuarios

```php
// Al crear usuario
Auditoria::registrar(
    'usuarios',
    'crear',
    "Creó usuario: {$usuario->nombre_usuario} ({$usuario->rol})",
    'usuarios',
    $usuario->id
);

// Al cambiar contraseña
Auditoria::registrar(
    'usuarios',
    'cambiar_password',
    "Cambió contraseña del usuario {$usuario->nombre_usuario}",
    'usuarios',
    $usuario->id
);
```

#### Suscripción

```php
// Al cambiar plan
Auditoria::registrar(
    'suscripcion',
    'cambiar_plan',
    "Cambió plan de {$planAnterior->nombre} a {$planNuevo->nombre}",
    'organizaciones',
    $org->id,
    ['plan' => $planAnterior->nombre],
    ['plan' => $planNuevo->nombre]
);

// Al suspender organización
Auditoria::registrar(
    'organizacion',
    'suspender',
    "Organización suspendida automáticamente por falta de pago",
    'organizaciones',
    $org->id,
    ['estado' => 'activa'],
    ['estado' => 'suspendida']
);
```

## Módulos Disponibles

- `socios` - Gestión de socios
- `boletas` - Generación y gestión de boletas
- `pagos` - Registros de pagos
- `lecturas` - Lecturas de medidores
- `usuarios` - Gestión de usuarios
- `suscripcion` - Cambios de plan
- `organizacion` - Cambios en la organización
- `sistema` - Acciones del sistema
- `auth` - Login/Logout (ya implementado)

## Acciones Disponibles

- `crear` - Creación de registros
- `editar` / `actualizar` - Modificación de registros
- `eliminar` - Eliminación de registros
- `login` - Inicio de sesión
- `logout` - Cierre de sesión
- `importar` - Importación masiva
- `exportar` - Exportación de datos
- `anular` - Anulación (boletas, pagos, etc.)
- `cambiar_plan` - Cambio de suscripción
- `suspender` - Suspensión de cuenta

## Acceder a los Logs

Los logs se pueden ver en:
- **Super-Admin:** `/superadmin/auditoria`
- Filtros disponibles: Organización, Módulo, Acción, Fecha

## Consultas Directas

```php
// Obtener logs de una organización
$logs = Auditoria::where('id_organizacion', $orgId)
    ->orderBy('created_at', 'desc')
    ->get();

// Obtener logs de un usuario
$logs = Auditoria::where('id_usuario', $userId)
    ->with('organizacion')
    ->paginate(50);

// Obtener logs de un módulo específico
$logs = Auditoria::where('modulo', 'socios')
    ->where('accion', 'eliminar')
    ->get();

// Logs de hoy
$logsHoy = Auditoria::whereDate('created_at', today())->get();
```

## Notas Importantes

1. El usuario y organización se obtienen automáticamente de `auth()->user()`
2. La IP y User-Agent se capturan automáticamente de la request
3. Los datos anteriores/nuevos son opcionales pero recomendados para cambios
4. Para acciones del super-admin, `id_organizacion` será `null`

## Ejemplo Completo en un Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class SocioController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required',
            'rut' => 'required',
            // ... más validaciones
        ]);

        $socio = Socio::create($validated);

        // Registrar en auditoría
        Auditoria::registrar(
            'socios',
            'crear',
            "Creó socio: {$socio->nombre} {$socio->apellido_paterno}",
            'socios',
            $socio->id,
            null,
            $socio->toArray()
        );

        return redirect()->route('socios.index')
            ->with('success', 'Socio creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $socio = Socio::findOrFail($id);
        $datosAnteriores = $socio->toArray();

        $socio->update($request->validated());

        // Registrar en auditoría
        Auditoria::registrar(
            'socios',
            'editar',
            "Editó socio #{$socio->id}: {$socio->nombre} {$socio->apellido_paterno}",
            'socios',
            $socio->id,
            $datosAnteriores,
            $socio->fresh()->toArray()
        );

        return redirect()->route('socios.index')
            ->with('success', 'Socio actualizado exitosamente');
    }

    public function destroy($id)
    {
        $socio = Socio::findOrFail($id);
        $datosAnteriores = $socio->toArray();

        $socio->delete();

        // Registrar en auditoría
        Auditoria::registrar(
            'socios',
            'eliminar',
            "Eliminó socio #{$id}: {$datosAnteriores['nombre']} {$datosAnteriores['apellido_paterno']}",
            'socios',
            $id,
            $datosAnteriores,
            null
        );

        return redirect()->route('socios.index')
            ->with('success', 'Socio eliminado exitosamente');
    }
}
```

## Recomendaciones

1. ✅ Registra **siempre** acciones de crear, editar y eliminar
2. ✅ Incluye datos antes/después en ediciones importantes
3. ✅ Usa descripciones claras y específicas
4. ✅ Mantén consistencia en nombres de módulos y acciones
5. ⚠️ No registres acciones de lectura (index, show) - solo modificaciones
6. ⚠️ No almacenes datos sensibles (contraseñas) en datos_anteriores/nuevos
