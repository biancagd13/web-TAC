<h1>Nuevo Rol</h1>

@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px;">
        <strong>¡Atención!</strong> Por favor corrige lo siguiente:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('roles.store') }}" method="POST">
    @csrf
    <label>Nombre del Rol:</label><br>
    <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Estudiante" required>
    <br><br>
    <button type="submit">Guardar Rol</button>
</form>
<br>
<a href="{{ route('roles.index') }}">Volver al listado</a>