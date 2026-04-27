@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center">
        {{-- Reducimos el ancho de col-md-8 a col-md-5 para que sea más esbelto --}}
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0" style="border-radius: 1.25rem; overflow: hidden;">
                
                {{-- Header más compacto --}}
                <div class="card-header p-3 text-white border-0" style="background: #1e6d2a;">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar-plus me-2 fs-5"></i>
                        <h6 class="mb-0 fw-bold text-uppercase small" style="letter-spacing: 1px;">Habilitar Sesión</h6>
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    {{-- Alerta de error más discreta --}}
                    @if(session('error'))
                        <div class="alert alert-danger border-0 small mb-4 py-2" style="border-radius: 10px; background-color: #fff5f5; color: #c53030;">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> 
                            {!! session('error') !!}
                        </div>
                    @endif

                    <form action="{{ route('asistencias.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                Taller e Instructor
                            </label>
                            <select name="ID_imparte" class="form-select form-select-sm shadow-none" 
                                    style="border-radius: 10px; border: 1.5px solid #edf2f7; padding: 0.6rem;" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($imparticiones as $im)
                                    <option value="{{ $im->ID_imparte }}">
                                        {{ $im->taller->nombre }} ({{ $im->usuario->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nota informativa pequeña --}}
                        <div class="p-3 mb-4 border-0 rounded-3" style="background-color: #f0f7f1; border-left: 4px solid #1e6d2a !important;">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                                <i class="bi bi-info-circle-fill text-success me-1"></i>
                                La sesión tendrá una vigencia de <strong>12 horas</strong>. Durante este tiempo no se podrá duplicar la sesión para este taller.
                            </p>
                        </div>

                        {{-- Botones ajustados --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('asistencias.index') }}" class="btn btn-light w-100 fw-bold small py-2" style="border-radius: 10px; font-size: 0.8rem; color: #718096;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn text-white w-100 fw-bold py-2 shadow-sm" 
                                    style="background: #1e6d2a; border-radius: 10px; font-size: 0.8rem; border: none;">
                                <i class="bi bi-qr-code me-2"></i>Generar QR
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Enlace de ayuda o panel debajo de la card (opcional) --}}
            <div class="text-center mt-4">
                <a href="{{ route('asistencias.index') }}" class="text-decoration-none small text-muted hover-success">
                    <i class="bi bi-arrow-left me-1"></i> Ver todas las asistencias
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Efecto hover suave */
    .hover-success:hover { color: #1e6d2a !important; }
    .form-select:focus { border-color: #1e6d2a; }
</style>
@endsection