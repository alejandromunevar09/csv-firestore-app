@extends('layouts.app')

@section('content')
  <h1>Importar CSV → Firestore</h1>

  <form action="{{ route('people.import.process') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div>
      <label>Archivo CSV</label><br>
      <input type="file" name="file" required>
      <small>Encabezados esperados: identification, firstname, lastname, address, cellphone, email, gender, birthday, sex, status</small>
    </div>
    <div>
      <label>Delimitador</label><br>
      <select name="delimiter">
        <option value="comma">, (coma)</option>
        <option value="semicolon">; (punto y coma)</option>
        <option value="tab">Tab</option>
      </select>
    </div>
    <button>Importar</button>
  </form>

  @if ($errors->any())
    <div style="color:#b00020;margin-top:8px">
      @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
    </div>
  @endif
@endsection
