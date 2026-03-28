<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $noticia->titulo }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .noticia-imagen-principal {
            max-height: 500px;
            object-fit: cover;
            width: 100%;
            border-radius: 10px 10px 0 0;
        }
        .noticia-contenido {
            white-space: pre-wrap;
            line-height: 1.8;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-4">
            <a href="{{ route('noticias.publicas') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Volver a Noticias
            </a>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <article class="card shadow-lg">
                    @if($noticia->imagen_destacada)
                    <img src="{{ asset('storage/' . $noticia->imagen_destacada) }}"
                         alt="{{ $noticia->titulo }}"
                         class="noticia-imagen-principal">
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            @if($noticia->categoria)
                            <span class="badge bg-info fs-6 me-2">{{ $noticia->categoria }}</span>
                            @endif

                            @if($noticia->destacada)
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-star"></i> Destacada
                            </span>
                            @endif
                        </div>

                        <h1 class="display-5 mb-4">{{ $noticia->titulo }}</h1>

                        <div class="d-flex gap-4 mb-4 text-muted">
                            <span>
                                <i class="fas fa-calendar"></i>
                                {{ $noticia->fecha_publicacion->format('d \d\e F \d\e Y') }}
                            </span>
                            <span>
                                <i class="fas fa-eye"></i>
                                {{ $noticia->vistas ?? 0 }} vistas
                            </span>
                        </div>

                        @if($noticia->resumen)
                        <div class="alert alert-light border-start border-primary border-4 mb-4">
                            <p class="mb-0 fw-bold fs-5">{{ $noticia->resumen }}</p>
                        </div>
                        @endif

                        <div class="noticia-contenido">
                            {{ $noticia->contenido }}
                        </div>
                    </div>

                    <div class="card-footer bg-light p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0 small">
                                    Publicado el {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="compartirFacebook()">
                                        <i class="fab fa-facebook"></i> Compartir
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="compartirWhatsApp()">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <div class="text-center mt-4">
                    <a href="{{ route('noticias.publicas') }}" class="btn btn-light">
                        <i class="fas fa-newspaper"></i> Ver más noticias
                    </a>
                    <a href="{{ route('landing') }}" class="btn btn-light">
                        <i class="fas fa-home"></i> Ir al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function compartirFacebook() {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank');
        }

        function compartirWhatsApp() {
            window.open('https://wa.me/?text=' + encodeURIComponent('{{ $noticia->titulo }} - ' + window.location.href), '_blank');
        }
    </script>
</body>
</html>
