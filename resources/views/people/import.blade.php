@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Importar Personas desde CSV</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('import_summary'))
            @php($s = session('import_summary'))

            <div class="alert alert-info mt-3">
                <strong>Resumen de importación</strong><br>
                Archivo: <code>{{ $s['archivo'] }}</code><br>
                Éxitos: {{ $s['totales']['exitos'] }} |
                Omitidos: {{ $s['totales']['omitidos'] }} |
                Errores: {{ $s['totales']['errores'] }}
            </div>

            @if (!empty($s['errores']))
                <div class="card mt-2">
                    <div class="card-header">Detalle de errores</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach ($s['errores'] as $err)
                                <li>{{ $err }}</li>
                                @break($loop->iteration >= 50) {{-- limita a 50 para no saturar --}}
                            @endforeach
                        </ul>
                        @if (count($s['errores']) > 50)
                            <small class="text-muted">Se ocultaron {{ count($s['errores']) - 50 }} errores adicionales.</small>
                        @endif
                    </div>
                </div>
            @endif
        @endif


        <form method="POST" action="{{ route('people.import.process') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Seleccione el archivo CSV</label>
                <input type="file" name="file" class="form-control" accept=".csv" required>
                <div class="form-text">El archivo debe contener las columnas: identification, firstname, lastname, address, cellphone, email, gender, birthday, sex, status</div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <a href="{{ route('people.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload"></i> Cargar CSV
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
