@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-print-none mb-4 card p-3 shadow-sm border-0" style="background-color: #f8f9fa;">
        <div class="row align-items-center">
            <div class="col-md-8 d-flex align-items-center">
            
                
                <div>
                    <h3 class="text-success fw-bold mb-0"><i class="bi bi-qr-code-scan"></i> Panel de QRs de Inscripción</h3>
                    <p class="text-muted mb-0">Puedes imprimir todo el catálogo o elegir un taller específico para su cartel oficial.</p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button onclick="window.print()" class="btn btn-primary shadow-sm">
                    <i class="bi bi-printer"></i> Imprimir Todo el Catálogo
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($talleres as $taller)
        <div class="col-md-4 mb-4 printable-card" id="taller-{{ $taller->ID_taller }}">
            <div class="card text-center border-success shadow-sm" style="border-width: 2px;">
                <div class="card-header bg-success text-white py-2">
                    <h5 class="fw-bold mb-0 text-uppercase">{{ $taller->nombre }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-3">
                        {!! QrCode::size(180)->margin(1)->generate('TAC-INS-' . $taller->ID_taller) !!}
                    </div>
                    
                    <h6 class="fw-bold text-dark mb-1">HORARIO:</h6>
                    <p class="text-muted small mb-2">{{ $taller->horario ?? 'Pendiente' }}</p>

                    <h6 class="fw-bold text-dark mb-1">INSTRUCTOR:</h6>
                    <p class="text-success fw-bold mb-3">
                        {{-- Buscamos el nombre del maestro desde la relación imparte --}}
                        {{ $taller->maestro_nombre ?? 'Sin instructor asignado' }}
                    </p>
                    
                    <p class="mb-0 text-muted" style="font-size: 0.7rem;">ID TALLER: #{{ $taller->ID_taller }}</p>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center d-print-none">
                    <span class="text-success fw-bold small">SISTEMA TAC</span>
                    <button onclick="imprimirUno('taller-{{ $taller->ID_taller }}')" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer"></i> Imprimir este
                    </button>
                </div>
                {{-- Pie de página solo para impresión --}}
                <div class="card-footer bg-white d-none d-print-block">
                    <p class="fw-bold text-success mb-0">UNIVERSIDAD TECNOLÓGICA DEL VALLE DE TOLUCA</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    function imprimirUno(id) {
        // Seleccionamos el contenido
        const elemento = document.getElementById(id);
        const contenido = elemento.innerHTML;

        // Creamos el iframe "fantasma"
        let iframe = document.getElementById('iframeImpresion');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'iframeImpresion';
            iframe.style.display = 'none'; // Completamente oculto
            document.body.appendChild(iframe);
        }

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
            <html>
                <head>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                    <style>
                        body { padding: 40px; display: flex; justify-content: center; }
                        .card { width: 400px; border: 2px solid #198754 !important; text-align: center; }
                        .btn, .d-print-none, .me-3 { display: none !important; }
                        .card-header { background-color: #198754 !important; color: white !important; -webkit-print-color-adjust: exact; }
                    </style>
                </head>
                <body>${contenido}</body>
            </html>
        `);
        doc.close();

        // Mandamos imprimir el iframe directamente
        setTimeout(() => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }, 300);
    }
</script>

<style>
    @media print {
        .d-print-none, .sidebar, .navbar, .btn, .me-3 { display: none !important; }
        .printable-card { width: 100% !important; margin: 0 auto !important; }
        .card { border: 2px solid #198754 !important; }
        .card-header { background-color: #198754 !important; color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>

<a href="{{ route('talleres.index') }}" class="btn text-white rounded-3 px-4" style="background-color: #0a6e0a;">
                    <i class="bi bi-arrow-left"></i> Regresar
                </a>

@endsection