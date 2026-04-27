@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff; min-height: 100vh;">
    
    <div class="mb-5 ps-4">
        <h1 class="fw-bold" style="font-size: 3rem; color: #000;">Registrar Nuevo Usuario</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mx-4 shadow-sm" style="border-radius: 15px; border: none; background-color: #f8d7da; color: #721c24;">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
                <strong>¡Atención! Revisa los datos:</strong>
            </div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('usuarios.store') }}" method="POST" class="px-4">
                @csrf

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Foto de Perfil</label>
                    <div class="col-md-8 text-center">
                        <div class="mb-3">
                            <img id="preview" src="https://via.placeholder.com/150" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #1e6d2a;">
                        </div>
                        <input type="file" id="foto_input" class="form-control" accept="image/*" style="border-radius: 20px;">
                        <input type="hidden" name="foto_perfil" id="foto_base64">
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Nombre completo</label>
                    <div class="col-md-8">
                        <input type="text" name="nombre" value="{{ old('nombre') }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" 
                               placeholder="Nombre completo" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Teléfono (WhatsApp)</label>
                    <div class="col-md-8">
                        <input type="text" name="telefono" value="{{ old('telefono') }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" 
                               placeholder="Ej: 527221234567" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Correo institucional</label>
                    <div class="col-md-8">
                        <input type="email" name="correo" value="{{ old('correo') }}" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" 
                               placeholder="ejemplo@utvt.edu.mx" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Contraseña</label>
                    <div class="col-md-8">
                        <input type="password" name="password" 
                               class="form-control shadow-sm" 
                               style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" 
                               placeholder="Mínimo 8 caracteres" required>
                    </div>
                </div>

                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Rol</label>
                    <div class="col-md-8">
                        <select name="ID_rol" class="form-select shadow-sm" 
                                style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" required>
                            <option value="">Seleccione un rol...</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->ID_rol }}" {{ old('ID_rol') == $rol->ID_rol ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-5 align-items-center">
                    <label class="col-md-4 fw-bold fs-5 text-end pe-5">Estado</label>
                    <div class="col-md-8">
                        <select name="activo" class="form-select shadow-sm" 
                                style="border-radius: 20px; padding: 12px 25px; border: 1px solid #ced4da;" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 text-center">
                        <button type="submit" class="btn btn-success w-100 fw-bold mb-3 shadow" 
                                style="background-color: #1e6d2a; border: none; border-radius: 15px; padding: 12px; font-size: 1.2rem;">
                            Guardar Usuario
                        </button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary w-100 fw-bold shadow-sm" 
                           style="background-color: #7bab7b; border: none; border-radius: 15px; padding: 12px; font-size: 1.2rem; color: white;">
                            Regresar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('foto_input').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('foto_base64').value = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection