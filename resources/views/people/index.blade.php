@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de Personas</h5>
        <a href="{{ route('people.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(isset($people) && count($people))
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Identificación</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Género</th>
                        <th>Fecha Nac.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($people as $id => $person)
                        <tr>
                            <td>{{ $person['identification'] ?? '' }}</td>
                            <td>{{ $person['firstname'] ?? '' }}</td>
                            <td>{{ $person['lastname'] ?? '' }}</td>
                            <td>{{ $person['cellphone'] ?? '' }}</td>
                            <td>{{ $person['email'] ?? '' }}</td>
                            <td>{{ $person['gender'] ?? '' }}</td>
                            <td>{{ $person['birthday'] ?? '' }}</td>
                            <td>
                                <a href="{{ route('people.edit', $id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('people.destroy', $id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar registro?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(isset($next) && $next)
                <div class="d-flex justify-content-end mt-3">
                    <a class="btn btn-outline-primary"
                       href="{{ route('people.index', ['after' => $next, 'limit' => $limit]) }}">
                        Siguiente →
                    </a>
                </div>
            @else
                <div class="text-muted mt-3">No hay más resultados.</div>
            @endif
        @else
            <p class="text-muted">No hay personas registradas.</p>
        @endif
    </div>
</div>
@endsection
