@extends('layouts.app')

@section('title', 'Nuevo Usuario - Sistema APR')

@section('content')
<div class="page-header">
    <h2 class="page-title">
        <i class="fas fa-user-plus"></i>
        Registrar Nuevo Usuario
    </h2>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Usuario</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <!-- Nombre de Usuario -->
                <div class="form-group col-md-4">
                    <label for="nombre_usuario" class="form-label required">Nombre de Usuario</label>
                    <input type="text"
                           class="form-control @error('nombre_usuario') is-invalid @enderror"
                           id="nombre_usuario"
                           name="nombre_usuario"
                           value="{{ old('nombre_usuario') }}"
                           placeholder="usuario123"
                           required>
                    @error('nombre_usuario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Nombre único para iniciar sesión</small>
                </div>

                <!-- Email -->
                <div class="form-group col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="correo@ejemplo.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Rol -->
                <div class="form-group col-md-4">
                    <label for="rol" class="form-label required">Rol</label>
                    <select class="form-control @error('rol') is-invalid @enderror"
                            id="rol"
                            name="rol"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="tesorero" {{ old('rol') == 'tesorero' ? 'selected' : '' }}>Tesorero</option>
                        <option value="operador" {{ old('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                        <option value="lecturista" {{ old('rol') == 'lecturista' ? 'selected' : '' }}>Lecturista</option>
                    </select>
                    @error('rol')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Nombre -->
                <div class="form-group col-md-4">
                    <label for="nombre" class="form-label required">Nombre</label>
                    <input type="text"
                           class="form-control @error('nombre') is-invalid @enderror"
                           id="nombre"
                           name="nombre"
                           value="{{ old('nombre') }}"
                           required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Apellido -->
                <div class="form-group col-md-4">
                    <label for="apellido" class="form-label required">Apellido</label>
                    <input type="text"
                           class="form-control @error('apellido') is-invalid @enderror"
                           id="apellido"
                           name="apellido"
                           value="{{ old('apellido') }}"
                           required>
                    @error('apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="form-group col-md-4">
                    <label for="activo" class="form-label required">Estado</label>
                    <select class="form-control @error('activo') is-invalid @enderror"
                            id="activo"
                            name="activo"
                            required>
                        <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('activo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Contraseña -->
                <div class="form-group col-md-4">
                    <label for="password" class="form-label required">Contraseña</label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Mínimo 6 caracteres</small>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="form-group col-md-4">
                    <label for="password_confirmation" class="form-label required">Confirmar Contraseña</label>
                    <input type="password"
                           class="form-control"
                           id="password_confirmation"
                           name="password_confirmation"
                           required>
                </div>
            </div>

            <!-- Permisos -->
            <div class="form-group">
                <label class="form-label">Permisos del Sistema</label>
                <div class="permissions-grid">
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="socios" {{ in_array('socios', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Socios</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="lecturas" {{ in_array('lecturas', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Lecturas</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="boletas" {{ in_array('boletas', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Boletas</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="pagos" {{ in_array('pagos', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Pagos</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="mantenciones" {{ in_array('mantenciones', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Mantenciones</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="incidentes" {{ in_array('incidentes', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Incidentes</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="reportes" {{ in_array('reportes', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Reportes</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="usuarios" {{ in_array('usuarios', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Usuarios</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="funcionarios" {{ in_array('funcionarios', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Funcionarios</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="sueldos" {{ in_array('sueldos', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Sueldos</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="cortes" {{ in_array('cortes', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Cortes de Suministro</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="trabajos" {{ in_array('trabajos', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Trabajos Realizados</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="renovaciones" {{ in_array('renovaciones', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Renovaciones de Medidores</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="vacaciones" {{ in_array('vacaciones', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Vacaciones</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="compras" {{ in_array('compras', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Compras</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="inventario" {{ in_array('inventario', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Inventario</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="tickets" {{ in_array('tickets', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Tickets</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="recordatorios" {{ in_array('recordatorios', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Recordatorios</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="movimientos_inventario" {{ in_array('movimientos_inventario', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Movimientos de Inventario</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="giros_bancarios" {{ in_array('giros_bancarios', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Giros Bancarios</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="directiva" {{ in_array('directiva', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Directiva</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="historial_consumo" {{ in_array('historial_consumo', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Historial de Consumo</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="historial_pagos" {{ in_array('historial_pagos', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Historial de Pagos</span>
                    </label>
                    <label class="permission-item">
                        <input type="checkbox" name="permisos[]" value="eventos" {{ in_array('eventos', old('permisos', [])) ? 'checked' : '' }}>
                        <span>Eventos</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .page-title i {
        color: var(--primary);
    }

    .card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius);
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 4px;
    }

    .form-text {
        color: var(--gray-500);
        font-size: 0.8rem;
        margin-top: 4px;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        padding: 20px;
        background: var(--gray-50);
        border-radius: var(--radius);
        border: 2px solid var(--gray-200);
        margin-top: 8px;
    }

    .permission-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 8px;
        border-radius: var(--radius);
        transition: background 0.2s;
    }

    .permission-item:hover {
        background: var(--white);
    }

    .permission-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .permission-item span {
        font-weight: 500;
        color: var(--gray-700);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-200);
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius);
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background: var(--gray-300);
    }

    select.form-control {
        cursor: pointer;
    }

    .col-md-4 {
        grid-column: span 1;
    }

    .col-md-8 {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .col-md-4,
        .col-md-8 {
            grid-column: span 1;
        }

        .permissions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
// Auto-asignar permisos según el rol
document.getElementById('rol').addEventListener('change', function() {
    const rol = this.value;
    const checkboxes = document.querySelectorAll('input[name="permisos[]"]');

    // Limpiar todos
    checkboxes.forEach(cb => cb.checked = false);

    if (rol === 'admin') {
        // Admin tiene todos los permisos
        checkboxes.forEach(cb => cb.checked = true);
    } else if (rol === 'tesorero') {
        // Tesorero: boletas, pagos, reportes
        ['boletas', 'pagos', 'reportes'].forEach(permiso => {
            const checkbox = document.querySelector(`input[value="${permiso}"]`);
            if (checkbox) checkbox.checked = true;
        });
    } else if (rol === 'operador') {
        // Operador: socios, lecturas, mantenciones, incidentes
        ['socios', 'lecturas', 'mantenciones', 'incidentes'].forEach(permiso => {
            const checkbox = document.querySelector(`input[value="${permiso}"]`);
            if (checkbox) checkbox.checked = true;
        });
    } else if (rol === 'lecturista') {
        // Lecturista: solo lecturas
        const checkbox = document.querySelector(`input[value="lecturas"]`);
        if (checkbox) checkbox.checked = true;
    }
});
</script>
@endsection
