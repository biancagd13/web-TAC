@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header p-4 text-white" style="background: #1e6d2a;">
                    <h4 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;">Editar Asignación de Taller</h4>
                </div>

                <div class="card-body p-4 bg-white">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 small mb-4">
                            <strong><i class="bi bi-exclamation-triangle me-2"></i>Error en la edición:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('imparte_taller.update', $imparte->ID_imparte) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Instructor Autorizado:</label>
                            <select name="ID_usuario" class="form-select rounded-3 shadow-none" required>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->ID_usuario }}" {{ $imparte->ID_usuario == $u->ID_usuario ? 'selected' : '' }}>{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Taller:</label>
                            <select name="ID_taller" class="form-select rounded-3 shadow-none" required>
                                @foreach($talleres as $t)
                                    <option value="{{ $t->ID_taller }}" {{ $imparte->ID_taller == $t->ID_taller ? 'selected' : '' }}>{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Periodo Escolar:</label>
                                <select name="periodo" class="form-select rounded-3 shadow-none" required>
                                    <option value="Ene-Abr 2026" {{ old('periodo', $imparte->periodo) == 'Ene-Abr 2026' ? 'selected' : '' }}>Ene-Abr 2026</option>
                                    <option value="May-Ago 2026" {{ old('periodo', $imparte->periodo) == 'May-Ago 2026' ? 'selected' : '' }}>May-Ago 2026</option>
                                    <option value="Sep-Dic 2026" {{ old('periodo', $imparte->periodo) == 'Sep-Dic 2026' ? 'selected' : '' }}>Sep-Dic 2026</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Fecha Asignación:</label>
                                <input type="date" name="fecha" class="form-control rounded-3 shadow-none" value="{{ old('fecha', $imparte->fecha) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Estado de la Asignación:</label>
                            <select name="activo" class="form-select rounded-3 shadow-none" required>
                                <option value="1" {{ $imparte->activo == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ $imparte->activo == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a href="{{ route('imparte_taller.index') }}" class="btn btn-light rounded-pill px-4 fw-bold text-muted">Cancelar</a>
                            <button type="submit" class="btn text-white px-5 rounded-pill fw-bold shadow-sm" style="background: #1e6d2a;">Actualizar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection