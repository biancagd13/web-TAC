<h1>Detalles de Asistencia - UTVT</h1>

@if(session('exito'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 15px;">
        <strong>¡Logrado!</strong> {{ session('exito') }}
    </div>
@endif

<a href="{{ route('detalle_asistencias.create') }}">Nueva Entrada</a>
<br><br>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>¿Entró?</th> 
        <th>Alumno</th>
        <th>Sesión ID</th> 
        <th>Acciones</th>
    </tr>
    @foreach($detalles as $d)
    <tr>
        <td>{{ $d->ID_detalle_asistencia }}</td>
        <td>{{ $d->fecha }}</td>
        <td>
            <strong>{{ $d->entro == '1' || $d->entro == '01:00:00' ? 'Sí' : 'No' }}</strong>
        </td>
        <td>{{ $d->usuario->nombre }}</td>
        <td>Sesión #{{ $d->ID_asistencia }}</td>
        <td>
            <a href="{{ route('detalle_asistencias.edit', $d->ID_detalle_asistencia) }}">Editar</a>
            <form action="{{ route('detalle_asistencias.destroy', $d->ID_detalle_asistencia) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">Eliminar</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>