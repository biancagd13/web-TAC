@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh;">
    
    <div class="text-center mb-5">
        <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: rgba(30, 109, 42, 0.1); color: #1e6d2a; font-weight: 700; letter-spacing: 1px;">
            <i class="bi bi-shield-check me-1"></i> SESIÓN DE ASISTENCIA ACTIVA
        </span>
        <h1 class="fw-black display-5 mb-1" style="color: #1e6d2a; font-weight: 900;">SISTEMA <span style="color: #000;">TAC</span></h1>
        <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
            <div class="text-end">
                <p class="mb-0 text-dark fw-bold fs-5">{{ $asistencia->imparteTaller->taller->nombre }}</p>
                <p class="mb-0 text-muted small">Instructor: {{ $asistencia->imparteTaller->usuario->nombre }}</p>
            </div>
            <div style="width: 2px; height: 40px; background-color: #dee2e6;"></div>
            <div class="text-start">
                <p class="mb-0 text-dark fw-bold fs-5"><i class="bi bi-calendar3 me-2"></i>{{ date('d/m/Y') }}</p>
                <p class="mb-0 text-muted small">ID Sesión: #{{ $asistencia->ID_asistencia }}</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <div class="card border-0 shadow-lg position-relative overflow-hidden" style="border-radius: 40px; background: #fff;">
                <div style="height: 10px; background: #1e6d2a; width: 100%;"></div>
                
                <div class="card-body p-5 text-center">
                    <p class="text-uppercase fw-bold text-muted small mb-4" style="letter-spacing: 2px;">Escanea el código para registrarte</p>
                    
                    {{-- CONTENEDOR DEL QR --}}
                    <div class="qr-wrapper p-3 d-inline-block shadow-sm mb-4" style="background: #fff; border-radius: 30px; border: 1px solid #f0f0f0;">
                        <div class="qr-frame p-2" style="border: 2px dashed #1e6d2a; border-radius: 20px;">
                            {!! $codigoQR !!}
                        </div>
                    </div>

                    <div class="timer-section mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-4">
                            <span class="text-muted small fw-bold">SEGURIDAD DINÁMICA</span>
                            <span id="timer-text" class="fw-black fs-4" style="color: #1e6d2a;">10s</span>
                        </div>
                        
                        <div class="progress-container mx-auto" style="width: 90%; height: 8px; background-color: #f1f3f5; border-radius: 20px; overflow: hidden;">
                            <div id="progress-fill" class="progress-bar" style="width: 100%; height: 100%; background-color: #1e6d2a; transition: width 1s linear;"></div>
                        </div>
                        
                        <p class="text-muted mt-3 mb-0" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> El código se regenera automáticamente para evitar duplicidad.
                        </p>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-lg shadow-sm text-white py-3" 
                        style="background-color: #1e6d2a; border-radius: 20px; font-weight: 700; border: none; transition: 0.3s;"
                        onclick="window.location.reload();">
                    <i class="bi bi-arrow-clockwise me-2"></i>REGENERAR AHORA
                </button>
                
                <a href="{{ route('asistencias.index') }}" class="btn btn-link text-decoration-none fw-bold mt-2" style="color: #6c757d;">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Panel de Gestión
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animación de pulso para el QR */
    .qr-frame {
        animation: pulse-border 2s infinite;
    }

    @keyframes pulse-border {
        0% { border-color: #1e6d2a; }
        50% { border-color: #8cc63f; }
        100% { border-color: #1e6d2a; }
    }

    /* Botón hover */
    .btn:hover {
        transform: translateY(-3px);
        filter: brightness(1.1);
    }

    .fw-black {
        font-weight: 900 !important;
    }
</style>

<script>
    let timeLeft = 30;
    const timerText = document.getElementById('timer-text');
    const progressFill = document.getElementById('progress-fill');

    const countdown = setInterval(() => {
        timeLeft--;
        timerText.innerText = timeLeft + "s";

        let percentage = (timeLeft / 30) * 100;
        progressFill.style.width = percentage + "%";

        if (timeLeft <= 3) {
            progressFill.style.backgroundColor = "#dc3545"; 
            timerText.style.color = "#dc3545";
        }

        if (timeLeft <= 0) {
            clearInterval(countdown);
            window.location.reload();
        }
    }, 1000);
</script>
@endsection