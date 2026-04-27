@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header p-4 text-white" style="background: #1e6d2a;">
                    <h4 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;">Registrar Nueva Carrera</h4>
                </div>

                <div class="card-body p-4 bg-white">
                    <form action="{{ route('carreras.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre de la carrera:</label>
                            <input type="text" name="nombre" class="form-control rounded-3 shadow-none" value="{{ old('nombre') }}" placeholder="Ej. Desarrollo de Software" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Clave Institucional:</label>
                            <input type="text" name="clave" class="form-control rounded-3 shadow-none" value="{{ old('clave') }}" placeholder="Ej. DSM-53" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Detalles / Descripción:</label>
                            <textarea name="detalle" class="form-control rounded-3 shadow-none" rows="2" placeholder="Opcional...">{{ old('detalle') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Estado Inicial:</label>
                            <select name="activo" class="form-select rounded-3 shadow-none" required>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('carreras.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Regresar</a>
                            <button type="submit" class="btn text-white px-5 rounded-pill fw-bold shadow-sm" style="background: #1e6d2a;">Guardar Carrera</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection