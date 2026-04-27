@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff;">
    
    <div class="mb-4">
        <h1 class="fw-bold mb-0" style="font-size: 2.5rem; color: #000;">Gestión de Usuarios</h1>
        <p class="text-muted fs-5">Administra todos los talleres deportivos y creativos</p>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 20px; background-color: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total talleres</p>
                        <h2 class="fw-bold mb-0">{{ $totalTalleres }}</h2>
                        <small class="text-success fw-bold">+3 este mes</small>
                    </div>
                    <span class="fs-1">🗓️</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 20px; background-color: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Usuarios</p>
                        <h2 class="fw-bold mb-0">{{ $totalUsuarios }}</h2>
                        <small class="text-success fw-bold">+24 nuevos</small>
                    </div>
                    <span class="fs-1">👥</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 20px; background-color: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Estudiantes</p>
                        <h2 class="fw-bold mb-0">{{ $totalEstudiantes }}</h2>
                        <small class="text-success fw-bold">+4 nuevos</small>
                    </div>
                    <span class="fs-1">🎓</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 20px; background-color: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Instructores</p>
                        <h2 class="fw-bold mb-0">{{ $totalInstructores }}</h2>
                        <small class="text-success fw-bold">+2 nuevos</small>
                    </div>
                    <span class="fs-1">👨‍🏫</span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 rounded-4 mb-4" style="background-color: #f1f3f4;">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <form action="{{ route('usuarios.index') }}" method="GET">
                    <div class="input-group bg-white rounded-3 border-0 shadow-sm overflow-hidden">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-0 py-2 shadow-none" 
                               placeholder="Buscar Usuario..." value="{{ $buscar ?? '' }}">
                        <button class="btn btn-secondary border-0 px-4" type="submit" style="background-color: #aeb1b4;">Filtrar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="#" class="btn fw-bold px-4 py-2" style="background-color: #0a6e0a; color: #000; border-radius: 10px;">
                    + Nuevo usuario
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="text-success">
                    <th>ID</th>
                    <th>Rol</th>
                    <th>Nombre</th>
                    <th>Gmail</th>
                    <th>Contraseña</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @foreach($usuarios as $user)
                <tr>
                    <td class="text-muted">{{ $user->ID_usuario }}</td>
                    <td>
                        @if($user->ID_rol == 1)
                            <span class="badge px-3 py-2" style="background-color: #d4edda; color: #155724; border-radius: 10px;">Estudiante</span>
                        @elseif($user->ID_rol == 2)
                            <span class="badge px-3 py-2" style="background-color: #ffe5d0; color: #856404; border-radius: 10px;">Instructor</span>
                        @else
                            <span class="badge px-3 py-2" style="background-color: #e2e3e5; color: #383d41; border-radius: 10px;">Admin</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $user->nombre }}</td>
                    <td class="text-muted">{{ $user->correo }}</td>
                    <td class="text-muted">********</td>
                    <td>
                        <span class="text-dark">{{ $user->activo ? 'Activo' : 'Inactivo' }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-sm btn-light border shadow-sm">📝</a>
                            <form action="#" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border shadow-sm">🗑️</button>
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
    .table thead th {
        border-bottom: none;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .table tbody tr {
        border-bottom: 1px solid #eee;
    }
</style>
@endsection