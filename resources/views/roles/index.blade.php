<h1>Listado de Roles - UTVT</h1>

@if (session('exito'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 15px;">
        <strong>¡Éxito!</strong> {{ session('exito') }}
    </div>
@endif

@if (session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px;">
        <strong>Error:</strong> {{ session('error') }}
    </div>
@endif

<a href="{{ route('roles.create') }}">Nuevo Rol</a>
<br><br>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre del Rol</th>
        <th>Acciones</th>
    </tr>
    @foreach($roles as $rol)
    <tr>
        <td>{{ $rol->ID_rol }}</td>
        <td>{{ $rol->nombre }}</td>
        <td>
            <a href="{{ route('roles.edit', $rol->ID_rol) }}">Editar</a>
            
            <form action="{{ route('roles.destroy', $rol->ID_rol) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este rol?')">Eliminar</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>