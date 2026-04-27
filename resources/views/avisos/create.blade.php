@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff; min-height: 100vh;">
    
    <div class="mb-5 ps-4">
        <h1 class="fw-bold" style="font-size: 3rem; color: #000;">Nuevo Aviso</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mx-4 shadow-sm" style="border-radius: 15px; background-color: #f8d7da; color: #721c24;">
            <strong>Error:</strong> No se pudo publicar el aviso.
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('avisos.store') }}" method="POST" class="px-4">
                @csrf

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Autor del Aviso</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control shadow-sm bg-light" 
                               style="border-radius: 20px; padding: 12px 25px;" 
                               value="{{ Auth::user()->nombre }} ({{ Auth::user()->ID_rol == 3 ? 'Admin' : 'Instructor' }})" readonly>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Título</label>
                    <div class="col-md-8">
                        <input type="text" name="titulo" value="{{ old('titulo') }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" 
                               placeholder="Título del aviso" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Contenido del Aviso</label>
                    <div class="col-md-8">
                        <textarea name="contenido" class="form-control shadow-sm" 
                                  style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da; min-height: 120px;" 
                                  placeholder="Escribe el mensaje aquí..." required>{{ old('contenido') }}</textarea>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Fecha de Publicación</label>
                    <div class="col-md-8">
                        <input type="date" name="fecha_publicacion" value="{{ date('Y-m-d') }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" required>
                    </div>
                </div>

                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 text-center">
                        <button type="submit" class="btn btn-success w-100 fw-bold mb-3 shadow" 
                                style="background-color: #1e6d2a; border-radius: 15px; padding: 12px; font-size: 1.2rem;">
                            Publicar Aviso
                        </button>
                        <a href="{{ route('avisos.index') }}" class="btn btn-secondary w-100 fw-bold shadow-sm" 
                           style="background-color: #7bab7b; border: none; border-radius: 15px; padding: 12px; color: white;">
                            Regresar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection