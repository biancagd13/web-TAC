@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header p-4 text-white" style="background: #1e6d2a;">
                    <h4 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;">Editar Carrera</h4>
                </div>

                <div class="card-body p-4 bg-white">
                    <form action="{{ route('carreras.update', $carrera) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre:</label>
                            <input type="text" name="nombre" class="form-control rounded-3 shadow-none" value="{{ old('nombre', $carrera->nombre) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Clave:</label>
                            <input type="text" name="clave" class="form-control rounded-3 shadow-none" value="{{ old('clave', $carrera->clave) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Detalles:</label>
                            <textarea name="detalle" class="form-control rounded-3 shadow-none" rows="2">{{ old('detalle', $carrera->detalle) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Estado:</label>
                            <select name="activo" class="form-select rounded-3 shadow-none" required>
                                <option value="1" {{ $carrera->activo == 1 ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ $carrera->activo == 0 ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('carreras.index') }}" class="btn btn-light rounded-pill px-4 fw-bold text-muted">Cancelar</a>
                            <button type="submit" class="btn text-white px-5 rounded-pill fw-bold shadow-sm" style="background: #1e6d2a;">Actualizar Carrera</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection