@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff; min-height: 100vh;">
    {{-- Encabezado Estilo TAC --}}
    <div class="mb-5 ps-md-4">
        <h1 class="fw-bold" style="font-size: 3rem; color: #000;">Mis Constancias</h1>
        <p class="text-muted fs-5">Descarga tus reconocimientos oficiales y valida tu formación integral.</p>
    </div>

    {{-- Estado Vacío Mejorado --}}
    @if($constancias->isEmpty())
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <div class="p-5 rounded-5 shadow-sm" style="background-color: #f8f9fa;">
                    <i class="bi bi-file-earmark-lock2" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h3 class="fw-bold mt-4">Sin documentos aún</h3>
                    <p class="text-muted">Tus constancias aparecerán aquí una vez que el instructor libere el taller y administración asigne un folio.</p>
                    <a href="{{ route('talleres.index') }}" class="btn btn-dark rounded-pill px-4 mt-3">Explorar Talleres</a>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4 px-md-4">
            @foreach($constancias as $constancia)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 25px; background-color: #fdfdfd; transition: transform 0.3s;">
                        <div class="card-body p-4 d-flex flex-column">
                            
                            {{-- Header de la Tarjeta --}}
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                     style="width: 50px; height: 50px; background-color: #e8f5e9;">
                                    <i class="bi bi-patch-check-fill text-success fs-4"></i>
                                </div>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #e8f5e9; color: #2e7d32; font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($constancia->fecha_emision)->format('d M, Y') }}
                                </span>
                            </div>

                            {{-- Información del Taller --}}
                            <div class="mb-4">
                                <h4 class="fw-bold text-dark mb-1">{{ $constancia->imparteTaller->taller->nombre }}</h4>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-person-badge me-2"></i>
                                    <span>Instructor: {{ $constancia->imparteTaller->usuario->nombre }}</span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <hr class="opacity-25 mb-4">

                                @if($constancia->detalleConstancia)
                                    {{-- Botón de Descarga Estilo TAC --}}
                                    <a href="{{ route('constancias.descargar', $constancia->ID_constancia) }}" 
                                       class="btn btn-success w-100 fw-bold shadow-sm p-3 d-flex align-items-center justify-content-center" 
                                       style="background-color: #0a6e0a; border-radius: 15px; border: none; transition: 0.3s;">
                                        <i class="bi bi-cloud-arrow-down-fill fs-5 me-2"></i>
                                        OBTENER CONSTANCIA
                                    </a>
                                @else
                                    {{-- Alerta de Pendiente --}}
                                    <div class="d-flex align-items-center p-3 rounded-4" style="background-color: #fff8e1; color: #856404;">
                                        <i class="bi bi-hourglass-split fs-4 me-3"></i>
                                        <div class="small">
                                            <strong class="d-block">Procesando folio</strong>
                                            Administración está validando tu documento.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    /* Efecto de elevación suave al pasar el mouse */
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    
    /* Ajustes responsivos para tablets y celulares */
    @media (max-width: 768px) {
        h1 { font-size: 2.2rem !important; }
        .card-body { p-3 !important; }
    }
</style>
@endsection