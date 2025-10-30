<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aplicación')</title>
    <!-- Bootstrap CSS CDN (versión estable) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f6f8fa; }
        .app-header { background: linear-gradient(90deg,#0d6efd 0%, #6610f2 100%); color: #fff; }
        .card-shadow { box-shadow: 0 6px 18px rgba(13,110,253,0.08); }
        /* Normalizar paginación y iconos para evitar flechas gigantes */
        .pagination { margin: 0; }
        .pagination .page-link, .pagination .page-item { font-size: 0.9rem; }
        /* Si la paginación incluye SVG/iconos, limitar su tamaño */
        .pagination .page-link svg, .pagination .page-link i { width: 1em; height: 1em; font-size: 1em; }
        /* Asegurar que los iconos de bootstrap no crezcan por herencia */
        .bi { vertical-align: -.125em; }
    </style>
</head>
<body>
    <header class="app-header py-3 mb-4">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="text-white text-decoration-none d-flex align-items-center" href="/">
                <i class="bi bi-people-fill fs-3 me-2"></i>
                <span class="fs-4 fw-semibold">Mi App - Empleados</span>
            </a>
            <nav>
                <a class="text-white me-3 text-decoration-none" href="{{ route('empleados.index') }}">Empleados</a>
            </nav>
        </div>
    </header>

    <main class="container mb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-muted py-4 border-top">
        <div class="container text-center small">
            &copy; {{ date('Y') }} Mi App — Gestión de empleados
        </div>
    </footer>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
