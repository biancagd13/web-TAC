@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff;">
    
    <div class="mb-4">
        <h1 class="fw-bold mb-0" style="font-size: 2.5rem; color: #000;">Gestión de Usuarios</h1>
        <p class="text-muted fs-5">Administra todos los miembros del Sistema TAC</p>
    </div>

    @if (session('exito'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px; background-color: #d4edda; color: #155724;">
            <strong><i class="bi bi-check-circle-fill me-2"></i>¡Éxito!</strong> {{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="p-4 rounded-4 mb-4 shadow-sm" style="background-color: #f1f3f4;">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <form action="{{ route('usuarios.index') }}" method="GET">
                    <div class="input-group bg-white rounded-3 border-0 shadow-sm overflow-hidden">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-0 py-2 shadow-none" 
                               placeholder="Buscar Usuario..." value="{{ $buscar ?? '' }}">
                        <button class="btn btn-secondary border-0 px-4 fw-bold" type="submit" style="background-color: #aeb1b4;">Filtrar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('usuarios.create') }}" class="btn fw-bold px-4 py-2" style="background-color: #0a6e0a; color: #fff; border-radius: 10px; border: none;">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo usuario
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle border-0">
            <thead>
                <tr style="color: #28a745; border-bottom: 2px solid #f0f0f0;">
                    <th class="border-0">Perfil</th>
                    <th class="border-0">ID</th>
                    <th class="border-0">Rol</th>
                    <th class="border-0">Nombre</th>
                    <th class="border-0">Teléfono</th>
                    <th class="border-0">Correo</th>
                    <th class="border-0">Estado</th>
                    <th class="border-0 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr style="border-bottom: 1px solid #f5f5f5;">
                    <td>
                        @if($usuario->foto_perfil)
                            <img src="{{ $usuario->foto_perfil }}" 
                                 class="rounded-circle shadow-sm" 
                                 style="width: 45px; height: 45px; object-fit: cover; border: 1px solid #ddd;">
                        @else
                            <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                 style="width: 45px; height: 45px; background-color: #e9ecef; border: 1px solid #ddd; color: #adb5bd;">
                                <i class="bi bi-person-fill fs-4"></i>
                            </div>
                        @endif
                    </td>
                    <td class="text-muted">{{ $usuario->ID_usuario }}</td>
                    <td>
                        @if($usuario->ID_rol == 1)
                            <span class="badge px-3 py-2" style="background-color: #d4edda; color: #155724; border-radius: 10px;">Estudiante</span>
                        @elseif($usuario->ID_rol == 2)
                            <span class="badge px-3 py-2" style="background-color: #ffe5d0; color: #856404; border-radius: 10px;">Instructor</span>
                        @else
                            <span class="badge px-3 py-2" style="background-color: #e2e3e5; color: #383d41; border-radius: 10px;">Admin</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $usuario->nombre }}</td>
                    <td class="text-muted">{{ $usuario->telefono ?? 'N/A' }}</td>
                    <td class="text-muted">{{ $usuario->correo }}</td>
                    <td>
                        @if($usuario->activo == 1)
                            <span class="fw-bold" style="color: #2c3e50;">Activo</span>
                        @else
                            <span class="fw-bold text-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('usuarios.edit', $usuario->ID_usuario) }}" class="btn btn-sm btn-light border shadow-sm">
                                <i class="bi bi-pencil-square text-secondary"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy', $usuario->ID_usuario) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border shadow-sm" onclick="return confirm('¿Eliminar este usuario?')">
                                    <i class="bi bi-trash3 text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .table td, .table th { padding: 1.2rem 0.75rem; }
    thead th { font-weight: 600; }
</style>
@endsection