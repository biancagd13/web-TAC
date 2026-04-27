@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <div class="mb-4 ps-2">
        <h1 class="fw-bold mb-0" style="font-size: 3rem; color: #1e6d2a;">Explorar Talleres</h1>
        <p class="text-muted fs-5">Descubre talleres deportivos y creativos para tu formación integral</p>
    </div>

    {{-- Alertas de éxito o error --}}
    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- NUEVA IMPLEMENTACIÓN: ALERTA DE PERIODO DE INSCRIPCIÓN CERRADO --}}
    @if(isset($periodoAbierto) && !$periodoAbierto)
    <div class="alert shadow-sm border-0 mb-5 p-0 overflow-hidden" style="border-radius: 20px; background-color: #fff;">
        <div class="d-flex">
            <div class="bg-danger d-flex align-items-center justify-content-center" style="width: 80px;">
                <i class="bi bi-calendar-x text-white fs-2"></i>
            </div>
            <div class="p-4">
                <h4 class="fw-bold text-danger mb-1">Inscripciones No Disponibles</h4>
                <p class="mb-0 text-secondary">
                    Hola, <strong>{{ auth()->user()->nombre }}</strong>. Actualmente el periodo de inscripción está <span class="badge bg-danger text-uppercase">Cerrado</span>. 
                    Podrás inscribirte nuevamente al inicio del próximo cuatrimestre. Por ahora puedes consultar la oferta disponible.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Barra de búsqueda estilizada --}}
    <form action="{{ route('talleres.index') }}" method="GET" class="mb-5">
        <div class="p-4 rounded-4 shadow-sm" style="background-color: #dbdbdb;">
            <div class="row align-items-center g-3">
                <div class="col-md-10">
                    <div class="input-group bg-white rounded-3 overflow-hidden border-0 shadow-sm">
                        <span class="input-group-text bg-white border-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="buscar" class="form-control border-0 py-2 shadow-none" 
                               placeholder="Buscar taller por nombre, categoría, instructor..." 
                               value="{{ $buscar ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-success shadow-sm px-4 py-2 border-0 w-100 fw-bold" style="background-color: #1e6d2a; border-radius: 10px;">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4">
        @forelse($talleres as $taller)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-lg p-4 h-100" style="border-radius: 25px; background-color: #fff; transition: transform 0.3s ease;">
                
                <div class="mb-1 d-flex justify-content-between align-items-start">
                    <h4 class="fw-bold mb-0 text-dark">{{ $taller->nombre }}</h4>
                    <span class="badge bg-light text-success border border-success px-2 py-1" style="font-size: 0.7rem;">Activo</span>
                </div>
                
                <p class="text-muted small mb-3">
                    Por: 
                    @if($taller->imparticiones && $taller->imparticiones->isNotEmpty())
                        {{ $taller->imparticiones->first()->usuario->nombre }}
                    @else
                        Instructor UTVT
                    @endif
                </p>

                <p class="text-secondary small mb-4" style="font-size: 0.95rem; line-height: 1.4; min-height: 3em;">
                    {{ Str::limit($taller->detalle, 100) }}
                </p>

                <div class="d-flex justify-content-between text-muted mb-2 small">
                    <span><i class="bi bi-clock-fill me-1"></i> {{ $taller->horario ?? 'Horario por definir' }}</span>
                    <span class="fw-bold text-success">90 min</span>
                </div>

                @php
                    $cupoMaximo = $taller->cupo ?? 30; 
                    $totalInscritos = $taller->inscripciones_count ?? $taller->inscripciones->count();
                    $lugaresDisponibles = $cupoMaximo - $totalInscritos;
                    $porcentaje = ($cupoMaximo > 0) ? ($totalInscritos / $cupoMaximo) * 100 : 0;
                    $inscrito = auth()->user()->inscripciones->contains('ID_taller', $taller->ID_taller);
                @endphp

                <div class="d-flex justify-content-between text-muted mb-3 small">
                    <span><i class="bi bi-people-fill me-1"></i> {{ $totalInscritos }}/{{ $cupoMaximo }} inscritos</span>
                    <span class="fw-bold {{ $lugaresDisponibles <= 5 ? 'text-danger' : 'text-primary' }}">
                        {{ $lugaresDisponibles <= 5 ? '¡Últimos lugares!' : 'Disponibles' }}
                    </span>
                </div>

                <div class="progress mb-2" style="height: 10px; border-radius: 10px; background-color: #eee;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                         style="width: {{ $porcentaje }}%; background-color: #76b82a;"
                         aria-valuenow="{{ $totalInscritos }}" aria-valuemin="0" aria-valuemax="{{ $cupoMaximo }}">
                    </div>
                </div>

                <small class="text-muted d-block mb-4">
                    @if($lugaresDisponibles > 0)
                        {{ $lugaresDisponibles }} lugares disponibles
                    @else
                        <span class="text-danger fw-bold">Cupo agotado</span>
                    @endif
                </small>

                {{-- LÓGICA DE BOTONES --}}
                @if($inscrito)
                    <div class="btn w-100 fw-bold border-0 py-2 mt-auto shadow-sm" 
                         style="background-color: #e8f5e9; color: #2e7d32; border-radius: 15px; cursor: default;">
                        <i class="bi bi-check-circle-fill me-2"></i> Ya estás inscrito
                    </div>
                @elseif(isset($periodoAbierto) && !$periodoAbierto)
                    <button class="btn w-100 fw-bold border-0 py-2 mt-auto shadow-sm" 
                            style="background-color: #eeeeee; color: #9e9e9e; border-radius: 15px; cursor: not-allowed;" disabled>
                        <i class="bi bi-lock-fill me-2"></i> Inscripción Cerrada
                    </button>
                @else
                    <form action="{{ route('inscripciones.store') }}" method="POST" class="mt-auto">
                        @csrf
                        <input type="hidden" name="ID_taller" value="{{ $taller->ID_taller }}">
                        <input type="hidden" name="ID_usuario" value="{{ auth()->user()->ID_usuario }}">
                        <input type="hidden" name="fecha" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="periodo" value="Enero - Abril 2026">
                        <input type="hidden" name="ID_carrera" value="{{ auth()->user()->inscripciones->first()->ID_carrera ?? 1 }}">

                        <button type="submit" class="btn w-100 fw-bold border-0 py-2 shadow-sm text-white" 
                                style="background-color: #1e6d2a; border-radius: 15px; transition: 0.3s;"
                                {{ $lugaresDisponibles <= 0 ? 'disabled' : '' }}
                                onclick="return confirm('¿Confirmas tu inscripción al taller de {{ $taller->nombre }}?')">
                            {{ $lugaresDisponibles <= 0 ? 'Sin cupo' : 'Inscribirse Ahora' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h4 class="text-muted mt-3">No encontramos talleres disponibles con esos criterios.</h4>
        </div>
        @endforelse
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endsection