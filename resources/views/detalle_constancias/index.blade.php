<h1>Detalles de Constancias - SISTEMA TAC</h1>

@if(session('exito'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 15px;">
        <strong>¡Éxito!</strong> {{ session('exito') }}
    </div>
@endif

<a href="{{ route('detalle_constancias.create') }}">Nuevo Detalle de Validación</a>
<br><br>

<table border="1">
    <tr>
        <th>ID Detalle</th>
        <th>Código de Validación</th>
        <th>Alumno / Taller</th>
        <th>Fecha de Envío</th>
        <th>Acciones</th>
    </tr>
    @foreach($detalles as $d)
    <tr>
        <td>{{ $d->ID_detalle_constancia }}</td>
        <td><code>{{ $d->codigo_validacion }}</code></td>
        <td>
            {{ $d->constancia->usuario->nombre }} <br>
            <small>{{ $d->constancia->imparteTaller->taller->nombre }}</small>
        </td>
        <td>{{ $d->fecha_envio_email ?? 'No enviado' }}</td>
        <td>
            <a href="{{ route('detalle_constancias.edit', $d->ID_detalle_constancia) }}">Editar</a>
            <form action="{{ route('detalle_constancias.destroy', $d->ID_detalle_constancia) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Eliminar esta validación?')">Eliminar</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>