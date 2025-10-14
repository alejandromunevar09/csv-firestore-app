<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CSV Firestore App') }}</title>

    {{-- Estilos Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Iconos opcionales (Bootstrap Icons) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">CSV Firestore App</a>

            <div>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('people.index') }}">Listado</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('people.create') }}">Crear</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('people.import.form') }}">Importar CSV</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        {{-- Contenido dinámico de cada vista --}}
        @yield('content')
    </main>

    {{-- Scripts Bootstrap (JS + Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
