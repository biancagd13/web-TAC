@extends('layouts.app')

@section('title', 'Gestión de Talleres - TAC')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold" style="color: #333;">Gestión de Talleres</h2>
            <p class="text-muted">Administra todos los talleres deportivos y creativos del sistema</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center" style="border-radius: 15px;">
                <p class="text-muted mb-1 small uppercase fw-bold">Total talleres</p>
                <h1 class="fw-bold mb-0" style="color: #0a6e0a;">{{ $totalTalleres }}</h1>
                <span class="badge bg-success-subtle text-success mt-2 w-50 mx-auto">+3 este mes</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center" style="border-radius: 15px;">
                <p class="text-muted mb-1 small uppercase fw-bold">Usuarios en sistema</p>
                <h1 class="fw-bold mb-0" style="color: #0a6e0a;">{{ $totalEstudiantes }}</h1>
                <span class="badge bg-primary-subtle text-primary mt-2 w-50 mx-auto">Estudiantes</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center" style="border-radius: 15px;">
                <p class="text-muted mb-1 small uppercase fw-bold">Instructores</p>
                <h1 class="fw-bold mb-0" style="color: #0a6e0a;">{{ $totalInstructores }}</h1>
                <span class="badge bg-dark-subtle text-dark mt-2 w-50 mx-auto">Personal Activo</span>
            </div>
        </div>
    </div>

    <form action="{{ route('talleres.index') }}" method="GET" class="row mb-4 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="buscar" class="form-control border-start-0 ps-0" placeholder="Buscar Taller..." value="{{ $buscar ?? '' }}">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <button type="submit" class="btn btn-outline-secondary rounded-3 me-2"><i class="bi bi-filter"></i> Filtrar</button>
            
            <a href="{{ route('talleres.create') }}" class="btn text-white rounded-3 px-4" style="background-color: #0a6e0a;">
                <i class="bi bi-plus-lg me-1"></i> Nuevo taller
            </a>

            @if(request('buscar'))
                <a href="{{ route('talleres.index') }}" class="btn btn-link text-muted small ms-2">Limpiar filtro</a>
            @endif
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
            <a href="{{ route('talleres.qr') }}" class="btn text-white rounded-3 px-4" style="background-color: #0a6e0a;">
    <i class="bi bi-qr-code"></i> Generar QRs Imprimibles
</a>
    </div>
</div>

    <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Taller</th>
                        <th>Detalle</th>
                        <th>Estado</th>
                        <th>Horario</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($talleres as $taller)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="p-2 rounded-3 bg-success-subtle me-3">
                                    <i class="bi bi-journal-bookmark text-success"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $taller->nombre }}</span>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($taller->detalle, 40) }}</small>
                        </td>
                        <td>
                            @if($taller->activo == 1)
                                <span class="badge rounded-pill" style="background-color: #e8f5e9; color: #2e7d32;">Activo</span>
                            @else
                                <span class="badge rounded-pill bg-light text-muted">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold"><i class="bi bi-clock me-1"></i> {{ $taller->horario ?? 'Pendiente' }}</div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('talleres.edit', $taller->ID_taller) }}" class="btn btn-sm btn-outline-primary border-0">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('talleres.destroy', $taller->ID_taller) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('¿Eliminar taller?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            No se encontraron talleres con ese criterio.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection