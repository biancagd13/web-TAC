<h1>Registrar Validación de Constancia</h1>

@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px;">
        @foreach ($errors->all() as $error)
            <strong>{{ $error }}</strong><br>
        @endforeach
    </div>
@endif

<form action="{{ route('detalle_constancias.store') }}" method="POST" id="form-validacion">
    @csrf
    
    <label><strong>1. Seleccionar Constancia (Alumno y Taller):</strong></label><br>
    <select name="ID_constancia" id="ID_constancia" required style="width: 100%; padding: 8px; margin-top: 5px;">
        <option value="">-- Seleccione una constancia --</option>
        @foreach($constancias as $c)
            <option value="{{ $c->ID_constancia }}" {{ old('ID_constancia') == $c->ID_constancia ? 'selected' : '' }}>
                {{ $c->usuario->nombre }} - {{ $c->imparteTaller->taller->nombre }} (Folio: {{ $c->ID_constancia }})
            </option>
        @endforeach
    </select>
    <br><br>

    <label><strong>2. Código de Validación:</strong></label><br>
    <input type="text" name="codigo_validacion" id="codigo_validacion" value="{{ old('codigo_validacion') }}" 
           readonly style="width: 100%; padding: 8px; background-color: #eee; cursor: not-allowed;" 
           placeholder="Se generará automáticamente...">
    <br><br>

    <label><strong>3. Firma Digital (Hash de Seguridad):</strong></label><br>
    <textarea name="firma_digital" id="firma_digital" readonly 
              style="width: 100%; padding: 8px; background-color: #eee; cursor: not-allowed; height: 60px;" 
              placeholder="Se generará automáticamente...">{{ old('firma_digital') }}</textarea>
    <br><br>

    <label><strong>4. Fecha de Envío Email:</strong></label><br>
    <input type="datetime-local" name="fecha_envio_email" value="{{ old('fecha_envio_email', date('Y-m-d\TH:i')) }}" 
           required style="width: 100%; padding: 8px;"><br><br>

    <button type="submit" style="background-color: #004a2f; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
        Guardar Registro de Validación
    </button>
</form>

<br>
<a href="{{ route('detalle_constancias.index') }}">← Volver al listado</a>

{{-- SCRIPT DE AUTOMATIZACIÓN PARA ALEXA --}}
<script>
    document.getElementById('ID_constancia').addEventListener('change', function() {
        const idConstancia = this.value;
        if (idConstancia) {
            // Generamos un código único basado en el ID y la fecha
            const randomSuffix = Math.random().toString(36).substring(2, 7).toUpperCase();
            const codigo = `TAC-2026-${idConstancia}-${randomSuffix}`;
            
            // Generamos una "firma" simulada (puedes usar una real en el controlador)
            const firma = `SIG-UTVT-${btoa(codigo).substring(0, 20)}`;

            document.getElementById('codigo_validacion').value = codigo;
            document.getElementById('firma_digital').value = firma;
        } else {
            document.getElementById('codigo_validacion').value = '';
            document.getElementById('firma_digital').value = '';
        }
    });
</script>