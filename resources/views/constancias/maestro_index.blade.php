@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #ffffff;">
    <div class="mb-4">
        <h1 class="fw-bold" style="color: #1e6d2a;">Liberación de Constancias</h1>
        <p class="text-muted">Selecciona a los alumnos que han cumplido con los requisitos para liberar su documento.</p>
    </div>

    @if(session('exito'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <i class="bi bi-patch-check-fill me-2"></i> {{ session('exito') }}
        </div>
    @endif

    <div class="table-responsive shadow-sm rounded-4 overflow-hidden border">
        <table class="table table-hover align-middle bg-white mb-0">
            <thead style="background-color: #f8f9fa;">
                <tr style="color: #1e6d2a;">
                    <th class="border-0 px-4">Alumno</th>
                    <th class="border-0">Taller</th>
                    <th class="border-0 text-center">Asistencia</th>
                    <th class="border-0 text-center">Estado</th>
                    <th class="border-0 text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $inscrito)
                <tr class="border-bottom">
                    <td class="px-4">
                        <div class="fw-bold">{{ $inscrito->usuario->nombre }}</div>
                        <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $inscrito->carrera->nombre ?? 'UTVT' }}</small>
                    </td>
                    <td>{{ $inscrito->taller->nombre }}</td>
                    
                    {{-- NUEVA COLUMNA: PORCENTAJE DE ASISTENCIA (SOLO TEXTO CENTRADO) --}}
<td class="text-center">
    <div class="d-flex flex-column align-items-center justify-content-center">
        {{-- Badge estilizado para el porcentaje --}}
        <span class="badge rounded-pill px-3 py-2 {{ $inscrito->porcentaje >= 80 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $inscrito->porcentaje >= 80 ? 'text-success' : 'text-danger' }}" 
              style="font-size: 0.9rem; min-width: 60px; border: 1px solid currentColor;">
            <i class="bi {{ $inscrito->porcentaje >= 80 ? 'bi-check-circle-fill' : 'bi-exclamation-circle' }} me-1"></i>
            {{ number_format($inscrito->porcentaje, 0) }}%
        </span>
        
        {{-- Subtexto informativo --}}
        <small class="text-muted mt-1" style="font-size: 0.7rem;">
            {{ $inscrito->porcentaje >= 80 ? 'Meta cumplida' : 'Baja asistencia' }}
        </small>
    </div>
</td>

                    <td class="text-center">
                        @php
                            $imparte = \App\Models\ImparteTaller::where('ID_taller', $inscrito->ID_taller)
                                        ->where('ID_usuario', Auth::user()->ID_usuario)->first();
                            
                            $constancia = $imparte ? \App\Models\Constancia::where('ID_usuario', $inscrito->ID_usuario)
                                        ->where('ID_imparte', $imparte->ID_imparte)->first() : null;
                        @endphp
                        
                        @if($constancia)
                            <span class="badge rounded-pill" style="background-color: rgba(212, 237, 218, 0.7); color: #155724; font-size: 0.75rem;">
                                {{ $constancia->detalleConstancia ? 'Validada' : 'En espera' }}
                            </span>
                        @else
                            <span class="badge rounded-pill bg-light text-muted border px-3" style="font-size: 0.75rem;">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$constancia)
                            <form action="{{ route('constancias.liberar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ID_usuario" value="{{ $inscrito->ID_usuario }}">
                                <input type="hidden" name="ID_taller" value="{{ $inscrito->ID_taller }}">
                                <button type="submit" class="btn btn-sm fw-bold text-white px-3" 
                                        style="background-color: #1e6d2a; border-radius: 10px;"
                                        onclick="return confirm('¿Liberar constancia para {{ $inscrito->usuario->nombre }}?')">
                                    <i class="bi bi-send-check me-1"></i> Liberar
                                </button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-light border disabled" style="border-radius: 10px; color: #1e6d2a;">
                                <i class="bi bi-check-all"></i> Liberada
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection