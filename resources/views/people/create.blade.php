@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Crear Persona</h5>
        <a href="{{ route('people.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Volver al listado
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ups...</strong> Hay errores en el formulario:<br>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('people.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Identificación</label>
                    <input type="text" name="identification" class="form-control" 
                           value="{{ old('identification') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="firstname" class="form-control"
                           value="{{ old('firstname') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="lastname" class="form-control"
                           value="{{ old('lastname') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control"
                           value="{{ old('address') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cellphone" class="form-control"
                           value="{{ old('cellphone') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Género</label>
                    <select name="gender" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ old('gender') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="Otro" {{ old('gender') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="birthday" class="form-control"
                           value="{{ old('birthday') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sexo</label>
                    <select name="sex" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Hombre" {{ old('sex') == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="Mujer" {{ old('sex') == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Activo" {{ old('status') == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ old('status') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Crear Persona
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
