@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="mb-4 ps-2">
        <h1 class="fw-bold text-dark h2">Listado de Inscripciones</h1>
        <p class="text-muted small">Control administrativo de alumnos inscritos en talleres integradores</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex flex-grow-1 position-relative" style="max-width: 400px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" class="form-control ps-5 border-light bg-light rounded-pill py-2 shadow-none" placeholder="Buscar inscripción...">
            </div>
            <a href="{{ route('inscripciones.create') }}" class="btn btn-success rounded-pill px-4 fw-bold" style="background-color: #1e6d2a; border: none;">
                <i class="bi bi-plus-lg me-2"></i> Nueva Inscripción
            </a>
        </div>
    </div>

    @if(session('exito'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('exito') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white border-bottom">
                    <tr class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">
                        <th class="px-4 py-3">Alumno</th>
                        <th>Carrera / Taller</th>
                        <th>Periodo</th>
                        <th>Fecha</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($inscripciones as $i)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                                    <i class="bi bi-person-badge fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $i->usuario->nombre }}</div>
                                    <div class="text-muted extra-small">ID: #{{ $i->ID_inscripción }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold text-success">{{ $i->taller->nombre ?? 'N/A' }}</div>
                            <div class="text-muted extra-small">{{ $i->carrera->nombre ?? 'N/A' }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border px-3">{{ $i->periodo }}</span></td>
                        <td class="text-muted small">{{ date('d/m/Y', strtotime($i->fecha)) }}</td>
                        <td class="text-end px-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('inscripciones.edit', $i->ID_inscripción) }}" class="btn btn-outline-primary btn-sm rounded-3 border-0 bg-light">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('inscripciones.destroy', $i->ID_inscripción) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 border-0 bg-light" onclick="return confirm('¿Eliminar?')">
                                        <i class="bi bi-trash"></i>
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
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .table thead th { border-top: none; }
</style>
@endsection