@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header p-4 text-white d-flex justify-content-between align-items-center" style="background: #1e6d2a;">
                    <div>
                        <h4 class="fw-bold mb-0">Pase de Lista Manual</h4>
                        <p class="mb-0 small opacity-75">Taller: {{ $asistencia->imparteTaller->taller->nombre }}</p>
                    </div>
                    <i class="bi bi-people-fill fs-1 opacity-25"></i>
                </div>
                
                <div class="card-body p-4 bg-white">
                    {{-- Buscador Dinámico --}}
                    <div class="input-group mb-4 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                        <span class="input-group-text bg-white border-end-0 px-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="alumnoSearch" class="form-control border-start-0 ps-0 shadow-none" placeholder="Buscar alumno por nombre o ID de usuario...">
                    </div>

                    <form action="{{ route('asistencias.guardar_manual', $asistencia->ID_asistencia) }}" method="POST">
                        @csrf
                        <div class="table-responsive rounded-3 border">
                            <table class="table table-hover align-middle mb-0" id="tablaAlumnos">
                                <thead class="bg-light text-uppercase small fw-bold">
                                    <tr>
                                        <th class="text-center py-3" width="100">Asistió</th>
                                        <th class="py-3">ID</th>
                                        <th class="py-3">Alumno</th>
                                        <th class="py-3">Carrera</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alumnos as $al)
                                    <tr class="alumno-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="alumnos_presentes[]" value="{{ $al->ID_usuario }}" 
                                                   class="form-check-input shadow-none" style="width: 1.4rem; height: 1.4rem; cursor: pointer;"
                                                   {{ in_array($al->ID_usuario, $presentesIds) ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-muted fw-bold small">#{{ $al->ID_usuario }}</td>
                                        <td class="fw-bold text-dark">{{ $al->usuario->nombre }}</td>
                                        <td class="small text-muted">{{ $al->carrera->nombre ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <a href="{{ route('asistencias.index') }}" class="btn btn-light rounded-pill px-4 border">Regresar</a>
                            <button type="submit" class="btn text-white px-5 rounded-pill fw-bold shadow" style="background: #1e6d2a;">
                                <i class="bi bi-save me-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Lógica del Buscador Dinámico
    document.getElementById('alumnoSearch').addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        const rows = document.querySelectorAll('.alumno-row');
        
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
</script>
@endsection