<h1>Editar Validación de Constancia</h1>

<form action="{{ route('detalle_constancias.update', $detalle->ID_detalle_constancia) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Constancia (Alumno y Taller):</label><br>
    <select name="ID_constancia" required>
        @foreach($constancias as $c)
            <option value="{{ $c->ID_constancia }}" 
                {{ $detalle->ID_constancia == $c->ID_constancia ? 'selected' : '' }}>
                {{ $c->usuario->nombre }} - {{ $c->imparteTaller->taller->nombre }} (ID: {{ $c->ID_constancia }})
            </option>
        @endforeach
    </select><br><br>

    <label>Código de Validación:</label><br>
    <input type="text" name="codigo_validacion" value="{{ $detalle->codigo_validacion }}" required><br><br>

    <label>Firma Digital:</label><br>
    <textarea name="firma_digital" required>{{ $detalle->firma_digital }}</textarea><br><br>

    <label>Fecha de Envío Email:</label><br>
    <input type="datetime-local" name="fecha_envio_email" 
        value="{{ $detalle->fecha_envio_email ? date('Y-m-d\TH:i', strtotime($detalle->fecha_envio_email)) : '' }}" required><br><br>

    <button type="submit">Actualizar Validación</button>
</form>