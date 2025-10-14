@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Editar Persona</h5>
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

        <form method="POST" action="{{ route('people.update', $id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Identificación</label>
                    <input type="text" name="identification" class="form-control" 
                           value="{{ old('identification', $person['identification'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="firstname" class="form-control"
                           value="{{ old('firstname', $person['firstname'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="lastname" class="form-control"
                           value="{{ old('lastname', $person['lastname'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="address" class="form-control"
                           value="{{ old('address', $person['address'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cellphone" class="form-control"
                           value="{{ old('cellphone', $person['cellphone'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $person['email'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Género</label>
                    <select name="gender" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Masculino" {{ (old('gender', $person['gender'] ?? '') == 'Masculino') ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ (old('gender', $person['gender'] ?? '') == 'Femenino') ? 'selected' : '' }}>Femenino</option>
                        <option value="Otro" {{ (old('gender', $person['gender'] ?? '') == 'Otro') ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="birthday" class="form-control"
                           value="{{ old('birthday', $person['birthday'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sexo</label>
                    <select name="sex" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Hombre" {{ (old('sex', $person['sex'] ?? '') == 'Hombre') ? 'selected' : '' }}>Hombre</option>
                        <option value="Mujer" {{ (old('sex', $person['sex'] ?? '') == 'Mujer') ? 'selected' : '' }}>Mujer</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Activo" {{ (old('status', $person['status'] ?? '') == 'Activo') ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ (old('status', $person['status'] ?? '') == 'Inactivo') ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
