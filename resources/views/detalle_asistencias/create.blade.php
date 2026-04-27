<h1 style="color: #004a2f;">Registrar Entrada de Alumno</h1>

{{-- CONTENEDOR DEL ESCÁNER QR --}}
<div id="seccion-escaneo" style="margin-bottom: 30px; text-align: center;">
    <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto; border: 2px solid #004a2f; border-radius: 10px; overflow: hidden;"></div>
    <p id="mensaje-escaneo" style="margin-top: 10px; color: #666;">
        <strong>Opción 1:</strong> Apunta tu cámara al QR del Instructor para pase de lista automático.
    </p>
</div>

<hr style="border: 1px solid #eee; margin: 20px 0;">

{{-- TU FORMULARIO MANUAL ACTUAL (Opción 2) --}}
<div id="seccion-manual">
    <p style="text-align: center; color: #004a2f;"><strong>Opción 2:</strong> Registro Manual</p>
    {{-- Aquí va el formulario que ya tienes con el botón "Guardar Entrada Manual" --}}
    <form action="{{ route('detalle_asistencias.store') }}" method="POST">
        @csrf
        {{-- ... campos de fecha, alumno y sesión ... --}}
        <button type="submit" style="background-color: #004a2f; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-weight: bold;">
            Guardar Entrada Manual
        </button>
    </form>
</div>

{{-- SCRIPT PARA ACTIVAR LA CÁMARA --}}
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Al detectar el QR, redirigimos automáticamente a la URL del código
        // decodedText contiene la URL generada por el Instructor (ej: /detalle_asistencias/create?id_asistencia=5...)
        window.location.href = decodedText;
    }

    function onScanFailure(error) {
        // Errores de enfoque, ignoramos para que siga buscando
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>