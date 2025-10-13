<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>People · Firestore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:24px}
    nav a{margin-right:12px}
    table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:8px} th{background:#f6f8fa}
    input,select,button{padding:6px 8px;margin:4px 0}
  </style>
</head>
<body>
  <nav>
    <a href="{{ route('people.index') }}">Listado</a>
    <a href="{{ route('people.create') }}">Crear</a>
    <a href="{{ route('people.import.form') }}">Importar CSV</a>
  </nav>

  @if (session('status'))
    <div style="background:#e6ffed;border:1px solid #b7eb8f;padding:8px;margin:12px 0">{{ session('status') }}</div>
  @endif

  @yield('content')
</body>
</html>
