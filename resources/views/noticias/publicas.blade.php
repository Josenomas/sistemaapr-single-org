<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .noticia-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .noticia-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .noticia-imagen {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center text-white mb-5">
            <h1 class="display-4 mb-3">📰 Noticias</h1>
            <p class="lead">Mantente informado con las últimas novedades</p>
        </div>

        @if($noticias->count() > 0)
        <div class="row g-4">
            @foreach($noticias as $noticia)
            <div class="col-md-6 col-lg-4">
                <div class="card noticia-card">
                    @if($noticia->imagen_destacada)
                    <img src="{{ asset('storage/' . $noticia->imagen_destacada) }}"
                         class="card-img-top noticia-imagen"
                         alt="{{ $noticia->titulo }}">
                    @else
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center noticia-imagen">
                        <i class="fas fa-newspaper fa-3x"></i>
                    </div>
                    @endif

                    <div class="card-body">
                        @if($noticia->categoria)
                        <span class="badge bg-info mb-2">{{ $noticia->categoria }}</span>
                        @endif

                        @if($noticia->destacada)
                        <span class="badge bg-warning text-dark mb-2">
                            <i class="fas fa-star"></i> Destacada
                        </span>
                        @endif

                        <h5 class="card-title">{{ $noticia->titulo }}</h5>

                        @if($noticia->resumen)
                        <p class="card-text text-muted">{{ Str::limit($noticia->resumen, 120) }}</p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i>
                                {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-eye"></i>
                                {{ $noticia->vistas ?? 0 }}
                            </small>
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <a href="{{ route('noticia.publica', [$organizacion->slug, $noticia->slug]) }}"
                           class="btn btn-primary btn-sm w-100">
                            Leer más <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $noticias->links() }}
        </div>
        @else
        <div class="text-center text-white py-5">
            <i class="fas fa-newspaper fa-5x mb-4 opacity-50"></i>
            <h3>No hay noticias publicadas</h3>
            <p>Vuelve pronto para ver las últimas actualizaciones</p>
        </div>
        @endif

        <div class="text-center mt-5">
            <a href="{{ route('landing') }}" class="btn btn-light">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
