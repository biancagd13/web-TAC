@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff; min-height: 100vh;">
    
    <div class="mb-5 ps-4">
        <h1 class="fw-bold" style="font-size: 3rem; color: #000;">Editar Aviso</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mx-4 shadow-sm" style="border-radius: 15px; border: none; background-color: #f8d7da; color: #721c24;">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-pencil-square fs-4 me-3"></i>
                <strong>Error en la edición del aviso.</strong>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('avisos.update', $aviso->ID_aviso) }}" method="POST" class="px-4">
                @csrf
                @method('PUT')

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Título</label>
                    <div class="col-md-8">
                        <input type="text" name="titulo" value="{{ old('titulo', $aviso->titulo) }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Contenido del Aviso</label>
                    <div class="col-md-8">
                        <textarea name="contenido" class="form-control shadow-sm" 
                                  style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da; min-height: 120px;" required>{{ old('contenido', $aviso->contenido) }}</textarea>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Fecha de Publicación</label>
                    <div class="col-md-8">
                        <input type="date" name="fecha_publicacion" value="{{ old('fecha_publicacion', $aviso->fecha_publicacion) }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" required>
                    </div>
                </div>

                <div class="row mb-5 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Cambiar Autor</label>
                    <div class="col-md-8">
                        <select name="ID_usuario" class="form-select shadow-sm" 
                                style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" required>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->ID_usuario }}" {{ $aviso->ID_usuario == $u->ID_usuario ? 'selected' : '' }}>
                                    {{ $u->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 text-center">
                        <button type="submit" class="btn btn-success w-100 fw-bold mb-3 shadow" 
                                style="background-color: #1e6d2a; border: none; border-radius: 15px; padding: 12px; font-size: 1.2rem;">
                            Actualizar Aviso
                        </button>
                        <a href="{{ route('avisos.index') }}" class="btn btn-secondary w-100 fw-bold shadow-sm" 
                           style="background-color: #7bab7b; border: none; border-radius: 15px; padding: 12px; font-size: 1.2rem; color: white;">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection