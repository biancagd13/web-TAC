<h1>Editar Rol</h1>

@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px;">
        <strong>Error en la edición:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('roles.update', $rol->ID_rol) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Nombre del Rol:</label><br>
    <input type="text" name="nombre" value="{{ old('nombre', $rol->nombre) }}" required>
    <br><br>
    <button type="submit">Actualizar Rol</button>
</form>
<br>
<a href="{{ route('roles.index') }}">Cancelar</a>