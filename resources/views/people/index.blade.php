@extends('layouts.app')

@section('content')
  <h1>People (Firestore)</h1>

  <form action="{{ route('people.search') }}" method="get" style="margin:12px 0">
    <select name="field">
      <option value="identification">identification (igual)</option>
      <option value="email">email (igual)</option>
      <option value="search_index">texto (token)</option>
    </select>
    <input name="q" placeholder="valor a buscar">
    <button>Buscar</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Doc ID</th>
        <th>identification</th>
        <th>firstname</th>
        <th>lastname</th>
        <th>email</th>
        <th>acciones</th>
      </tr>
    </thead>
    <tbody>
    @forelse($items as $it)
      <tr>
        <td>{{ $it['_id'] }}</td>
        <td>{{ $it['identification'] ?? '' }}</td>
        <td>{{ $it['firstname'] ?? '' }}</td>
        <td>{{ $it['lastname'] ?? '' }}</td>
        <td>{{ $it['email'] ?? '' }}</td>
        <td>
          <a href="{{ route('people.edit', $it['_id']) }}">Editar</a>
          <form action="{{ route('people.destroy', $it['_id']) }}" method="post" style="display:inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('¿Eliminar?')">Eliminar</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6">Sin datos</td></tr>
    @endforelse
    </tbody>
  </table>
@endsection
