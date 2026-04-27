<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Asistencia - TAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f4f6f9;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-warning text-dark fw-bold" style="border-radius: 15px 15px 0 0;">
                        <h5 class="mb-0">Editar Sesión #{{ $asistencia->ID_asistencia }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('asistencias.update', $asistencia->ID_asistencia) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-4">
                                <label class="form-label fw-bold">Taller Impartido:</label>
                                <select name="ID_imparte" class="form-select" required>
                                    @foreach($imparticiones as $im)
                                        <option value="{{ $im->ID_imparte }}" 
                                            {{ $asistencia->ID_imparte == $im->ID_imparte ? 'selected' : '' }}>
                                            {{ $im->usuario->nombre }} - {{ $im->taller->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('asistencias.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4">Actualizar Asistencia</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>