@extends('layouts.app')

@section('content')
  <h1>{{ $data ? 'Editar' : 'Crear' }} registro</h1>

  <form action="{{ $data ? route('people.update', $data['_id']) : route('people.store') }}" method="post">
    @csrf
    @if($data) @method('PUT') @endif

    @php
      $v = fn($k) => old($k, $data[$k] ?? '');
    @endphp

    <div><label>identification</label><br><input name="identification" value="{{ $v('identification') }}"></div>
    <div><label>firstname</label><br><input name="firstname" value="{{ $v('firstname') }}"></div>
    <div><label>lastname</label><br><input name="lastname" value="{{ $v('lastname') }}"></div>
    <div><label>address</label><br><input name="address" value="{{ $v('address') }}"></div>
    <div><label>cellphone</label><br><input name="cellphone" value="{{ $v('cellphone') }}"></div>
    <div><label>email</label><br><input type="email" name="email" value="{{ $v('email') }}"></div>
    <div><label>gender</label><br><input name="gender" value="{{ $v('gender') }}"></div>
    <div><label>birthday</label><br><input name="birthday" placeholder="YYYY-MM-DD" value="{{ $v('birthday') }}"></div>
    <div><label>sex</label><br><input name="sex" value="{{ $v('sex') }}"></div>
    <div><label>status</label><br><input name="status" value="{{ $v('status') }}"></div>

    <button>{{ $data ? 'Actualizar' : 'Guardar' }}</button>
  </form>

  @if ($errors->any())
    <div style="color:#b00020;margin-top:8px">
      @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
    </div>
  @endif
@endsection
