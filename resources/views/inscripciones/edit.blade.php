@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7 col-12">
            <h1 class="fw-bold text-dark mb-5 ms-md-5 ps-md-4">Editar Inscripción</h1>

            <div class="card border-0 bg-transparent ps-md-5">
                <form action="{{ route('inscripciones.update', $inscripcion->ID_inscripción) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Periodo escolar</label>
                        <div class="col-md-7">
                            <input type="text" name="periodo" class="form-control rounded-pill border-light bg-white shadow-sm py-2 px-4" 
                                   value="{{ $inscripcion->periodo }}" required>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Fecha de registro</label>
                        <div class="col-md-7">
                            <input type="date" name="fecha" class="form-control rounded-pill border-light bg-white shadow-sm py-2 px-4" 
                                   value="{{ $inscripcion->fecha }}" required>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Alumno</label>
                        <div class="col-md-7">
                            <select name="ID_usuario" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->ID_usuario }}" {{ $inscripcion->ID_usuario == $u->ID_usuario ? 'selected' : '' }}>
                                        {{ $u->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Taller asignado</label>
                        <div class="col-md-7">
                            <select name="ID_taller" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                @foreach($talleres as $t)
                                    <option value="{{ $t->ID_taller }}" {{ $inscripcion->ID_taller == $t->ID_taller ? 'selected' : '' }}>
                                        {{ $t->nombre }}
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
                            <a href="{{ route('inscripciones.index') }}" class="btn btn-success rounded-3 fw-bold py-2 shadow-sm opacity-50" style="background-color: #1e6d2a; border: none;">
                                Regresar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        border-width: 1px;
        font-size: 1rem;
        color: #555;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1e6d2a;
        box-shadow: 0 0 0 0.25rem rgba(30, 109, 42, 0.1);
    }
    ::placeholder { color: #ccc !important; font-size: 0.9rem; }
</style>
@endsection