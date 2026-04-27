@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7 col-12">
            <h1 class="fw-bold text-dark mb-5 ms-md-5 ps-md-4">Editar Constancia</h1>

            <div class="card border-0 bg-transparent ps-md-5">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4 mx-md-5">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('constancias.update', $constancia->ID_constancia) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Fecha de emisión</label>
                        <div class="col-md-7">
                            <input type="date" name="fecha_emision" 
                                   class="form-control rounded-pill border-light bg-white shadow-sm py-2 px-4" 
                                   value="{{ old('fecha_emision', $constancia->fecha_emision) }}" required>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Alumno</label>
                        <div class="col-md-7">
                            <select name="ID_usuario" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                <option value="">Seleccione un alumno</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->ID_usuario }}" {{ $constancia->ID_usuario == $u->ID_usuario ? 'selected' : '' }}>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Taller e instructor</label>
                        <div class="col-md-7">
                            <select name="ID_imparte" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                <option value="">Seleccione taller</option>
                                @foreach($imparticiones as $im)
                                    <option value="{{ $im->ID_imparte }}" {{ $constancia->ID_imparte == $im->ID_imparte ? 'selected' : '' }}>
                                        {{ $im->taller->nombre }} - ({{ $im->usuario->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-5 justify-content-center">
                        <div class="col-md-5 d-grid gap-3 ps-md-5">
                            <button type="submit" class="btn btn-success rounded-3 fw-bold py-2 shadow-sm" style="background-color: #1e6d2a; border: none;">
                                Guardar Cambios
                            </button>
                            <a href="{{ route('constancias.index') }}" class="btn btn-success rounded-3 fw-bold py-2 shadow-sm opacity-50" style="background-color: #1e6d2a; border: none;">
                                Regresar
                            </a>
                        </div>
                    </div>

                </form>
            </div>
            
            <div class="text-center mt-5">
                <p class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> 
                    Los cambios realizados se aplicarán al documento PDF descargable.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados para igualar tu captura */
    .form-control, .form-select {
        border-width: 1px;
        font-size: 1rem;
        color: #555;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1e6d2a;
        box-shadow: 0 0 0 0.25rem rgba(30, 109, 42, 0.1);
        background-color: #fff;
    }
    .btn-success:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
    }
</style>
@endsection