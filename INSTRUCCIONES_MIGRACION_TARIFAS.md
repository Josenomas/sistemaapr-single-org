# 🎯 INSTRUCCIONES DE MIGRACIÓN - SISTEMA DE TRAMOS POR TIPO DE CLIENTE

## ✅ ARCHIVOS COMPLETADOS (Backend)

### 1. Migraciones SQL
- ✅ `database/migrations/2025_11_29_add_tipo_cliente_to_configuraciones_tarifas.sql`
  - Agrega columnas: tipo_cliente, nombre_tarifa, vigente_desde, vigente_hasta
  - Inserta 12 tramos iniciales (4 por tipo de cliente)
  - Crea índices de optimización

- ✅ `database/migrations/2025_11_29_sp_generar_boletas_mes_v2.sql`
  - Reescribe procedimiento almacenado con lógica de tramos
  - Calcula según tipo_cliente y consumo
  - Aplica IVA automáticamente

### 2. Modelos PHP
- ✅ `app/Models/ConfiguracionTarifa.php`
  - Nuevos campos fillable y casts
  - Scopes: `porTipoCliente()`, `vigentesEn()`, `porNombreTarifa()`
  - Método: `calcularMontoPorConsumo()` - Calcula monto por tipo y consumo
  - Accessors: `rango_descripcion`, `nombre_tarifa_completo`, `tipo_cliente_badge`

### 3. Controladores
- ✅ `app/Http/Controllers/ConfiguracionTarifasController.php`
  - Validaciones actualizadas con campos nuevos
  - Filtros por tipo_cliente y nombre_tarifa
  - Método `calcular()` actualizado para usar tipo de cliente
  - Agrupación de tarifas en index()

- ✅ `app/Http/Controllers/BoletasController.php`
  - Método privado: `calcularBoletaPorTramos()` - Calcula boleta usando tramos
  - Método AJAX: `calcularMontos()` - Endpoint para cálculo en tiempo real
  - Import de `ConfiguracionTarifa`

### 4. Rutas
- ✅ `routes/web.php`
  - Nueva ruta: `POST /boletas/calcular-montos` → `boletas.calcularMontos`

---

## 📝 ARCHIVOS PENDIENTES (Frontend - Vistas)

### 1. Vista Index de Configuraciones Tarifarias
**Archivo**: `resources/views/configuraciones-tarifas/index.blade.php`

**Cambios necesarios**:
- Agregar filtros por tipo_cliente y nombre_tarifa
- Agrupar tramos por nombre_tarifa
- Mostrar badges de colores por tipo_cliente:
  - Residencial: badge-primary (azul)
  - Comercial: badge-warning (amarillo)
  - Industrial: badge-info (cyan)
- Mostrar vigencia (desde/hasta)
- Nueva estadística: `tipos_cliente`

**Código de ejemplo para filtros**:
```blade
<form method="GET" action="{{ route('configuraciones-tarifas.index') }}">
    <select name="tipo_cliente">
        <option value="">Todos los tipos</option>
        @foreach($tiposCliente as $tipo)
            <option value="{{ $tipo }}" {{ request('tipo_cliente') == $tipo ? 'selected' : '' }}>
                {{ ucfirst($tipo) }}
            </option>
        @endforeach
    </select>

    <select name="nombre_tarifa">
        <option value="">Todas las tarifas</option>
        @foreach($nombresTarifas as $nombre)
            <option value="{{ $nombre }}" {{ request('nombre_tarifa') == $nombre ? 'selected' : '' }}>
                {{ $nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit">Filtrar</button>
</form>
```

**Código para tabla agrupada**:
```blade
@foreach($tarifasAgrupadas as $tipoCliente => $tarifasPorNombre)
    <h3>{{ ucfirst($tipoCliente) }}</h3>

    @foreach($tarifasPorNombre as $nombreTarifa => $tramos)
        <div class="tarifa-group">
            <h4>{{ $nombreTarifa }}</h4>
            <table>
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Nombre Tramo</th>
                        <th>Rango</th>
                        <th>Monto</th>
                        <th>Cargo Fijo</th>
                        <th>IVA</th>
                        <th>Vigencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tramos as $tarifa)
                        <tr>
                            <td>{{ $tarifa->orden }}</td>
                            <td>{{ $tarifa->nombre }}</td>
                            <td>{{ $tarifa->rango_descripcion }}</td>
                            <td>${{ number_format($tarifa->monto, 0, ',', '.') }}</td>
                            <td>${{ number_format($tarifa->cargo_fijo, 0, ',', '.') }}</td>
                            <td>{{ $tarifa->iva }}%</td>
                            <td>
                                {{ $tarifa->vigente_desde->format('d/m/Y') }}
                                @if($tarifa->vigente_hasta)
                                    - {{ $tarifa->vigente_hasta->format('d/m/Y') }}
                                @else
                                    - Indefinido
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $tarifa->es_vigente ? 'badge-success' : 'badge-danger' }}">
                                    {{ $tarifa->es_vigente ? 'Vigente' : 'No vigente' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('configuraciones-tarifas.edit', $tarifa->id) }}">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
@endforeach
```

### 2. Vista Create de Configuraciones Tarifarias
**Archivo**: `resources/views/configuraciones-tarifas/create.blade.php`

**Campos a agregar**:
```blade
<!-- Select Tipo de Cliente -->
<div class="form-group">
    <label for="tipo_cliente" class="form-label required">Tipo de Cliente</label>
    <select name="tipo_cliente" id="tipo_cliente" class="form-control" required>
        <option value="">Seleccione tipo</option>
        <option value="residencial" {{ old('tipo_cliente') == 'residencial' ? 'selected' : '' }}>Residencial</option>
        <option value="comercial" {{ old('tipo_cliente') == 'comercial' ? 'selected' : '' }}>Comercial</option>
        <option value="industrial" {{ old('tipo_cliente') == 'industrial' ? 'selected' : '' }}>Industrial</option>
    </select>
</div>

<!-- Input Nombre de Tarifa -->
<div class="form-group">
    <label for="nombre_tarifa" class="form-label required">Nombre de la Tarifa</label>
    <input type="text" name="nombre_tarifa" id="nombre_tarifa"
           class="form-control" value="{{ old('nombre_tarifa') }}"
           placeholder="Ej: Tarifa Residencial 2025" required>
    <small class="text-muted">Nombre descriptivo para agrupar los tramos</small>
</div>

<!-- Date Picker Vigente Desde -->
<div class="form-group">
    <label for="vigente_desde" class="form-label required">Vigente Desde</label>
    <input type="date" name="vigente_desde" id="vigente_desde"
           class="form-control" value="{{ old('vigente_desde', date('Y-m-d')) }}" required>
</div>

<!-- Date Picker Vigente Hasta (opcional) -->
<div class="form-group">
    <label for="vigente_hasta" class="form-label">Vigente Hasta</label>
    <input type="date" name="vigente_hasta" id="vigente_hasta"
           class="form-control" value="{{ old('vigente_hasta') }}">
    <small class="text-muted">Dejar en blanco si no tiene fecha de término</small>
</div>
```

### 3. Vista Edit de Configuraciones Tarifarias
**Archivo**: `resources/views/configuraciones-tarifas/edit.blade.php`

**Mismos campos que create, pero con valores del modelo**:
```blade
<select name="tipo_cliente" id="tipo_cliente" class="form-control" required>
    <option value="residencial" {{ $tarifa->tipo_cliente == 'residencial' ? 'selected' : '' }}>Residencial</option>
    <option value="comercial" {{ $tarifa->tipo_cliente == 'comercial' ? 'selected' : '' }}>Comercial</option>
    <option value="industrial" {{ $tarifa->tipo_cliente == 'industrial' ? 'selected' : '' }}>Industrial</option>
</select>

<input type="text" name="nombre_tarifa" value="{{ $tarifa->nombre_tarifa }}" required>

<input type="date" name="vigente_desde" value="{{ $tarifa->vigente_desde->format('Y-m-d') }}" required>

<input type="date" name="vigente_hasta"
       value="{{ $tarifa->vigente_hasta ? $tarifa->vigente_hasta->format('Y-m-d') : '' }}">
```

### 4. Simulador de Tarifas
**Archivo**: `resources/views/configuraciones-tarifas/simulador.blade.php`

**Agregar select de tipo de cliente**:
```blade
<div class="form-group">
    <label for="tipo_cliente">Tipo de Cliente</label>
    <select id="tipo_cliente" class="form-control" required>
        <option value="">Seleccione tipo</option>
        <option value="residencial">Residencial</option>
        <option value="comercial">Comercial</option>
        <option value="industrial">Industrial</option>
    </select>
</div>

<div class="form-group">
    <label for="consumo">Consumo (m³)</label>
    <input type="number" id="consumo" class="form-control"
           min="0" step="0.01" placeholder="Ej: 25.5">
</div>

<button type="button" onclick="calcularTarifa()">Calcular</button>

<script>
function calcularTarifa() {
    const tipoCliente = document.getElementById('tipo_cliente').value;
    const consumo = document.getElementById('consumo').value;

    if (!tipoCliente || !consumo) {
        alert('Complete todos los campos');
        return;
    }

    fetch('{{ route("configuraciones-tarifas.calcular") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            tipo_cliente: tipoCliente,
            consumo: parseFloat(consumo)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar resultado
            document.getElementById('resultado').innerHTML = `
                <h3>Resultado del Cálculo</h3>
                <p><strong>Tipo:</strong> ${data.data.tipo_cliente}</p>
                <p><strong>Tarifa:</strong> ${data.data.nombre_tarifa}</p>
                <p><strong>Tramo:</strong> ${data.data.tramo} (${data.data.rango})</p>
                <p><strong>Monto Base:</strong> ${data.data.monto_base_formateado}</p>
                <p><strong>Cargo Fijo:</strong> ${data.data.cargo_fijo_formateado}</p>
                <p><strong>Cargo Consumo:</strong> ${data.data.cargo_consumo_formateado}</p>
                <p><strong>IVA (${data.data.porcentaje_iva}%):</strong> ${data.data.monto_iva_formateado}</p>
                <p class="total"><strong>TOTAL:</strong> ${data.data.total_formateado}</p>
            `;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al calcular tarifa');
    });
}
</script>
```

---

## 🗄️ EJECUCIÓN DE MIGRACIONES SQL

### Paso 1: Backup de Base de Datos (CRÍTICO)
```bash
# Desde XAMPP Control Panel o terminal
cd C:\xampp\mysql\bin
mysqldump -u root sistema_apr > C:\xampp\htdocs\ssr\backup_antes_migracion_$(date +%Y%m%d_%H%M%S).sql
```

### Paso 2: Ejecutar Migración de Tabla
```bash
cd C:\xampp\mysql\bin
mysql -u root sistema_apr < C:\xampp\htdocs\ssr\database\migrations\2025_11_29_add_tipo_cliente_to_configuraciones_tarifas.sql
```

**Verificación**:
```sql
USE sistema_apr;
DESCRIBE configuraciones_tarifas;
SELECT * FROM configuraciones_tarifas;
-- Deberías ver 12 registros (4 residenciales, 4 comerciales, 4 industriales)
```

### Paso 3: Ejecutar Migración de Procedimiento Almacenado
```bash
mysql -u root sistema_apr < C:\xampp\htdocs\ssr\database\migrations\2025_11_29_sp_generar_boletas_mes_v2.sql
```

**Verificación**:
```sql
SHOW PROCEDURE STATUS WHERE Name = 'sp_generar_boletas_mes';
-- Debería mostrar el procedimiento
```

---

## 🧪 TESTING

### 1. Probar Cálculo de Tarifas (PHP)
```php
use App\Models\ConfiguracionTarifa;

// Test 1: Residencial, 8 m³ (Tramo 1)
$resultado = ConfiguracionTarifa::calcularMontoPorConsumo('residencial', 8);
// Esperado: monto_base = 5000, total con IVA = 5950

// Test 2: Comercial, 25 m³ (Tramo 2)
$resultado = ConfiguracionTarifa::calcularMontoPorConsumo('comercial', 25);
// Esperado: monto_base = 15000, total con IVA = 17850

// Test 3: Industrial, 80 m³ (Tramo 4)
$resultado = ConfiguracionTarifa::calcularMontoPorConsumo('industrial', 80);
// Esperado: monto_base = 60000, total con IVA = 71400
```

### 2. Probar Generación Masiva de Boletas
```sql
-- IMPORTANTE: Primero asegúrate de tener lecturas registradas
USE sistema_apr;

-- Verificar que hay lecturas para el mes de prueba
SELECT COUNT(*) FROM lecturas WHERE mes = '2025-12';

-- Generar boletas del mes
CALL sp_generar_boletas_mes('2025-12');

-- Verificar boletas generadas
SELECT
    b.numero_boleta,
    s.nombre,
    s.tipo_cliente,
    b.consumo_m3,
    b.cargo_fijo,
    b.cargo_consumo,
    b.otros_cargos as iva,
    b.total
FROM boletas b
INNER JOIN socios s ON b.id_socio = s.id
WHERE b.mes = '2025-12'
ORDER BY s.tipo_cliente, b.consumo_m3;
```

### 3. Probar Endpoints AJAX
**Desde navegador (Console)**:
```javascript
// Test simulador de tarifas
fetch('/configuraciones-tarifas/calcular', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        tipo_cliente: 'residencial',
        consumo: 15
    })
})
.then(r => r.json())
.then(console.log);

// Test cálculo de boleta
fetch('/boletas/calcular-montos', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        id_socio: 1,  // Cambia por ID real
        consumo_m3: 25,
        mes: '2025-12'
    })
})
.then(r => r.json())
.then(console.log);
```

---

## 📊 DATOS DE EJEMPLO INSERTADOS

### Tarifa Residencial 2025
| Tramo | Rango | Monto | Cargo Fijo | IVA | Total c/IVA |
|-------|-------|-------|-----------|-----|-------------|
| 1 | 0-10 m³ | $5,000 | $2,500 | 19% | $5,950 |
| 2 | 11-20 m³ | $8,000 | $2,500 | 19% | $9,520 |
| 3 | 21-30 m³ | $12,000 | $2,500 | 19% | $14,280 |
| 4 | 31+ m³ | $18,000 | $2,500 | 19% | $21,420 |

### Tarifa Comercial 2025
| Tramo | Rango | Monto | Cargo Fijo | IVA | Total c/IVA |
|-------|-------|-------|-----------|-----|-------------|
| 1 | 0-15 m³ | $8,000 | $5,000 | 19% | $9,520 |
| 2 | 16-30 m³ | $15,000 | $5,000 | 19% | $17,850 |
| 3 | 31-50 m³ | $25,000 | $5,000 | 19% | $29,750 |
| 4 | 51+ m³ | $40,000 | $5,000 | 19% | $47,600 |

### Tarifa Industrial 2025
| Tramo | Rango | Monto | Cargo Fijo | IVA | Total c/IVA |
|-------|-------|-------|-----------|-----|-------------|
| 1 | 0-20 m³ | $12,000 | $8,000 | 19% | $14,280 |
| 2 | 21-40 m³ | $22,000 | $8,000 | 19% | $26,180 |
| 3 | 41-70 m³ | $38,000 | $8,000 | 19% | $45,220 |
| 4 | 71+ m³ | $60,000 | $8,000 | 19% | $71,400 |

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [x] Migración SQL de tabla creada
- [x] Migración SQL de procedimiento almacenado creada
- [x] Modelo ConfiguracionTarifa actualizado
- [x] ConfiguracionTarifasController actualizado
- [x] BoletasController actualizado con método calcular
- [x] Ruta AJAX agregada
- [ ] Ejecutar migraciones SQL en base de datos
- [ ] Actualizar vista index.blade.php
- [ ] Actualizar vista create.blade.php
- [ ] Actualizar vista edit.blade.php
- [ ] Actualizar simulador.blade.php
- [ ] Testing de cálculos
- [ ] Testing de generación masiva
- [ ] Registrar actividad en sistema

---

## 🚨 IMPORTANTE

1. **BACKUP OBLIGATORIO** antes de ejecutar migraciones SQL
2. **Verificar** que no hay boletas pendientes de generación con sistema antiguo
3. **Probar** en ambiente de desarrollo primero
4. **Comunicar** a usuarios el cambio de sistema tarifario
5. **Documentar** las nuevas tarifas para usuarios finales

---

## 📞 SOPORTE

Si encuentras errores durante la migración:

1. **Error en migración de tabla**: Verificar que no exista ya la columna `tipo_cliente`
   ```sql
   SHOW COLUMNS FROM configuraciones_tarifas WHERE Field = 'tipo_cliente';
   ```

2. **Error en procedimiento almacenado**: Verificar sintaxis SQL
   ```sql
   SHOW WARNINGS;
   ```

3. **Error en cálculos**: Verificar que hay tramos configurados
   ```sql
   SELECT COUNT(*) FROM configuraciones_tarifas WHERE activo = 1;
   ```

---

**Fecha de creación**: 2025-11-29
**Versión**: 2.0 - Sistema de Tramos por Tipo de Cliente
**Autor**: Sistema APR - Migración Automatizada
