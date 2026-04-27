<h1>Editar Detalle de Asistencia</h1>

<form action="{{ route('detalle_asistencias.update', $detalle->ID_detalle_asistencia) }}" method="POST">
    @csrf
    @method('PUT')
    
    <label>Fecha:</label><br>
    <input type="date" name="fecha" value="{{ $detalle->fecha }}" required><br><br>

    <label>¿Entró?:</label><br>
    <select name="entro" required>
        <option value="01:00" {{ $detalle->entro == '01:00:00' ? 'selected' : '' }}>Sí</option>
        <option value="00:00" {{ $detalle->entro == '00:00:00' ? 'selected' : '' }}>No</option>
    </select><br><br>

    <label>Cambiar Alumno:</label><br>
    <select name="ID_usuario" required>
        @foreach($usuarios as $u)
            <option value="{{ $u->ID_usuario }}" {{ $detalle->ID_usuario == $u->ID_usuario ? 'selected' : '' }}>
                {{ $u->nombre }}
            </option>
        @endforeach
    </select><br><br>

    <label>Cambiar Sesión:</label><br>
    <select name="ID_asistencia" required>
        @foreach($asistencias as $a)
            <option value="{{ $a->ID_asistencia }}" {{ $detalle->ID_asistencia == $a->ID_asistencia ? 'selected' : '' }}>
                Sesión #{{ $a->ID_asistencia }}
            </option>
        @endforeach
    </select><br><br>

    <button type="submit">Actualizar Datos</button>
</form>
<br>
<a href="{{ route('detalle_asistencias.index') }}">Cancelar</a>