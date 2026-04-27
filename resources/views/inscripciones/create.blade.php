@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7 col-12">
            <h1 class="fw-bold text-dark mb-5 ms-md-5 ps-md-4">Nueva Inscripción</h1>

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

                <form action="{{ route('inscripciones.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Periodo Escolar</label>
                        <div class="col-md-7">
                            <input type="text" name="periodo" class="form-control rounded-pill border-light bg-white shadow-sm py-2 px-4" 
                                   value="{{ old('periodo') }}" placeholder="Ej. Enero-Abril 2026" required>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Fecha de Registro</label>
                        <div class="col-md-7">
                            <input type="date" name="fecha" class="form-control rounded-pill border-light bg-white shadow-sm py-2 px-4" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Seleccionar Alumno</label>
                        <div class="col-md-7">
                            <select name="ID_usuario" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                <option value="">-- Seleccione Alumno --</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->ID_usuario }}">{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Seleccionar Carrera</label>
                        <div class="col-md-7">
                            <select name="ID_carrera" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                <option value="">-- Seleccione Carrera --</option>
                                @foreach($carreras as $c)
                                    <option value="{{ $c->ID_carrera }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center text-md-end">
                        <label class="col-md-4 col-form-label fw-bold text-dark h5 mb-0">Seleccionar Taller</label>
                        <div class="col-md-7">
                            <select name="ID_taller" class="form-select rounded-pill border-light bg-white shadow-sm py-2 px-4" required>
                                <option value="">-- Seleccione Taller --</option>
                                @foreach($talleres as $t)
                                    <option value="{{ $t->ID_taller }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-5 justify-content-center">
                        <div class="col-md-5 d-grid gap-3 ps-md-5">
                            <button type="submit" class="btn btn-success rounded-3 fw-bold py-2 shadow-sm" style="background-color: #1e6d2a; border: none;">
                                Guardar Inscripción
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
@endsection