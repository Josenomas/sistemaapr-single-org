@extends('layouts.superadmin')

@section('title', 'Gestión de Reclamos - Sistema APR')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-book text-danger"></i>
                        Libro de Reclamos
                    </h1>
                    <p class="text-muted mb-0">Gestión de reclamos según Ley 19.496</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h3 class="mb-0">{{ $reclamos->where('estado', 'pendiente')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-eye text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">En Revisión</h6>
                            <h3 class="mb-0">{{ $reclamos->where('estado', 'en_revision')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Resueltos</h6>
                            <h3 class="mb-0">{{ $reclamos->where('estado', 'resuelto')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-times-circle text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Rechazados</h6>
                            <h3 class="mb-0">{{ $reclamos->where('estado', 'rechazado')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Reclamos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Reclamante</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Plazo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reclamos as $reclamo)
                        <tr>
                            <td>
                                <strong class="text-danger">{{ $reclamo->numero_reclamo }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $reclamo->nombre_completo }}</strong><br>
                                    <small class="text-muted">{{ $reclamo->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $reclamo->tipo_reclamo_nombre }}</span>
                            </td>
                            <td>
                                <small>{{ $reclamo->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                @if($reclamo->estado === 'pendiente')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock"></i> Pendiente
                                    </span>
                                @elseif($reclamo->estado === 'en_revision')
                                    <span class="badge bg-info">
                                        <i class="fas fa-eye"></i> En Revisión
                                    </span>
                                @elseif($reclamo->estado === 'resuelto')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Resuelto
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i> Rechazado
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($reclamo->estado === 'pendiente' || $reclamo->estado === 'en_revision')
                                    @php
                                        $diasTranscurridos = $reclamo->created_at->diffInDays(now());
                                        $diasRestantes = 5 - $diasTranscurridos;
                                    @endphp
                                    @if($diasRestantes > 2)
                                        <span class="badge bg-success">{{ $diasRestantes }} días</span>
                                    @elseif($diasRestantes > 0)
                                        <span class="badge bg-warning text-dark">{{ $diasRestantes }} días</span>
                                    @else
                                        <span class="badge bg-danger">¡Vencido!</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('reclamos.show', $reclamo->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No hay reclamos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $reclamos->links() }}
            </div>
        </div>
    </div>

    <!-- Aviso Legal -->
    <div class="alert alert-warning mt-4" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Importante:</strong> Según la Ley 19.496 Art. 17D, debes responder los reclamos dentro de 5 días hábiles.
        Las multas por incumplimiento pueden alcanzar hasta <strong>50 UTM (aprox. $3.000.000 CLP)</strong>.
    </div>
</div>
@endsection
