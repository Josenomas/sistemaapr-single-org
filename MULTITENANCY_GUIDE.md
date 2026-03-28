# Guía de Multitenancy - Sistema APR SaaS

## 📋 Resumen Ejecutivo

Este documento describe la implementación completa del sistema de multitenancy (multi-inquilino) con suscripciones para el Sistema APR, convirtiéndolo en una plataforma SaaS (Software as a Service) que permite que múltiples organizaciones APR utilicen el mismo sistema de forma aislada y segura.

---

## 🏗️ Arquitectura del Sistema

### Modelo de Datos

El sistema utiliza un modelo de multitenancy **basado en columnas** (`id_organizacion`), donde cada registro de datos pertenece a una organización específica.

```
organizaciones (tabla central)
  ├── suscripciones (planes disponibles)
  └── usuarios, socios, boletas, lecturas... (27 tablas con id_organizacion)
```

### Planes de Suscripción

| Plan | Precio/mes | Socios | Usuarios | Características Especiales |
|------|------------|--------|----------|----------------------------|
| **Básico** | $20,000 | 100 | 1 | Módulos básicos |
| **Profesional** | $30,000 | 500 | 5 | Más módulos, reportes avanzados |
| **Enterprise** | $50,000 | Ilimitados | Ilimitados | Todos los módulos + Noticias + Dominio personalizado |

---

## 🔧 Componentes Implementados

### 1. Base de Datos

#### Tablas Principales
- **`suscripciones`**: Define los planes disponibles
- **`organizaciones`**: Cada APR es una organización
- **`noticias`**: Módulo exclusivo de plan Enterprise

#### Tablas con Multitenancy (27 tablas)
Todas incluyen columna `id_organizacion` con foreign key a `organizaciones`:

**Gestión de Socios:**
- `socios`
- `lecturas`
- `historial_consumo`
- `boletas`
- `pagos`

**Servicios:**
- `incidentes`
- `cortes_suministro`
- `renovaciones_medidores`

**RRHH:**
- `funcionarios`
- `sueldos`
- `vacaciones`
- `directiva`

**Finanzas:**
- `inventario`
- `movimientos_inventario`
- `movimientos_inventario_detalle`
- `compras`
- `giros_bancarios`

**Configuración:**
- `configuraciones_tarifas`
- `tarifas`

**Comunicación:**
- `notificaciones`
- `recordatorios`
- `eventos`
- `tickets`
- `ticket_respuestas`

**Contabilidad:**
- `activos_fijos`
- `rendiciones_mensuales`

**Operaciones:**
- `trabajos_realizados`
- `transacciones_flow`

### 2. Modelos Eloquent

#### Trait `BelongsToOrganizacion`
**Ubicación:** `app/Models/Traits/BelongsToOrganizacion.php`

Aplicado a 25+ modelos, proporciona:
- **Global Scope automático**: Filtra todas las consultas por `id_organizacion`
- **Auto-asignación**: Asigna automáticamente `id_organizacion` al crear registros
- **Relación**: Método `organizacion()` para acceder a la organización

```php
use App\Models\Traits\BelongsToOrganizacion;

class Socio extends Model
{
    use BelongsToOrganizacion; // ¡Eso es todo!
}
```

#### Modelos Principales

**`Suscripcion.php`**
```php
// Métodos útiles
$suscripcion->permiteModulo('noticias'); // bool
$suscripcion->tieneSociosIlimitados(); // bool
$suscripcion->tieneUsuariosIlimitados(); // bool
```

**`Organizacion.php`**
```php
// Métodos útiles
$organizacion->suscripcionActiva(); // bool
$organizacion->puedeAccederModulo('noticias'); // bool
$organizacion->puedeAgregarSocio(); // bool
$organizacion->puedeAgregarUsuario(); // bool
```

**`Noticia.php`** (Solo Enterprise)
- Auto-genera slug
- Cuenta vistas
- Scope `publicadas()`

### 3. Middleware

**Ubicación:** `app/Http/Middleware/`

| Middleware | Propósito | Uso |
|------------|-----------|-----|
| `CheckSuscripcionActiva` | Verifica que la suscripción esté activa | Aplicado globalmente a rutas autenticadas |
| `CheckModuloPermitido` | Valida acceso a módulos según plan | `modulo.permitido:noticias` |
| `CheckLimiteSocios` | Previene exceder límite de socios | En ruta POST `/socios` |
| `CheckLimiteUsuarios` | Previene exceder límite de usuarios | En ruta POST `/usuarios` |

**Registro en `app/Http/Kernel.php`:**
```php
protected $middlewareAliases = [
    'suscripcion.activa' => \App\Http\Middleware\CheckSuscripcionActiva::class,
    'modulo.permitido' => \App\Http\Middleware\CheckModuloPermitido::class,
    'limite.socios' => \App\Http\Middleware\CheckLimiteSocios::class,
    'limite.usuarios' => \App\Http\Middleware\CheckLimiteUsuarios::class,
];
```

### 4. Controladores

#### `OrganizacionController.php`
Gestiona información de la organización actual:
- `index()` - Ver información y estadísticas de uso
- `edit()` - Editar datos de la organización
- `update()` - Actualizar organización (nombre, logo, colores)
- `upgrade()` - Ver planes y cambiar suscripción

#### `NoticiasController.php` (Enterprise)
CRUD completo de noticias:
- `index()` - Lista de noticias (admin)
- `create()` / `store()` - Crear noticia
- `edit()` / `update()` - Editar noticia
- `destroy()` - Eliminar noticia
- `publicas()` - Vista pública de noticias
- `verPublica($slug)` - Ver noticia pública individual

### 5. Rutas

#### Rutas Protegidas (requieren autenticación)
```php
// Aplicado a TODAS las rutas autenticadas
Route::middleware(['auth', 'suscripcion.activa'])->group(function () {
    // ...
});

// Módulo de Noticias (solo Enterprise)
Route::middleware('modulo.permitido:noticias')->group(function () {
    Route::resource('noticias', NoticiasController::class);
});

// Gestión de Organización
Route::prefix('organizacion')->group(function () {
    Route::get('/', [OrganizacionController::class, 'index']);
    Route::get('/editar', [OrganizacionController::class, 'edit']);
    Route::put('/actualizar', [OrganizacionController::class, 'update']);
    Route::get('/upgrade', [OrganizacionController::class, 'upgrade']);
});
```

#### Rutas Públicas
```php
// Noticias públicas
Route::get('/noticias-publicas', [NoticiasController::class, 'publicas']);
Route::get('/noticia/{slug}', [NoticiasController::class, 'verPublica']);
```

### 6. Vistas

#### Módulo de Organización
- **`organizacion/index.blade.php`** - Dashboard de suscripción
  - Información de la APR
  - Detalles del plan actual
  - Estadísticas de uso (socios, usuarios)
  - Información de facturación

- **`organizacion/edit.blade.php`** - Editar organización
  - Datos generales (nombre, RUT, dirección)
  - Upload de logo
  - Colores personalizados (color picker)

- **`organizacion/upgrade.blade.php`** - Cambio de plan
  - Cards comparativas de planes
  - Botones de upgrade/downgrade
  - Información de facturación

---

## 🚀 Uso del Sistema

### Para Administradores del Sistema

#### Crear Nueva Organización

```php
use App\Models\Organizacion;
use App\Models\Suscripcion;

$planBasico = Suscripcion::where('nombre', 'basico')->first();

$organizacion = Organizacion::create([
    'nombre_apr' => 'APR Mi Comuna',
    'slug' => 'apr-mi-comuna',
    'rut' => '12345678-9',
    'id_suscripcion' => $planBasico->id,
    'estado_suscripcion' => 'prueba',
    'dias_prueba_restantes' => 30,
    'activo' => true,
]);
```

#### Crear Usuario para Organización

```php
use App\Models\Usuario;

Usuario::create([
    'nombre_usuario' => 'admin',
    'password' => bcrypt('password'),
    'nombre' => 'Admin',
    'apellido' => 'APR',
    'email' => 'admin@aprmico  muna.cl',
    'rol' => 'admin',
    'id_organizacion' => $organizacion->id,
    'activo' => true,
]);
```

### Para Desarrolladores

#### Agregar Nueva Tabla al Multitenancy

1. **Crear migración con `id_organizacion`:**
```php
Schema::create('mi_tabla', function (Blueprint $table) {
    $table->id();
    $table->foreignId('id_organizacion')->constrained('organizaciones')->onDelete('cascade');
    $table->index('id_organizacion');
    // otros campos...
});
```

2. **Aplicar trait al modelo:**
```php
use App\Models\Traits\BelongsToOrganizacion;

class MiModelo extends Model
{
    use BelongsToOrganizacion;

    protected $fillable = ['id_organizacion', /* otros campos */];
}
```

¡Eso es todo! El modelo ahora filtrará automáticamente por organización.

#### Crear Módulo Exclusivo de un Plan

1. **Agregar módulo a la suscripción:**
```php
// En SuscripcionesSeeder o manualmente en BD
'modulos_permitidos' => json_encode([
    'socios', 'lecturas', 'boletas', 'mi_modulo_nuevo'
])
```

2. **Proteger rutas con middleware:**
```php
Route::middleware('modulo.permitido:mi_modulo_nuevo')->group(function () {
    Route::resource('mi-modulo', MiModuloController::class);
});
```

3. **Validar en controlador:**
```php
public function index()
{
    if (!auth()->user()->organizacion->puedeAccederModulo('mi_modulo_nuevo')) {
        return redirect()->route('dashboard')
            ->with('error', 'Módulo no disponible en tu plan.');
    }
    // ...
}
```

---

## 📊 Datos de Prueba

### Organizaciones Creadas

| ID | Nombre | Slug | Plan | Usuario | Contraseña |
|----|--------|------|------|---------|------------|
| 1 | APR Prueba Desarrollo | apr-prueba | Profesional | admin | admin123 |
| 2 | APR Enterprise Demo | apr-enterprise | Enterprise | admin2 | admin123 |

### Datos Asignados

**Organización 1:**
- 5 socios
- 2 lecturas
- 1 funcionario
- 51 registros totales

**Organización 2:**
- 3 socios
- 3 lecturas
- 2 funcionarios

### Probar Aislamiento

1. Login con `admin` (Org 1) → Ver 5 socios
2. Logout
3. Login con `admin2` (Org 2) → Ver 3 socios DIFERENTES
4. Crear socio con `admin` → Solo aparece en Org 1
5. Crear socio con `admin2` → Solo aparece en Org 2

---

## 🔐 Seguridad

### Aislamiento de Datos

✅ **Global Scope automático** - Imposible acceder a datos de otra organización
✅ **Foreign Keys** - Integridad referencial garantizada
✅ **Middleware de validación** - Múltiples capas de seguridad
✅ **Auto-asignación** - No se puede olvidar asignar organización

### Validaciones Implementadas

1. **Suscripción Activa**: Todas las rutas autenticadas
2. **Límites de Plan**: Socios y usuarios
3. **Acceso a Módulos**: Por tipo de suscripción
4. **Permisos de Usuario**: Sistema de roles existente

---

## 📈 Próximas Mejoras Sugeridas

### Fase 2 - Registro Público
- [ ] Formulario de registro para nuevas organizaciones
- [ ] Verificación de email
- [ ] Onboarding automático
- [ ] Dashboard de bienvenida

### Fase 3 - Pagos
- [ ] Integración con Transbank/Mercado Pago
- [ ] Renovación automática de suscripciones
- [ ] Facturas electrónicas
- [ ] Historial de pagos

### Fase 4 - Administración
- [ ] Panel super-admin para gestionar organizaciones
- [ ] Métricas y analytics por organización
- [ ] Suspensión/activación de cuentas
- [ ] Soporte multiidioma

### Fase 5 - Avanzado
- [ ] WhiteLabel completo (subdominios personalizados)
- [ ] API REST para integraciones
- [ ] Exportación masiva de datos
- [ ] Sistema de backups automáticos por organización

---

## 🐛 Solución de Problemas

### "No veo datos después de login"

Verificar que el usuario tenga `id_organizacion` asignado:
```sql
SELECT id, nombre_usuario, id_organizacion FROM usuarios WHERE nombre_usuario = 'admin';
```

### "Error al crear socio: límite excedido"

El plan tiene límite de socios. Opciones:
1. Upgrade a plan superior
2. Eliminar socios inactivos
3. Contactar soporte

### "No puedo acceder al módulo de noticias"

Solo disponible en plan Enterprise. Ir a `/organizacion/upgrade` para cambiar plan.

### "Veo datos de otra organización"

🚨 **Esto NO debería pasar**. Si sucede:
1. Verificar que el modelo use `BelongsToOrganizacion` trait
2. Revisar consultas custom (deben usar Eloquent, no DB::table directamente)
3. Reportar bug inmediatamente

---

## 📞 Soporte

- **Email**: soporte@sistemaapr.cl
- **Documentación**: Ver este archivo
- **GitHub Issues**: [Reportar problema](https://github.com/tu-repo/issues)

---

## 📝 Notas de Versión

### v2.0.0 - Sistema Multitenancy (Marzo 2026)
- ✅ Implementación completa de multitenancy
- ✅ 3 planes de suscripción (Básico, Profesional, Enterprise)
- ✅ Global Scopes automáticos en 25+ modelos
- ✅ 4 middleware de validación
- ✅ Módulo de gestión de organización
- ✅ Módulo de noticias (Enterprise)
- ✅ Datos de prueba para 2 organizaciones
- ✅ Aislamiento de datos verificado

---

**Desarrollado con ❤️ para APR de Chile**
