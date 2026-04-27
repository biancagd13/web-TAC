@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 ps-2">
        <div>
            <h2 class="fw-bold mb-0" style="color: #1e6d2a;">Carreras UTVT</h2>
            <p class="text-muted small">Catálogo oficial de programas académicos</p>
        </div>
        <a href="{{ route('carreras.create') }}" class="btn text-white fw-bold px-4 rounded-pill" style="background: #1e6d2a; box-shadow: 0 4px 10px rgba(30,109,42,0.3);">
            <i class="bi bi-plus-lg me-2"></i>Nueva Carrera
        </a>
    </div>

    @if(session('exito'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('exito') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                <tr style="color: #1e6d2a; font-size: 0.85rem; text-transform: uppercase;">
                    <th class="px-4 py-3">ID</th>
                    <th>Nombre de Carrera</th>
                    <th>Clave</th>
                    <th>Estatus</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carreras as $carrera)
                <tr class="border-bottom">
                    <td class="px-4 text-muted fw-bold">#{{ $carrera->ID_carrera }}</td>
                    <td class="fw-bold text-dark">{{ $carrera->nombre }}</td>
                    <td class="text-muted small fw-bold">{{ $carrera->clave }}</td>
                    <td>
                        <span class="badge {{ $carrera->activo ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-3">
                            {{ $carrera->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group rounded-pill overflow-hidden border shadow-sm">
                            <a href="{{ route('carreras.edit', $carrera) }}" class="btn btn-white px-3"><i class="bi bi-pencil-square text-primary"></i></a>
                            <form action="{{ route('carreras.destroy', $carrera) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-white px-3" onclick="return confirm('¿Deseas eliminar esta carrera?')"><i class="bi bi-trash3 text-danger"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection