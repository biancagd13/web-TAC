@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="mb-4 ps-2">
        <h1 class="fw-bold text-dark h2">Control de Constancias</h1>
        <p class="text-muted small">Administra y valida los documentos oficiales del sistema TAC</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <p class="text-muted small mb-1 fw-bold text-uppercase">Total emitidas</p>
                <h2 class="fw-bold text-dark mb-0">{{ $constancias->total() }}</h2>
                <div class="badge bg-success bg-opacity-10 text-success mt-2 rounded-pill mx-auto" style="width: fit-content;">+ Emitidas este mes</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <p class="text-muted small mb-1 fw-bold text-uppercase">Con Folio</p>
                <h2 class="fw-bold text-success mb-0">{{ $constancias->whereNotNull('detalleConstancia')->count() }}</h2>
                <span class="badge bg-primary bg-opacity-10 text-primary mt-2 rounded-pill mx-auto" style="width: fit-content;">Listas para descarga</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <p class="text-muted small mb-1 fw-bold text-uppercase">En Validación</p>
                <h2 class="fw-bold text-warning mb-0">{{ $constancias->whereNull('detalleConstancia')->count() }}</h2>
                <span class="badge bg-secondary bg-opacity-10 text-secondary mt-2 rounded-pill mx-auto" style="width: fit-content;">Pendiente de folio</span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form action="{{ route('constancias.index') }}" method="GET" class="d-flex flex-grow-1 position-relative" style="max-width: 500px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="buscar" class="form-control ps-5 border-light bg-light rounded-pill py-2 shadow-none" 
                       placeholder="Buscar por código de validación..." value="{{ request('buscar') }}">
            </form>
            <div class="d-flex gap-2">
                <button class="btn btn-light rounded-pill border-light text-muted px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                <a href="{{ route('constancias.create') }}" class="btn btn-success rounded-pill px-4 fw-bold" style="background-color: #1e6d2a; border: none;">
                    <i class="bi bi-plus-lg me-2"></i> Generar constancia
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white border-bottom">
                    <tr class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">
                        <th class="px-4 py-3">Constancia</th>
                        <th>Detalle Validación</th>
                        <th>Estado</th>
                        <th>Fecha Emisión</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($constancias as $c)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                                    <i class="bi bi-file-earmark-pdf fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $c->usuario->nombre }}</div>
                                    <div class="text-muted extra-small">{{ $c->imparteTaller->taller->nombre ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($c->detalleConstancia)
                                <span class="badge bg-light text-dark border font-monospace px-3">{{ $c->detalleConstancia->codigo_validacion }}</span>
                            @else
                                <span class="text-muted small italic">Pendiente de generar</span>
                            @endif
                        </td>
                        <td>
                            @if($c->detalleConstancia)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 small">Activo</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 small">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <i class="bi bi-clock me-1"></i> {{ date('d/m/Y', strtotime($c->fecha_emision)) }}
                        </td>
                        <td class="text-end px-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('constancias.edit', $c->ID_constancia) }}" class="btn btn-outline-primary btn-sm rounded-3 border-0 bg-light">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('constancias.destroy', $c->ID_constancia) }}" method="POST" class="d-inline">
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
    <div class="mt-4 d-flex justify-content-center">
        {{ $constancias->links() }}
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .table thead th { border-top: none; }
    .card { transition: transform 0.2s ease; }
    .form-control:focus { border-color: #1e6d2a; box-shadow: none; background-color: #fff; }
</style>
@endsection