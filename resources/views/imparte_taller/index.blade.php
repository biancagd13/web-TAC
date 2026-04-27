@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 ps-2">
        <div>
            <h2 class="fw-bold mb-0" style="color: #1e6d2a;">Asignación de Talleres</h2>
            <p class="text-muted small">Control de instructores y periodos escolares</p>
        </div>
        <a href="{{ route('imparte_taller.create') }}" class="btn text-white fw-bold px-4 rounded-pill" style="background: #1e6d2a; box-shadow: 0 4px 10px rgba(30,109,42,0.3);">
            <i class="bi bi-plus-lg me-2"></i>Nueva Asignación
        </a>
    </div>

    @if(session('exito'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 animate__animated animate__fadeIn">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('exito') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                <tr style="color: #1e6d2a; font-size: 0.85rem; text-transform: uppercase;">
                    <th class="px-4 py-3">ID</th>
                    <th>Instructor / Personal</th>
                    <th>Taller Asignado</th>
                    <th>Periodo / Fecha</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($imparticiones as $item)
                <tr class="border-bottom">
                    <td class="px-4 text-muted fw-bold">#{{ $item->ID_imparte }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->usuario->nombre }}</div>
                        <small class="text-muted">Instructor Autorizado</small>
                    </td>
                    <td>
                        <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(30, 109, 42, 0.1); color: #1e6d2a;">
                            {{ $item->taller->nombre }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold small">{{ $item->periodo }}</div>
                        <div class="text-muted small">{{ date('d/m/Y', strtotime($item->fecha)) }}</div>
                    </td>
                    <td>
                        @if($item->activo == 1)
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Activo</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group rounded-pill overflow-hidden border shadow-sm">
                            <a href="{{ route('imparte_taller.edit', $item->ID_imparte) }}" class="btn btn-white px-3" title="Editar">
                                <i class="bi bi-pencil-square text-primary"></i>
                            </a>
                            <form action="{{ route('imparte_taller.destroy', $item->ID_imparte) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-white px-3" onclick="return confirm('¿Deseas eliminar esta asignación?')">
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
@endsection